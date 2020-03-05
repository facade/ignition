<?php

namespace Facade\Ignition\Tests\Support;

use Facade\Ignition\Support\ComposerClassMap;
use Facade\Ignition\Tests\TestCase;

class ComposerClassMapTest extends TestCase
{
    /** @test */
    public function it_uses_fake_classmap_if_the_autoloader_does_not_exist()
    {
        $classMap = new ComposerClassMap('invalid');

        $this->assertSame([], $classMap->listClasses());
        $this->assertSame([], $classMap->listClassesInPsrMaps());
        $this->assertNull($classMap->searchClassMap('SomeClass'));
        $this->assertNull($classMap->searchPsrMaps('SomeClass'));
    }
}
