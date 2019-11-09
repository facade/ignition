<?php

namespace Facade\Ignition\Tests\stubs\Controllers;

class MissingClassController
{
    public function index()
    {
        return Str::endsWith('This is a test', 'test');
    }
}
