<?php

namespace Facade\Ignition\SolutionProviders;

use Throwable;
use Illuminate\Support\Str;
use UnexpectedValueException;
use Facade\IgnitionContracts\BaseSolution;
use Facade\Ignition\Support\ComposerClassMap;
use Facade\Ignition\Support\StringComparator;
use Facade\IgnitionContracts\HasSolutionsForThrowable;
use Symfony\Component\Debug\Exception\FatalThrowableError;

class MergeConflictSolutionProvider implements HasSolutionsForThrowable
{
    protected const REGEX = '/\[([a-zA-Z\\\\]+)\]/m';

    public function canSolve(Throwable $throwable): bool
    {
        if (! $throwable instanceof FatalThrowableError) {
            return false;
        }

        if (Str::startsWith($throwable->getMessage(), 'syntax error, unexpected \'<<\'')) {
            $file = file_get_contents($throwable->getFile());
            if (strpos($file, '<<<<<<<') !== false &&
                strpos($file, '=======') !== false &&
                strpos($file, '>>>>>>>') !== false
            ) {
                return true;
            }
        }
        return false;
    }

    public function getSolutions(Throwable $throwable): array
    {
        $file = file_get_contents($throwable->getFile());
        preg_match('/\>\>\>\>\>\>\> (.*?)\n/', $file, $matches);
        $source = $matches[1];
        $folder = basename($throwable->getFile());
        $target = trim(`cd $folder; git branch | grep \* | cut -d ' ' -f2`);
        return [
            BaseSolution::create("Merge conflict from branch '$source' into '$target'")
                ->setSolutionDescription("You have a Git merge conflict. To undo your merge do `git reset --hard HEAD`"),
        ];
    }
}
