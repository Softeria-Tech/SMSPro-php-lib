<?php

return [
    /*
    |--------------------------------------------------------------------------
    | API Key
    |--------------------------------------------------------------------------
    |
    | This is your API key from SMSPro platform. You can find it in your
    | account dashboard.
    |
    */
    'api_key' => env('SMSPRO_API_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Sender ID
    |--------------------------------------------------------------------------
    |
    | This is your registered sender ID from SMSPro platform.
    | Leave empty to use the default sender ID.
    |
    */
    'sender_id' => env('SMSPRO_SENDER_ID', ''),

    /*
    |--------------------------------------------------------------------------
    | Base URL
    |--------------------------------------------------------------------------
    |
    | The base URL for the SMSPro API.
    |
    */
    'base_url' => env('SMSPRO_BASE_URL', 'https://sms.softeriatech.com/api/v1/bulksms'),

    /*
    |--------------------------------------------------------------------------
    | Default Country Code
    |--------------------------------------------------------------------------
    |
    | Default country code to prepend to phone numbers if not provided.
    |
    */
    'default_country_code' => env('SMSPRO_DEFAULT_COUNTRY_CODE', '254'),

    /*
    |--------------------------------------------------------------------------
    | Timeout
    |--------------------------------------------------------------------------
    |
    | Request timeout in seconds.
    |
    */
    'timeout' => env('SMSPRO_TIMEOUT', 30),

    /*
    |--------------------------------------------------------------------------
    | Retry Attempts
    |--------------------------------------------------------------------------
    |
    | Number of retry attempts for failed requests.
    |
    */
    'retry_attempts' => env('SMSPRO_RETRY_ATTEMPTS', 3),

    /*
    |--------------------------------------------------------------------------
    | Log Channel
    |--------------------------------------------------------------------------
    |
    | The log channel to use for logging SMS activities.
    |
    */
    'log_channel' => env('SMSPRO_LOG_CHANNEL', 'daily'),
];