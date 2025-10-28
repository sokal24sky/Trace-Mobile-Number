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

class AdminTaxCreate extends Controller {

    public function index() {

        if(!empty($_POST)) {
            /* Filter some of the variables */
            $_POST['name'] = input_clean($_POST['name'], 64);
            $_POST['description'] = input_clean($_POST['description'], 256);
            $_POST['value'] = (float) $_POST['value'];
            $_POST['value_type'] = in_array($_POST['value_type'], ['percentage', 'fixed']) ? input_clean($_POST['value_type']) : 'fixed';
            $_POST['type'] = in_array($_POST['type'], ['inclusive', 'exclusive']) ? input_clean($_POST['type']) : 'inclusive';
            $_POST['billing_type'] = in_array($_POST['billing_type'], ['personal', 'business', 'both']) ? input_clean($_POST['billing_type']) : 'both';
            $_POST['countries'] = isset($_POST['countries']) ? array_query_clean($_POST['countries']) : null;

            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            if(!\Altum\Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

                /* Database query */
                db()->insert('taxes', [
                    'name' => $_POST['name'],
                    'description' => $_POST['description'],
                    'value' => $_POST['value'],
                    'value_type' => $_POST['value_type'],
                    'type' => $_POST['type'],
                    'billing_type' => $_POST['billing_type'],
                    'countries' => json_encode($_POST['countries']),
                    'datetime' => get_date(),
                ]);

                /* Set a nice success message */
                Alerts::add_success(sprintf(l('global.success_message.create1'), '<strong>' . $_POST['name'] . '</strong>'));

                redirect('admin/taxes');
            }
        }

        /* Main View */
        $data = [];

        $view = new \Altum\View('admin/tax-create/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
