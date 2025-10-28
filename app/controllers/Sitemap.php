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

defined('ALTUMCODE') || die();

class Sitemap extends Controller {

    public function index() {

        /* Set the header as xml so the browser can read it properly */
        header('Content-Type: text/xml');

        $view = new \Altum\View('sitemap/sitemap_index', (array) $this);

        echo $view->run();

    }

    public function main() {
        /* Set the header as xml so the browser can read it properly */
        header('Content-Type: text/xml');

        $sitemap_urls = [
            '',
            'login',
            'lost-password',
        ];
        
        if(settings()->users->email_confirmation) {
            $sitemap_urls[] = 'resend-activation';
        }

        if(settings()->users->register_is_enabled) {
            $sitemap_urls[] = 'register';
        }

        if(\Altum\Plugin::is_active('affiliate') && settings()->affiliate->is_enabled) {
            $sitemap_urls[] = 'affiliate';
        }

        if(settings()->main->api_is_enabled) {
            $sitemap_urls[] = 'api-documentation';
        }

        if(settings()->email_notifications->contact && !empty(settings()->email_notifications->emails)) {
            $sitemap_urls[] = 'contact';
        }

        if(settings()->payment->is_enabled) {
            $sitemap_urls[] = 'plan';
        }

        if(settings()->content->pages_is_enabled) {
            $sitemap_urls[] = 'pages';
        }

        if(settings()->content->blog_is_enabled) {
            $sitemap_urls[] = 'blog';
        }

        /* Product specific */
        if(settings()->codes->qr_codes_is_enabled) {
            $sitemap_urls[] = 'qr';
        }

        if(settings()->codes->qr_reader_is_enabled) {
            $sitemap_urls[] = 'qr-reader';
        }

        if(settings()->codes->barcodes_is_enabled) {
            $sitemap_urls[] = 'barcode';
        }

        if(settings()->codes->barcode_reader_is_enabled) {
            $sitemap_urls[] = 'barcode-reader';
        }

        /* Multilingual */
        $new_sitemap_urls = [];

        foreach(\Altum\Language::$active_languages as $language_name => $language_code) {
            foreach($sitemap_urls as $url) {
                $new_sitemap_urls[] = settings()->main->default_language == $language_name ? SITE_URL . $url : SITE_URL . $language_code . '/' . $url;
            }
        }

        if(settings()->content->pages_is_enabled) {
            $pages = db()->where('type', 'internal')->where('is_published', 1)->get('pages', null, ['url', 'language']);
            $pages_categories = db()->get('pages_categories', null, ['url', 'language']);

            foreach ($pages as $page) {
                $new_sitemap_urls[] = SITE_URL . ($page->language ? \Altum\Language::$active_languages[$page->language] . '/' : '') . 'page/' . $page->url;
            }

            foreach ($pages_categories as $pages_category) {
                $new_sitemap_urls[] = SITE_URL . ($pages_category->language ? \Altum\Language::$active_languages[$pages_category->language] . '/' : '') . 'pages/' . $pages_category->url;
            }
        }

        if(settings()->content->blog_is_enabled) {
            $blog_posts = db()->where('is_published', 1)->get('blog_posts', null, ['url', 'language']);
            $blog_posts_categories = db()->get('blog_posts_categories', null, ['url', 'language']);

            foreach ($blog_posts as $blog_post) {
                $new_sitemap_urls[] = SITE_URL . ($blog_post->language ? \Altum\Language::$active_languages[$blog_post->language] . '/' : '') . 'blog/' . $blog_post->url;
            }

            foreach ($blog_posts_categories as $blog_posts_category) {
                $new_sitemap_urls[] = SITE_URL . ($blog_posts_category->language ? \Altum\Language::$active_languages[$blog_posts_category->language] . '/' : '') . 'blog/category/' . $blog_posts_category->url;
            }
        }


        /* Main View */
        $data = [
            'sitemap_urls' => $new_sitemap_urls,
        ];

        $view = new \Altum\View('sitemap/sitemap_main', (array) $this);

        echo $view->run($data);

    }

}
