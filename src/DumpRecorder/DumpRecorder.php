<?php

namespace Facade\Ignition\DumpRecorder;

use Illuminate\Support\Arr;
use Illuminate\Foundation\Application;
use Symfony\Component\VarDumper\VarDumper;
use Symfony\Component\VarDumper\Cloner\Data;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\CliDumper;
use Symfony\Component\VarDumper\Dumper\HtmlDumper as BaseHtmlDumper;

class DumpRecorder
{
    protected $dumps = [];

    /** @var \Illuminate\Foundation\Application */
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function register(): self
    {
        $multiDumpHandler = new MultiDumpHandler();

        $this->app->singleton(MultiDumpHandler::class, $multiDumpHandler);

        $previousHandler = VarDumper::setHandler(function ($var) use ($multiDumpHandler) {
            $multiDumpHandler->dump($var);
        });

        if ($previousHandler) {
            $multiDumpHandler->addHandler($previousHandler);
        } else {
            $multiDumpHandler->addHandler($this->getDefaultHandler());
        }

        $multiDumpHandler->addHandler(function ($var) {
            $this->app->make(DumpHandler::class)->dump($var);
        });

        return $this;
    }

    public function record(Data $data)
    {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 7);
        $file = Arr::get($backtrace, '6.file');
        $lineNumber = Arr::get($backtrace, '6.line');

        $htmlDump = (new HtmlDumper())->dump($data);

        $this->dumps[] = new Dump($htmlDump, $file, $lineNumber);
    }

    public function getDumps(): array
    {
        return $this->toArray();
    }

    public function reset()
    {
        $this->dumps = [];
    }

    public function toArray(): array
    {
        $dumps = [];

        foreach ($this->dumps as $dump) {
            $dumps[] = $dump->toArray();
        }

        return $dumps;
    }

    protected function getDefaultHandler()
    {
        return function ($value) {
            $data = (new VarCloner)->cloneVar($value);

            $dumper = in_array(PHP_SAPI, ['cli', 'phpdbg']) ? new CliDumper : new BaseHtmlDumper;
            $dumper->dump($data);
        };
    }
}
