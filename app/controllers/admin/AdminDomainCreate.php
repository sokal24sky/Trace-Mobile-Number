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

class AdminDomainCreate extends Controller {

    public function index() {

        /* Default variables */
        $values = [
            'scheme' => '',
            'host' => '',
        ];

        if(!empty($_POST)) {

            /* Clean some posted variables */
            $_POST['scheme'] = isset($_POST['scheme']) && in_array($_POST['scheme'], ['http://', 'https://']) ? input_clean($_POST['scheme']) : 'https://';
            $_POST['host'] = str_replace(' ', '', mb_strtolower(input_clean($_POST['host'], 128)));
            $_POST['host'] = string_starts_with('http://', $_POST['host']) || string_starts_with('https://', $_POST['host']) ? parse_url($_POST['host'], PHP_URL_HOST) : $_POST['host'];
            $_POST['custom_index_url'] = get_url($_POST['custom_index_url'], 256);
            $_POST['custom_not_found_url'] = get_url($_POST['custom_not_found_url'], 256);
            $_POST['is_enabled'] = (int) isset($_POST['is_enabled']);

            /* Default variables */
            $values['scheme'] = $_POST['scheme'];
            $values['host'] = $_POST['host'];
            $values['custom_index_url'] = $_POST['custom_index_url'];

            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* Check for any errors */
            $required_fields = ['host'];
            foreach($required_fields as $field) {
                if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                    Alerts::add_field_error($field, l('global.error_message.empty_field'));
                }
            }

            if(!\Altum\Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            /* If there are no errors continue the registering process */
            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {
                /* Define some needed variables */
                $type = 1;

                /* Add the row to the database */
                db()->insert('domains', [
                    'user_id' => $this->user->user_id,
                    'scheme' => $_POST['scheme'],
                    'host' => $_POST['host'],
                    'custom_index_url' => $_POST['custom_index_url'],
                    'custom_not_found_url' => $_POST['custom_not_found_url'],
                    'type' => $type,
                    'is_enabled' => $_POST['is_enabled'],
                    'datetime' => get_date(),
                ]);

                /* Clear the cache */
                cache()->deleteItems([
                    'domains?user_id=' . $this->user->user_id,
                    'domains_total?user_id=' . $this->user->user_id
                ]);

                cache()->deleteItem('available_additional_domains');

                /* Set a nice success message */
                Alerts::add_success(sprintf(l('global.success_message.create1'), '<strong>' . $_POST['host'] . '</strong>'));

                /* Redirect */
                redirect('admin/domains');
            }

        }

        /* Main View */
        $data = ['values' => $values];

        $view = new \Altum\View('admin/domain-create/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
