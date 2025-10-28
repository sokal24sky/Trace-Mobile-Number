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

use Altum\Response;
use Altum\Traits\Apiable;

defined('ALTUMCODE') || die();

class AdminApiPayments extends Controller {
    use Apiable;

    public function index() {

        $this->verify_request(true);

        /* Decide what to continue with */
        switch($_SERVER['REQUEST_METHOD']) {
            case 'GET':

                /* Detect if we only need an object, or the whole list */
                if(isset($this->params[0])) {
                    $this->get();
                } else {
                    $this->get_all();
                }

                break;
        }

        $this->return_404();
    }

    private function get_all() {

        /* Prepare the filtering system */
        $filters = (new \Altum\Filters([], [], []));
        $filters->set_default_order_by('id', $this->api_user->preferences->default_order_type ?? settings()->main->default_order_type);
        $filters->set_default_results_per_page($this->api_user->preferences->default_results_per_page ?? settings()->main->default_results_per_page);
        $filters->process();

        /* Prepare the paginator */
        $total_rows = database()->query("SELECT COUNT(*) AS `total` FROM `payments`")->fetch_object()->total ?? 0;
        $paginator = (new \Altum\Paginator($total_rows, $filters->get_results_per_page(), $_GET['page'] ?? 1, url('admin-api/payments?' . $filters->get_get() . '&page=%d')));

        /* Get the data */
        $data = [];
        $data_result = database()->query("
            SELECT
                *
            FROM
                `payments`
            WHERE
                1 = 1
                {$filters->get_sql_where()}
                {$filters->get_sql_order_by()}
                  
            {$paginator->get_sql_limit()}
        ");
        while($row = $data_result->fetch_object()) {

            /* Prepare the data */
            $row = [
                'id' => (int) $row->id,
                'user_id' => (int) $row->user_id,
                'plan_id' => (int) $row->plan_id,
                'payment_id' => $row->payment_id,
                'base_amount' => (float) $row->base_amount,
                'processor' => $row->processor,
                'type' => $row->type,
                'frequency' => $row->frequency,
                'code' => $row->code,
                'discount_amount' => (float) $row->discount_amount,
                'email' => $row->email,
                'name' => $row->name,
                'plan' => json_decode($row->plan ?? ''),
                'business' => json_decode($row->business ?? ''),
                'billing' => json_decode($row->billing ?? ''),
                'taxes_ids' => json_decode($row->taxes_ids ?? ''),
                'total_amount' => $row->total_amount,
                'total_amount_default_currency' => $row->total_amount_default_currency,
                'currency' => $row->currency,
                'payment_proof' => $row->payment_proof,
                'payment_proof_url' => \Altum\Uploads::get_full_url('offline_payment_proofs') . $row->payment_proof_url,
                'status' => (bool) (int) $row->status,
                'datetime' => $row->datetime,
            ];

            $data[] = $row;
        }

        /* Prepare the data */
        $meta = [
            'page' => $_GET['page'] ?? 1,
            'total_pages' => $paginator->getNumPages(),
            'results_per_page' => $filters->get_results_per_page(),
            'total_results' => (int) $total_rows,
        ];

        /* Prepare the pagination links */
        $others = ['links' => [
            'first' => $paginator->getPageUrl(1),
            'last' => $paginator->getNumPages() ? $paginator->getPageUrl($paginator->getNumPages()) : null,
            'next' => $paginator->getNextUrl(),
            'prev' => $paginator->getPrevUrl(),
            'self' => $paginator->getPageUrl($_GET['page'] ?? 1)
        ]];

        Response::jsonapi_success($data, $meta, 200, $others);
    }

    private function get() {

        $id = isset($this->params[0]) ? (int) $this->params[0] : null;

        /* Try to get details about the resource id */
        $payment = db()->where('id', $id)->getOne('payments');

        /* We haven't found the resource */
        if(!$payment) {
            $this->return_404();
        }

        /* Prepare the data */
        $data = [
            'id' => (int) $payment->id,
            'user_id' => (int) $payment->user_id,
            'plan_id' => (int) $payment->plan_id,
            'payment_id' => $payment->payment_id,
            'base_amount' => (float) $payment->base_amount,
            'processor' => $payment->processor,
            'type' => $payment->type,
            'frequency' => $payment->frequency,
            'code' => $payment->code,
            'discount_amount' => (float) $payment->discount_amount,
            'email' => $payment->email,
            'name' => $payment->name,
            'plan' => json_decode($payment->plan ?? ''),
            'business' => json_decode($payment->business ?? ''),
            'billing' => json_decode($payment->billing ?? ''),
            'taxes_ids' => json_decode($payment->taxes_ids ?? ''),
            'total_amount' => $payment->total_amount,
            'total_amount_default_currency' => $payment->total_amount_default_currency,
            'currency' => $payment->currency,
            'payment_proof' => $payment->payment_proof,
            'payment_proof_url' => \Altum\Uploads::get_full_url('offline_payment_proofs') . $payment->payment_proof_url,
            'status' => (bool) (int) $payment->status,
            'datetime' => $payment->datetime,
        ];

        Response::jsonapi_success($data);

    }

}
