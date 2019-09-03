<?php

namespace Facade\Ignition\Logger;

use Facade\FlareClient\Flare;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
use Throwable;

abstract class BaseFlareHandler extends AbstractProcessingHandler
{
    /** @var \Facade\FlareClient\Flare */
    protected $flare;

    public function __construct(Flare $flare, $level = Logger::DEBUG, $bubble = true)
    {
        $this->flare = $flare;

        parent::__construct($level, $bubble);
    }

    protected function shouldReport(array $report): bool
    {
        $context = $report['context'];

        return isset($context['exception']) && $context['exception'] instanceof Throwable;
    }
}

