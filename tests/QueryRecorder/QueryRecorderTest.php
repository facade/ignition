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
        $recorder = new QueryRecorder($this->app);
        $connection = app(Connection::class);

        foreach (range(1, 400) as $i) {
            $query = new QueryExecuted('query '.$i, [], time(), $connection);
            $recorder->record($query);
        }

        $this->assertCount(200, $recorder->getQueries());
        $this->assertSame('query 201', $recorder->getQueries()[0]['sql']);
    }
}
