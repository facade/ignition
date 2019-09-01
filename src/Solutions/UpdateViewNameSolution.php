<?php

namespace Facade\Ignition\Solutions;

use Illuminate\Support\Facades\Blade;
use Symfony\Component\Filesystem\Filesystem;
use Facade\IgnitionContracts\RunnableSolution;
use Illuminate\Support\Facades\View;

class UpdateViewNameSolution implements RunnableSolution
{
    private $missingView;
    private $suggestedView;

    public function __construct($missingView = null, $suggestedView = null, $controllerPath = null)
    {
        $this->missingView = $missingView;
        $this->suggestedView = $suggestedView;
        $this->controllerPath = $controllerPath;
    }

    public function getSolutionTitle(): string
    {
        return $this->missingView . ' was not found.';
    }

    public function getDocumentationLinks(): array
    {
        return [];
    }

    public function getSolutionActionDescription(): string
    {
        return 'Did you mean ' . $this->suggestedView . '?';
    }

    public function getRunButtonText(): string
    {
        return 'Update view name';
    }

    public function getSolutionDescription(): string
    {
        return '';
    }

    public function getRunParameters(): array
    {
        return [
            'missingView' => $this->missingView,
            'suggestedView' => $this->suggestedView,
            'controllerPath' => $this->controllerPath
        ];
    }

    public function isRunnable(array $parameters = [])
    {
        return $this->updateViewName($this->getRunParameters()) !== false;
    }

    public function run(array $parameters = [])
    {
        $output = $this->updateViewName($parameters);
        if ($output !== false) {
            file_put_contents(app_path() . $parameters['controllerPath'], $output);
        }
    }

    public function updateViewName(array $parameters = [])
    {
        $contents = file_get_contents(app_path() . $parameters['controllerPath']);
        $tokens = token_get_all($contents);
        $expectedTokens = collect($tokens)->map(function($token) use ($parameters) {
            if ($token[0] === T_CONSTANT_ENCAPSED_STRING && (
                $token[1] == "'" .  $parameters['missingView'] . "'" ||
                $token[1] == '"' . $parameters['missingView'] . '"'
            )) {
                $token[1] = "'" .  $parameters['suggestedView'] . "'";
            }
            return $token;
        })->toArray();

        $newContents = collect($expectedTokens)->map(function($token) {
            return is_array($token) ? $token[1] : $token;
        })->implode('');

        $newTokens = token_get_all($newContents);

        // If we're generating a file that is more different than we expected,
        // then don't allow it
        if ($expectedTokens !== $newTokens) {
            return false;
        }
        return $newContents;
    }
}
