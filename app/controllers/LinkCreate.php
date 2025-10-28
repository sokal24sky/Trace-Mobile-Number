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
use Altum\Date;


defined('ALTUMCODE') || die();

class LinkCreate extends Controller {

    public function index() {

        \Altum\Authentication::guard();

        /* Team checks */
        if(\Altum\Teams::is_delegated() && !\Altum\Teams::has_access('create.links')) {
            Alerts::add_info(l('global.info_message.team_no_access'));
            redirect('links');
        }

        /* Check for the plan limit */
        $total_rows = database()->query("SELECT COUNT(*) AS `total` FROM `links` WHERE `user_id` = {$this->user->user_id}")->fetch_object()->total ?? 0;

        if($this->user->plan_settings->links_limit != -1 && $total_rows >= $this->user->plan_settings->links_limit) {
            Alerts::add_info(l('global.info_message.plan_feature_limit'));
            redirect('links');
        }

        /* Get available custom domains */
        $domains = (new \Altum\Models\Domain())->get_available_domains_by_user($this->user);

        /* Get available projects */
        $projects = (new \Altum\Models\Projects())->get_projects_by_user_id($this->user->user_id);

        /* Get available pixels */
        $pixels = (new \Altum\Models\Pixel())->get_pixels($this->user->user_id);

        /* Targeting types */
        $targeting_types = ['continent_code', 'country_code', 'city_name', 'device_type', 'browser_language', 'rotation', 'os_name', 'browser_name'];

        if(!empty($_POST)) {
            $_POST['location_url'] = get_url($_POST['location_url']);
            $_POST['url'] = !empty($_POST['url']) && $this->user->plan_settings->custom_url_is_enabled ? get_slug($_POST['url'], '-', false) : false;
            $_POST['domain_id'] = isset($_POST['domain_id']) && isset($domains[$_POST['domain_id']]) ? (!empty($_POST['domain_id']) ? (int) $_POST['domain_id'] : null) : null;
            $_POST['is_enabled'] = (int) isset($_POST['is_enabled']);

            /* Bulk */
            $_POST['is_bulk'] = (int) isset($_POST['is_bulk']);

            if($_POST['is_bulk']) {
                $_POST['url'] = null;
            }

            /* Pixels */
            $_POST['pixels_ids'] = isset($_POST['pixels_ids']) ? array_map(
                function($pixel_id) {
                    return (int) $pixel_id;
                },
                array_filter($_POST['pixels_ids'], function($pixel_id) use($pixels) {
                    return array_key_exists($pixel_id, $pixels);
                })
            ) : [];
            $_POST['pixels_ids'] = json_encode($_POST['pixels_ids']);

            /* Temporary URL */
            $_POST['schedule'] = (int) isset($_POST['schedule']);
            if($_POST['schedule'] && !empty($_POST['start_date']) && !empty($_POST['end_date']) && Date::validate($_POST['start_date'], 'Y-m-d H:i:s') && Date::validate($_POST['end_date'], 'Y-m-d H:i:s')) {
                $_POST['start_date'] = (new \DateTime($_POST['start_date'], new \DateTimeZone($this->user->timezone)))->setTimezone(new \DateTimeZone(\Altum\Date::$default_timezone))->format('Y-m-d H:i:s');
                $_POST['end_date'] = (new \DateTime($_POST['end_date'], new \DateTimeZone($this->user->timezone)))->setTimezone(new \DateTimeZone(\Altum\Date::$default_timezone))->format('Y-m-d H:i:s');
            } else {
                $_POST['start_date'] = $_POST['end_date'] = null;
            }
            $_POST['expiration_url'] = get_url($_POST['expiration_url'] ?? null);
            $_POST['pageviews_limit'] = empty($_POST['pageviews_limit']) ? null : (int) $_POST['pageviews_limit'];

            /* Protection */
            $_POST['sensitive_content'] = (int) isset($_POST['sensitive_content']);
            $_POST['password'] = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;

            /* Advanced */
            $_POST['project_id'] = !empty($_POST['project_id']) && array_key_exists($_POST['project_id'], $projects) ? (int) $_POST['project_id'] : null;

            /* Targeting */
            $_POST['targeting_type'] = in_array($_POST['targeting_type'], array_merge(['false'], $targeting_types)) ? query_clean($_POST['targeting_type']) : 'false';

            /* App linking */
            $_POST['app_linking_is_enabled'] = (int) isset($_POST['app_linking_is_enabled']);

            $app_linking = [
                'ios_location_url' => null,
                'android_location_url' => null,
                'app' => null,
            ];

            if($_POST['app_linking_is_enabled']) {
                $supported_apps = require APP_PATH . 'includes/app_linking.php';
                foreach($supported_apps as $app_key => $app) {
                    foreach($app['formats'] as $format => $targets) {

                        if(preg_match('/' . $targets['regex'] . '/', $_POST['location_url'], $match)) {
                            if(
                                parse_url($_POST['location_url'], PHP_URL_HOST) === parse_url('https://' . str_replace('%s', 'placeholder', $format), PHP_URL_HOST)
                            ) {
                                if(count($match) > 1) {
                                    array_shift($match);
                                    $app_linking['ios_location_url'] = vsprintf($targets['iOS'], $match);
                                    $app_linking['android_location_url'] = vsprintf($targets['Android'], $match);
                                    $app_linking['app'] = $app_key;
                                }

                                break 2;
                            }
                        }

                    }
                }
            }

            /* Cloaking */
            $_POST['cloaking_is_enabled'] = (int) isset($_POST['cloaking_is_enabled']);
            $_POST['cloaking_title'] = input_clean($_POST['cloaking_title'], 70);
            $_POST['cloaking_meta_description'] = input_clean($_POST['cloaking_meta_description'], 160);
            $_POST['cloaking_custom_js'] = mb_substr(trim($_POST['cloaking_custom_js']), 0, 10000);
            $cloaking_favicon = \Altum\Uploads::process_upload(null, 'favicons', 'cloaking_favicon', 'cloaking_favicon_remove', settings()->links->favicon_size_limit);
            $cloaking_opengraph = \Altum\Uploads::process_upload(null, 'opengraphs', 'cloaking_opengraph', 'cloaking_opengraph_remove', settings()->links->opengraph_size_limit);

            /* HTTP */
            $_POST['http_status_code'] = in_array($_POST['http_status_code'], [301, 302, 307, 308]) ? (int) $_POST['http_status_code'] : 301;

            /* Query parameters forwarding */
            $_POST['forward_query_parameters_is_enabled'] = (int) isset($_POST['forward_query_parameters_is_enabled']);

            /* UTM */
            $_POST['utm_medium'] = input_clean($_POST['utm_medium'], 128);
            $_POST['utm_source'] = input_clean($_POST['utm_source'], 128);
            $_POST['utm_campaign'] = input_clean($_POST['utm_campaign'], 128);

            //ALTUMCODE:DEMO if(DEMO) if($this->user->user_id == 1) Alerts::add_error('Please create an account on the demo to test out this function.');

            /* Check for any errors */
            $required_fields = $_POST['is_bulk'] ? ['location_urls'] : ['location_url'];
            foreach($required_fields as $field) {
                if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                    Alerts::add_field_error($field, l('global.error_message.empty_field'));
                }
            }

            if(!\Altum\Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            /* Check for duplicate url if needed */
            if($_POST['url']) {
                $domain_id_where = $_POST['domain_id'] ? "AND `domain_id` = {$_POST['domain_id']}" : "AND `domain_id` IS NULL";
                $is_existing_link = database()->query("SELECT `link_id` FROM `links` WHERE `url` = '{$_POST['url']}' {$domain_id_where}")->num_rows;

                if($is_existing_link) {
                    Alerts::add_field_error('url', l('links.error_message.url_exists'));
                }

                if(array_key_exists($_POST['url'], \Altum\Router::$routes['']) || in_array($_POST['url'], \Altum\Language::$active_languages) || file_exists(ROOT_PATH . $_POST['url'])) {
                    Alerts::add_field_error('url', l('links.error_message.blacklisted_url'));
                }

                if(in_array($_POST['url'], settings()->links->blacklisted_keywords)) {
                    Alerts::add_field_error('url', l('links.error_message.blacklisted_keyword'));
                }

                /* Make sure the custom url meets the requirements */
                if(mb_strlen($_POST['url']) < ($this->user->plan_settings->url_minimum_characters ?? 1)) {
                    Alerts::add_field_error('url', sprintf(l('links.error_message.url_minimum_characters'), ($this->user->plan_settings->url_minimum_characters ?? 1)));
                }

                if(mb_strlen($_POST['url']) > ($this->user->plan_settings->url_maximum_characters ?? 64)) {
                    Alerts::add_field_error('url', sprintf(l('links.error_message.url_maximum_characters'), ($this->user->plan_settings->url_maximum_characters ?? 64)));
                }
            }

            if(!$_POST['is_bulk']) $this->check_location_url('location_url', $_POST['location_url']);
            $this->check_location_url('expiration_url', $_POST['expiration_url'], true);

            /* Bulk processing */
            if($_POST['is_bulk']) {
                $location_urls = preg_split('/\r\n|\r|\n/', $_POST['location_urls']);

                foreach($location_urls as $key => $location_url) {
                    /* Skip empty lines */
                    if(empty(trim($location_url))) {
                        unset($location_urls[$key]);
                        continue;
                    }

                    $this->check_location_url('location_urls', $location_url, true);
                }

                /* error checks */
                $total_bulk_urls = count($location_urls);
                if(!$total_bulk_urls) {
                    Alerts::add_field_error('location_urls', l('global.error_message.empty_field'));
                }

                if($this->user->plan_settings->links_limit != -1 && $total_rows + $total_bulk_urls > $this->user->plan_settings->links_limit) {
                    Alerts::add_field_error('location_urls', l('global.info_message.plan_feature_limit'));
                }
            }

            $settings = [
                'app_linking_is_enabled' => $_POST['app_linking_is_enabled'],
                'app_linking' => $app_linking,
                'cloaking_is_enabled' => $_POST['cloaking_is_enabled'],
                'cloaking_title' => $_POST['cloaking_title'],
                'cloaking_meta_description' => $_POST['cloaking_meta_description'],
                'cloaking_custom_js' => $_POST['cloaking_custom_js'],
                'cloaking_favicon' => $cloaking_favicon,
                'cloaking_opengraph' => $cloaking_opengraph,
                'http_status_code' => $_POST['http_status_code'],
                'schedule' => $_POST['schedule'],
                'start_date' => $_POST['start_date'],
                'end_date' => $_POST['end_date'],
                'pageviews_limit' => $_POST['pageviews_limit'],
                'expiration_url' => $_POST['expiration_url'],
                'password' => $_POST['password'],
                'sensitive_content' => $_POST['sensitive_content'],
                'targeting_type' => $_POST['targeting_type'],
                'forward_query_parameters_is_enabled' => $_POST['forward_query_parameters_is_enabled'],

                /* UTM */
                'utm' => [
                    'source' => $_POST['utm_source'],
                    'medium' => $_POST['utm_medium'],
                    'campaign' => $_POST['utm_campaign'],
                ]
            ];

            /* Process the targeting */
            foreach($targeting_types as $targeting_type) {
                ${'targeting_' . $targeting_type} = [];

                if(isset($_POST['targeting_' . $targeting_type . '_key'])) {
                    foreach($_POST['targeting_' . $targeting_type . '_key'] as $key => $value) {
                        if(empty(trim($_POST['targeting_' . $targeting_type . '_value'][$key]))) continue;

                        $this->check_location_url('targeting_' . $targeting_type . '_value[' . $key . ']', $_POST['targeting_' . $targeting_type . '_value'][$key]);

                        ${'targeting_' . $targeting_type}[] = [
                            'key' => trim(query_clean($value)),
                            'value' => get_url($_POST['targeting_' . $targeting_type . '_value'][$key]),
                        ];
                    }

                    $settings['targeting_' . $targeting_type] = ${'targeting_' . $targeting_type};
                }
            }


            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

                /* Clear the cache */
                cache()->deleteItem('links?user_id=' . $this->user->user_id);
                cache()->deleteItem('links_total?user_id=' . $this->user->user_id);
                cache()->deleteItem('links_dashboard?user_id=' . $this->user->user_id);

                /* Single url */
                if(!$_POST['is_bulk']) {
                    $url = $_POST['url'] ?: $this->generate_random_url();

                    /* Settings */
                    $settings = json_encode($settings);

                    /* Database query */
                    $link_id = db()->insert('links', [
                        'user_id' => $this->user->user_id,
                        'domain_id' => $_POST['domain_id'],
                        'project_id' => $_POST['project_id'],
                        'pixels_ids' => $_POST['pixels_ids'],
                        'url' => $url,
                        'location_url' => $_POST['location_url'],
                        'settings' => $settings,
                        'is_enabled' => $_POST['is_enabled'],
                        'datetime' => get_date(),
                    ]);

                    /* Set a nice success message */
                    Alerts::add_success(sprintf(l('global.success_message.create1'), '<strong>' . $url . '</strong>'));

                    redirect('link-update/' . $link_id . ($this->user->preferences->links_auto_copy_link ? '?auto_copy_link=true' : ''));

                }

                /* Bulk URLS */
                if($_POST['is_bulk']) {
                    $i = 1;

                    foreach($location_urls as $location_url) {
                        $url = $this->generate_random_url();

                        /* App linking processing per each URL */
                        $app_linking = [
                            'ios_location_url' => null,
                            'android_location_url' => null,
                            'app' => null,
                        ];

                        if($_POST['app_linking_is_enabled']) {
                            $supported_apps = require APP_PATH . 'includes/app_linking.php';
                            foreach($supported_apps as $app_key => $app) {
                                foreach($app['formats'] as $format => $targets) {

                                    if(preg_match('/' . $targets['regex'] . '/', $location_url, $match)) {
                                        if(
                                            str_contains($location_url, str_replace('%s', '', $format)) ||
                                            str_contains($location_url, preg_replace('/%s.*/', '', $format))
                                        ) {
                                            if(count($match) > 1) {
                                                array_shift($match);
                                                $app_linking['ios_location_url'] = vsprintf($targets['iOS'], $match);
                                                $app_linking['android_location_url'] = vsprintf($targets['Android'], $match);
                                                $app_linking['app'] = $app_key;
                                            }

                                            break 2;
                                        }
                                    }

                                }
                            }
                        }

                        /* Settings */
                        $settings = json_encode($settings);

                        /* Database query */
                        $link_id = db()->insert('links', [
                            'user_id' => $this->user->user_id,
                            'domain_id' => $_POST['domain_id'],
                            'project_id' => $_POST['project_id'],
                            'pixels_ids' => $_POST['pixels_ids'],
                            'url' => $url,
                            'location_url' => $location_url,
                            'settings' => $settings,
                            'is_enabled' => $_POST['is_enabled'],
                            'datetime' => get_date(),
                        ]);

                        /* Set a nice success message */
                        Alerts::add_success(sprintf(l('global.success_message.create1'), '<strong>' . $url . ' - #' . $i++ . '</strong>'));

                        /* Do not allow more than X at once */
                        if($i >= $this->user->plan_settings->links_bulk_limit) {
                            break;
                        }
                    }

                    redirect('links');
                }
            }
        }

