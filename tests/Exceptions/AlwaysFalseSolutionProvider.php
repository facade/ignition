<?php

namespace Facade\Ignition\Tests\Exceptions;

use Facade\IgnitionContracts\BaseSolution;
use Throwable;
use Facade\IgnitionContracts\Solution;
use Facade\IgnitionContracts\HasSolutionsForThrowable;

class AlwaysFalseSolutionProvider implements HasSolutionsForThrowable
{

    public function canSolve(Throwable $throwable): bool
    {
        return false;
    }

    public function getSolutions(Throwable $throwable): array
    {
        return [new BaseSolution('Base Solution')];
    }
}
