<?php

namespace Facade\Ignition\Tests\Support;

use Exception;
use Facade\FlareClient\Report;
use Facade\Ignition\Support\SentReports;
use Facade\Ignition\Tests\TestCase;
use Flare;

class SentReportsTest extends TestCase
{
    /** @var \Facade\Ignition\Support\SentReports[] */
    protected $sentReports = [];

    public function setUp(): void
    {
        parent::setUp();

        $this->sentReports = new SentReports();
    }

    /** @test */
    public function it_can_get_the_uuids()
    {
        $this->assertNull($this->sentReports->latestUuid());

        $report = $this->getReport('first-report');
        $this->sentReports->add($report);
        $this->assertEquals('first-report', $this->sentReports->latestUuid());

        $report = $this->getReport('second-report');
        $this->sentReports->add($report);
        $this->assertEquals('second-report', $this->sentReports->latestUuid());

        $this->assertEquals([
            'first-report',
            'second-report',
        ], $this->sentReports->uuids());
    }

    /** @test */
    public function it_can_get_the_error_urls()
    {
        $report = $this->getReport('first-report');
        $this->sentReports->add($report);

        $this->assertEquals('https://flareapp.io/tracked-occurrence/first-report', $this->sentReports->latestUrl());

        $report = $this->getReport('second-report');
        $this->sentReports->add($report);
        $this->assertEquals('https://flareapp.io/tracked-occurrence/second-report', $this->sentReports->latestUrl());

        $this->assertEquals([
            'https://flareapp.io/tracked-occurrence/first-report',
            'https://flareapp.io/tracked-occurrence/second-report',
        ], $this->sentReports->urls());
    }

    /** @test */
    public function it_can_be_cleared()
    {
        $report = $this->getReport('first-report');
        $this->sentReports->add($report);
        $this->assertCount(1, $this->sentReports->all());

        $this->sentReports->clear();
        $this->assertCount(0, $this->sentReports->all());
    }

    protected function getReport(string $fakeUuid = 'fake-uuid'): Report
    {
        Report::$fakeTrackingUuid = $fakeUuid;

        return Flare::report(new Exception());
    }
}
