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

class WebhookMollie extends Controller {

    public function index() {

        if((strtoupper($_SERVER['REQUEST_METHOD']) != 'POST' ) || empty($_POST['id'])) {
            die();
        }

        $mollie = new \Mollie\Api\MollieApiClient();
        $mollie->setApiKey(settings()->mollie->api_key);

        /* Retrieve the payment */
        $payment = $mollie->payments->get($_POST['id']);

        if($payment->isPaid() && ! $payment->hasRefunds() && ! $payment->hasChargebacks()) {

            if(!in_array($payment->sequenceType, ['oneoff', 'first', 'recurring'])) {
                die();
            }

            $payment_subscription_id = null;

            /* If its a first payment, start the subscription */
            if ($payment->sequenceType == 'first') {

                /* Determine correct interval from frequency */
                $interval = match ($payment->metadata->payment_frequency) {
                    'monthly' => '1 month',
                    'quarterly' => '3 months',
                    'biannual' => '6 months',
                    'annual' => '1 year',
                    default => '1 month', /* fallback default */
                };

                /* Calculate start date based on interval */
                $start_date = match ($interval) {
                    '1 month' => (new \DateTime())->modify('+1 month')->format('Y-m-d'),
                    '3 months' => (new \DateTime())->modify('+3 months')->format('Y-m-d'),
                    '6 months' => (new \DateTime())->modify('+6 months')->format('Y-m-d'),
                    '1 year' => (new \DateTime())->modify('+1 year')->format('Y-m-d'),
                };

                /* Generate the subscription */
                try {
                    $subscription = $mollie->subscriptions->createForId($payment->customerId, [
                        'amount' => [
                            'currency' => $payment->amount->currency,
                            'value' => $payment->amount->value,
                        ],
                        'description' => $payment->description,
                        'interval' => $interval,
                        'startDate' => $start_date,
                        'webhookUrl' => SITE_URL . 'webhook-mollie',
                        'metadata' => $payment->metadata,
                    ]);
                } catch (\Exception $exception) {
                    echo $exception->getCode() . ':' . $exception->getMessage();
                    http_response_code(400); die();
                }

                $payment_subscription_id = $subscription->customerId . '###' . $subscription->id;
            }

            /* Recurring payment */
            if($payment->sequenceType == 'recurring') {
                $payment_subscription_id = $payment->customerId . '###' . $payment->subscriptionId;
            }

            /* Start getting the payment details */
            $external_payment_id = $payment->id;
            $payment_total = $payment->amount->value;
            $payment_currency = $payment->amount->currency;
            $payment_type = $payment_subscription_id ? 'recurring' : 'one_time';

            /* Payment payer details */
            $payer_email = '';
            $payer_name = '';

            /* Process meta data */
            $metadata = $payment->metadata;
            $user_id = (int) $metadata->user_id;
            $plan_id = (int) $metadata->plan_id;
            $payment_frequency = $metadata->payment_frequency;
            $code = isset($metadata->code) ? $metadata->code : '';
            $discount_amount = isset($metadata->discount_amount) ? $metadata->discount_amount : 0;
            $base_amount = isset($metadata->base_amount) ? $metadata->base_amount : 0;
            $taxes_ids = isset($metadata->taxes_ids) ? $metadata->taxes_ids : null;

            (new Payments())->webhook_process_payment(
                'mollie',
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
