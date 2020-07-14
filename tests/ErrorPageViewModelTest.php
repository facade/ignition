<?php

namespace Facade\Ignition\Tests;

use Facade\FlareClient\Flare;
use Facade\FlareClient\Report;
use Facade\Ignition\ErrorPage\ErrorPageViewModel;
use Facade\Ignition\IgnitionConfig;

class ErrorPageViewModelTest extends TestCase
{
    /** @test */
    public function it_can_encode_invalid_user_data()
    {
        $flareClient = $this->app->make(Flare::class);

        $exception = new \Exception('Test Exception');

        /** @var Report $report */
        $report = $flareClient->createReport($exception);

        $report->group('bad-utf8', [
            'name' => 'Marcel'.utf8_decode('ø'),
        ]);

        $model = new ErrorPageViewModel($exception, new IgnitionConfig([]), $report, []);

        $this->assertNotEmpty($model->jsonEncode($report->toArray()));
    }

    /** @test */
    public function it_disables_share_report_when_share_report_controller_action_is_not_defined()
    {
        $flareClient = $this->app->make(Flare::class);

        $exception = new \Exception('Test Exception');

        /** @var Report $report */
        $report = $flareClient->createReport($exception);

        $model = new ErrorPageViewModel($exception, new IgnitionConfig([]), $report, []);

        $result = $model->toArray();

        $this->assertEquals('', $result['shareEndpoint']);
    }
}
