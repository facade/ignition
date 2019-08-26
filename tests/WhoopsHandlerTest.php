<?php

namespace Facade\Ignition\Tests;

use Illuminate\Support\Facades\Route;

class WhoopsHandlerTest extends TestCase
{
    /** @test */
    public function it_uses_a_custom_whoops_handler()
    {
        $this->app['config']['app.debug'] = true;

        Route::get('exception', function () {
            whoops();
        });

        $result = $this->get('/exception');

        $this->assertTrue(is_string($result->getContent()));
    }
}
