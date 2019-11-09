<?php

namespace Facade\Ignition\SolutionProviders;

use Exception;
use Throwable;
use Facade\IgnitionContracts\BaseSolution;
use Facade\IgnitionContracts\HasSolutionsForThrowable;

class RunningLaravelDuskInProductionProvider implements HasSolutionsForThrowable
{
    public function canSolve(Throwable $throwable): bool
    {
        if (! $throwable instanceof Exception) {
            return false;
        }

        return $throwable->getMessage() === 'It is unsafe to run Dusk in production.';
    }

    public function getSolutions(Throwable $throwable): array
    {
        return [
            BaseSolution::create('Laravel Dusk should not be run in production.')
                ->setSolutionDescription('Install the dependencies with the `--no-dev` flag.'),
            BaseSolution::create('Laravel Dusk can be run in other environments.')
                ->setSolutionDescription('Consider setting the `APP_ENV` to something other than `production` like `local` for example.'),
        ];
    }
}
