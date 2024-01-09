<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Caching
    |--------------------------------------------------------------------------
    |
    | Here you may specify wherether Bundle loads already compiled scripts
    | from disk when previously compiled. Typically you want to enable
    | this on production & disable this for your local development.
    |
    */
    'caching_enabled' => env('BUNDLE_CACHING_ENABLED', app()->isProduction()),

    /*
    |--------------------------------------------------------------------------
    | Build paths (glob patterns)
    |--------------------------------------------------------------------------
    |
    | Here you may specify which directories will be scanned when running the
    | bundle:build command. You'd run this comand to precompile all imports
    | so your production server won't run Bun & bundle imports on the fly.
    |
    */
    'build_paths' => [
        resource_path('views/**/*.blade.php')
    ]
];
