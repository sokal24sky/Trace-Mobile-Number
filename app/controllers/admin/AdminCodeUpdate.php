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

class AdminCodeUpdate extends Controller {

    public function index() {

        $code_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        if(!$code = db()->where('code_id', $code_id)->getOne('codes')) {
            redirect('admin/codes');
        }

        $code->plans_ids = json_decode($code->plans_ids ?? '[]');

        /* Requested plan details */
        $plans = (new \Altum\Models\Plan())->get_plans();

        if(!empty($_POST)) {
            /* Filter some of the variables */
            $_POST['name'] = input_clean($_POST['name'], 64);
            $_POST['type'] = in_array($_POST['type'], ['discount', 'redeemable']) ? input_clean($_POST['type']) : 'discount';
            $_POST['days'] = $_POST['type'] == 'redeemable' ? (int) $_POST['days'] : null;
            $_POST['plan_id'] = empty($_POST['plan_id']) ? null : (int) $_POST['plan_id'];
            $_POST['discount'] = $_POST['type'] == 'redeemable' ? 100 : (int) $_POST['discount'];
            $_POST['quantity'] = (int) $_POST['quantity'];
            $_POST['code'] = input_clean(get_slug($_POST['code'], '-', false), 32);

            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            if(!\Altum\Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

                /* Database query */
                db()->where('code_id', $code_id)->update('codes', [
                    'name' => $_POST['name'],
                    'type' => $_POST['type'],
                    'days' => $_POST['days'],
                    'code' => $_POST['code'],
                    'discount' => $_POST['discount'],
                    'quantity' => $_POST['quantity'],
                    'plans_ids' => json_encode($_POST['plans_ids']),
                ]);

                /* Set a nice success message */
                Alerts::add_success(sprintf(l('global.success_message.update1'), '<strong>' . $_POST['code'] . '</strong>'));

                /* Refresh the page */
                redirect('admin/code-update/' . $code_id);

            }

        }

        /* Main View */
        $data = [
            'code_id' => $code_id,
            'code' => $code,
            'plans' => $plans,
        ];

        $view = new \Altum\View('admin/code-update/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
