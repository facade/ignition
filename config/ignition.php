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
    | Possible values are 'phpstorm', 'vscode', 'vscode-insiders', 'sublime' and 'atom'.
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

    /*
    |--------------------------------------------------------------------------
    | Ignore solution providers
    |--------------------------------------------------------------------------
    |
    | Here you can specify the fully qualified class names of solution providers
    | that should not be used.
    |
    */

    'ignored_solution_providers' => [
        //
    ],

    /*
    |--------------------------------------------------------------------------
    | Runnable solutions
    |--------------------------------------------------------------------------
    |
    | Some of the solutions that Ignition presents to you are runnable and can perform various
    | tasks. By default this is only enabled if your application is running in debug mode.
    | If you would rather disable this feature completely, you can do so using the
    | enable_solution_execution configuration key.
    |
    */

    'enable_runnable_solutions' => env('IGNITION_ENABLE_RUNNABLE_SOLUTIONS', null),

    /*
    |--------------------------------------------------------------------------
    | Remote Path Mapping
    |--------------------------------------------------------------------------
    |
    | If you are using a remote dev server, like Laravel Homestead, Docker, or
    | even a remote VPS, it will be necessary to specify your path mapping.
    | Leaving either one, or both of these, empty or null will not trigger the
    | remote URL changes, and will treat your editor links as local files.
    |
    | 'remote_sites_path' is the full base path of your sites or projects inside
    |                     homestead, Docker, or remote dev servers, for example
    |                     `/home/vagrant/Code`.
    | 'local_sites_path'  is the full base path of your sites or projects on your
    |                     local computer that your IDE or editor is running on,
    |                     for example `/Users/<name>/Code` or
    |                     `C:\Users\<name>\Documents\Code`.
    */

    'remote_sites_path' => env('IGNITION_REMOTE_SITES_PATH', ''),
    'local_sites_path' => env('IGNITION_LOCAL_SITES_PATH', ''),

];
