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
use Altum\Captcha;
use Altum\Logger;
use Altum\Models\User;
use Google\Client;
use Google\Service\Oauth2;

defined('ALTUMCODE') || die();

class Login extends Controller {

    public function index() {

        $method	= (isset($this->params[0])) ? $this->params[0] : null;
        $redirect = process_and_get_redirect_params() ?? 'dashboard';
        $redirect_append = $redirect ? '?redirect=' . $redirect : null;

        if($method !== 'one-time-login-code') {
            \Altum\Authentication::guard('guest');
        }

        /* Default values */
        $values = [
            'email' => isset($_GET['email']) && is_string($_GET['email']) ? query_clean($_GET['email']) : '',
            'password' => '',
            'rememberme' => isset($_POST['rememberme']) || settings()->users->login_rememberme_checkbox_is_checked,
        ];

        //ALTUMCODE:DEMO if(DEMO) {$values['email'] = 'admin'; $values['password'] = 'admin';$user=(object)['twofa_secret' => null];}

        /* Initiate captcha */
        $captcha = new Captcha();

        /* One time login */
        if($method == 'one-time-login-code') {
            $one_time_login_code = isset($this->params[1]) ? query_clean($this->params[1]) : null;

            if(empty($one_time_login_code)) {
                redirect('login' . $redirect_append);
            }

            /* Try to get the user from the database */
            $user = db()->where('one_time_login_code', $one_time_login_code)->getOne('users', ['user_id', 'password', 'name', 'status', 'language']);

            if(!$user) {
                redirect('login' . $redirect_append);
            }

            if($user->status != 1) {
                Alerts::add_error(l('login.error_message.user_not_active'));
                redirect('login' . $redirect_append);
            }

            /* Login the user */
            $_SESSION['user_id'] = $user->user_id;
            $_SESSION['user_password_hash'] = md5($user->password);

            (new User())->login_aftermath_update($user->user_id);

            /* Remove one time login */
            db()->where('user_id', $user->user_id)->update('users', ['one_time_login_code' => null]);

            /* Set a welcome message */
            Alerts::add_info(sprintf(l('login.info_message.logged_in'), $user->name));

            /* Check to see if the user has a custom language set */
            if(\Altum\Language::$name == $user->language) {
                redirect($redirect);
            } else {
                redirect((\Altum\Language::$active_languages[$user->language] ? \Altum\Language::$active_languages[$user->language] . '/' : null) . $redirect, true);
            }
        }

        /* Facebook Login / Register */
        if(settings()->facebook->is_enabled && in_array($method, ['facebook-initiate', 'facebook'])) {
            $facebook_config = [
                'callback' => SITE_URL . 'login/facebook',
                'keys' => [
                    'key' => settings()->facebook->app_id,
                    'secret' => settings()->facebook->app_secret,
                ],
                'scope' => 'email public_profile',
            ];

            if($method == 'facebook-initiate') {
                $_SESSION['register_language'] = \Altum\Language::$name;

                try {
                    $facebook = new \Hybridauth\Provider\Facebook($facebook_config);
                    $facebook->disconnect();
                    $facebook->authenticate();
                } catch (\Exception $exception) {
                    Alerts::add_error($exception->getMessage());
                    redirect('login' . $redirect_append);
                }
            }

            if($method == 'facebook' && isset($_GET['code'])) {
                try {
                    $facebook = new \Hybridauth\Provider\Facebook($facebook_config);
                    $facebook->authenticate();
                    $facebook_account_info = $facebook->getUserProfile();
                    $name = $facebook_account_info->displayName;
                    $email = $facebook_account_info->email;

                    if(is_null($email)) {
                        Alerts::add_error(l('login.error_message.email_is_null'));
                        redirect('login' . $redirect_append);
                    }

                    $this->process_social_login($email, $name, $redirect, $method);
                } catch (\Exception $exception) {
                    Alerts::add_error($exception->getMessage());
                    redirect('login' . $redirect_append);
                }
            }
        }

        /* Google Login / Register */
        if(settings()->google->is_enabled && in_array($method, ['google-initiate', 'google'])) {
            $google_config = [
                'callback' => SITE_URL . 'login/google',
                'keys' => [
                    'key' => settings()->google->client_id,
                    'secret' => settings()->google->client_secret,
                ],
                'scope' => 'email profile',
            ];

            if($method == 'google-initiate') {
                $_SESSION['register_language'] = \Altum\Language::$name;

                try {
                    $google = new \Hybridauth\Provider\Google($google_config);
                    $google->disconnect();
                    $google->authenticate();
                } catch (\Exception $exception) {
                    Alerts::add_error($exception->getMessage());
                    redirect('login' . $redirect_append);
                }
            }

            if($method == 'google' && isset($_GET['code'])) {
                try {
                    $google = new \Hybridauth\Provider\Google($google_config);
                    $google->authenticate();
                    $google_account_info = $google->getUserProfile();
                    $name = $google_account_info->displayName;
                    $email = $google_account_info->email;

                    if(is_null($email)) {
                        Alerts::add_error(l('login.error_message.email_is_null'));
                        redirect('login' . $redirect_append);
                    }

                    $this->process_social_login($email, $name, $redirect, $method);
                } catch (\Exception $exception) {
                    Alerts::add_error($exception->getMessage());
                    redirect('login' . $redirect_append);
                }
            }
        }

        /* X Login / Register */
        if(settings()->twitter->is_enabled && in_array($method, ['twitter-initiate', 'twitter'])) {
            $twitter_config = [
                'callback' => SITE_URL . 'login/twitter',
                'keys' => [
                    'key' => settings()->twitter->consumer_api_key,
                    'secret' => settings()->twitter->consumer_api_secret,
                ],
                'include_email' => true,
            ];

            if($method == 'twitter-initiate') {
                $_SESSION['register_language'] = \Altum\Language::$name;

                try {
                    $twitter = new \Hybridauth\Provider\Twitter($twitter_config);
                    $twitter->disconnect();
                    $twitter->authenticate();
                } catch (\Exception $exception) {
                    Alerts::add_error($exception->getMessage());
                    redirect('login' . $redirect_append);
                }
            }

            if($method == 'twitter' && isset($_GET['oauth_token'], $_GET['oauth_verifier'])) {
                try {
                    $twitter = new \Hybridauth\Provider\Twitter($twitter_config);
                    $twitter->authenticate();
                    $twitter_account_info = $twitter->getUserProfile();
                    $name = $twitter_account_info->displayName;
                    $email = $twitter_account_info->email;

                    if(is_null($email)) {
                        Alerts::add_error(l('login.error_message.email_is_null'));
                        redirect('login' . $redirect_append);
                    }

                    $this->process_social_login($email, $name, $redirect, $method);
                } catch (\Exception $exception) {
                    Alerts::add_error($exception->getMessage());
                    redirect('login' . $redirect_append);
                }
            }
        }

        /* Discord Login / Register */
        if(settings()->discord->is_enabled && in_array($method, ['discord-initiate', 'discord'])) {
            $discord_config = [
                'callback' => SITE_URL . 'login/discord',
                'keys' => [
                    'key' => settings()->discord->client_id,
                    'secret' => settings()->discord->client_secret,
                ],
                'scope' => 'email identify',
            ];

            if($method == 'discord-initiate') {
                $_SESSION['register_language'] = \Altum\Language::$name;

                try {
                    $discord = new \Hybridauth\Provider\Discord($discord_config);
                    $discord->disconnect();
                    $discord->authenticate();
                } catch (\Exception $exception) {
                    Alerts::add_error($exception->getMessage());
                    redirect('login' . $redirect_append);
                }
            }

            if($method == 'discord' && isset($_GET['code'])) {
                try {
                    $discord = new \Hybridauth\Provider\Discord($discord_config);
                    $discord->authenticate();
                    $discord_account_info = $discord->getUserProfile();
                    $name = $discord_account_info->displayName;
                    $email = $discord_account_info->email;

                    if(is_null($email)) {
                        Alerts::add_error(l('login.error_message.email_is_null'));
                        redirect('login' . $redirect_append);
                    }

                    $this->process_social_login($email, $name, $redirect, $method);
                } catch (\Exception $exception) {
                    Alerts::add_error($exception->getMessage());
                    redirect('login' . $redirect_append);
                }
            }
        }

        /* LinkedIn Login / Register */
        if(settings()->linkedin->is_enabled && in_array($method, ['linkedin-initiate', 'linkedin'])) {
            $linkedin_config = [
                'callback' => SITE_URL . 'login/linkedin',
                'keys' => [
                    'key' => settings()->linkedin->client_id,
                    'secret' => settings()->linkedin->client_secret,
                ],
                'scope' => 'openid profile email',
            ];

            if($method == 'linkedin-initiate') {
                $_SESSION['register_language'] = \Altum\Language::$name;

                try {
                    $linkedin = new \Hybridauth\Provider\LinkedInOpenID($linkedin_config);
                    $linkedin->disconnect();
                    $linkedin->authenticate();
                } catch (\Exception $exception) {
                    Alerts::add_error($exception->getMessage());
                    redirect('login' . $redirect_append);
                }
            }

            if($method == 'linkedin' && isset($_GET['code'])) {
                try {
                    $linkedin = new \Hybridauth\Provider\LinkedInOpenID($linkedin_config);
                    $linkedin->authenticate();
                    $linkedin_account_info = $linkedin->getUserProfile();
                    $name = $linkedin_account_info->displayName;
                    $email = $linkedin_account_info->email;

                    if(is_null($email)) {
                        Alerts::add_error(l('login.error_message.email_is_null'));
                        redirect('login' . $redirect_append);
                    }

                    $this->process_social_login($email, $name, $redirect, $method);
                } catch (\Exception $exception) {
                    Alerts::add_error($exception->getMessage());
                    redirect('login' . $redirect_append);
                }
            }
        }

        /* Microsoft Login / Register */
        if(settings()->microsoft->is_enabled && in_array($method, ['microsoft-initiate', 'microsoft'])) {
            $microsoft_config = [
                'callback' => SITE_URL . 'login/microsoft',
                'keys' => [
                    'id' => settings()->microsoft->client_id,
                    'secret' => settings()->microsoft->client_secret,
                ],
                'scope' => 'user.read',
                'tenant' => 'common',
            ];

            if($method == 'microsoft-initiate') {
                $_SESSION['register_language'] = \Altum\Language::$name;

                try {
                    $microsoft = new \Hybridauth\Provider\MicrosoftGraph($microsoft_config);
                    $microsoft->disconnect();
                    $microsoft->authenticate();
                } catch (\Exception $exception) {
                    Alerts::add_error($exception->getMessage());
                    redirect('login' . $redirect_append);
                }
            }

            if($method == 'microsoft' && isset($_GET['code'])) {
                try {
                    $microsoft = new \Hybridauth\Provider\MicrosoftGraph($microsoft_config);
                    $microsoft->authenticate();
                    $microsoft_account_info = $microsoft->getUserProfile();
                    $name = $microsoft_account_info->displayName;
                    $email = $microsoft_account_info->email;
                    $id = $microsoft_account_info->identifier;

                    if(is_null($email)) {
                        Alerts::add_error(l('login.error_message.email_is_null'));
                        redirect('login' . $redirect_append);
                    }

                    $this->process_social_login($email, $name, $redirect, $method, $id);
                } catch (\Exception $exception) {
                    Alerts::add_error($exception->getMessage());
                    redirect('login' . $redirect_append);
                }
            }
        }

        if(!empty($_POST)) {
            /* Clean email and encrypt the password */
            $_POST['email'] = input_clean_email($_POST['email'] ?? '');
            $_POST['twofa_token'] = isset($_POST['twofa_token']) ? query_clean(str_replace(' ', '', $_POST['twofa_token'] ?? '')) : null;
            $values['email'] = $_POST['email'];
            $values['password'] = $_POST['password'];

            /* Check for any errors */
            $required_fields = ['email', 'password'];
            foreach($required_fields as $field) {
                if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                    Alerts::add_field_error($field, l('global.error_message.empty_field'));
                }
            }

            if(settings()->captcha->login_is_enabled && !isset($_SESSION['twofa_required']) && !$captcha->is_valid()) {
                Alerts::add_field_error('captcha', l('global.error_message.invalid_captcha'));
            }

            /* Make sure to check against the limiter */
            if(settings()->users->login_lockout_is_enabled) {
                $minutes_ago_datetime = (new \DateTime())->modify('-' . settings()->users->login_lockout_time . ' minutes')->format('Y-m-d H:i:s');

                $recent_fails = db()->where('ip', get_ip())->where('type', 'login.wrong_password')->where('datetime', $minutes_ago_datetime, '>=')->getValue('users_logs', 'COUNT(*)');

                if($recent_fails >= settings()->users->login_lockout_max_retries) {
                    Alerts::add_error(sprintf(l('global.error_message.limit_try_again'), settings()->users->login_lockout_time, l('global.date.minutes')));
                    setcookie('login_lockout', 'true', time()+60*settings()->users->login_lockout_time, COOKIE_PATH);
                    $_COOKIE['login_lockout'] = 'true';
                }
            }

            /* Try to get the user from the database */
            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {
                $user = db()->where('email', $_POST['email'])->getOne('users', ['user_id', 'email', 'name', 'status', 'password', 'token_code', 'twofa_secret', 'language']);

                if(!$user) {
                    Alerts::add_error(l('login.error_message.wrong_login_credentials'));
                } else {

                    if($user->status != 1) {
                        Alerts::add_error(l('login.error_message.user_not_active'));
                    } else

                        if(!password_verify($_POST['password'], $user->password)) {
                            Logger::users($user->user_id, 'login.wrong_password');

                            Alerts::add_error(l('login.error_message.wrong_login_credentials'));
                        }

                }
            }

            /* Check if the user has Two-factor Authentication enabled */
            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {
                if($user && $user->twofa_secret) {
                    $_SESSION['twofa_required'] = 1;


                    if($_POST['twofa_token']) {
                        $twofa = new \RobThree\Auth\TwoFactorAuth(new \RobThree\Auth\Providers\Qr\BaconQrCodeProvider(format: 'svg'), settings()->main->title, 6, 30);
                        $twofa_check = $twofa->verifyCode($user->twofa_secret, $_POST['twofa_token']);

                        if(!$twofa_check) {
                            Alerts::add_field_error('twofa_token', l('login.error_message.twofa_token'));
                        }
                    } else {
                        Alerts::add_info(l('login.info_message.twofa_token'));
                    }
                }
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors() && !Alerts::has_infos()) {

                /* If remember me is checked, log the user with cookies for X days else, remember just with a session */
                if(isset($_POST['rememberme'])) {
                    $token_code = $user->token_code;

                    /* Generate a new token */
                    if(empty($user->token_code)) {
                        $token_code = md5($user->email . microtime());

                        db()->where('user_id', $user->user_id)->update('users', ['token_code' => $token_code]);
                    }

                    setcookie('user_id', $user->user_id, time()+60*60*24* (settings()->users->login_rememberme_cookie_days ?? 30), COOKIE_PATH);
setcookie('token_code', $token_code, time()+60*60*24* (settings()->users->login_rememberme_cookie_days ?? 30), COOKIE_PATH);
setcookie('user_password_hash', md5($user->password), time()+60*60*24* (settings()->users->login_rememberme_cookie_days ?? 30), COOKIE_PATH);

                } else {
                    $_SESSION['user_id'] = $user->user_id;
                    $_SESSION['user_password_hash'] = md5($user->password);
                }

                unset($_SESSION['twofa_required']);

                (new User())->login_aftermath_update($user->user_id);

                Alerts::add_info(sprintf(l('login.info_message.logged_in'), $user->name));

                /* Check to see if the user has a custom language set */
                if(\Altum\Language::$name == $user->language) {
                    redirect($redirect);
                } else {
                    redirect((\Altum\Language::$active_languages[$user->language] ? \Altum\Language::$active_languages[$user->language] . '/' : null) . $redirect, true);
                }
            }
        }

