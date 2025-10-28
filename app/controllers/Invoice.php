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

use Altum\Models\Plan;
use Altum\Title;

defined('ALTUMCODE') || die();

class Invoice extends Controller {

    public function index() {

        \Altum\Authentication::guard();

        $id = isset($this->params[0]) ? (int) $this->params[0] : null;

        /* Make sure the campaign exists and is accessible to the user */
        if(!$payment = db()->where('id', $id)->getOne('payments')) {
            redirect();
        }

        if($payment->user_id != $this->user->user_id) {
            redirect();
        }

        /* Try to see if we get details from the billing */
        $payment->billing = json_decode($payment->billing ?? '');
        $payment->business = json_decode($payment->business ?? '');
        $payment->plan = json_decode($payment->plan ?? '');

        /* Get the plan details */
        $payment->plan_db = (new Plan())->get_plan_by_id($payment->plan_id);

        /* Check for potential taxes */
        $payment_taxes = (new \Altum\Models\Plan())->get_plan_taxes_by_taxes_ids($payment->taxes_ids);

        /* Calculate the price if a discount was used */
        $payment->price = $payment->discount_amount ? $payment->base_amount - $payment->discount_amount : $payment->base_amount;

        /* Calculate taxes */
        if(!empty($payment_taxes)) {

            /* Check for the inclusives */
            $inclusive_taxes_total_percentage = 0;

            foreach($payment_taxes as $key => $row) {
                if($row->type == 'exclusive') continue;

                $inclusive_taxes_total_percentage += $row->value;
            }

            $total_inclusive_tax = $payment->price - ($payment->price / (1 + $inclusive_taxes_total_percentage / 100));

            $price_without_inclusive_taxes = $payment->price - $total_inclusive_tax;

            foreach($payment_taxes as $key => $row) {
                if($row->type == 'exclusive') continue;

                $percentage_of_total_inclusive_tax = $row->value ? $row->value * 100 / $inclusive_taxes_total_percentage : 0;

                $inclusive_tax = number_format($total_inclusive_tax * $percentage_of_total_inclusive_tax / 100, 2);

                $payment_taxes[$key]->amount = $inclusive_tax;
            }

            /* Check for the exclusives */
            foreach($payment_taxes as $key => $row) {

                if($row->type == 'inclusive') {
                    continue;
                }

                $exclusive_tax = number_format($row->value_type == 'percentage' ? $price_without_inclusive_taxes * ($row->value / 100) : $row->value, 2);

                $payment_taxes[$key]->amount = $exclusive_tax;

            }

        }

        /* Set a custom title */
        Title::set(sprintf(l('invoice.title'), $payment->business->invoice_nr_prefix . $payment->id));

        /* Prepare the view */
        $data = [
            'payment' => $payment,
            'payment_taxes' => $payment_taxes
        ];

        $view = new \Altum\View('invoice/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
