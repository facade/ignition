<?php

namespace Facade\Ignition\Support;

use Illuminate\Support\Str;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class ComposerClassMap
{
    /** @var \Composer\Autoload\ClassLoader */
    protected $composer;

    /** @var string */
    protected $basePath;

    public function __construct(?string $autoloaderPath = null)
    {
        $this->composer = require $autoloaderPath ?? base_path('/vendor/autoload.php');
        $this->basePath = app_path();
    }

    public function listClasses(): array
    {
        $classes = $this->composer->getClassMap();

        return array_merge($classes, $this->listClassesInPsrMaps());
    }

    public function searchClassMap(string $missingClass): ?string
    {
        foreach ($this->composer->getClassMap() as $fqcn => $file) {
            $basename = basename($file, '.php');

            if ($basename === $missingClass) {
                return $fqcn;
            }
        }

        return null;
    }

    public function listClassesInPsrMaps(): array
    {
        // TODO: This is incorrect. Doesnt list all fqcns. Need to parse namespace? e.g. App\LoginController is wrong

        $prefixes = array_merge(
            $this->composer->getPrefixes(),
            $this->composer->getPrefixesPsr4()
        );

        $classes = [];

        foreach ($prefixes as $namespace => $directories) {
            foreach ($directories as $directory) {
                $files = $this->getFiles($directory);
                foreach ($files as $file) {
                    /** @var SplFileInfo $file */
                    $fqcn = $this->getFullyQualifiedClassNameFromFile($namespace, $file);
                    $classes[$fqcn] = $file->getRelativePathname();
                }
            }
        }

        return $classes;
    }

    public function searchPsrMaps(string $missingClass): ?string
    {
        $prefixes = array_merge(
            $this->composer->getPrefixes(),
            $this->composer->getPrefixesPsr4()
        );

        foreach ($prefixes as $namespace => $directories) {
            foreach ($directories as $directory) {
                $files = $this->getFiles($directory);

                foreach ($files as $file) {
                    /** @var SplFileInfo $file */
                    $basename = basename($file->getRelativePathname(), '.php');

                    if ($basename === $missingClass) {
                        return $namespace.basename($file->getRelativePathname(), '.php');
                    }
                }
            }
        }

        return null;
    }

    protected function getFullyQualifiedClassNameFromFile(string $rootNamespace, SplFileInfo $file): string
    {
        $class = trim(str_replace($this->basePath, '', $file->getRealPath()), DIRECTORY_SEPARATOR);

        $class = str_replace(
            [DIRECTORY_SEPARATOR, 'App\\'],
            ['\\', app()->getNamespace()],
            ucfirst(Str::replaceLast('.php', '', $class))
        );

        return $rootNamespace.$class;
    }

    private function getFiles($directory, \Closure $closure = null)
    {
        return (new Finder)
            ->in($directory)
            ->files()
            ->filter(null !== $closure ? $closure : function ($file) {
                return $file instanceof SplFileInfo;
            })
            ->name('*.php');
    }
}
