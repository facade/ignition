<?php

namespace Facade\Ignition\SolutionProviders;

use Throwable;
use Facade\Ignition\Support\ComposerClassMap;
use Facade\Ignition\Solutions\SuggestImportSolution;
use Facade\IgnitionContracts\HasSolutionsForThrowable;

class MissingImportSolutionProvider implements HasSolutionsForThrowable
{
    /** @var string */
    protected $foundClass;

    /** @var \Facade\Ignition\Support\ComposerClassMap */
    protected $composerClassMap;

    public function canSolve(Throwable $throwable): bool
    {
        $pattern = '/Class \'([^\s]+)\' not found/m';

        if (! preg_match($pattern, $throwable->getMessage(), $matches)) {
            return false;
        }

        $class = $matches[1];

        $this->composerClassMap = new ComposerClassMap();

        $this->search($class);

        return ! is_null($this->foundClass);
    }

    public function getSolutions(Throwable $throwable): array
    {
        $path = $this->composerClassMap->listClasses()[$throwable->getTrace()[0]['class']];
        $fileRelative = str_replace(app_path(), '', $path);
        return [new SuggestImportSolution($this->foundClass, $fileRelative)];
    }

    protected function search(string $missingClass)
    {
        $this->foundClass = $this->composerClassMap->searchClassMap($missingClass);

        if (is_null($this->foundClass)) {
            $this->foundClass = $this->composerClassMap->searchPsrMaps($missingClass);
        }
    }
}
