<?php

namespace Facade\Ignition\Tests\Solutions;

use BadMethodCallException;
use Exception;
use Facade\Ignition\SolutionProviders\UnknownValidationSolutionProvider;
use Facade\Ignition\Tests\TestCase;
use Illuminate\Support\Facades\Validator;

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

        Validator::extend('foo', function ($attribute, $value, $parameters, $validator) {
            return $value == 'foo';
        });

        Validator::extendImplicit('bar_a', function ($attribute, $value, $parameters, $validator) {
            return $value == 'bar';
        });

        /** @var \Facade\IgnitionContracts\Solution $solution */
        $solution = app(UnknownValidationSolutionProvider::class)->getSolutions($this->getBadMethodCallException($invalidRule))[0];

        $this->assertEquals("Did you mean `{$recommendedRule}` ?", $solution->getSolutionDescription());
        $this->assertEquals('Unknown Validation Rule', $solution->getSolutionTitle());
    }

    protected function getBadMethodCallException(string $rule = 'number'): BadMethodCallException
    {
        $default = new BadMethodCallException('Not a validation rule exception!');

        try {
            $validator = Validator::make(['number' => 10], ['number' => "{$rule}"]);
            $validator->validate();

            return $default;
        } catch (BadMethodCallException $badMethodCallException) {
            return $badMethodCallException;
        } catch (Exception $exception) {
            return $default;
        }
    }

    /**
     * Return a data set.
     *
     * @return array
     */
    public function rulesProvider(): array
    {
        return [
            ['number', 'numeric'],
            ['unik', 'unique'],
            ['fooo', 'foo'],
            ['bar_b', 'bar_a'],
        ];
    }
}
