<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    */

    'mailgun' => [
        'domain'   => env('MAILGUN_DOMAIN'),
        'secret'   => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme'   => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key'    => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Google OAuth — Socialite
    |--------------------------------------------------------------------------
    | Configura en: https://console.cloud.google.com/
    | Authorized redirect URI: https://tu-dominio.com/auth/google/callback
    */
    'google' => [
        'client_id'     => env('988535907686-27fnh8090pmo7ubo2u1kkcauhok4bapk.apps.googleusercontent.com'),
        'client_secret' => env('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'),
        'redirect'      => env('http://127.0.0.1:8000/auth/google/callback'),
    ],

];
