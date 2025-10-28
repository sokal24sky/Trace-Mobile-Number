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

class WebhookCryptoCom extends Controller {

    public function index() {

        /* Verify the source of the webhook event */
        $headers = getallheaders();
        $signature_header = isset($headers['Pay-Signature']) ? $headers['Pay-Signature'] : null;
        $payload = trim(@file_get_contents('php://input'));

        /* Make sure the signature is correct */
        $time_string = explode(',', $signature_header)[0];
        $time_value = explode('=', $time_string)[1];
        $signature_string = explode(',', $signature_header)[1];
        $signature_value = explode('=', $signature_string)[1];
        $signed_payload = $time_value . '.' . $payload;
        $computed_signature = \hash_hmac('sha256', $signed_payload, settings()->crypto_com->webhook_secret);

        if(!hash_equals($signature_value, $computed_signature)) {
            http_response_code(400); die();
        };

        $data = json_decode($payload);

        if($data->type == 'payment.captured') {

            /* Start getting the payment details */
            $payment_subscription_id = null;
            $external_payment_id = $data->id;
            $payment_total = $data->data->object->amount / 100;
            $payment_currency = $data->data->object->currency;
            $payment_type = 'one_time';

            /* Payment payer details */
            $payer_email = '';
            $payer_name = '';

            /* Process meta data */
            $metadata = $data->data->object->metadata;
            $user_id = (int) $metadata->user_id;
            $plan_id = (int) $metadata->plan_id;
            $payment_frequency = $metadata->payment_frequency;
            $code = isset($metadata->code) ? $metadata->code : '';
            $discount_amount = isset($metadata->discount_amount) ? $metadata->discount_amount : 0;
            $base_amount = isset($metadata->base_amount) ? $metadata->base_amount : 0;
            $taxes_ids = isset($metadata->taxes_ids) ? $metadata->taxes_ids : null;

            (new Payments())->webhook_process_payment(
                'crypto_com',
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
