<?php

namespace Facade\Ignition\SolutionProviders;

use Throwable;
use Facade\IgnitionContracts\Solution;
use Facade\IgnitionContracts\BaseSolution;
use Facade\Ignition\Exceptions\ViewException;
use Facade\IgnitionContracts\HasSolutionsForThrowable;
use Facade\Ignition\Solutions\MakeViewVariableOptionalSolution;
use Facade\Ignition\Solutions\SuggestCorrectVariableNameSolution;

class UndefinedVariableSolutionProvider implements HasSolutionsForThrowable
{
    private $variableName;

    private $viewFile;

    public function canSolve(Throwable $throwable): bool
    {
        if (! $throwable instanceof ViewException) {
            return false;
        }

        return $this->getNameAndView($throwable) !== null;
    }

    public function getSolutions(Throwable $throwable): array
    {
        $solutions = [];

        extract($this->getNameAndView($throwable));
        $solutions = collect($throwable->getViewData())->map(function ($value, $key) use ($variableName) {
            similar_text($variableName, $key, $percentage);

            return ['match' => $percentage, 'value' => $value];
        })->sortByDesc('match')->filter(function ($var, $key) {
            return $var['match'] > 40;
        })->keys()->map(function ($suggestion) use ($variableName, $viewFile) {
            return new SuggestCorrectVariableNameSolution($variableName, $viewFile, $suggestion);
        })->map(function ($solution) {
            // If the solution isn't runnable, then just return the suggestions without the fix
            if ($solution->isRunnable()) {
                return $solution;
            } else {
                return BaseSolution::create($solution->getSolutionTitle())
                    ->setSolutionDescription($solution->getSolutionActionDescription());
            }
        })->toArray();

        $optionalSolution = new MakeViewVariableOptionalSolution($variableName, $viewFile);
        if ($optionalSolution->isRunnable()) {
            $solutions[] = $optionalSolution;
        } else {
            $solutions[] = BaseSolution::create($optionalSolution->getSolutionTitle())
                ->setSolutionDescription($optionalSolution->getSolutionActionDescription());
        }

        return $solutions;
    }

    private function getNameAndView(Throwable $throwable)
    {
        $pattern = '/Undefined variable: (.*?) \(View: (.*?)\)/';

        preg_match($pattern, $throwable->getMessage(), $matches);
        if (count($matches) === 3) {
            [$string, $variableName, $viewFile] = $matches;

            return compact('variableName', 'viewFile');
        }
    }
}
