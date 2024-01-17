---
nav_order: 5
title: Production builds
image: "/assets/social-square.png"
---

## Running on a server

Eventhough Bun is very fast, since Bundle transpiles & bundles your imports on the fly it might slow down your uncached blade renders a bit. Because of this, and to catch bundling errors before users hit your page, it is not recommended to run on a production server. Code should be compiled before you deploy your app.

You may run `php artisan bundle:build` to bundle all your imports beforehand. These will be added to your `storage/app/bundle` directory, make sure to add those to vsc or otherwise build them in CI before deployment.

Note you need to check in your `storage/app/bundle` directory in version control or run the build command in CI in order to distribute the files on your production environment.

You can control which paths are scanned by publishing the Bundle config file and updating the `build_paths` array. Note this config option accepts an array paths.

```php
'build_paths' => [
    resource_path('views'),
]
```

Furthermore it is recommended to cache your blade views on the server by running `php artisan view:cache` in your deploy script.

Note that Bundle doesn't process your imports in production environments. So you don't need to install any npm dependencies on your production machines when you built everything beforehand unless you want to use the [failover system](https://laravel-bundle.dev/production-builds.html#failover-system).

<br />

---

## Error handling

Bundle will throw exceptions in when Laravel's debug mode is enabled, but only raise console errors when it's not.
Errors will still be reported so your error tracking will pick up any issues raised at blade compile time.

We'd love to add more readable error messages & comprehensive Ignition solutions. There are a lot of different scenarios to account for and we need your help! If you think we should provide a Ignition solution for a specific error please reach out via [GitHub](https://github.com/gwleuverink/bundle).

## Failover system

If a import somehow was deleted from storage on your production server, Bundle will try to process the script on the fly.

For this to work you need to install Bun on your app server. You may do this by installing it during deployment or checking in your `node_modules/.bin/bun` file (or the entire node_modules directory if you are importing files from there).

<br>

{: .warning }

> The failover system won't work on environments without a writable storage path (Like Vapor or other serverless setups) since Bun requires us to write a temporary file on the same disk Bun is invoked from.
