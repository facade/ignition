<?php

namespace Facade\Ignition\Tests\Solutions;

use Exception;
use Facade\Ignition\SolutionProviders\RunningLaravelDuskInProductionProvider;
use Facade\Ignition\Tests\TestCase;

class RunningLaravelDuskInProductionSolutionProviderTest extends TestCase
{
    /** @test */
    public function it_can_solve_dusk_in_production_exception()
    {
        $exception = $this->generate_dusk_exception();
        $canSolve = app(RunningLaravelDuskInProductionProvider::class)->canSolve($exception);
        [$first_solution, $second_solution] = app(RunningLaravelDuskInProductionProvider::class)->getSolutions($exception);

        $this->assertTrue($canSolve);
        $this->assertSame($first_solution->getSolutionTitle(), 'Laravel Dusk should not be run in production.');
        $this->assertSame($first_solution->getSolutionDescription(), 'Install the dependencies with the `--no-dev` flag.');

        $this->assertSame($second_solution->getSolutionTitle(), 'Laravel Dusk can be run in other environments.');
        $this->assertSame($second_solution->getSolutionDescription(), 'Consider setting the `APP_ENV` to something other than `production` like `local` for example.');
    }

    private function generate_dusk_exception(): Exception
    {
        return new Exception('It is unsafe to run Dusk in production.');
    }
}
