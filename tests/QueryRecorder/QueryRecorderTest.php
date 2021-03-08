<?php

namespace Facade\Ignition\Tests\QueryRecorder;

use Facade\Ignition\QueryRecorder\QueryRecorder;
use Facade\Ignition\Tests\TestCase;
use Illuminate\Database\Connection;
use Illuminate\Database\Events\QueryExecuted;

class QueryRecorderTest extends TestCase
{
    /** @test */
    public function it_limits_the_amount_of_recorded_queries()
    {
        $recorder = new QueryRecorder($this->app, true, 200);
        $connection = app(Connection::class);

        foreach (range(1, 400) as $i) {
            $query = new QueryExecuted('query '.$i, [], time(), $connection);
            $recorder->record($query);
        }

        $this->assertCount(200, $recorder->getQueries());
        $this->assertSame('query 201', $recorder->getQueries()[0]['sql']);
    }

    /** @test */
    public function it_does_not_limit_the_amount_of_recorded_queries()
    {
        $recorder = new QueryRecorder($this->app, true);
        $connection = app(Connection::class);

        foreach (range(1, 400) as $i) {
            $query = new QueryExecuted('query '.$i, [], time(), $connection);
            $recorder->record($query);
        }

        $this->assertCount(400, $recorder->getQueries());
        $this->assertSame('query 1', $recorder->getQueries()[0]['sql']);
    }

    /** @test */
    public function it_records_bindings()
    {
        $recorder = new QueryRecorder($this->app, true);
        $connection = app(Connection::class);

        $query = new QueryExecuted('query 1', ['abc' => 123], time(), $connection);
        $recorder->record($query);

        $this->assertCount(1, $recorder->getQueries());
        $this->assertSame('query 1', $recorder->getQueries()[0]['sql']);
        $this->assertIsArray($recorder->getQueries()[0]['bindings']);
        $this->assertSame('query 1', $recorder->getQueries()[0]['sql']);
        $this->assertSame(123, $recorder->getQueries()[0]['bindings']['abc']);
    }

    /** @test */
    public function it_does_not_record_bindings()
    {
        $recorder = new QueryRecorder($this->app, false);
        $connection = app(Connection::class);

        $query = new QueryExecuted('query 1', ['abc' => 123], time(), $connection);
        $recorder->record($query);

        $this->assertCount(1, $recorder->getQueries());
        $this->assertSame('query 1', $recorder->getQueries()[0]['sql']);
        $this->assertNull($recorder->getQueries()[0]['bindings']);
    }
}
