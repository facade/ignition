<?php

namespace Facade\Ignition\Facades;

use Illuminate\Support\Facades\Facade;

class Flare extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'flare.client';
    }
}
