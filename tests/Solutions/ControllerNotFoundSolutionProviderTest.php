<?php

namespace Facade\Ignition\Tests\Solutions;

use ReflectionException;
use Illuminate\Support\Str;
use Facade\Ignition\Tests\TestCase;
use Illuminate\Contracts\Container\BindingResolutionException;
use Facade\Ignition\SolutionProviders\ControllerNotFoundSolutionProvider;

class ControllerNotFoundSolutionProviderTest extends TestCase
{
    /** @var ControllerNotFoundSolutionProvider */
    private $provider;

    public function setUp(): void
    {
        parent::setUp();

        $this->provider = new ControllerNotFoundSolutionProvider();
    }

    /** @test */
    public function it_gets_controller_from_binding_resolution_exception()
    {
        $exception = new BindingResolutionException('Target class [App\Http\Controllers\Auth\LoginController] does not exist.');
        $solutions = $this->provider->getSolutions($exception);

        $this->assertTrue(Str::endsWith($solutions[0]->getSolutionDescription(), '`php artisan make:controller Auth/LoginController`.'));
    }

    /** @test */
    public function it_gets_controller_from_reflection_exception()
    {
        $exception = new ReflectionException('Class App\Http\Controllers\MissingController does not exist');
        $solutions = $this->provider->getSolutions($exception);

        $this->assertTrue(Str::endsWith($solutions[0]->getSolutionDescription(), '`php artisan make:controller MissingController`.'));
    }
}
