<?php

namespace Facade\Ignition\SolutionProviders;

use Illuminate\Support\Str;
use Illuminate\Validation\Validator;
use Throwable;
use ReflectionClass;
use ReflectionMethod;
use BadMethodCallException;
use Illuminate\Support\Collection;
use Facade\IgnitionContracts\BaseSolution;
use Facade\IgnitionContracts\HasSolutionsForThrowable;

class UnknownValidationSolutionProvider implements HasSolutionsForThrowable
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

        extract($this->getClassAndMethodFromExceptionMessage($throwable->getMessage()), EXTR_OVERWRITE);

        if ($class !== Validator::class || ! Str::startsWith($method, 'validate')) {
            return false;
        }

        return true;
    }

    public function getSolutions(Throwable $throwable): array
    {
        return [
            BaseSolution::create('Unknown Validation Rule')
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
        $rule = strtolower(str_replace('validate', '', $possibleMethod->name));

        return "Did you mean `{$rule}` ?";
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
            ->sortByDesc(function (ReflectionMethod $method) use ($invalidMethodName) {
                similar_text($invalidMethodName, $method->name, $percentage);

                return $percentage;
            })->first();
    }

    protected function getAvailableMethods($class): Collection
    {
        $class = new ReflectionClass($class);

        return Collection::make($class->getMethods())
            ->filter(function (ReflectionMethod $method) {
                return Str::startsWith($method->name, 'validate');
            });
    }
}
