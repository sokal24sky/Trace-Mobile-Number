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

namespace Altum\Models;

defined('ALTUMCODE') || die();

class Payments extends Model {

    public function webhook_process_payment($payment_processor, $external_payment_id, $payment_total, $payment_currency, $user_id, $plan_id, $payment_frequency, $code, $discount_amount, $base_amount, $taxes_ids, $payment_type, $payment_subscription_id, $payer_email, $payer_name) {
        /* Get the plan details */
        $plan = db()->where('plan_id', $plan_id)->getOne('plans');

        /* Just make sure the plan is still existing */
        if(!$plan) {
            http_response_code(400);die();
        }

        /* Make sure the transaction is not already existing */
        if(db()->where('payment_id', $external_payment_id)->where('processor', $payment_processor)->has('payments')) {
            http_response_code(400);die();
        }

        /* Make sure the account still exists */
        $user = db()->where('user_id', $user_id)->getOne('users');

        if(!$user) {
            http_response_code(400);die();
        }

        /* Unsubscribe from the previous plan if needed */
        if(!empty($user->payment_subscription_id) && ($payment_subscription_id && $user->payment_subscription_id != $payment_subscription_id)) {
            try {
                (new User())->cancel_subscription($user_id);
            } catch (\Exception $exception) {
                if(DEBUG) {
                    error_log($exception->getMessage());
                }
                echo $exception->getMessage();
                http_response_code(400); die();
            }
        }

        /* Check for potential paid plans */
        if($payment_total == 0) {
            /* Determine the expiration date of the plan */
            $plan_expiration_date = (new \DateTime())->modify('+' . $plan->trial_days . ' days')->format('Y-m-d H:i:s');

            /* Database query */
            db()->where('user_id', $user->user_id)->update('users', [
                'plan_id' => $plan->plan_id,
                'plan_settings' => $plan->settings,
                'plan_expiration_date' => $plan_expiration_date,
                'plan_trial_done' => 1,
                'payment_subscription_id' => $payment_subscription_id,
                'payment_processor' => $payment_processor,
                'payment_total_amount' => $base_amount - $discount_amount,
                'payment_currency' => $payment_currency,
            ]);

            /* Clear the cache */
            cache()->deleteItemsByTag('user_id=' . $user->user_id);

            return;
        }

        /* Codes */
        $code = (new Payments())->codes_payment_check($code, $user);

        /* Currency exchange in case its needed */
        $total_amount_default_currency = $payment_total;

        if(settings()->payment->default_currency != $payment_currency && settings()->payment->currency_exchange_api_key) {
            try {
                $response = \Unirest\Request::get('https://api.freecurrencyapi.com/v1/latest?apikey=' . settings()->payment->currency_exchange_api_key . '&base_currency=' . $payment_currency . '&currencies=' . settings()->payment->default_currency);

                if($response->code == 200) {
                    $total_amount_default_currency = $payment_total * $response->body->data->{settings()->payment->default_currency};
                    $total_amount_default_currency = number_format($total_amount_default_currency, 2, '.', '');
                }
            } catch (\Exception $exception) {
                /* :) */
            }
        }

        $payment_datetime = get_date();

        /* Add a log into the database */
        $payment_id = db()->insert('payments', [
            'user_id' => $user_id,
            'plan_id' => $plan_id,
            'processor' => $payment_processor,
            'type' => $payment_type,
            'frequency' => $payment_frequency,
            'code' => $code ? $code->code : null,
            'discount_amount' => $discount_amount,
            'base_amount' => $base_amount,
            'email' => $payer_email,
            'payment_id' => $external_payment_id,
            'name' => $payer_name,
            'plan' => json_encode(db()->where('plan_id', $plan_id)->getOne('plans', ['plan_id', 'name'])),
            'billing' => settings()->payment->taxes_and_billing_is_enabled && $user->billing ? $user->billing : null,
            'business' => json_encode(settings()->business),
            'taxes_ids' => $taxes_ids,
            'total_amount' => $payment_total,
            'total_amount_default_currency' => $total_amount_default_currency,
            'currency' => $payment_currency,
            'datetime' => $payment_datetime
        ]);

        /* Update the user with the new plan */
        $current_plan_expiration_date = $plan_id == $user->plan_id ? $user->plan_expiration_date : '';
        $modifier = match ($payment_frequency) {
            'monthly' => '+30 days +12 hours',
            'quarterly' => '+3 months +12 hours',
            'biannual' => '+6 months +12 hours',
            'annual' => '+12 months +12 hours',
            'lifetime' => '+100 years +12 hours',
        };
        $plan_expiration_date = (new \DateTime($current_plan_expiration_date))->modify($modifier)->format('Y-m-d H:i:s');

        /* Database query */
        db()->where('user_id', $user_id)->update('users', [
            'plan_id' => $plan_id,
            'plan_settings' => $plan->settings,
            'plan_expiration_date' => $plan_expiration_date,
            'plan_expiry_reminder' => 0,
            'plan_trial_done' => 1,
            'payment_subscription_id' => $payment_subscription_id,
            'payment_processor' => $payment_processor,
            'payment_total_amount' => $payment_total,
            'payment_currency' => $payment_currency,
        ]);

        /* Run potential hooks */
        \Altum\CustomHooks::user_payment_finished(['user' => $user, 'plan' => $plan]);

        /* Clear the cache */
        cache()->deleteItemsByTag('user_id=' . $user_id);

        /* Send notification to the user */
        $email_template = get_email_template(
            [],
            l('global.emails.user_payment.subject'),
            [
                '{{NAME}}' => $user->name,
                '{{PLAN_NAME}}' => $plan->name,
                '{{PLAN_EXPIRATION_DATE}}' => \Altum\Date::get($plan_expiration_date, 2),
                '{{USER_PLAN_LINK}}' => url('account-plan'),
                '{{USER_PAYMENTS_LINK}}' => url('account-payments'),
            ],
            l('global.emails.user_payment.body')
        );

        send_mail($user->email, $email_template->subject, $email_template->body, ['anti_phishing_code' => $user->anti_phishing_code, 'language' => $user->language]);

        /* Send notification to admin if needed */
        if(settings()->email_notifications->new_payment && !empty(settings()->email_notifications->emails)) {

            $email_template = get_email_template(
                [
                    '{{PROCESSOR}}' => l('pay.custom_plan.' . $payment_processor),
                    '{{TOTAL_AMOUNT}}' => $payment_total,
                    '{{CURRENCY}}' => $payment_currency,
                ],
                l('global.emails.admin_new_payment_notification.subject'),
                [
                    '{{PROCESSOR}}' => l('pay.custom_plan.' . $payment_processor),
                    '{{TOTAL_AMOUNT}}' => $payment_total,
                    '{{CURRENCY}}' => $payment_currency,
                    '{{NAME}}' => $user->email,
                    '{{EMAIL}}' => $user->email,
                    '{{PLAN_NAME}}' => $plan->name,
                    '{{PAYMENT_FREQUENCY}}' => l('plan.custom_plan.' . $payment_frequency),
                    '{{PAYMENT_TYPE}}' => l('pay.custom_plan.' . $payment_type . '_type'),
                    '{{PAYMENT_ID}}' => $payment_id,
                    '{{EXTERNAL_PAYMENT_ID}}' => $external_payment_id,
                    '{{PAYMENT_LINK}}' => url('admin/payments?id=' . $payment_id),
                    '{{DATE}}' => get_date(),
                    '{{DATE_TIMEZONE}}' => \Altum\Date::$default_timezone,
                    '{{CODE}}' => $code ?: l('global.none'),
                    '{{DISCOUNT_AMOUNT}}' => $discount_amount,
                    '{{PAYMENT_STATUS}}' => l('account_payments.status_approved'),
                ],
                l('global.emails.admin_new_payment_notification.body')
            );

            send_mail(explode(',', settings()->email_notifications->emails), $email_template->subject, $email_template->body);

        }

        /* Send webhook notification if needed */
        if(settings()->webhooks->payment_new) {

            fire_and_forget('post', settings()->webhooks->payment_new, [
                'user_id' => $user->user_id,
                'email' => $user->email,
                'name' => $user->name,
                'plan_id' => $plan_id,
                'plan_expiration_date' => $plan_expiration_date,
                'payment_id' => $payment_id,
                'payment_processor' => $payment_processor,
                'payment_type' => $payment_type,
                'payment_frequency' => $payment_frequency,
                'payment_total_amount' => $payment_total,
                'payment_currency' => $payment_currency,
                'payment_code' => $code->code,
                'datetime' => $payment_datetime,
            ]);

        }

        if(settings()->internal_notifications->admins_is_enabled && settings()->internal_notifications->new_payment) {
            db()->insert('internal_notifications', [
                'for_who' => 'admin',
                'from_who' => 'system',
                'icon' => 'fas fa-credit-card',
                'title' => l('global.notifications.new_payment.title'),
                'description' => sprintf(l('global.notifications.new_payment.description'), $user->name, $user->email, $payment_total, $payment_currency, l('pay.custom_plan.' . $payment_processor)),
                'url' => 'admin/payments',
                'datetime' => get_date(),
            ]);
        }

        /* Affiliate */
        (new Payments())->affiliate_payment_check($payment_id, $total_amount_default_currency, settings()->payment->default_currency, $user);
    }

