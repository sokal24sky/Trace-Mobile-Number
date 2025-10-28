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

class Logger {

    public static function users($user_id, $type) {

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
        $device_type = get_this_device_type();

        /* Detect extra details about the user */
        $whichbrowser = new \WhichBrowser\Parser($_SERVER['HTTP_USER_AGENT']);

        /* Detect extra details about the user */
        $os_name = $whichbrowser->os->name ?? null;

        db()->insert('users_logs', [
            'user_id'        => $user_id,
            'type'           => $type,
            'ip'             => $ip,
            'device_type'    => $device_type,
            'os_name'        => $os_name,
            'continent_code' => $continent_code,
            'country_code'   => $country_code,
            'city_name'      => $city_name,
            'datetime'       => get_date(),
        ]);

    }

}
