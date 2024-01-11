---
nav_order: 4
title: Production builds
---

### Running on a server

Eventhough Bun is very fast, since Bundle transpiles & bundles your imports on the fly it might slow down your uncached blade renders a bit. Because of this, and to catch bundling errors before users hit your page, it is not reccommended to run on a production server. Code should be compiled before you deploy your app.

You may run `php artisan bundle:build` to bundle all your imports beforehand. These will be added to your `storage/app/bundle` directory, make sure to add those to vsc or otherwise build them in CI before deployment.

You may configure what paths are scanned by publishing the Bundle config file and updating the `build_paths` array. Note this config option accepts an array of glob patterns.

```php
'build_paths' => [
    resource_path('views/**/*.blade.php')
]
```

In production the ` BUNDLE_CACHING_ENABLED`` env variable needs to be set to  `true`. When the variable is not set Bundle will automatically enable this option in production environments.

Furthermore it is recommended to cache your blade views on the server by running `php artisan view:cache` in your deploy script.
