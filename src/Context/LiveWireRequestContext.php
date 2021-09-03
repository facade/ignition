<?php

namespace Facade\Ignition\Context;

use Illuminate\Http\Request;
use Livewire\LivewireManager;

class LiveWireRequestContext extends LaravelRequestContext
{
    /** @var \Livewire\LivewireManager */
    protected $livewireManager;

    public function __construct(
        Request $request,
        LivewireManager $livewireManager
    )
    {
        parent::__construct($request);

        $this->livewireManager = $livewireManager;
    }

    public function getRequest(): array
    {
        $properties = parent::getRequest();

        $properties['method'] = $this->livewireManager->originalMethod();
        $properties['url'] = $this->livewireManager->originalUrl();

        return $properties;
    }

    public function getLiveWireInformation(): array
    {
        $componentId =  $this->request->input('fingerprint.id');
        $componentAlias =  $this->request->input('fingerprint.name');

        if($componentAlias === null){
            return [];
        }

        return [
            'component_alias' => $componentAlias,
            'component_id' => $componentId,
            'component_class' => $this->livewireManager->getClass($componentAlias),
            'data' => json_encode($this->request->input('serverMemo.data')),
            'updates' => json_encode($this->request->input('updates')),
        ];
    }

    public function toArray(): array
    {
        $properties = parent::toArray();

        $properties['livewire'] = $this->getLiveWireInformation();

        return $properties;
    }
}
