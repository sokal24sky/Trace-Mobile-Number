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

class AdminUsersLogs extends Controller {

    public function index() {

        /* Prepare the filtering system */
        $filters = (new \Altum\Filters(['user_id', 'device_type', 'country_code', 'continent_code'], ['ip', 'city_name', 'os_name', 'browser_name', 'browser_name'], ['id', 'datetime']));
        $filters->set_default_order_by('id', $this->user->preferences->default_order_type ?? settings()->main->default_order_type);
        $filters->set_default_results_per_page($this->user->preferences->default_results_per_page ?? settings()->main->default_results_per_page);

        /* Prepare the paginator */
        $total_rows = database()->query("SELECT COUNT(*) AS `total` FROM `users_logs` WHERE 1 = 1 {$filters->get_sql_where()}")->fetch_object()->total ?? 0;
        $paginator = (new \Altum\Paginator($total_rows, $filters->get_results_per_page(), $_GET['page'] ?? 1, url('admin/users-logs?' . $filters->get_get() . '&page=%d')));

        /* Get the data */
        $users_logs = [];
        $users_logs_result = database()->query("
            SELECT
                `users_logs`.*, `users`.`name` AS `user_name`, `users`.`email` AS `user_email`, `users`.`avatar` AS `user_avatar`
            FROM
                `users_logs`
            LEFT JOIN
                `users` ON `users_logs`.`user_id` = `users`.`user_id`
            WHERE
                1 = 1
                {$filters->get_sql_where('users_logs')}
                {$filters->get_sql_order_by('users_logs')}

            {$paginator->get_sql_limit()}
        ");
        while($row = $users_logs_result->fetch_object()) {
            $users_logs[] = $row;
        }

        /* Export handler */
        process_export_json($users_logs, ['user_id', 'type', 'ip', 'continent_code', 'country_code', 'city_name', 'device_type', 'os_name', 'browser_name', 'browser_name', 'datetime']);
        process_export_csv($users_logs, ['user_id', 'type', 'ip', 'continent_code', 'country_code', 'city_name', 'device_type', 'os_name', 'browser_name', 'browser_name', 'datetime']);

        /* Prepare the pagination view */
        $pagination = (new \Altum\View('partials/admin_pagination', (array) $this))->run(['paginator' => $paginator]);

        /* Main View */
        $data = [
            'users_logs' => $users_logs,
            'paginator' => $paginator,
            'pagination' => $pagination,
            'filters' => $filters
        ];

        $view = new \Altum\View('admin/users-logs/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function bulk() {

        /* Check for any errors */
        if(empty($_POST)) {
            redirect('admin/users-logs');
        }

        if(empty($_POST['selected'])) {
            redirect('admin/users-logs');
        }

        if(!isset($_POST['type'])) {
            redirect('admin/users-logs');
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

                    foreach($_POST['selected'] as $id) {
                        db()->where('id', $id)->delete('users_logs');
                    }
                    break;
            }

            session_start();

            /* Set a nice success message */
            Alerts::add_success(l('bulk_delete_modal.success_message'));

        }

        redirect('admin/users-logs');
    }

    public function delete() {

        $id = isset($this->params[0]) ? (int) $this->params[0] : null;

        //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

        if(!\Altum\Csrf::check('global_token')) {
            Alerts::add_error(l('global.error_message.invalid_csrf_token'));
        }

        if(!$user_log = db()->where('id', $id)->getOne('users_logs')) {
            redirect('admin/users-logs');
        }

        if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

            /* Delete the user log */
            db()->where('id', $id)->delete('users_logs');

            /* Set a nice success message */
            Alerts::add_success(l('global.success_message.delete2'));

        }

        redirect('admin/users-logs');
    }

}
