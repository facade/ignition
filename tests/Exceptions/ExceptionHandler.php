<?php

namespace Facade\Ignition\Tests\Exceptions;

use Whoops\Handler\HandlerInterface;
use Illuminate\Foundation\Exceptions\Handler;

class ExceptionHandler extends Handler
{
    protected function whoopsHandler()
    {
        return app(HandlerInterface::class);
    }
}
