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

use Altum\Models\User;

defined('ALTUMCODE') || die();

class Teams {
    public static $team = null;
    public static $team_member = null;
    public static $team_user = null;

    public static function initialize() {
        if(isset($_SESSION['team_id']) && \Altum\Plugin::is_active('teams')) {
            /* Get requested team */
            self::$team = (new \Altum\Models\Teams())->get_team_by_team_id($_SESSION['team_id']);

            if(self::$team) {
                /* Get team member */
                self::$team_member = (new \Altum\Models\TeamsMembers())->get_team_member_by_team_id_and_user_id(self::$team->team_id, \Altum\Authentication::$user_id);

                if(self::$team_member) {
                    self::$team_member->access = json_decode(self::$team_member->access);
                }
            }
        }
    }

    public static function delegate_access() {
        if(!self::$team || !self::$team_member) {
            return false;
        }

        /* Get team owner user */
        self::$team_user = (new User())->get_user_by_user_id(self::$team->user_id);

        return self::$team_user;
    }

    public static function is_delegated() {
        return self::$team && self::$team_member;
    }

    public static function has_access($access_level = null) {
        if(!self::$team || !self::$team_member) {
            /* Return true as there is no team or team member set */
            return true;
        }

        return self::$team_member->access->{$access_level};
    }
}
