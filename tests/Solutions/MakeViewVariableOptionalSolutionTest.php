<?php

namespace Facade\Ignition\Tests\Solutions;

use Facade\Ignition\Solutions\MakeViewVariableOptionalSolution;
use Facade\Ignition\Support\ComposerClassMap;
use Facade\Ignition\Tests\TestCase;
use Illuminate\Support\Facades\View;

class MakeViewVariableOptionalSolutionTest extends TestCase
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
    public function it_does_not_open_scheme_paths()
    {
        $solution = $this->getSolutionForPath('php://filter/resource=./tests/stubs/views/blade-exception.blade.php');
        $this->assertFalse($solution->isRunnable());
    }

    /** @test */
    public function it_does_open_relative_paths()
    {
        $solution = $this->getSolutionForPath('./tests/stubs/views/blade-exception.blade.php');
        $this->assertTrue($solution->isRunnable());
    }

    /** @test */
    public function it_does_not_open_other_extentions()
    {
        $solution = $this->getSolutionForPath('./tests/stubs/views/php-exception.php');
        $this->assertFalse($solution->isRunnable());
    }

    protected function getSolutionForPath($path): MakeViewVariableOptionalSolution
    {
        return new MakeViewVariableOptionalSolution('notSet', $path);
    }
}
