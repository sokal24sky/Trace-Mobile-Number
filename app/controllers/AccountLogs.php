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


defined('ALTUMCODE') || die();

class AccountLogs extends Controller {

    public function index() {

        \Altum\Authentication::guard();

        /* Prepare the filtering system */
        $filters = (new \Altum\Filters(['user_id', 'device_type', 'country_code', 'continent_code'], ['ip', 'city_name', 'os_name', 'browser_name', 'browser_name'], ['id', 'datetime']));
        $filters->set_default_order_by('id', $this->user->preferences->default_order_type ?? settings()->main->default_order_type);
        $filters->set_default_results_per_page($this->user->preferences->default_results_per_page ?? settings()->main->default_results_per_page);

        /* Prepare the paginator */
        $total_rows = database()->query("SELECT COUNT(*) AS `total` FROM `users_logs` WHERE `user_id` = {$this->user->user_id} {$filters->get_sql_where()}")->fetch_object()->total ?? 0;
        $paginator = (new \Altum\Paginator($total_rows, $filters->get_results_per_page(), $_GET['page'] ?? 1, url('account-logs?' . $filters->get_get() . 'page=%d')));

        /* Get the logs list for the user */
        $logs = [];
        $logs_result = database()->query("SELECT * FROM `users_logs` WHERE `user_id` = {$this->user->user_id} {$filters->get_sql_where()} {$filters->get_sql_order_by()} {$paginator->get_sql_limit()}");
        while($row = $logs_result->fetch_object()) $logs[] = $row;

        /* Export handler */
        process_export_json($logs, ['id', 'user_id', 'type', 'ip', 'continent_code', 'country_code', 'city_name', 'device_type', 'os_name', 'browser_name', 'browser_name', 'datetime']);
        process_export_csv($logs, ['id', 'user_id', 'type', 'ip', 'continent_code', 'country_code', 'city_name', 'device_type', 'os_name', 'browser_name', 'browser_name', 'datetime']);

        /* Prepare the pagination view */
        $pagination = (new \Altum\View('partials/pagination', (array) $this))->run(['paginator' => $paginator]);

        /* Get the account header menu */
        $menu = new \Altum\View('partials/account_header_menu', (array) $this);
        $this->add_view_content('account_header_menu', $menu->run());

        /* Prepare the view */
        $data = [
            'logs' => $logs,
            'filters' => $filters,
            'pagination' => $pagination
        ];

        $view = new \Altum\View('account-logs/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