        if(empty($_POST)) {
            unset($_SESSION['twofa_required']);
        }

        /* Prepare the view */
        $data = [
            'captcha' => $captcha,
            'values' => $values,
            'redirect_append' => $redirect_append,
            'user' => $user ?? null
        ];

        $view = new \Altum\View('login/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    /* After a successful social login auth, register or login the user */
    private function process_social_login($email, $name, $redirect, $method, $id = null) {

        /* Clear session variable */
        if(isset($_SESSION['register_language'])) {
            \Altum\Language::set_by_name($_SESSION['register_language']);
            unset($_SESSION['register_language']);
        }

        /* If the user is already in the system, log him in */
        if($user = db()->where('email', $email)->getOne('users', ['user_id', 'email', 'password', 'lost_password_code', 'language', 'name', 'extra'])) {

            /* Make sure the account has the id matching in case its required */
            if($id && $method == 'microsoft') {
                $user->extra = json_decode($user->extra ?? '');
                if($id != $user->extra->initial_social_id) {
                    throw new \Exception(l('login.error_message.email_is_null'));
                }
            }

            /* Make sure the user has a password set before letting the user login */
            (new User())->verify_null_password($user->user_id, $user->email, $user->password);

            $_SESSION['user_id'] = $user->user_id;
            $_SESSION['user_password_hash'] = md5($user->password);

            (new User())->login_aftermath_update($user->user_id, $method);

            Alerts::add_info(sprintf(l('login.info_message.logged_in'), $user->name));

            /* Check to see if the user has a custom language set */
            if(\Altum\Language::$name == $user->language) {
                redirect($redirect);
            } else {
                redirect((\Altum\Language::$active_languages[$user->language] ? \Altum\Language::$active_languages[$user->language] . '/' : null) . $redirect, true);
            }
        }

        /* Create a new account */
        else {

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

                /* Determine what plan is set by default */
                $plan_id                    = 'free';
                $plan_settings              = json_encode(settings()->plan_free->settings ?? '');
                $plan_expiration_date       = get_date();
                $lost_password_code         = md5($email . microtime());
                $password                   = settings()->users->register_social_login_require_password ? null : md5($email . microtime() . rand());

                $registered_user = (new User())->create(
                    $email,
                    $password,
                    $name,
                    1,
                    $method,
                    null,
                    $lost_password_code,
                    false,
                    $plan_id,
                    $plan_settings,
                    $plan_expiration_date,
                    settings()->main->default_timezone,
                    [
                        'initial_social_method' => $method,
                        'initial_social_id' => $id,
                    ]
                );

                /* Log the action */
                Logger::users($registered_user['user_id'], 'register.' . $method . '.success');

                /* Send a welcome email if needed */
                if(settings()->users->welcome_email_is_enabled) {

                    $email_template = get_email_template(
                        [],
                        l('global.emails.user_welcome.subject'),
                        [
                            '{{NAME}}' => $name,
                            '{{URL}}' => url(),
                                '{{DASHBOARD_LINK}}' => url('dashboard'),
                        ],
                        l('global.emails.user_welcome.body')
                    );

                    send_mail($email, $email_template->subject, $email_template->body);

                }

                /* Send notification to admin if needed */
                if(settings()->email_notifications->new_user && !empty(settings()->email_notifications->emails)) {

                    $email_template = get_email_template(
                        [],
                        l('global.emails.admin_new_user_notification.subject'),
                        [
                            '{{NAME}}' => $name,
                            '{{EMAIL}}' => $email,
                            '{{SOURCE}}' => $registered_user['source'],
                            '{{IP}}' => $registered_user['ip'],
                            '{{COUNTRY_NAME}}' => $registered_user['country'] ? get_country_from_country_code($registered_user['country']) : l('global.unknown'),
                            '{{CITY_NAME}}' => $registered_user['city_name'] ?? l('global.unknown'),
                            '{{DEVICE_TYPE}}' => l('global.device.' . $registered_user['device_type']),
                            '{{OS_NAME}}' => $registered_user['os_name'],
                            '{{BROWSER_NAME}}' => $registered_user['browser_name'],
                            '{{USER_LINK}}' => url('admin/user-view/' . $registered_user['user_id']),
                        ],
                        l('global.emails.admin_new_user_notification.body')
                    );

                    send_mail(explode(',', settings()->email_notifications->emails), $email_template->subject, $email_template->body);

                }

                /* Send webhook notification if needed */
                if(settings()->webhooks->user_new) {
                    fire_and_forget('post', settings()->webhooks->user_new, [
                        'user_id' => $registered_user['user_id'],
                        'email' => $email,
                        'name' => $name,
                        'source' => $method,
                        'is_newsletter_subscribed' => false,
                        'datetime' => get_date()
                    ]);
                }

                /* Send internal notification if needed */
                if(settings()->internal_notifications->admins_is_enabled && settings()->internal_notifications->new_user) {
                    db()->insert('internal_notifications', [
                        'for_who' => 'admin',
                        'from_who' => 'system',
                        'icon' => 'fas fa-user',
                        'title' => l('global.notifications.new_user.title'),
                        'description' => sprintf(l('global.notifications.new_user.description'), $name, $email),
                        'url' => 'admin/user-view/' . $registered_user['user_id'],
                        'datetime' => get_date(),
                    ]);
                }

                if($password) {
                    /* Login the user */
                    $_SESSION['user_id'] = $registered_user['user_id'];
                    $_SESSION['user_password_hash'] = md5($registered_user['password']);

                    (new User())->login_aftermath_update($registered_user['user_id'], $method);

                    /* Set a nice success message */
                    Alerts::add_success(l('register.success_message.login'));

                    redirect($redirect .'?welcome=' . $registered_user['user_id']);
                } else {
                    /* Redirect the newly created user to set a new password */
                    redirect('reset-password/' . md5($email) . '/' . $lost_password_code . '?redirect=' . $redirect . '&welcome=' . $registered_user['user_id']);
                }
            }
        }
    }

}
