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

namespace Altum;

defined('ALTUMCODE') || die();

class Currency {
    public static $currency = null;
    public static $default_currency = null;

    /* Languages directory path */
    public static $path = APP_PATH . 'languages/';

    public static function initialize() {

        self::$default_currency = settings()->payment->default_currency;
        self::$currency = self::$default_currency;

        if(is_logged_in() && \Altum\Authentication::$user->currency && array_key_exists(\Altum\Authentication::$user->currency, (array) settings()->payment->currencies)) {
            self::$currency = \Altum\Authentication::$user->currency;
        }

        if(isset($_COOKIE['set_currency']) && array_key_exists($_COOKIE['set_currency'], (array) settings()->payment->currencies)) {
            self::$currency = $_COOKIE['set_currency'];
        }

    }

}
