<?php

namespace Facade\Ignition\SolutionProviders;

use Composer\Autoload\ClassLoader;
use Facade\Ignition\Solutions\SuggestImportSolution;
use Facade\Ignition\Support\ComposerClassMap;
use Facade\IgnitionContracts\HasSolutionsForThrowable;
use Facade\IgnitionContracts\Solution;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Throwable;

class MissingImportSolutionProvider implements HasSolutionsForThrowable
{
    /** @var string */
    protected $foundClass;

    /** @var \Facade\Flare\Support\ComposerClassMap */
    protected $composerClassMap;

    public function canSolve(Throwable $throwable): bool
    {
        $pattern = '/Class \'([^\s]+)\' not found/m';

        if (!preg_match($pattern, $throwable->getMessage(), $matches)) {
            return false;
        }

        $class = $matches[1];

        $this->composerClassMap = new ComposerClassMap();

        $this->search($class);

        return ! is_null($this->foundClass);
    }

    public function getSolutions(Throwable $throwable): array
    {
        return [new SuggestImportSolution($this->foundClass)];
    }

    protected function search(string $missingClass)
    {
        $this->foundClass = $this->composerClassMap->searchClassMap($missingClass);

        if (is_null($this->foundClass)) {
            $this->foundClass = $this->composerClassMap->searchPsrMaps($missingClass);
        }
    }
}
