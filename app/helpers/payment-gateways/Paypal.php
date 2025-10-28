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

/* Helper class for PayPal v2 */
defined('ALTUMCODE') || die();

class Paypal {
    static public $sandbox_api_url = 'https://api-m.sandbox.paypal.com/';
    static public $live_api_url = 'https://api-m.paypal.com/';
    static public $access_token = null;

    public static function get_api_url() {
        return settings()->paypal->mode == 'live' ? self::$live_api_url : self::$sandbox_api_url;
    }

    public static function get_access_token() {
        if(self::$access_token) return self::$access_token;

        /* Generate PayPal access token */
        \Unirest\Request::auth(settings()->paypal->client_id, settings()->paypal->secret);

        $response = \Unirest\Request::post(self::get_api_url() . 'v1/oauth2/token', [], \Unirest\Request\Body::form(['grant_type' => 'client_credentials']));

        /* Check against errors */
        if($response->code >= 400) {
            throw new \Exception($response->body->error . ':' . $response->body->error_description);
        }

        return self::$access_token = $response->body->access_token;
    }

    public static function get_headers() {
        return [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . self::get_access_token()
        ];
    }

}
