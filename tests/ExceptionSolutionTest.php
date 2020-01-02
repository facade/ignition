<?php

namespace Facade\Ignition\Tests;

use Facade\Ignition\SolutionProviders\BadMethodCallSolutionProvider;
use Facade\Ignition\SolutionProviders\MissingAppKeySolutionProvider;
use Facade\Ignition\SolutionProviders\SolutionProviderRepository;
use Facade\Ignition\Tests\Exceptions\AlwaysFalseSolutionProvider;
use Facade\Ignition\Tests\Exceptions\AlwaysTrueSolutionProvider;
use Facade\IgnitionContracts\BaseSolution;
use Illuminate\Foundation\Auth\User;
use RuntimeException;

class ExceptionSolutionTest extends TestCase
{
    /** @test */
    public function it_returns_possible_solutions()
    {
        $repository = new SolutionProviderRepository();

        $repository->registerSolutionProvider(AlwaysTrueSolutionProvider::class);
        $repository->registerSolutionProvider(AlwaysFalseSolutionProvider::class);

        $solutions = $repository->getSolutionsForThrowable(new \Exception());

        $this->assertNotNull($solutions);
        $this->assertCount(1, $solutions);
        $this->assertTrue($solutions[0] instanceof BaseSolution);
    }

    /** @test */
    public function it_returns_possible_solutions_when_registered_together()
    {
        $repository = new SolutionProviderRepository();

        $repository->registerSolutionProviders([
            AlwaysTrueSolutionProvider::class,
            AlwaysFalseSolutionProvider::class,
        ]);

        $solutions = $repository->getSolutionsForThrowable(new \Exception());

        $this->assertNotNull($solutions);
        $this->assertCount(1, $solutions);
        $this->assertTrue($solutions[0] instanceof BaseSolution);
    }

    /** @test */
    public function it_can_ignore_solution_providers()
    {
        $this->app->make('config')->set('ignition.ignored_solution_providers', [
            AlwaysTrueSolutionProvider::class,
        ]);

        $repository = new SolutionProviderRepository();

        $repository->registerSolutionProvider(AlwaysTrueSolutionProvider::class);
        $repository->registerSolutionProvider(AlwaysFalseSolutionProvider::class);

        $solutions = $repository->getSolutionsForThrowable(new \Exception());

        $this->assertNotNull($solutions);
        $this->assertCount(0, $solutions);
    }

    /** @test */
    public function it_can_suggest_bad_method_call_exceptions()
    {
        if (version_compare($this->app->version(), '5.6.3', '<')) {
            $this->markTestSkipped('Laravel version < 5.6.3 do not support bad method call solutions');
        }

        try {
            collect([])->faltten();
        } catch (\Exception $exception) {
            $solution = new BadMethodCallSolutionProvider();

            $this->assertTrue($solution->canSolve($exception));
        }
    }

    /** @test */
    public function it_can_propose_a_solution_for_bad_method_call_exceptions_on_collections()
    {
        if (version_compare($this->app->version(), '5.6.3', '<')) {
            $this->markTestSkipped('Laravel version < 5.6.3 do not support bad method call solutions');
        }

        try {
            collect([])->frist(function ($item) {
            });
        } catch (\Exception $exception) {
            $solution = new BadMethodCallSolutionProvider();

            $this->assertSame('Did you mean Illuminate\Support\Collection::first() ?', $solution->getSolutions($exception)[0]->getSolutionDescription());
        }
    }

    /** @test */
    public function it_can_propose_a_solution_for_bad_method_call_exceptions_on_models()
    {
        if (version_compare($this->app->version(), '5.7', '<')) {
            $this->markTestSkipped('Laravel version < 5.7.0 does not provide the actual called class for model exceptions.');
        }

        try {
            $user = new User();
            $user->sarve();
        } catch (\Exception $exception) {
            $solution = new BadMethodCallSolutionProvider();

            $this->assertSame('Did you mean Illuminate\Foundation\Auth\User::save() ?', $solution->getSolutions($exception)[0]->getSolutionDescription());
        }
    }

    /** @test */
    public function it_can_propose_a_solution_for_missing_app_key_exceptions()
    {
        $exception = new RuntimeException(
            'No application encryption key has been specified.'
        );

        $solution = new MissingAppKeySolutionProvider();

        $this->assertSame('Generate your application encryption key using `php artisan key:generate`.', $solution->getSolutions($exception)[0]->getSolutionActionDescription());
    }
}
