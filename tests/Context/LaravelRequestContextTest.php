<?php

namespace Facade\Ignition\Tests\Context;

use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Facade\Ignition\Context\LaravelRequestContext;
use Facade\Ignition\Tests\TestCase;
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
            'otherParameter' => 'second'
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
            'email' => 'marcel@beyondco.de'
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
        $user = new class extends User {
            public function toFlare() {
                return ['id' => $this->id];
            }
        };

        $user->forceFill([
            'id' => 1,
            'email' => 'marcel@beyondco.de'
        ]);

        $request = $this->createRequest('GET', '/route', [], ['cookie' => 'noms']);
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        $context = new LaravelRequestContext($request);
        $contextData = $context->toArray();

        $this->assertSame(['id' => $user->id], $contextData['user']);
    }
}
