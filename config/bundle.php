<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Caching
    |--------------------------------------------------------------------------
    |
    | Here you may specify wherether Bundle loads previously compiled
    | js from disk. Typically you want to enable this on production
    | and disable this on your local development environment.
    |
    */
    'caching_enabled' => env('BUNDLE_CACHING_ENABLED', app()->isProduction()),

    /*
    |--------------------------------------------------------------------------
    | Cache-Control headers
    |--------------------------------------------------------------------------
    |
    | Feel free to tweak Cache-Control headers bundles are served with.
    | Bundle also adds a Last-Modified header in addition to naming
    | the file based on it's hashed contents.
    |
    */
    'cache_control_headers' => 'max-age=31536000, immutable',

    /*
    |--------------------------------------------------------------------------
    | Sourcemaps
    |--------------------------------------------------------------------------
    |
    | Here you may specify wherether Bundle will generate sourcemaps for
    | your imports. Sourcemaps are generated as a separate file, so it
    | won't impact performance when your imports are build for prod.
    |
    */
    'sourcemaps_enabled' => env('BUNDLE_SOURCEMAPS_ENABLED', false),

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
        resource_path('views/**/*.blade.php'),
    ],
];
