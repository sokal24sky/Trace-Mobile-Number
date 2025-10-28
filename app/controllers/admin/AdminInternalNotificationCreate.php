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

class AdminInternalNotificationCreate extends Controller {

    public function index() {

        $plans = (new \Altum\Models\Plan())->get_plans();

        /* Clear $_GET */
        foreach($_GET as $key => $value) {
            $_GET[$key] = input_clean($value);
        }

        if(!empty($_POST)) {
            set_time_limit(0);

            /* Filter some of the variables */
            $_POST['title'] = input_clean($_POST['title'], 128);
            $_POST['description'] = input_clean($_POST['description'], 1024);
            $_POST['url'] = get_url($_POST['url'], 512);
            $_POST['icon'] = input_clean($_POST['icon'], 64);

            /* Users ids */
            $_POST['users_ids'] = trim($_POST['users_ids'] ?? '');
            $_POST['users_ids'] = array_filter(array_map('intval', explode(',', $_POST['users_ids'])));
            $_POST['users_ids'] = array_values(array_unique($_POST['users_ids']));
            $_POST['users_ids'] = $_POST['users_ids'] ?: [0];

            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            if(!\Altum\Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            $required_fields = ['title', 'description'];
            foreach($required_fields as $field) {
                if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                    Alerts::add_field_error($field, l('global.error_message.empty_field'));
                }
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

                /* Get all the users needed */
                switch($_POST['segment']) {
                    case 'all':
                        $users = db()->get('users', null, ['user_id', 'name', 'email', 'continent_code', 'country', 'city_name', 'device_type', 'os_name', 'browser_name', 'browser_language']);
                        break;

                    case 'custom':
                        $users = db()->where('user_id', $_POST['users_ids'], 'IN')->get('users', null, ['user_id', 'name', 'email', 'continent_code', 'country', 'city_name', 'device_type', 'os_name', 'browser_name', 'browser_language']);
                        break;

                    case 'filter':

                        $query = db();

                        $has_filters = false;

                        /* Is subscribed */
                        $_POST['filters_is_newsletter_subscribed'] = isset($_POST['filters_is_newsletter_subscribed']) ? (bool) $_POST['filters_is_newsletter_subscribed'] : 0;

                        if($_POST['filters_is_newsletter_subscribed']) {
                            $has_filters = true;
                            $query->where('is_newsletter_subscribed', 1);
                        }

                        /* Plans */
                        if(isset($_POST['filters_plans'])) {
                            $has_filters = true;
                            $query->where('plan_id', $_POST['filters_plans'], 'IN');
                        }

                        /* Status */
                        if(isset($_POST['filters_status'])) {
                            $has_filters = true;
                            $query->where('status', $_POST['filters_status'], 'IN');
                        }

                        /* Countries */
                        if(isset($_POST['filters_countries'])) {
                            $has_filters = true;
                            $query->where('country', $_POST['filters_countries'], 'IN');
                        }

                        /* Continents */
                        if(isset($_POST['filters_continents'])) {
                            $has_filters = true;
                            $query->where('continent_code', $_POST['filters_continents'], 'IN');
                        }

                        /* Source */
                        if(isset($_POST['filters_source'])) {
                            $has_filters = true;
                            $query->where('source', $_POST['filters_source'], 'IN');
                        }

                        /* Device type */
                        if(isset($_POST['filters_device_type'])) {
                            $has_filters = true;
                            $query->where('device_type', $_POST['filters_device_type'], 'IN');
                        }

                        /* Languages */
                        if(isset($_POST['filters_languages'])) {
                            $has_filters = true;
                            $query->where('language', $_POST['filters_languages'], 'IN');
                        }

                        /* Cities */
                        if(!empty($_POST['filters_cities'])) {
                            $_POST['filters_cities'] = is_array($_POST['filters_cities']) ? $_POST['filters_cities'] : explode(',', $_POST['filters_cities']);
                            $_POST['filters_cities'] = array_filter(array_unique($_POST['filters_cities']));

                            if(count($_POST['filters_cities'])) {
                                $_POST['filters_cities'] = array_map(function($city) {
                                    return query_clean($city);
                                }, $_POST['filters_cities']);
                                $_POST['filters_cities'] = array_unique($_POST['filters_cities']);

                                $has_filters = true;
                                $query->where('city_name', $_POST['filters_cities'], 'IN');
                            }
                        }

                        /* Languages */
                        if(isset($_POST['filters_browser_languages'])) {
                            $_POST['filters_browser_languages'] = array_filter($_POST['filters_browser_languages'], function($locale) {
                                return array_key_exists($locale, get_locale_languages_array());
                            });

                            $has_filters = true;
                            $query->where('browser_language', $_POST['filters_browser_languages'], 'IN');
                        }

                        /* Filters operating systems */
                        if(isset($_POST['filters_operating_systems'])) {
                            $_POST['filters_operating_systems'] = array_filter($_POST['filters_operating_systems'], function($os_name) {
                                return in_array($os_name, ['iOS', 'Android', 'Windows', 'OS X', 'Linux', 'Ubuntu', 'Chrome OS']);
                            });

                            $has_filters = true;
                            $query->where('os_name', $_POST['filters_operating_systems'], 'IN');
                        }

                        /* Filters browsers */
                        if(isset($_POST['filters_browsers'])) {
                            $_POST['filters_browsers'] = array_filter($_POST['filters_browsers'], function($browser_name) {
                                return in_array($browser_name, ['Chrome', 'Firefox', 'Safari', 'Edge', 'Opera', 'Samsung Internet']);
                            });

                            $has_filters = true;
                            $query->where('browser_name', $_POST['filters_browsers'], 'IN');
                        }

                        $users = $has_filters ? $query->get('users', null, ['user_id', 'name', 'email', 'continent_code', 'country', 'city_name', 'device_type', 'os_name', 'browser_name', 'browser_language']) : [];

                        break;
                }

                $users_batch = [];

                foreach($users as $key => $user) {
                    $replacers = [
                        '{{WEBSITE_TITLE}}' => settings()->main->title,
                        '{{USER:NAME}}' => $user->name,
                        '{{USER:EMAIL}}' => $user->email,
                        '{{USER:CONTINENT_NAME}}' => get_continent_from_continent_code($user->continent_code),
                        '{{USER:COUNTRY_NAME}}' => get_country_from_country_code($user->country),
                        '{{USER:CITY_NAME}}' => $user->city_name,
                        '{{USER:DEVICE_TYPE}}' => l('global.device.' . $user->device_type),
                        '{{USER:OS_NAME}}' => $user->os_name,
                        '{{USER:BROWSER_NAME}}' => $user->browser_name,
                        '{{USER:BROWSER_LANGUAGE}}' => get_language_from_locale($user->browser_language),
                    ];

                    $title = process_spintax(str_replace(
                        array_keys($replacers),
                        array_values($replacers),
                        $_POST['title']
                    ));

                    $description = process_spintax(str_replace(
                        array_keys($replacers),
                        array_values($replacers),
                        $_POST['description']
                    ));

                    /* Database query */
                    $users_batch[$user->user_id] = [
                        'user_id' => $user->user_id,
                        'for_who' => 'user',
                        'from_who' => 'admin',
                        'title' => $title,
                        'description' => $description,
                        'url' => $_POST['url'],
                        'icon' => $_POST['icon'],
                        'datetime' => get_date(),
                    ];

                    unset($users[$key]);
                }

                /* Insert data */
                db()->insertInChunks('internal_notifications', $users_batch);

                /* Users ids */
                $users_ids_chunks = array_chunk(array_keys($users_batch), 5000);

                /* Database query */
                foreach($users_ids_chunks as $users_ids) {
                    db()->where('user_id', $users_ids, 'IN')->update('users', [
                        'has_pending_internal_notifications' => 1
                    ]);
                }

                /* Clear the cache */
                cache()->clear();

                /* Set a nice success message */
                Alerts::add_success(sprintf(l('global.success_message.create1'), '<strong>' . $_POST['title'] . '</strong>'));

                redirect('admin/internal-notifications');
            }
        }

        $values = [
            'title' => $_POST['title'] ?? $_GET['title'] ?? null,
            'description' => $_POST['description'] ?? $_GET['description'] ?? null,
            'url' => $_POST['url'] ?? $_GET['url'] ?? null,
            'icon' => $_POST['icon'] ?? $_GET['icon'] ?? 'fas fa-bolt',
            'segment' => $_POST['segment'] ?? $_GET['segment'] ?? 'all',
            'users_ids' => implode(',', $_POST['users_ids'] ?? []),
            'filters_is_newsletter_subscribed' => $_POST['filters_is_newsletter_subscribed'] ?? [],
            'filters_plans' => $_POST['filters_plans'] ?? [],
            'filters_status' => $_POST['filters_status'] ?? [],
            'filters_source' => $_POST['filters_source'] ?? [],
            'filters_device_type' => $_POST['filters_device_type'] ?? [],
            'filters_continents' => $_POST['filters_continents'] ?? [],
            'filters_countries' => $_POST['filters_countries'] ?? [],
            'filters_cities' => isset($_POST['filters_cities']) && implode(',', is_array($_POST['filters_cities']) ? $_POST['filters_cities'] : []),
            'filters_browser_languages' => $_POST['filters_browser_languages'] ?? [],
            'filters_languages' => $_POST['filters_languages'] ?? [],
            'filters_operating_systems' => $_POST['filters_operating_systems'] ?? [],
            'filters_browsers' => $_POST['filters_browsers'] ?? [],
        ];

        /* Main View */
        $data = [
            'values' => $values,
            'plans' => $plans,
        ];

        $view = new \Altum\View('admin/internal-notification-create/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
