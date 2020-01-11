<?php

namespace Facade\Ignition\Tests\Solutions;

use Facade\Ignition\SolutionProviders\MissingSemicolonSolutionProvider;
use Facade\Ignition\Tests\stubs\Controllers\MissingSemicolonController;
use Facade\Ignition\Tests\TestCase;
use Illuminate\Support\Facades\View;
use ParseError;

class MissingSemicolonSolutionProviderTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        View::addLocation(__DIR__.'/../stubs/views');
    }

    /** @test */
    public function it_can_solve_missing_semicolon_exception()
    {
        try {
            require './tests/stubs/Controllers/MissingSemicolonController.stub';
        } catch (ParseError $error) {
            $exception = $error;
        }
        $canSolve = app(MissingSemicolonSolutionProvider::class)->canSolve($exception);

        $this->assertTrue($canSolve);

        /** @var \Facade\IgnitionContracts\Solution $solution */
        $solutions = app(MissingSemicolonSolutionProvider::class)->getSolutions($exception);
        $parameters = $solutions[0]->getRunParameters();
        $this->assertTrue($solutions[0]->isRunnable($parameters));
    }
}
