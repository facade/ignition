<?php

namespace Facade\Ignition\Tests;

use Exception;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class RenderIgnitionPageTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        config()->set('app.debug', true);

        Route::get('will-fail', function () {
            throw new Exception('My exception');
        });
    }

    /** @test */
    public function when_requesting_html_it_will_respond_with_html()
    {
        /** @var \Illuminate\Http\Response $response */
        $response = $this
            ->get('will-fail')
            ->baseResponse;

        $this->assertStringStartsWith('text/html', $response->headers->get('Content-Type'));
        $this->assertTrue(Str::contains($response->getContent(), 'html'));
    }

    /** @test */
    public function when_requesting_json_it_will_respond_with_json()
    {
        /** @var \Illuminate\Http\Response $response */
        $response = $this
            ->getJson('will-fail');

        $this->assertStringStartsWith('application/json', $response->headers->get('Content-Type'));
        $this->assertEquals('My exception', json_decode($response->getContent(), true)['message']);
    }
}
