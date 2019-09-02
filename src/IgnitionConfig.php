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

    public function getTheme(): ?string
    {
        return Arr::get($this->options, 'theme');
    }

    public function getEnableShareButton(): bool
    {
        return Arr::get($this->options, 'enable_share_button', true);
    }

    public function toArray(): array
    {
        return [
            'editor' => $this->getEditor(),
            'theme' => $this->getTheme(),
            'enableShareButton' => $this->getEnableShareButton(),
            'directorySeparator' => DIRECTORY_SEPARATOR,
        ];
    }

    protected function mergeWithDefaultConfig(array $options = []): array
    {
        return array_merge(config('ignition'), $options);
    }
}
