<?php

namespace Facade\Ignition\Tests\Solutions;

use Facade\Ignition\Exceptions\ViewException;
use Facade\Ignition\SolutionProviders\UndefinedVariableSolutionProvider;
use Facade\Ignition\Support\ComposerClassMap;
use Facade\Ignition\Tests\TestCase;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class UndefinedVariableSolutionProviderTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        View::addLocation(__DIR__.'/../stubs/views');

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
        return new ViewException('Undefined variable: notSet (View: ./views/welcome.blade.php)');
    }
}
