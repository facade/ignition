<?php

namespace Facade\Ignition\Tests;

class DddTest extends TestCase
{
    /** @test */
    public function using_ddd_without_an_argument_will_throw_its_own_exception()
    {
        $this->expectExceptionMessage('You should pass at least 1 argument to `ddd`');

        ddd();
    }
}
