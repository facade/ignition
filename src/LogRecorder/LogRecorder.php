<?php

namespace Facade\Ignition\LogRecorder;

use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Log\Events\MessageLogged;

class LogRecorder
{
    /** @var \Facade\Flare\LogRecorder\LogMessage[] */
    protected $logMessages = [];

    /** @var \Illuminate\Contracts\Foundation\Application */
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function register(): self
    {
        $this->app['events']->listen(MessageLogged::class, [$this, 'record']);

        return $this;
    }

    public function record(MessageLogged $event): void
    {
        if ($this->shouldIgnore($event)) {
            return;
        }

        $this->logMessages[] = LogMessage::fromMessageLoggedEvent($event);
    }

    public function getLogMessages(): array
    {
        return $this->toArray();
    }

    public function toArray(): array
    {
        $logMessages = [];

        foreach ($this->logMessages as $log) {
            $logMessages[] = $log->toArray();
        }

        return $logMessages;
    }

    protected function shouldIgnore($event): bool
    {
        if (! isset($event->context['exception'])) {
            return false;
        }

        if (! $event->context['exception'] instanceof Exception) {
            return false;
        }

        return true;
    }

    public function reset(): void
    {
        $this->logMessages = [];
    }
}
