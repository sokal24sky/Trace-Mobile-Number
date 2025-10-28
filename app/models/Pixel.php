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

namespace Altum\Models;

defined('ALTUMCODE') || die();

class Pixel extends Model {

    public function get_pixels($user_id) {
        if(!settings()->links->pixels_is_enabled) return [];

        /* Get the user pixels */
        $pixels = [];

        /* Try to check if the user posts exists via the cache */
        $cache_instance = cache()->getItem('pixels?user_id=' . $user_id);

        /* Set cache if not existing */
        if(is_null($cache_instance->get())) {

            /* Get data from the database */
            $pixels_result = database()->query("SELECT * FROM `pixels` WHERE `user_id` = {$user_id}");
            while($row = $pixels_result->fetch_object()) $pixels[$row->pixel_id] = $row;

            cache()->save(
                $cache_instance->set($pixels)->expiresAfter(CACHE_DEFAULT_SECONDS)->addTag('user_id=' . $user_id)->addTag('pixels?user_id=' . $user_id)
            );

        } else {

            /* Get cache */
            $pixels = $cache_instance->get();

        }

        return $pixels;

    }

    public function get_pixels_by_pixels_ids_and_user_id($pixels_ids, $user_id) {
        if(!settings()->links->pixels_is_enabled) return [];

        if(empty($pixels_ids)) return [];

        $pixels_ids_plain = implode(',', $pixels_ids);

        /* Get the user pixels */
        $pixels = [];

        /* Try to check if the user posts exists via the cache */
        $cache_instance = cache()->getItem('pixels?pixels_ids_plain=' . $pixels_ids_plain);

        /* Set cache if not existing */
        if(is_null($cache_instance->get())) {

            /* Get data from the database */
            $pixels_result = database()->query("SELECT * FROM `pixels` WHERE `pixel_id` IN({$pixels_ids_plain})");
            while($row = $pixels_result->fetch_object()) $pixels[$row->pixel_id] = $row;

            cache()->save(
                $cache_instance->set($pixels)->expiresAfter(CACHE_DEFAULT_SECONDS)->addTag('user_id=' . $user_id)->addTag('pixels?user_id=' . $user_id)
            );

        } else {

            /* Get cache */
            $pixels = $cache_instance->get();

        }

        return $pixels;

    }
}
