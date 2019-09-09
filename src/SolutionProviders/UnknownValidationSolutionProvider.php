<?php

namespace Facade\Ignition\SolutionProviders;

use BadMethodCallException;
use Facade\IgnitionContracts\BaseSolution;
use Facade\IgnitionContracts\HasSolutionsForThrowable;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Validation\Validator;
use ReflectionClass;
use ReflectionMethod;
use Throwable;

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
        $rule           = lcfirst(str_replace('validate', '', $possibleMethod));

        return "Did you mean `{$rule}` ?";
    }

    protected function getClassAndMethodFromExceptionMessage(string $message): ?array
    {
        if (! preg_match(self::REGEX, $message, $matches)) {
            return null;
        }

        if ($matches[1] !== Validator::class) {
            return null;
        }

        if (! Str::startsWith($matches[2], 'validate')) {
            return null;
        }

        return [
            'class'  => $matches[1],
            'method' => $matches[2],
        ];
    }

    protected function findPossibleMethod(string $class, string $invalidMethodName)
    {
        return $this->getAvailableMethods($class)
            ->sortByDesc(function (string $method) use ($invalidMethodName) {
                similar_text($invalidMethodName, $method, $percentage);

                return $percentage;
            })->first();
    }

    protected function getAvailableMethods($class): Collection
    {
        $class = new ReflectionClass($class);

        $extensions = Collection::make((\Illuminate\Support\Facades\Validator::make([], []))->extensions)
            ->keys()
            ->map(function (string $extension) {
                return 'validate' . ucfirst($extension);
            });

        return Collection::make($class->getMethods())
            ->filter(function (ReflectionMethod $method) {
                return Str::startsWith($method->name, 'validate') && ! in_array($method->name, [
                    'validate',
                    'validated',
                    'validateAttribute',
                    'validateUsingCustomRule'
                ]);
            })
            ->map(function (ReflectionMethod $method) {
                return $method->name;
            })
            ->merge($extensions);
    }
}
