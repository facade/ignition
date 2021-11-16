<?php

namespace Facade\Ignition\Tests\Context;

use Facade\Ignition\Context\LivewireRequestContext;
use Facade\Ignition\Tests\TestCase;
use Facade\Ignition\Tests\TestClasses\FakeLivewireManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class LivewireRequestContextTest extends TestCase
{
    /** @var \Livewire\LivewireManager */
    private $livewireManager;

    public function setUp(): void
    {
        parent::setUp();

        $this->livewireManager = FakeLivewireManager::setUp();
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
        ], [
            [
                'type' => 'callMethod',
                'payload' => [
                    'id' => 'remove-me',
                    'method' => 'chang',
                    'params' => ['a'],
                ],
            ],
        ]);

        $livewire = $context->toArray()['livewire'];

        $this->assertSame($id, $livewire['component_id']);
        $this->assertSame($name, $livewire['component_alias']);
        $this->assertNull($livewire['component_class']);
    }

    /** @test */
    public function it_combines_data_into_one_payload()
    {
        $context = $this->createRequestContext([
            'path' => 'http://localhost/referred',
            'method' => 'GET',
            'id' => uniqid(),
            'name' => 'fake-component',
        ], [], [
            'data' => [
                'string' => 'Ruben',
                'array' => ['a', 'b'],
                'modelCollection' => [],
                'model' => [],
                'date' => '2021-11-10T14:20:36+0000',
                'collection' => ['a', 'b'],
                'stringable' => 'Test',
                'wireable' => ['a', 'b'],
            ],
            'dataMeta' => [
                'modelCollections' => [
                    'modelCollection' => [
                        'class' => 'App\\\\Models\\\\User',
                        'id' => [1, 2, 3, 4],
                        'relations' => [],
                        'connection' => 'mysql',
                    ],
                ],
                'models' => [
                    'model' => [
                        'class' => 'App\\\\Models\\\\User',
                        'id' => 1,
                        'relations' => [],
                        'connection' => 'mysql',
                    ],
                ],
                'dates' => [
                    'date' => 'carbonImmutable',
                ],
                'collections' => [
                    'collection',
                ],
                'stringables' => [
                    'stringable',
                ],
                'wireables' => [
                    'wireable',
                ],
            ],
        ]);

        $livewire = $context->toArray()['livewire'];

        $this->assertEquals([
            "string" => "Ruben",
            "array" => ['a', 'b'],
            "modelCollection" => [
                "class" => "App\\\\Models\\\\User",
                "id" => [1, 2, 3, 4],
                "relations" => [],
                "connection" => "mysql",
            ],
            "model" => [
                "class" => "App\\\\Models\\\\User",
                "id" => 1,
                "relations" => [],
                "connection" => "mysql",
            ],
            "date" => "2021-11-10T14:20:36+0000",
            "collection" => ['a', 'b'],
            "stringable" => "Test",
            "wireable" => ['a', 'b'],
        ], $livewire['data']);
    }

    protected function createRequestContext(array $fingerprint, array $updates = [], array $serverMemo = []): LivewireRequestContext
    {
        $providedRequest = null;

        Route::post('livewire', function (Request $request) use (&$providedRequest) {
            $providedRequest = $request;
        })->name('livewire.message');

        $this->postJson('livewire', [
            'fingerprint' => $fingerprint,
            'serverMemo' => $serverMemo,
            'updates' => $updates,
        ]);

        return new LivewireRequestContext($providedRequest, $this->livewireManager);
    }
}
