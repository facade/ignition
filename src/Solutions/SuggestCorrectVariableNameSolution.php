<?php

namespace Facade\Ignition\Solutions;

use Illuminate\Support\Facades\Artisan;
use Facade\IgnitionContracts\RunnableSolution;
use Illuminate\Support\Facades\Blade;

class SuggestCorrectVariableNameSolution implements RunnableSolution
{

    private $variableName;
    private $viewFile;

    public function __construct($variableName = null, $viewFile = null, $suggested = null)
    {
        $this->variableName = $variableName;
        $this->viewFile = $viewFile;
        $this->suggested = $suggested;
    }

    public function getSolutionTitle(): string
    {
        return 'Possible typo $' . $this->variableName;
    }

    public function getDocumentationLinks(): array
    {
        return [];
    }

    public function getSolutionActionDescription(): string
    {
        $path = str_replace(base_path() . '/', '', $this->viewFile);
        $output = [
            'Did you mean `$' . $this->suggested . '`?',
        ];
        return implode(PHP_EOL, $output);
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
            'suggested' => $this->suggested
        ];
    }

    public function isRunnable(array $parameters = [])
    {
        return $this->fixTypo($this->getRunParameters()) !== false;
    }

    public function run(array $parameters = [])
    {
        $output = $this->fixTypo($parameters);
        if ($output !== false) {
            file_put_contents($parameters['viewFile'], $output);
        }
    }

    public function fixTypo(array $parameters = [])
    {
        $originalContents = file_get_contents($parameters['viewFile']);
        $newContents = str_replace('$' . $parameters['variableName'], '$' . $parameters['suggested'], $originalContents);

        // Compile blade, tokenize
        $originalTokens = token_get_all(Blade::compileString($originalContents));
        $newTokens = token_get_all(Blade::compileString($newContents));

        // Generate what we expect the tokens to be after we change the blade file
        $expectedTokens = $originalTokens;
        foreach ($expectedTokens as $key => $token) {
            if ($token[0] === T_VARIABLE && $token[1] === '$' . $parameters['variableName']) {
                $expectedTokens[$key][1] = '$' . $parameters['suggested'];
            }
        }
        if ($expectedTokens !== $newTokens) {
            return false;
        }

        return $newContents;
    }

}
