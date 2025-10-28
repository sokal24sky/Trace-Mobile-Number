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

class AdminDomainUpdate extends Controller {

    public function index() {

        $domain_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        /* Check if user exists */
        if(!$domain = db()->where('domain_id', $domain_id)->getOne('domains')) {
            redirect('admin/domains');
        }

        /* Get some user details of the domain owner */
        $user = db()->where('user_id', $domain->user_id)->getOne('users', ['user_id', 'email', 'name', 'avatar']);

        if(!empty($_POST)) {
            /* Clean some posted variables */
            $_POST['scheme'] = isset($_POST['scheme']) && in_array($_POST['scheme'], ['http://', 'https://']) ? input_clean($_POST['scheme']) : 'https://';
            $_POST['host'] = str_replace(' ', '', mb_strtolower(input_clean($_POST['host'], 128)));
            $_POST['host'] = string_starts_with('http://', $_POST['host']) || string_starts_with('https://', $_POST['host']) ? parse_url($_POST['host'], PHP_URL_HOST) : $_POST['host'];
            $_POST['custom_index_url'] = get_url($_POST['custom_index_url'], 256);
            $_POST['custom_not_found_url'] = get_url($_POST['custom_not_found_url'], 256);
            $_POST['is_enabled'] = (int) isset($_POST['is_enabled']);

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

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

                /* Update the row of the database */
                db()->where('domain_id', $domain->domain_id)->update('domains', [
                    'scheme' => $_POST['scheme'],
                    'host' => $_POST['host'],
                    'custom_index_url' => $_POST['custom_index_url'],
                    'custom_not_found_url' => $_POST['custom_not_found_url'],
                    'is_enabled' => $_POST['is_enabled'],
                ]);

                /* Clear the cache */
                cache()->deleteItems([
                    'domains?user_id=' . $domain->user_id,
                    'domain?domain_id=' . $domain->domain_id,
                    'domain?host=' . md5($domain->host)
                ]);
                cache()->deleteItem('available_additional_domains');

                /* Set a nice success message */
                Alerts::add_success(sprintf(l('global.success_message.update1'), '<strong>' . $_POST['host'] . '</strong>'));

                redirect('admin/domain-update/' . $domain->domain_id);
            }

        }

        /* Main View */
        $data = [
            'user' => $user,
            'domain' => $domain
        ];

        $view = new \Altum\View('admin/domain-update/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
