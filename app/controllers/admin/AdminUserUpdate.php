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

defined('ALTUMCODE') || die();

class AdminUserUpdate extends Controller {

    public function index() {

        $user_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        /* Check if user exists */
        if(!$user = db()->where('user_id', $user_id)->getOne('users')) {
            redirect('admin/users');
        }

        $user->plan_settings = json_decode($user->plan_settings);

        $additional_domains = db()->where('is_enabled', 1)->where('type', 1)->get('domains');

        if(!empty($_POST)) {
            /* Filter some of the variables */
            $_POST['name'] = input_clean($_POST['name']);
            $_POST['status'] = (int) $_POST['status'];
            $_POST['type'] = (int) $_POST['type'];
            $_POST['plan_trial_done'] = (int) isset($_POST['plan_trial_done']);

            if(\Altum\Plugin::is_active('affiliate')) {
                $_POST['referred_by'] = !empty($_POST['referred_by']) ? (int) $_POST['referred_by'] : null;
            }

            switch($_POST['plan_id']) {
                case 'free':

                    $plan_settings = json_encode(settings()->plan_free->settings ?? '');

                    break;

                case 'custom':

                    /* Determine the enabled QR codes */
                    $enabled_qr_codes = [];

                    foreach(array_keys((require APP_PATH . 'includes/qr_codes.php')) as $key) {
                        $enabled_qr_codes[$key] = isset($_POST['enabled_qr_codes']) && in_array($key, $_POST['enabled_qr_codes']);
                    }

                    /* Determine the enabled barcodes */
                    $enabled_barcodes = [];

                    foreach(array_keys((require APP_PATH . 'includes/barcodes.php')) as $key) {
                        $enabled_barcodes[$key] = isset($_POST['enabled_barcodes']) && in_array($key, $_POST['enabled_barcodes']);
                    }

                    $plan_settings = [
                        'url_minimum_characters'            => (int) $_POST['url_minimum_characters'],
                        'url_maximum_characters'            => (int) $_POST['url_maximum_characters'],
                        'ai_qr_codes_per_month_limit'       => (int) $_POST['ai_qr_codes_per_month_limit'],
                        'qr_codes_limit'                    => (int) $_POST['qr_codes_limit'],
                        'qr_codes_bulk_limit'               => (int) max(0, $_POST['qr_codes_bulk_limit']),
                        'barcodes_limit'                    => (int) $_POST['barcodes_limit'],
                        'barcodes_bulk_limit'               => (int) max(0, $_POST['barcodes_bulk_limit']),
                        'links_limit'                       => (int) $_POST['links_limit'],
                        'links_bulk_limit'                  => (int) max(0, $_POST['links_bulk_limit']),
                        'projects_limit'                    => (int) $_POST['projects_limit'],
                        'pixels_limit'                      => (int) $_POST['pixels_limit'],
                        'domains_limit'                     => (int) $_POST['domains_limit'],
                        'teams_limit'                       => (int) $_POST['teams_limit'],
                        'team_members_limit'                => (int) $_POST['team_members_limit'],
                        'statistics_retention'              => (int) $_POST['statistics_retention'],
                        'additional_domains'                => $_POST['additional_domains'] ?? [],
                        'analytics_is_enabled'              => isset($_POST['analytics_is_enabled']),
                        'custom_url_is_enabled'             => isset($_POST['custom_url_is_enabled']),
                        'utm_parameters_is_enabled'         => isset($_POST['utm_parameters_is_enabled']),
                        'password_protection_is_enabled'    => isset($_POST['password_protection_is_enabled']),
                        'sensitive_content_is_enabled'      => isset($_POST['sensitive_content_is_enabled']),
                        'cloaking_is_enabled'               => isset($_POST['cloaking_is_enabled']),
                        'app_linking_is_enabled'            => isset($_POST['app_linking_is_enabled']),
                        'targeting_is_enabled'              => isset($_POST['targeting_is_enabled']),
                        'api_is_enabled'                    => isset($_POST['api_is_enabled']),
                        'affiliate_commission_percentage'   => (int) $_POST['affiliate_commission_percentage'],
                        'no_ads'                            => isset($_POST['no_ads']),
                        'white_labeling_is_enabled' => isset($_POST['white_labeling_is_enabled']),
                'export' => [
                            'pdf'                           => isset($_POST['export']) && in_array('pdf', $_POST['export']),
                            'csv'                           => isset($_POST['export']) && in_array('csv', $_POST['export']),
                            'json'                          => isset($_POST['export']) && in_array('json', $_POST['export']),
                        ],
                        'removable_branding'                => isset($_POST['removable_branding']),
                        'qr_reader_is_enabled'              => isset($_POST['qr_reader_is_enabled']),
                        'barcode_reader_is_enabled'         => isset($_POST['barcode_reader_is_enabled']),
                        'enabled_qr_codes'                  => $enabled_qr_codes,
                        'enabled_barcodes'                  => $enabled_barcodes,
                    ];

                    $plan_settings = json_encode($plan_settings);

                    break;

                default:

                    $_POST['plan_id'] = (int) $_POST['plan_id'];

                    /* Make sure this plan exists */
                    if(!$plan_settings = db()->where('plan_id', $_POST['plan_id'])->getValue('plans', 'settings')) {
                        redirect('admin/user-update/' . $user->user_id);
                    }

                    break;
            }

            $_POST['plan_expiration_date'] = \Altum\Date::validate($_POST['plan_expiration_date'], 'Y-m-d') || \Altum\Date::validate($_POST['plan_expiration_date'], 'Y-m-d H:i:s') ? $_POST['plan_expiration_date'] : '';
            $_POST['plan_expiration_date'] = (new \DateTime($_POST['plan_expiration_date']))->format('Y-m-d H:i:s');

            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* Check for any errors */
            $required_fields = ['name', 'email'];
            foreach($required_fields as $field) {
                if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                    Alerts::add_field_error($field, l('global.error_message.empty_field'));
                }
            }

            if(!\Altum\Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }
            if(mb_strlen($_POST['name']) < 1 || mb_strlen($_POST['name']) > 64) {
                Alerts::add_field_error('name', l('admin_users.error_message.name_length'));
            }
            if(filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) == false) {
                //ALTUMCODE:DEMO if(DEMO) {
                Alerts::add_field_error('email', l('global.error_message.invalid_email'));
                //ALTUMCODE:DEMO }
            }
            if(db()->where('email', $_POST['email'])->has('users') && $_POST['email'] !== $user->email) {
                Alerts::add_field_error('email', l('admin_users.error_message.email_exists'));
            }

