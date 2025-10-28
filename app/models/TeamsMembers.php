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

class TeamsMembers extends Model {

    public function get_team_member_by_team_id_and_user_id($team_id, $user_id) {

        /* Get the team member */
        $team_member = null;

        /* Try to check if the resource exists via the cache */
        $cache_instance = cache()->getItem('team_member?team_id=' . $team_id . '&user_id=' . $user_id);

        /* Set cache if not existing */
        if(is_null($cache_instance->get())) {

            /* Get data from the database */
            $team_member = db()->where('team_id', $team_id)->where('user_id', $user_id)->getOne('teams_members');

            if($team_member) {
                cache()->save(
                    $cache_instance->set($team_member)->expiresAfter(CACHE_DEFAULT_SECONDS)->addTag('user_id=' . $team_member->user_id)->addTag('team_id=' . $team_member->team_id)
                );
            }

        } else {

            /* Get cache */
            $team_member = $cache_instance->get();

        }

        return $team_member;

    }

}
