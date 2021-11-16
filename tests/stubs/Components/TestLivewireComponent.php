<?php

namespace Facade\Ignition\Tests\stubs\Components;

use App\CustomProp;
use App\Models\User;
use Carbon\CarbonImmutable;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Stringable;
use Livewire\Component;

class TestLivewireComponent extends Component
{
    public $string;

    public $stringable;

    public function mount(string $title)
    {
        $this->string = $title;
    }

    public function render()
    {
        return 'nowp';
    }

    public function change()
    {
        $this->string = 'Ruben';
    }

    public function getComputedProperty()
    {
        return 'bla';
    }
}
