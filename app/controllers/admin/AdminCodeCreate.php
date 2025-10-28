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

class AdminCodeCreate extends Controller {

    public function index() {

        set_time_limit(0);

        /* Requested plan details */
        $plans = (new \Altum\Models\Plan())->get_plans();

        if(!empty($_POST)) {
            /* Filter some of the variables */
            $_POST['name'] = input_clean($_POST['name'], 64);
            $_POST['type'] = in_array($_POST['type'], ['discount', 'redeemable']) ? input_clean($_POST['type']) : 'discount';
            $_POST['days'] = $_POST['type'] == 'redeemable' ? (int) $_POST['days'] : null;
            $_POST['discount'] = $_POST['type'] == 'redeemable' ? 100 : (int) $_POST['discount'];
            $_POST['quantity'] = (int) $_POST['quantity'];
            $_POST['code'] = input_clean(get_slug($_POST['code'], '-', false), 32);
            $_POST['is_bulk'] = (int) isset($_POST['is_bulk']);
            $_POST['amount'] = (int) $_POST['amount'];
            $_POST['prefix'] = mb_strtoupper(input_clean($_POST['prefix']));

            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            if(!\Altum\Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

                $datetime = get_date();
                $plans_ids = json_encode($_POST['plans_ids']);

                /* Bulk generator */
                if($_POST['is_bulk']) {
                    $start_time = microtime(true);

                    $codes_batch = [];

                    for($i = 0; $i < $_POST['amount']; $i++) {
                        $code = $_POST['prefix'] . mb_strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));

                        $codes_batch[] = [
                            'name' => $code,
                            'type' => $_POST['type'],
                            'days' => $_POST['days'],
                            'code' => $code,
                            'discount' => $_POST['discount'],
                            'quantity' => $_POST['quantity'],
                            'plans_ids' => $plans_ids,
                            'datetime' => $datetime,
                        ];
                    }

                    /* Insert data */
                    db()->insertInChunks('codes', $codes_batch);
                }

                /* Normal database insertion */
                else {
                    /* Database query */
                    db()->insert('codes', [
                        'name' => $_POST['name'],
                        'type' => $_POST['type'],
                        'days' => $_POST['days'],
                        'code' => $_POST['code'],
                        'discount' => $_POST['discount'],
                        'quantity' => $_POST['quantity'],
                        'plans_ids' => $plans_ids,
                        'datetime' => $datetime,
                    ]);
                }

                /* Set a nice success message */
                Alerts::add_success(l('global.success_message.create2'));

                redirect('admin/codes');
            }
        }

        $values = [
            'type' => $_POST['type'] ?? 'discount',
            'plans_ids' => $_POST['plans_ids'] ?? array_keys($plans),
        ];

        /* Main View */
        $data = [
            'values' => $values,
            'plans' => $plans,
        ];

        $view = new \Altum\View('admin/code-create/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
