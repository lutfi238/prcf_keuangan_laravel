<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Developer Mode
    |--------------------------------------------------------------------------
    |
    | When enabled, OTP codes are displayed on screen for testing purposes.
    | Also enables additional debugging features.
    |
    */
    'developer_mode' => env('PRCF_DEVELOPER_MODE', true),

    /*
    |--------------------------------------------------------------------------
    | Skip OTP Verification
    |--------------------------------------------------------------------------
    |
    | When enabled, users can login without OTP verification.
    | Useful for development and testing.
    |
    */
    'skip_otp' => env('PRCF_SKIP_OTP', true),

    /*
    |--------------------------------------------------------------------------
    | OTP Configuration
    |--------------------------------------------------------------------------
    */
    'otp' => [
        'expiry_seconds' => 60,
        'length' => 6,
        'max_attempts' => 5,
    ],

    /*
    |--------------------------------------------------------------------------
    | Email Configuration
    |--------------------------------------------------------------------------
    */
    'email' => [
        'from_name' => env('MAIL_FROM_NAME', 'PRCF INDONESIA Financial'),
        'from_address' => env('MAIL_FROM_ADDRESS', 'prcfpbl@gmail.com'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Registration Settings
    |--------------------------------------------------------------------------
    */
    'registration' => [
        'enabled' => true,
        'require_admin_approval' => true,
        'default_role' => 'Project Manager',
    ],

    /*
    |--------------------------------------------------------------------------
    | File Upload Settings
    |--------------------------------------------------------------------------
    */
    'uploads' => [
        'max_size_mb' => 10,
        'allowed_extensions' => ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'png', 'jpg', 'jpeg'],
        'paths' => [
            'tor' => 'uploads/tor',
            'budgets' => 'uploads/budgets',
            'receipts' => 'uploads/receipts',
        ],
    ],
];
