<?php

namespace Facade\Ignition\Solutions;

use Illuminate\Support\Facades\Blade;
use Symfony\Component\Filesystem\Filesystem;
use Facade\IgnitionContracts\RunnableSolution;
use Illuminate\Support\Facades\View;

class CreateViewFileSolution implements RunnableSolution
{
    private $viewName;

    public function __construct($viewName = null)
    {
        $this->viewName = $viewName;
    }

    public function getSolutionTitle(): string
    {
        return $this->viewName . ' was not found.';
    }

    public function getDocumentationLinks(): array
    {
        return [];
    }

    public function getSolutionActionDescription(): string
    {
        return 'Are you sure the view exist and is a `.blade.php` file?';
    }

    public function getRunButtonText(): string
    {
        return 'Create file';
    }

    public function getSolutionDescription(): string
    {
        return '';
    }

    public function getRunParameters(): array
    {
        return [
            'viewName' => $this->viewName,
        ];
    }

    public function run(array $parameters = [])
    {
        $parts = explode('.', $parameters['viewName']);
        $file = array_pop($parts);
        $path = implode('/', $parts);
        $fileViewFinder = View::getFinder();

        $filesystem = new Filesystem();
        $filesystem->mkdir($fileViewFinder->getPaths()[0] . '/' . $path . '/');
        touch($fileViewFinder->getPaths()[0] . '/' . $path . '/' . $file . '.blade.php');
    }
}
