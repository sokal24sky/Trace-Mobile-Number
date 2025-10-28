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

$access = [
    'read' => [
        'read.all' => l('global.all')
    ],

    'create' => [
        'create.links' => l('links.title'),
    ],

    'update' => [
        'update.links' => l('links.title'),
    ],

    'delete' => [
        'delete.links' => l('links.title'),
    ],
];

if(settings()->links->projects_is_enabled) {
    $access['create']['create.projects'] = l('projects.title');
    $access['update']['update.projects'] = l('projects.title');
    $access['delete']['delete.projects'] = l('projects.title');
}

if(settings()->links->pixels_is_enabled) {
    $access['create']['create.pixels'] = l('pixels.title');
    $access['update']['update.pixels'] = l('pixels.title');
    $access['delete']['delete.pixels'] = l('pixels.title');
}

if(settings()->codes->ai_qr_codes_is_enabled) {
    $access['create']['create.ai_qr_codes'] = l('ai_qr_codes.title');
    $access['update']['update.ai_qr_codes'] = l('ai_qr_codes.title');
    $access['delete']['delete.ai_qr_codes'] = l('ai_qr_codes.title');
}

if(settings()->codes->qr_codes_is_enabled) {
    $access['create']['create.qr_codes'] = l('qr_codes.title');
    $access['update']['update.qr_codes'] = l('qr_codes.title');
    $access['delete']['delete.qr_codes'] = l('qr_codes.title');
}

if(settings()->codes->barcodes_is_enabled) {
    $access['create']['create.barcodes'] = l('barcodes.title');
    $access['update']['update.barcodes'] = l('barcodes.title');
    $access['delete']['delete.barcodes'] = l('barcodes.title');
}

if(settings()->links->domains_is_enabled) {
    $access['create']['create.domains'] = l('domains.title');
    $access['update']['update.domains'] = l('domains.title');
    $access['delete']['delete.domains'] = l('domains.title');
}

return $access;
