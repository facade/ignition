<?php

namespace Facade\Ignition\Context;

use Illuminate\Http\Request;
use Facade\FlareClient\Context\ContextInterface;
use Facade\FlareClient\Context\ContextDetectorInterface;

class LaravelContextDetector implements ContextDetectorInterface
{
    public function detectCurrentContext(): ContextInterface
    {
        if (app()->runningInConsole()) {
            return new LaravelConsoleContext($_SERVER['argv'] ?? []);
        }

        return new LaravelRequestContext(app(Request::class));
    }
}
