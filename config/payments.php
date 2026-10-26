<?php

return [
    'sandbox' => (bool) env('PAYMENTS_SANDBOX', true),
    'vietqr' => [
        'bank_id' => env('VIETQR_BANK_ID', '970422'),
        'account_number' => env('VIETQR_ACCOUNT_NUMBER', '0000000000'),
        'account_name' => env('VIETQR_ACCOUNT_NAME', 'GUNPLAHUB DEMO'),
    ],
];
