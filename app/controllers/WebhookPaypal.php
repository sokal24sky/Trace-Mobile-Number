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

class WebhookPaypal extends Controller {

    public function index() {

        $payload = @file_get_contents('php://input');
        $data = json_decode($payload);

        if($payload && $data) {

            try {
                $paypal_api_url = \Altum\PaymentGateways\Paypal::get_api_url();
                $headers = \Altum\PaymentGateways\Paypal::get_headers();
            } catch (\Exception $exception) {
                if(DEBUG) {
                    error_log($exception->getMessage());
                }
                echo $exception->getMessage();
                http_response_code(400); die();
            }

            /* Approve one time payment order and process it */
            if($data->event_type == 'CHECKOUT.ORDER.APPROVED') {
                $response = \Unirest\Request::post($paypal_api_url . 'v2/checkout/orders/' . $data->resource->id . '/capture', $headers);

                /* Check against errors */
                if($response->code >= 400) {
                    if(DEBUG) {
                        error_log($response->body->name . ':' . $response->body->message);
                    }
                    echo $response->body->name . ':' . $response->body->message;
                    http_response_code(400); die();
                }

                /* Start getting the payment details */
                $payment_subscription_id = null;
                $external_payment_id = $response->body->id;
                $payment_total = $response->body->purchase_units[0]->payments->captures[0]->amount->value;
                $payment_currency = $response->body->purchase_units[0]->payments->captures[0]->amount->currency_code;
                $payment_type = 'one_time';

                /* Payment payer details */
                $payer_email = $response->body->payer->email_address;
                $payer_name = $response->body->payer->name->given_name . $response->body->payer->name->surname;

                /* Parse metadata */
                $metadata = explode('&', $response->body->purchase_units[0]->payments->captures[0]->custom_id);
                $user_id = (int) $metadata[0];
                $plan_id = (int) $metadata[1];
                $payment_frequency = $metadata[2];
                $base_amount = $metadata[3];
                $code = $metadata[4];
                $discount_amount = $metadata[5] ? $metadata[5] : 0;
                $taxes_ids = $metadata[6] ?: null;

                (new Payments())->webhook_process_payment(
                    'paypal',
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

            /* Handle received payments by subscriptions */
            if($data->event_type == 'PAYMENT.SALE.COMPLETED') {

                $response = \Unirest\Request::get($paypal_api_url . 'v1/billing/subscriptions/' . $data->resource->billing_agreement_id . '?fields=plan', $headers);

                /* Check against errors */
                if($response->code >= 400) {
                    if(DEBUG) {
                        error_log($response->body->name . ':' . $response->body->message);
                    }
                    echo $response->body->name . ':' . $response->body->message;
                    http_response_code(400); die();
                }

                /* Start getting the payment details */
                $external_payment_id = $data->resource->id;
                $payment_total = $data->resource->amount->total;
                $payment_currency = $data->resource->amount->currency;
                $payment_type = 'recurring';
                $payment_subscription_id = $data->resource->billing_agreement_id;

                /* Payment payer details */
                $payer_email = $response->body->subscriber->email_address;
                $payer_name = $response->body->subscriber->name->given_name . $response->body->subscriber->name->surname;

                if(isset($response->body->custom_id)) {
                    /* Parse metadata */
                    $metadata = explode('&', $response->body->custom_id);
                    $user_id = (int) $metadata[0];
                    $plan_id = (int) $metadata[1];
                    $payment_frequency = $metadata[2];
                    $base_amount = $metadata[3];
                    $code = $metadata[4];
                    $discount_amount = $metadata[5] ? $metadata[5] : 0;
                    $taxes_ids = $metadata[6] ?: null;
                } else {

                    /* Check for old subscriptions meta data */
                    $extra = explode('###', $response->body->plan->name);

                    if(isset($extra[0], $extra[1], $extra[2])) {
                        $user_id = (int) $extra[0];
                        $plan_id = (int) $extra[1];
                        $payment_frequency = $extra[2];
                        $code = $extra[3];
                        $discount_amount = 0;
                        $base_amount = 0;
                    } else {
                        $extra = explode('!!', $response->body->plan->name);

                        $user_id = (int) $extra[0];
                        $plan_id = (int) $extra[1];
                        $base_amount = $extra[2];
                        $payment_frequency = $extra[3];
                        $code = $extra[4];
                        $discount_amount = $extra[5] ? $extra[5] : 0;
                        $taxes_ids = $extra[6];
                    }
                }

                (new Payments())->webhook_process_payment(
                    'paypal',
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

        }

        die('');

    }

}
