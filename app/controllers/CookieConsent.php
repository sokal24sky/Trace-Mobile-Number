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

namespace Altum\Controllers;


defined('ALTUMCODE') || die();

class CookieConsent extends Controller {

    public function index() {

        if(!settings()->cookie_consent->is_enabled || !settings()->cookie_consent->logging_is_enabled) {
            redirect('not-found');
        }

        $payload = @file_get_contents('php://input');
        $_POST = json_decode($payload, true);

        if(!\Altum\Csrf::check('global_token')) {
            redirect();
        }

        /* Detect extra details about the user */
        $whichbrowser = new \WhichBrowser\Parser($_SERVER['HTTP_USER_AGENT']);

        /* Do not track bots */
        if($whichbrowser->device->type == 'bot') {
            return;
        }

        $allowed_levels = ['necessary', 'analytics', 'targeting'];
        $levels = array_filter($_POST['level'], function($level) use ($allowed_levels) {
            return in_array($level, $allowed_levels);
        });

        /* Generate new CSV line */
        $browser_name = $whichbrowser->browser->name ?? null;
        $os_name = $whichbrowser->os->name ?? null;
        $browser_language = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? mb_substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) : null;
        $device_type = get_this_device_type();
        $ip = get_ip();
        $date = (new \DateTime())->format('Y-m-d');
        $time = (new \DateTime())->format('H:i:s') . ' UTC';
        $accepted_levels = implode('+', $levels);

        $new_line = implode(',', [$ip, $date, $time, $accepted_levels, $device_type, $browser_language, $browser_name, $os_name]);

        if(!file_exists(UPLOADS_PATH . 'cookie_consent/data.csv')) {
            $first_line = 'IP,Date,Time,Accepted cookies,Device type,Browser language,Browser name,OS Name';
            file_put_contents(UPLOADS_PATH . 'cookie_consent/data.csv', $first_line . PHP_EOL , FILE_APPEND | LOCK_EX);
        }

        file_put_contents(UPLOADS_PATH . 'cookie_consent/data.csv', $new_line . PHP_EOL , FILE_APPEND | LOCK_EX);

        /* Generate .htaccess if not existing */
        if(!file_exists(UPLOADS_PATH . 'cookie_consent/.htaccess')) {
            file_put_contents(UPLOADS_PATH . 'cookie_consent/.htaccess', 'Deny from all');
        }

        die();
    }

}
