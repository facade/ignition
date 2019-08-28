<?php

namespace Facade\Ignition;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;

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

    public function getTheme(): ?string
    {
        return Arr::get($this->options, 'theme');
    }

    public function toArray(): array
    {
        return [
            'editor' => $this->getEditor(),
            'theme' => $this->getTheme(),
            'directorySeparator' => DIRECTORY_SEPARATOR,
        ];
    }

    protected function mergeWithDefaultConfig(array $options = []): array
    {
        return array_merge(config('ignition'), $options);
    }
}
