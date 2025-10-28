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

/* Easily configurable footer socials */

return [
    'threads' => [
        'name' => 'Threads',
        'icon' => 'fab fa-threads',
        'format' => 'https://threads.com/@%s',
        'input_display_format' => true,
        'placeholder' => '',
    ],

    'youtube' => [
        'name' => 'YouTube',
        'icon' => 'fab fa-youtube',
        'format' => 'https://youtube.com/%s',
        'input_display_format' => true,
        'placeholder' => '',
    ],

    'facebook' => [
        'name' => 'Facebook',
        'icon' => 'fab fa-facebook',
        'format' => 'https://facebook.com/%s',
        'input_display_format' => true,
        'placeholder' => '',
    ],

    'x' => [
        'name' => 'X',
        'icon' => 'fab fa-x-twitter',
        'format' => 'https://x.com/%s',
        'input_display_format' => true,
        'placeholder' => '',
    ],

    'instagram' => [
        'name' => 'Instagram',
        'icon' => 'fab fa-instagram',
        'format' => 'https://instagram.com/%s',
        'input_display_format' => true,
        'placeholder' => '',
    ],

    'tiktok' => [
        'name' => 'TikTok',
        'icon' => 'fab fa-tiktok',
        'format' => 'https://tiktok.com/@%s',
        'input_display_format' => true,
        'placeholder' => '',
    ],

    'linkedin' => [
        'name' => 'LinkedIn',
        'icon' => 'fab fa-linkedin',
        'format' => 'https://linkedin.com/%s',
        'input_display_format' => true,
        'placeholder' => '',
    ],

    'whatsapp' => [
        'name' => 'WhatsApp',
        'icon' => 'fab fa-whatsapp',
        'format' => 'https://wa.me/%s',
        'input_display_format' => false,
        'placeholder' => '010101010101'
    ],

    'email'=> [
        'name' => 'Email',
        'icon' => 'fas fa-envelope',
        'format' => 'mailto:%s',
        'input_display_format' => false,
        'placeholder' => 'hey@example.com'
    ],
];
