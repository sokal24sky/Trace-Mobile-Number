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

defined('ALTUMCODE') || die();

class Alerts {
    public static $types = ['success', 'error', 'info', 'warning'];

    /* Field errors */
    public static function add_field_error($key, $message) {
        if(!isset($_SESSION['field_errors'][$key])) {
            $_SESSION['field_errors'][$key] = [$message];
        } else {
            $_SESSION['field_errors'][$key][] = $message;
        }
    }

    public static function has_field_errors($key = null) {
        if(is_null($key)) {
            return !empty($_SESSION['field_errors']);
        }
        else if(is_array($key)) {
            $has_errors = false;

            foreach($key as $field_name) {
                /* Regex check if needed */
                if(strpos($field_name, '*') !== false) {

                    foreach(($_SESSION['field_errors'] ?? []) as $session_field_error_key => $session_field_error_value) {
                        if(mb_ereg($field_name, $session_field_error_key) && !empty($session_field_error_value)) {
                            $has_errors = true;
                            break;
                        }
                    }

                }

                /* Exact checks */
                else {
                    if(isset($_SESSION['field_errors'][$field_name]) && !empty($_SESSION['field_errors'][$field_name])) {
                        $has_errors = true;
                        break;
                    }
                }

            }

            return $has_errors;
        }
        else {
            return isset($_SESSION['field_errors'][$key]) && !empty($_SESSION['field_errors'][$key]);
        }
    }

    public static function get_first_field_error($key) {
        return reset($_SESSION['field_errors'][$key]);
    }

    public static function output_field_error($key) {
        $output = null;

        if(self::has_field_errors($key)) {
            $output = '<div class="invalid-feedback d-inline-block">' . self::get_first_field_error($key) . '</div>';

            unset($_SESSION['field_errors'][$key]);
            
            if(empty($_SESSION['field_errors'])) {
                unset($_SESSION['field_errors']);
            }
        }

        return $output;
    }

    public static function clear_field_errors($key = null) {
        if($key) {
            unset($_SESSION['field_errors'][$key]);
        } else {
            unset($_SESSION['field_errors']);
        }
    }

    /* Session alerts */
    public static function add($type, $key, $message) {
        if(!isset($_SESSION[$type][$key])) {
            $_SESSION[$type][$key] = [$message];
        } else {
            $_SESSION[$type][$key][] = $message;
        }
    }

    public static function has($type, $key) {
        if(is_null($key)) {
            return isset($_SESSION[$type]) && count($_SESSION[$type]);
        } else {
            return isset($_SESSION[$type][$key]);
        }
    }

    public static function get($type, $key) {
        return $_SESSION[$type][$key];
    }

    public static function output_alerts($type = null) {
        $types = is_null($type) ? self::$types : [$type];
        $output = null;

        foreach($types as $type) {
            if(!isset($_SESSION[$type]) || empty($_SESSION[$type])) {
                continue;
            }

            foreach($_SESSION[$type] as $key => $value) {
                foreach($value as $message_key => $message) {
                    $output .= output_alert($type, $message);
                }

                unset($_SESSION[$type][$key]);
            }
        }

        return $output;
    }

    /* Errors */
    public static function add_warning($message, $key = 'warning') {
        self::add('warning', $key, $message);
    }

    public static function has_warnings($key = null) {
        return self::has('warning', $key);
    }

    public static function add_error($message, $key = 'error') {
        self::add('error', $key, $message);
    }

    public static function has_errors($key = null) {
        return self::has('error', $key);
    }

    /* Infos */
    public static function add_info($message, $key = 'info') {
        self::add('info', $key, $message);
    }

    public static function has_infos($key = null) {
        return self::has('info', $key);
    }

    /* Successes */
    public static function add_success($message, $key = 'success') {
        self::add('success', $key, $message);
    }

    public static function has_successes($key = null) {
        return self::has('success', $key);
    }

    public static function clear() {

    }
}
