<?php

namespace Facade\Ignition\Solutions;

use Facade\IgnitionContracts\RunnableSolution;
use Illuminate\Support\Facades\Blade;

class SuggestCorrectVariableNameSolution implements RunnableSolution
{
    /** @var string */
    private $variableName;

    /** @var string */
    private $viewFile;

    public function __construct($variableName = null, $viewFile = null, $suggested = null)
    {
        $this->variableName = $variableName;
        $this->viewFile = $viewFile;
        $this->suggested = $suggested;
    }

    public function getSolutionTitle(): string
    {
        return 'Possible typo $'.$this->variableName;
    }

    public function getDocumentationLinks(): array
    {
        return [];
    }

    public function getSolutionActionDescription(): string
    {
        $path = str_replace(base_path().'/', '', $this->viewFile);

        return "Did you mean `$$this->suggested`?";
    }

    public function getRunButtonText(): string
    {
        return 'Fix typo';
    }

    public function getSolutionDescription(): string
    {
        return '';
    }

    public function getRunParameters(): array
    {
        return [
            'variableName' => $this->variableName,
            'viewFile' => $this->viewFile,
            'suggested' => $this->suggested,
        ];
    }

    public function isRunnable(array $parameters = []): bool
    {
        return $this->fixTypo($this->getRunParameters()) !== false;
    }

    public function run(array $parameters = []): void
    {
        $output = $this->fixTypo($parameters);
        if ($output === false) {
            return;
        }

        file_put_contents($parameters['viewFile'], $output);
    }

    protected function fixTypo(array $parameters = [])
    {
        if ($this->isBlackListed($parameters['suggested'])) {
            return false;
        }

        if (! $this->isAlphaNumericWithUnderscore($parameters['suggested'])) {
            return false;
        }

        $originalContents = file_get_contents($parameters['viewFile']);
        $newContents = str_replace('$'.$parameters['variableName'], '$'.$parameters['suggested'], $originalContents);

        $originalTokens = token_get_all(Blade::compileString($originalContents));
        $newTokens = token_get_all(Blade::compileString($newContents));

        $expectedTokens = $this->generateExpectedTokens($originalTokens, $parameters['variableName'], $parameters['suggested']);

        if ($expectedTokens !== $newTokens) {
            return false;
        }

        return $newContents;
    }

    protected function isAlphaNumericWithUnderscore(string $input): bool
    {
        return preg_match('/^[a-zA-Z]+[a-zA-Z0-9_]+$/', $input);
    }

    protected function generateExpectedTokens(array $originalTokens, string $variableName, string $suggested): array
    {
        $expectedTokens = $originalTokens;
        foreach ($expectedTokens as $key => $token) {
            if ($token[0] === T_VARIABLE && $token[1] === '$'.$variableName) {
                $expectedTokens[$key][1] = "$$suggested";
            }
        }

        return $expectedTokens;
    }

    private function isBlackListed(string $suggested): bool
    {
        $suggested = strtolower($suggested);

        return in_array($suggested, [
            'globals',
            '_get',
            '_post',
            '_cookie',
            '_env',
        ]);
    }
}
