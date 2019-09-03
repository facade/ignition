<?php

namespace Facade\Ignition\Tests;

use Facade\Ignition\IgnitionConfig;

class IgnitionConfigTest extends TestCase
{
    /** @test */
    public function it_enables_runnable_solutions_in_debug_mode()
    {
        $this->app['config']['app.debug'] = true;

        $config = new IgnitionConfig([]);

        $this->assertTrue($config->getEnableRunnableSolutions());
    }

    /** @test */
    public function it_disables_runnable_solutions_in_production_mode()
    {
        $this->app['config']['app.debug'] = false;

        $config = new IgnitionConfig([]);

        $this->assertFalse($config->getEnableRunnableSolutions());
    }

    /** @test */
    public function it_prioritizes_config_value_over_debug_mode()
    {
        $this->app['config']['app.debug'] = true;

        $config = new IgnitionConfig([
            'enable_runnable_solutions' => false,
        ]);

        $this->assertFalse($config->getEnableRunnableSolutions());
    }
}
