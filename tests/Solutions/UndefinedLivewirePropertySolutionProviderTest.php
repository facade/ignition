<?php

namespace Facade\Ignition\Tests\Solutions;

use Facade\Ignition\SolutionProviders\UndefinedLivewirePropertySolutionProvider;
use Facade\Ignition\Tests\stubs\Components\TestLivewireComponent;
use Facade\Ignition\Tests\TestCase;
use Facade\Ignition\Tests\TestClasses\FakeLivewireManager;
use Livewire\Exceptions\PropertyNotFoundException;

class UndefinedLivewirePropertySolutionProviderTest extends TestCase
{
    /** @test */
    public function it_can_solve_an_unknown_livewire_property()
    {
        FakeLivewireManager::setUp()->addAlias('test-livewire-component', TestLivewireComponent::class);

        $exception = new PropertyNotFoundException('strng', 'test-livewire-component');

        $canSolve = app(UndefinedLivewirePropertySolutionProvider::class)->canSolve($exception);
        [$firstSolution, $secondSolution] = app(UndefinedLivewirePropertySolutionProvider::class)->getSolutions($exception);

        $this->assertTrue($canSolve);

        $this->assertSame('Possible typo Facade\Ignition\Tests\stubs\Components\TestLivewireComponent::$strng', $firstSolution->getSolutionTitle());
        $this->assertSame('Did you mean `$string`?', $firstSolution->getSolutionDescription());

        $this->assertSame('Possible typo Facade\Ignition\Tests\stubs\Components\TestLivewireComponent::$strng', $secondSolution->getSolutionTitle());
        $this->assertSame('Did you mean `$stringable`?', $secondSolution->getSolutionDescription());
    }

    /** @test */
    public function it_can_solve_an_unknown_livewire_computed_property()
    {
        FakeLivewireManager::setUp()->addAlias('test-livewire-component', TestLivewireComponent::class);

        $exception = new PropertyNotFoundException('compted', 'test-livewire-component');

        $canSolve = app(UndefinedLivewirePropertySolutionProvider::class)->canSolve($exception);
        [$solution] = app(UndefinedLivewirePropertySolutionProvider::class)->getSolutions($exception);

        $this->assertTrue($canSolve);

        $this->assertSame('Possible typo Facade\Ignition\Tests\stubs\Components\TestLivewireComponent::$compted', $solution->getSolutionTitle());
        $this->assertSame('Did you mean `$computed`?', $solution->getSolutionDescription());
    }
}
