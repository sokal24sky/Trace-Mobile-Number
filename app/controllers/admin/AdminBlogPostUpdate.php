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

use Altum\Alerts;

defined('ALTUMCODE') || die();

class AdminBlogPostUpdate extends Controller {

    public function index() {

        $blog_post_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        /* Check if resource exists */
        if(!$blog_post = db()->where('blog_post_id', $blog_post_id)->getOne('blog_posts')) {
            redirect('admin/blog-posts');
        }

        if(!empty($_POST)) {
            /* Filter some of the variables */
            $_POST['url'] = input_clean(get_slug($_POST['url']), 256);
            $_POST['title'] = input_clean($_POST['title'], 256);
            $_POST['description'] = input_clean($_POST['description'], 256);
            $_POST['image_description'] = input_clean($_POST['image_description'], 256);
            $_POST['keywords'] = input_clean($_POST['keywords'], 256);
            $_POST['editor'] = in_array($_POST['editor'], ['wysiwyg', 'blocks', 'raw']) ? input_clean($_POST['editor']) : 'raw';
            $_POST['blog_posts_category_id'] = empty($_POST['blog_posts_category_id']) ? null : (int) $_POST['blog_posts_category_id'];
            $_POST['language'] = !empty($_POST['language']) ? input_clean($_POST['language']) : null;
            $_POST['is_published'] = (int) isset($_POST['is_published']);
            $_POST['content'] = $_POST['editor'] == 'wysiwyg' ? quilljs_to_bootstrap($_POST['content']) : $_POST['content'];

            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* Check for any errors */
            $required_fields = ['title', 'url'];
            foreach($required_fields as $field) {
                if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                    Alerts::add_field_error($field, l('global.error_message.empty_field'));
                }
            }

            if(!\Altum\Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            if(db()->where('blog_post_id', $blog_post->blog_post_id, '<>')->where('url', $_POST['url'])->where('language', $_POST['language'])->has('blog_posts')) {
                Alerts::add_field_error('url', l('admin_blog.error_message.url_exists'));
            }

            $blog_post->image = \Altum\Uploads::process_upload($blog_post->image, 'blog', 'image', 'image_remove', null);

            /* If there are no errors, continue */
            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

                /* Database query */
                db()->where('blog_post_id', $blog_post->blog_post_id)->update('blog_posts', [
                    'blog_posts_category_id' => $_POST['blog_posts_category_id'],
                    'url' => $_POST['url'],
                    'title' => $_POST['title'],
                    'description' => $_POST['description'],
                    'keywords' => $_POST['keywords'],
                    'image' => $blog_post->image,
                    'image_description' => $_POST['image_description'],
                    'editor' => $_POST['editor'],
                    'content' => $_POST['content'],
                    'language' => $_POST['language'],
                    'is_published' => $_POST['is_published'],
                    'last_datetime' => get_date(),
                ]);

                /* Set a nice success message */
                Alerts::add_success(sprintf(l('global.success_message.update1'), '<strong>' . $_POST['title'] . '</strong>'));

                /* Clear the cache */
                cache()->deleteItemsByTag('blog_posts');

                redirect('admin/blog-post-update/' . $blog_post_id);
            }
        }

        /* Get the blog posts categories available */
        $blog_posts_categories = db()->get('blog_posts_categories', null, ['blog_posts_category_id', 'title']);

        /* Main View */
        $data = [
            'blog_posts_categories' => $blog_posts_categories,
            'blog_post' => $blog_post,
        ];

        $view = new \Altum\View('admin/blog-post-update/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
