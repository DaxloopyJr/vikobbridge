<?php

return [
    'base_url' => env('SELCOM_BASE_URL', 'https://api.selcommobile.com'),
    'api_key' => env('SELCOM_API_KEY', ''),
    'api_secret' => env('SELCOM_API_SECRET', ''),
    'vendor_id' => env('SELCOM_VENDOR_ID', ''),
    'currency' => 'TZS',
    'callback_url' => env('APP_URL') . '/payment/callback',
];
