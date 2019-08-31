<?php

namespace Facade\Ignition\Tests\Middleware;

use \Facade\Ignition\Middleware\UpdateMaskedRequestParameters as TestSubject;
use \PHPUnit\Framework\TestCase;

class UpdateMaskedRequestParameters extends TestCase
{

    /**
     * @var \Facade\Ignition\Middleware\UpdateMaskedRequestParameters
     */
    private $subject;
    /**
     * @var \Faker\Generator
     */
    private $faker;

    public function __construct($name = null, array $data = [], $dataName = '') {
        parent::__construct($name, $data, $dataName);

        $this->subject = new TestSubject();
        $this->faker = \Faker\Factory::create();
    }

    public function getFieldsToMask(): array
    {
        return [
            'password',
            'email',
            'email_address',
        ];
    }

    /** @test */
    public function it_correctly_masks_top_level_properties()
    {
        $before = $expected = [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'email' => $this->faker->email,
        ];

        $this->subject->maskProperties($this->getFieldsToMask(), $before);

        $this->assertNotEquals($expected['email'], $before['email']);

        unset($expected['email'], $before['email']);

        $this->assertEquals($expected, $before);
    }

    /** @test */
    public function it_correctly_masks_nested_level_properties()
    {
        $before = $expected = [
            'company' => $this->faker->company,
            'request' => [
                'first_name' => $this->faker->firstName,
                'last_name' => $this->faker->lastName,
                'email' => $this->faker->email,
            ]
        ];

        $this->subject->maskProperties($this->getFieldsToMask(), $before);

        $this->assertNotEquals($expected['request']['email'], $before['request']['email']);

        unset($expected['request']['email'], $before['request']['email']);

        $this->assertEquals($expected, $before);
    }

    /** @test */
    public function it_correctly_masks_multiple_nested_level_properties()
    {
        $before = $expected = [
            'company' => $this->faker->company,
            'request' => [
                'first_name' => $this->faker->firstName,
                'last_name' => $this->faker->lastName,
                'email_address' => $this->faker->email,
                'password' => $this->faker->email,
            ]
        ];

        $this->subject->maskProperties($this->getFieldsToMask(), $before);

        $this->assertNotEquals($expected['request']['email_address'], $before['request']['email_address']);
        $this->assertNotEquals($expected['request']['password'], $before['request']['password']);

        unset(
            $expected['request']['email_address'],
            $before['request']['email_address'],

            $expected['request']['password'],
            $before['request']['password']
        );

        $this->assertEquals($expected, $before);
    }

    /** @test */
    public function it_correctly_types_properties()
    {
        $actual = $this->subject->cast("123456789");

        $this->assertEquals(123456789, $actual);
        $this->assertIsInt($actual);

        $actual = $this->subject->cast("123456789.5");

        $this->assertEquals(123456789.5, $actual);
        $this->assertIsFloat($actual);

        $actual = $this->subject->cast("testing");

        $this->assertEquals("testing", $actual);
        $this->assertIsString($actual);
    }

    /** @test */
    public function it_correctly_identifies_changes()
    {
        $before = [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
        ];

        $changed = false;
        $this->subject->maskProperties($this->getFieldsToMask(), $before, $changed);

        $this->assertFalse($changed);

        $before['email'] = $this->faker->email;

        $this->subject->maskProperties($this->getFieldsToMask(), $before, $changed);

        $this->assertTrue($changed);
    }

    /** @test */
    public function it_correctly_masks_a_query_string()
    {
        $url = $before_url = $this->faker->url;
        $this->subject->maskQueryString($this->getFieldsToMask(), $url);

        $this->assertEquals($before_url, $url);

        $url = $before_url = $this->faker->url  . '?' . http_build_query(['password' => $this->faker->password(10)]);
        $this->subject->maskQueryString($this->getFieldsToMask(), $url);

        $this->assertNotEquals($before_url, $url);
    }

    /** @test */
    public function it_randomises_a_value_from_the_provided_array()
    {
        $tests = ['a', 'b', 'c', 'd', 'e', 'f'];

        $random_selections = [];
        for ($i = 0; $i < 30; $i++) {
            $random_selection = $this->subject->randomiseValueFromArray($tests);

            if (!isset($random_selections[$random_selection])) {
                $random_selections[$random_selection] = 0;
            }
            $random_selections[$random_selection]++;

            $this->assertTrue(in_array($random_selection, $tests));
        }

        $this->assertGreaterThan(1, count($random_selections));
    }

    /** @test */
    public function it_generates_a_random_string()
    {
        $result = $this->subject->randomString(30);

        $this->assertTrue(strlen($result) === 30);

        $this->assertIsString($result);
    }

    /** @test */
    public function it_generates_a_random_float()
    {
        $result = $this->subject->randomFloat(15);

        $this->assertTrue(strlen($result) === 15);

        $this->assertIsFloat($result);
    }

    /** @test */
    public function it_generates_a_random_integer()
    {
        $result = $this->subject->randomInteger(10);

        $this->assertTrue(strlen($result) === 10);

        $this->assertIsInt($result);
    }
}
