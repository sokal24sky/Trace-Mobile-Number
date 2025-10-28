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

class BlogPosts extends Model {

    public function get_popular_blog_posts_by_language($language) {

        /* Get the resources */
        $blog_posts = [];

        /* Try to check if the user posts exists via the cache */
        $cache_instance = cache()->getItem('blog_posts?type=popular&language=' . $language);

        /* Set cache if not existing */
        if(is_null($cache_instance->get())) {

            /* Get data from the database */
            $blog_posts_result = database()->query("
                SELECT * 
                FROM `blog_posts`
                WHERE (`language` = '{$language}' OR `language` IS NULL) AND `is_published` = 1
                ORDER BY `total_views` DESC
                LIMIT 5
            ");
            while($row = $blog_posts_result->fetch_object()) $blog_posts[$row->blog_post_id] = $row;

            cache()->save(
                $cache_instance->set($blog_posts)->expiresAfter(CACHE_DEFAULT_SECONDS)->addTag('blog_posts')
            );

        } else {

            /* Get cache */
            $blog_posts = $cache_instance->get();

        }

        return $blog_posts;

    }

    public function delete($blog_post_id) {

        $blog_post = db()->where('blog_post_id', $blog_post_id)->getOne('blog_posts', ['blog_post_id', 'image']);

        if(!$blog_post) return;

        \Altum\Uploads::delete_uploaded_file($blog_post->image, 'blog');

        /* Delete the resource */
        db()->where('blog_post_id', $blog_post_id)->delete('blog_posts');

        /* Clear the cache */
        cache()->deleteItemsByTag('blog_posts');

    }

}
