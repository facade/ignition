<?php

namespace Facade\Ignition\Solutions;

use Illuminate\Support\Facades\Artisan;
use Facade\IgnitionContracts\RunnableSolution;

class MakeViewVariableOptionalSolution implements RunnableSolution
{

    private $variableName;
    private $viewFile;

    public function __construct($variableName, $viewFile)
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
        return 'Make the variable optional in the blade template. ```{{ $' . $this->variableName . ' ?? \'\' }}```';
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

    }
}
