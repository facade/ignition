<?php

namespace Facade\Ignition\Solutions;

use Facade\IgnitionContracts\Solution;

class CreateControllerSolution implements Solution
{
    /** @var string */
    protected $class;

    public function __construct(string $class)
    {
        $this->class = $class;
    }

    public function getSolutionTitle(): string
    {
        return 'The requested controller does not exist';
    }

    public function getSolutionDescription(): string
    {
        $controller = $this->getControllerPath();

        return "Your route is pointing to a controller that does not exist. You can create the controller using `php artisan make:controller $controller`.";
    }

    public function getDocumentationLinks(): array
    {
        return [
            'Basics: Controller docs' => 'https://laravel.com/docs/6.0/controllers',
        ];
    }

    private function getControllerPath(): string
    {
        $controller = str_replace('App\\Http\\Controllers\\', '', $this->class);
        $controller = str_replace('\\', '/', $controller);

        return $controller;
    }
}
