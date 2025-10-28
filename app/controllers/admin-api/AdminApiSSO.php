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

use Altum\Logger;
use Altum\Models\User;
use Altum\Response;
use Altum\Traits\Apiable;

defined('ALTUMCODE') || die();

class AdminApiSSO extends Controller {
    use Apiable;

    public function index() {

        $this->verify_request(true);

        /* Decide what to continue with */
        switch($_SERVER['REQUEST_METHOD']) {
            case 'POST':

                /* Detect what method to use */
                if(isset($this->params[0]) && $this->params[0] == 'login') {
                    $this->login();
                }

                if(isset($this->params[0]) && $this->params[0] == 'update') {
                    $this->update();
                }

                if(isset($this->params[0]) && $this->params[0] == 'delete') {
                    $this->delete();
                }

                break;
        }

        $this->return_404();

    }

    private function login() {

        $required_fields = ['email'];

        /* Check for any errors */
        foreach($required_fields as $field) {
            if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                $this->response_error(l('global.error_message.empty_fields'), 401);
                break 1;
            }
        }

        $_POST['email'] = input_clean_email($_POST['email'] ?? '');
        $redirect = isset($_POST['redirect']) ? query_clean($_POST['redirect']) : 'dashboard';

        /* Login the user */
        $user = db()->where('email', $_POST['email'])->getOne('users', ['user_id', 'email', 'datetime']);

        if($user) {
            $one_time_login_code = md5($user->email . $user->datetime . time());

            /* Database query */
            db()->where('user_id', $user->user_id)->update('users', ['one_time_login_code' => $one_time_login_code]);

            /* Clear the cache */
            cache()->deleteItemsByTag('user_id=' . $user->user_id);

            /* Prepare the data */
            $data = [
                'one_time_login_code' => $one_time_login_code,
                'url' => url('login/one-time-login-code/' . $one_time_login_code . '?redirect=' . $redirect),
                'id' => $user->user_id
            ];

            Response::jsonapi_success($data, null, 200);
        }

        /* Create the user */
        else {
            $_POST['name'] = input_clean_name($_POST['name'], 64);
            $_POST['email'] = input_clean_email($_POST['email'] ?? '');

            $registered_user = (new User())->create(
                $_POST['email'],
                $_POST['password'],
                $_POST['name'],
                1,
                'direct',
                null,
                null,
                false,
                'free',
                json_encode(settings()->plan_free->settings),
                null,
                settings()->main->default_timezone
            );

            /* Send webhook notification if needed */
            if(settings()->webhooks->user_new) {
                fire_and_forget('post', settings()->webhooks->user_new, [
                    'user_id' => $registered_user['user_id'],
                    'email' => $_POST['email'],
                    'name' => $_POST['name'],
                    'source' => 'direct',
                    'is_newsletter_subscribed' => false,
                    'datetime' => get_date(),
                ]);
            }

            $one_time_login_code = md5($registered_user['user_id'] . $registered_user['email'] . time());

            /* Database query */
            db()->where('user_id', $registered_user['user_id'])->update('users', ['one_time_login_code' => $one_time_login_code]);

            /* Prepare the data */
            $data = [
                'one_time_login_code' => $one_time_login_code,
                'url' => url('login/one-time-login-code/' . $one_time_login_code . '?redirect=' . $redirect),
                'id' => $registered_user['user_id']
            ];

            Response::jsonapi_success($data, null, 200);
        }

        die();
    }

    private function update() {

        $_POST['email'] = input_clean_email($_POST['email'] ?? '');

        /* Try to get details about the resource id */
        $user = db()->where('email', $_POST['email'])->getOne('users', ['user_id']);

        /* We haven't found the resource */
        if(!$user) {
            $this->return_404();
        }

        /* Potential variables */
        $to_update = [];

        if(isset($_POST['new_email'])) {
            $_POST['new_email'] = mb_substr(filter_var($_POST['new_email'], FILTER_SANITIZE_EMAIL), 0, 320);
            $to_update['email'] = $_POST['new_email'];
        }

        if(isset($_POST['name'])) {
            $_POST['name'] = input_clean_name($_POST['name'], 64);
            $to_update['name'] = $_POST['name'];
        }

        /* Database query */
        db()->where('user_id', $user->user_id)->update('users', $to_update);

        Logger::users($user->user_id, 'email_change.success');

        http_response_code(200);
        die();

    }

    private function delete() {

        $_POST['email'] = input_clean_email($_POST['email'] ?? '');

        /* Try to get details about the resource id */
        $user = db()->where('email', $_POST['email'])->getOne('users', ['user_id']);

        /* We haven't found the resource */
        if(!$user) {
            $this->return_404();
        }

        /* Delete the user */
        (new User())->delete($user->user_id);

        http_response_code(200);
        die();

    }

}
