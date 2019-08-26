<?php

namespace Facade\Ignition\SolutionProviders;

use Illuminate\Support\Facades\DB;
use Facade\Ignition\Solutions\SuggestUsingCorrectDbNameSolution;
use Facade\IgnitionContracts\HasSolutionsForThrowable;
use Facade\IgnitionContracts\Solution;
use Throwable;

class DefaultDbNameSolutionProvider implements HasSolutionsForThrowable
{
    public function canSolve(Throwable $throwable): bool
    {
        try {
            DB::connection()->select('SELECT 1');
        } catch (\Exception $e) {
            return env('DB_DATABASE') === 'homestead';
        }

        return false;
    }

    public function getSolutions(Throwable $throwable): array
    {
        return [new SuggestUsingCorrectDbNameSolution()];
    }
}

