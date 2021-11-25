<?php

namespace Facade\Ignition\Tests\Solutions;

use Facade\Ignition\SolutionProviders\UndefinedLivewireMethodSolutionProvider;
use Facade\Ignition\Tests\stubs\Components\TestLivewireComponent;
use Facade\Ignition\Tests\TestCase;
use Facade\Ignition\Tests\TestClasses\FakeLivewireManager;
use Livewire\Exceptions\MethodNotFoundException;

class UndefinedLivewireMethodSolutionProviderTest extends TestCase
{
    /** @test */
    public function it_can_solve_an_unknown_livewire_method()
    {
        FakeLivewireManager::setUp()->addAlias('test-livewire-component', TestLivewireComponent::class);

        $exception = new MethodNotFoundException('chnge', 'test-livewire-component');

        $canSolve = app(UndefinedLivewireMethodSolutionProvider::class)->canSolve($exception);
        [$solution] = app(UndefinedLivewireMethodSolutionProvider::class)->getSolutions($exception);

        $this->assertTrue($canSolve);

        $this->assertSame('Possible typo `Facade\Ignition\Tests\stubs\Components\TestLivewireComponent::chnge()`', $solution->getSolutionTitle());
        $this->assertSame('Did you mean `change()`?', $solution->getSolutionDescription());
    }
}
