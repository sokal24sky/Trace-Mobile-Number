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

class AccountPreferences extends Controller {

    public function index() {

        \Altum\Authentication::guard();

        if(is_null($this->user->preferences)) {
            $this->user->preferences = new \StdClass();
        }

        if(!empty($_POST)) {

            /* White labeling */
            $_POST['white_label_title'] = isset($_POST['white_label_title']) ? input_clean($_POST['white_label_title'], 32) : '';

            /* Uploads processing */
            foreach(['logo_light', 'logo_dark', 'favicon'] as $image_key) {
                $this->user->preferences->{'white_label_' . $image_key} = \Altum\Uploads::process_upload($this->user->preferences->{'white_label_' . $image_key}, 'users', 'white_label_' . $image_key, 'white_label_' . $image_key . '_remove', null);
            }

            /* Clean some posted variables */
            $_POST['default_results_per_page'] = isset($_POST['default_results_per_page']) && in_array($_POST['default_results_per_page'], [10, 25, 50, 100, 250, 500, 1000]) ? (int) $_POST['default_results_per_page'] : settings()->main->default_results_per_page;
            $_POST['default_order_type'] = isset($_POST['default_order_type']) && in_array($_POST['default_order_type'], ['ASC', 'DESC']) ? $_POST['default_order_type'] : settings()->main->default_order_type;

            /* Custom */
            $_POST['links_default_order_by'] = isset($_POST['links_default_order_by']) && in_array($_POST['links_default_order_by'], ['link_id', 'datetime', 'last_datetime', 'url', 'location_url', 'pageviews']) ? $_POST['links_default_order_by'] : 'link_id';
            $_POST['ai_qr_codes_default_order_by'] = isset($_POST['ai_qr_codes_default_order_by']) && in_array($_POST['ai_qr_codes_default_order_by'], ['ai_qr_code_id', 'datetime', 'last_datetime', 'name']) ? $_POST['ai_qr_codes_default_order_by'] : 'ai_qr_code_id';
            $_POST['qr_codes_default_order_by'] = isset($_POST['qr_codes_default_order_by']) && in_array($_POST['qr_codes_default_order_by'], ['qr_code_id', 'datetime', 'last_datetime', 'name', 'type']) ? $_POST['qr_codes_default_order_by'] : 'qr_code_id';
            $_POST['barcodes_default_order_by'] = isset($_POST['barcodes_default_order_by']) && in_array($_POST['barcodes_default_order_by'], ['barcode_id', 'datetime', 'last_datetime', 'name', 'type']) ? $_POST['barcodes_default_order_by'] : 'barcode_id';
            $_POST['projects_default_order_by'] = isset($_POST['projects_default_order_by']) && in_array($_POST['projects_default_order_by'], ['project_id', 'last_datetime', 'name', 'datetime']) ? $_POST['projects_default_order_by'] : 'project_id';
            $_POST['pixels_default_order_by'] = isset($_POST['pixels_default_order_by']) && in_array($_POST['pixels_default_order_by'], ['pixel_id', 'last_datetime', 'name', 'datetime']) ? $_POST['pixels_default_order_by'] : 'pixel_id';
            $_POST['domains_default_order_by'] = isset($_POST['domains_default_order_by']) && in_array($_POST['domains_default_order_by'], ['domain_id', 'last_datetime', 'host', 'datetime']) ? $_POST['domains_default_order_by'] : 'domain_id';

            $_POST['links_auto_copy_link'] = isset($_POST['links_auto_copy_link']);

            /* Allowed dashboard features */
            $allowed_dashboard_features = ['ai_qr_codes', 'qr_codes', 'barcodes', 'links'];

            /* Sanitize input - keep only valid features */
            $_POST['dashboard'] = array_values(array_filter($_POST['dashboard'], fn($item) => in_array($item, $allowed_dashboard_features)));

            /* Preserve the order of $_POST['dashboard'] */
            $dashboard = array_fill_keys($_POST['dashboard'], true);

            /* Append missing features at the end with false */
            foreach ($allowed_dashboard_features as $feature) {
                if(!array_key_exists($feature, $dashboard)) {
                    $dashboard[$feature] = false;
                }
            }

            /* Tracking */
            $_POST['excluded_ips'] = array_filter(array_map('trim', explode(',', input_clean($_POST['excluded_ips'], 500))));


            //ALTUMCODE:DEMO if(DEMO) if($this->user->user_id == 1) Alerts::add_error('Please create an account on the demo to test out this function.');

            /* Check for any errors */
            if(!\Altum\Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

                $preferences = json_encode([
                    'white_label_title' => $_POST['white_label_title'],
                    'white_label_logo_light' => $this->user->preferences->white_label_logo_light,
                    'white_label_logo_dark' => $this->user->preferences->white_label_logo_dark,
                    'white_label_favicon' => $this->user->preferences->white_label_favicon,
                    'default_results_per_page' => $_POST['default_results_per_page'],
                    'default_order_type' => $_POST['default_order_type'],

                    'links_default_order_by' => $_POST['links_default_order_by'],
                    'ai_qr_codes_default_order_by' => $_POST['ai_qr_codes_default_order_by'],
                    'qr_codes_default_order_by' => $_POST['qr_codes_default_order_by'],
                    'barcodes_default_order_by' => $_POST['barcodes_default_order_by'],
                    'projects_default_order_by' => $_POST['projects_default_order_by'],
                    'pixels_default_order_by' => $_POST['pixels_default_order_by'],
                    'domains_default_order_by' => $_POST['domains_default_order_by'],
                    'links_auto_copy_link' => $_POST['links_auto_copy_link'],

                    'dashboard' => $dashboard,

                    'excluded_ips' => $_POST['excluded_ips'],
                ]);

                /* Database query */
                db()->where('user_id', $this->user->user_id)->update('users', [
                    'preferences' => $preferences,
                ]);

                /* Set a nice success message */
                Alerts::add_success(l('account_preferences.success_message'));

                /* Clear the cache */
                cache()->deleteItemsByTag('user_id=' . $this->user->user_id);

                /* Send webhook notification if needed */
                if(settings()->webhooks->user_update) {
                    fire_and_forget('post', settings()->webhooks->user_update, [
                        'user_id' => $this->user->user_id,
                        'email' => $this->user->email,
                        'name' => $this->user->name,
                        'source' => 'account_preferences',
                        'datetime' => get_date(),
                    ]);
                }

                redirect('account-preferences');
            }

        }

        /* Get the account header menu */
        $menu = new \Altum\View('partials/account_header_menu', (array) $this);
        $this->add_view_content('account_header_menu', $menu->run());

        /* Prepare the view */
        $data = [];

        $view = new \Altum\View('account-preferences/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
