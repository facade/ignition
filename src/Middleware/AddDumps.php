<?php

namespace Facade\Ignition\Middleware;

use Facade\Ignition\DumpRecorder\DumpRecorder;
use Facade\FlareClient\Report;

class AddDumps
{
    /** @var \Facade\Flare\DumpRecorder\DumpRecorder */
    protected $dumpRecorder;

    public function __construct(DumpRecorder $dumpRecorder)
    {
        $this->dumpRecorder = $dumpRecorder;
    }

    public function handle(Report $report, $next)
    {
        $report->group('dumps', $this->dumpRecorder->getDumps());

        return $next($report);
    }
}
