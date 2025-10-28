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

defined('ALTUMCODE') || die();

class TeamUpdate extends Controller {

    public function index() {

        if(!\Altum\Plugin::is_active('teams')) {
            redirect('not-found');
        }

        \Altum\Authentication::guard();

        $team_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        if(!$team = db()->where('team_id', $team_id)->where('user_id', $this->user->user_id)->getOne('teams')) {
            redirect('teams');
        }

        if(!empty($_POST)) {
            $_POST['name'] = trim(input_clean($_POST['name']));

            //ALTUMCODE:DEMO if(DEMO) if($this->user->user_id == 1) Alerts::add_error('Please create an account on the demo to test out this function.');

            /* Check for any errors */
            $required_fields = ['name'];
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
                db()->where('team_id', $team->team_id)->update('teams', [
                    'name' => $_POST['name'],
                    'last_datetime' => get_date(),
                ]);

                /* Clear the cache */
                cache()->deleteItem('team?team_id=' . $team->team_id);

                /* Set a nice success message */
                Alerts::add_success(sprintf(l('global.success_message.update1'), '<strong>' . $_POST['name'] . '</strong>'));

                redirect('team-update/' . $team_id);
            }
        }

        /* Prepare the view */
        $data = [
            'team' => $team
        ];

        $view = new \Altum\View('team-update/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
