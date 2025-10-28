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

class AdminPlanCreate extends Controller {

    public function index() {

        if(in_array(settings()->license->type, ['Extended License', 'extended'])) {
            /* Get the available taxes from the system */
            $taxes = db()->get('taxes');
        }

        $additional_domains = db()->where('is_enabled', 1)->where('type', 1)->get('domains');

        if(!empty($_POST)) {
            /* Filter some of the variables */
            $_POST['name'] = input_clean($_POST['name'], 64);
            $_POST['description'] = input_clean($_POST['description'], 256);

            /* Prices */
            $prices = [
                'monthly' => [],
                'annual' => [],
                'lifetime' => [],
            ];

            foreach((array) settings()->payment->currencies as $currency => $currency_data) {
                $prices['monthly'][$currency] = (float) $_POST['monthly_price'][$currency];
                $prices['annual'][$currency] = (float) $_POST['annual_price'][$currency];
                $prices['lifetime'][$currency] = (float) $_POST['lifetime_price'][$currency];
            }

            $prices = json_encode($prices);

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

            $_POST['settings'] = [
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

            $_POST['settings']['custom_redirect_url'] = get_url($_POST['custom_redirect_url']);
            $_POST['settings']['tag'] = input_clean($_POST['tag'], 64);

            $_POST['settings'] = json_encode($_POST['settings']);

            $_POST['color'] = !verify_hex_color($_POST['color']) ? null : $_POST['color'];
            $_POST['status'] = (int) $_POST['status'];
            $_POST['order'] = (int) $_POST['order'];
            $_POST['trial_days'] = (int) $_POST['trial_days'];
            $_POST['taxes_ids'] = json_encode($_POST['taxes_ids'] ?? []);

            /* Translations */
            foreach($_POST['translations'] as $language_name => $array) {
                foreach($array as $key => $value) {
                    $_POST['translations'][$language_name][$key] = input_clean($value);
                }
                if(!array_key_exists($language_name, \Altum\Language::$active_languages)) {
                    unset($_POST['translations'][$language_name]);
                }
            }

            $_POST['translations'] = json_encode($_POST['translations']);

            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* Check for any errors */
            $required_fields = ['name'];
            foreach($required_fields as $field) {
                if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                    Alerts::add_field_error($field, l('global.error_message.empty_field'));
                }
            }

            if(!\Altum\Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

                /* Database query */
                db()->insert('plans', [
                    'name' => $_POST['name'],
                    'description' => $_POST['description'],
                    'prices' => $prices,
                    'settings' => $_POST['settings'],
                    'translations' => $_POST['translations'],
                    'taxes_ids' => $_POST['taxes_ids'],
                    'color' => $_POST['color'],
                    'status' => $_POST['status'],
                    'order' => $_POST['order'],
                    'datetime' => get_date(),
                ]);

                /* Clear the cache */
                cache()->deleteItem('plans');

                /* Set a nice success message */
                Alerts::add_success(sprintf(l('global.success_message.create1'), '<strong>' . $_POST['name'] . '</strong>'));

                redirect('admin/plans');
            }
        }


        /* Main View */
        $data = [
            'taxes' => $taxes ?? null,
            'additional_domains' => $additional_domains,
        ];

        $view = new \Altum\View('admin/plan-create/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
