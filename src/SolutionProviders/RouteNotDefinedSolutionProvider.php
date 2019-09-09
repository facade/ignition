<?php

namespace Facade\Ignition\SolutionProviders;

use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Throwable;
use InvalidArgumentException;
use Illuminate\Support\Facades\Route;
use Facade\IgnitionContracts\BaseSolution;
use Facade\Ignition\Exceptions\ViewException;
use Facade\Ignition\Support\StringComparator;
use Facade\IgnitionContracts\HasSolutionsForThrowable;

class RouteNotDefinedSolutionProvider implements HasSolutionsForThrowable
{
    protected const REGEX = '/Route \[(.*)\] not defined/m';

    public function canSolve(Throwable $throwable): bool
    {
        if (! $throwable instanceof RouteNotFoundException) {
            return false;
        }

        return preg_match(self::REGEX, $throwable->getMessage(), $matches);
    }

    public function getSolutions(Throwable $throwable): array
    {
        preg_match(self::REGEX, $throwable->getMessage(), $matches);

        $missingRoute = $matches[1] ?? null;

        $suggestedRoute = $this->findRelatedRoute($missingRoute);

        if ($suggestedRoute) {
            return [
                BaseSolution::create("{$missingRoute} was not defined.")
                    ->setSolutionDescription("Did you mean `{$suggestedRoute}`?"),
            ];
        }

        return [
            BaseSolution::create("{$missingRoute} was not defined.")
                ->setSolutionDescription('Are you sure that the route is defined'),
        ];
    }

    protected function findRelatedRoute(string $missingRoute): ?string
    {
        Route::getRoutes()->refreshNameLookups();

        return StringComparator::findClosestMatch(array_keys(Route::getRoutes()->getRoutesByName()), $missingRoute);
    }
}
