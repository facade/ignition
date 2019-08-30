<?php

namespace Facade\Ignition\SolutionProviders;

use Throwable;
use RuntimeException;
use Facade\Ignition\Solutions\GenerateAppKeySolution;
use Facade\IgnitionContracts\HasSolutionsForThrowable;

class MissingAppKeySolutionProvider implements HasSolutionsForThrowable
{
    public function canSolve(Throwable $throwable): bool
    {
        if (! $throwable instanceof RuntimeException) {
            return false;
        }

        return $throwable->getMessage() === 'No application encryption key has been specified.';
    }

    public function getSolutions(Throwable $throwable): array
    {
        return [new GenerateAppKeySolution()];
    }
}
