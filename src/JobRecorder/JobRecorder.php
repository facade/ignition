<?php

namespace Facade\Ignition\JobRecorder;

use Exception;
use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Queue\Events\JobExceptionOccurred;
use Illuminate\Queue\Jobs\Job;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionProperty;
use RuntimeException;

class JobRecorder
{
    /** @var \Illuminate\Contracts\Foundation\Application */
    protected $app;

    /** @var \Illuminate\Queue\Jobs\Job|null */
    protected $job = null;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function register(): self
    {
        $this->app['events']->listen(JobExceptionOccurred::class, [$this, 'record']);

        return $this;
    }

    public function record(JobExceptionOccurred $event): void
    {
        $this->job = $event->job;
    }

    public function toArray(): array
    {
        if ($this->job === null) {
            return [];
        }

        return array_filter([
            'name' => $this->job->resolveName(),
            'connection' => $this->job->getConnectionName(),
            'queue' => $this->job->getQueue(),
            'properties' => $this->getJobProperties(),
        ]);
    }

    public function getJob(): ?Job
    {
        return $this->job;
    }

    public function reset(): void
    {
        $this->job = null;
    }

    protected function getJobProperties(): array
    {
        $payload = $this->job->payload();

        if (! array_key_exists('data', $payload)) {
            return [];
        }

        try {
            $job = $this->getCommand($payload['data']);
        } catch (Exception $exception) {
            return [];
        }

        $defaultProperties = [
            'job',
            'closure',
            'connection',
            'queue',
        ];

        return collect((new ReflectionClass($job))->getProperties())
            ->reject(function (ReflectionProperty $property) use ($defaultProperties) {
                return in_array($property->name, $defaultProperties);
            })
            ->mapWithKeys(function (ReflectionProperty $property) use ($job) {
                $property->setAccessible(true);

                return [$property->name => $property->getValue($job)];
            })
            ->toArray();
    }

    // Taken from Illuminate\Queue\CallQueuedHandler
    protected function getCommand(array $data): object
    {
        if (Str::startsWith($data['command'], 'O:')) {
            return unserialize($data['command']);
        }

        if ($this->app->bound(Encrypter::class)) {
            return unserialize($this->app[Encrypter::class]->decrypt($data['command']));
        }

        throw new RuntimeException('Unable to extract job payload.');
    }
}
