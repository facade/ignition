<?php

namespace Facade\Ignition\Logger;

use Throwable;
use Monolog\Logger;
use Facade\FlareClient\Flare;
use Facade\Ignition\Ignition;
use Facade\Ignition\Tabs\Tab;
use Monolog\Handler\AbstractProcessingHandler;

class FlareHandler extends AbstractProcessingHandler
{
    /** @var \Facade\FlareClient\Flare */
    protected $flare;

    protected $minimumReportLogLevel = Logger::ERROR;

    public function __construct(Flare $flare, $level = Logger::DEBUG, $bubble = true)
    {
        $this->flare = $flare;

        parent::__construct($level, $bubble);
    }

    public function setMinimumReportLogLevel(int $level)
    {
        if (! in_array($level, Logger::getLevels())) {
            throw new \InvalidArgumentException('The given minimum log level is not supported.');
        }

        $this->minimumReportLogLevel = $level;
    }

    protected function write(array $report): void
    {
        if (! $this->shouldReport($report)) {
            return;
        }

        if ($this->hasException($report)) {
            /** @var Throwable $throwable */
            $throwable = $report['context']['exception'];

            collect(Ignition::$tabs)
                ->each(function (Tab $tab) use ($throwable) {
                    $tab->beforeRenderingErrorPage($this->flare, $throwable);
                });

            $this->flare->report($report['context']['exception']);

            return;
        }

        if (config('flare.send_logs_as_events')) {
            if ($this->hasValidLogLevel($report)) {
                $this->flare->reportMessage($report['message'], 'Log '.Logger::getLevelName($report['level']));
            }
        }
    }

    protected function shouldReport(array $report): bool
    {
        return $this->hasException($report) || $this->hasValidLogLevel($report);
    }

    protected function hasException(array $report): bool
    {
        $context = $report['context'];

        return isset($context['exception']) && $context['exception'] instanceof Throwable;
    }

    protected function hasValidLogLevel(array $report): bool
    {
        return $report['level'] >= $this->minimumReportLogLevel;
    }
}
