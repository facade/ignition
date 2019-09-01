<?php

namespace Facade\Ignition\Tests\stubs\Controllers;

class TestTypoController
{
    public function index()
    {
        // Intentional error
        return view('blade-exceptio');
    }
}
