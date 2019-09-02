<?php

namespace Facade\Ignition\Context;

use Illuminate\Http\Request;
use Facade\FlareClient\Context\RequestContext;

class LaravelRequestContext extends RequestContext
{
    /** @var \Illuminate\Http\Request */
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function getUser(): array
    {
        $user = $this->request->user();

        if (! $user) {
            return [];
        }

        if (method_exists($user, 'toFlare')) {
            return $user->toFlare();
        }

        if (method_exists($user, 'toArray')) {
            return $user->toArray();
        }

        return [];
    }

    public function getRoute(): array
    {
        $route = $this->request->route();

        return [
            'route' => optional($route)->getName(),
            'routeParameters' => collect(optional($route)->parameters ?? [])->toArray(),
            'controllerAction' => optional($route)->getActionName(),
            'middleware' => array_values(optional($route)->gatherMiddleware() ?? []),
        ];
    }

    public function toArray(): array
    {
        $properties = parent::toArray();

        $properties['route'] = $this->getRoute();

        $properties['user'] = $this->getUser();

        return $properties;
    }
}
