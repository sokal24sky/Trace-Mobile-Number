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

class WebhookLemonsqueezy extends Controller {

    public function index() {

        /* Verify the source of the webhook event */
        $headers = getallheaders();
        $signature = isset($headers['X-Signature']) ? $headers['X-Signature'] : null;
        $payload = trim(@file_get_contents('php://input'));

        /* Make sure the signature is correct */
        if(!$signature || !hash_equals(hash_hmac('sha256', $payload, settings()->lemonsqueezy->signing_secret), $signature)) {
            http_response_code(400); die();
        }

        /* Parse payload */
        $data = json_decode($payload);

        $event_type = $data->meta->event_name;

        /* One time payments */
        if($event_type == 'order_created' || $event_type == 'subscription_payment_success') {

            /* Start getting the payment details */
            $payment_subscription_id = $event_type == 'subscription_payment_success' ? $data->data->attributes->subscription_id : null;
            $external_payment_id = $data->data->id;
            $payment_total = $data->data->attributes->total / 100;
            $payment_currency = $data->data->attributes->currency;
            $payment_type = $event_type == 'subscription_payment_success' ? 'recurring' : 'one_time';

            /* Payment payer details */
            $payer_email = $data->data->attributes->user_email;
            $payer_name = $data->data->attributes->user_name;

            /* Process meta data */
            $metadata = $data->meta->custom_data;
            $user_id = (int) $metadata->user_id;
            $plan_id = (int) $metadata->plan_id;
            $payment_frequency = $metadata->payment_frequency;
            $code = isset($metadata->code) && $metadata->code != 'null' ? $metadata->code : '';
            $discount_amount = isset($metadata->discount_amount) ? $metadata->discount_amount : 0;
            $base_amount = isset($metadata->base_amount) ? $metadata->base_amount : 0;
            $taxes_ids = isset($metadata->taxes_ids) ? $metadata->taxes_ids : null;

            (new Payments())->webhook_process_payment(
                'lemonsqueezy',
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

        die();

    }

}
