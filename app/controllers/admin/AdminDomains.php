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
use Altum\Models\Domain;

defined('ALTUMCODE') || die();

class AdminDomains extends Controller {

    public function index() {

        /* Prepare the filtering system */
        $filters = (new \Altum\Filters(['is_enabled', 'user_id', 'type'], ['host'], ['domain_id', 'last_datetime', 'datetime', 'host']));
        $filters->set_default_order_by($this->user->preferences->domains_default_order_by, $this->user->preferences->default_order_type ?? settings()->main->default_order_type);
        $filters->set_default_results_per_page($this->user->preferences->default_results_per_page ?? settings()->main->default_results_per_page);

        /* Prepare the paginator */
        $total_rows = database()->query("SELECT COUNT(*) AS `total` FROM `domains` WHERE 1 = 1 {$filters->get_sql_where()}")->fetch_object()->total ?? 0;
        $paginator = (new \Altum\Paginator($total_rows, $filters->get_results_per_page(), $_GET['page'] ?? 1, url('admin/domains?' . $filters->get_get() . '&page=%d')));

        /* Get the data */
        $domains = [];
        $domains_result = database()->query("
            SELECT
                `domains`.*, `users`.`name` AS `user_name`, `users`.`email` AS `user_email`, `users`.`avatar` AS `user_avatar`
            FROM
                `domains`
            LEFT JOIN
                `users` ON `domains`.`user_id` = `users`.`user_id`
            WHERE
                1 = 1
                {$filters->get_sql_where('domains')}
                {$filters->get_sql_order_by('domains')}
            
            {$paginator->get_sql_limit()}
        ");
        while($row = $domains_result->fetch_object()) {
            $domains[] = $row;
        }

        /* Export handler */
        process_export_csv($domains, ['domain_id', 'user_id', 'scheme', 'host', 'custom_index_url', 'custom_not_found_url', 'is_enabled', 'last_datetime', 'datetime'], sprintf(l('admin_domains.title')));
        process_export_json($domains, ['domain_id', 'user_id', 'scheme', 'host', 'custom_index_url', 'custom_not_found_url', 'is_enabled', 'last_datetime', 'datetime'], sprintf(l('admin_domains.title')));

        /* Prepare the pagination view */
        $pagination = (new \Altum\View('partials/admin_pagination', (array) $this))->run(['paginator' => $paginator]);

        /* Main View */
        $data = [
            'domains' => $domains,
            'filters' => $filters,
            'pagination' => $pagination
        ];

        $view = new \Altum\View('admin/domains/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function bulk() {

        /* Check for any errors */
        if(empty($_POST)) {
            redirect('admin/domains');
        }

        if(empty($_POST['selected'])) {
            redirect('admin/domains');
        }

        if(!isset($_POST['type'])) {
            redirect('admin/domains');
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

                    foreach($_POST['selected'] as $domain_id) {
                        (new Domain())->delete($domain_id);
                    }

                    break;
            }

            session_start();

            /* Set a nice success message */
            Alerts::add_success(l('bulk_delete_modal.success_message'));

        }

        redirect('admin/domains');
    }

    public function delete() {

        $domain_id = (isset($this->params[0])) ? (int) $this->params[0] : null;

        //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

        if(!\Altum\Csrf::check('global_token')) {
            Alerts::add_error(l('global.error_message.invalid_csrf_token'));
        }

        if(!$domain = db()->where('domain_id', $domain_id)->getOne('domains', ['domain_id', 'host'])) {
            redirect('admin/domains');
        }

        if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

            (new Domain())->delete($domain->domain_id);

            /* Set a nice success message */
            Alerts::add_success(sprintf(l('global.success_message.delete1'), '<strong>' . $domain->host . '</strong>'));

        }

        redirect('admin/domains');
    }

}
