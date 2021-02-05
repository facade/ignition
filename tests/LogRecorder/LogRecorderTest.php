<?php

namespace Facade\Ignition\Tests\LogRecorder;

use Exception;
use Facade\Ignition\LogRecorder\LogRecorder;
use Facade\Ignition\Tests\TestCase;
use Illuminate\Log\Events\MessageLogged;

class LogRecorderTest extends TestCase
{
    /** @test */
    public function it_limits_the_amount_of_recorded_logs()
    {
        $recorder = new LogRecorder($this->app, 200);

        foreach (range(1, 400) as $i) {
            $log = new MessageLogged('info', 'test ' . $i, []);
            $recorder->record($log);
        }

        $this->assertCount(200, $recorder->getLogMessages());
        $this->assertSame('test 201', $recorder->getLogMessages()[0]['message']);
    }

    /** @test */
    public function it_does_not_limit_the_amount_of_recorded_queries()
    {
        $recorder = new LogRecorder($this->app);

        foreach (range(1, 400) as $i) {
            $log = new MessageLogged('info', 'test ' . $i, []);
            $recorder->record($log);
        }

        $this->assertCount(400, $recorder->getLogMessages());
        $this->assertSame('test 1', $recorder->getLogMessages()[0]['message']);
    }

    /** @test */
    public function it_does_not_record_log_containing_an_exception()
    {
        $recorder = new LogRecorder($this->app);

        $log = new MessageLogged('info', 'test 1', ['exception' => new Exception('test')]);
        $recorder->record($log);
        $log = new MessageLogged('info', 'test 2', []);
        $recorder->record($log);

        $this->assertCount(1, $recorder->getLogMessages());
        $this->assertSame('test 2', $recorder->getLogMessages()[0]['message']);
    }

    /** @test */
    public function it_does_not_ignore_log_if_exception_key_does_not_contain_exception()
    {
        $recorder = new LogRecorder($this->app);

        $log = new MessageLogged('info', 'test 1', ['exception' => 'test']);
        $recorder->record($log);
        $log = new MessageLogged('info', 'test 2', []);
        $recorder->record($log);

        $this->assertCount(2, $recorder->getLogMessages());
        $this->assertSame('test 1', $recorder->getLogMessages()[0]['message']);
        $this->assertSame('test 2', $recorder->getLogMessages()[1]['message']);
        $this->assertIsArray($recorder->getLogMessages()[0]['context']);
        $this->assertArrayHasKey('exception', $recorder->getLogMessages()[0]['context']);
        $this->assertSame('test', $recorder->getLogMessages()[0]['context']['exception']);
    }
}
