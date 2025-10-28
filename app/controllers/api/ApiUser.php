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

use Altum\Response;
use Altum\Traits\Apiable;

defined('ALTUMCODE') || die();

class ApiUser extends Controller {
    use Apiable;

    public function index() {

        $this->verify_request();

        /* Decide what to continue with */
        switch($_SERVER['REQUEST_METHOD']) {
            case 'GET':
                $this->get();
                break;
        }

        $this->return_404();
    }

    public function get() {

        /* Prepare the data */
        $data = [
            'id' => (int) $this->api_user->user_id,
            'name' => $this->api_user->name,
            'email' => $this->api_user->email,
            'language' => $this->api_user->language,
            'timezone' => $this->api_user->timezone,
            'anti_phishing_code' => (bool) $this->api_user->anti_phishing_code,
            'is_newsletter_subscribed' => (bool) $this->api_user->is_newsletter_subscribed,
            'billing' => $this->api_user->billing,
            'status' => (bool) $this->api_user->status,
            'plan_id' => $this->api_user->plan_id,
            'plan_expiration_date' => $this->api_user->plan_expiration_date,
            'plan_settings' => $this->api_user->plan_settings,
            'plan_trial_done' => (bool) $this->api_user->plan_trial_done,
            'payment_processor' => $this->api_user->payment_processor,
            'payment_total_amount' => $this->api_user->payment_total_amount,
            'payment_currency' => $this->api_user->payment_currency,
            'payment_subscription_id' => $this->api_user->payment_subscription_id,
            'source' => $this->api_user->source,
            'ip' => $this->api_user->ip,
            'continent_code' => $this->api_user->continent_code,
            'country' => $this->api_user->country,
            'city_name' => $this->api_user->city_name,
            'os_name' => $this->api_user->os_name,
            'browser_name' => $this->api_user->browser_name,
            'browser_language' => $this->api_user->browser_language,
            'device_type' => $this->api_user->device_type,
            'api_key' => $this->api_user->api_key,
            'referral_key' => $this->api_user->referral_key,
            'referred_by' => $this->api_user->referred_by,
            'last_activity' => $this->api_user->last_activity,
            'total_logins' => (int) $this->api_user->total_logins,
            'datetime' => $this->api_user->datetime,
            'next_cleanup_datetime' => $this->api_user->next_cleanup_datetime,
        ];

        Response::jsonapi_success($data);
    }
}
