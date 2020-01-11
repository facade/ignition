<?php

namespace Facade\Ignition\Tests\Solutions;

use Facade\Ignition\SolutionProviders\MergeConflictSolutionProvider;
use Facade\Ignition\Tests\stubs\Controllers\GitConflictController;
use Facade\Ignition\Tests\TestCase;
use Illuminate\Support\Facades\View;
use ParseError;

class MergeConflictSolutionProviderTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        View::addLocation(__DIR__.'/../stubs/views');
    }

    /** @test */
    public function it_can_solve_merge_conflict_exception()
    {
        try {
            app(GitConflictController::class);
        } catch (ParseError $error) {
            $exception = $error;
        }
        $canSolve = app(MergeConflictSolutionProvider::class)->canSolve($exception);

        $this->assertTrue($canSolve);
    }
}
