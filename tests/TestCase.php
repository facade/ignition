<?php

namespace Facade\Ignition\Tests;

use Facade\Ignition\Facades\Flare;
use Facade\Ignition\IgnitionServiceProvider;
use Facade\Ignition\Tests\TestClasses\FakeTime;
use Facade\FlareClient\Api;
use Facade\FlareClient\Glows\Glow;
use Facade\FlareClient\Report;

class TestCase extends \Orchestra\Testbench\TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Api::$sendInBatches = false;
    }

    protected function getPackageProviders($app)
    {
        return [IgnitionServiceProvider::class];
    }

    protected function getPackageAliases($app)
    {
        return [
            'Flare' => Flare::class
        ];
    }

    public function useTime(string $dateTime, string $format = 'Y-m-d H:i:s')
    {
        $fakeTime = new FakeTime($dateTime, $format);

        Report::useTime($fakeTime);
        Glow::useTime($fakeTime);
    }
}
