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

class AdminPages extends Controller {

    public function index() {

        /* Prepare the filtering system */
        $filters = (new \Altum\Filters(['is_published', 'pages_category_id'], ['title', 'url', 'description', 'keywords'], ['page_id', 'datetime', 'last_datetime']));
        $filters->set_default_order_by('page_id', $this->user->preferences->default_order_type ?? settings()->main->default_order_type);
        $filters->set_default_results_per_page($this->user->preferences->default_results_per_page ?? settings()->main->default_results_per_page);

        /* Prepare the paginator */
        $total_rows = database()->query("SELECT COUNT(*) AS `total` FROM `pages` WHERE 1 = 1 {$filters->get_sql_where()}")->fetch_object()->total ?? 0;
        $paginator = (new \Altum\Paginator($total_rows, $filters->get_results_per_page(), $_GET['page'] ?? 1, url('admin/pages?' . $filters->get_get() . '&page=%d')));

        /* Get the data */
        $pages = [];
        $pages_result = database()->query("
            SELECT
                *
            FROM
                `pages`
            WHERE
                1 = 1
                {$filters->get_sql_where()}
                {$filters->get_sql_order_by()}
                  
            {$paginator->get_sql_limit()}
        ");
        while($row = $pages_result->fetch_object()) {
            $pages[] = $row;
        }

        /* Prepare the pagination view */
        $pagination = (new \Altum\View('partials/admin_pagination', (array) $this))->run(['paginator' => $paginator]);

        /* Get all pages categories */
        $pages_categories = [];
        $pages_categories_result = database()->query("SELECT `pages_category_id`, `title` FROM `pages_categories`");
        while($row = $pages_categories_result->fetch_object()) {
            $pages_categories[$row->pages_category_id] = $row;
        }

        /* Main View */
        $data = [
            'pages' => $pages,
            'pages_categories' => $pages_categories,
            'paginator' => $paginator,
            'pagination' => $pagination,
            'filters' => $filters,
        ];

        $view = new \Altum\View('admin/pages/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function bulk() {

        /* Check for any errors */
        if(empty($_POST)) {
            redirect('admin/pages');
        }

        if(empty($_POST['selected'])) {
            redirect('admin/pages');
        }

        if(!isset($_POST['type'])) {
            redirect('admin/pages');
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
                        db()->where('page_id', $id)->delete('pages');
                    }

                    break;
            }

            /* Clear the cache */
            cache()->deleteItems(['pages_top', 'pages_bottom', 'pages_hidden']);
            cache()->deleteItemsByTag('pages');

            session_start();

            /* Set a nice success message */
            Alerts::add_success(l('bulk_delete_modal.success_message'));

        }

        redirect('admin/pages');
    }

    public function delete() {

        $page_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

        if(!\Altum\Csrf::check('global_token')) {
            Alerts::add_error(l('global.error_message.invalid_csrf_token'));
        }

        if(!$page = db()->where('page_id', $page_id)->getOne('pages', ['page_id', 'title'])) {
            redirect('admin/pages');
        }

        if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

            /* Delete the page */
            db()->where('page_id', $page_id)->delete('pages');

            /* Clear the cache */
            cache()->deleteItems(['pages_top', 'pages_bottom', 'pages_hidden']);
            cache()->deleteItemsByTag('pages');

            /* Set a nice success message */
            Alerts::add_success(sprintf(l('global.success_message.delete1'), '<strong>' . $page->title . '</strong>'));

        }

        redirect('admin/pages');
    }

}
