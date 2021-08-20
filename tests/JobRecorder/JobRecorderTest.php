<?php

namespace Facade\Ignition\Tests\JobRecorder;

use Exception;
use Facade\Ignition\JobRecorder\JobRecorder;
use Facade\Ignition\Tests\stubs\jobs\QueueableJob;
use Facade\Ignition\Tests\TestCase;
use Illuminate\Container\Container;
use Illuminate\Queue\Events\JobExceptionOccurred;
use Illuminate\Queue\Jobs\RedisJob;
use Illuminate\Queue\Queue;
use Illuminate\Queue\RedisQueue;

class JobRecorderTest extends TestCase
{
    /** @test */
    public function it_can_record_a_failed_job()
    {
        $recorder = (new JobRecorder($this->app));

        $job = new QueueableJob([]);

        $recorder->record($this->createEvent(
            'redis',
            'default',
            $job
        ));

        $this->assertEquals([
            'name' => 'Facade\Ignition\Tests\stubs\jobs\QueueableJob',
            'connection' => 'redis',
            'queue' => 'default',
            'properties' => [
                'data' => [],
                'delay' => null,
                'afterCommit' => null,
                'middleware' => [],
                'chained' => [],
                'chainConnection' => null,
                'chainQueue' => null,
                'chainCatchCallbacks' => null,
            ],
        ], $recorder->toArray());
    }

    /** @test */
    public function it_can_record_a_failed_job_with_data()
    {
        $recorder = (new JobRecorder($this->app));

        $job = new QueueableJob([
            'int' => 42,
            'boolean' => true,
        ]);

        $recorder->record($this->createEvent(
            'redis',
            'default',
            $job
        ));

        $this->assertEquals([
            'name' => 'Facade\Ignition\Tests\stubs\jobs\QueueableJob',
            'connection' => 'redis',
            'queue' => 'default',
            'properties' => [
                'data' => [
                    'int' => 42,
                    'boolean' => true,
                ],
                'delay' => null,
                'afterCommit' => null,
                'middleware' => [],
                'chained' => [],
                'chainConnection' => null,
                'chainQueue' => null,
                'chainCatchCallbacks' => null,
            ],
        ], $recorder->toArray());
    }

    /** @test */
    public function it_can_record_a_closure_job()
    {
        $recorder = (new JobRecorder($this->app));

        $data = [
            'int' => 42,
            'boolean' => true,
        ];

        $job = function () use ($data) {
        };

        $recorder->record($this->createEvent(
            'redis',
            'default',
            $job
        ));

        $this->assertEquals([
            'name' => 'Closure (JobRecorderTest.php:93)',
            'connection' => 'redis',
            'queue' => 'default',
            'properties' => [
                'delay' => null,
                'afterCommit' => null,
                'middleware' => [],
                'chained' => [],
                'deleteWhenMissingModels' => true,
                'batchId' => null,
                'failureCallbacks' => [],
                'chainConnection' => null,
                'chainQueue' => null,
                'chainCatchCallbacks' => null,
            ],
        ], $recorder->toArray());
    }

    /** @test */
    public function it_can_handle_a_job_with_an_unserializeable_payload()
    {
        $recorder = (new JobRecorder($this->app));

        $payload = json_encode([
            'job' => 'Fake Job Name',
        ]);

        $event = new JobExceptionOccurred(
            'redis',
            new RedisJob(
                app(Container::class),
                app(RedisQueue::class),
                $payload,
                $payload,
                'redis',
                'default'
            ),
            new Exception()
        );

        $recorder->record($event);

        $this->assertEquals([
            'name' => 'Fake Job Name',
            'connection' => 'redis',
            'queue' => 'default',
        ], $recorder->toArray());
    }

    /**
     * @param string $connection
     * @param \Illuminate\Contracts\Queue\ShouldQueue|\Closure $job
     *
     * @return \Illuminate\Queue\Events\JobExceptionOccurred
     */
    private function createEvent(
        string $connection,
        string $queue,
        $job
    ): JobExceptionOccurred {
        $fakeQueue = new class extends Queue {
            public function getPayload($job, $connection): string
            {
                return $this->createPayload($job, $connection);
            }
        };

        $payload = $fakeQueue->getPayload($job, $connection);

        return new JobExceptionOccurred(
            $connection,
            new RedisJob(
                app(Container::class),
                app(RedisQueue::class),
                $payload,
                $payload,
                $connection,
                $queue
            ),
            new Exception()
        );
    }
}
