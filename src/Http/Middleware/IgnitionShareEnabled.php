<?php

namespace Facade\Ignition\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Facade\Ignition\IgnitionConfig;

class IgnitionShareEnabled
{
    /** @var IgnitionConfig */
    protected $ignitionConfig;

    public function __construct(IgnitionConfig $ignitionConfig)
    {
        $this->ignitionConfig = $ignitionConfig;
    }

    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (! $this->ignitionConfig->getEnableShareButton()) {
            abort(404);
        }

        return $next($request);
    }
}
