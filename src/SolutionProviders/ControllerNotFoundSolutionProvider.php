<?php

namespace Facade\Ignition\SolutionProviders;

use Throwable;
use ReflectionException;
use Illuminate\Support\Str;
use Facade\IgnitionContracts\HasSolutionsForThrowable;
use Facade\Ignition\Solutions\CreateControllerSolution;
use Illuminate\Contracts\Container\BindingResolutionException;

class ControllerNotFoundSolutionProvider implements HasSolutionsForThrowable
{
    public function canSolve(Throwable $throwable): bool
    {
        if ($throwable instanceof BindingResolutionException) {
            return $this->isMissingControllerBindingError($throwable->getMessage());
        }

        if ($throwable instanceof ReflectionException) {
            return $this->isMissingControllerReflectionError($throwable->getMessage());
        }

        return false;
    }

    protected function isMissingControllerBindingError(string $message): bool
    {
        return Str::startsWith($message, 'Target class [App\\Http\\Controllers\\');
    }

    protected function isMissingControllerReflectionError(string $message): bool
    {
        return Str::startsWith($message, 'Class App\Http\Controllers\\') && Str::endsWith($message, 'Controller does not exist');
    }

    public function getSolutions(Throwable $throwable): array
    {
        $controller = $this->getController($throwable);

        return [
            new CreateControllerSolution($controller),
        ];
    }

    private function getController(Throwable $throwable): string
    {
        // Class App\Http\Controllers\SomeController does not exist
        if ($throwable instanceof ReflectionException) {
            return preg_replace('/^.*(App\\\\[^\s]+).*$/', '$1', $throwable->getMessage());
        }

        return preg_replace('/^.*\[(.*?)\].*$/', '$1', $throwable->getMessage());
    }
}