        /* Set default values */
        $values = [
            'location_url' => $_POST['location_url'] ?? '',
            'location_urls' => $_POST['location_urls'] ?? '',
            'url' => $_POST['url'] ?? '',
            'domain_id' => $_POST['domain_id'] ?? '',
            'project_id' => $_POST['project_id'] ?? '',
            'pixels_ids' => json_decode($_POST['pixels_ids'] ?? '[]'),
            'is_bulk' => $_POST['is_bulk'] ?? false,
            'is_enabled' => $_POST['is_enabled'] ?? true,
            'app_linking_is_enabled' => $_POST['app_linking_is_enabled'] ?? true,
            'app_linking' => $app_linking ?? [],
            'pixels' => $_POST['pixels'] ?? [],
            'schedule' => $_POST['schedule'] ?? false,
            'start_date' => $_POST['start_date'] ?? '',
            'end_date' => $_POST['end_date'] ?? '',
            'pageviews_limit' => $_POST['pageviews_limit'] ?? '',
            'expiration_url' => $_POST['expiration_url'] ?? '',
            'targeting_type' => $_POST['targeting_type'] ?? 'false',
            'utm_source' => $_POST['utm_source'] ?? '',
            'utm_medium' => $_POST['utm_medium'] ?? '',
            'utm_campaign' => $_POST['utm_campaign'] ?? '',
            'password' => $_POST['password'] ?? '',
            'sensitive_content' => $_POST['sensitive_content'] ?? '',
            'cloaking_is_enabled' => $_POST['cloaking_is_enabled'] ?? false,
            'cloaking_title' => $_POST['cloaking_title'] ?? '',
            'cloaking_meta_description' => $_POST['cloaking_meta_description'] ?? '',
            'cloaking_custom_js' => $_POST['cloaking_custom_js'] ?? '',
            'http_status_code' => $_POST['http_status_code'] ?? 301,
            'forward_query_parameters_is_enabled' => $_POST['forward_query_parameters_is_enabled'] ?? false,
        ];

