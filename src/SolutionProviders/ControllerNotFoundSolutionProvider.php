<?php

namespace Facade\Ignition\SolutionProviders;

use Throwable;
use Illuminate\Support\Str;
use Facade\IgnitionContracts\HasSolutionsForThrowable;
use Facade\Ignition\Solutions\CreateControllerSolution;
use Illuminate\Contracts\Container\BindingResolutionException;

class ControllerNotFoundSolutionProvider implements HasSolutionsForThrowable
{
    public function canSolve(Throwable $throwable): bool
    {
        if (! $throwable instanceof BindingResolutionException) {
            return false;
        }

        return  $this->isMissingControllerError($throwable->getMessage());
    }

    protected function isMissingControllerError(string $message): bool
    {
        return Str::startsWith($message, 'Target class [App\\Http\\Controllers\\');
    }

    public function getSolutions(Throwable $throwable): array
    {
        $controller = $this->getController($throwable->getMessage());

        return [
            new CreateControllerSolution($controller),
        ];
    }

    private function getController(string $message): string
    {
        return preg_replace('/^.*\[(.*?)\].*$/', '$1', $message);
    }
}
