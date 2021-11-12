<?php

namespace Facade\Ignition\Tests\Context;

use Facade\Ignition\Context\LivewireRequestContext;
use Facade\Ignition\Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Livewire\Component;
use Livewire\LivewireManager;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class LivewireRequestContextTest extends TestCase
{
    /** @var \Livewire\LivewireManager */
    private $livewireManager;

    public function setUp(): void
    {
        parent::setUp();

        $this->livewireManager = new class extends LivewireManager {
            public $fakeAliases = [];

            public function isDefinitelyLivewireRequest()
            {
                return true;
            }

            public function getClass($alias)
            {
                return $this->fakeAliases[$alias] ?? parent::getClass($alias);
            }
        };
    }

    /** @test */
    public function it_returns_the_referer_url_and_method()
    {
        $context = $this->createRequestContext([
            'path' => 'referred',
            'method' => 'GET',
        ]);

        $request = $context->getRequest();

        $this->assertSame('http://localhost/referred', $request['url']);
        $this->assertSame('GET', $request['method']);
    }

    /** @test */
    public function it_returns_livewire_component_information()
    {
        $alias = 'fake-component';
        $class = 'fake-class';

        $this->livewireManager->fakeAliases[$alias] = $class;

        $context = $this->createRequestContext([
            'path' => 'http://localhost/referred',
            'method' => 'GET',
            'id' => $id = uniqid(),
            'name' => $alias,
        ]);

        $livewire = $context->toArray()['livewire'];

        $this->assertSame($id, $livewire['component_id']);
        $this->assertSame($alias, $livewire['component_alias']);
        $this->assertSame($class, $livewire['component_class']);
    }

    /** @test */
    public function it_returns_livewire_component_information_when_it_does_not_exist()
    {
        $context = $this->createRequestContext([
            'path' => 'http://localhost/referred',
            'method' => 'GET',
            'id' => $id = uniqid(),
            'name' => $name = 'fake-component',
        ]);

        $livewire = $context->toArray()['livewire'];

        $this->assertSame($id, $livewire['component_id']);
        $this->assertSame($name, $livewire['component_alias']);
        $this->assertNull($livewire['component_class']);
    }

    /** @test */
    public function it_removes_ids_from_update_payloads()
    {
        $context = $this->createRequestContext([
            'path' => 'http://localhost/referred',
            'method' => 'GET',
            'id' => $id = uniqid(),
            'name' => $name = 'fake-component',
        ]);

        $livewire = $context->toArray()['livewire'];

        $this->assertSame($id, $livewire['component_id']);
        $this->assertSame($name, $livewire['component_alias']);
        $this->assertNull($livewire['component_class']);
    }

    protected function createRequestContext(array $fingerprint): LivewireRequestContext
    {
        $providedRequest = null;

        Route::post('livewire', function (Request $request) use (&$providedRequest) {
            $providedRequest = $request;
        })->name('livewire.message');

        $componentPayload = '{"fingerprint":{"id":"SUnhW5UiFCcEC5xjt4zb","name":"test-component","locale":"en","path":"","method":"GET"},"serverMemo":{"children":{"1ANGhqH":{"id":"qwuf6BvvSeRDkH3ZQbrD","tag":"div"},"Ep3Zi4H":{"id":"PC69TcCIs9f3x4ffRrPK","tag":"div"}},"errors":[],"htmlHash":"80b854ba","data":{"title":"Ruben","other":"Jah","array":["a","b"],"modelCollection":[],"model":[],"date":"2021-11-10T14:20:36+0000","collection":["a","b"],"stringable":"Test","wireable":"[\"a\",\"b\"]"},"dataMeta":{"modelCollections":{"modelCollection":{"class":"App\\Models\\User","id":[1,2,3,4,5],"relations":[],"connection":"mysql"}},"models":{"model":{"class":"App\\Models\\User","id":1,"relations":[],"connection":"mysql"}},"dates":{"date":"carbonImmutable"},"collections":["collection"],"stringables":["stringable"],"wireables":["wireable"]},"checksum":"d6ec201251c66428c4f57939747d190b425d264857e70406aa39107dd6f1da38"},"updates":[{"type":"callMethod","payload":{"id":"zyjb","method":"chang","params":[]}}]}';


        $this->postJson('livewire', json_decode($componentPayload, true));

        return new LivewireRequestContext($providedRequest, $this->livewireManager);
    }
}
