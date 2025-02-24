<?php

return [
    /*
    |--------------------------------------------------------------------------
    | External Payment Convenience Fee
    |--------------------------------------------------------------------------
    |
    | This value represents the convenience fee (in cents) charged for external
    | payment methods like Zip. Set to 0 to disable the convenience fee.
    |
    */
    'convenience_fee' => env('PARKING_CONVENIENCE_FEE', 1500), // Default ₱15.00
];