            if(!empty($_POST['new_password']) && !empty($_POST['repeat_password'])) {
                if(mb_strlen($_POST['new_password']) < 6 || mb_strlen($_POST['new_password']) > 64) {
                    Alerts::add_field_error('new_password', l('global.error_message.password_length'));
                }
                if($_POST['new_password'] !== $_POST['repeat_password']) {
                    Alerts::add_field_error('repeat_password', l('global.error_message.passwords_not_matching'));
                }
            }

            /* If there are no errors, continue */
            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

                /* Update the basic user settings */
                db()->where('user_id', $user->user_id)->update('users', [
                    'name' => $_POST['name'],
                    'email' => $_POST['email'],
                    'status' => $_POST['status'],
                    'type' => $_POST['type'],
                    'plan_id' => $_POST['plan_id'],
                    'plan_expiration_date' => $_POST['plan_expiration_date'],
                    'plan_expiry_reminder' => $user->plan_expiration_date != $_POST['plan_expiration_date'] ? 0 : 1,
                    'plan_settings' => $plan_settings,
                    'plan_trial_done' => $_POST['plan_trial_done'],
                    'referred_by' => $user->referred_by != $_POST['referred_by'] ? $_POST['referred_by'] : $user->referred_by,
                ]);

                /* Update the password if set */
                if(!empty($_POST['new_password']) && !empty($_POST['repeat_password'])) {
                    $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

                    /* Database query */
                    db()->where('user_id', $user->user_id)->update('users', ['password' => $new_password]);
                }

                /* Update all websites if any */
                if(settings()->sso->is_enabled && count((array) settings()->sso->websites)) {
                    foreach(settings()->sso->websites as $website) {
                        $response = \Unirest\Request::post(
                            $website->url . 'admin-api/sso/update',
                            ['Authorization' => 'Bearer ' . $website->api_key],
                            \Unirest\Request\Body::form([
                                'name' => $_POST['name'],
                                'email' => $user->email,
                                'new_email' => $_POST['email'],
                            ])
                        );
                    }
                }

                /* Set a nice success message */
                Alerts::add_success(sprintf(l('global.success_message.update1'), '<strong>' . $_POST['name'] . '</strong>'));

                /* Clear the cache */
                cache()->deleteItemsByTag('user_id=' . $user->user_id);

                redirect('admin/user-update/' . $user->user_id);
            }

        }

        /* Get all the plans available */
        $plans = db()->where('status', 0, '<>')->get('plans');

        /* Main View */
        $data = [
            'user' => $user,
            'plans' => $plans,
            'additional_domains' => $additional_domains,
        ];

        $view = new \Altum\View('admin/user-update/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