        foreach($targeting_types as $targeting_type) {
            $values['targeting_' . $targeting_type] = ${'targeting_' . $targeting_type} ?? [];
        }

        /* Prepare the view */
        $data = [
            'domains' => $domains,
            'pixels' => $pixels,
            'projects' => $projects,
            'values' => $values
        ];

        $view = new \Altum\View('link-create/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    private function generate_random_url() {
        $is_existing_link = true;
        $url = null;

        /* Generate random url if not specified */
        while($is_existing_link) {
            $url = mb_strtolower(string_generate(settings()->links->random_url_length ?? 7));

            $domain_id_where = $_POST['domain_id'] ? "AND `domain_id` = {$_POST['domain_id']}" : "AND `domain_id` IS NULL";
            $is_existing_link = database()->query("SELECT `link_id` FROM `links` WHERE `url` = '{$_POST['url']}' {$domain_id_where}")->num_rows;
        }

        return $url;
    }

    /* Function to bundle together all the checks of an url */
    private function check_location_url($key, $url, $can_be_empty = false) {

        if(empty(trim($url)) && $can_be_empty) {
            return;
        }

        if(empty(trim($url))) {
            Alerts::add_field_error($key, l('global.error_message.empty_fields'));
        }

        $url_details = parse_url($url);

        if(!isset($url_details['scheme'])) {
            Alerts::add_field_error($key, l('links.error_message.invalid_location_url'));
        }

        /* Make sure the domain is not blacklisted */
        $domain = get_domain_from_url($url);

        if($domain && in_array($domain, settings()->links->blacklisted_domains)) {
            Alerts::add_field_error($key, l('links.error_message.blacklisted_domain'));
        }

        /* Check the url with google safe browsing to make sure it is a safe website */
        if(settings()->links->google_safe_browsing_is_enabled) {
            if(google_safe_browsing_check($url, settings()->links->google_safe_browsing_api_key)) {
                Alerts::add_field_error($key, l('links.error_message.blacklisted_location_url'));
            }
        }
    }
}
