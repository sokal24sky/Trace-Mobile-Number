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

class AdminBlogPostsCategories extends Controller {

    public function index() {

        /* Prepare the filtering system */
        $filters = (new \Altum\Filters([], ['title', 'url'], ['blog_posts_category_id', 'datetime', 'last_datetime']));
        $filters->set_default_order_by('blog_posts_category_id', $this->user->preferences->default_order_type ?? settings()->main->default_order_type);
        $filters->set_default_results_per_page($this->user->preferences->default_results_per_page ?? settings()->main->default_results_per_page);

        /* Prepare the paginator */
        $total_rows = database()->query("SELECT COUNT(*) AS `total` FROM `blog_posts_categories` WHERE 1 = 1 {$filters->get_sql_where()}")->fetch_object()->total ?? 0;
        $paginator = (new \Altum\Paginator($total_rows, $filters->get_results_per_page(), $_GET['page'] ?? 1, url('admin/blog-posts-categories?' . $filters->get_get() . '&page=%d')));

        /* Get the data */
        $blog_posts_categories = [];
        $blog_posts_categories_result = database()->query("
            SELECT
                *
            FROM
                `blog_posts_categories`
            WHERE
                1 = 1
                {$filters->get_sql_where()}
                {$filters->get_sql_order_by()}
                  
            {$paginator->get_sql_limit()}
        ");
        while($row = $blog_posts_categories_result->fetch_object()) {
            $blog_posts_categories[] = $row;
        }

        /* Prepare the pagination view */
        $pagination = (new \Altum\View('partials/admin_pagination', (array) $this))->run(['paginator' => $paginator]);

        /* Main View */
        $data = [
            'blog_posts_categories' => $blog_posts_categories,
            'paginator' => $paginator,
            'pagination' => $pagination,
            'filters' => $filters,
        ];

        $view = new \Altum\View('admin/blog-posts-categories/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function bulk() {

        /* Check for any errors */
        if(empty($_POST)) {
            redirect('admin/blog-posts-categories');
        }

        if(empty($_POST['selected'])) {
            redirect('admin/blog-posts-categories');
        }

        if(!isset($_POST['type'])) {
            redirect('admin/blog-posts-categories');
        }

        //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

        if(!\Altum\Csrf::check()) {
            Alerts::add_error(l('global.error_message.invalid_csrf_token'));
        }

        if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

            set_time_limit(0);

            session_write_close();

            switch($_POST['type']) {
                case 'delete':

                    foreach($_POST['selected'] as $id) {
                        db()->where('blog_posts_category_id', $id)->delete('blog_posts_categories');
                    }

                    /* Clear the cache */
                    cache()->deleteItemsByTag('blog_posts_categories');

                    break;
            }

            session_start();

            /* Set a nice success message */
            Alerts::add_success(l('bulk_delete_modal.success_message'));

        }

        redirect('admin/blog-posts-categories');
    }

    public function delete() {

        $blog_posts_category_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

        if(!\Altum\Csrf::check('global_token')) {
            Alerts::add_error(l('global.error_message.invalid_csrf_token'));
        }

        if(!$blog_posts_category = db()->where('blog_posts_category_id', $blog_posts_category_id)->getOne('blog_posts_categories', ['blog_posts_category_id', 'title'])) {
            redirect('admin/blog-posts-categories');
        }

        if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

            /* Delete the resource */
            db()->where('blog_posts_category_id', $blog_posts_category_id)->delete('blog_posts_categories');

            /* Clear the cache */
            cache()->deleteItemsByTag('blog_posts_categories');

            /* Set a nice success message */
            Alerts::add_success(sprintf(l('global.success_message.delete1'), '<strong>' . $blog_posts_category->title . '</strong>'));

        }

        redirect('admin/blog-posts-categories');
    }

}
