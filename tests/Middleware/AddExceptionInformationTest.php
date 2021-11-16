<?php

namespace Facade\Ignition\Tests\Middleware;

use Exception;
use Facade\Ignition\Facades\Flare;
use Facade\Ignition\Tests\TestCase;
use Illuminate\Database\QueryException;

class AddExceptionInformationTest extends TestCase
{
    /** @test */
    public function it_will_add_query_information_with_a_query_exception()
    {
        $sql = 'select * from users where emai = "ruben@spatie.be"';

        $report = Flare::createReport(new QueryException(
            '' . $sql . '',
            [],
            new Exception()
        ));

        $context = $report->toArray()['context'];

        $this->assertArrayHasKey('exception', $context);
        $this->assertSame($sql, $context['exception']['raw_sql']);
    }

    /** @test */
    public function it_wont_add_query_information_without_a_query_exception()
    {
        $report = Flare::createReport(new Exception());

        $context = $report->toArray()['context'];

        $this->assertArrayNotHasKey('exception', $context);
    }
}
