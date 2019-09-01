<?php

namespace Facade\Ignition\SolutionProviders;

use Throwable;
use RuntimeException;
use Facade\IgnitionContracts\Solution;
use Facade\Ignition\Solutions\MakeViewVariableOptionalSolution;
use Facade\IgnitionContracts\HasSolutionsForThrowable;

class UndefinedVariableSolutionProvider implements HasSolutionsForThrowable
{
    public function canSolve(Throwable $throwable): bool
    {
        if (! $throwable instanceof \Facade\Ignition\Exceptions\ViewException) {
            return false;
        }
        $message = $throwable->getMessage();
        preg_match('/Undefined variable: (.*?) \(View: (.*?)\)/', $message, $matches);
        if (count($matches) == 3) {
            $this->variableName = $matches[1];
            $this->viewFile = $matches[2];
            return 'Variable not defined';
        }
    }

    public function getSolutions(Throwable $throwable): array
    {
        return [
            new MakeViewVariableOptionalSolution($this->variableName, $this->viewFile)
        ];
    }
}
