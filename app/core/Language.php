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

class Language {
    /* Selected language */
    public static $name;
    public static $code;
    public static $status;

    /* Available languages found in the /app/languages directory */
    public static $languages = [];
    public static $languages_ordered = [];

    /* Enabled languages, for easy reference */
    /* $name => $code */
    public static $active_languages = [];

    /* Defaults */
    public static $main_name = 'english';
    public static $default_name;
    public static $default_code;

    /* Languages directory path */
    public static $path = APP_PATH . 'languages/';

    public static function initialize() {

        /* Determine all the languages available in the directory */
        foreach(glob(self::$path . '*.php') as $file_path) {
            $file_path_exploded = explode('/', $file_path);
            $file_name = str_replace('.php', '', trim(end($file_path_exploded)));

            if($file_name == 'english#en#active' || $file_name == 'english#en#inactive') {
                continue;
            }

            /* Parse file details */
            $file_name_exploded = explode('#', $file_name);

            $language = [
                'name' => $file_name_exploded[0],
                'code' => $file_name_exploded[1],
                'status' => settings()->languages->{$file_name_exploded[0]}->status ?? true,
                'content' => null,
                'order' => settings()->languages->{$file_name_exploded[0]}->order ?? 1,
                'language_flag' => settings()->languages->{$file_name_exploded[0]}->language_flag ?? '',
            ];

            self::$languages[$language['name']] = $language;

            if($language['status']) {
                self::$active_languages[$language['name']] = $language['code'];
            }
        }

        /* Sort by order */
        self::$languages_ordered = self::$languages;
        usort(self::$languages_ordered, function ($a, $b) {
            return $a['order'] - $b['order'];
        });

    }

    public static function get($name = null) {

        if(!$name) {
            $name = self::$name;

            /* Check if we already processed the language file */
            if(isset(self::$languages[$name]['content'])) {
                return self::$languages[$name]['content'];
            }
        }

        /* Make sure we have access to the requested language */
        if(!array_key_exists($name, self::$languages)) {
            /* Try and use the default one if available */
            if(array_key_exists(self::$default_name, self::$languages)) {
                $name = self::$default_name;
            }
            /* Try and use the main one if available */
            if(array_key_exists(self::$main_name, self::$languages)) {
                $name = self::$main_name;
            } else {
                die('The language system is missing the current selected language, the default language, and the main language. Fallback is not successful.');
            }
        }

        /* Check if we already processed the language file */
        if(isset(self::$languages[$name]['content'])) {
            return self::$languages[$name]['content'];
        }

        /* Caching system */
        if(\Altum\Router::$path !== 'admin' && ALTUMCODE == 66) {
            /* Try to access the cached file */
            if(file_exists(self::$path . 'cache/' . $name . '#' . self::$languages[$name]['code'] . '.php')) {
                self::$languages[$name]['content'] = require self::$path . 'cache/' . $name . '#' . self::$languages[$name]['code'] . '.php';
            }

            /* We need to generate the caching */
            else {
                /* Include the language file */
                if(file_exists(self::$path . $name . '#' . self::$languages[$name]['code'] . '.php')) {
                    self::$languages[$name]['content'] = require self::$path . $name . '#' . self::$languages[$name]['code'] . '.php';

                    /* Only generate the caching if permissions allow */
                    if(is_writable(self::$path . 'cache/')) {
                        /* Run processing hook */
                        $prefixes_to_skip = \Altum\CustomHooks::generate_language_prefixes_to_skip();
                        self::$languages[$name]['content'] = self::generate_cached_language_file(self::$languages[$name], $prefixes_to_skip);
                    }
                }
            }

        }

        /* Include the original file if we are in the admin panel */
        else {
            /* Include the language file */
            if(file_exists(self::$path . $name . '#' . self::$languages[$name]['code'] . '.php')) {
                self::$languages[$name]['content'] = require self::$path . $name . '#' . self::$languages[$name]['code'] . '.php';
            }
        }

        /* Check the language file */
        if(is_null(self::$languages[$name]['content'])) {
            die('language.corrupted=Restore the original language file.');
        }

        /* Include the admin language file if needed */
        if(\Altum\Router::$path == 'admin') {
            if(file_exists(self::$path . 'admin/' . $name . '#' . self::$languages[$name]['code'] . '.php')) {
                $admin_language = require self::$path . 'admin/' . $name . '#' . self::$languages[$name]['code'] . '.php';
            }

            // FAILSAFE HERE

            /* Merge */
            self::$languages[$name]['content'] = self::$languages[$name]['content'] + $admin_language;
        }

        return self::$languages[$name]['content'];
    }

    public static function set_by_name($name) {

        if(array_key_exists($name, self::$languages)) {
            self::$name = self::$languages[$name]['name'];
            self::$code = self::$languages[$name]['code'];
            self::$status = self::$languages[$name]['status'];
        }

    }

    public static function set_by_code($code) {

        if($name = array_search($code, self::$active_languages)) {
            self::$name = self::$languages[$name]['name'];
            self::$code = self::$languages[$name]['code'];
            self::$status = self::$languages[$name]['status'];
        }

    }

    public static function set_default_by_name($name) {
        if(isset(self::$languages[$name])) {
            self::$default_name = self::$languages[$name]['name'];
            self::$default_code = self::$languages[$name]['code'];
        } else {
            self::$default_name = self::$languages[self::$main_name]['name'];
            self::$default_code = self::$languages[self::$main_name]['code'];
        }

        if(!isset(self::$name)) {
            self::$name = self::$languages[self::$default_name]['name'];
            self::$code = self::$languages[self::$default_name]['code'];
        }

    }

    public static function clear_cache(){
        if(ALTUMCODE != 66) return;

        /* Determine all the languages available in the directory */
        foreach(glob(self::$path . 'cache/*.php') as $file_path) {
            unlink($file_path);
        }

    }

    public static function generate_cached_language_file($language, $prefixes_to_skip) {
        /* Non skip able even if asked for */
        $keys_to_not_skip = [
            'index.breadcrumb',
            'index.menu'
        ];
        
        /* New language strings */
        $language_strings = '';

        /* Go through the language content */
        foreach($language['content'] as $key => $value) {

            /* Remove translations that are not used */
            if(!in_array($key, $keys_to_not_skip)) {
                foreach ($prefixes_to_skip as $prefix) {
                    if(string_starts_with($prefix, $key)) {
                        unset($language['content'][$key]);
                        continue 2;
                    }
                }
            }

            /* Add the language string */
            $value = addcslashes($value, "'");
            $language_strings .= "\t'{$key}' => '{$value}',\n";
        }

        /* Prepare new strings for saving */
        $language_content = function($language_strings) {
            return <<<ALTUM
<?php

return [
{$language_strings}
];
ALTUM;
        };

        /* Save / update file */
        file_put_contents(Language::$path . 'cache/' . $language['name'] . '#' . $language['code'] . '.php', $language_content($language_strings));
        chmod(Language::$path . 'cache/' . $language['name'] . '#' . $language['code'] . '.php', 0777);

        return $language['content'];
    }
}
