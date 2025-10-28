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

use Altum\Alerts;
use Altum\Title;

defined('ALTUMCODE') || die();

class TeamMemberUpdate extends Controller {

    public function index() {

        if(!\Altum\Plugin::is_active('teams')) {
            redirect('not-found');
        }

        \Altum\Authentication::guard();

        $team_member_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        if(!$team_member = db()->where('team_member_id', $team_member_id)->getOne('teams_members')) {
            redirect('teams');
        }
        $team_member->access = json_decode($team_member->access);

        if(!$team = db()->where('team_id', $team_member->team_id)->where('user_id', $this->user->user_id)->getOne('teams')) {
            redirect('teams');
        }

        $teams_access = require APP_PATH . 'includes/teams_access.php';

        if(!empty($_POST)) {
            /* Generate the access variable for the database */
            $access = [];
            foreach($teams_access as $key => $value) {
                foreach($value as $access_key => $access_translation) {
                    $access[$access_key] = in_array($access_key, $_POST['access'] ?? []);
                }
            }

            /* Force read access */
            $access['read.all'] = true;

            //ALTUMCODE:DEMO if(DEMO) if($this->user->user_id == 1) Alerts::add_error('Please create an account on the demo to test out this function.');

            /* Check for any errors */
            $required_fields = [];
            foreach($required_fields as $field) {
                if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                    Alerts::add_field_error($field, l('global.error_message.empty_field'));
                }
            }

            if(!\Altum\Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

                /* Database query */
                db()->where('team_member_id', $team_member->team_member_id)->update('teams_members', [
                    'access' => json_encode($access),
                    'last_datetime' => get_date(),
                ]);

                /* Clear the cache */
                cache()->deleteItem('team_member?team_id=' . $team_member->team_id . '&user_id=' . $team_member->user_id);

                /* Set a nice success message */
                Alerts::add_success(l('global.success_message.update2'));

                redirect('team-member-update/' . $team_member_id);
            }
        }

        /* Set a custom title */
        Title::set(sprintf(l('team_member_update.title'), $team->name));

        /* Prepare the view */
        $data = [
            'team' => $team,
            'team_member' => $team_member,
            'teams_access' => $teams_access
        ];

        $view = new \Altum\View('team-member-update/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
