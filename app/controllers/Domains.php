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

class Domains extends Controller {

    public function index() {

        if(!settings()->links->domains_is_enabled) {
            redirect('not-found');
        }

        \Altum\Authentication::guard();

        /* Prepare the filtering system */
        $filters = (new \Altum\Filters(['user_id', 'is_enabled'], ['host'], ['domain_id', 'last_datetime', 'host', 'datetime']));
        $filters->set_default_order_by($this->user->preferences->domains_default_order_by, $this->user->preferences->default_order_type ?? settings()->main->default_order_type);
        $filters->set_default_results_per_page($this->user->preferences->default_results_per_page ?? settings()->main->default_results_per_page);

        /* Prepare the paginator */
        $total_rows = database()->query("SELECT COUNT(*) AS `total` FROM `domains` WHERE `user_id` = {$this->user->user_id} {$filters->get_sql_where()}")->fetch_object()->total ?? 0;
        $paginator = (new \Altum\Paginator($total_rows, $filters->get_results_per_page(), $_GET['page'] ?? 1, url('domains?' . $filters->get_get() . '&page=%d')));

        /* Get the domains list for the user */
        $domains = [];
        $domains_result = database()->query("SELECT * FROM `domains` WHERE `user_id` = {$this->user->user_id} {$filters->get_sql_where()} {$filters->get_sql_order_by()} {$paginator->get_sql_limit()}");
        while($row = $domains_result->fetch_object()) $domains[] = $row;

        /* Export handler */
        process_export_csv($domains, ['domain_id', 'user_id', 'scheme', 'host', 'custom_index_url', 'custom_not_found_url', 'is_enabled', 'last_datetime', 'datetime'], sprintf(l('domains.title')));
        process_export_json($domains, ['domain_id', 'user_id', 'scheme', 'host', 'custom_index_url', 'custom_not_found_url', 'is_enabled', 'last_datetime', 'datetime'], sprintf(l('domains.title')));

        /* Prepare the pagination view */
        $pagination = (new \Altum\View('partials/pagination', (array) $this))->run(['paginator' => $paginator]);

        /* Prepare the view */
        $data = [
            'domains' => $domains,
            'total_domains' => $total_rows,
            'pagination' => $pagination,
            'filters' => $filters,
        ];

        $view = new \Altum\View('domains/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function bulk() {

        if(!settings()->links->domains_is_enabled) {
            redirect('not-found');
        }

        \Altum\Authentication::guard();

        /* Check for any errors */
        if(empty($_POST)) {
            redirect('domains');
        }

        if(empty($_POST['selected'])) {
            redirect('domains');
        }

        if(!isset($_POST['type'])) {
            redirect('domains');
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

                    /* Team checks */
                    if(\Altum\Teams::is_delegated() && !\Altum\Teams::has_access('delete.domains')) {
                        Alerts::add_info(l('global.info_message.team_no_access'));
                        redirect('domains');
                    }

                    foreach($_POST['selected'] as $domain_id) {
                        if($domain = db()->where('domain_id', $domain_id)->where('user_id', $this->user->user_id)->getOne('domains', ['domain_id'])) {
                            (new Domain())->delete($domain_id);
                        }
                    }

                    break;
            }

            session_start();

            /* Set a nice success message */
            Alerts::add_success(l('bulk_delete_modal.success_message'));

        }

        redirect('domains');
    }

    public function delete() {

        if(!settings()->links->domains_is_enabled) {
            redirect('not-found');
        }

        \Altum\Authentication::guard();

        /* Team checks */
        if(\Altum\Teams::is_delegated() && !\Altum\Teams::has_access('delete.domains')) {
            Alerts::add_info(l('global.info_message.team_no_access'));
            redirect('domains');
        }

        if(empty($_POST)) {
            redirect('domains');
        }

        $domain_id = (int) query_clean($_POST['domain_id']);

        //ALTUMCODE:DEMO if(DEMO) if($this->user->user_id == 1) Alerts::add_error('Please create an account on the demo to test out this function.');

        if(!\Altum\Csrf::check()) {
            Alerts::add_error(l('global.error_message.invalid_csrf_token'));
        }

        if(!$domain = db()->where('domain_id', $domain_id)->where('user_id', $this->user->user_id)->getOne('domains', ['domain_id', 'host'])) {
            redirect('domains');
        }

        if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

            (new Domain())->delete($domain->domain_id);

            /* Set a nice success message */
            Alerts::add_success(sprintf(l('global.success_message.delete1'), '<strong>' . $domain->host . '</strong>'));

            redirect('domains');
        }

        redirect('domains');
    }
}
