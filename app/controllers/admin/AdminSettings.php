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
use Altum\Title;


defined('ALTUMCODE') || die();

class AdminSettings extends Controller {

    public function index() {
        redirect('admin/settings/main');
    }

    private function process() {
        $method	= (isset(\Altum\Router::$method) && file_exists(THEME_PATH . 'views/admin/settings/partials/' . \Altum\Router::$method . '.php')) ? \Altum\Router::$method : 'main';
        $payment_processors = require APP_PATH . 'includes/payment_processors.php';

        /* Set a custom title */
        Title::set(sprintf(l('admin_settings.title'), l('admin_settings.' . $method . '.tab')));

        /* Method View */
        $view = new \Altum\View('admin/settings/partials/' . $method, (array) $this);
        $this->add_view_content('method', $view->run());

        /* Main View */
        $view = new \Altum\View('admin/settings/index', (array) $this);
        $this->add_view_content('content', $view->run([
            'method' => $method,
            'payment_processors' => $payment_processors,
        ]));
    }

    private function update_settings($key, $value) {
        if(!\Altum\Csrf::check()) {
            Alerts::add_error(l('global.error_message.invalid_csrf_token'));
        }

        if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

            /* Update the database */
            db()->where('`key`', $key)->update('settings', ['value' => $value]);

            $this->after_update_settings($key);
        }

