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

class AdminImageOptimizer extends Controller {

    public function index() {

        /* Prepare the filtering system */
        $filters = (new \Altum\Filters([], ['path', 'file'], ['image_optimization_id', 'original_size', 'new_size', 'percentage_difference', 'datetime']));
        $filters->set_default_order_by('image_optimization_id', $this->user->preferences->default_order_type ?? settings()->main->default_order_type);
        $filters->set_default_results_per_page($this->user->preferences->default_results_per_page ?? settings()->main->default_results_per_page);

        /* Prepare the paginator */
        $total_rows = database()->query("SELECT COUNT(*) AS `total` FROM `image_optimizations` WHERE 1 = 1 {$filters->get_sql_where()}")->fetch_object()->total ?? 0;
        $paginator = (new \Altum\Paginator($total_rows, $filters->get_results_per_page(), $_GET['page'] ?? 1, url('admin/image-optimizer?' . $filters->get_get() . '&page=%d')));

        /* Get the data */
        $image_optimizations = [];
        $image_optimizations_result = database()->query("
            SELECT
                `image_optimizations`.*
            FROM
                `image_optimizations`
            WHERE
                1 = 1
                {$filters->get_sql_where()}
                {$filters->get_sql_order_by()}

            {$paginator->get_sql_limit()}
        ");
        while($row = $image_optimizations_result->fetch_object()) {
            $image_optimizations[] = $row;
        }

        /* Export handler */
        process_export_json($image_optimizations, ['image_optimization_id', 'original_format', 'original_size', 'new_size', 'percentage_difference', 'file', 'path', 'datetime']);
        process_export_csv($image_optimizations, ['image_optimization_id', 'original_format', 'original_size', 'new_size', 'percentage_difference', 'file', 'path', 'datetime']);

        /* Prepare the pagination view */
        $pagination = (new \Altum\View('partials/admin_pagination', (array) $this))->run(['paginator' => $paginator]);

        /* Main View */
        $data = [
            'image_optimizations' => $image_optimizations,
            'paginator' => $paginator,
            'pagination' => $pagination,
            'filters' => $filters
        ];

        $view = new \Altum\View('admin/image-optimizer/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function bulk() {

        /* Check for any errors */
        if(empty($_POST)) {
            redirect('admin/image-optimizer');
        }

        if(empty($_POST['selected'])) {
            redirect('admin/image-optimizer');
        }

        if(!isset($_POST['type'])) {
            redirect('admin/image-optimizer');
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

                    foreach($_POST['selected'] as $image_optimization_id) {
                        db()->where('image_optimization_id', $image_optimization_id)->delete('image_optimizations');
                    }
                    break;
            }

            session_start();

            /* Set a nice success message */
            Alerts::add_success(l('bulk_delete_modal.success_message'));

        }

        redirect('admin/image-optimizer');
    }

    public function delete() {

        $image_optimization_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

        if(!\Altum\Csrf::check('global_token')) {
            Alerts::add_error(l('global.error_message.invalid_csrf_token'));
        }

        if(!$image_optimization = db()->where('image_optimization_id', $image_optimization_id)->getOne('image_optimizations')) {
            redirect('admin/image-optimizer');
        }

        if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

            /* Delete the user log */
            db()->where('image_optimization_id', $image_optimization_id)->delete('image_optimizations');

            /* Set a nice success message */
            Alerts::add_success(l('global.success_message.delete2'));

        }

        redirect('admin/image-optimizer');
    }

}
