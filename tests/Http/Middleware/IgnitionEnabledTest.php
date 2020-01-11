<?php

namespace Facade\Ignition\Tests\Http\Middleware;

use Facade\Ignition\Http\Middleware\IgnitionEnabled;
use Facade\Ignition\Tests\TestCase;
use Illuminate\Support\Facades\Route;

class IgnitionEnabledTest extends TestCase
{
    /** @test */
    public function it_returns_404_with_debug_mode_disabled()
    {
        $this->app['config']['app.debug'] = false;

        Route::get('middleware-test', function () {
            return 'success';
        })->middleware([IgnitionEnabled::class]);

        $this->get('middleware-test')->assertStatus(404);
    }

    /** @test */
    public function it_returns_ok_with_debug_mode_enabled()
    {
        $this->app['config']['app.debug'] = true;

        Route::get('middleware-test', function () {
            return 'success';
        })->middleware([IgnitionEnabled::class]);

        $this->get('middleware-test')->assertStatus(200);
    }
}
