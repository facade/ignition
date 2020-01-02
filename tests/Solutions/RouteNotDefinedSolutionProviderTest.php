<?php

namespace Facade\Ignition\Tests\Solutions;

use Facade\Ignition\SolutionProviders\RouteNotDefinedSolutionProvider;
use Facade\Ignition\Tests\TestCase;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

class RouteNotDefinedSolutionProviderTest extends TestCase
{
    /** @test */
    public function it_can_solve_a_route_not_defined_exception()
    {
        $canSolve = app(RouteNotDefinedSolutionProvider::class)->canSolve($this->getRouteNotDefinedException());

        $this->assertTrue($canSolve);
    }

    /** @test */
    public function it_can_recommend_changing_the_route_name()
    {
        Route::get('/test', 'TestController@typo')->name('test.typo');

        /** @var \Facade\IgnitionContracts\Solution $solution */
        $solution = app(RouteNotDefinedSolutionProvider::class)->getSolutions($this->getRouteNotDefinedException())[0];

        $this->assertTrue(Str::contains($solution->getSolutionDescription(), 'Did you mean `test.typo`?'));
    }

    /** @test */
    public function it_wont_recommend_another_route_if_the_names_are_too_different()
    {
        Route::get('/test', 'TestController@typo')->name('test.typo');

        /** @var \Facade\IgnitionContracts\Solution $solution */
        $solution = app(RouteNotDefinedSolutionProvider::class)->getSolutions($this->getRouteNotDefinedException('test.is-too-different'))[0];

        $this->assertFalse(Str::contains($solution->getSolutionDescription(), 'Did you mean'));
    }

    protected function getRouteNotDefinedException(string $route = 'test.typoo'): RouteNotFoundException
    {
        return new RouteNotFoundException("Route [{$route}] not defined.");
    }
}
