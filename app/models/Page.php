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

use Altum\Language;

defined('ALTUMCODE') || die();

class Page extends Model {

    public function get_pages($position) {

        $pages_data = [];

        $cache_instance = cache()->getItem('pages_all');

        /* Set cache if not existing */
        if(is_null($cache_instance->get())) {
            $result = database()->query('SELECT `url`, `title`, `type`, `open_in_new_tab`, `language`, `icon`, `position`, `plans_ids` FROM `pages` WHERE `is_published` = 1 ORDER BY `order`');

            while($row = $result->fetch_object()) {
                $row->plans_ids = json_decode($row->plans_ids ?? '');

                $pages_data[] = $row;
            }

            cache()->save($cache_instance->set($pages_data)->expiresAfter(CACHE_DEFAULT_SECONDS)->addTag('pages'));

        } else {

            /* Get cache */
            $pages_data = $cache_instance->get();

        }

        $filtered_pages = [];

        foreach($pages_data as $page) {

            /* Only keep pages that match the requested position */
            if($page->position != $position) {
                continue;
            }

            /* Make sure the language of the page still exists */
            if($page->language && !isset(\Altum\Language::$active_languages[$page->language])) {
                continue;
            }

            if($page->type == 'internal') {
                $page->target = '_self';
                $page->url = SITE_URL . ($page->language ? \Altum\Language::$active_languages[$page->language] . '/' : null) . 'page/' . $page->url;
            } else {
                $page->target = $page->open_in_new_tab ? '_blank' : '_self';
            }

            /* Check language */
            if($page->language && $page->language != Language::$name) {
                continue;
            }

            /* Filter by plan if needed */
            if(!empty($page->plans_ids)) {
                if(!is_logged_in()) continue;

                if(!in_array(user()->plan_id, $page->plans_ids)) {
                    continue;
                }
            }

            $filtered_pages[] = $page;
        }

        return $filtered_pages;
    }

}
