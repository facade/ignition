<?php

namespace Facade\Ignition\Logger;

use Throwable;
use Monolog\Logger;
use Facade\FlareClient\Flare;
use Facade\Ignition\Ignition;
use Facade\Ignition\Tabs\Tab;
use Monolog\Handler\AbstractProcessingHandler;

class FlareHandlerPhp71 extends BaseFlareHandler
{
    protected function write(array $report)
    {
        if ($this->shouldReport($report)) {

            /** @var Throwable $throwable */
            $throwable = $report['context']['exception'];

            collect(Ignition::$tabs)
                ->each(function (Tab $tab) use ($throwable) {
                    $tab->beforeRenderingErrorPage($this->flare, $throwable);
                });

            $this->flare->report($report['context']['exception']);
        }
    }
}
