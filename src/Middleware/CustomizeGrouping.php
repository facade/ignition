<?php

namespace Facade\Ignition\Middleware;

use Facade\FlareClient\Report;
use Facade\FlareClient\Enums\GroupingTypes;

class CustomizeGrouping
{
    protected $groupingType;

    public function __construct($groupingType)
    {
        $this->groupingType = $groupingType;
    }

    public function handle(Report $report, $next)
    {
        $report->groupByTopFrame();

        if ($this->groupingType === GroupingTypes::EXCEPTION) {
            $report->groupByException();
        }

        return $next($report);
    }
}
