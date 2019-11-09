<?php

namespace Facade\Ignition\SolutionProviders;

use Facade\Ignition\Solutions\FixMissingSemicolonSolution;
use Facade\IgnitionContracts\HasSolutionsForThrowable;
use ParseError;
use Symfony\Component\Debug\Exception\FatalThrowableError;
use Throwable;

class MissingSemicolonSolutionProvider implements HasSolutionsForThrowable
{
    protected const REGEX = "/syntax error, unexpected \'(.*?)\'(.*?);/";

    public function canSolve(Throwable $throwable): bool
    {
        if (! $throwable instanceof FatalThrowableError && ! $throwable instanceof ParseError) {
            return false;
        }
        preg_match(self::REGEX, $throwable->getMessage(), $matches);

        return isset($matches[1]);
    }

    public function getSolutions(Throwable $throwable): array
    {
        preg_match(self::REGEX, $throwable->getMessage(), $matches);

        $filePath = str_replace(base_path(), '', $throwable->getFile());
        $solution = new FixMissingSemicolonSolution($filePath, $throwable->getLine(), $matches[1]);

        return $solution->isRunnable()
            ? [$solution]
            : [];
    }
}
