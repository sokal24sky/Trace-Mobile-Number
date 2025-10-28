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

class WebhookStripe extends Controller {

    public function index() {

        /* Initiate Stripe */
        \Stripe\Stripe::setApiKey(settings()->stripe->secret_key);
        \Stripe\Stripe::setApiVersion('2023-10-16');

        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];

        try {
            $event = \Stripe\Webhook::constructEvent($payload, $sig_header, settings()->stripe->webhook_secret);
        } catch(\Exception $exception) {
            /* Invalid payload */
            echo $exception->getMessage(); http_response_code(400); die();
        }

        if(!in_array($event->type, ['invoice.paid', 'checkout.session.completed', 'customer.subscription.created'])) {
            die('Event type not needed to be handled, returning ok.');
        }

        $session = $event->data->object;
        $external_payment_id = $session->id;

        switch($event->type) {
            /* Handle trial start */
            case 'customer.subscription.created':

                /* Only run when the subscription starts with a trial */
                if($session->status === 'trialing') {

                    $metadata = $session->metadata;

                    $user_id = (int) $metadata->user_id;
                    $plan_id = (int) $metadata->plan_id;
                    $payment_frequency = $metadata->payment_frequency;
                    $code = isset($metadata->code) ? $metadata->code : '';
                    $discount_amount = isset($metadata->discount_amount) ? $metadata->discount_amount : 0;
                    $base_amount = isset($metadata->base_amount) ? $metadata->base_amount : 0;
                    $taxes_ids = isset($metadata->taxes_ids) ? $metadata->taxes_ids : null;

                    $payment_type = 'recurring';
                    $payment_subscription_id = $session->id;

                    /* Set to 0 since no payment yet */
                    $payer_email = null;
                    $payer_name = null;
                    $payment_currency = settings()->payment->default_currency;
                    $payment_total = 0;

                }

                break;

            /* Handling recurring payments */
            case 'invoice.paid':

                $payer_email = $session->customer_email;
                $payer_name = $session->customer_name;

                $payment_currency = mb_strtoupper($session->currency);
                $payment_total = in_array($payment_currency, ['MGA', 'BIF', 'CLP', 'PYG', 'DJF', 'RWF', 'GNF', 'UGX', 'JPY', 'VND', 'VUV', 'XAF', 'KMF', 'KRW', 'XOF', 'XPF']) ? $session->amount_paid : $session->amount_paid / 100;

                /* Process meta data */
                $metadata = $session->lines->data[0]->metadata;

                $user_id = (int) $metadata->user_id;
                $plan_id = (int) $metadata->plan_id;
                $payment_frequency = $metadata->payment_frequency;
                $code = isset($metadata->code) ? $metadata->code : '';
                $discount_amount = isset($metadata->discount_amount) ? $metadata->discount_amount : 0;
                $base_amount = isset($metadata->base_amount) ? $metadata->base_amount : 0;
                $taxes_ids = isset($metadata->taxes_ids) ? $metadata->taxes_ids : null;

                /* Vars */
                $payment_type = $session->subscription ? 'recurring' : 'one_time';
                $payment_subscription_id = $payment_type == 'recurring' ? $session->subscription : '';

                break;

            /* Handling one time payments */
            case 'checkout.session.completed':

                /* Exit when the webhook comes for recurring payments as the invoice.paid event will handle it */
                if($session->subscription) {
                    die();
                }

                $payer_email = $session->customer_details->email;
                $payer_name = $session->customer_details->name;

                $payment_currency = mb_strtoupper($session->currency);
                $payment_total = in_array($payment_currency, ['MGA', 'BIF', 'CLP', 'PYG', 'DJF', 'RWF', 'GNF', 'UGX', 'JPY', 'VND', 'VUV', 'XAF', 'KMF', 'KRW', 'XOF', 'XPF']) ? $session->amount_total : $session->amount_total / 100;

                /* Process meta data */
                $metadata = $session->metadata;

                $user_id = (int) $metadata->user_id;
                $plan_id = (int) $metadata->plan_id;
                $payment_frequency = $metadata->payment_frequency;
                $code = isset($metadata->code) ? $metadata->code : '';
                $discount_amount = isset($metadata->discount_amount) ? $metadata->discount_amount : 0;
                $base_amount = isset($metadata->base_amount) ? $metadata->base_amount : 0;
                $taxes_ids = isset($metadata->taxes_ids) ? $metadata->taxes_ids : null;

                /* Vars */
                $payment_type = $session->subscription ? 'recurring' : 'one_time';
                $payment_subscription_id =  $payment_type == 'recurring' ? $session->subscription : '';

                break;
        }

        (new Payments())->webhook_process_payment(
            'stripe',
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
