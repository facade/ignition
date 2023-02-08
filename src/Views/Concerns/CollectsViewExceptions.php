<?php

namespace Facade\Ignition\Views\Concerns;

use Illuminate\Foundation\Application;
use Illuminate\Support\Collection;
use Illuminate\View\Engines\CompilerEngine;

trait CollectsViewExceptions
{
    protected $lastCompiledData = [];

    public function collectViewData($path, array $data): void
    {
        $this->lastCompiledData[] = [
            'path' => $path,
            'compiledPath' => $this->getCompiledPath($path),
            'data' => $this->filterViewData($data),
        ];
    }
    
    public function flushViewData(): void
    {
        // If we need to render multiple views at the same time, 
        // $lastCompiledData will hold all the view data, which can lead to excessive memory usage.
        // We can flush the view data so that we can prevent memory transitions and also improve retrieval efficiency.
        $this->lastCompiledData = [];
    }

    public function filterViewData(array $data): array
    {
        // By default, Laravel views get two shared data keys:
        // __env and app. We try to filter them out.
        return array_filter($data, function ($value, $key) {
            if ($key === 'app') {
                return ! $value instanceof Application;
            }

            return $key !== '__env';
        }, ARRAY_FILTER_USE_BOTH);
    }

    public function getCompiledViewData($compiledPath): array
    {
        $compiledView = $this->findCompiledView($compiledPath);

        return $compiledView['data'] ?? [];
    }

    public function getCompiledViewName($compiledPath): string
    {
        $compiledView = $this->findCompiledView($compiledPath);

        return $compiledView['path'] ?? $compiledPath;
    }

    protected function findCompiledView($compiledPath): ?array
    {
        return Collection::make($this->lastCompiledData)
            ->first(function ($compiledData) use ($compiledPath) {
                $comparePath = $compiledData['compiledPath'];

                return realpath(dirname($comparePath)).DIRECTORY_SEPARATOR.basename($comparePath) === $compiledPath;
            });
    }

    protected function getCompiledPath($path): string
    {
        if ($this instanceof CompilerEngine) {
            return $this->getCompiler()->getCompiledPath($path);
        }

        return $path;
    }
}
