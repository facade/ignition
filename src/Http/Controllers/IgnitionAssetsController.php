<?php

namespace Facade\Ignition\Http\Controllers;

class IgnitionAssetsController
{
    public function __invoke()
    {
        return file_get_contents(__DIR__.'/../../../resources/compiled/ignition.js');
    }
}