<?php

namespace Facade\Ignition\SolutionProviders;

use Throwable;
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

        if (! isset($variableName)) {
            return [];
        }

        $solutions = $this->findCorrectVariableSolutions($throwable, $variableName, $viewFile);
        $solutions[] = $this->findOptionalVariableSolution($variableName, $viewFile);

        return $solutions;
    }

    protected function findCorrectVariableSolutions(Throwable $throwable, string $variableName, string $viewFile): array
    {
        return collect($throwable->getViewData())->map(function ($value, $key) use ($variableName) {
            similar_text($variableName, $key, $percentage);

            return ['match' => $percentage, 'value' => $value];
        })->sortByDesc('match')->filter(function ($var, $key) {
            return $var['match'] > 40;
        })->keys()->map(function ($suggestion) use ($variableName, $viewFile) {
            return new SuggestCorrectVariableNameSolution($variableName, $viewFile, $suggestion);
        })->map(function ($solution) {
            return $solution->isRunnable()
                ? $solution
                : BaseSolution::create($solution->getSolutionTitle())
                    ->setSolutionDescription($solution->getSolutionActionDescription());
        })->toArray();
    }

    protected function findOptionalVariableSolution(string $variableName, string $viewFile)
    {
        $optionalSolution = new MakeViewVariableOptionalSolution($variableName, $viewFile);

        return $optionalSolution->isRunnable()
            ? $optionalSolution
            : BaseSolution::create($optionalSolution->getSolutionTitle())
                ->setSolutionDescription($optionalSolution->getSolutionActionDescription());
    }

    protected function getNameAndView(Throwable $throwable): ?array
    {
        $pattern = '/Undefined variable: (.*?) \(View: (.*?)\)/';

        preg_match($pattern, $throwable->getMessage(), $matches);
        if (count($matches) === 3) {
            [$string, $variableName, $viewFile] = $matches;

            return compact('variableName', 'viewFile');
        }
    }
}
