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
                ->setSolutionDescription('You should regenerate your assets')
                ->setDocumentationLinks([
                    'Running Mix' => 'https://laravel.com/docs/master/mix#running-mix'
                ])
        ];
    }
}
