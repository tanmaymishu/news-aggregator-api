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

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'news' => [
        \App\Services\News\NewsSource::NEWSAPI_ID => [
            'base_url' => 'https://newsapi.org/v2',
            'key' => env('NEWSAPI_KEY'),
            'page_size' => 100,
        ],
        \App\Services\News\NewsSource::NYTIMES_ID => [
            'base_url' => 'https://api.nytimes.com',
            'key' => env('NYTIMES_KEY'),
            'page_size' => 10,
        ],
        \App\Services\News\NewsSource::THEGUARDIAN_ID => [
            'base_url' => 'https://content.guardianapis.com',
            'key' => env('THEGUARDIAN_KEY'),
            'page_size' => 100,
        ],
    ]

];
