<?php

namespace Facade\Ignition\Tests\TestClasses;

use Livewire\LivewireManager;

class FakeLivewireManager extends LivewireManager
{
    public $fakeAliases = [];

    public static function setUp(): self
    {
        $manager = new self();

        app()->instance(LivewireManager::class, $manager);

        return $manager;
    }

    public function isDefinitelyLivewireRequest()
    {
        return true;
    }

    public function getClass($alias)
    {
        return $this->fakeAliases[$alias] ?? parent::getClass($alias);
    }

    public function addAlias(string $alias, string $class): void
    {
        $this->fakeAliases[$alias] = $class;
    }
}
