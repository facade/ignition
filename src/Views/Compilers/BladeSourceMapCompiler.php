<?php

namespace Facade\Ignition\Views\Compilers;

use Illuminate\View\Compilers\BladeCompiler;

class BladeSourceMapCompiler extends BladeCompiler
{
    public function detectLineNumber(string $filename, int $exceptionLineNumber): int
    {
        $map = $this->compileString(file_get_contents($filename));
        $map = explode("\n", $map);

        $line = $map[$exceptionLineNumber - $this->getExceptionLineOffset()] ?? $exceptionLineNumber;
        $pattern = '/\|---LINE:([0-9]+)---\|/m';

        if (preg_match($pattern, $line, $matches)) {
            return $matches[1];
        }

        return $exceptionLineNumber;
    }

    protected function getExceptionLineOffset(): int
    {
        /*
         * Laravel 5.8.0- 5.8.9 added the view name as a comment in the compiled view on a new line.
         * That's why the offset to detect the correct line number must be 2 instead of 1.
         */
        if (version_compare(app()->version(), '5.8.0', '>=') &&
            version_compare(app()->version(), '5.8.9', '<=')
        ) {
            return 2;
        }

        return 1;
    }

    public function compileString($value)
    {
        try {
            $value = $this->addEchoLineNumbers($value);

            $value = $this->addStatementLineNumbers($value);

            $value = parent::compileString($value);

            return $this->trimEmptyLines($value);
        } catch (\Exception $e) {
            return $value;
        }
    }

    protected function addEchoLineNumbers(string $value)
    {
        $pattern = sprintf('/(@)?%s\s*(.+?)\s*%s(\r?\n)?/s', $this->contentTags[0], $this->contentTags[1]);

        if (preg_match_all($pattern, $value, $matches, PREG_OFFSET_CAPTURE)) {
            foreach (array_reverse($matches[0]) as $match) {
                $position = mb_strlen(substr($value, 0, $match[1]));

                $value = $this->insertLineNumberAtPosition($position, $value);
            }
        }

        return $value;
    }

    protected function addStatementLineNumbers(string $value)
    {
        $shouldInsertLineNumbers = preg_match_all(
            '/\B@(@?\w+(?:::\w+)?)([ \t]*)(\( ( (?>[^()]+) | (?3) )* \))?/x',
            $value,
            $matches,
            PREG_OFFSET_CAPTURE
        );

        if ($shouldInsertLineNumbers) {
            foreach (array_reverse($matches[0]) as $match) {
                $position = mb_strlen(substr($value, 0, $match[1]));

                $value = $this->insertLineNumberAtPosition($position, $value);
            }
        }

        return $value;
    }

    protected function insertLineNumberAtPosition(int $position, string $value)
    {
        $before = mb_substr($value, 0, $position);
        $lineNumber = count(explode("\n", $before));

        return mb_substr($value, 0, $position)."|---LINE:{$lineNumber}---|".mb_substr($value, $position);
    }

    protected function trimEmptyLines(string $value)
    {
        $value = preg_replace('/^\|---LINE:([0-9]+)---\|$/m', '', $value);

        return ltrim($value, PHP_EOL);
    }
}
