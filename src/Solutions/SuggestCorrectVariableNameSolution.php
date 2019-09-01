<?php

namespace Facade\Ignition\Solutions;

use Illuminate\Support\Facades\Artisan;
use Facade\IgnitionContracts\RunnableSolution;

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
        return 'Possible typo in $' . $this->variableName;
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

    public function run(array $parameters = [])
    {
        $originalContents = file_get_contents($parameters['viewFile']);
        $contents = str_replace('$' . $parameters['variableName'], '$' . $parameters['suggested'], $originalContents);
        file_put_contents($parameters['viewFile'], $contents);
    }
}
