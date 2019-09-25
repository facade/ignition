<?php

namespace Facade\Ignition\ErrorPage;

use Closure;
use Exception;
use Throwable;
use Facade\Ignition\Ignition;
use Facade\FlareClient\Report;
use Laravel\Telescope\Telescope;
use Facade\Ignition\IgnitionConfig;
use Illuminate\Contracts\Support\Arrayable;
use Laravel\Telescope\IncomingExceptionEntry;
use Facade\Ignition\Solutions\SolutionTransformer;
use Laravel\Telescope\Http\Controllers\HomeController;

class ErrorPageViewModel implements Arrayable
{
    /** @var \Throwable */
    protected $throwable;

    /** @var array */
    protected $solutions;

    /** @var \Facade\Ignition\IgnitionConfig */
    protected $ignitionConfig;

    /** @var \Facade\FlareClient\Report */
    protected $report;

    public function __construct(Throwable $throwable, IgnitionConfig $ignitionConfig, Report $report, array $solutions)
    {
        $this->throwable = $throwable;

        $this->ignitionConfig = $ignitionConfig;

        $this->report = $report;

        $this->solutions = $solutions;
    }

    public function throwableString(): string
    {
        return sprintf(
            "%s: %s in file %s on line %d\n\n%s\n",
            get_class($this->throwable),
            $this->throwable->getMessage(),
            $this->throwable->getFile(),
            $this->throwable->getLine(),
            $this->report->getThrowable()->getTraceAsString()
        );
    }

    public function telescopeUrl(): ?string
    {
        try {
            if (! class_exists(Telescope::class)) {
                return null;
            }

            if (! count(Telescope::$entriesQueue)) {
                return null;
            }

            $telescopeEntry = collect(Telescope::$entriesQueue)->first(function ($entry) {
                return $entry instanceof IncomingExceptionEntry;
            });

            if (is_null($telescopeEntry)) {
                return null;
            }

            $telescopeEntryId = (string) $telescopeEntry->uuid;

            return url(action([HomeController::class, 'index'])."/exceptions/{$telescopeEntryId}");
        } catch (Exception $exception) {
            return null;
        }
    }

    public function title(): string
    {
        return "🧨 {$this->report->getMessage()}";
    }

    public function config(): array
    {
        return $this->ignitionConfig->toArray();
    }

    public function solutions(): array
    {
        $solutions = [];

        foreach ($this->solutions as $solution) {
            $solutions[] = (new SolutionTransformer($solution))->toArray();
        }

        return $solutions;
    }

    protected function shareEndpoint()
    {
        // use string notation as L5.5 and L5.6 don't support array notation yet
        return action('\Facade\Ignition\Http\Controllers\ShareReportController');
    }

    public function report(): array
    {
        return $this->report->toArray();
    }

    public function jsonEncode($data): string
    {
        $jsonOptions = JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT;

        if (version_compare(phpversion(), '7.2', '>=')) {
            return json_encode($data, JSON_PARTIAL_OUTPUT_ON_ERROR | $jsonOptions);
        }

        return json_encode($data, JSON_PARTIAL_OUTPUT_ON_ERROR | $jsonOptions);
    }

    public function getAssetContents(string $asset): string
    {
        $assetPath = __DIR__."/../../resources/compiled/{$asset}";

        return file_get_contents($assetPath);
    }

    public function styles(): array
    {
        return array_keys(Ignition::styles());
    }

    public function scripts(): array
    {
        return array_keys(Ignition::scripts());
    }

    public function tabs(): string
    {
        return json_encode(Ignition::$tabs);
    }

    public function toArray(): array
    {
        return [
            'throwableString' => $this->throwableString(),
            'telescopeUrl' => $this->telescopeUrl(),
            'shareEndpoint' => $this->shareEndpoint(),
            'title' => $this->title(),
            'config' => $this->config(),
            'solutions' => $this->solutions(),
            'report' => $this->report(),
            'housekeepingEndpoint' => url(config('ignition.housekeeping_endpoint_prefix', '_ignition')),
            'styles' => $this->styles(),
            'scripts' => $this->scripts(),
            'tabs' => $this->tabs(),
            'jsonEncode' => Closure::fromCallable([$this, 'jsonEncode']),
            'getAssetContents' => Closure::fromCallable([$this, 'getAssetContents']),
        ];
    }
}
