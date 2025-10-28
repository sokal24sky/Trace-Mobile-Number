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

class Projects extends Model {

    public function get_projects_by_user_id($user_id) {
        if(!settings()->links->projects_is_enabled) return [];

        /* Get the user projects */
        $projects = [];

        /* Try to check if the user posts exists via the cache */
        $cache_instance = cache()->getItem('projects?user_id=' . $user_id);

        /* Set cache if not existing */
        if(is_null($cache_instance->get())) {

            /* Get data from the database */
            $projects_result = database()->query("SELECT * FROM `projects` WHERE `user_id` = {$user_id}");
            while($row = $projects_result->fetch_object()) $projects[$row->project_id] = $row;

            cache()->save(
                $cache_instance->set($projects)->expiresAfter(CACHE_DEFAULT_SECONDS)->addTag('user_id=' . $user_id)
            );

        } else {

            /* Get cache */
            $projects = $cache_instance->get();

        }

        return $projects;

    }

}
