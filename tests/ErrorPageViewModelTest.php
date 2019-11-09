<?php

namespace Facade\Ignition\Tests;

use Facade\FlareClient\Report;
use Facade\Ignition\IgnitionConfig;
use Facade\Ignition\ErrorPage\ErrorPageViewModel;

class ErrorPageViewModelTest extends TestCase
{
    /** @test */
    public function it_can_encode_invalid_user_data()
    {
        $flareClient = $this->app->make('flare.client');

        $exception = new \Exception('Test Exception');

        /** @var Report $report */
        $report = $flareClient->createReport($exception);

        $report->group('bad-utf8', [
            'name' => 'Marcel'.utf8_decode('ø'),
        ]);

        $model = new ErrorPageViewModel($exception, new IgnitionConfig([]), $report, []);

        $this->assertNotEmpty($model->jsonEncode($report->toArray()));
    }
}
