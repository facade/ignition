<?php

namespace Facade\Ignition\Solutions;

use Facade\IgnitionContracts\RunnableSolution;

class SuggestImportSolution implements RunnableSolution
{
    /** @var string */
    protected $class;

    public function __construct(string $class = null)
    {
        $this->class = $class;
    }

    public function getSolutionTitle(): string
    {
        return 'A class import is missing';
    }

    public function getSolutionDescription(): string
    {
        return '';
    }

    public function getSolutionActionDescription(): string
    {
        $output = [
            'You have a missing class import. Try importing this class: `'.$this->class.'`.',
        ];

        return implode(PHP_EOL, $output);
    }

    public function getRunButtonText(): string
    {
        return 'Import class';
    }

    public function getDocumentationLinks(): array
    {
        return [];
    }

    public function getRunParameters(): array
    {
        return [
            'class' => $this->class,
        ];
    }

    public function isRunnable(array $parameters = [])
    {
        return false;
        //return $this->makeOptional($this->getRunParameters()) !== false;
    }

    public function run(array $parameters = [])
    {
        $output = $this->importClass($parameters);
        if ($output !== false) {
            file_put_contents($parameters['viewFile'], $output);
        }
    }

    public function importClass(array $parameters = [])
    {
        return false;
    }
}
