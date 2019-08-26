<?php

namespace Facade\Ignition\Middleware;

use Facade\Ignition\LogRecorder\LogRecorder;
use Facade\FlareClient\Report;

class AddLogs
{
    /** @var \Facade\Flare\LogRecorder\LogRecorder */
    protected $logRecorder;

    public function __construct(LogRecorder $logRecorder)
    {
        $this->logRecorder = $logRecorder;
    }

    public function handle(Report $report, $next)
    {
        $report->group('logs', $this->logRecorder->getLogMessages());

        return $next($report);
    }
}
