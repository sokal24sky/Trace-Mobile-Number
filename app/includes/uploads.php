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
    /* Main */
    'logo_light' => [
        'whitelisted_file_extensions' => ['jpg', 'jpeg', 'png', 'svg', 'gif', 'webp', 'avif'],
        'path' => 'main/',
    ],
    'logo_dark' => [
        'whitelisted_file_extensions' => ['jpg', 'jpeg', 'png', 'svg', 'gif', 'webp', 'avif'],
        'path' => 'main/',
    ],
    'logo_email' => [
        'whitelisted_file_extensions' => ['jpg', 'jpeg', 'png', 'gif'],
        'path' => 'main/',
    ],
    'favicon' => [
        'whitelisted_file_extensions' => ['jpg', 'jpeg', 'png', 'ico', 'svg', 'gif', 'webp'],
        'path' => 'main/',
    ],
    'opengraph' => [
        'whitelisted_file_extensions' => ['jpg', 'jpeg', 'png', 'svg', 'gif', 'webp', 'avif'],
        'path' => 'main/',
    ],
    'custom_images' => [
        'whitelisted_file_extensions' => ['jpg', 'jpeg', 'png', 'svg', 'gif', 'webp', 'avif'],
        'path' => 'main/',
    ],

    /* Users misc */
    'users' => [
        'whitelisted_file_extensions' => ['jpg', 'jpeg', 'png', 'svg', 'gif', 'webp'],
        'path' => 'users/',
    ],

    /* PWA plugin */
    'app_icon' => [
        'whitelisted_file_extensions' => ['png'],
        'path' => 'pwa/',
    ],
    'app_screenshots' => [
        'whitelisted_file_extensions' => ['jpg', 'jpeg', 'png'],
        'path' => 'pwa/',
    ],
    'pwa' => [
        'path' => 'pwa/',
    ],

    /* Dynamic OG images plugin */
    'dynamic_og_images' => [
        'whitelisted_file_extensions' => ['jpg', 'jpeg', 'png', 'webp'],
    ],

    'push_notifications_icon' => [
        'whitelisted_file_extensions' => ['jpg', 'jpeg', 'png'],
        'path' => 'main/',
    ],

    /* Blog featured images */
    'blog' => [
        'whitelisted_file_extensions' => ['jpg', 'jpeg', 'png', 'svg', 'gif', 'webp', 'avif'],
        'path' => 'blog/',
    ],

    /* Payment proofs for offline payments */
    'offline_payment_proofs' => [
        'whitelisted_file_extensions' => ['jpg', 'jpeg', 'png', 'webp', 'avif', 'pdf'],
        'path' => 'offline_payment_proofs/',
    ],

    /* AI QR codes */
    'ai_qr_codes' => [
        'path' => 'ai_qr_codes/'
    ],

    'ai_qr_codes/temp' => [
        'path' => 'ai_qr_codes/temp/'
    ],

    'ai_qr_code_default_image' => [
        'whitelisted_file_extensions' => ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp', 'avif'],
        'path' => 'ai_qr_codes/'
    ],


    /* QR codes */
    'qr_codes' => [
        'path' => 'qr_codes/logo/'
    ],
    'qr_code' => [
        'path' => 'qr_codes/logo/'
    ],

    'qr_codes/logo' => [
        'whitelisted_file_extensions' => ['jpg', 'jpeg', 'png', 'gif'],
        'path' => 'qr_codes/logo/'
    ],
    'qr_code_logo' => [
        'whitelisted_file_extensions' => ['jpg', 'jpeg', 'png', 'gif'],
        'path' => 'qr_codes/logo/'
    ],

    'qr_code_default_image' => [
        'whitelisted_file_extensions' => ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp', 'avif'],
        'path' => 'qr_codes/logo/'
    ],

    'qr_code_background' => [
        'whitelisted_file_extensions' => ['jpg', 'jpeg', 'png', 'gif']
    ],

    'qr_code_foreground' => [
        'whitelisted_file_extensions' => ['jpg', 'jpeg', 'png', 'gif']
    ],

    'qr_code_reader' => [
        'whitelisted_file_extensions' => ['png', 'jpg', 'jpeg', 'svg', 'webp']
    ],

    /* Barcodes */
    'barcodes' => [
    ],

    'barcode_reader' => [
        'whitelisted_file_extensions' => ['png', 'jpg', 'jpeg', 'svg', 'webp']
    ],

    /* Favicons */
    'favicons' => [
        'whitelisted_file_extensions' => ['jpg', 'jpeg', 'png', 'ico', 'svg', 'gif', 'webp'],
        'path' => 'favicons/'
    ],

    /* Opengraphs */
    'opengraphs' => [
        'whitelisted_file_extensions' => ['jpg', 'jpeg', 'png', 'ico', 'svg', 'gif', 'webp'],
        'path' => 'opengraphs/'
    ],
];
