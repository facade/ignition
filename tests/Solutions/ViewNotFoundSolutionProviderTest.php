<?php

namespace Facade\Ignition\Tests\Solutions;

use Facade\Ignition\SolutionProviders\ViewNotFoundSolutionProvider;
use Facade\Ignition\Tests\TestCase;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use InvalidArgumentException;

class ViewNotFoundSolutionProviderTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        View::addLocation(__DIR__.'/../stubs/views');
    }

    /** @test */
    public function it_can_solve_the_exception()
    {
        $canSolve = app(ViewNotFoundSolutionProvider::class)->canSolve($this->getViewNotFoundException());

        $this->assertTrue($canSolve);
    }

    /** @test */
    public function it_can_recommend_changing_a_typo_in_the_view_name()
    {
        /** @var \Facade\IgnitionContracts\Solution $solution */
        $solution = app(ViewNotFoundSolutionProvider::class)->getSolutions($this->getViewNotFoundException())[0];

        $this->assertStringContainsString('Did you mean `php-exception`?', $solution->getSolutionActionDescription());
    }

    /** @test */
    public function it_can_recommend_changing_a_view_typo_in_a_controller()
    {
        // Run an action within the controller
        try {
            app(\Facade\Ignition\Tests\stubs\Controllers\TestTypoController::class)->index();
        } catch (InvalidArgumentException $e) {
            $exception = $e;
        }
        $solution = app(ViewNotFoundSolutionProvider::class)->getSolutions($e)[0];

        $this->assertTrue($solution->isRunnable());
        $this->assertStringContainsString('Did you mean `blade-exception`?', $solution->getSolutionActionDescription());
        $this->assertTrue(Str::contains($solution->getSolutionDescription(), 'Did you mean `php-exception`?'));
    }

    /** @test */
    public function it_wont_recommend_another_controller_class_if_the_names_are_too_different()
    {
        $unknownView = 'a-view-that-doesnt-exist-and-is-not-a-typo';

        /** @var \Facade\IgnitionContracts\Solution $solution */
        $solution = app(ViewNotFoundSolutionProvider::class)->getSolutions($this->getViewNotFoundException($unknownView))[0];

        $this->assertFalse(Str::contains($solution->getSolutionDescription(), 'Did you mean'));
    }

    protected function getViewNotFoundException(string $view = 'phpp-exceptionn'): InvalidArgumentException
    {
        return new InvalidArgumentException("View [{$view}] not found.");
    }
}
