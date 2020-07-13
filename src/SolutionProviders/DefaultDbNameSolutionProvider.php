<?php

namespace Facade\Ignition\SolutionProviders;

use Exception;
use Facade\Ignition\Solutions\SuggestUsingCorrectDbNameSolution;
use Facade\IgnitionContracts\HasSolutionsForThrowable;
use Illuminate\Support\Facades\DB;
use Throwable;

class DefaultDbNameSolutionProvider implements HasSolutionsForThrowable
{
    const MYSQL_UNKNOWN_DATABASE_CODE = 1049;

    public function canSolve(Throwable $throwable): bool
    {
        try {
            DB::connection()->select('SELECT 1');
        } catch (Exception $exception) {
            if ($this->isUnknownDatabaseCode($exception->getCode())) {
                return in_array(env('DB_DATABASE'), ['homestead', 'laravel']);
            }
        }

        return false;
    }

    public function getSolutions(Throwable $throwable): array
    {
        return [new SuggestUsingCorrectDbNameSolution()];
    }

    protected function isUnknownDatabaseCode($code): bool
    {
        return $code === static::MYSQL_UNKNOWN_DATABASE_CODE;
    }
}
