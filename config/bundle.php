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
    'caching' => env('BUNDLE_CACHING', true),

    /*
    |--------------------------------------------------------------------------
    | Import resolution timeout
    |--------------------------------------------------------------------------
    |
    | The _import() function uses a built-in non blocking polling mechanism in
    | order to account for script tags that are not processed sequentially.
    | Here you can tweak it's internal timout in ms.
    |
    */
    'import_resolution_timeout' => env('BUNDLE_IMPORT_RESOLUTION_TIMEOUT', 200),

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
    'sourcemaps' => env('BUNDLE_SOURCEMAPS', false),

    /*
    |--------------------------------------------------------------------------
    | Minification
    |--------------------------------------------------------------------------
    |
    | All code is minified by default. This can make issues harder to debug.
    | Using sourcemaps should relieve this issue. But in case you need it;
    | Simply disable the minification option to below to stop minifing.
    |
    */
    'minify' => env('BUNDLE_MINIFY', true),

    /*
    |--------------------------------------------------------------------------
    | Build paths
    |--------------------------------------------------------------------------
    |
    | Here you may specify which directories will be scanned when running the
    | bundle:build command. You'd run this comand to precompile all imports
    | so your production server won't run Bun & bundle imports on the fly.
    |
    */
    'build_paths' => [
        resource_path('views'),
    ],
];
