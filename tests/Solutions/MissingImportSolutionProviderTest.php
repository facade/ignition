<?php

namespace Facade\Ignition\Tests\Solutions;

use \Exception;
use Illuminate\Support\Str;
use UnexpectedValueException;
use Facade\Ignition\Tests\TestCase;
use Illuminate\Support\Facades\Route;
use Facade\Ignition\Support\ComposerClassMap;
use Symfony\Component\Debug\Exception\FatalThrowableError;
use Facade\Ignition\Tests\stubs\Controllers\MissingClassController;
use Facade\Ignition\SolutionProviders\MissingImportSolutionProvider;

class MissingImportSolutionProviderTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function it_can_solve_missing_class_exception()
    {
        try {
            app(MissingClassController::class)->index();
        } catch (\Throwable $error) {
            $exception = $error;
        }
        $canSolve = app(MissingImportSolutionProvider::class)->canSolve($exception);
        $this->assertTrue($canSolve);
        // /** @var \Facade\IgnitionContracts\Solution $solution */
        // $solutions = app(MissingSemicolonSolutionProvider::class)->getSolutions($exception);
        // $parameters = $solutions[0]->getRunParameters();
        // $this->assertTrue($solutions[0]->isRunnable($parameters));
    }


}
