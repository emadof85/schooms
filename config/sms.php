<?php

return [
    /*
    |--------------------------------------------------------------------------
    | SMS Provider Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the SMS provider to use for sending SMS messages.
    | Supported providers: twilio, victorylink
    |
    */

    'provider' => env('SMS_PROVIDER', 'twilio'),

    /*
    |--------------------------------------------------------------------------
    | API Credentials
    |--------------------------------------------------------------------------
    |
    | API credentials for the SMS provider
    |
    */

    'api_key' => env('SMS_API_KEY'),
    'api_secret' => env('SMS_API_SECRET'),
    'sender_id' => env('SMS_SENDER_ID'),

    /*
    |--------------------------------------------------------------------------
    | Default Settings
    |--------------------------------------------------------------------------
    */

    'default_country_code' => env('SMS_DEFAULT_COUNTRY_CODE', '+966'),
    'max_message_length' => env('SMS_MAX_LENGTH', 160),
];