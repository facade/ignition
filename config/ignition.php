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
    | Homestead Path Mapping
    |--------------------------------------------------------------------------
    |
    | If you are using homestead, it will be necessary to specify your path
    | mapping, just like in your Homestead.yaml file.
    |
    | 'homestead-sites-path' is the base path inside homestead, f.e. `/home/vagrant/Code`
    | 'local-sites-path' is the base path on your host computer, f.e. `/Users/<name>/Code` or `C:\Users\<name>\Documents\Code`
    */
    'homestead-sites-path' => env('IGNITION_HOMESTEAD_SITES_PATH', '/home/vagrant'),
    'local-sites-path' => env('IGNITION_LOCAL_SITES_PATH', '~/code'),

    /*
    |--------------------------------------------------------------------------
    | Theme
    |--------------------------------------------------------------------------
    |
    | Specify which theme should be used. You can choose between 'light', 'dark' and 'auto'.
    |
    */

    'theme' => env('IGNITION_THEME', 'light'),
];
