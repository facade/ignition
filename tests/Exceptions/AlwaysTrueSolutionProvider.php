<?php

namespace Facade\Ignition\Tests\Exceptions;

use Throwable;
use Facade\IgnitionContracts\BaseSolution;
use Facade\IgnitionContracts\HasSolutionsForThrowable;

class AlwaysTrueSolutionProvider implements HasSolutionsForThrowable
{
    public function canSolve(Throwable $throwable): bool
    {
        return true;
    }

    public function getSolutions(Throwable $throwable): array
    {
        return [new BaseSolution('Base Solution')];
    }
}
