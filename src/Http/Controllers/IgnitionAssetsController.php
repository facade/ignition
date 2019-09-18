<?php

namespace Facade\Ignition\Http\Controllers;

use Illuminate\Http\Response;

class IgnitionAssetsController
{
    public function __invoke()
    {
        $assetContent = file_get_contents(__DIR__.'/../../../resources/compiled/ignition.js');

        return Response::create($assetContent, 200, [
            'Content-Type' => 'application/javascript',
        ]);
    }
}
