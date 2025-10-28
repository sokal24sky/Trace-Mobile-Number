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

namespace Altum\Models;

defined('ALTUMCODE') || die();

class Settings extends Model {

    public function get() {

        $cache_instance = cache()->getItem('settings');

        /* Set cache if not existing */
        if(!$cache_instance->get()) {

            $result = database()->query("SELECT * FROM `settings`");
            $data = new \StdClass();

            while($row = $result->fetch_object()) {

                /* Put the value in a variable so we can check if its json or not */
                $value = json_decode($row->value);

                $data->{$row->key} = is_null($value) ? $row->value : $value;

            }

            cache()->save($cache_instance->set($data)->expiresAfter(CACHE_DEFAULT_SECONDS));

        } else {

            /* Get cache */
            $data = $cache_instance->get('settings');

        }

        /* Define some stuff from the database */
        if(!defined('PRODUCT_VERSION')) define('PRODUCT_VERSION', $data->product_info->version);
        if(!defined('PRODUCT_CODE')) define('PRODUCT_CODE', $data->product_info->code);

        /* Set the full url for assets */
        $assets_url = SITE_URL . ASSETS_URL_PATH;
        $uploads_url = SITE_URL . UPLOADS_URL_PATH;

        if(\Altum\Plugin::is_active('offload')) {
            if(!empty($data->offload->assets_url)) {
                $assets_url = $data->offload->assets_url;
            }

            if(!empty($data->offload->uploads_url)) {
                $uploads_url = $data->offload->uploads_url;
            }

            /* CDN */
            if(!empty($data->offload->cdn_assets_url)) {
                $assets_url = $data->offload->cdn_assets_url . ASSETS_URL_PATH;
            }

            if(!empty($data->offload->cdn_uploads_url)) {
                $uploads_url = $data->offload->cdn_uploads_url . UPLOADS_URL_PATH;
            }
        }

        define('ASSETS_FULL_URL', $assets_url);
        define('UPLOADS_FULL_URL', $uploads_url);

        return $data;
    }

}
