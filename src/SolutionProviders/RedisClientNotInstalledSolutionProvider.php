<?php

namespace Facade\Ignition\SolutionProviders;

use Throwable;
use LogicException;
use Facade\IgnitionContracts\BaseSolution;
use Facade\IgnitionContracts\HasSolutionsForThrowable;

class RedisClientNotInstalledSolutionProvider implements HasSolutionsForThrowable
{
    public function canSolve(Throwable $throwable): bool
    {
        if (! $throwable instanceof LogicException && ! $throwable instanceof ViewException) {
            return false;
        }

        return true;
    }


    public function getSolutions(Throwable $throwable): array
    {
        $client = config('database.redis.client');
        list($message, $description) = $this->getSolutionMessage($client);

        return [
            BaseSolution::create($message)
                ->setSolutionDescription($description),
        ];
    }

    protected function getSolutionMessage(string $client): array
    {
        $predis = file_exists(base_path('vendor/predis/predis'));
        $phpredis = file_exists(base_path('vendor/phpredis/phpredis'));

        if ($client === 'predis') {
            if ($predis === false && $phpredis === true) {
                return [
                    'predis is set as client, but it wasn\'t installed.',
                    'either run `composer require predis/predis` or default to phpredis which is installed',
                ];
            }
        }

        if ($predis === true && $phpredis === false) {
            return [
                'phpredis is set as client, but it wasn\'t installed.',
                'either run `composer require phpredis/phpredis` or set the `REDIS_CLIENT` to predis in your `.env`.',
            ];
        }

        return [
            'there was no client installed.',
            'run `composer require phpredis/phpredis`',
        ];
    }
}
