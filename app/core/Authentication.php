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

namespace Altum;

use Altum\Models\User;

defined('ALTUMCODE') || die();

class Authentication {

    public static $is_logged_in = null;
    public static $user_id = null;
    public static $user = null;
    public static $login_guard_is_set = false;

    public static function check() {

        /* Verify if the current route allows use to do the check */
        if(\Altum\Router::$controller_settings['no_authentication_check']) {
            return false;
        }

        /* Already logged in from previous checks */
        if(self::$is_logged_in) {
            return self::$user_id;
        }

        /* Check the cookies first */
        if(
            isset($_COOKIE['user_id'])
            && isset($_COOKIE['token_code'])
            && mb_strlen($_COOKIE['token_code']) > 0
            && $user = (new User())->get_user_by_user_id($_COOKIE['user_id'])
        ) {
           if($user->token_code == $_COOKIE['token_code'] && isset($_COOKIE['user_password_hash']) && $_COOKIE['user_password_hash'] == md5($user->password)) {
               self::$is_logged_in = true;
               self::$user_id = $user->user_id;

               self::$user = $user;

               return true;
           }
        }

        /* Check the Session */
        if(
            isset($_SESSION['user_id'])
            && !empty($_SESSION['user_id'])
            && $user = (new User())->get_user_by_user_id($_SESSION['user_id'])
        ) {
            if(isset($_SESSION['user_password_hash']) && $_SESSION['user_password_hash'] == md5($user->password ?? '')) {
                self::$is_logged_in = true;
                self::$user_id = $user->user_id;

                self::$user = $user;

                return true;
            }
        }

        return false;
    }


    public static function is_admin() {

        if(!self::check()) {
            return false;
        }

        return self::$user->type > 0;
    }


    public static function guard($permission = 'user') {

        switch ($permission) {
            case 'guest':

                if(self::check()) {
                    redirect(isset($_GET['redirect']) ? $_GET['redirect'] : 'dashboard');
                }

                break;

            case 'user':

                if(!self::check()) {
                    redirect('login?redirect=' . \Altum\Router::$original_request . (\Altum\Router::$original_request_query ? '?' . \Altum\Router::$original_request_query : null));
                }

                /* Check if user is banned */
                if(self::$user->status != 1) {
                    self::logout();
                }

                self::$login_guard_is_set = true;

                break;

            case 'admin':

                if(!self::check()) {
                    redirect('login?redirect=' . \Altum\Router::$original_request . (\Altum\Router::$original_request_query ? '?' . \Altum\Router::$original_request_query : null));
                }

                /* Check if user is banned */
                if(self::$user->status != 1) {
                    self::logout();
                }

                /* Check if user is admin */
                if(self::$user->type != 1) {
                    redirect('dashboard');
                }

                self::$login_guard_is_set = true;

                break;
        }

    }


    public static function logout($page = '') {

        if(self::check()) {
            db()->where('user_id', self::$user_id)->update('users', ['token_code' => '']);

            /* Clear the cache */
            cache()->deleteItemsByTag('user_id=' . self::$user_id);
        }

        session_destroy();
        setcookie('user_id', '', time()-30, COOKIE_PATH);
        setcookie('token_code', '', time()-30, COOKIE_PATH);
        setcookie('user_password_hash', '', time()-30, COOKIE_PATH);
        setcookie('spotlight_has_results', '', time()-30, COOKIE_PATH);

        if($page !== false) {
            redirect($page);
        }
    }

    public static function get_authorization_bearer() {
        $headers = getallheaders();
        $authorization_header = $headers['Authorization'] ?? $headers['authorization'] ?? null;

        if(!$authorization_header) {
            return null;
        }

        if(!preg_match('/Bearer\s(\S+)/', $authorization_header, $matches)) {
            return null;
        }

        return $matches[1];
    }
}
