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

class AdminBroadcastUpdate extends Controller {

    public function index() {

        $broadcast_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        if(!$broadcast = db()->where('broadcast_id', $broadcast_id)->getOne('broadcasts')) {
            redirect('admin/broadcasts');
        }

        if($broadcast->status == 'processing') {
            Alerts::add_error(l('admin_broadcast_update.error_message.processing'));
            redirect('admin/broadcasts');
        }

        $broadcast->settings = json_decode($broadcast->settings ?? '');
        $broadcast->users_ids = implode(',', json_decode($broadcast->users_ids));

        $plans = (new \Altum\Models\Plan())->get_plans();

        if(!empty($_POST)) {
            /* Filter some of the variables */
            $_POST['name'] = input_clean($_POST['name'], 64);
            $_POST['subject'] = input_clean($_POST['subject'], 128);
            $_POST['segment'] = in_array($_POST['segment'], ['all', 'subscribers', 'custom', 'filter']) ? input_clean($_POST['segment']) : 'subscribers';
            $_POST['is_system_email'] = (int) isset($_POST['is_system_email']);

            /* Users ids */
            $_POST['users_ids'] = trim($_POST['users_ids'] ?? '');
            $_POST['users_ids'] = array_filter(array_map('intval', explode(',', $_POST['users_ids'])));
            $_POST['users_ids'] = array_values(array_unique($_POST['users_ids']));
            $_POST['users_ids'] = $_POST['users_ids'] ?: [0];

            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            if(!\Altum\Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            /* Preview email */
            if(isset($_POST['preview'])) {
                $_POST['preview_email'] = mb_substr(filter_var($_POST['preview_email'], FILTER_SANITIZE_EMAIL), 0, 320);

                $required_fields = ['subject', 'content', 'preview_email'];
                foreach($required_fields as $field) {
                    if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                        Alerts::add_field_error($field, l('global.error_message.empty_field'));
                    }
                }

                if(filter_var($_POST['preview_email'], FILTER_VALIDATE_EMAIL) == false) {
                    Alerts::add_field_error('preview_email', l('global.error_message.invalid_email'));
                }
            }

            /* Save draft or send */
            else {
                $required_fields = ['name', 'subject', 'content'];
                foreach($required_fields as $field) {
                    if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                        Alerts::add_field_error($field, l('global.error_message.empty_field'));
                    }
                }
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

                /* Preview email */
                if(isset($_POST['preview'])) {
                    $vars = [
                        '{{USER:NAME}}' => $this->user->name,
                        '{{USER:EMAIL}}' => $this->user->email,
                        '{{USER:CONTINENT_NAME}}' => get_continent_from_continent_code($this->user->continent_code),
                        '{{USER:COUNTRY_NAME}}' => get_country_from_country_code($this->user->country),
                        '{{USER:CITY_NAME}}' => $this->user->city_name,
                        '{{USER:DEVICE_TYPE}}' => l('global.device.' . $this->user->device_type),
                        '{{USER:OS_NAME}}' => $this->user->os_name,
                        '{{USER:BROWSER_NAME}}' => $this->user->browser_name,
                        '{{USER:BROWSER_LANGUAGE}}' => get_language_from_locale($this->user->browser_language),
                    ];

                    $email_template = get_email_template(
                        $vars,
                        htmlspecialchars_decode($_POST['subject']),
                        $vars,
                        convert_editorjs_json_to_html($_POST['content'])
                    );

                    send_mail($_POST['preview_email'], $email_template->subject, $email_template->body, ['is_broadcast' => true, 'is_system_email' => $_POST['is_system_email'], 'anti_phishing_code' => $this->user->anti_phishing_code, 'language' => $this->user->language], $_POST['preview_email']);

                    /* Set a nice success message */
                    Alerts::add_success(sprintf(l('admin_broadcast_create.success_message.preview'), '<strong>' . $_POST['preview_email'] . '</strong>'));
                }

                if(isset($_POST['save']) || isset($_POST['send'])) {
                    $settings = [
                        'is_system_email' => $_POST['is_system_email'],
                    ];

                    /* Get all the users needed */
                    switch($_POST['segment']) {
                        case 'all':
                            $users = db()->get('users', null, ['user_id']);
                            break;

                        case 'subscribers':
                            $users = db()->where('is_newsletter_subscribed', 1)->get('users', null, ['user_id']);
                            break;

                        case 'custom':
                            $users = db()->where('user_id', $_POST['users_ids'], 'IN')->get('users', null, ['user_id']);
                            break;

                        case 'filter':

                            $query = db();

                            $has_filters = false;

                            /* Is subscribed */
                            $_POST['filters_is_newsletter_subscribed'] = isset($_POST['filters_is_newsletter_subscribed']) ? (bool) $_POST['filters_is_newsletter_subscribed'] : 0;

                            if($_POST['filters_is_newsletter_subscribed']) {
                                $has_filters = true;
                                $query->where('is_newsletter_subscribed', 1);
                                $settings['filters_is_newsletter_subscribed'] = (int) $_POST['filters_is_newsletter_subscribed'];
                            }

                            /* Plans */
                            if(isset($_POST['filters_plans'])) {
                                $has_filters = true;
                                $query->where('plan_id', $_POST['filters_plans'], 'IN');
                                $settings['filters_plans'] = $_POST['filters_plans'];
                            }

                            /* Status */
                            if(isset($_POST['filters_status'])) {
                                $has_filters = true;
                                $query->where('status', $_POST['filters_status'], 'IN');
                                $settings['filters_status'] = $_POST['filters_status'];
                            }

                            /* Cities */
                            if(!empty($_POST['filters_cities'])) {
                                $_POST['filters_cities'] = explode(',', $_POST['filters_cities']);
                        $_POST['filters_cities'] = array_filter(array_unique($_POST['filters_cities']));

                                if(count($_POST['filters_cities'])) {
                                    $_POST['filters_cities'] = array_map(function($city) {
                                        return query_clean($city);
                                    }, $_POST['filters_cities']);

                                    $has_filters = true;
                                    $query->where('city_name', $_POST['filters_cities'], 'IN');
                                    $settings['filters_cities'] = $_POST['filters_cities'];
                                }
                            }

                            /* Countries */
                            if(isset($_POST['filters_countries'])) {
                                $has_filters = true;
                                $query->where('country', $_POST['filters_countries'], 'IN');
                                $settings['filters_countries'] = $_POST['filters_countries'];
                            }

                            /* Continents */
                            if(isset($_POST['filters_continents'])) {
                                $has_filters = true;
                                $query->where('continent_code', $_POST['filters_continents'], 'IN');
                                $settings['filters_continents'] = $_POST['filters_continents'];
                            }

                            /* Source */
                            if(isset($_POST['filters_source'])) {
                                $has_filters = true;
                                $query->where('source', $_POST['filters_source'], 'IN');
                                $settings['filters_source'] = $_POST['filters_source'];
                            }

                            /* Device type */
                            if(isset($_POST['filters_device_type'])) {
                                $has_filters = true;
                                $query->where('device_type', $_POST['filters_device_type'], 'IN');
                                $settings['filters_device_type'] = $_POST['filters_device_type'];
                            }

                            /* Languages */
                            if(isset($_POST['filters_languages'])) {
                                $has_filters = true;
                                $query->where('language', $_POST['filters_languages'], 'IN');
                                $settings['filters_languages'] = $_POST['filters_languages'];
                            }

                            /* Browser languages */
                            if(isset($_POST['filters_browser_languages'])) {
                                $_POST['filters_browser_languages'] = array_filter($_POST['filters_browser_languages'], function($locale) {
                                    return array_key_exists($locale, get_locale_languages_array());
                                });

                                $has_filters = true;
                                $query->where('browser_language', $_POST['filters_browser_languages'], 'IN');
                                $settings['filters_browser_languages'] = $_POST['filters_browser_languages'];
                            }

                            /* Filters operating systems */
                            if(isset($_POST['filters_operating_systems'])) {
                                $_POST['filters_operating_systems'] = array_filter($_POST['filters_operating_systems'], function($os_name) {
                                    return in_array($os_name, ['iOS', 'Android', 'Windows', 'OS X', 'Linux', 'Ubuntu', 'Chrome OS']);
                                });

                                $has_filters = true;
                                $query->where('os_name', $_POST['filters_operating_systems'], 'IN');
                                $settings['filters_operating_systems'] = $_POST['filters_operating_systems'];
                            }

                            /* Filters browsers */
                            if(isset($_POST['filters_browsers'])) {
                                $_POST['filters_browsers'] = array_filter($_POST['filters_browsers'], function($browser_name) {
                                    return in_array($browser_name, ['Chrome', 'Firefox', 'Safari', 'Edge', 'Opera', 'Samsung Internet']);
                                });

                                $has_filters = true;
                                $query->where('browser_name', $_POST['filters_browsers'], 'IN');
                                $settings['filters_browsers'] = $_POST['filters_browsers'];
                            }

                            $users = $has_filters ? $query->get('users', null, ['user_id']) : [];

                            break;

                    }

                    /* Get all users ids */
                    $users_ids = array_column($users, 'user_id');

                    /* Free memory */
                    unset($users);

                    if($broadcast->status == 'sent') {
                        /* Database query */
                        db()->where('broadcast_id', $broadcast->broadcast_id)->update('broadcasts', [
                            'name' => $_POST['name'],
                            'last_datetime' => get_date(),
                        ]);
                    }

                    else {
                        /* Database query */
                        db()->where('broadcast_id', $broadcast->broadcast_id)->update('broadcasts', [
                            'name' => $_POST['name'],
                            'subject' => $_POST['subject'],
                            'content' => $_POST['content'],
                            'segment' => $_POST['segment'],
                            'settings' => json_encode($settings),
                            'users_ids' => json_encode($users_ids),
                            'total_emails' => count($users_ids),
                            'status' => isset($_POST['save']) ? 'draft' : 'processing',
                            'last_datetime' => get_date(),
                        ]);
                    }


                    if(isset($_POST['save'])) {
                        /* Set a nice success message */
                        Alerts::add_success(sprintf(l('global.success_message.update1'), '<strong>' . $_POST['name'] . '</strong>'));
                    } else {
                        /* Set a nice success message */
                        Alerts::add_success(sprintf(l('admin_broadcast_create.success_message.send'), '<strong>' . $_POST['name'] . '</strong>'));

                        redirect('admin/broadcasts');
                    }

                }

                /* Refresh the page */
                redirect('admin/broadcast-update/' . $broadcast_id);

            }

        }

        /* Main View */
        $data = [
            'broadcast_id' => $broadcast_id,
            'broadcast' => $broadcast,
            'plans' => $plans,
        ];

        $view = new \Altum\View('admin/broadcast-update/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
