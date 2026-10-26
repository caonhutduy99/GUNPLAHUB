<?php

return [
    'sandbox' => (bool) env('SHIPPING_SANDBOX', true),
    'carrier' => env('SHIPPING_CARRIER', 'GHN Sandbox'),
    'tracking_url' => env('SHIPPING_TRACKING_URL'),
];
