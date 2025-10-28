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
use YooKassa\Model\Notification\NotificationSucceeded;
use YooKassa\Model\NotificationEventType;

defined('ALTUMCODE') || die();

class WebhookYookassa extends Controller {

    public function index() {

        if((strtoupper($_SERVER['REQUEST_METHOD']) != 'POST')) {
            die();
        }

        $yookassa = new \YooKassa\Client();
        $yookassa->setAuth(settings()->yookassa->shop_id, settings()->yookassa->secret_key);

        $payload = @file_get_contents('php://input');

        $data = json_decode($payload, true);

        try {
            $notification = new NotificationSucceeded($data);
        } catch (\Exception $exception) {
            http_response_code(400); die($exception->getCode() . ':' . $exception->getMessage());
        }

        if($notification->getEvent() == NotificationEventType::PAYMENT_SUCCEEDED) {
            $payment_subscription_id = null;

            /* Start getting the payment details */
            $external_payment_id = $notification->getObject()->getId();
            $payment_total = $notification->getObject()->getAmount()->getValue();
            $payment_currency = $notification->getObject()->getAmount()->getCurrency();
            $payment_type = $payment_subscription_id ? 'recurring' : 'one_time';

            /* Payment payer details */
            $payer_email = '';
            $payer_name = '';

            /* Process meta data */
            $metadata = $notification->getObject()->getMetadata();
            $user_id = (int) $metadata->user_id;
            $plan_id = (int) $metadata->plan_id;
            $payment_frequency = $metadata->payment_frequency;
            $code = isset($metadata->code) ? $metadata->code : '';
            $discount_amount = isset($metadata->discount_amount) ? $metadata->discount_amount : 0;
            $base_amount = isset($metadata->base_amount) ? $metadata->base_amount : 0;
            $taxes_ids = isset($metadata->taxes_ids) ? $metadata->taxes_ids : null;

            (new Payments())->webhook_process_payment(
                'yookassa',
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