        redirect('admin/settings/' . $key);
    }

    private function after_update_settings($key) {

        /* Clear the language cache */
        \Altum\Language::clear_cache();

        /* Clear the cache */
        cache()->deleteItem('settings');

        /* Set a nice success message */
        Alerts::add_success(l('global.success_message.update2'));

        /* Refresh the page */
        redirect('admin/settings/' . $key);

    }

    public function main() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* Make sure there is way to auto redirect yourself to the homepage infinitely */
            if($_POST['index_url']) {
                $site_url_parsed = parse_url(SITE_URL);
                $index_url_parsed = parse_url(settings()->main->index_url);

                if($site_url_parsed['host'] == $index_url_parsed['host'] && ($site_url_parsed['path'] == $index_url_parsed['path'] || $site_url_parsed['path'] == $index_url_parsed['path'] . '/')) {
                    $_POST['index_url'] = null;
                }
            }

            /* Make sure there is way to auto redirect yourself to the not found page infinitely */
            if($_POST['not_found_url']) {
                $site_url_parsed = parse_url(SITE_URL . 'not-found');
                $not_found_url_parsed = parse_url(settings()->main->not_found_url);

                if($site_url_parsed['host'] == $not_found_url_parsed['host'] && ($site_url_parsed['path'] == $not_found_url_parsed['path'] || $site_url_parsed['path'] == $not_found_url_parsed['path'] . '/')) {
                    $_POST['not_found_url'] = null;
                }
            }

            /* Uploads processing */
            foreach(['logo_light', 'logo_dark', 'logo_email', 'favicon', 'opengraph'] as $image_key) {
                settings()->main->{$image_key} = \Altum\Uploads::process_upload(settings()->main->{$image_key}, $image_key, $image_key, $image_key . '_remove', null);
            }

            $_POST['force_https_is_enabled'] = (int) isset($_POST['force_https_is_enabled']);
            if(!string_starts_with('https://', SITE_URL)) {
                $_POST['force_https_is_enabled'] = false;
            }

            /* AI Scraping */
            $_POST['ai_scraping_is_allowed'] = isset($_POST['ai_scraping_is_allowed']);
            $_POST['se_indexing'] = isset($_POST['se_indexing']);
            $_POST['iframe_embedding'] = trim(preg_replace('/\s+/', ' ', $_POST['iframe_embedding']));

            if(!is_writable(ROOT_PATH . 'robots.txt')) {
                Alerts::add_info(sprintf(l('global.error_message.directory_not_writable'), ROOT_PATH . 'robots.txt'));
            }

            /* Process content for robots.txt */
            $new_robots_content = '';

            /* Process Search engine Indexing */
            if($_POST['se_indexing']) {
                $new_robots_content .= 'User-agent: *' . "\n";
                $new_robots_content .= 'Allow: /' . "\n";
            } else {
                $new_robots_content .= 'User-agent: *' . "\n";
                $new_robots_content .= 'Disallow: /' . "\n";
            }

            /* Process AI scraping */
            if(!$_POST['ai_scraping_is_allowed']) {
                $new_robots_content .= "\n";
                $new_robots_content .= 'User-agent: GPTBot' . "\n";
                $new_robots_content .= 'User-agent: Google-Extended' . "\n";
                $new_robots_content .= 'Disallow: /' . "\n";
            }

            $new_robots_content .= "\n";
            $new_robots_content .= 'Sitemap: ' . SITE_URL . 'sitemap';

            file_put_contents(ROOT_PATH . 'robots.txt', $new_robots_content);

            $_POST['avatar_size_limit'] = $_POST['avatar_size_limit'] > get_max_upload() || $_POST['avatar_size_limit'] < 0 ? get_max_upload() : (float) $_POST['avatar_size_limit'];

            /* :) */
            $value = json_encode([
                'title' => $_POST['title'],
                'title_separator' => $_POST['title_separator'],
                'default_language' => $_POST['default_language'],
                'default_theme_style' => $_POST['default_theme_style'],
                'default_timezone' => $_POST['default_timezone'],
                'index_url' => $_POST['index_url'],
                'terms_and_conditions_url' => $_POST['terms_and_conditions_url'],
                'privacy_policy_url' => $_POST['privacy_policy_url'],
                'not_found_url' => $_POST['not_found_url'],
                'ai_scraping_is_allowed' => $_POST['ai_scraping_is_allowed'],
                'se_indexing' => $_POST['se_indexing'],
                'iframe_embedding' => $_POST['iframe_embedding'],
                'display_index_plans' => isset($_POST['display_index_plans']),
                'display_index_testimonials' => isset($_POST['display_index_testimonials']),
                'display_index_faq' => isset($_POST['display_index_faq']),
                'display_index_latest_blog_posts' => isset($_POST['display_index_latest_blog_posts']),
                'default_results_per_page' => (int) $_POST['default_results_per_page'],
                'default_order_type' => $_POST['default_order_type'],
                'auto_language_detection_is_enabled' => isset($_POST['auto_language_detection_is_enabled']),
                'blog_is_enabled' => isset($_POST['blog_is_enabled']),
                'api_is_enabled' => isset($_POST['api_is_enabled']),
                'theme_style_change_is_enabled' => isset($_POST['theme_style_change_is_enabled']),
                'white_labeling_is_enabled' => isset($_POST['white_labeling_is_enabled']),
                'admin_spotlight_is_enabled' => isset($_POST['admin_spotlight_is_enabled']),
                'user_spotlight_is_enabled' => isset($_POST['user_spotlight_is_enabled']),
                'logo_light' => settings()->main->logo_light ?? '',
                'logo_dark' => settings()->main->logo_dark ?? '',
                'logo_email' => settings()->main->logo_email ?? '',
                'opengraph' => settings()->main->opengraph ?? '',
                'favicon' => settings()->main->favicon ?? '',
                'openai_api_key' => $_POST['openai_api_key'],
                'openai_model' => $_POST['openai_model'],
                'force_https_is_enabled' => $_POST['force_https_is_enabled'],
                'broadcasts_statistics_is_enabled' => isset($_POST['broadcasts_statistics_is_enabled']),
                'breadcrumbs_is_enabled' => isset($_POST['breadcrumbs_is_enabled']),
                'display_pagination_when_no_pages' => isset($_POST['display_pagination_when_no_pages']),
                'chart_cache' => (int) $_POST['chart_cache'],
                'chart_days' => (int) $_POST['chart_days'],
                'avatar_size_limit' => $_POST['avatar_size_limit'],

                'maintenance_is_enabled' => isset($_POST['maintenance_is_enabled']),
                'maintenance_title' => $_POST['maintenance_title'],
                'maintenance_description' => $_POST['maintenance_description'],
                'maintenance_button_text' => $_POST['maintenance_button_text'],
                'maintenance_button_url' => $_POST['maintenance_button_url'],
            ]);

            $this->update_settings('main', $value);
        }
    }

    public function users() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['blacklisted_domains'] = array_filter(array_map('trim', explode(',', $_POST['blacklisted_domains'])));
            $_POST['blacklisted_countries'] = $_POST['blacklisted_countries'] ?? [];

            $value = json_encode([
                'email_aliases_is_enabled' => isset($_POST['email_aliases_is_enabled']),
                'email_confirmation' => isset($_POST['email_confirmation']),
                'welcome_email_is_enabled' => isset($_POST['welcome_email_is_enabled']),
                'register_is_enabled' => isset($_POST['register_is_enabled']),
                'register_only_social_logins' => isset($_POST['register_only_social_logins']),
                'register_social_login_require_password' => isset($_POST['register_social_login_require_password']),
                'register_display_newsletter_checkbox' => isset($_POST['register_display_newsletter_checkbox']),
                'account_display_newsletter_checkbox' => isset($_POST['account_display_newsletter_checkbox']),
                'login_rememberme_checkbox_is_checked' => isset($_POST['login_rememberme_checkbox_is_checked']),
                'login_rememberme_cookie_days' => (int) $_POST['login_rememberme_cookie_days'],
                'auto_delete_unconfirmed_users' => (int) $_POST['auto_delete_unconfirmed_users'],
                'auto_delete_inactive_users' => (int) $_POST['auto_delete_inactive_users'],
                'user_deletion_reminder' => (int) $_POST['user_deletion_reminder'],
                'blacklisted_domains' => $_POST['blacklisted_domains'],
                'blacklisted_countries' => $_POST['blacklisted_countries'],
                'login_lockout_is_enabled' => isset($_POST['login_lockout_is_enabled']),
                'login_lockout_max_retries' => (int) $_POST['login_lockout_max_retries'] < 1 ? 1 : (int) $_POST['login_lockout_max_retries'],
                'login_lockout_time' => (int) $_POST['login_lockout_time'] < 1 ? 1 : (int) $_POST['login_lockout_time'],
                'lost_password_lockout_is_enabled' => isset($_POST['lost_password_lockout_is_enabled']),
                'lost_password_lockout_max_retries' => (int) $_POST['lost_password_lockout_max_retries'] < 1 ? 1 : (int) $_POST['lost_password_lockout_max_retries'],
                'lost_password_lockout_time' => (int) $_POST['lost_password_lockout_time'] < 1 ? 1 : (int) $_POST['lost_password_lockout_time'],
                'resend_activation_lockout_is_enabled' => isset($_POST['resend_activation_lockout_is_enabled']),
                'resend_activation_lockout_max_retries' => (int) $_POST['resend_activation_lockout_max_retries'] < 1 ? 1 : (int) $_POST['resend_activation_lockout_max_retries'],
                'resend_activation_lockout_time' => (int) $_POST['resend_activation_lockout_time'] < 1 ? 1 : (int) $_POST['resend_activation_lockout_time'],
                'register_lockout_is_enabled' => isset($_POST['register_lockout_is_enabled']),
                'register_lockout_max_registrations' => (int) $_POST['register_lockout_max_registrations'] < 1 ? 1 : (int) $_POST['register_lockout_max_registrations'],
                'register_lockout_time' => (int) $_POST['register_lockout_time'] < 1 ? 1 : (int) $_POST['register_lockout_time'],
            ]);

            $this->update_settings('users', $value);
        }
    }

    public function content() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $value = json_encode([
                'blog_is_enabled' => isset($_POST['blog_is_enabled']),
                'blog_share_is_enabled' => isset($_POST['blog_share_is_enabled']),
                'blog_search_widget_is_enabled' => isset($_POST['blog_search_widget_is_enabled']),
                'blog_categories_widget_is_enabled' => isset($_POST['blog_categories_widget_is_enabled']),
                'blog_popular_widget_is_enabled' => isset($_POST['blog_popular_widget_is_enabled']),
                'blog_views_is_enabled' => isset($_POST['blog_views_is_enabled']),
                'blog_ratings_is_enabled' => isset($_POST['blog_ratings_is_enabled']),
                'blog_columns' => (int) max(1, min((int) $_POST['blog_columns'], 2)),

                'pages_is_enabled' => isset($_POST['pages_is_enabled']),
                'pages_share_is_enabled' => isset($_POST['pages_share_is_enabled']),
                'pages_popular_widget_is_enabled' => isset($_POST['pages_popular_widget_is_enabled']),
                'pages_views_is_enabled' => isset($_POST['pages_views_is_enabled']),
            ]);

            $this->update_settings('content', $value);
        }
    }

    public function payment() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['is_enabled'] = (int) isset($_POST['is_enabled']);
            $_POST['type'] = in_array($_POST['type'], ['one_time', 'recurring', 'both']) ? input_clean($_POST['type']) : 'both';
            $_POST['codes_is_enabled'] = (int) isset($_POST['codes_is_enabled']);
            $_POST['taxes_and_billing_is_enabled'] = (int) isset($_POST['taxes_and_billing_is_enabled']);
            $_POST['invoice_is_enabled'] = (int) isset($_POST['invoice_is_enabled']);
            $_POST['trial_require_card'] = (int) isset($_POST['trial_require_card']);
            $_POST['default_currency'] = input_clean(mb_strtoupper($_POST['default_currency']));

            $currencies = [];

            foreach($_POST['code'] as $code) {
                if(mb_strlen($code) !== 3) {
                    continue;
                }

                $currencies[$code] = [
                    'code' => mb_strtoupper($code),
                    'symbol' => $_POST['symbol'][$code],
                    'display_as' => $_POST['display_as'][$code],
                    'currency_placement' => $_POST['currency_placement'][$code],
                    'default_payment_processor' => $_POST['default_payment_processor'][$code],
                ];
            }

            if(!array_key_exists($_POST['default_currency'], $currencies)) {
                $_POST['default_currency'] = array_key_first($currencies);
            }

            $value = json_encode([
                'is_enabled' => $_POST['is_enabled'],
                'type' => $_POST['type'],
                'default_payment_type' => $_POST['default_payment_type'],
                'default_payment_frequency' => $_POST['default_payment_frequency'],
                'currencies' => $currencies,
                'default_currency' => $_POST['default_currency'],
                'codes_is_enabled' => $_POST['codes_is_enabled'],
                'taxes_and_billing_is_enabled' => $_POST['taxes_and_billing_is_enabled'],
                'invoice_is_enabled' => $_POST['invoice_is_enabled'],
                'trial_require_card' => $_POST['trial_require_card'],
                'user_plan_expiry_reminder' => (int) $_POST['user_plan_expiry_reminder'],
                'user_plan_expiry_checker_is_enabled' => isset($_POST['user_plan_expiry_checker_is_enabled']),
                'currency_exchange_api_key' => $_POST['currency_exchange_api_key'],
            ]);

            $this->update_settings('payment', $value);
        }
    }

    public function paypal() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['is_enabled'] = (int) isset($_POST['is_enabled']);
            $_POST['mode'] = in_array($_POST['mode'], ['live', 'sandbox']) ? input_clean($_POST['mode']) : 'live';

            $value = json_encode([
                'is_enabled' => $_POST['is_enabled'],
                'mode' => $_POST['mode'],
                'client_id' => $_POST['client_id'],
                'secret' => $_POST['secret'],
                'currencies' => $_POST['currencies'] ?? [],
            ]);

            $this->update_settings('paypal', $value);
        }
    }

    public function stripe() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['is_enabled'] = (int) isset($_POST['is_enabled']);

            $value = json_encode([
                'is_enabled' => $_POST['is_enabled'],
                'publishable_key' => $_POST['publishable_key'],
                'secret_key' => $_POST['secret_key'],
                'webhook_secret' => $_POST['webhook_secret'],
                'currencies' => $_POST['currencies'] ?? [],
            ]);

            $this->update_settings('stripe', $value);
        }
    }

    public function offline_payment() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['is_enabled'] = (int) isset($_POST['is_enabled']);
            $_POST['proof_size_limit'] = $_POST['proof_size_limit'] > get_max_upload() || $_POST['proof_size_limit'] < 0 ? get_max_upload() : (float) $_POST['proof_size_limit'];

            $value = json_encode([
                'is_enabled' => $_POST['is_enabled'],
                'instructions' => $_POST['instructions'],
                'proof_size_limit' => $_POST['proof_size_limit'],
                'currencies' => $_POST['currencies'] ?? [],
            ]);

            $this->update_settings('offline_payment', $value);
        }
    }

    public function coinbase() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['is_enabled'] = (int) isset($_POST['is_enabled']);

            $value = json_encode([
                'is_enabled' => $_POST['is_enabled'],
                'api_key' => $_POST['api_key'],
                'webhook_secret' => $_POST['webhook_secret'],
                'currencies' => $_POST['currencies'] ?? [],
            ]);

            $this->update_settings('coinbase', $value);
        }
    }

    public function payu() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['is_enabled'] = (int) isset($_POST['is_enabled']);
            $_POST['mode'] = in_array($_POST['mode'], ['secure', 'sandbox']) ? input_clean($_POST['mode']) : 'secure';

            $value = json_encode([
                'is_enabled' => $_POST['is_enabled'],
                'mode' => $_POST['mode'],
                'merchant_pos_id' => $_POST['merchant_pos_id'],
                'signature_key' => $_POST['signature_key'],
                'oauth_client_id' => $_POST['oauth_client_id'],
                'oauth_client_secret' => $_POST['oauth_client_secret'],
                'currencies' => $_POST['currencies'] ?? [],
            ]);

            $this->update_settings('payu', $value);
        }
    }

    public function iyzico() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['is_enabled'] = (int) isset($_POST['is_enabled']);
            $_POST['mode'] = in_array($_POST['mode'], ['live', 'sandbox']) ? input_clean($_POST['mode']) : 'live';

            $value = json_encode([
                'is_enabled' => $_POST['is_enabled'],
                'mode' => $_POST['mode'],
                'api_key' => $_POST['api_key'],
                'secret_key' => $_POST['secret_key'],
                'currencies' => $_POST['currencies'] ?? [],
            ]);

            $this->update_settings('iyzico', $value);
        }
    }

    public function paystack() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['is_enabled'] = (int) isset($_POST['is_enabled']);

            $value = json_encode([
                'is_enabled' => $_POST['is_enabled'],
                'public_key' => $_POST['public_key'],
                'secret_key' => $_POST['secret_key'],
                'currencies' => $_POST['currencies'] ?? [],
            ]);

            $this->update_settings('paystack', $value);
        }
    }

    public function razorpay() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['is_enabled'] = (int) isset($_POST['is_enabled']);

            $value = json_encode([
                'is_enabled' => $_POST['is_enabled'],
                'key_id' => $_POST['key_id'],
                'key_secret' => $_POST['key_secret'],
                'webhook_secret' => $_POST['webhook_secret'],
                'currencies' => $_POST['currencies'] ?? [],
            ]);

            $this->update_settings('razorpay', $value);
        }
    }

    public function mollie() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['is_enabled'] = (int) isset($_POST['is_enabled']);

            $value = json_encode([
                'is_enabled' => $_POST['is_enabled'],
                'api_key' => $_POST['api_key'],
                'currencies' => $_POST['currencies'] ?? [],
            ]);

            $this->update_settings('mollie', $value);
        }
    }

    public function yookassa() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['is_enabled'] = (int) isset($_POST['is_enabled']);

            $value = json_encode([
                'is_enabled' => $_POST['is_enabled'],
                'shop_id' => $_POST['shop_id'],
                'secret_key' => $_POST['secret_key'],
                'currencies' => $_POST['currencies'] ?? [],
            ]);

            $this->update_settings('yookassa', $value);
        }
    }

    public function crypto_com() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['is_enabled'] = (int) isset($_POST['is_enabled']);

            $value = json_encode([
                'is_enabled' => $_POST['is_enabled'],
                'publishable_key' => $_POST['publishable_key'],
                'secret_key' => $_POST['secret_key'],
                'webhook_secret' => $_POST['webhook_secret'],
                'currencies' => $_POST['currencies'] ?? [],
            ]);

            $this->update_settings('crypto_com', $value);
        }
    }

    public function paddle() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['is_enabled'] = (int) isset($_POST['is_enabled']);
            $_POST['mode'] = in_array($_POST['mode'], ['live', 'sandbox']) ? input_clean($_POST['mode']) : 'live';

            $value = json_encode([
                'is_enabled' => $_POST['is_enabled'],
                'mode' => $_POST['mode'],
                'vendor_id' => $_POST['vendor_id'],
                'api_key' => $_POST['api_key'],
                'public_key' => $_POST['public_key'],
                'currencies' => $_POST['currencies'] ?? [],
            ]);

            $this->update_settings('paddle', $value);
        }
    }

    public function mercadopago() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['is_enabled'] = (int) isset($_POST['is_enabled']);

            $value = json_encode([
                'is_enabled' => $_POST['is_enabled'],
                'access_token' => $_POST['access_token'],
                'currencies' => $_POST['currencies'] ?? [],
            ]);

            $this->update_settings('mercadopago', $value);
        }
    }

    public function midtrans() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['is_enabled'] = (int) isset($_POST['is_enabled']);

            $value = json_encode([
                'is_enabled' => $_POST['is_enabled'],
                'server_key' => $_POST['server_key'],
                'mode' => $_POST['mode'],
                'currencies' => $_POST['currencies'] ?? [],
            ]);

            $this->update_settings('midtrans', $value);
        }
    }

    public function flutterwave() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['is_enabled'] = (int) isset($_POST['is_enabled']);

            $value = json_encode([
                'is_enabled' => $_POST['is_enabled'],
                'secret_key' => $_POST['secret_key'],
                'currencies' => $_POST['currencies'] ?? [],
            ]);

            $this->update_settings('flutterwave', $value);
        }
    }

    public function lemonsqueezy() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['is_enabled'] = (int) isset($_POST['is_enabled']);

            $value = json_encode([
                'is_enabled' => $_POST['is_enabled'],
                'api_key' => $_POST['api_key'],
                'signing_secret' => $_POST['signing_secret'],
                'store_id' => $_POST['store_id'],
                'one_time_monthly_variant_id' => $_POST['one_time_monthly_variant_id'],
                'one_time_quarterly_variant_id' => $_POST['one_time_quarterly_variant_id'],
                'one_time_biannual_variant_id' => $_POST['one_time_biannual_variant_id'],
                'one_time_annual_variant_id' => $_POST['one_time_annual_variant_id'],
                'one_time_lifetime_variant_id' => $_POST['one_time_lifetime_variant_id'],
                'recurring_monthly_variant_id' => $_POST['recurring_monthly_variant_id'],
                'recurring_annual_variant_id' => $_POST['recurring_annual_variant_id'],
                'currencies' => $_POST['currencies'] ?? [],
            ]);

            $this->update_settings('lemonsqueezy', $value);
        }
    }

    public function myfatoorah() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['is_enabled'] = (int) isset($_POST['is_enabled']);
            $_POST['api_endpoint'] = in_array($_POST['api_endpoint'], ['api.myfatoorah.com', 'api-sa.myfatoorah.com', 'api-qa.myfatoorah.com', 'api-eg.myfatoorah.com', 'apitest.myfatoorah.com',]) ? input_clean($_POST['api_endpoint']) : 'api.myfatoorah.com';

            $value = json_encode([
                'is_enabled' => $_POST['is_enabled'],
                'api_endpoint' => $_POST['api_endpoint'],
                'api_key' => $_POST['api_key'],
                'secret_key' => $_POST['secret_key'],
                'currencies' => $_POST['currencies'] ?? [],
            ]);

            $this->update_settings('myfatoorah', $value);
        }
    }

    public function affiliate() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            if(!\Altum\Plugin::is_active('affiliate')) {
                redirect('admin/settings/affiliate');
            }

            /* :) */
            $_POST['is_enabled'] = (int) isset($_POST['is_enabled']);
            $_POST['commission_type'] = in_array($_POST['commission_type'], ['once', 'forever']) ? input_clean($_POST['commission_type']) : 'once';
            $_POST['tracking_type'] = in_array($_POST['tracking_type'], ['first', 'last']) ? input_clean($_POST['tracking_type']) : 'first';
            $_POST['tracking_duration'] = (int) $_POST['tracking_duration'] >= 1 ? (int) $_POST['tracking_duration'] : 30;
            $_POST['minimum_withdrawal_amount'] = (float) $_POST['minimum_withdrawal_amount'];

            /* Translations */
            foreach($_POST['translations'] as $language_name => $array) {
                if(!array_key_exists($language_name, \Altum\Language::$active_languages)) {
                    unset($_POST['translations'][$language_name]);
                }
            }

            $value = json_encode([
                'is_enabled' => $_POST['is_enabled'],
                'commission_type' => $_POST['commission_type'],
                'tracking_type' => $_POST['tracking_type'],
                'tracking_duration' => $_POST['tracking_duration'],
                'minimum_withdrawal_amount' => $_POST['minimum_withdrawal_amount'],
                'withdrawal_notes' => $_POST['withdrawal_notes'],

                'translations' => $_POST['translations'],
            ]);

            $this->update_settings('affiliate', $value);
        }
    }

    public function business() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['brand_name'] = input_clean($_POST['brand_name']);

            $value = json_encode([
                'brand_name' => $_POST['brand_name'],
                'invoice_nr_prefix' => $_POST['invoice_nr_prefix'],
                'name' => $_POST['name'],
                'address' => $_POST['address'],
                'city' => $_POST['city'],
                'county' => $_POST['county'],
                'zip' => $_POST['zip'],
                'country' => $_POST['country'],
                'email' => $_POST['email'],
                'phone' => $_POST['phone'],
                'tax_type' => $_POST['tax_type'],
                'tax_id' => $_POST['tax_id'],
                'custom_key_one' => $_POST['custom_key_one'],
                'custom_value_one' => $_POST['custom_value_one'],
                'custom_key_two' => $_POST['custom_key_two'],
                'custom_value_two' => $_POST['custom_value_two'],
            ]);

            $this->update_settings('business', $value);
        }
    }

    public function captcha() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['type'] = in_array($_POST['type'], ['basic', 'recaptcha', 'hcaptcha', 'turnstile']) ? $_POST['type'] : 'basic';
            foreach(['login', 'register', 'lost_password', 'resend_activation', 'contact'] as $key) {
                $_POST[$key . '_is_enabled'] = isset($_POST[$key . '_is_enabled']);
            }

            /* Check for errors */
            if($_POST['type'] == 'basic') {
                if(!extension_loaded('gd') || !function_exists('gd_info')) {
                    Alerts::add_error(sprintf(l('global.error_message.function_required'), 'GD'));
                }
            }

            $value = json_encode([
                'type' => $_POST['type'],
                'recaptcha_public_key' => $_POST['recaptcha_public_key'],
                'recaptcha_private_key' => $_POST['recaptcha_private_key'],
                'hcaptcha_site_key' => $_POST['hcaptcha_site_key'],
                'hcaptcha_secret_key' => $_POST['hcaptcha_secret_key'],
                'turnstile_site_key' => $_POST['turnstile_site_key'],
                'turnstile_secret_key' => $_POST['turnstile_secret_key'],
                'login_is_enabled' => $_POST['login_is_enabled'],
                'register_is_enabled' => $_POST['register_is_enabled'],
                'lost_password_is_enabled' => $_POST['lost_password_is_enabled'],
                'resend_activation_is_enabled' => $_POST['resend_activation_is_enabled'],
                'contact_is_enabled' => $_POST['contact_is_enabled'],
            ]);

            $this->update_settings('captcha', $value);
        }
    }

    public function facebook() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['is_enabled'] = (int) isset($_POST['is_enabled']);

            $value = json_encode([
                'is_enabled' => $_POST['is_enabled'],
                'app_id' => $_POST['app_id'],
                'app_secret' => $_POST['app_secret'],
            ]);

            $this->update_settings('facebook', $value);
        }
    }

    public function google() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['is_enabled'] = (int) isset($_POST['is_enabled']);

            $value = json_encode([
                'is_enabled' => $_POST['is_enabled'],
                'client_id' => $_POST['client_id'],
                'client_secret' => $_POST['client_secret'],
            ]);

            $this->update_settings('google', $value);
        }
    }

    public function twitter() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['is_enabled'] = (int) isset($_POST['is_enabled']);

            $value = json_encode([
                'is_enabled' => $_POST['is_enabled'],
                'consumer_api_key' => $_POST['consumer_api_key'],
                'consumer_api_secret' => $_POST['consumer_api_secret'],
            ]);

            $this->update_settings('twitter', $value);
        }
    }

    public function discord() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['is_enabled'] = (int) isset($_POST['is_enabled']);

            $value = json_encode([
                'is_enabled' => $_POST['is_enabled'],
                'client_id' => $_POST['client_id'],
                'client_secret' => $_POST['client_secret'],
            ]);

            $this->update_settings('discord', $value);
        }
    }

    public function linkedin() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['is_enabled'] = (int) isset($_POST['is_enabled']);

            $value = json_encode([
                'is_enabled' => $_POST['is_enabled'],
                'client_id' => $_POST['client_id'],
                'client_secret' => $_POST['client_secret'],
            ]);

            $this->update_settings('linkedin', $value);
        }
    }

    public function microsoft() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['is_enabled'] = (int) isset($_POST['is_enabled']);

            $value = json_encode([
                'is_enabled' => $_POST['is_enabled'],
                'client_id' => $_POST['client_id'],
                'client_secret' => $_POST['client_secret'],
            ]);

            $this->update_settings('microsoft', $value);
        }
    }

    public function ads() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $value = json_encode([
                'ad_blocker_detector_is_enabled' => isset($_POST['ad_blocker_detector_is_enabled']),
                'ad_blocker_detector_lock_is_enabled' => isset($_POST['ad_blocker_detector_lock_is_enabled']),
                'ad_blocker_detector_delay' => (int) $_POST['ad_blocker_detector_delay'],
                'header' => $_POST['header'],
                'footer' => $_POST['footer'],
            ]);

            $this->update_settings('ads', $value);
        }
    }

    public function cookie_consent() {
        $this->process();

        /* CSV Export */
        if(isset($_GET['export']) && $_GET['export'] == 'csv') {
            //ALTUMCODE:DEMO if(DEMO) exit('This command is blocked on the demo.');

            header('Content-Disposition: attachment; filename="data.csv";');
            header('Content-Type: application/csv; charset=UTF-8');

            die(file_get_contents(UPLOADS_PATH . 'cookie_consent/data.csv'));
        }

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['is_enabled'] = (int) isset($_POST['is_enabled']);
            $_POST['logging_is_enabled'] = (int) isset($_POST['logging_is_enabled']);
            $_POST['necessary_is_enabled'] = true;
            $_POST['analytics_is_enabled'] = (int) isset($_POST['analytics_is_enabled']);
            $_POST['targeting_is_enabled'] = (int) isset($_POST['targeting_is_enabled']);
            $_POST['layout'] = in_array($_POST['layout'], ['cloud', 'box', 'bar']) ? $_POST['layout'] : 'cloud';
            $_POST['position_y'] = in_array($_POST['position_y'], ['top', 'middle', 'bottom']) ? $_POST['position_y'] : 'bottom';
            $_POST['position_x'] = in_array($_POST['position_x'], ['left', 'center', 'right']) ? $_POST['position_x'] : 'center';

            if($_POST['logging_is_enabled']) {
                if(!is_writable(UPLOADS_PATH . 'cookie_consent/')) {
                    Alerts::add_error(sprintf(l('global.error_message.directory_not_writable'), UPLOADS_PATH . 'cookie_consent/'));
                }
            }

            $value = json_encode([
                'is_enabled' => $_POST['is_enabled'],
                'logging_is_enabled' => $_POST['logging_is_enabled'],
                'necessary_is_enabled' => $_POST['necessary_is_enabled'],
                'analytics_is_enabled' => $_POST['analytics_is_enabled'],
                'targeting_is_enabled' => $_POST['targeting_is_enabled'],
                'layout' => $_POST['layout'],
                'position_y' => $_POST['position_y'],
                'position_x' => $_POST['position_x'],
            ]);

            $this->update_settings('cookie_consent', $value);
        }
    }

    public function socials() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $value = [];
            foreach(require APP_PATH . 'includes/admin_socials.php' as $key => $social) {
                $value[$key] = $_POST[$key];
            }

            /* Share buttons */
            $value['share_buttons'] = [];
            $social_share_keys = [
                'facebook',
                'threads',
                'x',
                'pinterest',
                'linkedin',
                'reddit',
                'whatsapp',
                'telegram',
                'snapchat',
                'microsoft_teams',
                'email',
                'copy',
                'share',
                'print'
            ];
            foreach($social_share_keys as $key) {
                $value['share_buttons'][$key] = isset($_POST['share_button_' . $key]);
            }

            $value = json_encode($value);

            $this->update_settings('socials', $value);
        }
    }

    public function smtp() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['auth'] = (int) isset($_POST['auth']);
            $_POST['username'] = input_clean($_POST['username'] ?? '');
            $_POST['password'] = $_POST['password'] ?? '';
            $_POST['cc'] = str_replace(' ', '', $_POST['cc']);
            $_POST['bcc'] = str_replace(' ', '', $_POST['bcc']);
            $_POST['button_background_color'] = !verify_hex_color($_POST['button_background_color']) ? '#000000' : $_POST['button_background_color'];
            $_POST['button_text_color'] = !verify_hex_color($_POST['button_text_color']) ? '#000000' : $_POST['button_text_color'];
            $_POST['button_border_radius'] = (int) $_POST['button_border_radius'];
            $_POST['main_container_border_radius'] = (int) $_POST['main_container_border_radius'];

            $value = json_encode([
                'from_name' => $_POST['from_name'],
                'from' => $_POST['from'],

                'reply_to_name' => $_POST['reply_to_name'],
                'reply_to' => $_POST['reply_to'],

                'cc' => $_POST['cc'],
                'bcc' => $_POST['bcc'],

                'host' => $_POST['host'],
                'encryption' => $_POST['encryption'],
                'port' => $_POST['port'],
                'auth' => $_POST['auth'],
                'username' => $_POST['username'],
                'password' => $_POST['password'],
                'display_socials' => isset($_POST['display_socials']),
                'company_details' => $_POST['company_details'],

                'button_background_color' => $_POST['button_background_color'],
                'button_text_color' => $_POST['button_text_color'],
                'button_border_radius' => $_POST['button_border_radius'],
                'main_container_border_radius' => $_POST['main_container_border_radius'],
            ]);

            $this->update_settings('smtp', $value);
        }
    }

    public function theme() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            if(!is_writable(ASSETS_PATH . 'css/custom-bootstrap/')) {
                Alerts::add_error(sprintf(l('global.error_message.directory_not_writable'), ASSETS_PATH . 'css/custom-bootstrap/'));
                redirect('admin/settings/' . \Altum\Router::$method);
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {
                $admin_primary_themes = require APP_PATH . 'includes/admin_primary_themes.php';
                $admin_gray_themes = require APP_PATH . 'includes/admin_gray_themes.php';

                /* Process */
                $theme = [];

                /* Go through all the inputs and clean them */
                foreach(['light', 'dark'] as $mode) {

                    /* Presets for primary / gray */
                    $_POST[$mode . '_primary_theme'] = isset($_POST[$mode . '_primary_theme']) && array_key_exists($_POST[$mode . '_primary_theme'], $admin_primary_themes[$mode]) ? $_POST[$mode . '_primary_theme'] : 'custom';
                    $_POST[$mode . '_gray_theme'] = isset($_POST[$mode . '_gray_theme']) && array_key_exists($_POST[$mode . '_gray_theme'], $admin_gray_themes[$mode]) ? $_POST[$mode . '_gray_theme'] : 'custom';

                    $theme[$mode . '_primary_theme'] = $_POST[$mode . '_primary_theme'];
                    $theme[$mode . '_gray_theme'] = $_POST[$mode . '_gray_theme'];


                    foreach(['50', '100', '200', '300', '400', '500', '600', '700', '800', '900'] as $key) {
                        if(isset($_POST[$mode . '_primary_' . $key])) {
                            $_POST[$mode . '_primary_' . $key] = !verify_hex_color($_POST[$mode . '_primary_' . $key]) ? '#000000' : $_POST[$mode . '_primary_' . $key];
                            $theme[$mode . '_primary_' . $key] = $_POST[$mode . '_primary_' . $key];
                        }

                        if(isset($_POST[$mode . '_gray_' . $key])) {
                            $_POST[$mode . '_gray_' . $key] = !verify_hex_color($_POST[$mode . '_gray_' . $key]) ? '#000000' : $_POST[$mode . '_gray_' . $key];
                            $theme[$mode . '_gray_' . $key] = $_POST[$mode . '_gray_' . $key];
                        }
                    }

                    /* Others */
                    $_POST[$mode . '_border_radius'] = isset($_POST[$mode . '_border_radius']) && $_POST[$mode . '_border_radius'] >= 0 && $_POST[$mode . '_border_radius'] <= 1 ? (float) $_POST[$mode . '_border_radius'] : 0.3;
                    $theme[$mode . '_border_radius'] = $_POST[$mode . '_border_radius'];

                    /* Font family */
                    $theme[$mode . '_font_family'] = match($_POST[$mode . '_font_family']) {
                        'default' => '',
                        'custom' => $_POST[$mode . '_font_family_custom'],
                        default => $_POST[$mode . '_font_family'],
                    };
                }

                $css_files = [
                    'bootstrap' => 'light',
                    'bootstrap-rtl' => 'light',
                    'bootstrap-dark' => 'dark',
                    'bootstrap-dark-rtl' => 'dark',
                ];

                foreach($css_files as $key => $value) {
                    $theme[$value . '_is_enabled'] = isset($_POST[$value . '_is_enabled']);

                    if(!$theme[$value . '_is_enabled']) {
                        continue;
                    }

                    /* Initiate SCSS - PHP compiler */
                    $compiler = new \ScssPhp\ScssPhp\Compiler;
                    $compiler->setOutputStyle(\ScssPhp\ScssPhp\OutputStyle::COMPRESSED);
                    $compiler->setImportPaths(ASSETS_PATH . 'scss/');

                    /* Get the current SCSS file content */
                    $main_scss_content = file_get_contents(ASSETS_PATH . 'scss/' . $key . '.scss');

                    /* Font family */
                    $font_family = $theme[$value . '_font_family'] ? '$font-family-base: ' . $theme[$value . '_font_family'] . ';' : null;

                    /* Replace the SCSS file content with the custom colors */
                    $main_scss_content = '
                    $primary-50: ' . $_POST[$value . '_primary_50'] . ';
                    $primary-100: ' . $_POST[$value . '_primary_100'] . ';
                    $primary-200: ' . $_POST[$value . '_primary_200'] . ';
                    $primary-300: ' . $_POST[$value . '_primary_300'] . ';
                    $primary-400: ' . $_POST[$value . '_primary_400'] . ';
                    $primary: ' . $_POST[$value . '_primary_500'] . ';
                    $primary-600: ' . $_POST[$value . '_primary_600'] . ';
                    $primary-700: ' . $_POST[$value . '_primary_700'] . ';
                    $primary-800: ' . $_POST[$value . '_primary_800'] . ';
                    $primary-900: ' . $_POST[$value . '_primary_900'] . ';
                    
                    $gray-50: ' . $_POST[$value . '_gray_50'] . ' !default;
                    $gray-100: ' . $_POST[$value . '_gray_100'] . ' !default;
                    $gray-200: ' . $_POST[$value . '_gray_200'] . ' !default;
                    $gray-300: ' . $_POST[$value . '_gray_300'] . ' !default;
                    $gray-400: ' . $_POST[$value . '_gray_400'] . ' !default;
                    $gray-500: ' . $_POST[$value . '_gray_500'] . ' !default;
                    $gray-600: ' . $_POST[$value . '_gray_600'] . ' !default;
                    $gray-700: ' . $_POST[$value . '_gray_700'] . ' !default;
                    $gray-800: ' . $_POST[$value . '_gray_800'] . ' !default;
                    $gray-900: ' . $_POST[$value . '_gray_900'] . ' !default;
                    
                    ' . $font_family . '
                    ' . strstr($main_scss_content, '/* :) */');

                    $main_scss_content = preg_replace('/border-radius: (.*)rem;/', 'border-radius: ' . $theme[$value . '_border_radius'] . 'rem;', $main_scss_content);

                    /* Compile to CSS */
                    $compiled_css = $compiler->compileString($main_scss_content)->getCss();

                    /* Save the custom CSS file */
                    file_put_contents(ASSETS_PATH . 'css/custom-bootstrap/' . $key . '.min.css', $compiled_css);

                    /* Offload uploading */
                    if(\Altum\Plugin::is_active('offload') && settings()->offload->uploads_url) {
                        try {
                            $s3 = new \Aws\S3\S3Client(get_aws_s3_config());

                            /* Upload image */
                            $result = $s3->putObject([
                                'Bucket' => settings()->offload->storage_name,
                                'Key' => 'assets/css/custom-bootstrap/' . $key . '.min.css',
                                'ContentType' => 'text/css',
                                'SourceFile' => ASSETS_PATH . 'css/custom-bootstrap/' . $key . '.min.css',
                                'ACL' => 'public-read'
                            ]);
                        } catch (\Exception $exception) {
                            Alerts::add_error($exception->getMessage());
                        }
                    }
                }

                /* :) */
                $value = json_encode($theme);

                $this->update_settings('theme', $value);
            }
        }
    }

    public function custom() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $value = json_encode([
                'body_content' => $_POST['body_content'],
                'head_js' => $_POST['head_js'],
                'welcome_js' => $_POST['welcome_js'],
                'pay_thank_you_js' => $_POST['pay_thank_you_js'],
                'head_css' => $_POST['head_css'],
            ]);

            $this->update_settings('custom', $value);
        }
    }

    public function custom_images() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            $images_keys = [
                'index/hero.png',
                'index/dynamic.png',
                'index/privacy.png',
                'index/static.png',
            ];

            if(is_null(settings()->custom_images)) {
                settings()->custom_images = (object) [];
            }

            /* Uploads processing */
            foreach($images_keys as $image_key) {
                $image_key_id = str_replace('.', '_', get_slug($image_key));
                settings()->custom_images->{$image_key_id} = \Altum\Uploads::process_upload(settings()->custom_images->{$image_key_id} ?? null, 'custom_images', $image_key_id, $image_key_id . '_remove', null);
            }

            /* :) */
            $value = json_encode(settings()->custom_images);

            $this->update_settings('custom_images', $value);
        }
    }

    public function announcements() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['guests_is_enabled'] = (int) isset($_POST['guests_is_enabled']);
            $_POST['guests_id'] = md5($_POST['content'] . time());
            $_POST['guests_text_color'] = !verify_hex_color($_POST['guests_text_color']) ? '#000000' : $_POST['guests_text_color'];
            $_POST['guests_background_color'] = !verify_hex_color($_POST['guests_background_color']) ? '#ffffff' : $_POST['guests_background_color'];
            $_POST['users_is_enabled'] = (int) isset($_POST['users_is_enabled']);
            $_POST['users_id'] = md5($_POST['content'] . time());
            $_POST['users_text_color'] = !verify_hex_color($_POST['users_text_color']) ? '#000000' : $_POST['users_text_color'];
            $_POST['users_background_color'] = !verify_hex_color($_POST['users_background_color']) ? '#ffffff' : $_POST['users_background_color'];

            /* Translations */
            foreach($_POST['translations'] as $language_name => $array) {
                if(!array_key_exists($language_name, \Altum\Language::$active_languages)) {
                    unset($_POST['translations'][$language_name]);
                }
            }

            //$_POST['translations'][\Altum\Language::$default_name]['guests_content'] = $_POST['guests_content'];
            //$_POST['translations'][\Altum\Language::$default_name]['users_content'] = $_POST['users_content'];

            $value = json_encode([
                'guests_is_enabled' => $_POST['guests_is_enabled'],
                'guests_id' => $_POST['guests_id'],
                'guests_content' => $_POST['guests_content'],
                'guests_text_color' => $_POST['guests_text_color'],
                'guests_background_color' => $_POST['guests_background_color'],
                'users_is_enabled' => $_POST['users_is_enabled'],
                'users_id' => $_POST['users_id'],
                'users_content' => $_POST['users_content'],
                'users_text_color' => $_POST['users_text_color'],
                'users_background_color' => $_POST['users_background_color'],

                'translations' => $_POST['translations'],
            ]);

            $this->update_settings('announcements', $value);
        }
    }

    public function internal_notifications() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['users_is_enabled'] = (int) isset($_POST['users_is_enabled']);
            $_POST['admins_is_enabled'] = (int) isset($_POST['admins_is_enabled']);
            $_POST['new_user'] = (int) isset($_POST['new_user']);
            $_POST['delete_user'] = (int) isset($_POST['delete_user']);
            $_POST['new_newsletter_subscriber'] = (int) isset($_POST['new_newsletter_subscriber']);
            $_POST['new_payment'] = (int) isset($_POST['new_payment']);
            $_POST['new_affiliate_withdrawal'] = (int) isset($_POST['new_affiliate_withdrawal']);

            $value = json_encode([
                'users_is_enabled' => $_POST['users_is_enabled'],
                'admins_is_enabled' => $_POST['admins_is_enabled'],
                'new_user' => $_POST['new_user'],
                'delete_user' => $_POST['delete_user'],
                'new_newsletter_subscriber' => $_POST['new_newsletter_subscriber'],
                'new_payment' => $_POST['new_payment'],
                'new_affiliate_withdrawal' => $_POST['new_affiliate_withdrawal'],
            ]);

            $this->update_settings('internal_notifications', $value);
        }
    }

    public function email_notifications() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['emails'] = str_replace(' ', '', $_POST['emails']);
            $_POST['new_user'] = (int) isset($_POST['new_user']);
            $_POST['delete_user'] = (int) isset($_POST['delete_user']);
            $_POST['new_payment'] = (int) isset($_POST['new_payment']);
            $_POST['new_domain'] = (int) isset($_POST['new_domain']);
            $_POST['contact'] = (int) isset($_POST['contact']);
            $_POST['new_affiliate_withdrawal'] = (int) isset($_POST['new_affiliate_withdrawal']);

            $value = json_encode([
                'emails' => $_POST['emails'],
                'new_user' => $_POST['new_user'],
                'delete_user' => $_POST['delete_user'],
                'new_payment' => $_POST['new_payment'],
                'new_domain' => $_POST['new_domain'],
                'contact' => $_POST['contact'],
                'new_affiliate_withdrawal' => $_POST['new_affiliate_withdrawal'],
            ]);

            $this->update_settings('email_notifications', $value);
        }
    }

    public function push_notifications() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            if(!\Altum\Plugin::is_active('push-notifications')) {
                redirect('admin/settings/push_notifications');
            }

            /* Uploads processing */
            settings()->push_notifications->icon = \Altum\Uploads::process_upload(settings()->push_notifications->icon, 'push_notifications_icon', 'icon', 'icon' . '_remove', null);

            $value = json_encode([
                'is_enabled' => isset($_POST['is_enabled']),
                'guests_is_enabled' => isset($_POST['guests_is_enabled']),
                'ask_to_subscribe_is_enabled' => isset($_POST['ask_to_subscribe_is_enabled']),
                'ask_to_subscribe_delay' => (int) $_POST['ask_to_subscribe_delay'],
                'ask_to_subscribe_delay_minimum_pageviews_count' => (int) $_POST['ask_to_subscribe_delay_minimum_pageviews_count'],
                'icon' => settings()->push_notifications->icon ?? '',
                'public_key' => settings()->push_notifications->public_key,
                'private_key' => settings()->push_notifications->private_key,
                'notifications_per_cron' => (int) $_POST['notifications_per_cron'],
                'notifications_per_cron_batch' => (int) $_POST['notifications_per_cron_batch'],
                'notifications_per_cron_batch_concurrently' => (int) $_POST['notifications_per_cron_batch_concurrently'],
            ]);

            $this->update_settings('push_notifications', $value);
        }
    }

    public function webhooks() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['wait_for_response_domains'] = array_filter(array_map('trim', explode(',', $_POST['wait_for_response_domains'])));
            $_POST['user_new'] = input_clean($_POST['user_new']);
            $_POST['user_update'] = input_clean($_POST['user_update']);
            $_POST['user_delete'] = input_clean($_POST['user_delete']);
            $_POST['payment_new'] = input_clean($_POST['payment_new']);
            $_POST['code_redeemed'] = input_clean($_POST['code_redeemed']);
            $_POST['contact'] = input_clean($_POST['contact']);
            $_POST['cron_start'] = input_clean($_POST['cron_start']);
            $_POST['cron_end'] = input_clean($_POST['cron_end']);
            $_POST['domain_new'] = input_clean($_POST['domain_new']);
            $_POST['domain_update'] = input_clean($_POST['domain_update']);

            $value = json_encode([
                'wait_for_response_domains' => $_POST['wait_for_response_domains'],
                'user_new' => $_POST['user_new'],
                'user_update' => $_POST['user_update'],
                'user_delete' => $_POST['user_delete'],
                'payment_new' => $_POST['payment_new'],
                'code_redeemed' => $_POST['code_redeemed'],
                'contact' => $_POST['contact'],
                'cron_start' => $_POST['cron_start'],
                'cron_end' => $_POST['cron_end'],
                'domain_new' => $_POST['domain_new'],
                'domain_update' => $_POST['domain_update'],
            ]);

            $this->update_settings('webhooks', $value);
        }
    }

    public function offload() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            if(!\Altum\Plugin::is_active('offload')) {
                redirect('admin/settings/offload');
            }

            /* :) */
            $value = json_encode([
                'cdn_uploads_url' => !empty($_POST['cdn_uploads_url']) ? rtrim($_POST['cdn_uploads_url'], '/') . '/' : '',
                'cdn_assets_url' => !empty($_POST['cdn_assets_url']) ? rtrim($_POST['cdn_assets_url'], '/') . '/' : '',
                'provider' => $_POST['provider'],
                'assets_url' => !empty($_POST['assets_url']) ? rtrim($_POST['assets_url'], '/') . '/' : '',
                'uploads_url' => !empty($_POST['uploads_url']) ? rtrim($_POST['uploads_url'], '/') . '/' : '',
                'endpoint_url' => $_POST['endpoint_url'],
                'bucket_endpoint' => (int) isset($_POST['bucket_endpoint']),
                'access_key' => $_POST['access_key'],
                'secret_access_key' => $_POST['secret_access_key'],
                'storage_name' => $_POST['storage_name'],
                'region' => $_POST['region'],
            ]);

            $this->update_settings('offload', $value);
        }
    }

    public function pwa() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            if(!\Altum\Plugin::is_active('pwa')) {
                redirect('admin/settings/pwa');
            }

            if(!is_writable(UPLOADS_PATH . \Altum\Uploads::get_path('pwa'))) {
                Alerts::add_error(sprintf(l('global.error_message.directory_not_writable'), UPLOADS_PATH . \Altum\Uploads::get_path('pwa')));
            }

            /* :) */
            $_POST['app_name'] = input_clean($_POST['app_name']);
            $_POST['short_app_name'] = input_clean($_POST['short_app_name']);
            $_POST['app_description'] = input_clean($_POST['app_description']);
            $_POST['theme_color'] = !verify_hex_color($_POST['theme_color']) ? '#ffffff' : $_POST['theme_color'];
            $_POST['app_start_url'] = get_url($_POST['app_start_url']);
            if(empty($_POST['app_start_url']) || !string_starts_with(SITE_URL, $_POST['app_start_url'])) {
                $_POST['app_start_url'] = SITE_URL;
            }

            $parsed_url = parse_url($_POST['app_start_url']);
            parse_str($parsed_url['query'] ?? '', $query);

            if(
                empty($query['utm_source']) &&
                empty($query['utm_medium']) &&
                empty($query['utm_campaign'])
            ) {
                $query['utm_source'] = 'pwa';
                $query['utm_medium'] = 'web-app';
                $query['utm_campaign'] = 'install-or-pwa-launch';

                $base = $parsed_url['scheme'] . '://' . $parsed_url['host'] . ($parsed_url['path'] ?? '');
                $_POST['app_start_url'] = $base . '?' . http_build_query($query);
            }

            /* App icons */
            settings()->pwa->app_icon = \Altum\Uploads::process_upload(settings()->pwa->app_icon, 'app_icon', 'app_icon', 'app_icon_remove', null);
            settings()->pwa->app_icon_maskable = \Altum\Uploads::process_upload(settings()->pwa->app_icon_maskable, 'app_icon', 'app_icon_maskable', 'app_icon_maskable_remove', null);

            $value = [
                'is_enabled' => isset($_POST['is_enabled']),
                'display_install_bar' => isset($_POST['display_install_bar']),
                'display_install_bar_for_guests' => isset($_POST['display_install_bar_for_guests']),
                'display_install_bar_delay' => (int) $_POST['display_install_bar_delay'],
                'display_install_bar_minimum_pageviews_count' => (int) $_POST['display_install_bar_minimum_pageviews_count'],
                'app_name' => $_POST['app_name'],
                'short_app_name' => $_POST['short_app_name'],
                'app_description' => $_POST['app_description'],
                'background_color' => $_POST['background_color'],
                'theme_color' => $_POST['theme_color'],
                'app_start_url' => $_POST['app_start_url'],
                'app_icon' => settings()->pwa->app_icon ?? '',
                'app_icon_maskable' => settings()->pwa->app_icon_maskable ?? '',
            ];

            /* Screenshots */
            $mobile_screenshots = [];
            $desktop_screenshots = [];
            foreach([1, 2, 3, 4, 5, 6] as $key) {
                /* Mobile */
                settings()->pwa->{'mobile_screenshot_' .  $key} = \Altum\Uploads::process_upload(settings()->pwa->{'mobile_screenshot_' .  $key}, 'app_screenshots', 'mobile_screenshot_' .  $key, 'mobile_screenshot_' .  $key . '_remove', null);
                $value['mobile_screenshot_' .  $key] = settings()->pwa->{'mobile_screenshot_' .  $key};

                if($value['mobile_screenshot_' .  $key]) {
                    $mobile_screenshots[] = \Altum\Uploads::get_full_url('app_screenshots') . $value['mobile_screenshot_' .  $key];
                }

                /* Desktop */
                settings()->pwa->{'desktop_screenshot_' .  $key} = \Altum\Uploads::process_upload(settings()->pwa->{'desktop_screenshot_' .  $key}, 'app_screenshots', 'desktop_screenshot_' .  $key, 'desktop_screenshot_' .  $key . '_remove', null);
                $value['desktop_screenshot_' .  $key] = settings()->pwa->{'desktop_screenshot_' .  $key};

                if($value['desktop_screenshot_' .  $key]) {
                    $desktop_screenshots[] = \Altum\Uploads::get_full_url('app_screenshots') . $value['desktop_screenshot_' .  $key];
                }
            }

            /* Shortcuts */
            $shortcuts = [];
            foreach([1, 2, 3] as $key) {
                $value['shortcut_name_' . $key] = input_clean($_POST['shortcut_name_' . $key]);
                $value['shortcut_description_' . $key] = input_clean($_POST['shortcut_description_' . $key]);

                if(empty($_POST['shortcut_url_' . $key]) || !string_starts_with(SITE_URL, $_POST['shortcut_url_' . $key])) {
                    $_POST['shortcut_url_' . $key] = SITE_URL;
                }
                $value['shortcut_url_' . $key] = get_url($_POST['shortcut_url_' . $key]);

                settings()->pwa->{'shortcut_icon_' .  $key} = \Altum\Uploads::process_upload(settings()->pwa->{'shortcut_icon_' .  $key}, 'app_screenshots', 'shortcut_icon_' .  $key, 'shortcut_icon_' .  $key . '_remove', null);
                $value['shortcut_icon_' .  $key] = settings()->pwa->{'shortcut_icon_' .  $key};

                if($value['shortcut_icon_' .  $key]) {
                    $desktop_screenshots[] = \Altum\Uploads::get_full_url('app_screenshots') . $value['shortcut_icon_' .  $key];
                }

                $shortcuts[] = [
                    'name' => $value['shortcut_name_' . $key],
                    'description' => $value['shortcut_description_' . $key],
                    'url' => $value['shortcut_url_' . $key],
                    'icon_url' => $value['shortcut_icon_' .  $key] ? \Altum\Uploads::get_full_url('app_screenshots') . $value['shortcut_icon_' .  $key] : null,
                ];
            }

            /* Generate the manifest file */
            $manifest = pwa_generate_manifest([
                'name' => $_POST['app_name'],
                'short_name' => $_POST['short_app_name'],
                'description' => $_POST['app_description'],
                'background_color' => $_POST['background_color'],
                'theme_color' => $_POST['theme_color'],
                'app_icon_url' => settings()->pwa->app_icon ? \Altum\Uploads::get_full_url('app_icon') . settings()->pwa->app_icon : null,
                'app_icon_maskable_url' => settings()->pwa->app_icon_maskable ? \Altum\Uploads::get_full_url('app_icon') . settings()->pwa->app_icon_maskable : null,
                'start_url' => $_POST['app_start_url'],
                'mobile_screenshots' => $mobile_screenshots,
                'desktop_screenshots' => $desktop_screenshots,
                'shortcuts' => $shortcuts,
            ]);
            pwa_save_manifest($manifest);

            $this->update_settings('pwa', json_encode($value));
        }
    }

    public function image_optimizer() {
        $this->process();

        if(!empty($_POST)) {

            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            if(!\Altum\Plugin::is_active('image-optimizer')) {
                redirect('admin/settings/image_optimizer');
            }

            /* :) */
            $_POST['provider'] = isset($_POST['provider']) && in_array($_POST['provider'], ['local', 'resmushit', 'imagerypro']) ? $_POST['provider'] : 'local';
            $_POST['imagerypro_api_key'] = input_clean($_POST['imagerypro_api_key']);
            $_POST['quality'] = isset($_POST['quality']) & $_POST['quality'] >= 50 && $_POST['quality'] <= 100 ? (int) $_POST['quality'] : 75;

            $value = [
                'is_enabled' => isset($_POST['is_enabled']),
                'statistics_is_enabled' => isset($_POST['statistics_is_enabled']),
                'provider' => $_POST['provider'],
                'imagerypro_api_key' => $_POST['imagerypro_api_key'],
                'quality' => $_POST['quality'],
            ];

            $this->update_settings('image_optimizer', json_encode($value));
        }
    }

    public function dynamic_og_images() {
        $this->process();

        if(!empty($_POST)) {

            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            if(!\Altum\Plugin::is_active('dynamic-og-images')) {
                redirect('admin/settings/dynamic_og_images');
            }

            /* :) */
            $_POST['imagerypro_api_key'] = input_clean($_POST['imagerypro_api_key']);
            $_POST['api_key'] = input_clean($_POST['api_key']);
            $_POST['quality'] = isset($_POST['quality']) & $_POST['quality'] >= 50 && $_POST['quality'] <= 100 ? (int) $_POST['quality'] : 75;
            $_POST['title'] = input_clean($_POST['title'], 64);
            settings()->dynamic_og_images->logo = \Altum\Uploads::process_upload(settings()->dynamic_og_images->logo, 'logo_light', 'logo', 'logo_remove', null);
            settings()->dynamic_og_images->background = \Altum\Uploads::process_upload(settings()->dynamic_og_images->background, 'logo_light', 'background', 'background_remove', null);
            $_POST['screenshot_image_border_radius'] = isset($_POST['screenshot_image_border_radius']) & $_POST['screenshot_image_border_radius'] >= 0 && $_POST['screenshot_image_border_radius'] <= 40 ? (int) $_POST['screenshot_image_border_radius'] : 25;
            $_POST['title_color'] = !verify_hex_color($_POST['title_color']) ? '#ffffff' : $_POST['title_color'];
            $_POST['background_color'] = !verify_hex_color($_POST['background_color']) ? '#000000' : $_POST['background_color'];
            $_POST['refresh_interval'] = isset($_POST['refresh_interval']) & $_POST['refresh_interval'] >= 5 && $_POST['refresh_interval'] <= 90 ? (int) $_POST['refresh_interval'] : 10;

            $value = [
                'is_enabled' => isset($_POST['is_enabled']),
                'api_key' => $_POST['api_key'],
                'imagerypro_api_key' => $_POST['imagerypro_api_key'],
                'quality' => $_POST['quality'],
                'title' => $_POST['title'],
                'logo' => settings()->dynamic_og_images->logo ?? null,
                'background' => settings()->dynamic_og_images->background ?? null,
                'screenshot_image_border_radius' => $_POST['screenshot_image_border_radius'],
                'title_color' => $_POST['title_color'],
                'background_color' => $_POST['background_color'],
                'refresh_interval' => $_POST['refresh_interval'],
            ];

            $this->update_settings('dynamic_og_images', json_encode($value));
        }
    }

    public function email_shield() {
        $this->process();

        if(!empty($_POST)) {

            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            if(!\Altum\Plugin::is_active('dynamic-og-images')) {
                redirect('admin/settings/email_shield');
            }

            /* :) */
            $_POST['email_shield_api_key'] = input_clean($_POST['email_shield_api_key']);
            $_POST['whitelisted_domains'] = array_filter(array_map('trim', explode(',', $_POST['whitelisted_domains'])));

            $value = [
                'is_enabled' => isset($_POST['is_enabled']),
                'statistics_is_enabled' => isset($_POST['statistics_is_enabled']),
                'email_shield_api_key' => $_POST['email_shield_api_key'],
                'whitelisted_domains' => $_POST['whitelisted_domains'],
            ];

            $this->update_settings('email_shield', json_encode($value));
        }
    }

    public function sso() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $websites = [];

            foreach($_POST['id'] as $id) {
                $websites[$id] = [
                    'id' => get_slug($id),
                    'name' => $_POST['name'][$id],
                    'icon' => $_POST['icon'][$id],
                    'api_key' => $_POST['api_key'][$id],
                    'url' => string_ends_with('/', $_POST['url'][$id]) ? $_POST['url'][$id] : $_POST['url'][$id] . '/',
                ];
            }

            $value = [
                'is_enabled' => isset($_POST['is_enabled']),
                'display_menu_items' => isset($_POST['display_menu_items']),
                'websites' => $websites,
            ];

            $this->update_settings('sso', json_encode($value));
        }
    }

    public function cron() {
        /* Get the latest cronjob details */
        settings()->cron = json_decode(db()->where('`key`', 'cron')->getValue('settings', '`value`'));

        $this->process();
    }

    public function health() {
        $this->process();
    }

    public function cache() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            cache()->clear();

            /* Clear the language cache */
            \Altum\Language::clear_cache();

            /* Set a nice success message */
            Alerts::add_success(l('global.success_message.update2'));

            /* Refresh the page */
            redirect('admin/settings/cache');
        }
    }

    public function license() {
        $this->process();

        if(!empty($_POST) && !empty($_POST['new_license'])) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            $altumcode_api = 'https://api.altumcode.com/validate';

            /* Make sure the license is correct */
            $response = \Unirest\Request::post($altumcode_api, [], [
                'type'              => 'update',
                'license'           => $_POST['new_license'],
                'url'               => url(),
                'product_key'       => PRODUCT_KEY,
                'product_name'      => PRODUCT_NAME,
                'product_version'   => PRODUCT_VERSION,
            ]);

            if($response->body->status == 'error') {
                Alerts::add_error($response->body->message);
            }

            /* Success check */
            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {
                if($response->body->status == 'success') {
                    /* Run external SQL if needed */
                    if(!empty($response->body->sql)) {
                        $dump = array_filter(explode('-- SEPARATOR --', $response->body->sql));

                        foreach($dump as $query) {
                            database()->query($query);
                        }
                    }

                    Alerts::add_success($response->body->message);

                    $this->after_update_settings('license');
                }
            }

            redirect('admin/settings/license');
        }
    }

    public function support() {
        $this->process();

        if(!empty($_POST) && !empty($_POST['new_key'])) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            $altumcode_api = 'https://api.altumcode.com/validate-support-extension';
            //$altumcode_api = 'http://127.0.0.1/altumcode-api/validate-support-extension';

            /* Make sure the license is correct */
            $response = \Unirest\Request::post($altumcode_api, [], [
                'support_key'       => $_POST['new_key'],
                'license_type'      => settings()->license->type,
                'installation_url'  => url(),
                'product_key'       => PRODUCT_KEY,
                'product_name'      => PRODUCT_NAME,
                'product_version'   => PRODUCT_VERSION,
                'server_ip'         => $_SERVER['SERVER_ADDR'],
                'client_ip'         => get_ip()
            ]);

            if($response->body->status == 'error') {
                Alerts::add_error($response->body->message);
            }

            /* Success check */
            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {
                if($response->body->status == 'success') {
                    /* Run external SQL if needed */
                    if(!empty($response->body->sql)) {
                        database()->query($response->body->sql);
                    }

                    Alerts::add_success($response->body->message);

                    $this->after_update_settings('support');
                }
            }

            redirect('admin/settings/support');
        }
    }

    public function codes() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['ai_qr_codes_is_enabled'] = (int) isset($_POST['ai_qr_codes_is_enabled']);
            $_POST['qr_codes_is_enabled'] = (int) isset($_POST['qr_codes_is_enabled']);
            $_POST['qr_reader_is_enabled'] = (int) isset($_POST['qr_reader_is_enabled']);
            $_POST['barcodes_is_enabled'] = (int) isset($_POST['barcodes_is_enabled']);
            $_POST['barcode_reader_is_enabled'] = (int) isset($_POST['barcode_reader_is_enabled']);
            $_POST['logo_size_limit'] = $_POST['logo_size_limit'] > get_max_upload() || $_POST['logo_size_limit'] < 0 ? get_max_upload() : (float) $_POST['logo_size_limit'];
            $_POST['background_size_limit'] = $_POST['background_size_limit'] > get_max_upload() || $_POST['background_size_limit'] < 0 ? get_max_upload() : (float) $_POST['background_size_limit'];

            $available_qr_codes = [];
            foreach(require APP_PATH . 'includes/qr_codes.php' as $key => $value) {
                $available_qr_codes[$key] = in_array($key, $_POST['available_qr_codes'] ?? []);
            }

            $available_barcodes = [];
            foreach(require APP_PATH . 'includes/barcodes.php' as $key => $value) {
                $available_barcodes[$key] = in_array($key, $_POST['available_barcodes'] ?? []);
            }

            $qr_codes_branding_logo = \Altum\Uploads::process_upload(settings()->codes->qr_codes_branding_logo, 'qr_code_logo', 'qr_codes_branding_logo', 'qr_codes_branding_logo_remove', null);
            $ai_qr_codes_default_image = \Altum\Uploads::process_upload(settings()->codes->ai_qr_codes_default_image, 'ai_qr_code_default_image', 'ai_qr_codes_default_image', 'ai_qr_codes_default_image_remove', null);
            $qr_codes_default_image = \Altum\Uploads::process_upload(settings()->codes->qr_codes_default_image, 'qr_code_default_image', 'qr_codes_default_image', 'qr_codes_default_image_remove', null);

            $value = json_encode([
                'ai_qr_codes_is_enabled' => $_POST['ai_qr_codes_is_enabled'],
                'ai_qr_codes_replicate_api_key' => $_POST['ai_qr_codes_replicate_api_key'],
                'ai_qr_codes_default_image' => $ai_qr_codes_default_image,

                'qr_codes_is_enabled' => $_POST['qr_codes_is_enabled'],
                'qr_reader_is_enabled' => $_POST['qr_reader_is_enabled'],
                'barcodes_is_enabled' => $_POST['barcodes_is_enabled'],
                'barcode_reader_is_enabled' => $_POST['barcode_reader_is_enabled'],
                'logo_size_limit' => $_POST['logo_size_limit'],
                'background_size_limit' => $_POST['background_size_limit'],
                'available_qr_codes' => $available_qr_codes,
                'available_barcodes' => $available_barcodes,
                'qr_codes_branding_logo' => $qr_codes_branding_logo,
                'qr_codes_default_image' => $qr_codes_default_image,
            ]);

            $this->update_settings('codes', $value);
        }
    }

    public function links() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['random_url_length'] = isset($_POST['random_url_length']) && $_POST['random_url_length'] < 4 ? 4 : (int) $_POST['random_url_length'];
            $_POST['qr_reader_is_enabled'] = (int) isset($_POST['qr_reader_is_enabled']);
            $_POST['barcode_reader_is_enabled'] = (int) isset($_POST['barcode_reader_is_enabled']);
            $_POST['projects_is_enabled'] = (int) isset($_POST['projects_is_enabled']);
            $_POST['domains_is_enabled'] = (int) isset($_POST['domains_is_enabled']);
            $_POST['additional_domains_is_enabled'] = (int) isset($_POST['additional_domains_is_enabled']);
            $_POST['main_domain_is_enabled'] = (int) isset($_POST['main_domain_is_enabled']);
            $_POST['blacklisted_domains'] = array_filter(array_map('trim', explode(',', $_POST['blacklisted_domains'])));
            $_POST['blacklisted_keywords'] = array_filter(array_map('trim', explode(',', $_POST['blacklisted_keywords'])));
            $_POST['pixels_is_enabled'] = (int) isset($_POST['pixels_is_enabled']);
            $_POST['google_safe_browsing_is_enabled'] = (int) isset($_POST['google_safe_browsing_is_enabled']);
            $_POST['favicon_size_limit'] = $_POST['favicon_size_limit'] > get_max_upload() || $_POST['favicon_size_limit'] < 0 ? get_max_upload() : (float) $_POST['favicon_size_limit'];
            $_POST['opengraph_size_limit'] = $_POST['opengraph_size_limit'] > get_max_upload() || $_POST['opengraph_size_limit'] < 0 ? get_max_upload() : (float) $_POST['opengraph_size_limit'];

            $value = json_encode([
                'random_url_length' => $_POST['random_url_length'],
                'qr_reader_is_enabled' => $_POST['qr_reader_is_enabled'],
                'barcode_reader_is_enabled' => $_POST['barcode_reader_is_enabled'],
                'projects_is_enabled' => $_POST['projects_is_enabled'],
                'domains_is_enabled' => $_POST['domains_is_enabled'],
                'additional_domains_is_enabled' => $_POST['additional_domains_is_enabled'],
                'main_domain_is_enabled' => $_POST['main_domain_is_enabled'],
                'domains_custom_main_ip' => $_POST['domains_custom_main_ip'],
                'blacklisted_domains' => $_POST['blacklisted_domains'],
                'blacklisted_keywords' => $_POST['blacklisted_keywords'],
                'google_safe_browsing_is_enabled' => $_POST['google_safe_browsing_is_enabled'],
                'google_safe_browsing_api_key' => $_POST['google_safe_browsing_api_key'],
                'favicon_size_limit' => $_POST['favicon_size_limit'],
                'opengraph_size_limit' => $_POST['opengraph_size_limit'],
                'pixels_is_enabled' => $_POST['pixels_is_enabled'],
            ]);

            $this->update_settings('links', $value);
        }
    }

    public function send_test_email() {

        if(empty($_POST)) {
            redirect('admin/settings/smtp');
        }

        /* Check for any errors */
        $required_fields = ['email'];
        foreach($required_fields as $field) {
            if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                Alerts::add_field_error($field, l('global.error_message.empty_field'));
            }
        }

        if(!\Altum\Csrf::check()) {
            Alerts::add_error(l('global.error_message.invalid_csrf_token'));
        }

        /* If there are no errors, continue */
        if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

            $result = send_mail(
                $_POST['email'],
                settings()->main->title . ' - Test Email',
                'This is just a test email to confirm that the smtp email settings are properly working!<br /><br /><a href="' . SITE_URL . '" class="cta">Sample button</a>',
                [],
                null,
                true
            );

            if($result->ErrorInfo == '') {
                Alerts::add_success(l('admin_settings_send_test_email_modal.success_message'));
            } else {
                Alerts::add_error(sprintf(l('admin_settings_send_test_email_modal.error_message'), $result->ErrorInfo));
                Alerts::add_info(implode('<br />', $result->errors));
            }

        }

        redirect('admin/settings/smtp');
    }

}
