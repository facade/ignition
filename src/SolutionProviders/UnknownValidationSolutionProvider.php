<?php

namespace Facade\Ignition\SolutionProviders;

use BadMethodCallException;
use Facade\IgnitionContracts\BaseSolution;
use Facade\IgnitionContracts\HasSolutionsForThrowable;
use Illuminate\Support\Collection;
use Illuminate\Validation\Validator;
use ReflectionClass;
use ReflectionMethod;
use Throwable;

class UnknownValidationSolutionProvider implements HasSolutionsForThrowable
{
    protected const REGEX = '/Illuminate\\\\Validation\\\\Validator::(?P<method>validate(?!(Attribute|UsingCustomRule))[A-Z][a-zA-Z]+)/m';

    public function canSolve(Throwable $throwable): bool
    {
        if (! $throwable instanceof BadMethodCallException) {
            return false;
        }

        return ! is_null($this->getMethodFromExceptionMessage($throwable->getMessage()));
    }

    public function getSolutions(Throwable $throwable): array
    {
        return [
            BaseSolution::create('Unknown Validation Rule')
                ->setSolutionDescription($this->getSolutionDescription($throwable)),
        ];
    }

    protected function getSolutionDescription(Throwable $throwable): string
    {
        $method = $this->getMethodFromExceptionMessage($throwable->getMessage());

        $possibleMethod = $this->findPossibleMethod($method);
        $rule = lcfirst(str_replace('validate', '', $possibleMethod));

        return "Did you mean `{$rule}` ?";
    }

    protected function getMethodFromExceptionMessage(string $message): ?string
    {
        if (! preg_match(self::REGEX, $message, $matches)) {
            return null;
        }

        return $matches['method'];
    }

    protected function findPossibleMethod(string $invalidMethodName)
    {
        return $this->getAvailableMethods()
            ->sortByDesc(function (string $method) use ($invalidMethodName) {
                similar_text($invalidMethodName, $method, $percentage);

                return $percentage;
            })->first();
    }

    protected function getAvailableMethods(): Collection
    {
        $class = new ReflectionClass(Validator::class);

        $extensions = Collection::make((\Illuminate\Support\Facades\Validator::make([], []))->extensions)
            ->keys()
            ->map(function (string $extension) {
                return 'validate' . ucfirst($extension);
            });

        return Collection::make($class->getMethods())
            ->filter(function (ReflectionMethod $method) {
                return preg_match('/(validate(?!(Attribute|UsingCustomRule))[A-Z][a-zA-Z]+)/', $method->name);
            })
            ->map(function (ReflectionMethod $method) {
                return $method->name;
            })
            ->merge($extensions);
    }
}
