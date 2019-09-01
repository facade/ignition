<?php

namespace Facade\Ignition;

use Illuminate\Support\Arr;
use Illuminate\Contracts\Support\Arrayable;

class IgnitionConfig implements Arrayable
{
    /** @var array */
    protected $options;

    public function __construct(array $options = [])
    {
        $this->options = $this->mergeWithDefaultConfig($options);
    }

    public function getEditor(): ?string
    {
        return Arr::get($this->options, 'editor');
    }

    public function getHomesteadSitesPath(): ?string
    {
        return Arr::get($this->options, 'homestead-sites-path');
    }

    public function getLocalSitesPath(): ?string
    {
        return Arr::get($this->options, 'local-sites-path');
    }

    public function getTheme(): ?string
    {
        return Arr::get($this->options, 'theme');
    }

    public function toArray(): array
    {
        return [
            'editor' => $this->getEditor(),
            'homestead-sites-path' => $this->getHomesteadSitesPath(),
            'local-sites-path' => $this->getLocalSitesPath(),
            'theme' => $this->getTheme(),
            'directorySeparator' => DIRECTORY_SEPARATOR,
        ];
    }

    protected function mergeWithDefaultConfig(array $options = []): array
    {
        return array_merge(config('ignition'), $options);
    }
}
