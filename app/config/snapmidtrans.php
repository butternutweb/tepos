<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Butternut - SNAP Midtrans - Essentials
    |--------------------------------------------------------------------------
    |
    | Midtrans ServerKey, isProduction,
    |
    */

    'serverKey' => env('MIDTRANS_SERVER_KEY'),
    'clientKey' => env('MIDTRANS_CLIENT_KEY'),
    'isProduction' => env('MIDTRANS_IS_PRODUCTION',false),

    /*
    |--------------------------------------------------------------------------
    | Butternut - SNAP Midtrans - others
    |--------------------------------------------------------------------------
    |
    | Other settings, for your convinience
    |
    */
    'urlSandbox' => 'https://app.sandbox.midtrans.com/snap/v1/transactions',
    'urlProduction' => 'https://app.midtrans.com/snap/v1/transactions',
    'clientUrlSandbox' => 'https://app.sandbox.midtrans.com/snap/snap.js',
    'clientUrlProduction' => 'https://app.midtrans.com/snap/snap.js',
    'challengeUrlSandbox' => 'https://api.sandbox.midtrans.com/v2',
    'challengeUrlProduction' => 'https://api.midtrans.com/v2',

    'enabledPayments' => [
                "credit_card",
                "mandiri_clickpay",
                "cimb_clicks",
                "bca_klikbca",
                "bca_klikpay",
                "bri_epay",
                "telkomsel_cash",
                "echannel",
                "bbm_money",
                "xl_tunai",
                "indosat_dompetku",
                "mandiri_ecash",
                "permata_va",
                "bca_va",
                "other_va",
                "kioson",
                "Indomaret"
    ],


];