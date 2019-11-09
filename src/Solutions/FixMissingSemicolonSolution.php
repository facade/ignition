<?php

namespace Facade\Ignition\Solutions;

use Facade\IgnitionContracts\RunnableSolution;
use Illuminate\Support\Str;

class FixMissingSemicolonSolution implements RunnableSolution
{
    private $filePath;
    private $lineNumber;
    private $unexpected;

    public function __construct($filePath = null, $lineNumber = null, $unexpected = null)
    {
        $this->filePath = $filePath;
        $this->lineNumber = $lineNumber;
        $this->unexpected = $unexpected;
    }

    public function getSolutionTitle(): string
    {
        return 'You are missing a semicolon.';
    }

    public function getDocumentationLinks(): array
    {
        return [];
    }

    public function getSolutionActionDescription(): string
    {
        return 'Each instruction requires termination with a semicolon.';
    }

    public function getRunButtonText(): string
    {
        return 'Insert missing semicolon';
    }

    public function getSolutionDescription(): string
    {
        return '';
    }

    public function getRunParameters(): array
    {
        return [
            'filePath' => $this->filePath,
            'lineNumber' => $this->lineNumber,
            'unexpected' => $this->unexpected,
        ];
    }

    public function isRunnable()
    {
        return $this->insertSemicolon($this->getRunParameters()) !== false;
    }

    public function run(array $parameters = [])
    {
        $output = $this->insertSemicolon($parameters);
        if ($output !== false) {
            file_put_contents(app_path().$parameters['filePath'], $output);
        }
    }

    public function insertSemicolon(array $parameters = [])
    {
        if (strpos($parameters['filePath'], 'ignition/tests/Solutions') !== false) {
            $file = $parameters['filePath'];
        } else {
            $file = app_path().$parameters['filePath'];
        }

        if (! is_file($file)) {
            return false;
        }
        $contents = file_get_contents($file);
        $tokens = token_get_all($contents);

        $reverseTokens = array_reverse($tokens);

        $output = [];
        $insertSemicolon = false;
        $line = 0;
        foreach ($reverseTokens as $token) {
            $char = isset($token[1]) ? $token[1] : $token;

            $output[] = $char;

            if (isset($token[2])) {
                $line = $token[2];
            }
            if (is_string($token) && $line == $parameters['lineNumber'] && $char == $parameters['unexpected']) {
                $insertSemicolon = true;
            }
            if ($insertSemicolon && isset($token[0]) && $token[0] == T_WHITESPACE) {
                $insertSemicolon = false;
                $output[] = ';';
            }
        }
        $proposedFix = implode('', array_reverse($output));

        $result = exec(sprintf('echo %s | php -l', escapeshellarg($proposedFix)), $output, $exit);

        if (Str::contains($result, 'No syntax errors')) {
            return $proposedFix;
        }

        return false;
    }
}
