<?php

namespace Facade\Ignition\SolutionProviders;

use Facade\IgnitionContracts\ProvidesSolution;
use Throwable;
use Illuminate\Support\Collection;
use Facade\IgnitionContracts\Solution;
use Facade\IgnitionContracts\HasSolutionsForThrowable;
use Facade\IgnitionContracts\SolutionProviderRepository as SolutionProviderRepositoryContract;

class SolutionProviderRepository implements SolutionProviderRepositoryContract
{
    /** @var \Illuminate\Support\Collection */
    protected $solutionProviders;

    public function __construct(array $solutionProviders = [])
    {
        $this->solutionProviders = Collection::make($solutionProviders);
    }

    public function registerSolutionProvider(string $solutionProviderClass): SolutionProviderRepositoryContract
    {
        $this->solutionProviders->push($solutionProviderClass);

        return $this;
    }

    public function registerSolutionProviders(array $solutionProviderClasses): SolutionProviderRepositoryContract
    {
        $this->solutionProviders->merge($solutionProviderClasses);

        return $this;
    }

    public function getSolutionsForThrowable(Throwable $throwable): array
    {
        $solutions = [];

        if ($throwable instanceof Solution) {
            $solutions[] = $throwable;
        }

        if ($throwable instanceof ProvidesSolution) {
            $solutions[] = $throwable->getSolution();
        }

        $providerSolutions = $this->solutionProviders
            ->filter(function (string $solutionClass) {
                return in_array(HasSolutionsForThrowable::class, class_implements($solutionClass));
            })
            ->map(function (string $solutionClass) {
                return app($solutionClass);
            })
            ->filter
            ->canSolve($throwable)
            ->map(function (HasSolutionsForThrowable $solutionProvider) use ($throwable) {
                return $solutionProvider->getSolutions($throwable);
            })
            ->flatten()
            ->toArray();

        return array_merge($solutions, $providerSolutions);
    }

    public function getSolutionForClass(string $solutionClass): ?Solution
    {
        if (! class_exists($solutionClass)) {
            return null;
        }

        if (! in_array(Solution::class, class_implements($solutionClass))) {
            return null;
        }

        return app($solutionClass);
    }
}
