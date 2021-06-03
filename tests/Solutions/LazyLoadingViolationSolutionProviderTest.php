<?php

namespace Facade\Ignition\Tests\Solutions;

use Exception;
use Facade\Ignition\SolutionProviders\LazyLoadingViolationSolutionProvider;
use Facade\Ignition\SolutionProviders\MissingMixManifestSolutionProvider;
use Facade\Ignition\Tests\TestCase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\LazyLoadingViolationException;
use Illuminate\Foundation\Auth\User;

class LazyLoadingViolationSolutionProviderTest extends TestCase
{
    /** @test */
    public function it_can_solve_lazy_loading_violations()
    {
        $canSolve = app(LazyLoadingViolationSolutionProvider::class)
            ->canSolve(new LazyLoadingViolationException(new User(), 'posts'));

        $this->assertTrue($canSolve);

        $canSolve = app(LazyLoadingViolationSolutionProvider::class)
            ->canSolve(new Exception('generic exception'));

        $this->assertFalse($canSolve);
    }

    public function it_can_provide_the_solution_for_lazy_loading_exceptions()
    {
        $solutions = app(LazyLoadingViolationSolutionProvider::class)
            ->getSolutions(new LazyLoadingViolationException(new User(), 'posts'));

        $this->assertCount(1, $solutions);
    }
}
