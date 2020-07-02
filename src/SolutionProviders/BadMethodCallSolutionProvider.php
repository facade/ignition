<?php

namespace Facade\Ignition\SolutionProviders;

use Throwable;
use ReflectionClass;
use ReflectionMethod;
use BadMethodCallException;
use Illuminate\Support\Collection;
use Facade\IgnitionContracts\BaseSolution;
use phpDocumentor\Reflection\DocBlockFactory;
use Facade\IgnitionContracts\HasSolutionsForThrowable;

class BadMethodCallSolutionProvider implements HasSolutionsForThrowable
{
    protected const REGEX = '/([a-zA-Z\\\\]+)::([a-zA-Z]+)/m';

    public function canSolve(Throwable $throwable): bool
    {
        if (! $throwable instanceof BadMethodCallException) {
            return false;
        }

        if (is_null($this->getClassAndMethodFromExceptionMessage($throwable->getMessage()))) {
            return false;
        }

        return true;
    }

    public function getSolutions(Throwable $throwable): array
    {
        return [
            BaseSolution::create('Bad Method Call')
            ->setSolutionDescription($this->getSolutionDescription($throwable)),
        ];
    }

    public function getSolutionDescription(Throwable $throwable): string
    {
        if (! $this->canSolve($throwable)) {
            return '';
        }

        extract($this->getClassAndMethodFromExceptionMessage($throwable->getMessage()), EXTR_OVERWRITE);

        $possibleMethod = $this->findPossibleMethod($class, $method);

        return "Did you mean {$class}::{$possibleMethod}() ?";
    }

    protected function getClassAndMethodFromExceptionMessage(string $message): ?array
    {
        if (! preg_match(self::REGEX, $message, $matches)) {
            return null;
        }

        return [
            'class' => $matches[1],
            'method' => $matches[2],
        ];
    }

    protected function findPossibleMethod(string $class, string $invalidMethodName)
    {
        return $this->getAvailableMethods($class)
            ->sortByDesc(function ($method) use ($invalidMethodName) {
                similar_text($invalidMethodName, $method, $percentage);
                return $percentage;
            })->first();
    }

    protected function getAvailableMethods($class): Collection
    {
        $class = new ReflectionClass($class);

        return Collection::make($class->getMethods())
        ->map(function (ReflectionMethod $method) {
            return $method->name;
        })->merge($this->getAvailableMethodsFromClassDoc($class));
    }

    public function getAvailableMethodsFromClassDoc(ReflectionClass $class): Collection
    {
        $doc = DocBlockFactory::createInstance()->create($class->getDocComment());

        $methods = [];
        foreach ($doc->getTagsByName('method') as $tag) {
            $methods[] = $tag->getMethodName();
        }

        return Collection::make($methods);
    }
}
