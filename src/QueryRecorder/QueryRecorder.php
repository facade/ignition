<?php

namespace Facade\Ignition\QueryRecorder;

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Contracts\Foundation\Application;

class QueryRecorder
{
    /** @var \Facade\Ignition\QueryRecorder\Query|[] */
    protected $queries = [];

    /** @var \Illuminate\Contracts\Foundation\Application */
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function register()
    {
        $this->app['events']->listen(QueryExecuted::class, [$this, 'record']);

        return $this;
    }

    public function record(QueryExecuted $queryExecuted)
    {
        $reportBindings = config('flare.reporting.report_query_bindings');

        $this->queries[] = Query::fromQueryExecutedEvent($queryExecuted, $reportBindings);
    }

    public function getQueries(): array
    {
        $queries = [];

        foreach ($this->queries as $query) {
            $queries[] = $query->toArray();
        }

        return $queries;
    }

    public function reset()
    {
        $this->queries = [];
    }
}
