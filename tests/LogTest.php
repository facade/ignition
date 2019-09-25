<?php

namespace Facade\Ignition\Tests;

use Facade\FlareClient\Flare;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Facade\Ignition\Tests\Mocks\FakeClient;

class LogTest extends TestCase
{
    /** @var \Facade\Ignition\Tests\Mocks\FakeClient */
    protected $fakeClient;

    public function setUp(): void
    {
        parent::setUp();

        $this->app['config']['logging.channels.flare'] = [
            'driver' => 'flare',
        ];

        $this->app['config']['logging.default'] = 'flare';

        $this->app['config']['flare.key'] = 'some-key';

        $this->fakeClient = new FakeClient();

        $currentClient = $this->app->make('flare.client');

        $middleware = $currentClient->getMiddleware();

        $this->app->singleton('flare.client', function () use ($middleware) {
            $flare = new Flare($this->fakeClient, null, null);

            foreach ($middleware as $singleMiddleware) {
                $flare->registerMiddleware($singleMiddleware);
            }

            return $flare;
        });

        $this->useTime('2019-01-01 12:34:56');
    }

    /** @test */
    public function it_reports_exceptions_using_the_flare_api()
    {
        Route::get('exception', function () {
            whoops();
        });

        $this->get('/exception');

        $this->fakeClient->assertRequestsSent(1);
    }

    /** @test */
    public function it_does_not_report_normal_log_messages()
    {
        Log::info('this is a log message');
        Log::debug('this is a log message');

        $this->fakeClient->assertRequestsSent(0);
    }

    /** @test */
    public function it_reports_log_messages_above_the_specified_minimum_level()
    {
        Log::error('this is a log message');
        Log::emergency('this is a log message');
        Log::critical('this is a log message');

        $this->fakeClient->assertRequestsSent(3);
    }

    /** @test */
    public function it_can_log_null_values()
    {
        Log::info(null);
        Log::debug(null);
        Log::error(null);
        Log::emergency(null);
        Log::critical(null);

        $this->fakeClient->assertRequestsSent(3);
    }

    /** @test */
    public function it_adds_log_messages_to_the_report()
    {
        Route::get('exception', function () {
            Log::info('info log');
            Log::debug('debug log');
            Log::notice('notice log');

            whoops();
        });

        $this->get('/exception');

        $this->fakeClient->assertRequestsSent(1);

        $arguments = $this->fakeClient->requests[0]['arguments'];

        $logs = $arguments['context']['logs'];

        $this->assertCount(3, $logs);
    }

    public function provideMessageLevels()
    {
        return [
            ['info'],
            ['notice'],
            ['debug'],
            ['warning'],
            ['error'],
            ['critical'],
            ['emergency'],
        ];
    }

    /**
     * @test
     * @dataProvider provideMessageLevels
     */
    public function it_can_report_logs($logLevel)
    {
        Log::log($logLevel, 'log');

        Route::get('exception', function () {
            whoops();
        });

        $this->get('/exception');

        $arguments = $this->fakeClient->requests[0]['arguments'];

        $logs = $arguments['context']['logs'];

        $this->assertCount(1, $logs);
        $this->assertEquals($logLevel, $logs[0]['level']);
        $this->assertEquals('log', $logs[0]['message']);
        $this->assertEquals([], $logs[0]['context']);
    }

    /** @test */
    public function it_can_report_logs_with_metadata()
    {
        Log::info('log', [
            'meta' => 'data',
        ]);

        Route::get('exception', function () {
            whoops();
        });

        $this->get('/exception');

        $arguments = $this->fakeClient->requests[0]['arguments'];

        $logs = $arguments['context']['logs'];

        $this->assertEquals(['meta' => 'data'], $logs[0]['context']);
    }
}
