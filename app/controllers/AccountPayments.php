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

class AccountPayments extends Controller {

    public function index() {

        \Altum\Authentication::guard();

        if(!settings()->payment->is_enabled) {
            redirect('not-found');
        }

        $payment_processors = require APP_PATH . 'includes/payment_processors.php';

        /* Prepare the filtering system */
        $filters = (new \Altum\Filters(['processor', 'type', 'frequency'], [], ['id', 'total_amount', 'datetime']));
        $filters->set_default_order_by('id', $this->user->preferences->default_order_type ?? settings()->main->default_order_type);
        $filters->set_default_results_per_page($this->user->preferences->default_results_per_page ?? settings()->main->default_results_per_page);

        /* Prepare the paginator */
        $total_rows = database()->query("SELECT COUNT(*) AS `total` FROM `payments` WHERE `user_id` = {$this->user->user_id} {$filters->get_sql_where()}")->fetch_object()->total ?? 0;
        $paginator = (new \Altum\Paginator($total_rows, $filters->get_results_per_page(), $_GET['page'] ?? 1, url('account-payments?' . $filters->get_get() . '&page=%d')));

        /* Get the payments list for the user */
        $payments = [];
        $payments_result = database()->query("SELECT `payments`.*, `plans`.`name`  AS `plan_name`, `plans`.`translations` FROM `payments` LEFT JOIN plans ON `payments`.plan_id = plans.plan_id WHERE `user_id` = {$this->user->user_id} {$filters->get_sql_where('payments')} {$filters->get_sql_order_by('payments')} {$paginator->get_sql_limit()}");
        while($row = $payments_result->fetch_object()) {
            $row->translations = json_decode($row->translations ?? '');
            $payments[] = $row;
        }

        /* Export handler */
        process_export_json($payments, ['id', 'user_id', 'plan_id', 'payment_id', 'email', 'name', 'processor', 'type', 'frequency', 'billing', 'taxes_ids', 'base_amount', 'code', 'discount_amount', 'total_amount', 'currency', 'status', 'datetime']);
        process_export_csv($payments, ['id', 'user_id', 'plan_id', 'payment_id', 'email', 'name', 'processor', 'type', 'frequency', 'base_amount', 'code', 'discount_amount', 'total_amount', 'currency', 'status', 'datetime']);

        /* Prepare the pagination view */
        $pagination = (new \Altum\View('partials/pagination', (array) $this))->run(['paginator' => $paginator]);

        /* Get the account header menu */
        $menu = new \Altum\View('partials/account_header_menu', (array) $this);
        $this->add_view_content('account_header_menu', $menu->run());

        /* Prepare the view */
        $data = [
            'payments' => $payments,
            'pagination' => $pagination,
            'filters' => $filters,
            'payment_processors' => $payment_processors,
        ];

        $view = new \Altum\View('account-payments/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }


}
