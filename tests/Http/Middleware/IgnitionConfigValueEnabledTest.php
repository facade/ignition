<?php

namespace Facade\Ignition\Tests\Http\Middleware;

use Facade\Ignition\Http\Middleware\IgnitionConfigValueEnabled;
use Facade\Ignition\Tests\TestCase;
use Illuminate\Support\Facades\Route;

class IgnitionConfigValueEnabledTest extends TestCase
{
    /** @test */
    public function it_returns_404_with_enable_share_button_disabled()
    {
        $this->app['config']['ignition.enable_share_button'] = false;

        Route::get('middleware-test', function () {
            return 'success';
        })->middleware(IgnitionConfigValueEnabled::class.':enableShareButton');

        $this->get('middleware-test')->assertStatus(404);
    }

    /** @test */
    public function it_returns_200_with_enable_share_button_enabled()
    {
        $this->app['config']['ignition.enable_share_button'] = true;

        Route::get('middleware-test', function () {
            return 'success';
        })->middleware(IgnitionConfigValueEnabled::class.':enableShareButton');

        $this->get('middleware-test')->assertStatus(200);
    }

    /** @test */
    public function it_returns_404_with_enable_runnable_solutions_disabled()
    {
        $this->app['config']['ignition.enable_runnable_solutions'] = false;

        Route::get('middleware-test', function () {
            return 'success';
        })->middleware(IgnitionConfigValueEnabled::class.':enableRunnableSolutions');

        $this->get('middleware-test')->assertStatus(404);
    }

    /** @test */
    public function it_returns_200_with_enable_runnable_solutions_enabled()
    {
        $this->app['config']['ignition.enable_runnable_solutions'] = true;

        Route::get('middleware-test', function () {
            return 'success';
        })->middleware(IgnitionConfigValueEnabled::class.':enableRunnableSolutions');

        $this->get('middleware-test')->assertStatus(200);
    }
}
