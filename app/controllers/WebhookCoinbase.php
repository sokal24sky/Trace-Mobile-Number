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
use Altum\PaymentGateways\Coinbase;

defined('ALTUMCODE') || die();

class WebhookCoinbase extends Controller {

    public function index() {

        /* Verify the source of the webhook event */
        $headers = getallheaders();
        $signature_header = isset($headers['X-Cc-Webhook-Signature']) ? $headers['X-Cc-Webhook-Signature'] : null;
        $payload = trim(@file_get_contents('php://input'));

        try {
            $data = Coinbase::verify_webhook_signature($payload, $signature_header);
        } catch (\Exception $exception) {
            if(DEBUG) {
                error_log($exception->getMessage());
            }
            echo $exception->getMessage();
            http_response_code(400); die();
        }

        if($data->event->type == 'charge:confirmed') {

            /* Start getting the payment details */
            $payment_subscription_id = null;
            $external_payment_id = $data->event->data->id;
            $payment_total = $data->event->data->pricing->local->amount;
            $payment_currency = $data->event->data->pricing->local->currency;
            $payment_type = 'one_time';

            /* Payment payer details */
            $payer_email = '';
            $payer_name = '';

            /* Process meta data */
            $metadata = $data->event->data->metadata;
            $user_id = (int) $metadata->user_id;
            $plan_id = (int) $metadata->plan_id;
            $payment_frequency = $metadata->payment_frequency;
            $code = isset($metadata->code) ? $metadata->code : '';
            $discount_amount = isset($metadata->discount_amount) ? $metadata->discount_amount : 0;
            $base_amount = isset($metadata->base_amount) ? $metadata->base_amount : 0;
            $taxes_ids = isset($metadata->taxes_ids) ? $metadata->taxes_ids : null;

            (new Payments())->webhook_process_payment(
                'coinbase',
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
