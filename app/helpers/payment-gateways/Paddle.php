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

namespace Altum\PaymentGateways;

/* Helper class for Paddle */
defined('ALTUMCODE') || die();

class Paddle {
    static public $sandbox_api_url = 'https://sandbox-vendors.paddle.com/api/';
    static public $live_api_url = 'https://vendors.paddle.com/api/';

    public static function get_api_url() {
        return settings()->paddle->mode == 'live' ? self::$live_api_url : self::$sandbox_api_url;
    }

}
