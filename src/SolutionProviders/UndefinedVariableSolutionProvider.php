<?php

namespace Facade\Ignition\SolutionProviders;

use Throwable;
use RuntimeException;
use Facade\IgnitionContracts\Solution;
use Facade\Ignition\Exceptions\ViewException;
use Facade\IgnitionContracts\HasSolutionsForThrowable;
use Facade\Ignition\Solutions\MakeViewVariableOptionalSolution;
use Facade\Ignition\Solutions\SuggestCorrectVariableNameSolution;

class UndefinedVariableSolutionProvider implements HasSolutionsForThrowable
{
    public function canSolve(Throwable $throwable): bool
    {
        if (! $throwable instanceof ViewException) {
            return false;
        }

        $pattern = '/Undefined variable: (.*?) \(View: (.*?)\)/';

        preg_match($pattern, $throwable->getMessage(), $matches);
        if (count($matches) === 3) {
            list($string, $this->variableName, $this->viewFile) = $matches;
            return true;
        }
    }

    public function getSolutions(Throwable $throwable): array
    {
        $solutions = [];

        $variableName = $this->variableName;
        $viewFile = $this->viewFile;

        $solutions = collect($throwable->getViewData())->map(function ($value, $key) use ($variableName) {
            similar_text($variableName, $key, $percentage);
            return ['match' => $percentage, 'value' => $value ];
        })->sortByDesc('match')->filter(function($var, $key) {
            return $var['match'] > 40;
        })->keys()->map(function($suggestion) use ($variableName, $viewFile) {
            return new SuggestCorrectVariableNameSolution($variableName, $viewFile, $suggestion);
        })->toArray();

        $solutions[] = new MakeViewVariableOptionalSolution($this->variableName, $this->viewFile);
        return $solutions;
    }
}
