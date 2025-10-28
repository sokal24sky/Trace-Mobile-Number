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

namespace Altum\controllers;

use Altum\Models\Payments;

defined('ALTUMCODE') || die();

class WebhookMyfatoorah extends Controller {

    public function index() {

        if((strtoupper($_SERVER['REQUEST_METHOD']) != 'POST')) {
            die();
        }

        $payload = @file_get_contents('php://input');

        $data = json_decode($payload);

        if(!$data) {
            die();
        }

        if(empty($data->EventType) || $data->EventType != 1) {
            die();
        }

        if($data->Data->TransactionStatus != 'SUCCESS') {
            die();
        }

        /* Verify the signature */
        if(settings()->myfatoraah->secret_key) {
            $headers = getallheaders();
            $signature = isset($headers['X-MyFatoorah-Signature']) ? $headers['X-MyFatoorah-Signature'] : '';
            $computed_hash = hash_hmac('sha256', $payload, settings()->myfatoraah->secret_key);
            if(!hash_equals($computed_hash, $signature)) {
                die('Invalid signature');
            }
        }

        /* Get payment data */
        $external_payment_id = $data->Data->InvoiceId;

        $payment_subscription_id = false;

        /* Start getting the payment details */
        $payment_total = $data->Data->InvoiceValueInPayCurrency;
        $payment_currency = $data->Data->PayCurrency;
        $payment_type = $payment_subscription_id ? 'recurring' : 'one_time';

        /* Payment payer details */
        $payer_email = $data->Data->CustomerEmail;
        $payer_name = $data->Data->CustomerName;

        /* Process meta data */
        $metadata = explode('&', $data->Data->CustomerReference);
        $user_id = (int) $metadata[0];
        $plan_id = (int) $metadata[1];
        $payment_frequency = $metadata[2];
        $base_amount = $metadata[3];
        $code = $metadata[4];
        $discount_amount = $metadata[5] ? $metadata[5] : 0;
        $taxes_ids = $metadata[6] ?: null;

        (new Payments())->webhook_process_payment(
            'myfatoorah',
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
