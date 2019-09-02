<?php

namespace Facade\Ignition\SolutionProviders;

use Throwable;
use Illuminate\Support\Facades\DB;
use Facade\IgnitionContracts\HasSolutionsForThrowable;
use Facade\Ignition\Solutions\SuggestUsingCorrectDbNameSolution;

class DefaultDbNameSolutionProvider implements HasSolutionsForThrowable
{
    public function canSolve(Throwable $throwable): bool
    {
        if ($this->canTryDatabaseConnection()) {
            try {
                DB::connection()->select('SELECT 1');
            } catch (\Exception $e) {
                return env('DB_DATABASE') === 'homestead';
            }
        }

        return false;
    }

    public function getSolutions(Throwable $throwable): array
    {
        return [new SuggestUsingCorrectDbNameSolution()];
    }

    protected function canTryDatabaseConnection()
    {
        return version_compare(app()->version(), '5.6.28', '>');
    }
}
