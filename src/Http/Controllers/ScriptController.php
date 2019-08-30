<?php

namespace Facade\Ignition\Http\Controllers;

use Illuminate\Http\Request;
use Facade\Ignition\Ignition;

class ScriptController
{
    public function __invoke(Request $request)
    {
        return response(
            file_get_contents(
                Ignition::scripts()[$request->script]
            ),
            200,
            ['Content-Type' => 'application/javascript']
        );
    }
}
