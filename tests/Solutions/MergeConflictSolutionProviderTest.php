<?php

namespace Facade\Ignition\Tests\Solutions;

use Facade\Ignition\Tests\TestCase;
use Illuminate\Support\Facades\View;
use Facade\Ignition\SolutionProviders\MergeConflictSolutionProvider;

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
        copy(__DIR__.'/../stubs/Controllers/GitConflictController.stub', __DIR__.'/../stubs/Controllers/GitConflictController.php');
        try {
            app(\Facade\Ignition\Tests\stubs\Controllers\GitConflictController::class);
        } catch (\ParseError $e) {
            $exception = $e;
        }
        $canSolve = app(MergeConflictSolutionProvider::class)->canSolve($exception);

        $this->assertTrue($canSolve);
    }
}
