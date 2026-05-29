<?php

declare(strict_types=1);

// config for Ziadoz/LaravelDiagnostics
return [

    /*
    |--------------------------------------------------------------------------
    | HTTP Outbound Check
    |--------------------------------------------------------------------------
    |
    | The URL used for the HTTP outbound connectivity diagnostic. Defaults to
    | Cloudflare's connectivity check endpoint, which is purpose-built for
    | this use case, globally available, and returns a minimal response.
    | Override this if your environment uses a proxy or is air-gapped.
    |
    */
    'http' => [
        'url' => 'https://1.1.1.1/cdn-cgi/trace',
    ],

    /*
    |--------------------------------------------------------------------------
    | User-Registered Diagnostics
    |--------------------------------------------------------------------------
    |
    | Register your own diagnostics here. Each entry should be a fully-qualified
    | class name extending Ziadoz\LaravelDiagnostics\Diagnostics\Diagnostic.
    |
    | Example:
    |   App\Diagnostics\MyServiceDiagnostic::class,
    |
    */
    'diagnostics' => [
        //
    ],

];
