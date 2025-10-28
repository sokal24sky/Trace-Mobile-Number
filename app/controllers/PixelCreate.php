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

class PixelCreate extends Controller {

    public function index() {

        if(!settings()->links->pixels_is_enabled) {
            redirect('not-found');
        }

        \Altum\Authentication::guard();

        /* Team checks */
        if(\Altum\Teams::is_delegated() && !\Altum\Teams::has_access('create.pixels')) {
            Alerts::add_info(l('global.info_message.team_no_access'));
            redirect('pixels');
        }

        /* Check for the plan limit */
        $total_rows = database()->query("SELECT COUNT(*) AS `total` FROM `pixels` WHERE `user_id` = {$this->user->user_id}")->fetch_object()->total ?? 0;

        if($this->user->plan_settings->pixels_limit != -1 && $total_rows >= $this->user->plan_settings->pixels_limit) {
            Alerts::add_info(l('global.info_message.plan_feature_limit'));
            redirect('pixels');
        }

        if(!empty($_POST)) {
            $_POST['name'] = trim(query_clean($_POST['name']));
            $_POST['type'] = array_key_exists($_POST['type'], require APP_PATH . 'includes/l/pixels.php') ? $_POST['type'] : '';
            $_POST['pixel'] = trim(query_clean($_POST['pixel']));

            //ALTUMCODE:DEMO if(DEMO) if($this->user->user_id == 1) Alerts::add_error('Please create an account on the demo to test out this function.');

            /* Check for any errors */
            $required_fields = ['name', 'type', 'pixel'];
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
                db()->insert('pixels', [
                    'user_id' => $this->user->user_id,
                    'type' => $_POST['type'],
                    'name' => $_POST['name'],
                    'pixel' => $_POST['pixel'],
                    'datetime' => get_date(),
                ]);

                /* Set a nice success message */
                Alerts::add_success(sprintf(l('global.success_message.create1'), '<strong>' . $_POST['name'] . '</strong>'));

                /* Clear the cache */
                cache()->deleteItem('pixels?user_id=' . $this->user->user_id);
                cache()->deleteItem('pixels_total?user_id=' . $this->user->user_id);

                redirect('pixels');
            }
        }

        $values = [
            'name' => $_POST['name'] ?? '',
            'type' => $_POST['type'] ?? '',
            'pixel' => $_POST['pixel'] ?? '',
        ];

        /* Prepare the view */
        $data = [
            'values' => $values
        ];

        $view = new \Altum\View('pixel-create/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
