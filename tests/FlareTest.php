<?php

namespace Facade\Ignition\Tests;

use Facade\FlareClient\Flare;
use Facade\Ignition\Tests\Mocks\FakeClient;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;

class FlareTest extends TestCase
{
    /** @var \Facade\Flare\Tests\Mocks\FakeClient */
    protected $fakeClient;

    public function setUp(): void
    {
        parent::setUp();

        Artisan::call('view:clear');

        $this->app['config']['logging.channels.flare'] = [
            'driver' => 'flare',
        ];

        $this->app['config']['logging.default'] = 'flare';

        $this->app['config']['flare.key'] = 'some-key';

        $this->fakeClient = new FakeClient();

        $this->app->singleton(Flare::class, function () {
            return new Flare($this->fakeClient);
        });

        $this->useTime('2019-01-01 12:34:56');

        View::addLocation(__DIR__.'/stubs/views');
    }

    /** @test */
    public function it_can_manually_report_exceptions()
    {
        \Flare::report(new \Exception());

        $this->fakeClient->assertRequestsSent(1);
    }

    /** @test */
    public function it_can_remove_view_data()
    {
        Route::get('exception', function () {
            return view('blade-exception', ['foo' => 'bar']);
        });

        $this->get('/exception');

        $lastRequest = $this->fakeClient->getLastRequest();

        $this->assertNotNull(Arr::get($lastRequest, 'arguments.context.view.data.foo'));

        $this->app['config']['flare.reporting.report_view_data'] = false;

        Route::get('exception', function () {
            return view('blade-exception', ['foo' => 'bar']);
        });

        $this->get('/exception');

        $lastRequest = $this->fakeClient->getLastRequest();

        $this->assertNull(Arr::get($lastRequest, 'arguments.context.view.data.foo'));
    }
}