    public function codes_payment_check($code, $user) {
        /* Make sure the code exists */
        $codes_code = db()->where('code', $code)->where('type', 'discount')->getOne('codes');

        if($codes_code) {
            /* Check if we should insert the usage of the code or not */
            if(!db()->where('user_id', $user->user_id)->where('code_id', $codes_code->code_id)->has('redeemed_codes')) {

                /* Update the code usage */
                db()->where('code_id', $codes_code->code_id)->update('codes', ['redeemed' => db()->inc()]);

                /* Add log for the redeemed code */
                db()->insert('redeemed_codes', [
                    'code_id'   => $codes_code->code_id,
                    'user_id'   => $user->user_id,
                    'datetime'  => get_date()
                ]);
            }

            return $codes_code;
        }

        return null;
    }

    public function affiliate_payment_check($payment_id, $payment_total, $payment_currency, $user) {
        if(\Altum\Plugin::is_active('affiliate') && settings()->affiliate->is_enabled && $user->referred_by) {
            if((settings()->affiliate->commission_type == 'once' && !$user->referred_by_has_converted) || settings()->affiliate->commission_type == 'forever') {
                $referral_user = db()->where('user_id', $user->referred_by)->getOne('users', ['user_id', 'email', 'status', 'plan_settings']);
                $referral_user->plan_settings = json_decode($referral_user->plan_settings);

                /* Make sure the referral user is active and existing */
                if($referral_user && $referral_user->status == 1) {
                    $amount = number_format($payment_total * (float) $referral_user->plan_settings->affiliate_commission_percentage / 100, 2, '.', '');

                    /* Insert the affiliate commission */
                    db()->insert('affiliates_commissions', [
                        'user_id' => $referral_user->user_id,
                        'referred_user_id' => $user->user_id,
                        'payment_id' => $payment_id,
                        'amount' => $amount,
                        'currency' => $payment_currency,
                        'datetime' => get_date()
                    ]);

                    /* Update the referred user */
                    db()->where('user_id', $user->user_id)->update('users', ['referred_by_has_converted' => 1]);
                }
            }
        }
    }

}
