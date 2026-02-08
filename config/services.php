<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],
    'google' => [
    'client_id' => env('GOOGLE_CLIENT_ID'),
    'client_secret' => env('GOOGLE_CLIENT_SECRET'),
    'redirect' => env('GOOGLE_REDIRECT_URL', '/auth/google/call-back'),
],
'momo' => [
        'environment' => env('MOMO_ENV', 'sandbox'),
        'api_user' => env('MOMO_API_USER'),
        'api_key' => env('MOMO_API_KEY'),
        'subscription_key' => env('MOMO_SUBSCRIPTION_KEY'),
        'callback_url' => env('MOMO_CALLBACK_URL'),
    ],
    'brevo' => [
    'api_key' => env('BREVO_API_KEY'),
],
    'weflexfy' => [
        'access_key' => env('WEFLEXFY_ACCESS_KEY'),
        'secret_key' => env('WEFLEXFY_SECRET_KEY'),
        'business_number' => env('WEFLEXFY_BUSINESS_NUMBER'),
        'recipient_number' => env('WEFLEXFY_BUSINESS_NUMBER'),
    ],

'google_maps' => [
    'api_key' => env('GOOGLE_MAPS_API_KEY'),
],

'exchange_rate' => [
    'api_key' => env('EXCHANGE_RATE_API_KEY'),
    'base_currency' => env('EXCHANGE_RATE_BASE_CURRENCY', 'RWF'),
],

'google_vision' => [
    'api_key' => env('GOOGLE_VISION_API_KEY'),
],

];
