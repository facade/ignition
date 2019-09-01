<?php

namespace Facade\Ignition\Tests\Solutions;

use UnexpectedValueException;
use Facade\Ignition\Tests\TestCase;
use Illuminate\Support\Facades\Route;
use Facade\Ignition\Exceptions\ViewException;
use Facade\Ignition\Support\ComposerClassMap;
use Facade\Ignition\Tests\stubs\Controllers\TestTypoController;
use Facade\Ignition\SolutionProviders\UndefinedVariableSolutionProvider;

class UndefinedVariableSolutionProviderTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->app->bind(
            ComposerClassMap::class,
            function () {
                return new ComposerClassMap(__DIR__.'/../../vendor/autoload.php');
            }
        );
    }

    /** @test */
    public function it_can_solve_the_exception()
    {
        $canSolve = app(UndefinedVariableSolutionProvider::class)->canSolve($this->getUndefinedVariableException());

        $this->assertTrue($canSolve);
    }

    protected function getUndefinedVariableException(): ViewException
    {
        return new ViewException("Undefined variable: notSet (View: ./views/welcome.blade.php)");
    }
}
