<?php

namespace Facade\Ignition\Tests\Solutions;

use Exception;
use Facade\Ignition\SolutionProviders\UnsupportedCacheLockDriverProvider;
use Facade\Ignition\Tests\TestCase;
use Facade\IgnitionContracts\Solution;

class UnsupportedCacheLockDriverSolutionProviderTest extends TestCase
{
    public function cacheLockExceptionsProvider()
    {
        return [
            ['Call to undefined method Illuminate\Cache\FileStore::lock()'],
            ['Call to undefined method Illuminate\Cache\DatabaseStore::lock()'],
            ['Call to undefined method Illuminate\Cache\ApcStore::lock()'],
        ];
    }

    /**
     * @dataProvider cacheLockExceptionsProvider
     * @test
     */
    public function it_can_solve_unsupported_cache_lock_driver_exceptions($exceptionMessage)
    {
        $exception = new Exception($exceptionMessage);

        $this->assertTrue(app(UnsupportedCacheLockDriverProvider::class)->canSolve($exception));

        /** @var $solution Solution */
        [$solution] = app(UnsupportedCacheLockDriverProvider::class)->getSolutions($exception);

        $this->assertStringContainsString('current cache driver does not support atomic locks', $solution->getSolutionTitle());
    }
}
