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

class Meta {
    public static $link_alternate = true;
    public static $description = null;
    public static $keywords = null;
    public static $canonical = null;
    public static $robots = null;
    public static $opengraph = [
        'og:type' => 'website',
        'og:url' => null,
        'og:title' => null,
        'og:description' => null,
        'og:image' => null,
    ];
    public static $twitter = [
        /* Twitter */
        'twitter:card' => 'summary_large_image',
        'twitter:site' => null,
        'twitter:url' => null,
        'twitter:title' => null,
        'twitter:description' => null,
        'twitter:image' => null
    ];

    public static function initialize() {

        /* Add the prefix if needed */
        $language_key = preg_replace('/-/', '_', \Altum\Router::$controller_key);

        if(\Altum\Router::$path != '') {
            $language_key = \Altum\Router::$path . '_' . $language_key;
        }

        /* Check if the default is viable and use it */
        self::$description = l($language_key . '.meta_description', null, true);
        self::$keywords = l($language_key . '.meta_keywords', null, true);

        /* Set title */
        self::set_social_title(\Altum\Title::get());

        /* Opengraph image */
        if(settings()->main->opengraph) {
            self::set_social_image(\Altum\Uploads::get_full_url('opengraph') . settings()->main->opengraph);
        }

        /* Canonical automation */
        self::set_canonical_url();

        /* Twitter */
        self::$twitter['twitter:site'] = settings()->socials->x ? '@' . settings()->socials->x : null;
    }

    public static function set_description($value) {
        self::$description = $value;
        self::set_social_description($value);
    }

    public static function set_keywords($value) {
        self::$keywords = $value;
    }

    public static function set_social_url($value) {
        self::$opengraph['og:url'] = $value;
        self::$twitter['twitter:url'] = $value;
    }

    public static function set_social_title($value) {
        self::$opengraph['og:title'] = $value;
        self::$twitter['twitter:title'] = $value;
    }

    public static function set_social_description($value) {
        self::$opengraph['og:description'] = $value;
        self::$twitter['twitter:description'] = $value;
    }

    public static function set_social_image($value) {
        self::$opengraph['og:image'] = $value;
        self::$twitter['twitter:image'] = $value;
    }

    public static function set_canonical_url($value = null) {
        self::$canonical = $value ?? url(\Altum\Router::$original_request);
    }

    public static function set_robots($value) {
        self::$robots = $value;
    }

    public static function output() {
        self::$opengraph['og:site_name'] = settings()->main->title;
        self::$opengraph['og:url'] = self::$opengraph['og:url'] ?: url(\Altum\Router::$original_request);
        self::$twitter['twitter:url'] = self::$twitter['twitter:url'] ?: url(\Altum\Router::$original_request);

        echo '<!-- Open graph / Twitter markup -->' . "\n";
        foreach(\Altum\Meta::$opengraph as $key => $value) {
            if($value) {
                echo '<meta property="' . $key . '" content="' . $value . '" />' . "\n";
            }
        }

        foreach(\Altum\Meta::$twitter as $key => $value) {
            if($value) {
                echo '<meta name="' . $key . '" content="' . $value . '" />' . "\n";
            }
        }
    }

    public static function set_link_alternate($value) {
        self::$link_alternate = $value;
    }
}
