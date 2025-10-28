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
use Altum\Meta;
use Altum\Models\User;
use Altum\Title;

defined('ALTUMCODE') || die();

class Link extends Controller {
    public $link = null;
    public $link_user = null;
    public $has_access = null;

    public function index() {

        $this->link = \Altum\Router::$data['link'];

        /* Make sure there are no extra URL additions */
        if(isset($this->params[1])) {
            redirect('not-found');
        }

        /* Make sure the link is active */
        if(!$this->link->is_enabled) {
            redirect('not-found');
        }

        $this->link_user = (new User())->get_user_by_user_id($this->link->user_id);

        /* Make sure to check if the user is active */
        if($this->link_user->status != 1) {
            redirect('not-found');
        }

        /* Process the plan of the user */
        (new User())->process_user_plan_expiration_by_user($this->link_user);

        /* Parse some details */
        foreach(['settings', 'pixels_ids'] as $key) {
            $this->link->{$key} = json_decode($this->link->{$key});
        }

        /* Check for temporary URL */
        if(isset($this->link->settings->pageviews_limit) && $this->link->settings->pageviews_limit) {
            $current_pageviews = db()->where('link_id', $this->link->link_id)->getValue('links', 'pageviews');
        }

        if(
            (
                $this->link->settings->schedule && !empty($this->link->settings->start_date) && !empty($this->link->settings->end_date) &&
                (
                    \Altum\Date::get('', null) < \Altum\Date::get($this->link->settings->start_date, null, \Altum\Date::$default_timezone) ||
                    \Altum\Date::get('', null) > \Altum\Date::get($this->link->settings->end_date, null, \Altum\Date::$default_timezone)
                )
            )
            || (isset($current_pageviews) && $current_pageviews >= $this->link->settings->pageviews_limit)
        ) {
            if($this->link->settings->expiration_url) {
                header('Location: ' . $this->link->settings->expiration_url, true, $this->link->settings->http_status_code ?? 301);
                die();
            } else {
                redirect('not-found');
            }
        }

        /* Check if the user has access to the link */
        $this->has_access = !$this->link->settings->password || ($this->link->settings->password && isset($_COOKIE['link_password_' . $this->link->link_id]) && $_COOKIE['link_password_' . $this->link->link_id] == $this->link->settings->password);

        /* Do not let the user have password protection if the plan doesnt allow it */
        if(!$this->link_user->plan_settings->password_protection_is_enabled) {
            $this->has_access = true;
        }

        /* Set the default language of the user, including the link timezone */
        \Altum\Language::set_by_name($this->link_user->language);

        /* Meta */
        Meta::set_canonical_url($this->link->full_url);

        /* Check if the password form is submitted */
        if(!$this->has_access && !empty($_POST)) {
            /* Check for any errors */
            if(!\Altum\Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            if(!password_verify($_POST['password'], $this->link->settings->password)) {
                Alerts::add_field_error('password', l('l_link.password.error_message'));
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {
                /* Set a cookie */
                setcookie('link_password_' . $this->link->link_id, $this->link->settings->password, time()+60*60*24*30);

                header('Location: ' . $this->link->full_url);

                die();
            }
        }

        /* Check if the user has access to the link */
        $can_see_content = !$this->link->settings->sensitive_content || ($this->link->settings->sensitive_content && isset($_COOKIE['link_sensitive_content_' . $this->link->link_id]));

        /* Do not let the user have password protection if the plan doesnt allow it */
        if(!$this->link_user->plan_settings->sensitive_content_is_enabled) {
            $can_see_content = true;
        }

        /* Check if the password form is submitted */
        if(!$can_see_content && !empty($_POST) && isset($_POST['type']) && $_POST['type'] == 'sensitive_content') {
            /* Check for any errors */
            if(!\Altum\Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {
                /* Set a cookie */
                setcookie('link_sensitive_content_' . $this->link->link_id, 'true', time()+60*60*24*30);

                header('Location: ' . $this->link->full_url);

                die();
            }
        }

        /* Display the password form */
        if(!$this->has_access) {
            /* Set a custom title */
            Title::set(l('l_link.password.title'));

            /* Main View */
            $data = [
                'link' => $this->link,
            ];

            $view = new \Altum\View('l/partials/password', (array) $this);
            $this->add_view_content('content', $view->run($data));
        }

        else if(!$can_see_content) {

            /* Set a custom title */
            Title::set(l('l_link.sensitive_content.title'));

            /* Main View */
            $view = new \Altum\View('l/partials/sensitive_content', (array) $this);

            $this->add_view_content('content', $view->run());

        }

        /* No password or access granted */
        else {

            $this->create_statistics();
            $this->process_redirect();

        }

    }

    /* Insert statistics log */
    private function create_statistics() {

        $cookie_name = 'l_statistics_' . $this->link->link_id;

        if(isset($_COOKIE[$cookie_name]) && (int) $_COOKIE[$cookie_name] >= 3) {
            return;
        }

        if(!$this->link_user->plan_settings->analytics_is_enabled) {
            return;
        }

        /* Ignore excluded ips */
        $excluded_ips = array_flip($this->link_user->preferences->excluded_ips ?? []);
        if(isset($excluded_ips[get_ip()])) return;

        /* Detect extra details about the user */
        $whichbrowser = new \WhichBrowser\Parser($_SERVER['HTTP_USER_AGENT']);

        /* Do not track bots */
        if($whichbrowser->device->type == 'bot') {
            return;
        }

        /* Detect extra details about the user */
        $browser_name = $whichbrowser->browser->name ?? null;
        $os_name = $whichbrowser->os->name ?? null;
        $browser_language = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? mb_substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) : null;
        $device_type = get_this_device_type();
        $is_unique = isset($_COOKIE[$cookie_name]) ? 0 : 1;

        /* Detect the location */
        try {
            $maxmind = (get_maxmind_reader_city())->get(get_ip());
        } catch(\Exception $exception) {
            /* :) */
        }
        $continent_code = isset($maxmind) && isset($maxmind['continent']) ? $maxmind['continent']['code'] : null;
        $country_code = isset($maxmind) && isset($maxmind['country']) ? $maxmind['country']['iso_code'] : null;
        $city_name = isset($maxmind) && isset($maxmind['city']) ? $maxmind['city']['names']['en'] : null;

        /* Process referrer */
        $referrer = [
            'host' => null,
            'path' => null
        ];

        if(isset($_SERVER['HTTP_REFERER'])) {
            $referrer = parse_url($_SERVER['HTTP_REFERER']);

            if($_SERVER['HTTP_REFERER'] == $this->link->full_url) {
                $is_unique = 0;

                $referrer = [
                    'host' => null,
                    'path' => null
                ];
            }
        }

        /* Check if referrer actually comes from the QR code */
        if(isset($_GET['referrer']) && in_array($_GET['referrer'], ['qr', 'link'])) {
            $referrer = [
                'host' => $_GET['referrer'],
                'path' => null
            ];
        }

        $utm_source = input_clean($_GET['utm_source'] ?? null);
        $utm_medium = input_clean($_GET['utm_medium'] ?? null);
        $utm_campaign = input_clean($_GET['utm_campaign'] ?? null);

        /* Insert the log */
        db()->insert('statistics', [
            'link_id' => $this->link->link_id,
            'user_id' => $this->link->user_id,
            'project_id' => $this->link->project_id,
            'continent_code' => $continent_code,
            'country_code' => $country_code,
            'city_name' => $city_name,
            'os_name' => $os_name,
            'browser_name' => $browser_name,
            'referrer_host' => $referrer['host'],
            'referrer_path' => $referrer['path'],
            'device_type' => $device_type,
            'browser_language' => $browser_language,
            'utm_source' => $utm_source,
            'utm_medium' => $utm_medium,
            'utm_campaign' => $utm_campaign,
            'is_unique' => $is_unique,
            'datetime' => get_date(),
        ]);

        /* Add the unique hit to the link table as well */
        db()->where('link_id', $this->link->link_id)->update('links', ['pageviews' => db()->inc()]);

        /* Set cookie to try and avoid multiple entrances */
        $cookie_new_value = isset($_COOKIE[$cookie_name]) ? (int) $_COOKIE[$cookie_name] + 1 : 0;
        setcookie($cookie_name, (int) $cookie_new_value, time()+60*60*24*1);
    }

    public function process_redirect() {

        /* Check if we should redirect the user or kill the script */
        if(isset($_GET['no_redirect'])) {
            die();
        }

        /* Check for query forwarding */
        $append_query = null;
        if($this->link->settings->forward_query_parameters_is_enabled && \Altum\Router::$original_request_query) {
            $append_query = \Altum\Router::$original_request_query;
        }

        if($this->link_user->plan_settings->utm_parameters_is_enabled) {
            $utm_parameters = [];
            if($this->link->settings->utm->source) $utm_parameters['utm_source'] = $this->link->settings->utm->source;
            if($this->link->settings->utm->medium) $utm_parameters['utm_medium'] = $this->link->settings->utm->medium;
            if($this->link->settings->utm->campaign) $utm_parameters['utm_campaign'] = $this->link->settings->utm->campaign;

            if(count($utm_parameters)) {
                $append_query = $append_query ? $append_query . '&' . http_build_query($utm_parameters) : http_build_query($utm_parameters);
            }
        }

        if($append_query) {
            $parsed_url = parse_url($this->link->location_url);
            $already_existing_query_parameters = $parsed_url['query'] ?? '';
            $final_query_string = $already_existing_query_parameters . '&' . $append_query;

            parse_str($final_query_string, $final_query_array);
            $final_query_array = array_unique($final_query_array);

            $append_query = '?' . http_build_query($final_query_array);
            $this->link->location_url = $parsed_url['scheme'] . '://' . $parsed_url['host'] . ($parsed_url['path'] ?? '');
        }

        /* Check for targeting */
        if($this->link_user->plan_settings->targeting_is_enabled) {
            if($this->link->settings->targeting_type == 'continent_code') {
                /* Detect the location */
                try {
                    $maxmind = (get_maxmind_reader_country())->get(get_ip());
                } catch (\Exception $exception) {
                    /* :) */
                }
                $continent_code = isset($maxmind) && isset($maxmind['continent']) ? $maxmind['continent']['code'] : null;

                foreach ($this->link->settings->{'targeting_' . $this->link->settings->targeting_type} as $value) {
                    if($continent_code == $value->key) {
                        $this->redirect_to(
                            $value->value . $append_query,
                            $this->link_user->plan_settings->cloaking_is_enabled && $this->link->settings->cloaking_is_enabled ? $this->link->settings : false,
                            $this->link_user->plan_settings->app_linking_is_enabled && $this->link->settings->app_linking_is_enabled && $this->link->settings->app_linking->app ? $this->link->settings->app_linking : false,
                        );
                    }
                }
            }

            if($this->link->settings->targeting_type == 'country_code') {
                /* Detect the location */
                try {
                    $maxmind = (get_maxmind_reader_country())->get(get_ip());
                } catch (\Exception $exception) {
                    /* :) */
                }
                $country_code = isset($maxmind) && isset($maxmind['country']) ? $maxmind['country']['iso_code'] : null;

                foreach ($this->link->settings->{'targeting_' . $this->link->settings->targeting_type} as $value) {
                    if($country_code == $value->key) {
                        $this->redirect_to(
                            $value->value . $append_query,
                            $this->link_user->plan_settings->cloaking_is_enabled && $this->link->settings->cloaking_is_enabled ? $this->link->settings : false,
                            $this->link_user->plan_settings->app_linking_is_enabled && $this->link->settings->app_linking_is_enabled && $this->link->settings->app_linking->app ? $this->link->settings->app_linking : false,
                        );
                    }
                }
            }

            if($this->link->settings->targeting_type == 'city_name') {
                /* Detect the location */
                try {
                    $maxmind = (get_maxmind_reader_city())->get(get_ip());
                } catch (\Exception $exception) {
                    /* :) */
                }
                $city_name = isset($maxmind) && isset($maxmind['city']) ? $maxmind['city']['names']['en'] : null;

                foreach ($this->link->settings->{'targeting_' . $this->link->settings->targeting_type} as $value) {
                    if($city_name == $value->key) {
                        $this->redirect_to(
                            $value->value . $append_query,
                            $this->link_user->plan_settings->cloaking_is_enabled && $this->link->settings->cloaking_is_enabled ? $this->link->settings : false,
                            $this->link_user->plan_settings->app_linking_is_enabled && $this->link->settings->app_linking_is_enabled && $this->link->settings->app_linking->app ? $this->link->settings->app_linking : false,
                        );
                    }
                }
            }

            if($this->link->settings->targeting_type == 'device_type') {
                $device_type = get_this_device_type();

                foreach ($this->link->settings->{'targeting_' . $this->link->settings->targeting_type} as $value) {
                    if($device_type == $value->key) {
                        $this->redirect_to(
                            $value->value . $append_query,
                            $this->link_user->plan_settings->cloaking_is_enabled && $this->link->settings->cloaking_is_enabled ? $this->link->settings : false,
                            $this->link_user->plan_settings->app_linking_is_enabled && $this->link->settings->app_linking_is_enabled && $this->link->settings->app_linking->app ? $this->link->settings->app_linking : false,
                        );
                    }
                }
            }

            if($this->link->settings->targeting_type == 'os_name') {
                /* Detect extra details about the user */
                $whichbrowser = new \WhichBrowser\Parser($_SERVER['HTTP_USER_AGENT']);
                $os_name = $whichbrowser->os->name ?? null;

                foreach ($this->link->settings->{'targeting_' . $this->link->settings->targeting_type} as $value) {
                    if($os_name == $value->key) {
                        $this->redirect_to(
                            $value->value . $append_query,
                            $this->link_user->plan_settings->cloaking_is_enabled && $this->link->settings->cloaking_is_enabled ? $this->link->settings : false,
                            $this->link_user->plan_settings->app_linking_is_enabled && $this->link->settings->app_linking_is_enabled && $this->link->settings->app_linking->app ? $this->link->settings->app_linking : false,
                        );
                    }
                }
            }

            if($this->link->settings->targeting_type == 'browser_name') {
                /* Detect extra details about the user */
                $whichbrowser = new \WhichBrowser\Parser($_SERVER['HTTP_USER_AGENT']);
                $browser_name = $whichbrowser->browser->name ?? null;

                foreach ($this->link->settings->{'targeting_' . $this->link->settings->targeting_type} as $value) {
                    if($browser_name == $value->key) {
                        $this->redirect_to(
                            $value->value . $append_query,
                            $this->link_user->plan_settings->cloaking_is_enabled && $this->link->settings->cloaking_is_enabled ? $this->link->settings : false,
                            $this->link_user->plan_settings->app_linking_is_enabled && $this->link->settings->app_linking_is_enabled && $this->link->settings->app_linking->app ? $this->link->settings->app_linking : false,
                        );
                    }
                }
            }

            if($this->link->settings->targeting_type == 'browser_language') {
                $browser_language = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? mb_substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) : null;

                foreach ($this->link->settings->{'targeting_' . $this->link->settings->targeting_type} as $value) {
                    if($browser_language == $value->key) {
                        $this->redirect_to(
                            $value->value . $append_query,
                            $this->link_user->plan_settings->cloaking_is_enabled && $this->link->settings->cloaking_is_enabled ? $this->link->settings : false,
                            $this->link_user->plan_settings->app_linking_is_enabled && $this->link->settings->app_linking_is_enabled && $this->link->settings->app_linking->app ? $this->link->settings->app_linking : false,
                        );
                    }
                }
            }

            if($this->link->settings->targeting_type == 'rotation') {
                $total_chances = 0;

                foreach ($this->link->settings->{'targeting_' . $this->link->settings->targeting_type} as $value) {
                    $total_chances += (int) $value->key;
                }

                $chosen_winner = rand(0, $total_chances - 1);

                $start = 0;
                $end = 0;

                foreach ($this->link->settings->{'targeting_' . $this->link->settings->targeting_type} as $value) {
                    $chance = (int) $value->key;
                    $end += $chance;

                    if($chosen_winner >= $start && $chosen_winner < $end) {
                        $this->redirect_to(
                            $value->value . $append_query,
                            $this->link_user->plan_settings->cloaking_is_enabled && $this->link->settings->cloaking_is_enabled ? $this->link->settings : false,
                            $this->link_user->plan_settings->app_linking_is_enabled && $this->link->settings->app_linking_is_enabled && $this->link->settings->app_linking->app ? $this->link->settings->app_linking : false,
                        );
                    }

                    $start += $chance;
                }
            }
        }

        /* :) */
        $this->redirect_to(
            $this->link->location_url . $append_query,
            $this->link_user->plan_settings->cloaking_is_enabled && $this->link->settings->cloaking_is_enabled ? $this->link->settings : false,
            $this->link_user->plan_settings->app_linking_is_enabled && $this->link->settings->app_linking_is_enabled && $this->link->settings->app_linking->app ? $this->link->settings->app_linking : false,
        );
    }

    private function redirect_to($location_url, $cloaking = false, $app_linking = false) {
        if(!count($this->link->pixels_ids) && !$cloaking && !$app_linking) {

            /* Classic redirect */
            header('Location: ' . $location_url, true, $this->link->settings->http_status_code ?? 301);
            die();

        } else {

            /* App deep linking automatic detection */
            if($app_linking) {
                $whichbrowser = new \WhichBrowser\Parser($_SERVER['HTTP_USER_AGENT']);
                $os_name = $whichbrowser->os->name ?? null;
                $app_linking_location_url = null;

                if($os_name == 'iOS') {
                    $app_linking_location_url = $app_linking->ios_location_url;
                }

                if($os_name == 'Android') {
                    $app_linking_location_url = $app_linking->android_location_url;
                }
            }

            if(count($this->link->pixels_ids)) {
                /* Get the needed pixels */
                $pixels = count($this->link->pixels_ids) ? (new \Altum\Models\Pixel())->get_pixels_by_pixels_ids_and_user_id($this->link->pixels_ids, $this->link->user_id) : [];

                /* Prepare the pixels view */
                $pixels_view = new \Altum\View('l/partials/pixels');
                $this->add_view_content('pixels', $pixels_view->run(['pixels' => $pixels]));
            }

            /* Meta */
            Meta::set_social_url(url(\Altum\Router::$original_request));
            if($cloaking->cloaking_opengraph) Meta::set_social_image(\Altum\Uploads::get_full_url('opengraph') . $cloaking->cloaking_opengraph);
            if($cloaking->cloaking_title) Meta::set_social_title($cloaking->cloaking_title);
            if($cloaking->cloaking_meta_description) Meta::set_description($cloaking->cloaking_meta_description);


            /* Prepare & Output the view */
            $pixels_redirect_wrapper = new \Altum\View('l/pixels_redirect_wrapper', (array) $this);

            echo $pixels_redirect_wrapper->run([
                'app_linking_location_url' => $app_linking_location_url ?? null,
                'location_url' => $location_url,
                'cloaking' => $cloaking,
                'pixels' => $pixels ?? []
            ]);

            die();
        }
    }
}
