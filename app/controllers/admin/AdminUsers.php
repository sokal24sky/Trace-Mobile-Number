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
use Altum\Models\Plan;
use Altum\Models\User;

defined('ALTUMCODE') || die();

class AdminUsers extends Controller {

    public function index() {

        /* Prepare the filtering system */
        $filters = (new \Altum\Filters(['status', 'source', 'plan_id', 'device_type', 'country', 'continent_code', 'type', 'referred_by', 'is_newsletter_subscribed', 'language'], ['name', 'email', 'city_name', 'os_name', 'browser_name', 'browser_language'], ['user_id', 'email', 'datetime', 'last_activity', 'name', 'total_logins', 'plan_expiration_date']));
        $filters->set_default_order_by('user_id', $this->user->preferences->default_order_type ?? settings()->main->default_order_type);
        $filters->set_default_results_per_page($this->user->preferences->default_results_per_page ?? settings()->main->default_results_per_page);

        /* Prepare the paginator */
        $total_rows = database()->query("SELECT COUNT(*) AS `total` FROM `users` WHERE 1 = 1 {$filters->get_sql_where()}")->fetch_object()->total ?? 0;
        $paginator = (new \Altum\Paginator($total_rows, $filters->get_results_per_page(), $_GET['page'] ?? 1, url('admin/users?' . $filters->get_get() . '&page=%d')));

        /* Get the data */
        $users = [];
        $users_result = database()->query("
            SELECT
                *
            FROM
                `users`
            WHERE
                1 = 1
                {$filters->get_sql_where()}
                {$filters->get_sql_order_by()}
                  
            {$paginator->get_sql_limit()}
        ");
        while($row = $users_result->fetch_object()) {
            $users[] = $row;
        }

        /* Export handler */
        process_export_json($users, ['user_id', 'email', 'name', 'billing', 'plan_id', 'plan_settings', 'plan_expiration_date', 'plan_trial_done', 'status', 'source', 'language', 'timezone', 'continent_code', 'country', 'city_name', 'datetime', 'next_cleanup_datetime', 'last_activity', 'total_logins']);
        process_export_csv($users, ['user_id', 'email', 'name', 'plan_id', 'plan_expiration_date', 'plan_trial_done', 'status', 'source', 'language', 'timezone', 'continent_code', 'country', 'city_name', 'datetime', 'next_cleanup_datetime', 'last_activity', 'total_logins']);

        /* Requested plan details */
        $plans = (new \Altum\Models\Plan())->get_plans();
        $plans['free'] = (new Plan())->get_plan_by_id('free');
        $plans['custom'] = (new Plan())->get_plan_by_id('custom');

        /* Prepare the pagination view */
        $pagination = (new \Altum\View('partials/admin_pagination', (array) $this))->run(['paginator' => $paginator]);

        /* Main View */
        $data = [
            'users' => $users,
            'plans' => $plans,
            'paginator' => $paginator,
            'pagination' => $pagination,
            'filters' => $filters
        ];

        $view = new \Altum\View('admin/users/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function login() {

        $user_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

        if(!\Altum\Csrf::check('global_token')) {
            Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            redirect('admin/users');
        }

        if($user_id == $this->user->user_id) {
            redirect('admin/users');
        }

        /* Check if resource exists */
        if(!$user = db()->where('user_id', $user_id)->getOne('users')) {
            redirect('admin/users');
        }

        if($user->status != 1) {
            Alerts::add_error(l('admin_user_login_modal.error_message.disabled'));
            redirect('admin/users');
        }

        if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

            /* Logout of the admin */
            \Altum\Authentication::logout(false);

            /* Login as the new user */
            session_start();
            $_SESSION['user_id'] = $user->user_id;
            $_SESSION['user_password_hash'] = md5($user->password);

            /* Tell the script that we're actually logged in as an admin in the background */
            $_SESSION['admin_user_id'] = $this->user->user_id;

            /* Set a nice success message */
            Alerts::add_success(sprintf(l('admin_user_login_modal.success_message'), $user->name));

            redirect('dashboard');

        }

        redirect('admin/users');
    }

    public function bulk() {

        /* Check for any errors */
        if(empty($_POST)) {
            redirect('admin/users');
        }

        if(empty($_POST['selected'])) {
            redirect('admin/users');
        }

        if(!isset($_POST['type'])) {
            redirect('admin/users');
        }

        //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

        if(!\Altum\Csrf::check()) {
            Alerts::add_error(l('global.error_message.invalid_csrf_token'));
        }

        if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

            set_time_limit(0);

            session_write_close();

            switch($_POST['type']) {
                case 'delete':

                    foreach($_POST['selected'] as $user_id) {
                        /* Do not allow self-deletion */
                        if($user_id == $this->user->user_id) {
                            continue;
                        }

                        (new User())->delete((int) $user_id);
                    }

                    session_start();

                    /* Set a nice success message */
                    Alerts::add_success(l('bulk_delete_modal.success_message'));

                    break;

                case 'resend_activation':

                    $total = 0;

                    foreach($_POST['selected'] as $user_id) {
                        $user = db()->where('user_id', $user_id)->getOne('users', ['user_id', 'status', 'name', 'email', 'language']);

                        if($user && !$user->status) {
                            /* Generate new email code */
                            $email_code = md5($user->email . microtime());

                            /* Update the current activation email */
                            db()->where('user_id', $user->user_id)->update('users', ['email_activation_code' => $email_code]);

                            /* Prepare the email */
                            $email_template = get_email_template(
                                [
                                    '{{NAME}}' => $user->name,
                                ],
                                l('global.emails.user_activation.subject', $user->language),
                                [
                                    '{{ACTIVATION_LINK}}' => url('activate-user?email=' . md5($user->email) . '&email_activation_code=' . $email_code . '&type=user_activation'),
                                    '{{NAME}}' => $user->name,
                                ],
                                l('global.emails.user_activation.body', $user->language)
                            );

                            /* Send the email */
                            send_mail($user->email, $email_template->subject, $email_template->body);

                            Logger::users($user->user_id, 'resend_activation.request_sent');

                            $total++;
                        }
                    }

                    session_start();

                    /* Set a nice success message */
                    Alerts::add_success(sprintf(l('admin_users_bulk_resend_activation_modal.success_message'), nr($total)));

                    break;
            }

        }

        redirect('admin/users');
    }

    public function delete() {

        $user_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

        if(!\Altum\Csrf::check('global_token')) {
            Alerts::add_error(l('global.error_message.invalid_csrf_token'));
        }

        /* Do not allow self-deletion */
        if($user_id == $this->user->user_id) {
            Alerts::add_error(l('admin_users.error_message.self_delete'));
        }

        if(!$user = db()->where('user_id', $user_id)->getOne('users')) {
            redirect('admin/users');
        }

        if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

            /* Delete the user */
            (new User())->delete($user_id);

            /* Set a nice success message */
            Alerts::add_success(sprintf(l('global.success_message.delete1'), '<strong>' . $user->name . '</strong>'));

        }

        redirect('admin/users');
    }

}
