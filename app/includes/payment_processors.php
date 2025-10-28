<?php
/*
 * Copyright (c) 2025 AltumCode (https://altumcode.com/)
 *
 * This software is licensed exclusively by AltumCode and is sold only via https://altumcode.com/.
 * Unauthorized distribution, modification, or use of this software without a valid license is not permitted and may be subject to applicable legal actions.
 *
 * 🌍 View all other existing AltumCode projects via https://altumcode.com/
 * 📧 Get in touch for support or general queries via https://altumcode.com/contact
 * 📤 Download the latest version via https://altumcode.com/downloads
 *
 * 🐦 X/Twitter: https://x.com/AltumCode
 * 📘 Facebook: https://facebook.com/altumcode
 * 📸 Instagram: https://instagram.com/altumcode
 */

defined('ALTUMCODE') || die();

return [
    'paypal' => [
        'payment_type' => ['one_time', 'recurring'],
        'icon' => 'fab fa-paypal',
        'color' => '#3b7bbf',
    ],
    'stripe' => [
        'payment_type' => ['one_time', 'recurring'],
        'icon' => 'fab fa-stripe',
        'color' => '#5433FF',
    ],
    'offline_payment' => [
        'payment_type' => ['one_time'],
        'icon' => 'fas fa-university',
        'color' => '#393f4a',
    ],
    'coinbase' => [
        'payment_type' => ['one_time'],
        'icon' => 'fab fa-bitcoin',
        'color' => '#0050FF',
    ],
    'payu' => [
        'payment_type' => ['one_time'],
        'icon' => 'fas fa-underline',
        'color' => '#A6C306',
    ],
    'iyzico' => [
        'payment_type' => ['one_time'],
        'icon' => 'fas fa-teeth',
        'color' => '#1E64FF',
    ],
    'paystack' => [
        'payment_type' => ['one_time', 'recurring'],
        'icon' => 'fas fa-money-check',
        'color' => '#00C3F7',
    ],
    'razorpay' => [
        'payment_type' => ['one_time', 'recurring'],
        'icon' => 'fas fa-heart',
        'color' => '#2b84ea',
    ],
    'mollie' => [
        'payment_type' => ['one_time', 'recurring'],
        'icon' => 'fas fa-shopping-basket',
        'color' => '#465975',
    ],
    'yookassa' => [
        'payment_type' => ['one_time'],
        'icon' => 'fas fa-ruble-sign',
        'color' => '#004CAA',
    ],
    'crypto_com' => [
        'payment_type' => ['one_time'],
        'icon' => 'fas fa-coins',
        'color' => '#4b71d7',
    ],
    'paddle' => [
        'payment_type' => ['one_time'],
        'icon' => 'fas fa-star',
        'color' => '#a6b0b9',
    ],
    'mercadopago' => [
        'payment_type' => ['one_time'],
        'icon' => 'fas fa-handshake',
        'color' => '#009EE3',
    ],
    'midtrans' => [
        'payment_type' => ['one_time'],
        'icon' => 'fas fa-grip-vertical',
        'color' => '#002855',
    ],
    'flutterwave' => [
        'payment_type' => ['one_time', 'recurring'],
        'icon' => 'fas fa-water',
        'color' => '#FB9129',
    ],
    'lemonsqueezy' => [
        'payment_type' => ['one_time', 'recurring'],
        'icon' => 'fas fa-lemon',
        'color' => '#F5C518',
    ],
    'myfatoorah' => [
        'payment_type' => ['one_time'],
        'icon' => 'fas fa-feather',
        'color' => '#0000ff',
    ],
];
