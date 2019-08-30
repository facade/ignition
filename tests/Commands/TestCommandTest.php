<?php

namespace Facade\Ignition\Tests\Commands;

use Facade\Ignition\Tests\TestCase;

class TestCommandTest extends TestCase
{
    /** @test */
    public function it_can_execute_the_test_command()
    {
        $testResult = $this->artisan('flare:test');

        is_int($testResult)
            ? $this->assertSame(0, $testResult)
            : $testResult->assertExitCode(0);
    }
}
