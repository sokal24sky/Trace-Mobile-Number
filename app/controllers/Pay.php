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
use Altum\PaymentGateways\Coinbase;
use Altum\PaymentGateways\Lemonsqueezy;
use Altum\PaymentGateways\Paddle;
use Altum\PaymentGateways\Paystack;
use Altum\Response;
use Altum\Title;
use Razorpay\Api\Api;

defined('ALTUMCODE') || die();

class Pay extends Controller {
    public $plan_id;
    public $return_type;
    public $payment_processor;
    public $plan;
    public $plan_taxes;
    public $applied_taxes_ids = [];
    public $code = null;
    public $payment_extra_data = null;

    public function index() {

        \Altum\Authentication::guard();

        if(!settings()->payment->is_enabled) {
            redirect('not-found');
        }

        $payment_processors = require APP_PATH . 'includes/payment_processors.php';
        $this->plan_id = isset($this->params[0]) ? $this->params[0] : null;
        $this->return_type = isset($_GET['return_type']) && in_array($_GET['return_type'], ['success', 'cancel']) ? $_GET['return_type'] : null;
        $this->payment_processor = isset($_GET['payment_processor']) && array_key_exists($_GET['payment_processor'], $payment_processors) ? $_GET['payment_processor'] : null;

        /* ^_^ */
        switch($this->plan_id) {
            case 'free':

                $this->plan = settings()->plan_free;

                if($this->user->plan_id == 'free') {
                    Alerts::add_info(l('pay.free.free_already'));
                } else {
                    Alerts::add_info(l('pay.free.other_plan_not_expired'));
                }

                redirect('plan');

                break;

            default:

                $this->plan_id = (int) $this->plan_id;

                $plans = (new \Altum\Models\Plan())->get_plans();

                /* Check if plan exists */
                $this->plan = $plans[$this->plan_id] ?? null;
                if(!$this->plan) {
                    redirect('plan');
                }

                /* Check for potential taxes */
                $this->plan_taxes = (new \Altum\Models\Plan())->get_plan_taxes_by_taxes_ids($this->plan->taxes_ids);

                /* Filter them out */
                if($this->plan_taxes) {
                    foreach($this->plan_taxes as $key => $value) {

                        /* Type */
                        if($value->billing_type != $this->user->billing->type && $value->billing_type != 'both') {
                            unset($this->plan_taxes[$key]);
                        }

                        /* Countries */
                        if($value->countries && !in_array($this->user->billing->country, $value->countries)) {
                            unset($this->plan_taxes[$key]);
                        }

                        if(isset($this->plan_taxes[$key])) {
                            $this->applied_taxes_ids[] = (int) $value->tax_id;
                        }

                    }

                    $this->plan_taxes = array_values($this->plan_taxes);
                }

                break;
        }

        /* Make sure the plan is enabled */
        if(!$this->plan->status) {
            redirect('plan');
        }

        if(
            settings()->payment->taxes_and_billing_is_enabled
            && ($this->user->plan_trial_done || !$this->plan->trial_days || isset($_GET['trial_skip']))
            && (empty($this->user->billing->name) || empty($this->user->billing->address) || empty($this->user->billing->city) || empty($this->user->billing->county) || empty($this->user->billing->zip))
        ) {
            redirect('pay-billing/' . $this->plan_id . '?' . (isset($_GET['trial_skip']) ? '&trial_skip=true' : null) . (isset($_GET['code']) ? '&code=' . $_GET['code'] : null));
        }

        /* Form submission processing */
        /* Make sure that this only runs on user click submit post and not on callbacks / webhooks */
        if(!empty($_POST) && !$this->return_type) {

            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');
            //ALTUMCODE:DEMO if(DEMO) redirect('pay/' . $this->plan_id . (isset($_GET['trial_skip']) ? '?trial_skip=true' : null));

            /* Check for code usage */
            if(settings()->payment->codes_is_enabled && isset($_POST['code'])) {
                $_POST['code'] = query_clean($_POST['code']);
                $this->code = database()->query("SELECT * FROM `codes` WHERE `code` = '{$_POST['code']}' AND `redeemed` < `quantity`")->fetch_object();

                if($this->code) {
                    $this->code->plans_ids = json_decode($this->code->plans_ids ?? '');

                    if(db()->where('user_id', $this->user->user_id)->where('code_id', $this->code->code_id)->has('redeemed_codes')) {
                        $this->code = null;
                    }

                    if(!in_array($this->plan_id, $this->code->plans_ids)) {
                        $this->code = null;
                    }
                }
            }

            /* Check for any errors */
            if(!\Altum\Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            /* Process further */
            if($this->plan->trial_days && !$this->user->plan_trial_done && !isset($_GET['trial_skip'])) {
                /* :) */
            } else if($this->code && $this->code->type == 'redeemable' && in_array($this->plan_id, $this->code->plans_ids)) {

                /* Cancel current subscription if needed */
                if($this->user->plan_id != $this->plan->plan_id) {
                    try {
                        (new User())->cancel_subscription($this->user->user_id);
                    } catch (\Exception $exception) {
                        Alerts::add_error($exception->getCode() . ':' . $exception->getMessage());
                        redirect('pay/' . $this->plan_id . '?' . (isset($_GET['trial_skip']) ? '&trial_skip=true' : null) . (isset($_GET['code']) ? '&code=' . $_GET['code'] : null));
                    }
                }

            } else {
                $_POST['payment_frequency'] = query_clean($_POST['payment_frequency']);
                $_POST['payment_processor'] = query_clean($_POST['payment_processor']);
                $_POST['payment_type'] = query_clean($_POST['payment_type']);

                /* Make sure the chosen option comply */
                if(!in_array($_POST['payment_frequency'], ['monthly', 'quarterly', 'biannual', 'annual', 'lifetime'])) {
                    redirect('pay/' . $this->plan_id . '?' . (isset($_GET['trial_skip']) ? '&trial_skip=true' : null) . (isset($_GET['code']) ? '&code=' . $_GET['code'] : null));
                }

                if(!array_key_exists($_POST['payment_processor'], $payment_processors)) {
                    redirect('pay/' . $this->plan_id . '?' . (isset($_GET['trial_skip']) ? '&trial_skip=true' : null) . (isset($_GET['code']) ? '&code=' . $_GET['code'] : null));
                } else {

                    /* Make sure the payment processor is active */
                    if(!settings()->{$_POST['payment_processor']}->is_enabled) {
                        redirect('pay/' . $this->plan_id . '?' . (isset($_GET['trial_skip']) ? '&trial_skip=true' : null) . (isset($_GET['code']) ? '&code=' . $_GET['code'] : null));
                    }

                }

                if(!in_array($_POST['payment_type'], ['one_time', 'recurring'])) {
                    redirect('pay/' . $this->plan_id . '?' . (isset($_GET['trial_skip']) ? '&trial_skip=true' : null) . (isset($_GET['code']) ? '&code=' . $_GET['code'] : null));
                }

                /* Lifetime */
                if($_POST['payment_frequency'] == 'lifetime') {
                    $_POST['payment_type'] = 'one_time';
                }

                /* Make sure recurring is available for the payment processor */
                if(!in_array('recurring', $payment_processors[$_POST['payment_processor']]['payment_type'])) {
                    $_POST['payment_type'] = 'one_time';
                }
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {
                /* Check if we should start the trial or not */
                if(!settings()->payment->trial_require_card && $this->plan->trial_days && !$this->user->plan_trial_done && !isset($_GET['trial_skip'])) {

                    /* Determine the expiration date of the plan */
                    $plan_expiration_date = (new \DateTime())->modify('+' . $this->plan->trial_days . ' days')->format('Y-m-d H:i:s');
                    $plan_settings = json_encode($this->plan->settings ?? '');

                    /* Database query */
                    db()->where('user_id', $this->user->user_id)->update('users', [
                        'plan_id' => $this->plan_id,
                        'plan_settings' => $plan_settings,
                        'plan_expiration_date' => $plan_expiration_date,
                        'plan_trial_done' => 1,
                    ]);

                    /* Clear the cache */
                    cache()->deleteItemsByTag('user_id=' . $this->user->user_id);

                    /* Success message and redirect */
                    $this->redirect_pay_thank_you();
                }

                /* Redeem */
                else if($this->code && $this->code->type == 'redeemable' && in_array($this->plan_id, $this->code->plans_ids)) {

                    $datetime = $this->user->plan_id == $this->plan->plan_id ? $this->user->plan_expiration_date : '';
                    $plan_expiration_date = (new \DateTime($datetime))->modify('+' . $this->code->days . ' days')->format('Y-m-d H:i:s');
                    $plan_settings = json_encode($this->plan->settings ?? '');

                    /* Database query */
                    db()->where('user_id', $this->user->user_id)->update('users', [
                        'plan_id' => $this->plan_id,
                        'plan_expiration_date' => $plan_expiration_date,
                        'plan_settings' => $plan_settings,
                        'plan_expiry_reminder' => 0,
                    ]);

                    /* Update the code usage */
                    db()->where('code_id', $this->code->code_id)->update('codes', ['redeemed' => db()->inc()]);

                    /* Add log for the redeemed code */
                    db()->insert('redeemed_codes', [
                        'code_id'   => $this->code->code_id,
                        'user_id'   => $this->user->user_id,
                        'datetime'  => get_date()
                    ]);

                    /* Send webhook notification if needed */
                    if(settings()->webhooks->code_redeemed) {
                        fire_and_forget('post', settings()->webhooks->code_redeemed, [
                            'user_id' => $this->user->user_id,
                            'email' => $this->user->email,
                            'name' => $this->user->name,
                            'plan_id' => $this->plan_id,
                            'plan_expiration_date' => $plan_expiration_date,
                            'code_id' => $this->code->code_id,
                            'code' => $this->code->code,
                            'code_name' => $this->code->name,
                            'redeemed_days' => $this->code->days,
                            'datetime' => get_date(),
                        ]);
                    }

                    /* Send admin notification if enabled */
                    if (settings()->email_notifications->new_code_redeemed && !empty(settings()->email_notifications->emails)) {

                        $email_template = get_email_template(
                            [
                                '{{CODE}}' => $this->code->code,
                            ],
                            l('global.emails.admin_new_code_redeemed_notification.subject'),
                            [
                                '{{CODE}}' => $this->code->code,
                                '{{REDEEMED_DAYS}}' => nr($this->code->days),
                                '{{NAME}}' => $this->user->name,
                                '{{EMAIL}}' => $this->user->email,
                                '{{PLAN_NAME}}' => $this->plan->name,
                                '{{USER_LINK}}' => url('admin/user-view/' . $this->user->user_id),
                            ],
                            l('global.emails.admin_new_code_redeemed_notification.body')
                        );

                        send_mail(
                            explode(',', settings()->email_notifications->emails),
                            $email_template->subject,
                            $email_template->body
                        );
                    }

                    /* Clear the cache */
                    cache()->deleteItemsByTag('user_id=' . $this->user->user_id);

                    /* Success message and redirect */
                    $this->redirect_pay_thank_you();
                }

                else {
                    $this->{$_POST['payment_processor']}();
                }
            }

        }

        /* Include the detection of callbacks processing */
        $this->payment_return_process();

        /* Set a custom title */
        Title::set(sprintf(l('pay.title'), $this->plan->translations->{\Altum\Language::$name}->name ?? $this->plan->name));

        /* Prepare the view */
        $data = [
            'plan_id'           => $this->plan_id,
            'plan'              => $this->plan,
            'plan_taxes'        => $this->plan_taxes,
            'payment_processors'=> $payment_processors,
            'payment_extra_data'=> $this->payment_extra_data,
        ];

        $view = new \Altum\View('pay/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    private function payment_return_process() {

        /* Return confirmation processing if successfully */
        if($this->return_type && $this->payment_processor && $this->return_type == 'success') {

            /* Redirect to the thank you page */
            $this->redirect_pay_thank_you();
        }

        /* Return confirmation processing if failed */
        if($this->return_type && $this->payment_processor && $this->return_type == 'cancel') {
            Alerts::add_error(l('pay.error_message.canceled_payment'));
            redirect('pay/' . $this->plan_id . '?' . (isset($_GET['trial_skip']) ? '&trial_skip=true' : null) . (isset($_GET['code']) ? '&code=' . $_GET['code'] : null));
        }

    }

    /* Ajax to check if discount codes are available */
    public function code() {
        \Altum\Authentication::guard();

        $_POST = json_decode(file_get_contents('php://input'), true);

        if(!\Altum\Csrf::check('global_token')) {
            die();
        }

        if(!settings()->payment->is_enabled || !settings()->payment->codes_is_enabled) {
            die();
        }

        if(empty($_POST)) {
            die();
        }

        $_POST['plan_id'] = (int) $_POST['plan_id'];
        $_POST['code'] = trim(query_clean($_POST['code']));

        if(!$plan = db()->where('plan_id', $_POST['plan_id'])->getOne('plans')) {
            Response::json(l('pay.error_message.code_invalid'), 'error');
        }

        /* Make sure the discount code exists */
        $code = database()->query("SELECT * FROM `codes` WHERE `code` = '{$_POST['code']}' AND `redeemed` < `quantity`")->fetch_object();

        if(!$code) {
            Response::json(l('pay.error_message.code_invalid'), 'error');
        }

        $code->plans_ids = json_decode($code->plans_ids ?? '[]');

        if(!in_array($_POST['plan_id'], $code->plans_ids)) {
            Response::json(l('pay.error_message.code_invalid'), 'error');
        }

        if(db()->where('user_id', $this->user->user_id)->where('code_id', $code->code_id)->has('redeemed_codes')) {
            Response::json(l('pay.error_message.code_used'), 'error');
        }

        Response::json(
            sprintf(l('pay.success_message.code'), '<strong>' . $code->discount . '%</strong>'),
            'success',
            [
                'code' => $code,
                'submit_text' => $code->type == 'redeemable' ? sprintf(l('pay.custom_plan.code_redeemable'), $code->days) : null
            ]
        );
    }

    /* Generate the generic return url parameters */
    private function return_url_parameters($return_type, $base_amount, $total_amount, $code, $discount_amount) {
        return
            '&return_type=' . $return_type
            . '&payment_processor=' . $_POST['payment_processor']
            . '&payment_frequency=' . $_POST['payment_frequency']
            . '&payment_type=' . $_POST['payment_type']
            . '&code=' . $code
            . '&discount_amount=' . $discount_amount
            . '&base_amount=' . $base_amount
            . '&total_amount=' . $total_amount
            . '&currency=' . currency();
    }

    /* Simple url generator to return the thank you page */
    private function redirect_pay_thank_you() {
        $thank_you_url_parameters_raw = array_filter($_GET, function($key) {
            return $key != 'altum';
        }, ARRAY_FILTER_USE_KEY);

        $thank_you_url_parameters = '&plan_id=' . $this->plan_id;
        $thank_you_url_parameters .= '&user_id=' . $this->user->user_id;

        /* Trial */
        if($this->plan->trial_days && !$this->user->plan_trial_done && !isset($_GET['trial_skip'])) {
            $thank_you_url_parameters .= '&trial_days=' . $this->plan->trial_days;
        }

        /* Redeemed */
        if($this->code && $this->code->type == 'redeemable' && in_array($this->plan_id, $this->code->plans_ids)) {
            $thank_you_url_parameters .= '&code_days=' . $this->code->days;
        }

        foreach($thank_you_url_parameters_raw as $key => $value) {
            $thank_you_url_parameters .= '&' . $key . '=' . $value;
        }

        $thank_you_url_parameters .= '&unique_transaction_identifier=' . md5(\Altum\Date::get('', 4) . $thank_you_url_parameters);

        redirect('pay-thank-you?' . $thank_you_url_parameters);
    }

    private function get_price_details() {
        /* Payment details */
        $price = $base_amount = (float) $this->plan->prices->{$_POST['payment_frequency']}->{currency()};
        $code = '';
        $discount_amount = 0;

        /* Check for code usage */
        if($this->code) {
            /* Discount amount */
            $discount_amount = number_format(($price * $this->code->discount / 100), 2, '.', '');

            /* Calculate the new price */
            $price = $price - $discount_amount;

            $code = $this->code->code;
        }

        $payment_frequency_days = match ($_POST['payment_frequency']) {
            'monthly' => 30,
            'quarterly' => 90,
            'biannual' => 180,
            'annual' => 365,
            default => 30,
        };

        return [
            'base_amount' => $base_amount,
            'price' => $price,
            'code' => $code,
            'discount_amount' => $discount_amount,
            'payment_frequency_days' => $payment_frequency_days,
        ];
    }

    private function calculate_price_with_taxes($discounted_price) {

        $price = $discounted_price;

        if($this->plan_taxes) {

            /* Check for the inclusives */
            $inclusive_taxes_total_percentage = 0;

            foreach($this->plan_taxes as $row) {
                if($row->type == 'exclusive') continue;

                $inclusive_taxes_total_percentage += $row->value;
            }

            $total_inclusive_tax = $price - ($price / (1 + $inclusive_taxes_total_percentage / 100));

            $price_without_inclusive_taxes = $price - $total_inclusive_tax;

            /* Check for the exclusives */
            $exclusive_taxes_array = [];

            foreach($this->plan_taxes as $row) {

                if($row->type == 'inclusive') {
                    continue;
                }

                $exclusive_tax = $row->value_type == 'percentage' ? $price_without_inclusive_taxes * ($row->value / 100) : $row->value;

                $exclusive_taxes_array[] = $exclusive_tax;

            }

            $exclusive_taxes = array_sum($exclusive_taxes_array);

            /* Price with all the taxes */
            $price_with_taxes = $price + $exclusive_taxes;

            $price = $price_with_taxes;
        }

        return $price;

    }

    private function paypal() {

        /* Get price details */
        extract($this->get_price_details());

        /* Apply taxes to base price */
        $price_with_taxes = $this->calculate_price_with_taxes($price);

        /* Format price based on currency */
        $formatted_price = in_array(currency(), ['JPY', 'TWD', 'HUF'])
            ? number_format($price_with_taxes, 0, '.', '')
            : number_format($price_with_taxes, 2, '.', '');

        /* Prepare redirect query parameters */
        $trial_skip_parameter = isset($_GET['trial_skip']) ? '&trial_skip=true' : '';
        $discount_code_parameter = isset($_GET['code']) ? '&code=' . $_GET['code'] : '';

        try {
            $paypal_api_url = \Altum\PaymentGateways\Paypal::get_api_url();
            $paypal_headers = \Altum\PaymentGateways\Paypal::get_headers();
        } catch (\Exception $exception) {
            Alerts::add_error($exception->getMessage());
            redirect('pay/' . $this->plan_id . '?' . $trial_skip_parameter . $discount_code_parameter);
        }

        /* Create unique custom id for Paypal tracking */
        $paypal_custom_id = $this->user->user_id . '&' . $this->plan_id . '&' . $_POST['payment_frequency'] . '&' . $base_amount . '&' . $code . '&' . $discount_amount . '&' . json_encode($this->applied_taxes_ids);

        /* Handle payment types */
        switch ($_POST['payment_type']) {

            case 'one_time':

                $paypal_order_payload = [
                    'intent' => 'CAPTURE',
                    'purchase_units' => [[
                        'amount' => [
                            'currency_code' => currency(),
                            'value' => $formatted_price,
                            'breakdown' => [
                                'item_total' => [
                                    'currency_code' => currency(),
                                    'value' => $formatted_price
                                ]
                            ]
                        ],
                        'description' => l('plan.custom_plan.' . $_POST['payment_frequency']),
                        'custom_id' => $paypal_custom_id,
                        'items' => [[
                            'name' => settings()->business->brand_name . ' - ' . $this->plan->name,
                            'description' => l('plan.custom_plan.' . $_POST['payment_frequency']),
                            'quantity' => 1,
                            'unit_amount' => [
                                'currency_code' => currency(),
                                'value' => $formatted_price
                            ]
                        ]]
                    ]],
                    'application_context' => [
                        'brand_name' => settings()->business->brand_name ?: settings()->main->title,
                        'landing_page' => 'NO_PREFERENCE',
                        'shipping_preference' => 'NO_SHIPPING',
                        'user_action' => 'PAY_NOW',
                        'return_url' => url('pay/' . $this->plan_id . $this->return_url_parameters('success', $base_amount, $formatted_price, $code, $discount_amount)),
                        'cancel_url' => url('pay/' . $this->plan_id . $this->return_url_parameters('cancel', $base_amount, $formatted_price, $code, $discount_amount))
                    ]
                ];

                $paypal_response = \Unirest\Request::post(
                    $paypal_api_url . 'v2/checkout/orders',
                    $paypal_headers,
                    \Unirest\Request\Body::json($paypal_order_payload)
                );

                if ($paypal_response->code >= 400) {
                    $paypal_error_message = (DEBUG || \Altum\Authentication::is_admin())
                        ? $paypal_response->body->name . ':' . $paypal_response->body->message
                        : l('pay.error_message.failed_payment');
                    Alerts::add_error($paypal_error_message);
                    redirect('pay/' . $this->plan_id . '?' . $trial_skip_parameter . $discount_code_parameter);
                }

                $paypal_payment_url = $paypal_response->body->links[1]->href;
                header('Location: ' . $paypal_payment_url); die();

                break;

            case 'recurring':

                $paypal_product_id = $this->plan_id . '_' . $_POST['payment_frequency'] . '_' . $formatted_price . '_' . currency();

                /* Ensure the Paypal product exists, create if missing */
                $product_response = \Unirest\Request::get(
                    $paypal_api_url . 'v1/catalogs/products/' . $paypal_product_id,
                    $paypal_headers
                );

                if ($product_response->code == 404) {
                    $product_create_response = \Unirest\Request::post(
                        $paypal_api_url . 'v1/catalogs/products',
                        $paypal_headers,
                        \Unirest\Request\Body::json([
                            'id' => $paypal_product_id,
                            'name' => settings()->business->brand_name . ' - ' . $this->plan->name,
                            'type' => 'DIGITAL',
                        ])
                    );

                    if ($product_create_response->code >= 400) {
                        $paypal_error_message = (DEBUG || \Altum\Authentication::is_admin())
                            ? $product_create_response->body->name . ':' . $product_create_response->body->message
                            : l('pay.error_message.failed_payment');
                        Alerts::add_error($paypal_error_message);
                        redirect('pay/' . $this->plan_id . '?' . $trial_skip_parameter . $discount_code_parameter);
                    }
                }

                /* Create Paypal billing plan */
                $plan_response = \Unirest\Request::post(
                    $paypal_api_url . 'v1/billing/plans',
                    $paypal_headers,
                    \Unirest\Request\Body::json([
                        'product_id' => $paypal_product_id,
                        'name' => settings()->business->brand_name . ' - ' . $this->plan->name . ' - ' . l('plan.custom_plan.' . $_POST['payment_frequency']),
                        'description' => l('plan.custom_plan.' . $_POST['payment_frequency']),
                        'status' => 'ACTIVE',
                        'billing_cycles' => [[
                            'pricing_scheme' => [
                                'fixed_price' => [
                                    'currency_code' => currency(),
                                    'value' => $formatted_price
                                ]
                            ],
                            'frequency' => [
                                'interval_unit' => 'DAY',
                                'interval_count' => $payment_frequency_days
                            ],
                            'tenure_type' => 'REGULAR',
                            'sequence' => 1,
                            'total_cycles' => 0
                        ]],
                        'payment_preferences' => [
                            'auto_bill_outstanding' => true,
                            'setup_fee' => [
                                'currency_code' => currency(),
                                'value' => $formatted_price
                            ],
                            'setup_fee_failure_action' => 'CANCEL',
                            'payment_failure_threshold' => 0
                        ]
                    ])
                );

                if ($plan_response->code >= 400) {
                    $paypal_error_message = (DEBUG || \Altum\Authentication::is_admin())
                        ? $plan_response->body->name . ':' . $plan_response->body->message
                        : l('pay.error_message.failed_payment');
                    Alerts::add_error($paypal_error_message);
                    redirect('pay/' . $this->plan_id . '?' . $trial_skip_parameter . $discount_code_parameter);
                }

                /* Create Paypal subscription */
                $subscription_response = \Unirest\Request::post(
                    $paypal_api_url . 'v1/billing/subscriptions',
                    $paypal_headers,
                    \Unirest\Request\Body::json([
                        'plan_id' => $plan_response->body->id,
                        'start_time' => (new \DateTime())->modify('+' . $payment_frequency_days . ' days')->format(DATE_ISO8601),
                        'quantity' => 1,
                        'custom_id' => $paypal_custom_id,
                        'payment_method' => [
                            'payer_selected' => 'PAYPAL',
                            'payee_preferred' => 'IMMEDIATE_PAYMENT_REQUIRED'
                        ],
                        'application_context' => [
                            'brand_name' => settings()->business->brand_name ?: settings()->main->title,
                            'shipping_preference' => 'NO_SHIPPING',
                            'user_action' => 'SUBSCRIBE_NOW',
                            'return_url' => url('pay/' . $this->plan_id . $this->return_url_parameters('success', $base_amount, $formatted_price, $code, $discount_amount)),
                            'cancel_url' => url('pay/' . $this->plan_id . $this->return_url_parameters('cancel', $base_amount, $formatted_price, $code, $discount_amount))
                        ]
                    ])
                );

                if ($subscription_response->code >= 400) {
                    $paypal_error_message = (DEBUG || \Altum\Authentication::is_admin())
                        ? $subscription_response->body->name . ':' . $subscription_response->body->message
                        : l('pay.error_message.failed_payment');
                    Alerts::add_error($paypal_error_message);
                    redirect('pay/' . $this->plan_id . '?' . $trial_skip_parameter . $discount_code_parameter);
                }

                $paypal_payment_url = $subscription_response->body->links[0]->href;
                header('Location: ' . $paypal_payment_url); die();

                break;
        }

    }

    private function stripe() {

        /* Initiate Stripe configuration */
        \Stripe\Stripe::setApiKey(settings()->stripe->secret_key);
        \Stripe\Stripe::setApiVersion('2023-10-16');

        /* Trial */
        $trial_days = 0;
        if($this->plan->trial_days && !$this->user->plan_trial_done && !isset($_GET['trial_skip'])) {
            $trial_days = $this->plan->trial_days;
        }

        /* Get price details */
        extract($this->get_price_details());

        /* Apply taxes to base price */
        $price_with_taxes = $this->calculate_price_with_taxes($price);

        /* Format price for Stripe amount */
        $stripe_formatted_price = in_array(currency(), [
            'MGA', 'BIF', 'CLP', 'PYG', 'DJF', 'RWF', 'GNF', 'UGX',
            'JPY', 'VND', 'VUV', 'XAF', 'KMF', 'KRW', 'XOF', 'XPF'
        ])
            ? number_format($price_with_taxes, 0, '.', '')
            : number_format($price_with_taxes, 2, '.', '') * 100;

        /* Prepare formatted price string for URLs */
        $formatted_price = number_format($price_with_taxes, 2, '.', '');

        /* Prepare metadata for tracking */
        $stripe_metadata = [
            'user_id' => $this->user->user_id,
            'plan_id' => $this->plan_id,
            'payment_frequency' => $_POST['payment_frequency'],
            'base_amount' => $base_amount,
            'code' => $code,
            'discount_amount' => $discount_amount,
            'taxes_ids' => json_encode($this->applied_taxes_ids)
        ];

        /* Prepare line item for payment/session */
        $stripe_line_item = [
            'price_data' => [
                'currency' => currency(),
                'product_data' => [
                    'name' => settings()->business->brand_name . ' - ' . $this->plan->name,
                    'description' => l('plan.custom_plan.' . $_POST['payment_frequency']),
                ],
                'unit_amount' => $stripe_formatted_price
            ],
            'quantity' => 1
        ];

        /* Add recurring interval for subscription if needed */
        if ($_POST['payment_type'] === 'recurring') {
            $stripe_line_item['price_data']['recurring'] = [
                'interval' => 'day',
                'interval_count' => $payment_frequency_days
            ];
        }

        /* Build the Stripe session payload */
        $stripe_session_data = [
            'mode' => $_POST['payment_type'] === 'recurring' ? 'subscription' : 'payment',
            'customer_email' => $this->user->email,
            'currency' => currency(),
            'line_items' => [ $stripe_line_item ],
            'metadata' => $stripe_metadata,
            'success_url' => url('pay/' . $this->plan_id . $this->return_url_parameters('success', $base_amount, $formatted_price, $code, $discount_amount)),
            'cancel_url' => url('pay/' . $this->plan_id . $this->return_url_parameters('cancel', $base_amount, $formatted_price, $code, $discount_amount)),
        ];

        /* Add subscription data if payment is recurring */
        if ($_POST['payment_type'] === 'recurring') {
            $stripe_session_data['subscription_data'] = [
                'metadata' => $stripe_metadata
            ];

            /* Apply trial days when requested */
            if ($trial_days > 0) {
                /* Collects a payment method now, first charge happens after trial ends */
                $stripe_session_data['subscription_data']['trial_period_days'] = $trial_days;
            }
        }

        /* Generate and redirect to Stripe session */
        try {
            $stripe_session = \Stripe\Checkout\Session::create($stripe_session_data);
        } catch (\Exception $exception) {

            /* Prepare redirect query parameters */
            $trial_skip_parameter = isset($_GET['trial_skip']) ? '&trial_skip=true' : '';
            $discount_code_parameter = isset($_GET['code']) ? '&code=' . $_GET['code'] : '';

            $stripe_error_message = (DEBUG || \Altum\Authentication::is_admin())
                ? $exception->getMessage()
                : l('pay.error_message.failed_payment');
            Alerts::add_error($stripe_error_message);

            redirect('pay/' . $this->plan_id . '?' . $trial_skip_parameter . $discount_code_parameter);
        }

        /* Redirect user to Stripe checkout */
        header('Location: ' . $stripe_session->url); die();

    }

    private function coinbase() {

        /* Get price details */
        extract($this->get_price_details());

        /* Apply taxes to base price */
        $price_with_taxes = $this->calculate_price_with_taxes($price);

        /* Format price */
        $formatted_price = number_format($price_with_taxes, 2, '.', '');

        /* Prepare redirect query parameters */
        $trial_skip_parameter = isset($_GET['trial_skip']) ? '&trial_skip=true' : '';
        $discount_code_parameter = isset($_GET['code']) ? '&code=' . $_GET['code'] : '';

        /* Prepare Coinbase request payload */
        $coinbase_payload = [
            'name' => settings()->business->brand_name . ' - ' . $this->plan->name,
            'description' => l('plan.custom_plan.' . $_POST['payment_frequency']),
            'local_price' => [
                'amount' => $formatted_price,
                'currency' => currency()
            ],
            'pricing_type' => 'fixed_price',
            'metadata' => [
                'user_id' => $this->user->user_id,
                'plan_id' => $this->plan_id,
                'payment_frequency' => $_POST['payment_frequency'],
                'base_amount' => $base_amount,
                'code' => $code,
                'discount_amount' => $discount_amount,
                'taxes_ids' => json_encode($this->applied_taxes_ids)
            ],
            'redirect_url' => url('pay/' . $this->plan_id . $this->return_url_parameters('success', $base_amount, $formatted_price, $code, $discount_amount)),
            'cancel_url' => url('pay/' . $this->plan_id . $this->return_url_parameters('cancel', $base_amount, $formatted_price, $code, $discount_amount)),
        ];

        /* Send charge request to Coinbase */
        $coinbase_response = \Unirest\Request::post(
            \Altum\PaymentGateways\Coinbase::get_api_url() . 'charges',
            \Altum\PaymentGateways\Coinbase::get_headers(),
            \Unirest\Request\Body::json($coinbase_payload)
        );

        /* Handle errors */
        if ($coinbase_response->code >= 400) {
            $coinbase_error_message = (DEBUG || \Altum\Authentication::is_admin())
                ? $coinbase_response->body->error->type . ':' . $coinbase_response->body->error->message
                : l('pay.error_message.failed_payment');
            Alerts::add_error($coinbase_error_message);
            redirect('pay/' . $this->plan_id . '?' . $trial_skip_parameter . $discount_code_parameter);
        }

        /* Redirect to Coinbase hosted payment page */
        header('Location: ' . $coinbase_response->body->data->hosted_url); die();
    }

    private function offline_payment() {

        /* Redirect to thank you page if returning from a successful offline payment */
        if ($this->return_type && $this->payment_processor === 'offline_payment' && $this->return_type === 'success') {
            $this->redirect_pay_thank_you();
        }

        /* Get price details */
        extract($this->get_price_details());

        /* Calculate and format the final price */
        $price_with_taxes = $this->calculate_price_with_taxes($price);
        $formatted_price = number_format($price_with_taxes, 2, '.', '');

        /* Prepare unique payment id */
        $payment_unique_id = md5(
            $this->user->user_id .
            $this->plan_id .
            $_POST['payment_type'] .
            $_POST['payment_frequency'] .
            $this->user->email .
            get_date()
        );

        /* Check if offline payment proof was provided */
        $offline_payment_proof_provided = !empty($_FILES['offline_payment_proof']['name']);

        /* Prepare redirect query parameters */
        $trial_skip_parameter = isset($_GET['trial_skip']) ? '&trial_skip=true' : '';
        $discount_code_parameter = isset($_GET['code']) ? '&code=' . $_GET['code'] : '';

        /* Validate uploaded offline payment proof */
        if (!$offline_payment_proof_provided) {
            Alerts::add_error(l('pay.error_message.offline_payment_proof_missing'));
            redirect('pay/' . $this->plan_id . '?' . $trial_skip_parameter . $discount_code_parameter);
        }

        /* Handle file upload for offline payment proof */
        $offline_payment_proof_file = \Altum\Uploads::process_upload(
            null,
            'offline_payment_proofs',
            'offline_payment_proof',
            'offline_payment_proof_remove',
            settings()->offline_payment->proof_size_limit
        );

        /* If there are field errors, redirect back */
        if (Alerts::has_field_errors() && !Alerts::has_errors()) {
            redirect('pay/' . $this->plan_id . '?' . $trial_skip_parameter . $discount_code_parameter);
        }

        $plan = db()->where('plan_id', $this->plan_id)->getOne('plans', ['plan_id', 'name']);

        /* Insert the payment record into the database */
        $payment_id = db()->insert('payments', [
            'user_id' => $this->user->user_id,
            'plan_id' => $this->plan_id,
            'processor' => 'offline_payment',
            'type' => $_POST['payment_type'],
            'frequency' => $_POST['payment_frequency'],
            'code' => $code,
            'discount_amount' => $discount_amount,
            'base_amount' => $base_amount,
            'email' => $this->user->email,
            'payment_id' => $payment_unique_id,
            'name' => $this->user->name,
            'plan' => json_encode($plan),
            'billing' => settings()->payment->taxes_and_billing_is_enabled && $this->user->billing ? json_encode($this->user->billing) : null,
            'business' => json_encode(settings()->business),
            'taxes_ids' => !empty($this->applied_taxes_ids) ? json_encode($this->applied_taxes_ids) : null,
            'total_amount' => $formatted_price,
            'currency' => currency(),
            'payment_proof' => $offline_payment_proof_file,
            'status' => 0,
            'datetime' => get_date()
        ]);

        /* Send admin notification if enabled */
        if (settings()->email_notifications->new_payment && !empty(settings()->email_notifications->emails)) {

            $email_template = get_email_template(
                [
                    '{{PROCESSOR}}' => l('pay.custom_plan.offline_payment'),
                    '{{TOTAL_AMOUNT}}' => $formatted_price,
                    '{{CURRENCY}}' => currency()
                ],
                l('global.emails.admin_new_payment_notification.subject'),
                [
                    '{{PROCESSOR}}' => l('pay.custom_plan.offline_payment'),
                    '{{TOTAL_AMOUNT}}' => $formatted_price,
                    '{{CURRENCY}}' => currency(),
                    '{{NAME}}' => $this->user->name,
                    '{{EMAIL}}' => $this->user->email,
                    '{{PLAN_NAME}}' => $plan->name,
                    '{{PAYMENT_FREQUENCY}}' => l('plan.custom_plan.' . $_POST['payment_frequency']),
                    '{{PAYMENT_TYPE}}' => l('pay.custom_plan.' . $_POST['payment_type'] . '_type'),
                    '{{PAYMENT_ID}}' => $payment_id,
                    '{{EXTERNAL_PAYMENT_ID}}' => $payment_unique_id,
                    '{{PAYMENT_LINK}}' => url('admin/payments?id=' . $payment_id),
                    '{{DATE}}' => get_date(),
                    '{{DATE_TIMEZONE}}' => \Altum\Date::$default_timezone,
                    '{{CODE}}' => $code ?: l('global.none'),
                    '{{DISCOUNT_AMOUNT}}' => $discount_amount,
                    '{{PAYMENT_STATUS}}' => l('account_payments.status_pending'),
                ],
                l('global.emails.admin_new_payment_notification.body')
            );

            send_mail(
                explode(',', settings()->email_notifications->emails),
                $email_template->subject,
                $email_template->body
            );
        }

        /* Redirect to the thank you page after offline payment submission */
        redirect('pay/' . $this->plan_id . $this->return_url_parameters('success', $base_amount, $formatted_price, $code, $discount_amount));
    }

    private function payu() {

        /* Get price details */
        extract($this->get_price_details());

        /* Apply taxes to base price */
        $price_with_taxes = $this->calculate_price_with_taxes($price);

        /* Format final price */
        $formatted_price = number_format($price_with_taxes, 2, '.', '');

        /* Prepare redirect query parameters */
        $trial_skip_parameter = isset($_GET['trial_skip']) ? '&trial_skip=true' : '';
        $discount_code_parameter = isset($_GET['code']) ? '&code=' . $_GET['code'] : '';

        /* Configure PayU environment and credentials */
        \OpenPayU_Configuration::setEnvironment(settings()->payu->mode);
        \OpenPayU_Configuration::setMerchantPosId(settings()->payu->merchant_pos_id);
        \OpenPayU_Configuration::setSignatureKey(settings()->payu->signature_key);
        \OpenPayU_Configuration::setOauthClientId(settings()->payu->oauth_client_id);
        \OpenPayU_Configuration::setOauthClientSecret(settings()->payu->oauth_client_secret);
        \OpenPayU_Configuration::setOauthTokenCache(new \OauthCacheFile(UPLOADS_PATH . 'cache'));

        /* Generate unique payment id */
        $payu_payment_id = md5(
            $this->user->user_id .
            $this->plan_id .
            $_POST['payment_type'] .
            $_POST['payment_frequency'] .
            $this->user->email .
            get_date()
        );

        /* Prepare PayU order data */
        $payu_order_data = [
            'notifyUrl' => SITE_URL . 'webhook-payu',
            'continueUrl' => url('pay/' . $this->plan_id . $this->return_url_parameters('success', $base_amount, $formatted_price, $code, $discount_amount)),
            'customerIp' => get_ip(),
            'merchantPosId' => \OpenPayU_Configuration::getOauthClientId() ?: \OpenPayU_Configuration::getMerchantPosId(),
            'description' => settings()->business->brand_name . ' - ' . $this->plan->name . ' - ' . l('plan.custom_plan.' . $_POST['payment_frequency']),
            'currencyCode' => currency(),
            'totalAmount' => $formatted_price * 100,
            'extOrderId' => $payu_payment_id,
            'products' => [
                [
                    'name' => settings()->business->brand_name . ' - ' . $this->plan->name . ' - ' . l('plan.custom_plan.' . $_POST['payment_frequency']),
                    'unitPrice' => $formatted_price * 100,
                    'quantity' => 1
                ]
            ],
            'buyer' => [
                'email' => $this->user->email,
                'firstName' => $this->user->name
            ]
        ];

        try {
            $payu_response = \OpenPayU_Order::create($payu_order_data);
            $payu_status_description = \OpenPayU_Util::statusDesc($payu_response->getStatus());

            if ($payu_response->getStatus() !== 'SUCCESS') {
                $payu_error_message = (DEBUG || \Altum\Authentication::is_admin())
                    ? $payu_status_description
                    : l('pay.error_message.failed_payment');
                Alerts::add_error($payu_error_message);
                redirect('pay/' . $this->plan_id . '?' . $trial_skip_parameter . $discount_code_parameter);
            }

            /* Log payment intent to database */
            db()->insert('payments', [
                'user_id' => $this->user->user_id,
                'plan_id' => $this->plan_id,
                'processor' => 'payu',
                'type' => $_POST['payment_type'],
                'frequency' => $_POST['payment_frequency'],
                'code' => $code,
                'discount_amount' => $discount_amount,
                'base_amount' => $base_amount,
                'email' => $this->user->email,
                'payment_id' => $payu_payment_id,
                'name' => $this->user->name,
                'plan' => json_encode(db()->where('plan_id', $this->plan_id)->getOne('plans', ['plan_id', 'name'])),
                'billing' => settings()->payment->taxes_and_billing_is_enabled && $this->user->billing ? json_encode($this->user->billing) : null,
                'business' => json_encode(settings()->business),
                'taxes_ids' => !empty($this->applied_taxes_ids) ? json_encode($this->applied_taxes_ids) : null,
                'total_amount' => $formatted_price,
                'currency' => currency(),
                'status' => 0,
                'datetime' => get_date()
            ]);

            /* Redirect to PayU payment page */
            header('Location: ' . $payu_response->getResponse()->redirectUri); die();

        } catch (\OpenPayU_Exception $exception) {
            $payu_error_message = (DEBUG || \Altum\Authentication::is_admin())
                ? $exception->getMessage()
                : l('pay.error_message.failed_payment');
            Alerts::add_error($payu_error_message);
            redirect('pay/' . $this->plan_id . '?' . $trial_skip_parameter . $discount_code_parameter);
        }
    }

    private function iyzico() {

        /* Get price details */
        extract($this->get_price_details());

        /* Apply taxes to base price */
        $price_with_taxes = $this->calculate_price_with_taxes($price);

        /* Format price */
        $formatted_price = number_format($price_with_taxes, 2, '.', '');

        /* Prepare redirect query parameters */
        $trial_skip_parameter = isset($_GET['trial_skip']) ? '&trial_skip=true' : '';
        $discount_code_parameter = isset($_GET['code']) ? '&code=' . $_GET['code'] : '';

        /* Generate custom payment ID */
        $custom_payment_id = md5(
            $this->user->user_id .
            $this->plan_id .
            $_POST['payment_type'] .
            $_POST['payment_frequency'] .
            $this->user->email .
            get_date()
        );

        /* Log the payment intent */
        $payment_log_id = db()->insert('payments', [
            'user_id' => $this->user->user_id,
            'plan_id' => $this->plan_id,
            'processor' => 'iyzico',
            'type' => $_POST['payment_type'],
            'frequency' => $_POST['payment_frequency'],
            'code' => $code,
            'discount_amount' => $discount_amount,
            'base_amount' => $base_amount,
            'email' => $this->user->email,
            'payment_id' => $custom_payment_id,
            'name' => $this->user->name,
            'plan' => json_encode(db()->where('plan_id', $this->plan_id)->getOne('plans', ['plan_id', 'name'])),
            'billing' => (settings()->payment->taxes_and_billing_is_enabled && $this->user->billing)
                ? json_encode($this->user->billing) : null,
            'business' => json_encode(settings()->business),
            'taxes_ids' => !empty($this->applied_taxes_ids) ? json_encode($this->applied_taxes_ids) : null,
            'total_amount' => $formatted_price,
            'currency' => currency(),
            'status' => 0,
            'datetime' => get_date()
        ]);

        /* Set up Iyzico API options */
        $iyzico_options = new \Iyzipay\Options();
        $iyzico_options->setApiKey(settings()->iyzico->api_key);
        $iyzico_options->setSecretKey(settings()->iyzico->secret_key);
        $iyzico_options->setBaseUrl(
            settings()->iyzico->mode === 'live'
                ? 'https://api.iyzipay.com'
                : 'https://sandbox-api.iyzipay.com'
        );

        /* Build the product creation request */
        $iyzico_product_request = new \Iyzipay\Request\Iyzilink\IyziLinkSaveProductRequest();
        $iyzico_product_request->setLocale(\Iyzipay\Model\Locale::EN);
        $iyzico_product_request->setConversationId($payment_log_id);
        $iyzico_product_request->setName(currency());
        $iyzico_product_request->setDescription(
            settings()->business->brand_name .
            ' - ' . $this->plan->name .
            ' - ' . l('plan.custom_plan.' . $_POST['payment_frequency'])
        );
        $iyzico_product_request->setBase64EncodedImage('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==');
        $iyzico_product_request->setPrice($formatted_price);
        $iyzico_product_request->setCurrency(currency());
        $iyzico_product_request->setAddressIgnorable(false);
        $iyzico_product_request->setSoldLimit(1);
        $iyzico_product_request->setInstallmentRequest(false);

        /* Send product creation request to Iyzico */
        $iyzico_response = \Iyzipay\Model\Iyzilink\IyziLinkSaveProduct::create(
            $iyzico_product_request,
            $iyzico_options
        );

        if ($iyzico_response->getStatus() !== 'success') {
            $iyzico_error_message = (DEBUG || \Altum\Authentication::is_admin())
                ? $iyzico_response->getErrorCode() . ':' . $iyzico_response->getErrorMessage()
                : l('pay.error_message.failed_payment');
            Alerts::add_error($iyzico_error_message);
            redirect('pay/' . $this->plan_id . '?' . $trial_skip_parameter . $discount_code_parameter);
        }

        /* Redirect user to Iyzico payment page */
        header('Location: ' . $iyzico_response->getUrl()); die();
    }

    private function paystack() {

        /* Set Paystack secret key */
        Paystack::$secret_key = settings()->paystack->secret_key;

        /* Get price details */
        extract($this->get_price_details());

        /* Apply taxes to base price */
        $price_with_taxes = $this->calculate_price_with_taxes($price);

        /* Format price as string with two decimals */
        $formatted_price = number_format($price_with_taxes, 2, '.', '');

        /* Prepare redirect query parameters */
        $trial_skip_parameter = isset($_GET['trial_skip']) ? '&trial_skip=true' : '';
        $discount_code_parameter = isset($_GET['code']) ? '&code=' . $_GET['code'] : '';

        switch ($_POST['payment_type']) {

            case 'one_time':

                /* Prepare one-time payment initialization payload */
                $paystack_one_time_payload = [
                    'key' => settings()->paystack->public_key,
                    'email' => $this->user->email,
                    'first_name' => $this->user->name,
                    'amount' => (int) ($formatted_price * 100),
                    'currency' => currency(),
                    'metadata' => [
                        'user_id' => $this->user->user_id,
                        'plan_id' => $this->plan_id,
                        'payment_frequency' => $_POST['payment_frequency'],
                        'base_amount' => $base_amount,
                        'code' => $code,
                        'discount_amount' => $discount_amount,
                        'taxes_ids' => json_encode($this->applied_taxes_ids)
                    ],
                    'callback_url' => url('pay/' . $this->plan_id . $this->return_url_parameters('success', $base_amount, $formatted_price, $code, $discount_amount))
                ];

                /* Create one-time payment link */
                $paystack_response = \Unirest\Request::post(
                    Paystack::$api_url . 'transaction/initialize',
                    Paystack::get_headers(),
                    \Unirest\Request\Body::json($paystack_one_time_payload)
                );

                if (!$paystack_response->body->status) {
                    $paystack_error_message = (DEBUG || \Altum\Authentication::is_admin())
                        ? $paystack_response->body->message
                        : l('pay.error_message.failed_payment');
                    Alerts::add_error($paystack_error_message);
                    redirect('pay/' . $this->plan_id . '?' . $trial_skip_parameter . $discount_code_parameter);
                }

                header('Location: ' . $paystack_response->body->data->authorization_url); die();

            case 'recurring':

                /* Prepare Paystack plan payload */
                $paystack_plan_payload = [
                    'name' => $this->plan->name,
                    'interval' => match ($_POST['payment_frequency']) {
                        'annual' => 'annually',
                        default => $_POST['payment_frequency']
                    },
                    'amount' => (int) ($formatted_price * 100),
                    'currency' => currency()
                ];

                /* Create Paystack plan */
                $paystack_plan_response = \Unirest\Request::post(
                    Paystack::$api_url . 'plan',
                    Paystack::get_headers(),
                    \Unirest\Request\Body::json($paystack_plan_payload)
                );

                if (!$paystack_plan_response->body->status) {
                    $paystack_error_message = (DEBUG || \Altum\Authentication::is_admin())
                        ? $paystack_plan_response->body->message
                        : l('pay.error_message.failed_payment');
                    Alerts::add_error($paystack_error_message);
                    redirect('pay/' . $this->plan_id . '?' . $trial_skip_parameter . $discount_code_parameter);
                }

                $paystack_plan_code = $paystack_plan_response->body->data->plan_code;

                /* Prepare recurring payment initialization payload */
                $paystack_recurring_payload = [
                    'key' => settings()->paystack->public_key,
                    'email' => $this->user->email,
                    'first_name' => $this->user->name,
                    'currency' => currency(),
                    'amount' => (int) ($formatted_price * 100),
                    'plan' => $paystack_plan_code,
                    'metadata' => [
                        'user_id' => $this->user->user_id,
                        'plan_id' => $this->plan_id,
                        'payment_frequency' => $_POST['payment_frequency'],
                        'base_amount' => $base_amount,
                        'code' => $code,
                        'discount_amount' => $discount_amount,
                        'taxes_ids' => json_encode($this->applied_taxes_ids)
                    ],
                    'callback_url' => url('pay/' . $this->plan_id . $this->return_url_parameters('success', $base_amount, $formatted_price, $code, $discount_amount))
                ];

                /* Create recurring payment link */
                $paystack_recurring_response = \Unirest\Request::post(
                    Paystack::$api_url . 'transaction/initialize',
                    Paystack::get_headers(),
                    \Unirest\Request\Body::json($paystack_recurring_payload)
                );

                if (!$paystack_recurring_response->body->status) {
                    $paystack_error_message = (DEBUG || \Altum\Authentication::is_admin())
                        ? $paystack_recurring_response->body->message
                        : l('pay.error_message.failed_payment');
                    Alerts::add_error($paystack_error_message);
                    redirect('pay/' . $this->plan_id . '?' . $trial_skip_parameter . $discount_code_parameter);
                }

                header('Location: ' . $paystack_recurring_response->body->data->authorization_url); die();
        }

        die();
    }

    private function razorpay() {

        /* Initialize Razorpay API */
        $razorpay_api = new \Razorpay\Api\Api(settings()->razorpay->key_id, settings()->razorpay->key_secret);

        /* Get price details */
        extract($this->get_price_details());

        /* Apply taxes to base price */
        $price_with_taxes = $this->calculate_price_with_taxes($price);

        /* Format price */
        $formatted_price = number_format($price_with_taxes, 2, '.', '');

        /* Prepare redirect query parameters */
        $trial_skip_parameter = isset($_GET['trial_skip']) ? '&trial_skip=true' : '';
        $discount_code_parameter = isset($_GET['code']) ? '&code=' . $_GET['code'] : '';

        switch ($_POST['payment_type']) {

            case 'one_time':

                /* Create one-time payment link */
                try {
                    $razorpay_payment_link_response = $razorpay_api->paymentLink->create([
                        'amount' => $formatted_price * 100,
                        'currency' => currency(),
                        'accept_partial' => false,
                        'description' => settings()->business->brand_name . ' - ' . $this->plan->name . ' - ' . l('plan.custom_plan.' . $_POST['payment_frequency']),
                        'customer' => [
                            'name' => $this->user->name,
                            'email' => $this->user->email
                        ],
                        'notify' => [
                            'sms' => false,
                            'email' => false
                        ],
                        'reminder_enable' => false,
                        'notes' => [
                            'user_id' => $this->user->user_id,
                            'plan_id' => $this->plan_id,
                            'payment_frequency' => $_POST['payment_frequency'],
                            'base_amount' => $base_amount,
                            'code' => $code,
                            'discount_amount' => $discount_amount,
                            'taxes_ids' => json_encode($this->applied_taxes_ids)
                        ],
                        'callback_url' => url('pay/' . $this->plan_id . $this->return_url_parameters('success', $base_amount, $formatted_price, $code, $discount_amount)),
                        'callback_method' => 'get'
                    ]);
                } catch (\Exception $exception) {
                    $razorpay_error_message = (DEBUG || \Altum\Authentication::is_admin()) ? $exception->getMessage() : l('pay.error_message.failed_payment');
                    Alerts::add_error($razorpay_error_message);
                    redirect('pay/' . $this->plan_id . '?' . $trial_skip_parameter . $discount_code_parameter);
                }

                header('Location: ' . $razorpay_payment_link_response['short_url']); die();

            case 'recurring':

                /* Create Razorpay plan */
                try {
                    $razorpay_plan_response = $razorpay_api->plan->create([
                        'period' => 'daily',
                        'interval' => $payment_frequency_days,
                        'item' => [
                            'name' => $this->plan->name,
                            'description' => settings()->business->brand_name . ' - ' . $this->plan->name . ' - ' . l('plan.custom_plan.' . $_POST['payment_frequency']),
                            'amount' => $formatted_price * 100,
                            'currency' => currency()
                        ]
                    ]);
                } catch (\Exception $exception) {
                    $razorpay_error_message = (DEBUG || \Altum\Authentication::is_admin()) ? $exception->getMessage() : l('pay.error_message.failed_payment');
                    Alerts::add_error($razorpay_error_message);
                    redirect('pay/' . $this->plan_id . '?' . $trial_skip_parameter . $discount_code_parameter);
                }

                /* Define total_count for auto-expiration */
                $total_count = match ($_POST['payment_frequency']) {
                    'monthly' => 1200,
                    'quarterly' => 400,
                    'biannual' => 600,
                    'annual' => 100,
                    default => 1200
                };

                /* Create Razorpay subscription */
                try {
                    $razorpay_subscription_response = $razorpay_api->subscription->create([
                        'plan_id' => $razorpay_plan_response['id'],
                        'quantity' => 1,
                        'total_count' => $total_count,
                        'notes' => [
                            'user_id' => $this->user->user_id,
                            'plan_id' => $this->plan_id,
                            'payment_frequency' => $_POST['payment_frequency'],
                            'base_amount' => $base_amount,
                            'code' => $code,
                            'discount_amount' => $discount_amount,
                            'taxes_ids' => json_encode($this->applied_taxes_ids)
                        ]
                    ]);
                } catch (\Exception $exception) {
                    $razorpay_error_message = (DEBUG || \Altum\Authentication::is_admin()) ? $exception->getMessage() : l('pay.error_message.failed_payment');
                    Alerts::add_error($razorpay_error_message);
                    redirect('pay/' . $this->plan_id . '?' . $trial_skip_parameter . $discount_code_parameter);
                }

                header('Location: ' . $razorpay_subscription_response['short_url']); die();
        }

        die();
    }

    private function mollie() {

        /* Initialize Mollie client */
        $mollie_client = new \Mollie\Api\MollieApiClient();
        $mollie_client->setApiKey(settings()->mollie->api_key);

        /* Get price details */
        extract($this->get_price_details());

        /* Apply taxes to base price */
        $price_with_taxes = $this->calculate_price_with_taxes($price);

        /* Format price to two decimals */
        $formatted_price = number_format($price_with_taxes, 2, '.', '');

        /* Prepare redirect query parameters */
        $trial_skip_parameter = isset($_GET['trial_skip']) ? '&trial_skip=true' : '';
        $discount_code_parameter = isset($_GET['code']) ? '&code=' . $_GET['code'] : '';

        /* Shared payment data for Mollie */
        $mollie_payment_data = [
            'amount' => [
                'currency' => currency(),
                'value' => $formatted_price,
            ],
            'description' => settings()->business->brand_name . ' - ' . $this->plan->name . ' - ' . l('plan.custom_plan.' . $_POST['payment_frequency']),
            'metadata' => [
                'user_id' => $this->user->user_id,
                'plan_id' => $this->plan_id,
                'payment_frequency' => $_POST['payment_frequency'],
                'base_amount' => $base_amount,
                'code' => $code,
                'discount_amount' => $discount_amount,
                'taxes_ids' => json_encode($this->applied_taxes_ids)
            ],
            'redirectUrl' => url('pay/' . $this->plan_id . $this->return_url_parameters('success', $base_amount, $formatted_price, $code, $discount_amount)),
            'webhookUrl' => SITE_URL . 'webhook-mollie'
        ];

        switch ($_POST['payment_type']) {

            case 'one_time':

                try {
                    $mollie_payment = $mollie_client->payments->create($mollie_payment_data);
                } catch (\Exception $exception) {
                    $mollie_error_message = (DEBUG || \Altum\Authentication::is_admin())
                        ? $exception->getMessage()
                        : l('pay.error_message.failed_payment');
                    Alerts::add_error($mollie_error_message);
                    redirect('pay/' . $this->plan_id . '?' . $trial_skip_parameter . $discount_code_parameter);
                }

                header('Location: ' . $mollie_payment->getCheckoutUrl()); die();

            case 'recurring':

                /* Create Mollie customer for recurring payments */
                try {
                    $mollie_customer = $mollie_client->customers->create([
                        'name' => $this->user->name,
                        'email' => $this->user->email,
                    ]);
                } catch (\Exception $exception) {
                    $mollie_error_message = (DEBUG || \Altum\Authentication::is_admin())
                        ? $exception->getMessage()
                        : l('pay.error_message.failed_payment');
                    Alerts::add_error($mollie_error_message);
                    redirect('pay/' . $this->plan_id . '?' . $trial_skip_parameter . $discount_code_parameter);
                }

                /* Create the first recurring payment */
                try {
                    $mollie_payment = $mollie_customer->createPayment(array_merge($mollie_payment_data, [
                        'sequenceType' => 'first'
                    ]));
                } catch (\Exception $exception) {
                    $mollie_error_message = (DEBUG || \Altum\Authentication::is_admin())
                        ? $exception->getMessage()
                        : l('pay.error_message.failed_payment');
                    Alerts::add_error($mollie_error_message);
                    redirect('pay/' . $this->plan_id . '?' . $trial_skip_parameter . $discount_code_parameter);
                }

                header('Location: ' . $mollie_payment->getCheckoutUrl()); die();
        }

        die();
    }

    private function crypto_com() {

        /* Get price details */
        extract($this->get_price_details());

        /* Apply taxes to base price */
        $price_with_taxes = $this->calculate_price_with_taxes($price);

        /* Convert price to smallest currency unit (integer) */
        $final_price_smallest_unit = number_format($price_with_taxes, 2, '.', '') * 100;

        /* Prepare redirect query parameters */
        $trial_skip_parameter = isset($_GET['trial_skip']) ? '&trial_skip=true' : '';
        $discount_code_parameter = isset($_GET['code']) ? '&code=' . $_GET['code'] : '';

        switch ($_POST['payment_type']) {

            case 'one_time':

                /* Authenticate Crypto.com API */
                \Unirest\Request::auth(settings()->crypto_com->secret_key, '');

                /* Prepare Crypto.com payment payload */
                $crypto_com_payment_payload = [
                    'description' => settings()->business->brand_name . ' - ' . $this->plan->name . ' - ' . l('plan.custom_plan.' . $_POST['payment_frequency']),
                    'amount' => $final_price_smallest_unit,
                    'currency' => currency(),
                    'metadata' => json_encode([
                        'user_id' => $this->user->user_id,
                        'plan_id' => $this->plan_id,
                        'payment_frequency' => $_POST['payment_frequency'],
                        'base_amount' => $base_amount,
                        'code' => $code,
                        'discount_amount' => $discount_amount,
                        'taxes_ids' => $this->applied_taxes_ids
                    ]),
                    'return_url' => url('pay/' . $this->plan_id . $this->return_url_parameters('success', $base_amount, $final_price_smallest_unit / 100, $code, $discount_amount)),
                    'cancel_url' => url('pay/' . $this->plan_id . $this->return_url_parameters('cancel', $base_amount, $final_price_smallest_unit / 100, $code, $discount_amount))
                ];

                /* Make payment request to Crypto.com */
                $crypto_com_response = \Unirest\Request::post(
                    'https://pay.crypto.com/api/payments',
                    [],
                    \Unirest\Request\Body::Form($crypto_com_payment_payload)
                );

                /* Handle errors */
                if ($crypto_com_response->code >= 400) {
                    $crypto_com_error_message = (DEBUG || \Altum\Authentication::is_admin())
                        ? $crypto_com_response->body->error->type . ':' . $crypto_com_response->body->error->error_message
                        : l('pay.error_message.failed_payment');
                    Alerts::add_error($crypto_com_error_message);
                    redirect('pay/' . $this->plan_id . '?' . $trial_skip_parameter . $discount_code_parameter);
                }

                /* Redirect to Crypto.com payment page */
                header('Location: ' . $crypto_com_response->body->payment_url); die();

        }
    }

    private function paddle() {

        /* Get price details */
        extract($this->get_price_details());

        /* Apply taxes to base price */
        $price_with_taxes = $this->calculate_price_with_taxes($price);

        /* Format final price */
        $formatted_price = number_format($price_with_taxes, 2, '.', '');

        /* Prepare redirect query parameters */
        $trial_skip_parameter = isset($_GET['trial_skip']) ? '&trial_skip=true' : '';
        $discount_code_parameter = isset($_GET['code']) ? '&code=' . $_GET['code'] : '';

        switch ($_POST['payment_type']) {

            case 'one_time':

                /* Build passthrough string for Paddle tracking */
                $paddle_custom_id = $this->user->user_id . '&' . $this->plan_id . '&' . $_POST['payment_frequency'] . '&' . $base_amount . '&' . $code . '&' . $discount_amount . '&' . json_encode($this->applied_taxes_ids);

                /* Set logo image url for Paddle checkout */
                $paddle_image_url = settings()->main->{'logo_' . \Altum\ThemeStyle::get()} !== ''
                    ? settings()->main->{'logo_' . \Altum\ThemeStyle::get() . '_full_url'}
                    : '';

                /* Prepare Paddle pay link API payload */
                $paddle_pay_link_payload = [
                    'vendor_id' => settings()->paddle->vendor_id,
                    'vendor_auth_code' => settings()->paddle->api_key,
                    'title' => settings()->business->brand_name . ' - ' . $this->plan->name . ' - ' . l('plan.custom_plan.' . $_POST['payment_frequency']),
                    'webhook_url' => SITE_URL . 'webhook-paddle',
                    'prices' => [currency() . ':' . $formatted_price],
                    'customer_email' => $this->user->email,
                    'passthrough' => $paddle_custom_id,
                    'return_url' => url('pay/' . $this->plan_id . $this->return_url_parameters('success', $base_amount, $formatted_price, $code, $discount_amount)),
                    'image_url' => $paddle_image_url,
                    'quantity_variable' => 0
                ];

                /* Request Paddle payment link */
                $paddle_response = \Unirest\Request::post(
                    \Altum\PaymentGateways\Paddle::get_api_url() . '2.0/product/generate_pay_link',
                    [],
                    \Unirest\Request\Body::Form($paddle_pay_link_payload)
                );

                /* Handle Paddle API failure */
                if (!$paddle_response->body->success) {
                    $paddle_error_message = (DEBUG || \Altum\Authentication::is_admin())
                        ? $paddle_response->body->error->code . ':' . $paddle_response->body->error->message
                        : l('pay.error_message.failed_payment');
                    Alerts::add_error($paddle_error_message);
                    redirect('pay/' . $this->plan_id . '?' . $trial_skip_parameter . $discount_code_parameter);
                }

                /* Store the generated payment link */
                $this->payment_extra_data = [
                    'payment_processor' => 'paddle',
                    'url' => $paddle_response->body->response->url
                ];

                break;
        }

    }

    private function yookassa() {

        /* Initialize YooKassa client */
        $yookassa_client = new \YooKassa\Client();
        $yookassa_client->setAuth(settings()->yookassa->shop_id, settings()->yookassa->secret_key);

        /* Get price details */
        extract($this->get_price_details());

        /* Apply taxes to base price */
        $price_with_taxes = $this->calculate_price_with_taxes($price);

        /* Format price to string with two decimals */
        $formatted_price = number_format($price_with_taxes, 2, '.', '');

        /* Prepare redirect query parameters */
        $trial_skip_parameter = isset($_GET['trial_skip']) ? '&trial_skip=true' : '';
        $discount_code_parameter = isset($_GET['code']) ? '&code=' . $_GET['code'] : '';

        switch ($_POST['payment_type']) {

            case 'one_time':

                try {
                    /* Prepare payment metadata */
                    $yookassa_payment_metadata = [
                        'user_id' => $this->user->user_id,
                        'plan_id' => $this->plan_id,
                        'payment_frequency' => $_POST['payment_frequency'],
                        'base_amount' => $base_amount,
                        'code' => $code,
                        'discount_amount' => $discount_amount,
                        'taxes_ids' => json_encode($this->applied_taxes_ids)
                    ];

                    /* Prepare payment receipt item */
                    $yookassa_receipt_item = [
                        'description' => settings()->business->brand_name . ' - ' . $this->plan->name . ' - ' . l('plan.custom_plan.' . $_POST['payment_frequency']),
                        'quantity' => 1,
                        'amount' => [
                            'currency' => currency(),
                            'value' => $formatted_price
                        ],
                        'vat_code' => 1,
                        'payment_subject' => 'commodity',
                        'payment_mode' => 'full_payment'
                    ];

                    /* Prepare payment request payload */
                    $yookassa_payment_payload = [
                        'amount' => [
                            'currency' => currency(),
                            'value' => $formatted_price
                        ],
                        'description' => settings()->business->brand_name . ' - ' . $this->plan->name . ' - ' . l('plan.custom_plan.' . $_POST['payment_frequency']),
                        'metadata' => $yookassa_payment_metadata,
                        'receipt' => [
                            'customer' => [
                                'email' => $this->user->email
                            ],
                            'items' => [
                                $yookassa_receipt_item
                            ]
                        ],
                        'confirmation' => [
                            'type' => 'redirect',
                            'return_url' => url('pay/' . $this->plan_id . $this->return_url_parameters('success', $base_amount, $formatted_price, $code, $discount_amount))
                        ],
                        'capture' => true
                    ];

                    /* Create YooKassa payment */
                    $yookassa_payment = $yookassa_client->createPayment(
                        $yookassa_payment_payload,
                        uniqid('', true)
                    );

                } catch (\Exception $exception) {
                    $yookassa_error_message = (DEBUG || \Altum\Authentication::is_admin())
                        ? $exception->getMessage()
                        : l('pay.error_message.failed_payment');
                    Alerts::add_error($yookassa_error_message);
                    redirect('pay/' . $this->plan_id . '?' . $trial_skip_parameter . $discount_code_parameter);
                }

                header('Location: ' . $yookassa_payment->getConfirmation()->getConfirmationUrl()); die();
        }

        die();
    }

    private function mercadopago() {

        /* Get price details */
        extract($this->get_price_details());

        /* Apply taxes to base price */
        $price_with_taxes = $this->calculate_price_with_taxes($price);

        /* Format price */
        $formatted_price = number_format($price_with_taxes, 2, '.', '');

        /* Prepare redirect query parameters */
        $trial_skip_parameter = isset($_GET['trial_skip']) ? '&trial_skip=true' : '';
        $discount_code_parameter = isset($_GET['code']) ? '&code=' . $_GET['code'] : '';

        switch ($_POST['payment_type']) {

            case 'one_time':

                /* Build unique custom id */
                $mercadopago_custom_id = $this->user->user_id . '&' . $this->plan_id . '&' . $_POST['payment_frequency'] . '&' . $base_amount . '&' . $code . '&' . $discount_amount . '&' . json_encode($this->applied_taxes_ids);

                /* Build MercadoPago payload */
                $mercadopago_payload = [
                    'items' => [[
                        'id' => $this->plan->plan_id,
                        'title' => settings()->business->brand_name . ' - ' . $this->plan->name . ' - ' . l('plan.custom_plan.' . $_POST['payment_frequency']),
                        'quantity' => 1,
                        'currency_id' => currency(),
                        'unit_price' => (float) $formatted_price
                    ]],
                    'back_urls' => [
                        'success' => url('pay/' . $this->plan_id . $this->return_url_parameters('success', $base_amount, $formatted_price, $code, $discount_amount)),
                        'pending' => url('pay/' . $this->plan_id . $this->return_url_parameters('success', $base_amount, $formatted_price, $code, $discount_amount)),
                        'failure' => url('pay/' . $this->plan_id . $this->return_url_parameters('cancel', $base_amount, $formatted_price, $code, $discount_amount))
                    ],
                    'external_reference' => $mercadopago_custom_id,
                    'notification_url' => SITE_URL . 'webhook-mercadopago'
                ];

                $mercadopago_response = \Unirest\Request::post(
                    'https://api.mercadopago.com/checkout/preferences',
                    [
                        'Authorization' => 'Bearer ' . settings()->mercadopago->access_token,
                        'Content-Type' => 'application/json'
                    ],
                    \Unirest\Request\Body::json($mercadopago_payload)
                );

                /* Handle MercadoPago errors */
                if ($mercadopago_response->code >= 400) {
                    $mercadopago_error_message = (DEBUG || \Altum\Authentication::is_admin())
                        ? $mercadopago_response->body->error . ':' . $mercadopago_response->body->message
                        : l('pay.error_message.failed_payment');

                    Alerts::add_error($mercadopago_error_message);
                    redirect('pay/' . $this->plan_id . '?' . $trial_skip_parameter . $discount_code_parameter);
                }

                /* Redirect to MercadoPago checkout */
                header('Location: ' . $mercadopago_response->body->init_point); die();

                break;
        }
    }

    private function midtrans() {

        /* Get price details */
        extract($this->get_price_details());

        /* Apply taxes to base price */
        $price_with_taxes = $this->calculate_price_with_taxes($price);

        /* Format price as integer (Midtrans requirement) */
        $formatted_price = number_format($price_with_taxes, 0, '.', '');

        /* Prepare redirect query parameters */
        $trial_skip_parameter = isset($_GET['trial_skip']) ? '&trial_skip=true' : '';
        $discount_code_parameter = isset($_GET['code']) ? '&code=' . $_GET['code'] : '';

        switch ($_POST['payment_type']) {

            case 'one_time':

                /* Generate custom identifiers for tracking */
                $midtrans_custom_id = $this->user->user_id . '&' . $this->plan_id . '&' . $_POST['payment_frequency'] . '&' . $base_amount . '&' . $code . '&' . $discount_amount . '&' . json_encode($this->applied_taxes_ids);
                $midtrans_payment_id = md5($this->user->user_id . $this->plan_id . $_POST['payment_type'] . $_POST['payment_frequency'] . $this->user->email . get_date());

                /* Build Midtrans API URL */
                $midtrans_api_url = 'https://api' . (settings()->midtrans->mode == 'sandbox' ? 'sandbox' : '') . '.midtrans.com/v1/payment-links';

                /* Prepare API headers */
                $midtrans_headers = [
                    'Authorization' => 'Basic ' . base64_encode(settings()->midtrans->server_key . ':'),
                    'Content-Type' => 'application/json',
                    'X-Override-Notification' => SITE_URL . 'webhook-midtrans'
                ];

                /* Prepare API request body */
                $midtrans_body = [
                    'transaction_details' => [
                        'order_id' => md5($midtrans_payment_id),
                        'gross_amount' => $formatted_price
                    ],
                    'expiry' => [
                        'duration' => 1,
                        'unit' => 'days'
                    ],
                    'item_details' => [[
                        'price' => $formatted_price,
                        'quantity' => 1,
                        'name' => settings()->business->brand_name . ' - ' . $this->plan->name . ' - ' . l('plan.custom_plan.' . $_POST['payment_frequency'])
                    ]],
                    'custom_field1' => $midtrans_custom_id
                ];

                /* Send request to Midtrans */
                $midtrans_response = \Unirest\Request::post(
                    $midtrans_api_url,
                    $midtrans_headers,
                    \Unirest\Request\Body::json($midtrans_body)
                );

                /* Handle errors */
                if ($midtrans_response->code >= 400) {
                    $midtrans_error_message = (DEBUG || \Altum\Authentication::is_admin())
                        ? $midtrans_response->body->error_messages
                        : l('pay.error_message.failed_payment');

                    Alerts::add_error($midtrans_error_message);
                    redirect('pay/' . $this->plan_id . '?' . $trial_skip_parameter . $discount_code_parameter);
                }

                /* Redirect to Midtrans payment URL */
                header('Location: ' . $midtrans_response->body->payment_url); die();

        }
    }

    private function flutterwave() {

        /* Get price details */
        extract($this->get_price_details());

        /* Apply taxes to base price */
        $price_with_taxes = $this->calculate_price_with_taxes($price);

        /* Format price as integer for API */
        $formatted_price = number_format($price_with_taxes, 0, '.', '');

        /* Prepare redirect query parameters */
        $trial_skip_parameter = isset($_GET['trial_skip']) ? '&trial_skip=true' : '';
        $discount_code_parameter = isset($_GET['code']) ? '&code=' . $_GET['code'] : '';

        switch ($_POST['payment_type']) {

            case 'one_time':

                /* Generate unique payment id */
                $payment_id = md5(
                    $this->user->user_id .
                    $this->plan_id .
                    $_POST['payment_type'] .
                    $_POST['payment_frequency'] .
                    $this->user->email .
                    get_date()
                );

                /* Prepare request body */
                $flutterwave_payment_payload = [
                    'tx_ref' => $payment_id,
                    'amount' => $formatted_price,
                    'currency' => currency(),
                    'redirect_url' => url('pay/' . $this->plan_id . $this->return_url_parameters('success', $base_amount, $formatted_price, $code, $discount_amount)),
                    'meta' => [
                        'user_id' => $this->user->user_id,
                        'plan_id' => $this->plan_id,
                        'payment_frequency' => $_POST['payment_frequency'],
                        'base_amount' => $base_amount,
                        'code' => $code,
                        'discount_amount' => $discount_amount,
                        'taxes_ids' => json_encode($this->applied_taxes_ids)
                    ],
                    'customer' => [
                        'email' => $this->user->email,
                        'name' => $this->user->name
                    ]
                ];

                /* Make Flutterwave API request */
                $flutterwave_response = \Unirest\Request::post(
                    'https://api.flutterwave.com/v3/payments',
                    [
                        'Authorization' => 'Bearer ' . settings()->flutterwave->secret_key,
                        'Content-Type' => 'application/json',
                    ],
                    \Unirest\Request\Body::json($flutterwave_payment_payload)
                );

                if ($flutterwave_response->code >= 400) {
                    $flutterwave_error_message = (DEBUG || \Altum\Authentication::is_admin()) ? $flutterwave_response->body->message : l('pay.error_message.failed_payment');
                    Alerts::add_error($flutterwave_error_message);
                    redirect('pay/' . $this->plan_id . '?' . $trial_skip_parameter . $discount_code_parameter);
                }

                header('Location: ' . $flutterwave_response->body->data->link); die();

            case 'recurring':

                /* Match frequency names for Flutterwave */
                $flutterwave_payment_frequency = match ($_POST['payment_frequency']) {
                    'annual' => 'yearly',
                    'biannual' => 'bi-annually',
                    default => $_POST['payment_frequency']
                };

                /* Fetch existing payment plans */
                $payment_plans_response = \Unirest\Request::get(
                    'https://api.flutterwave.com/v3/payment-plans',
                    [
                        'Authorization' => 'Bearer ' . settings()->flutterwave->secret_key,
                        'Content-Type' => 'application/json',
                    ]
                );

                if ($payment_plans_response->code >= 400) {
                    $flutterwave_error_message = (DEBUG || \Altum\Authentication::is_admin()) ? $payment_plans_response->body->message : l('pay.error_message.failed_payment');
                    Alerts::add_error($flutterwave_error_message);
                    redirect('pay/' . $this->plan_id . '?' . $trial_skip_parameter . $discount_code_parameter);
                }

                /* Find existing plan or create new one */
                $payment_plan = null;
                foreach ($payment_plans_response->body->data as $existing_plan) {
                    if ($existing_plan->amount == $formatted_price && $existing_plan->interval == $flutterwave_payment_frequency) {
                        $payment_plan = $existing_plan;
                        break;
                    }
                }

                if (!$payment_plan) {
                    $create_plan_response = \Unirest\Request::post(
                        'https://api.flutterwave.com/v3/payment-plans',
                        [
                            'Authorization' => 'Bearer ' . settings()->flutterwave->secret_key,
                            'Content-Type' => 'application/json',
                        ],
                        \Unirest\Request\Body::json([
                            'name' => settings()->business->brand_name . ' - ' . $this->plan->name,
                            'amount' => $formatted_price,
                            'currency' => currency(),
                            'interval' => $flutterwave_payment_frequency
                        ])
                    );

                    if ($create_plan_response->code >= 400) {
                        $flutterwave_error_message = (DEBUG || \Altum\Authentication::is_admin()) ? $create_plan_response->body->message : l('pay.error_message.failed_payment');
                        Alerts::add_error($flutterwave_error_message);
                        redirect('pay/' . $this->plan_id . '?' . $trial_skip_parameter . $discount_code_parameter);
                    }

                    $payment_plan = $create_plan_response->body->data;
                }

                /* Generate unique payment id */
                $payment_id = md5(
                    $this->user->user_id .
                    $this->plan_id .
                    $_POST['payment_type'] .
                    $_POST['payment_frequency'] .
                    $this->user->email .
                    get_date()
                );

                /* Prepare request body for recurring payment */
                $flutterwave_recurring_payload = [
                    'payment_plan' => $payment_plan->id,
                    'tx_ref' => $payment_id,
                    'amount' => $formatted_price,
                    'currency' => currency(),
                    'redirect_url' => url('pay/' . $this->plan_id . $this->return_url_parameters('success', $base_amount, $formatted_price, $code, $discount_amount)),
                    'meta' => [
                        'user_id' => $this->user->user_id,
                        'plan_id' => $this->plan_id,
                        'payment_frequency' => $_POST['payment_frequency'],
                        'base_amount' => $base_amount,
                        'code' => $code,
                        'discount_amount' => $discount_amount,
                        'taxes_ids' => json_encode($this->applied_taxes_ids)
                    ],
                    'customer' => [
                        'email' => $this->user->email,
                        'name' => $this->user->name
                    ]
                ];

                $flutterwave_recurring_response = \Unirest\Request::post(
                    'https://api.flutterwave.com/v3/payments',
                    [
                        'Authorization' => 'Bearer ' . settings()->flutterwave->secret_key,
                        'Content-Type' => 'application/json',
                    ],
                    \Unirest\Request\Body::json($flutterwave_recurring_payload)
                );

                if ($flutterwave_recurring_response->code >= 400) {
                    $flutterwave_error_message = (DEBUG || \Altum\Authentication::is_admin()) ? $flutterwave_recurring_response->body->message : l('pay.error_message.failed_payment');
                    Alerts::add_error($flutterwave_error_message);
                    redirect('pay/' . $this->plan_id . '?' . $trial_skip_parameter . $discount_code_parameter);
                }

                header('Location: ' . $flutterwave_recurring_response->body->data->link); die();
        }
    }

    private function lemonsqueezy() {

        /* Set Lemonsqueezy API key */
        Lemonsqueezy::$api_key = settings()->lemonsqueezy->api_key;

        /* Get price details */
        extract($this->get_price_details());

        /* Apply taxes to base price */
        $price_with_taxes = $this->calculate_price_with_taxes($price);

        /* Format price for Lemonsqueezy API (cents or units) */
        $formatted_price = in_array(currency(), [
            'MGA', 'BIF', 'CLP', 'PYG', 'DJF', 'RWF', 'GNF', 'UGX', 'JPY', 'VND', 'VUV',
            'XAF', 'KMF', 'KRW', 'XOF', 'XPF'
        ])
            ? number_format($price_with_taxes, 0, '.', '')
            : number_format($price_with_taxes, 2, '.', '') * 100;

        /* Store string price for redirect parameters */
        $formatted_price_for_url = number_format($price_with_taxes, 2, '.', '');

        /* Prepare redirect query parameters */
        $trial_skip_parameter = isset($_GET['trial_skip']) ? '&trial_skip=true' : '';
        $discount_code_parameter = isset($_GET['code']) ? '&code=' . $_GET['code'] : '';

        /* Prepare custom user data for checkout */
        $custom_user_data = [
            'user_id' => (string) $this->user->user_id,
            'plan_id' => (string) $this->plan_id,
            'payment_frequency' => (string) $_POST['payment_frequency'],
            'base_amount' => (string) $base_amount,
            'code' => (string) ($code ?: 'null'),
            'discount_amount' => (string) $discount_amount,
            'taxes_ids' => json_encode($this->applied_taxes_ids),
        ];

        /* Prepare Lemonsqueezy checkout payload */
        $lemonsqueezy_checkout_payload = [
            'data' => [
                'type' => 'checkouts',
                'attributes' => [
                    'custom_price' => $formatted_price,
                    'expires_at' => (new \DateTime('now', new \DateTimeZone('UTC')))
                        ->add(new \DateInterval('P1D'))
                        ->format(\DateTime::ATOM),
                    'checkout_data' => [
                        'email' => $this->user->email,
                        'name' => $this->user->name,
                        'custom' => $custom_user_data,
                    ],
                    'product_options' => [
                        'redirect_url' => url(
                            'pay/' . $this->plan_id .
                            $this->return_url_parameters('success', $base_amount, $formatted_price_for_url, $code, $discount_amount)
                        )
                    ]
                ],
                'relationships' => [
                    'store' => [
                        'data' => [
                            'type' => 'stores',
                            'id' => settings()->lemonsqueezy->store_id,
                        ]
                    ],
                    'variant' => [
                        'data' => [
                            'type' => 'variants',
                            'id' => settings()->lemonsqueezy->{$_POST['payment_type'] . '_' . $_POST['payment_frequency'] . '_variant_id'},
                        ]
                    ]
                ]
            ]
        ];

        /* Generate the payment link */
        $lemonsqueezy_response = \Unirest\Request::post(
            Lemonsqueezy::$api_url . 'checkouts',
            Lemonsqueezy::get_headers(),
            \Unirest\Request\Body::json($lemonsqueezy_checkout_payload)
        );

        if ($lemonsqueezy_response->code === 201 && !empty($lemonsqueezy_response->body->data->attributes->url)) {
            /* Redirect to payment */
            header('Location: ' . $lemonsqueezy_response->body->data->attributes->url); die();
        } else {
            /* Handle payment creation errors */
            $lemonsqueezy_error_message = (DEBUG || \Altum\Authentication::is_admin())
                ? print_r($lemonsqueezy_response->body->errors, true)
                : l('pay.error_message.failed_payment');
            Alerts::add_error($lemonsqueezy_error_message);
            redirect('pay/' . $this->plan_id . '?' . $trial_skip_parameter . $discount_code_parameter);
        }

    }

    private function myfatoorah() {

        /* Get price details */
        extract($this->get_price_details());

        /* Apply taxes to base price */
        $price_with_taxes = $this->calculate_price_with_taxes($price);

        /* Format price as integer (no decimals for API) */
        $formatted_price = intval(number_format($price_with_taxes, 2, '.', ''));

        /* Prepare meta data for customer reference */
        $customer_reference = $this->user->user_id . '&' . $this->plan_id . '&' . $_POST['payment_frequency'] . '&' . $base_amount . '&' . $code . '&' . $discount_amount . '&' . json_encode($this->applied_taxes_ids);

        /* Prepare redirect query parameters */
        $trial_skip_parameter = isset($_GET['trial_skip']) ? '&trial_skip=true' : '';
        $discount_code_parameter = isset($_GET['code']) ? '&code=' . $_GET['code'] : '';

        /* Prepare MyFatoorah payload */
        $myfatoorah_payload = [
            'InvoiceValue' => $formatted_price,
            'CustomerName' => $this->user->name,
            'CustomerEmail' => $this->user->email,
            'WebhookUrl' => SITE_URL . 'webhook-myfatoorah',
            'NotificationOption' => 'LNK',
            'CustomerReference' => $customer_reference,
            'CallBackUrl' => url('pay/' . $this->plan_id . $this->return_url_parameters('success', $base_amount, $formatted_price, $code, $discount_amount)),
            'ErrorUrl' => url('pay/' . $this->plan_id . $this->return_url_parameters('cancel', $base_amount, $formatted_price, $code, $discount_amount))
        ];

        /* Send payment request to MyFatoorah */
        try {
            $myfatoorah_response = \Unirest\Request::post(
                'https://' . settings()->myfatoorah->api_endpoint . '/v2/SendPayment',
                [
                    'Authorization' => 'Bearer ' . settings()->myfatoorah->api_key,
                    'Content-Type' => 'application/json'
                ],
                \Unirest\Request\Body::json($myfatoorah_payload)
            );
        } catch (\Exception $exception) {
            $myfatoorah_error_message = (DEBUG || \Altum\Authentication::is_admin())
                ? $exception->getMessage()
                : l('pay.error_message.failed_payment');
            Alerts::add_error($myfatoorah_error_message);
            redirect('pay/' . $this->plan_id . '?' . $trial_skip_parameter . $discount_code_parameter);
        }

        if ($myfatoorah_response->code === 200 && !empty($myfatoorah_response->body->Data->InvoiceURL)) {
            /* Redirect to payment */
            header('Location: ' . $myfatoorah_response->body->Data->InvoiceURL); die();
        } else {
            $myfatoorah_error_message = (DEBUG || \Altum\Authentication::is_admin())
                ? print_r($myfatoorah_response->body, true)
                : l('pay.error_message.failed_payment');
            Alerts::add_error($myfatoorah_error_message);
            redirect('pay/' . $this->plan_id . '?' . $trial_skip_parameter . $discount_code_parameter);
        }

    }

}
