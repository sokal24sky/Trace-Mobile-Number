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

class WebhookMidtrans extends Controller {

    public function index() {

        if((strtoupper($_SERVER['REQUEST_METHOD']) != 'POST')) {
            die();
        }

        $payload = @file_get_contents('php://input');

        $data = json_decode($payload, true);

        if(!$data) {
            die('0');
        }

        if(!in_array($data['transaction_status'], ['capture', 'settlement'])) {
            die('1');
        }

        if(isset($data['fraud_status']) && $data['fraud_status'] != 'accept') {
            die('2');
        }

        if($data['signature_key'] !== hash('sha512', $data['order_id'] . $data['status_code'] . $data['gross_amount'] . settings()->midtrans->server_key)) {
            die('3');
        }

        /* Get payment data */
        $external_payment_id = $data['transaction_id'];
        $payment_subscription_id = null;

        /* Start getting the payment details */
        $payment_total = $data['gross_amount'];
        $payment_currency = $data['currency'];
        $payment_type = $payment_subscription_id ? 'recurring' : 'one_time';

        /* Payment payer details */
        $payer_email = '';
        $payer_name = '';

        /* Process meta data */
        $metadata = explode('&', $data['custom_field1']);
        $user_id = (int) $metadata[0];
        $plan_id = (int) $metadata[1];
        $payment_frequency = $metadata[2];
        $base_amount = $metadata[3];
        $code = $metadata[4];
        $discount_amount = $metadata[5] ? $metadata[5] : 0;
        $taxes_ids = $metadata[6] ?: null;

        (new Payments())->webhook_process_payment(
            'midtrans',
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
