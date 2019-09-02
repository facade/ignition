<?php

return [
    /*
    |
    |--------------------------------------------------------------------------
    | Flare API key
    |--------------------------------------------------------------------------
    |
    | If you want to send your errors to the Flare service, you can specify
    | the API key of your project here.
    |
    | More info: https://flareapp.io/docs/general/projects
    |
    */

    'key' => env('FLARE_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Reporting options
    |--------------------------------------------------------------------------
    |
    | These options determine which information is being transmitted
    | to Flare.
    |
    */

    'reporting' => [
        'anonymize_ips' => true,
        'collect_git_information' => true,
        'report_queries' => true,
        'report_query_bindings' => true,
        'report_view_data' => true,
    ],
];
