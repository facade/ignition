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
        // $originalContents = file_get_contents($parameters['viewFile']);
        // $newContents = str_replace('$'.$parameters['variableName'], '$'.$parameters['variableName']." ?? ''", $originalContents);
        // // Compile blade, tokenize
        // $originalTokens = token_get_all(Blade::compileString($originalContents));
        // $newTokens = token_get_all(Blade::compileString($newContents));
        // // Generate what we expect the tokens to be after we change the blade file
        // $expectedTokens = [];
        // foreach ($originalTokens as $key => $token) {
        //     $expectedTokens[] = $token;
        //     if ($token[0] === T_VARIABLE && $token[1] === '$'.$parameters['variableName']) {
        //         $expectedTokens[] = [T_WHITESPACE, ' ', $token[2]];
        //         $expectedTokens[] = [T_COALESCE, '??', $token[2]];
        //         $expectedTokens[] = [T_WHITESPACE, ' ', $token[2]];
        //         $expectedTokens[] = [T_CONSTANT_ENCAPSED_STRING, "''", $token[2]];
        //     }
        // }
        // if ($expectedTokens !== $newTokens) {
        //     return false;
        // }
        // return $newContents;
    }
}
