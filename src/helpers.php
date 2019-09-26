<?php

if (! function_exists('ddd')) {
    function ddd() {
        $args = func_get_args();
        call_user_func_array('dump', $args);

        $handler = app(\Facade\Ignition\ErrorPage\ErrorPageHandler::class);
        $exception = new \Exception('Dump, Debug, Die');
        $handler->handle($exception, 'DebugTab', [
            'dump' => true,
            'glow' => false,
            'log' => false,
            'query' => false,
        ]);
        die();
    }
}