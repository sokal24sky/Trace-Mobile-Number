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

use Altum\Alerts;
use Altum\Models\User;

defined('ALTUMCODE') || die();

class AccountRedeemCode extends Controller {

    public function index() {
        \Altum\Authentication::guard();

        if(!settings()->payment->is_enabled || !settings()->payment->codes_is_enabled) {
            redirect('not-found');
        }

        /* Get all plans */
        $plans = (new \Altum\Models\Plan())->get_plans();

        if(!empty($_POST)) {
            $_POST['plan_id'] = (int) $_POST['plan_id'];
            $code = $_POST['code'] = trim(query_clean($_POST['code']));

            //ALTUMCODE:DEMO if(DEMO) if($this->user->user_id == 1) Alerts::add_error('Please create an account on the demo to test out this function.');

            /* Check for any errors */
            if(!\Altum\Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token')); redirect('account-redeem-code');
            }

            if(!array_key_exists($_POST['plan_id'], $plans)) {
                Alerts::add_field_error('code', l('account_redeem_code.error_message.code_invalid')); redirect('account-redeem-code');
            }

            if(!$code = database()->query("SELECT * FROM `codes` WHERE `code` = '{$code}' AND `type` = 'redeemable' AND `redeemed` < `quantity`")->fetch_object()) {
                Alerts::add_field_error('code', l('account_redeem_code.error_message.code_invalid')); redirect('account-redeem-code');
            }

            $code->plans_ids = json_decode($code->plans_ids ?? '[]');

            if(!in_array($_POST['plan_id'], $code->plans_ids)) {
                Alerts::add_field_error('code', l('account_redeem_code.error_message.code_invalid')); redirect('account-redeem-code');
            }

            if(db()->where('user_id', $this->user->user_id)->where('code_id', $code->code_id)->has('redeemed_codes')) {
                Alerts::add_field_error('code', l('account_redeem_code.error_message.code_used')); redirect('account-redeem-code');
            }

            /* Cancel current subscription if needed */
            if($this->user->plan_id != $_POST['plan_id']) {
                try {
                    (new User())->cancel_subscription($this->user->user_id);
                } catch (\Exception $exception) {
                    Alerts::add_error($exception->getCode() . ':' . $exception->getMessage());
                }
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

                $datetime = $this->user->plan_id == $_POST['plan_id'] ? $this->user->plan_expiration_date : '';
                $plan_expiration_date = (new \DateTime($datetime))->modify('+' . $code->days . ' days')->format('Y-m-d H:i:s');
                $plan = $plans[$_POST['plan_id']];
                $plan_settings = json_encode($plan->settings ?? '');

                /* Database query */
                db()->where('user_id', $this->user->user_id)->update('users', [
                    'plan_id' => $_POST['plan_id'],
                    'plan_expiration_date' => $plan_expiration_date,
                    'plan_settings' => $plan_settings,
                    'plan_expiry_reminder' => 0,
                ]);

                /* Update the code usage */
                db()->where('code_id', $code->code_id)->update('codes', ['redeemed' => db()->inc()]);

                /* Add log for the redeemed code */
                db()->insert('redeemed_codes', [
                    'code_id'   => $code->code_id,
                    'user_id'   => $this->user->user_id,
                    'datetime'  => get_date()
                ]);

                /* Send webhook notification if needed */
                if(settings()->webhooks->code_redeemed) {
                    fire_and_forget('post', settings()->webhooks->code_redeemed, [
                        'user_id' => $this->user->user_id,
                        'email' => $this->user->email,
                        'name' => $this->user->name,
                        'plan_id' => $_POST['plan_id'],
                        'plan_expiration_date' => $plan_expiration_date,
                        'code_id' => $code->code_id,
                        'code' => $code->code,
                        'code_name' => $code->name,
                        'redeemed_days' => $code->days,
                        'datetime' => get_date(),
                    ]);
                }

                /* Clear the cache */
                cache()->deleteItemsByTag('user_id=' . $this->user->user_id);

                /* Set a nice success message */
                if($code->days >= 10*365) {
                    Alerts::add_success(sprintf(l('account_redeem_code.success_message_lifetime'), $plan->translations->{\Altum\Language::$name}->name ?: $plan->name));
                } else {
                    Alerts::add_success(sprintf(l('account_redeem_code.success_message'), $code->days, $plan->translations->{\Altum\Language::$name}->name ?: $plan->name));
                }

                redirect('account-redeem-code');
            }

        }

        /* Default values */
        $values = [
            'plan_id' => $_POST['plan_id'] ?? null,
            'code' => $_POST['code'] ?? null,
        ];

        /* Get the account header menu */
        $menu = new \Altum\View('partials/account_header_menu', (array) $this);
        $this->add_view_content('account_header_menu', $menu->run());

        /* Prepare the view */
        $data = [
            'plans' => $plans,
            'values' => $values,
        ];

        $view = new \Altum\View('account-redeem-code/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
