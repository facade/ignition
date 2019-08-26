<?php

namespace Facade\Ignition\Tests;

use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\View;
use Facade\Ignition\Exceptions\ViewException;

class ViewTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        View::addLocation(__DIR__.'/stubs/views');
    }

    /** @test */
    public function it_detects_blade_view_exceptions()
    {
        $this->expectException(ViewException::class);

        view('blade-exception')->render();
    }

    /** @test */
    public function it_detects_the_original_line_number_in_view_exceptions()
    {
        try {
            view('blade-exception')->render();
        } catch (ViewException $exception) {
            $this->assertSame(3, $exception->getLine());
        }
    }

    /** @test */
    public function it_adds_additional_blade_information_to_the_exception()
    {
        $viewData = [
            'app' => 'foo',
            'data' => true,
            'user' => new User()
        ];

        try {
            view('blade-exception', $viewData)->render();
        } catch (ViewException $exception) {
            $this->assertSame($viewData, $exception->getViewData());
        }
    }

    /** @test */
    public function it_detects_php_view_exceptions()
    {
        $this->expectException(ViewException::class);

        view('php-exception')->render();
    }
}
