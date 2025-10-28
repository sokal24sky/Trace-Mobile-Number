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

class Title {
    public static $full_title;
    public static $site_title;
    public static $page_title;
    public static $title_separator = '-';

    public static function initialize($site_title, $title_separator = '-') {

        self::$site_title = $site_title;
        self::$title_separator = $title_separator;

        /* Add the prefix if needed */
        $language_key = preg_replace('/-/', '_', \Altum\Router::$controller_key);

        if(\Altum\Router::$path != '') {
            $language_key = \Altum\Router::$path . '_' . $language_key;
        }

        /* Check if the default is viable and use it */
        $page_title = (l($language_key . '.title')) ? l($language_key . '.title') : \Altum\Router::$controller;

        self::set($page_title);
    }

    public static function set($page_title, $full = false) {

        self::$page_title = self::$title_separator == '-' ? $page_title : str_replace(' - ', ' ' . self::$title_separator . ' ', $page_title);

        self::$full_title = self::$page_title . ($full ? null : ' ' . self::$title_separator . ' ' . self::$site_title);

        /* Set title */
        Meta::set_social_title(self::$full_title);
    }


    public static function get() {

        return self::$full_title;

    }

}
