<?php

namespace Facade\Ignition\SolutionProviders;

use Facade\Ignition\Exceptions\ViewException;
use Illuminate\Support\Str;
use Throwable;
use Facade\IgnitionContracts\BaseSolution;
use Facade\IgnitionContracts\HasSolutionsForThrowable;

class MissingMixManifestSolutionProvider implements HasSolutionsForThrowable
{
    public function canSolve(Throwable $throwable): bool
    {
        if (! $throwable instanceof ViewException) {
            return false;
        }

        return Str::startsWith($throwable->getMessage(), 'The Mix manifest does not exist');
    }

    public function getSolutions(Throwable $throwable): array
    {
        return [
            BaseSolution::create('')
                ->setSolutionDescription('Generate the Mix manifest by using `npm install && npm run dev`.')
        ];
    }
}
