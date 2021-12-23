<?php

namespace Facade\Ignition\Tests\Context;

use Facade\Ignition\Context\LaravelRequestContext;
use Facade\Ignition\Tests\TestCase;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class LaravelRequestContextTest extends TestCase
{
    protected function createRequest($method, $uri, $parameters = [], $cookies = [], $files = [], $server = [], $content = null)
    {
        $files = array_merge($files, $this->extractFilesFromDataArray($parameters));

        $symfonyRequest = SymfonyRequest::create(
            $this->prepareUrlForRequest($uri),
            $method,
            $parameters,
            $cookies,
            $files,
            array_replace($this->serverVariables, $server),
            $content
        );

        return Request::createFromBase($symfonyRequest);
    }

    /** @test */
    public function it_returns_route_name_in_context_data()
    {
        $route = Route::get('/route/', function () {
        })->name('routeName');

        $request = $this->createRequest('GET', '/route');

        $route->bind($request);

        $request->setRouteResolver(function () use ($route) {
            return $route;
        });

        $context = new LaravelRequestContext($request);

        $contextData = $context->toArray();

        $this->assertSame('routeName', $contextData['route']['route']);
    }

    /** @test */
    public function it_returns_route_parameters_in_context_data()
    {
        $route = Route::get('/route/{parameter}/{otherParameter}', function () {
        });

        $request = $this->createRequest('GET', '/route/value/second');

        $route->bind($request);

        $request->setRouteResolver(function () use ($route) {
            return $route;
        });

        $context = new LaravelRequestContext($request);

        $contextData = $context->toArray();

        $this->assertSame([
            'parameter' => 'value',
            'otherParameter' => 'second',
        ], $contextData['route']['routeParameters']);
    }

    /** @test */
    public function it_will_call_the_to_flare_method_on_route_parameters_when_it_exists()
    {
        $route = Route::get('/route/{user}', function ($user) {
        });

        $request = $this->createRequest('GET', '/route/1');

        $route->bind($request);

        $request->setRouteResolver(function () use ($route) {
            $route->setParameter('user', new class () {
                public function toFlare(): array
                {
                    return ['stripped'];
                }
            });

            return $route;
        });

        $context = new LaravelRequestContext($request);

        $contextData = $context->toArray();

        $this->assertSame([
            'user' => ['stripped'],
        ], $contextData['route']['routeParameters']);
    }

    /** @test */
    public function it_returns_the_url()
    {
        $request = $this->createRequest('GET', '/route', []);

        $context = new LaravelRequestContext($request);

        $request = $context->getRequest();

        $this->assertSame('http://localhost/route', $request['url']);
    }

    /** @test */
    public function it_returns_the_cookies()
    {
        $request = $this->createRequest('GET', '/route', [], ['cookie' => 'noms']);

        $context = new LaravelRequestContext($request);

        $this->assertSame(['cookie' => 'noms'], $context->getCookies());
    }

    /** @test */
    public function it_returns_the_authenticated_user()
    {
        $user = new User();
        $user->forceFill([
            'id' => 1,
            'email' => 'marcel@beyondco.de',
        ]);

        $request = $this->createRequest('GET', '/route', [], ['cookie' => 'noms']);
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        $context = new LaravelRequestContext($request);
        $contextData = $context->toArray();

        $this->assertSame($user->toArray(), $contextData['user']);
    }

    /** @test */
    public function it_the_authenticated_user_model_has_a_toFlare_method_it_will_be_used_to_collect_user_data()
    {
        $user = new class () extends User {
            public function toFlare()
            {
                return ['id' => $this->id];
            }
        };

        $user->forceFill([
            'id' => 1,
            'email' => 'marcel@beyondco.de',
        ]);

        $request = $this->createRequest('GET', '/route', [], ['cookie' => 'noms']);
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        $context = new LaravelRequestContext($request);
        $contextData = $context->toArray();

        $this->assertSame(['id' => $user->id], $contextData['user']);
    }

    /** @test */
    public function it_the_authenticated_user_model_has_no_matching_method_it_will_return_no_user_data()
    {
        $user = new class () {
        };

        $request = $this->createRequest('GET', '/route', [], ['cookie' => 'noms']);
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        $context = new LaravelRequestContext($request);
        $contextData = $context->toArray();

        $this->assertSame([], $contextData['user']);
    }

    /** @test */
    public function it_the_authenticated_user_model_is_broken_it_will_return_no_user_data()
    {
        $user = new class () extends User {
            protected $appends = ['invalid'];
        };

        $request = $this->createRequest('GET', '/route', [], ['cookie' => 'noms']);
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        $context = new LaravelRequestContext($request);
        $contextData = $context->toArray();

        $this->assertSame([], $contextData['user']);
    }
}
