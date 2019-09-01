<?php

namespace Facade\Ignition\SolutionProviders;

use Throwable;
use RuntimeException;
use Facade\IgnitionContracts\Solution;
use Facade\Ignition\Exceptions\ViewException;
use Facade\IgnitionContracts\HasSolutionsForThrowable;
use Facade\Ignition\Solutions\MakeViewVariableOptionalSolution;

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
        return [
            new MakeViewVariableOptionalSolution($this->variableName, $this->viewFile)
        ];
    }
}
