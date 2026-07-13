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

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | BMKG Weather API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for BMKG (Badan Meteorologi, Klimatologi, dan Geofisika)
    | weather API integration. Location code determines the forecast area.
    |
    */

    'bmkg' => [
        'api_url' => env('BMKG_API_URL', 'https://api.bmkg.go.id/publik/prakiraan-cuaca'),
        'location_code' => env('BMKG_LOCATION_CODE', '501297'), // Default: Jakarta (adjust to your location)
        'timeout' => env('BMKG_API_TIMEOUT', 10), // seconds
        
        // Location codes reference (Indonesia):
        // 501297 = DKI Jakarta
        // 501271 = Bandung, Jawa Barat
        // 501212 = Surabaya, Jawa Timur
        // 501128 = Semarang, Jawa Tengah
        // 501153 = Yogyakarta
        // Check https://api.bmkg.go.id for your area code
    ],

];
