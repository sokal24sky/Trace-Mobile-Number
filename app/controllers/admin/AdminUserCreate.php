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
use Altum\Logger;
use Altum\Models\User;

defined('ALTUMCODE') || die();

class AdminUserCreate extends Controller {

    public function index() {

        /* Default variables */
        $values = [
            'name' => '',
            'email' => '',
            'password' => ''
        ];

        if(!empty($_POST)) {

            /* Clean some posted variables */
            $_POST['name']		= input_clean($_POST['name']);
            $_POST['email']		= filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

            /* Default variables */
            $values['name'] = $_POST['name'];
            $values['email'] = $_POST['email'];
            $values['password'] = $_POST['password'];

            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* Check for any errors */
            $required_fields = ['name', 'email' ,'password'];
            foreach($required_fields as $field) {
                if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                    Alerts::add_field_error($field, l('global.error_message.empty_field'));
                }
            }

            if(!\Altum\Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }
            if(mb_strlen($_POST['name']) < 1 || mb_strlen($_POST['name']) > 64) {
                Alerts::add_field_error('name', l('admin_users.error_message.name_length'));
            }
            if(db()->where('email', $_POST['email'])->has('users')) {
                Alerts::add_field_error('email', l('admin_users.error_message.email_exists'));
            }
            if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                Alerts::add_field_error('email', l('global.error_message.invalid_email'));
            }
            if(mb_strlen($_POST['password']) < 6 || mb_strlen($_POST['password']) > 64) {
                Alerts::add_field_error('password', l('global.error_message.password_length'));
            }

            /* If there are no errors, continue */
            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

                $registered_user = (new User())->create(
                    $_POST['email'],
                    $_POST['password'],
                    $_POST['name'],
                    1,
                    'admin_create',
                    null,
                    null,
                    false,
                    'free',
                    json_encode(settings()->plan_free->settings),
                    null,
                    settings()->main->default_timezone,
                    '',
                    true
                );

                /* Send webhook notification if needed */
                if(settings()->webhooks->user_new) {
                    fire_and_forget('post', settings()->webhooks->user_new, [
                        'user_id' => $registered_user['user_id'],
                        'email' => $_POST['email'],
                        'name' => $_POST['name'],
                        'source' => 'admin_create',
                        'is_newsletter_subscribed' => false,
                        'datetime' => get_date(),
                    ]);
                }

                /* Log the action */
                Logger::users($registered_user['user_id'], 'register.success');

                /* Set a nice success message */
                Alerts::add_success(sprintf(l('global.success_message.create1'), '<strong>' . $_POST['name'] . '</strong>'));

                /* Redirect */
                redirect('admin/user-update/' . $registered_user['user_id']);
            }

        }

        /* Main View */
        $data = [
            'values' => $values
        ];

        $view = new \Altum\View('admin/user-create/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
