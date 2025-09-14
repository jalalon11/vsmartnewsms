<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => [
        'api/*',
        'sanctum/csrf-cookie',
        'login',
        'logout',
        'register',
        '*'
    ],

    'allowed_methods' => ['*'],

    'allowed_origins' => env('APP_ENV') === 'local' ? ['*'] : [
        env('APP_URL'),
        'https://' . parse_url(env('APP_URL'), PHP_URL_HOST),
        'http://' . parse_url(env('APP_URL'), PHP_URL_HOST),
    ],

    'allowed_origins_patterns' => [
        env('CORS_ALLOWED_ORIGINS_PATTERN', ''),
    ],

    'allowed_headers' => [
        '*',
        'Accept',
        'Authorization',
        'Content-Type',
        'X-Requested-With',
        'X-CSRF-TOKEN',
        'X-Inertia',
        'X-Inertia-Version',
    ],

    'exposed_headers' => [
        'X-Inertia',
        'X-Inertia-Location',
    ],

    'max_age' => env('CORS_MAX_AGE', 86400),

    'supports_credentials' => true,

];
