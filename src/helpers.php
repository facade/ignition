<?php

if (! function_exists('ddd')) {
    function ddd()
    {
        $args = func_get_args();
        call_user_func_array('dump', $args);

        $handler = app(\Facade\Ignition\ErrorPage\ErrorPageHandler::class);

        $client = app()->make('flare.client');

        $report = $client->createReportFromMessage('Dump, Die, Debug', 'info');

        $handler->handleReport($report, 'DebugTab', [
            'dump' => true,
            'glow' => false,
            'log' => false,
            'query' => false,
        ]);

        die();
    }
}
