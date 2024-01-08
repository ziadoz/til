# Feature Flag Laravel Packages

Disable auto-discovery of the package in your `composer.json` file:

```
"extra": {
    "laravel": {
        "dont-discover": [
            "package-namespace/package-name"
        ]
    }
}
```

Then in your `App\Providers\AppServiceProvider` class, add the check inside the `register()` method:

```
if (featureIsEnabled('package-name')) {
    $this->app->register(\Path\To\PackageServiceProvider::class);
}
```

Link: https://mattstauffer.com/blog/conditionally-loading-service-providers-in-laravel-5/