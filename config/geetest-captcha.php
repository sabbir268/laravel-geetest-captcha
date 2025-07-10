<?php

// config for Salahhusa9/GeetestCaptcha
return [
    /*
    |--------------------------------------------------------------------------
    | GeeTest Captcha ID
    |--------------------------------------------------------------------------
    |
    | Your GeeTest Captcha ID from the dashboard
    |
    */
    'captcha_id' => env('GEETEST_ID'),

    /*
    |--------------------------------------------------------------------------
    | GeeTest Captcha Key
    |--------------------------------------------------------------------------
    |
    | Your GeeTest Captcha Key from the dashboard
    |
    */
    'captcha_key' => env('GEETEST_KEY'),

    /*
    |--------------------------------------------------------------------------
    | GeeTest API Server
    |--------------------------------------------------------------------------
    |
    | The GeeTest API server endpoint
    |
    */
    'api_server' => env('GEETEST_API_SERVER', 'http://gcaptcha4.geetest.com'),

    /*
    |--------------------------------------------------------------------------
    | Request Timeout
    |--------------------------------------------------------------------------
    |
    | Timeout for API requests in seconds
    |
    */
    'timeout' => env('GEETEST_TIMEOUT', 5),

    /*
    |--------------------------------------------------------------------------
    | JavaScript Assets
    |--------------------------------------------------------------------------
    |
    | GeeTest JavaScript CDN URL
    |
    */
    'js_url' => env('GEETEST_JS_URL', 'https://static.geetest.com/v4/gt4.js'),
];
