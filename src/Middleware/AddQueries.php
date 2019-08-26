<?php

namespace Facade\Ignition\Middleware;

use Facade\Ignition\QueryRecorder\QueryRecorder;
use Facade\FlareClient\Report;

class AddQueries
{
    /** @var \Facade\Flare\QueryRecorder\QueryRecorder */
    protected $queryRecorder;

    public function __construct(QueryRecorder $queryRecorder)
    {
        $this->queryRecorder = $queryRecorder;
    }

    public function handle(Report $report, $next)
    {
        $report->group('queries', $this->queryRecorder->getQueries());

        return $next($report);
    }
}
