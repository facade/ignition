<?php

namespace Facade\Ignition\Solutions;

use Facade\IgnitionContracts\RunnableSolution;

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
        return $this->missingView.' was not found.';
    }

    public function getDocumentationLinks(): array
    {
        return [];
    }

    public function getSolutionActionDescription(): string
    {
        return 'Did you mean `'.$this->suggestedView.'`?';
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
            'controllerPath' => $this->controllerPath,
        ];
    }

    public function isRunnable()
    {
        return $this->updateViewName($this->getRunParameters()) !== false;
    }

    public function run(array $parameters = [])
    {
        $output = $this->updateViewName($parameters);
        if ($output !== false && $output !== '') {
            file_put_contents(app_path().$parameters['controllerPath'], $output);
        }
    }

    public function updateViewName(array $parameters = [])
    {
        if (strpos($parameters['controllerPath'], 'ignition/tests/stubs') !== false) {
            $file = $parameters['controllerPath'];
        } else {
            $file = app_path().$parameters['controllerPath'];
        }
        if (! is_file($file)) {
            return false;
        }
        $contents = file_get_contents($file);
        $tokens = token_get_all($contents);

        $contents = $this->getProposedFileFromTokens(
            $tokens,
            $parameters['missingView'],
            $parameters['suggestedView']
        );

        if ($contents === false) {
            return false;
        }

        return $contents;
    }

    protected function getProposedFileFromTokens(array $tokens, string $missingView, string $suggestedView)
    {
        $viewKey = 0;
        $expectedTokens = collect($tokens)->map(function ($token, $key) use ($missingView, $suggestedView, &$viewKey) {
            if ($token[0] == T_STRING && $token[1] == 'view') {
                $viewKey = $key;
            }
            if ($token[0] === T_CONSTANT_ENCAPSED_STRING && ($viewKey == ($key - 2)) && (
                $token[1] == "'$missingView'" ||
                $token[1] == '"'.$missingView.'"'
            )) {
                $token[1] = "'$suggestedView'";
            }

            return $token;
        })->toArray();
        $newContents = collect($expectedTokens)->map(function ($token) {
            return is_array($token) ? $token[1] : $token;
        })->implode('');

        $newTokens = token_get_all($newContents);

        if ($expectedTokens !== $newTokens) {
            return false;
        }

        return $newContents;
    }
}
