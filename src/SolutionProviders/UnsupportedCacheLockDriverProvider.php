<?php

namespace Facade\Ignition\SolutionProviders;

use Exception;
use Facade\IgnitionContracts\BaseSolution;
use Facade\IgnitionContracts\HasSolutionsForThrowable;
use Throwable;

class UnsupportedCacheLockDriverProvider implements HasSolutionsForThrowable
{
    public function canSolve(Throwable $throwable): bool
    {
        if (! $throwable instanceof Exception) {
            return false;
        }

        return in_array($throwable->getMessage(), [
            'Call to undefined method Illuminate\Cache\FileStore::lock()',
            'Call to undefined method Illuminate\Cache\DatabaseStore::lock()',
            'Call to undefined method Illuminate\Cache\ApcStore::lock()',
        ]);
    }

    public function getSolutions(Throwable $throwable): array
    {
        return [
            BaseSolution::create('Your current cache driver does not support atomic locks.')
                ->setSolutionDescription('Consider switching your cache driver to `redis`, `memcached`, or `dynamodb`.')
                ->setDocumentationLinks(['Cache: Atomic Locks docs' => 'https://laravel.com/docs/7.x/cache#atomic-locks']),
        ];
    }
}
