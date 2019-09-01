<?php

namespace Facade\Ignition\Exceptions;

use ErrorException;
use Facade\Ignition\DumpRecorder\HtmlDumper;
use Facade\FlareClient\Contracts\ProvidesFlareContext;
use Facade\IgnitionContracts\ProvidesSolution;
use Facade\IgnitionContracts\Solution;

class ViewExceptionWithSolution extends ViewException implements ProvidesSolution
{
    /** @var Solution */
    protected $solution;

    public function setSolution(Solution $solution)
    {
        $this->solution = $solution;
    }

    public function getSolution(): Solution
    {
        return $this->solution;
    }
}
