<?php

namespace Facade\Ignition\Tests\Actions;

use Facade\FlareClient\Flare;
use Facade\FlareClient\Glows\Glow;
use Facade\Ignition\Actions\ShareReportAction;
use Facade\Ignition\Tests\Mocks\FakeClient;
use Facade\Ignition\Tests\TestCase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Arr;

class ShareReportActionTest extends TestCase
{
    /** @var \Facade\Flare\Tests\Mocks\FakeClient */
    protected $fakeClient;

    /** @var \Facade\Flare\Actions\ShareReportAction */
    protected $shareAction;

    public function setUp(): void
    {
        parent::setUp();

        $this->fakeClient = new FakeClient();
        $this->shareAction = new ShareReportAction($this->fakeClient);
    }

    /** @test */
    public function sharing_all_tabs_removes_no_data()
    {
        $report = $this->getTestReport();

        $this->shareAction->handle($report, [
            'stackTraceTab',
            'debugTab',
            'userTab',
            'requestTab',
            'appTab',
            'contextTab',
        ]);

        $sharedReport = $this->fakeClient->requests[0]['arguments']['report'];

        $this->assertEquals($report, $sharedReport);
    }

    /** @test */
    public function it_removes_user_data()
    {
        $report = $this->getTestReport();

        $this->shareAction->handle($report, [
            'stackTraceTab',
            'debugTab',
            'requestTab',
            'appTab',
            'contextTab',
        ]);

        $sharedReport = $this->fakeClient->requests[0]['arguments']['report'];

        $this->assertFalse(Arr::has($sharedReport, 'context.user'));
        $this->assertFalse(Arr::has($sharedReport, 'context.request.ip'));
        $this->assertFalse(Arr::has($sharedReport, 'context.request.useragent'));
    }

    /** @test */
    public function it_removes_stack_frames_except_the_last_frame()
    {
        $report = $this->getTestReport();

        $this->shareAction->handle($report, [
            'debugTab',
            'requestTab',
            'userTab',
            'appTab',
            'contextTab',
        ]);

        $sharedReport = $this->fakeClient->requests[0]['arguments']['report'];

        $this->assertCount(1, $sharedReport['stacktrace']);
        $this->assertSame($report['stacktrace'][0], $sharedReport['stacktrace'][0]);
    }

    /** @test */
    public function it_removes_debug_data()
    {
        $report = $this->getTestReport();

        $this->shareAction->handle($report, [
            'stackTraceTab',
            'requestTab',
            'userTab',
            'appTab',
            'contextTab',
        ]);

        $sharedReport = $this->fakeClient->requests[0]['arguments']['report'];

        $this->assertCount(0, Arr::get($sharedReport, 'glows'));
        $this->assertFalse(Arr::has($sharedReport, 'context.dumps'));
        $this->assertFalse(Arr::has($sharedReport, 'context.logs'));
        $this->assertFalse(Arr::has($sharedReport, 'context.queries'));
    }

    /** @test */
    public function it_removes_request_data()
    {
        $report = $this->getTestReport();

        $this->shareAction->handle($report, [
            'stackTraceTab',
            'appTab',
            'debugTab',
            'userTab',
            'contextTab',
        ]);

        $sharedReport = $this->fakeClient->requests[0]['arguments']['report'];

        $this->assertFalse(Arr::has($sharedReport, 'context.request'));
        $this->assertFalse(Arr::has($sharedReport, 'context.request_data'));
        $this->assertFalse(Arr::has($sharedReport, 'context.headers'));
        $this->assertFalse(Arr::has($sharedReport, 'context.session'));
        $this->assertFalse(Arr::has($sharedReport, 'context.cookies'));
    }

    /** @test */
    public function it_removes_context_data()
    {
        $report = $this->getTestReport();

        $this->shareAction->handle($report, [
            'stackTraceTab',
            'requestTab',
            'appTab',
            'debugTab',
            'userTab',
        ]);

        $sharedReport = $this->fakeClient->requests[0]['arguments']['report'];

        $this->assertFalse(Arr::has($sharedReport, 'context.env'));
        $this->assertFalse(Arr::has($sharedReport, 'context.git'));
        $this->assertFalse(Arr::has($sharedReport, 'context.context'));
        $this->assertFalse(Arr::has($sharedReport, 'context.custom_context'));
    }

    /** @test */
    public function it_removes_app_data()
    {
        $report = $this->getTestReport();

        $this->shareAction->handle($report, [
            'stackTraceTab',
            'requestTab',
            'debugTab',
            'userTab',
            'contextTab',
        ]);

        $sharedReport = $this->fakeClient->requests[0]['arguments']['report'];

        $this->assertFalse(Arr::has($sharedReport, 'context.view'));
        $this->assertFalse(Arr::has($sharedReport, 'context.route'));
    }

    protected function getTestReport(): array
    {
        Model::unguard();

        /** @var \Facade\Flare\Flare $flareClient */
        $flareClient = $this->app->make(Flare::class);

        $report = $flareClient->createReport(new \BadMethodCallException('Test Exception'));

        $userData = (new User([
            'id' => 1,
            'name' => 'Marcel',
            'email' => 'marcel@beyondco.de',
        ]))->toArray();

        $report->addGlow(new Glow('Example Glow Data 1'));
        $report->addGlow(new Glow('Example Glow Data 2'));

        $report->group('user', $userData);

        $report->group('request', [
            'ip' => '127.0.0.1',
            'useragent' => 'some-useragent-string',
        ]);

        $dummyGroups = [
            'request',
            'request_data',
            'headers',
            'session',
            'cookies',
            'view',
            'queries',
            'route',
            'user',
            'env',
            'git',
            'context',
            'logs',
            'dumps',

            'custom_context',
        ];

        foreach ($dummyGroups as $group) {
            $report->group($group, [
                'key_1' => 'value',
                'key_2' => 'value',
            ]);
        }

        return $report->toArray();
    }
}
