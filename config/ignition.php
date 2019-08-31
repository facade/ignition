<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Editor
    |--------------------------------------------------------------------------
    |
    | Here you can specify the editor that should be opened when clicking
    | code links.
    |
    | Possible values are 'phpstorm', 'vscode', 'sublime' and 'atom'.
    */

    'editor' => env('IGNITION_EDITOR', 'phpstorm'),

    /*
    |--------------------------------------------------------------------------
    | Theme
    |--------------------------------------------------------------------------
    |
    | Specify which theme should be used. You can choose between 'light', 'dark' and 'auto'.
    |
    */

    'theme' => env('IGNITION_THEME', 'light'),

    /*
    |--------------------------------------------------------------------------
    | Masked request parameters
    |--------------------------------------------------------------------------
    |
    | Here you can optionally specify which HTTP request parameters should be
    | be sanitized.
    |
    | Use this to specify any sensitive parameters your application uses such
    | as passwords, credit card numbers, email addresses...
    */

    'masked_request_parameters' => env('IGNITION_MASKED_REQUEST_PARAMETERS', []),
];
