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

use Altum\Models\Payments;
use Altum\PaymentGateways\Paystack;

defined('ALTUMCODE') || die();

class WebhookPaystack extends Controller {

    public function index() {

        if((strtoupper($_SERVER['REQUEST_METHOD']) != 'POST' ) || !isset($_SERVER['HTTP_X_PAYSTACK_SIGNATURE'])) {
            die();
        }

        $payload = @file_get_contents('php://input');

        if($_SERVER['HTTP_X_PAYSTACK_SIGNATURE'] !== hash_hmac('sha512', $payload, settings()->paystack->secret_key)) {
            die();
        }

        $data = json_decode($payload);

        if(!$data) {
            die();
        }

        if($data->event == 'charge.success') {

            /* Get subscription details if needed */
            $payment_subscription_id = null;

            if(isset($data->data->plan->id)) {
                Paystack::$secret_key = settings()->paystack->secret_key;

                $response = \Unirest\Request::get(Paystack::$api_url . 'plan/' . $data->data->plan->id, Paystack::get_headers());

                if(!$response->body->status) {
                    if(DEBUG) {
                        die($response->body->message);
                    } else {
                        http_response_code(400); die();
                    }
                }

                $payment_subscription_id = $response->body->data->subscriptions[0]->subscription_code . '###' . $response->body->data->subscriptions[0]->email_token;
            }

            /* Start getting the payment details */
            $external_payment_id = $data->data->id;
            $payment_total = $data->data->amount / 100;
            $payment_currency = $data->data->currency;
            $payment_type = isset($data->data->plan->id) ? 'recurring' : 'one_time';

            /* Payment payer details */
            $payer_email = $data->data->customer->email;
            $payer_name = $data->data->customer->first_name . $data->data->customer->last_name;

            /* Process meta data */
            $metadata = $data->data->metadata;
            $user_id = (int) $metadata->user_id;
            $plan_id = (int) $metadata->plan_id;
            $payment_frequency = $metadata->payment_frequency;
            $code = isset($metadata->code) ? $metadata->code : '';
            $discount_amount = isset($metadata->discount_amount) ? $metadata->discount_amount : 0;
            $base_amount = isset($metadata->base_amount) ? $metadata->base_amount : 0;
            $taxes_ids = isset($metadata->taxes_ids) ? $metadata->taxes_ids : null;

            (new Payments())->webhook_process_payment(
                'paystack',
                $external_payment_id,
                $payment_total,
                $payment_currency,
                $user_id,
                $plan_id,
                $payment_frequency,
                $code,
                $discount_amount,
                $base_amount,
                $taxes_ids,
                $payment_type,
                $payment_subscription_id,
                $payer_email,
                $payer_name
            );

            die('successful');
        }

        die();

    }

}
