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

class WebhookPaddle extends Controller {

    public function index() {

        if(empty($_POST)) {
            die();
        }

        $public_key = openssl_get_publickey(settings()->paddle->public_key);

        /* Get the p_signature parameter & base64 decode it. */
        $signature = base64_decode($_POST['p_signature']);

        /* Get the fields sent in the request, and remove the p_signature parameter */
        $fields = $_POST;
        unset($fields['p_signature']);

        /* ksort() and serialize the fields */
        ksort($fields);
        foreach($fields as $k => $v) {
            if(!in_array(gettype($v), array('object', 'array'))) {
                $fields[$k] = "$v";
            }
        }
        $data = serialize($fields);

        /* Verify the signature */
        $verification = openssl_verify($data, $signature, $public_key, OPENSSL_ALGO_SHA1);

        if(!$verification) {
            die('Invalid signature verification.');
        }


        /* Start getting the payment details */
        $payment_subscription_id = null;
        $external_payment_id = $_POST['p_order_id'];
        $payment_total = $_POST['p_sale_gross'];
        $payment_currency = $_POST['p_currency'];
        $payment_type = 'one_time';

        /* Payment payer details */
        $payer_email = $_POST['p_customer_email'];
        $payer_name = $_POST['p_customer_name'];

        /* Parse metadata */
        $metadata = explode('&', $_POST['passthrough']);
        $user_id = (int) $metadata[0];
        $plan_id = (int) $metadata[1];
        $payment_frequency = $metadata[2];
        $base_amount = $metadata[3];
        $code = $metadata[4];
        $discount_amount = $metadata[5] ? $metadata[5] : 0;
        $taxes_ids = $metadata[6] ?: null;

        (new Payments())->webhook_process_payment(
            'paddle',
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
