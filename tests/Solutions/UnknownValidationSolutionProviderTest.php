<?php

namespace Facade\Ignition\Tests\Solutions;

use Facade\Ignition\SolutionProviders\UnknownValidationSolutionProvider;
use Facade\Ignition\Tests\TestCase;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UnknownValidationSolutionProviderTest extends TestCase
{
    /** @test */
    public function it_can_solve_the_exception()
    {
        if (version_compare($this->app->version(), '5.6.3', '<')) {
            $this->markTestSkipped('Laravel version < 5.6.3 do not support bad method call solutions');
        }

        $canSolve = app(UnknownValidationSolutionProvider::class)->canSolve($this->getBadMethodCallException());

        $this->assertTrue($canSolve);
    }

    /**
     * @test
     * @dataProvider rulesProvider
     *
     * @param $invalidRule
     * @param $recommendedRule
     */
    public function it_can_recommend_changing_the_rule($invalidRule, $recommendedRule)
    {
        if (version_compare($this->app->version(), '5.6.3', '<')) {
            $this->markTestSkipped('Laravel version < 5.6.3 do not support bad method call solutions');
        }

        /** @var \Facade\IgnitionContracts\Solution $solution */
        $solution = app(UnknownValidationSolutionProvider::class)->getSolutions($this->getBadMethodCallException($invalidRule))[0];

        $this->assertTrue(Str::contains($solution->getSolutionDescription(), "Did you mean `{$recommendedRule}`"));
        $this->assertEquals('Unknown Validation Rule', $solution->getSolutionTitle());
    }

    protected function getBadMethodCallException(string $rule = 'number'): \BadMethodCallException
    {
        $default = new \BadMethodCallException('Not a validation rule exception!');
        try {
            $validator = Validator::make(['number' => 10], ['number' => "{$rule}"]);
            $validator->validate();
            return $default;
        } catch (\BadMethodCallException $badMethodCallException) {
            return $badMethodCallException;
        } catch (\Exception $exception) {
            return $default;
        }
    }

    public function rulesProvider()
    {
        return [
            ['number', 'numeric'],
            ['unik', 'unique'],
        ];
    }
}
