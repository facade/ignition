<?php

namespace Facade\Ignition\SolutionProviders;

use Facade\Ignition\Solutions\LivewireDiscoverSolution;
use Facade\IgnitionContracts\HasSolutionsForThrowable;
use Illuminate\Database\QueryException;
use Throwable;

class MissingLivewireComponentSolutionProvider implements HasSolutionsForThrowable
{
    public function canSolve(Throwable $throwable): bool
    {
        if (! class_exists('ComponentNotFoundException') {
            return false;
        }
        if (! $throwable instanceof \Livewire\Exceptions\ComponentNotFoundException) {
            return false;
        }

        return true;
    }

    public function getSolutions(Throwable $throwable): array
    {
        return [new LivewireDiscoverSolution('A livewire component was not found')];
    }
}
