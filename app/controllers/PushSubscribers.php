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

class PushSubscribers extends Controller {

    public function index() {
        redirect('not-found');
    }

    public function create_ajax() {

        if(!\Altum\Plugin::is_active('push-notifications') || !settings()->push_notifications->is_enabled) {
            redirect('not-found');
        }

        if(!is_logged_in() && !settings()->push_notifications->guests_is_enabled) {
            redirect('not-found');
        }

        if(empty($_POST)) {
            redirect();
        }

        /* Check for any errors */
        $required_fields = ['endpoint', 'p256dh', 'auth'];
        foreach($required_fields as $field) {
            if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                redirect();
            }
        }

        /* Parse the data */
        $_POST['endpoint'] = get_url($_POST['endpoint']);
        $subscriber_id = md5($_POST['endpoint']);
        $keys = json_encode([
            'p256dh' => $_POST['p256dh'],
            'auth' => $_POST['auth'],
        ]);

        /* Make sure only whitelisted endpoints are accepted */
        $endpoint = parse_url($_POST['endpoint']);
        $whitelisted_hosts = [
            'android.googleapis.com',
            'fcm.googleapis.com',
            'updates.push.services.mozilla.com',
            'updates-autopush.stage.mozaws.net',
            'updates-autopush.dev.mozaws.net',
            'notify.windows.com',
            'push.apple.com',
            'in-vcm-api.vivoglobal.com',
        ];

        $accepted = false;
        foreach($whitelisted_hosts as $whitelisted_host) {
            if(string_ends_with($whitelisted_host, $endpoint['host'])) {
               $accepted = true;
            }
        }

        if(!$accepted) {
            redirect();
        }

        $ip = get_ip();

        /* Detect the location */
        try {
            $maxmind = (get_maxmind_reader_city())->get($ip);
        } catch(\Exception $exception) {
            /* :) */
        }
        $continent_code = isset($maxmind) && isset($maxmind['continent']) ? $maxmind['continent']['code'] : null;
        $country_code = isset($maxmind) && isset($maxmind['country']) ? $maxmind['country']['iso_code'] : null;
        $city_name = isset($maxmind) && isset($maxmind['city']) ? $maxmind['city']['names']['en'] : null;

        /* Detect extra details about the user */
        $whichbrowser = new \WhichBrowser\Parser($_SERVER['HTTP_USER_AGENT']);
        $browser_name = $whichbrowser->browser->name ?? null;
        $os_name = $whichbrowser->os->name ?? null;
        $browser_language = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? mb_substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) : null;
        $device_type = get_this_device_type();

        /* Insert / update */
        db()->onDuplicate([
            'user_id', 'endpoint', 'keys',
        ])->insert('push_subscribers', [
            'subscriber_id' => $subscriber_id,
            'user_id' => is_logged_in() ? $this->user->user_id : null,
            'endpoint' => $_POST['endpoint'],
            'keys' => $keys,
            'ip' => $ip,
            'city_name' => $city_name,
            'country_code' => $country_code,
            'continent_code' => $continent_code,
            'os_name' => $os_name,
            'browser_name' => $browser_name,
            'browser_language' => $browser_language,
            'device_type' => $device_type,
            'datetime' => get_date(),
        ]);

        die();
    }

    public function delete_ajax() {

        if(!\Altum\Plugin::is_active('push-notifications') || !settings()->push_notifications->is_enabled) {
            redirect('not-found');
        }

        if(!is_logged_in() && !settings()->push_notifications->guests_is_enabled) {
            redirect('not-found');
        }

        if(empty($_POST)) {
            redirect();
        }

        /* Check for any errors */
        $required_fields = ['endpoint', 'p256dh', 'auth'];
        foreach($required_fields as $field) {
            if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                redirect();
            }
        }

        /* Parse the data */
        $_POST['endpoint'] = get_url($_POST['endpoint']);
        $subscriber_id = md5($_POST['endpoint']);

        /* Make sure only whitelisted endpoints are accepted */
        $endpoint = parse_url($_POST['endpoint']);
        $whitelisted_hosts = [
            'android.googleapis.com',
            'fcm.googleapis.com',
            'updates.push.services.mozilla.com',
            'updates-autopush.stage.mozaws.net',
            'updates-autopush.dev.mozaws.net',
            'notify.windows.com',
            'push.apple.com',
            'in-vcm-api.vivoglobal.com',
        ];

        $accepted = false;
        foreach($whitelisted_hosts as $whitelisted_host) {
            if(string_ends_with($whitelisted_host, $endpoint['host'])) {
                $accepted = true;
            }
        }

        if(!$accepted) {
            redirect();
        }

        /* Database query */
        db()->where('subscriber_id', $subscriber_id)->delete('push_subscribers');

        die();
    }

}
