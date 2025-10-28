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

class PayThankYou extends Controller {

    public function index() {

        \Altum\Authentication::guard();

        if(!settings()->payment->is_enabled) {
            redirect('not-found');
        }

        $plan_id = $_GET['plan_id'] ?? null;

        /* Make sure it is either the trial / free plan or normal plans */
        switch($plan_id) {

            case 'free':

                /* Get the current settings for the free plan */
                $plan = settings()->plan_free;

                break;

            default:

                $plan_id = (int) $plan_id;

                /* Check if plan exists */
                if(!$plan = (new \Altum\Models\Plan())->get_plan_by_id($plan_id)) {
                    redirect('plan');
                }

                break;
        }

        /* Make sure the plan is enabled */
        if(!$plan->status) {
            redirect('plan');
        }

        /* Extra safety */
        $thank_you_url_parameters_raw = array_filter($_GET, function($key) {
            return !in_array($key, ['altum', 'unique_transaction_identifier']);
        }, ARRAY_FILTER_USE_KEY);


        $thank_you_url_parameters = '';
        foreach($thank_you_url_parameters_raw as $key => $value) {
            $thank_you_url_parameters .= '&' . $key . '=' . $value;
        }

        $unique_transaction_identifier = md5(\Altum\Date::get('', 4) . $thank_you_url_parameters);

        if($_GET['unique_transaction_identifier'] != $unique_transaction_identifier) {
            redirect('plan');
        }

        /* Flutterwave handle failed payments */
        if($_GET['payment_processor'] == 'flutterwave' && $_GET['status'] != 'successful') {
            redirect('pay/' . $_GET['plan_id'] . '?return_type=cancel&payment_processor=flutterwave');
        }
        
        /* Custom payment redirect */
        if($plan_id !== 'free' && $plan->settings->custom_redirect_url) {
            header('Location: ' . $plan->settings->custom_redirect_url);
        }

        /* Prepare the view */
        $data = [
            'plan_id'    => $plan_id,
            'plan'       => $plan,
        ];

        $view = new \Altum\View('pay-thank-you/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
