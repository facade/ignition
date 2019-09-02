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
    | Sharing
    |--------------------------------------------------------------------------
    |
    | Ignition allows you to manually share your local errors with colleagues or people around the world.
    | Sharing errors is completely free and does not require an account on Flare.
    | If you do not want to have the ability to share your local errors, you can disable it here.
    |
    */

    'enable_share_button' => env('IGNITION_SHARING_ENABLED', true),
];
