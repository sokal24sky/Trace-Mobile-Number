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

defined('ALTUMCODE') || die();

class WebhookFlutterwave extends Controller {

    public function index() {

        if((strtoupper($_SERVER['REQUEST_METHOD']) != 'POST')) {
            die();
        }

        $payload = @file_get_contents('php://input');

        $data = json_decode($payload, true);

        if(!$data) {
            die('0');
        }

        if(!isset($data['status']) || !isset($data['id'])) {
            die('1');
        }

        if($data['status'] != 'successful') {
            die('2');
        }

        /* Get transaction data */
        $response = \Unirest\Request::get(
            'https://api.flutterwave.com/v3/transactions/' . $data['id'] . '/verify',
            [
                'Authorization' => 'Bearer ' . settings()->flutterwave->secret_key,
                'Content-Type' => 'application/json',
            ],
        );

        /* Check against errors */
        if($response->code >= 400) {
            http_response_code(400); die($response->body->message);
        }

        $payment = $response->body->data;

        if($response->body->status != 'success' || $payment->status != 'successful') {
            http_response_code(400); die('payment not successful');
        }

        /* Get payment data */
        $external_payment_id = $payment->id;
        $payment_subscription_id = null;

        /* Check if it's a subscription */
        if(isset($data['paymentPlan']) && !is_null($data['paymentPlan'])) {

            /* Get subscription data */
            $response = \Unirest\Request::get(
                'https://api.flutterwave.com/v3/subscriptions?transaction_id=' . $payment->id,
                [
                    'Authorization' => 'Bearer ' . settings()->flutterwave->secret_key,
                    'Content-Type' => 'application/json',
                ],
            );

            /* Check against errors */
            if($response->code >= 400) {
                http_response_code(400); die($response->body->message);
            }

            if(isset($response->body->data[0]) && $response->body->data[0]->status != 'cancelled') {
                $payment_subscription_id = $response->body->data[0]->id;
            }
        }

        /* Start getting the payment details */
        $payment_total = $payment->amount;
        $payment_currency = $payment->currency;
        $payment_type = $payment_subscription_id ? 'recurring' : 'one_time';

        /* Payment payer details */
        $payer_email = $payment->customer->name;
        $payer_name = $payment->customer->name;

        /* Process meta data */
        $metadata = $payment->meta;
        $user_id = (int) $metadata->user_id;
        $plan_id = (int) $metadata->plan_id;
        $payment_frequency = $metadata->payment_frequency;
        $code = isset($metadata->code) ? $metadata->code : '';
        $discount_amount = isset($metadata->discount_amount) ? $metadata->discount_amount : 0;
        $base_amount = isset($metadata->base_amount) ? $metadata->base_amount : 0;
        $taxes_ids = isset($metadata->taxes_ids) ? $metadata->taxes_ids : null;

        (new Payments())->webhook_process_payment(
            'flutterwave',
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

        echo 'successful';

    }

}
