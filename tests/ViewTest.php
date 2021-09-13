<?php

namespace Facade\Ignition\Tests;

use Facade\Ignition\Exceptions\ViewException;
use Facade\Ignition\Exceptions\ViewExceptionWithSolution;
use Facade\IgnitionContracts\BaseSolution;
use Facade\IgnitionContracts\ProvidesSolution;
use Facade\IgnitionContracts\Solution;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\View;

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
    public function it_detects_the_original_line_number_in_view_exceptions_with_utf8_characters()
    {
        try {
            view('blade-exception-utf8')->render();
        } catch (ViewException $exception) {
            $this->assertSame(11, $exception->getLine());
        }
    }

    /** @test */
    public function it_adds_additional_blade_information_to_the_exception()
    {
        $viewData = [
            'app' => 'foo',
            'data' => true,
            'user' => new User(),
        ];

        try {
            view('blade-exception', $viewData)->render();
        } catch (ViewException $exception) {
            $this->assertSame($viewData, $exception->getViewData());
        }
    }

    /** @test */
    public function it_adds_base_exception_solution_to_view_exception()
    {
        try {
            $exception = new ExceptionWithSolution();
            view('solution-exception', ['exception' => $exception])->render();
        } catch (ViewException $exception) {
            $this->assertTrue($exception instanceof ViewExceptionWithSolution);
            $this->assertInstanceOf(Solution::class, $exception->getSolution());
            $this->assertSame('This is a solution', $exception->getSolution()->getSolutionTitle());
        }
    }

    /** @test */
    public function it_detects_php_view_exceptions()
    {
        $this->expectException(ViewException::class);

        view('php-exception')->render();
    }
}

class ExceptionWithSolution extends \Exception implements ProvidesSolution
{
    public function getSolution(): Solution
    {
        return BaseSolution::create('This is a solution')
            ->setSolutionDescription('With a description');
    }
}
