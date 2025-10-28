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

class BlogPostsCategories extends Model {

    public function get_blog_posts_categories_by_language($language) {

        /* Get the resources */
        $blog_posts_categories = [];

        /* Try to check if the user posts exists via the cache */
        $cache_instance = cache()->getItem('blog_posts_categories?language=' . $language);

        /* Set cache if not existing */
        if(is_null($cache_instance->get())) {

            /* Get data from the database */
            $blog_posts_categories_result = database()->query("
                SELECT * 
                FROM `blog_posts_categories`
                WHERE `language` = '{$language}' OR `language` IS NULL
                ORDER BY `order` ASC
            ");
            while($row = $blog_posts_categories_result->fetch_object()) $blog_posts_categories[$row->blog_posts_category_id] = $row;

            cache()->save(
                $cache_instance->set($blog_posts_categories)->expiresAfter(CACHE_DEFAULT_SECONDS)->addTag('blog_posts_categories')
            );

        } else {

            /* Get cache */
            $blog_posts_categories = $cache_instance->get();

        }

        return $blog_posts_categories;

    }

}
