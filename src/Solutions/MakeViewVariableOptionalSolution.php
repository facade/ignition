<?php

namespace Facade\Ignition\Solutions;

use Illuminate\Support\Facades\Artisan;
use Facade\IgnitionContracts\RunnableSolution;

class MakeViewVariableOptionalSolution implements RunnableSolution
{

    private $variableName;
    private $viewFile;

    public function __construct($variableName = null, $viewFile = null)
    {
        $this->variableName = $variableName;
        $this->viewFile = $viewFile;
    }

    public function getSolutionTitle(): string
    {
        return 'Variable is not set for the view';
    }

    public function getDocumentationLinks(): array
    {
        return [
            'Blade - Displaying Data' => 'https://laravel.com/docs/5.8/blade#displaying-data',
        ];
    }

    public function getSolutionActionDescription(): string
    {
        $path = str_replace(base_path() . '/', '', $this->viewFile);
        $output = [
            'Make the variable optional in the blade template.',
            'Replace `{{ $' . $this->variableName . ' }}` with `{{ $' . $this->variableName . ' ?? \'\' }}`'
        ];
        return implode(PHP_EOL, $output);
    }

    public function getRunButtonText(): string
    {
        return 'Make variable optional';
    }

    public function getSolutionDescription(): string
    {
        return '';
    }

    public function getRunParameters(): array
    {
        return [
            'variableName' => $this->variableName,
            'viewFile' => $this->viewFile
        ];
    }

    public function run(array $parameters = [])
    {
        $originalContents = file_get_contents($parameters['viewFile']);
        $contents = str_replace('$' . $parameters['variableName'], '$' . $parameters['variableName'] . " ?? ''", $originalContents);
        file_put_contents($parameters['viewFile'], $contents);
    }
}
