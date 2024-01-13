---
nav_order: 4
title: Production builds
---

## Running on a server

Eventhough Bun is very fast, since Bundle transpiles & bundles your imports on the fly it might slow down your uncached blade renders a bit. Because of this, and to catch bundling errors before users hit your page, it is not reccommended to run on a production server. Code should be compiled before you deploy your app.

You may run `php artisan bundle:build` to bundle all your imports beforehand. These will be added to your `storage/app/bundle` directory, make sure to add those to vsc or otherwise build them in CI before deployment.

You can control which paths are scanned by publishing the Bundle config file and updating the `build_paths` array. Note this config option accepts an array of glob patterns.

```php
'build_paths' => [
    resource_path('views/**/*.blade.php')
]
```

Furthermore it is recommended to cache your blade views on the server by running `php artisan view:cache` in your deploy script.

<br />

---

## Errors handling

Bundle will throw exceptions in development, but only raise console errors in a production environment.
Errors will still be reported so your error tracking will still pick up any issues raised at blade compile time.

## Failover system

If a import somehow was deleted from storage on your production server, Bundle will try to bundle the script on the fly.

For this to work you need to install Bundle on your app server. You may do this by installing it during deployment or checking in your `node_modules/.bin/bun` file.

<br>

{: .warning }

> This failover system won't work on environments without a writable storage path (Like Vapor or other serverless setups) since Bun requires us to write a temporary file on the same disk Bun is invoked from.
