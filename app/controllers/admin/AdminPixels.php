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

class AdminPixels extends Controller {

    public function index() {

        /* Prepare the filtering system */
        $filters = (new \Altum\Filters(['user_id', 'type'], ['name', 'pixel'], ['pixel_id', 'last_datetime', 'name', 'datetime']));
        $filters->set_default_order_by($this->user->preferences->pixels_default_order_by, $this->user->preferences->default_order_type ?? settings()->main->default_order_type);
        $filters->set_default_results_per_page($this->user->preferences->default_results_per_page ?? settings()->main->default_results_per_page);

        /* Prepare the paginator */
        $total_rows = database()->query("SELECT COUNT(*) AS `total` FROM `pixels` WHERE 1 = 1 {$filters->get_sql_where()}")->fetch_object()->total ?? 0;
        $paginator = (new \Altum\Paginator($total_rows, $filters->get_results_per_page(), $_GET['page'] ?? 1, url('admin/pixels?' . $filters->get_get() . '&page=%d')));

        /* Get the data */
        $pixels = [];
        $pixels_result = database()->query("
            SELECT
                `pixels`.*, `users`.`name` AS `user_name`, `users`.`email` AS `user_email`, `users`.`avatar` AS `user_avatar`
            FROM
                `pixels`
            LEFT JOIN
                `users` ON `pixels`.`user_id` = `users`.`user_id`
            WHERE
                1 = 1
                {$filters->get_sql_where('pixels')}
                {$filters->get_sql_order_by('pixels')}

            {$paginator->get_sql_limit()}
        ");
        while($row = $pixels_result->fetch_object()) {
            $pixels[] = $row;
        }

        /* Export handler */
        process_export_csv($pixels, ['pixel_id', 'user_id', 'type', 'name', 'pixel', 'last_datetime', 'datetime'], sprintf(l('admin_pixels.title')));
        process_export_json($pixels, ['pixel_id', 'user_id', 'type', 'name', 'pixel', 'last_datetime', 'datetime'], sprintf(l('admin_pixels.title')));

        /* Prepare the pagination view */
        $pagination = (new \Altum\View('partials/admin_pagination', (array) $this))->run(['paginator' => $paginator]);

        /* Main View */
        $data = [
            'pixels' => $pixels,
            'filters' => $filters,
            'pagination' => $pagination
        ];

        $view = new \Altum\View('admin/pixels/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function bulk() {

        //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

        /* Check for any errors */
        if(empty($_POST)) {
            redirect('admin/pixels');
        }

        if(empty($_POST['selected'])) {
            redirect('admin/pixels');
        }

        if(!isset($_POST['type'])) {
            redirect('admin/pixels');
        }

        if(!\Altum\Csrf::check()) {
            Alerts::add_error(l('global.error_message.invalid_csrf_token'));
        }

        if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

            set_time_limit(0);

            session_write_close();

            switch($_POST['type']) {
                case 'delete':

                    foreach($_POST['selected'] as $pixel_id) {
                        $user_id = db()->where('pixel_id', $pixel_id)->getValue('pixels', 'user_id');

                        /* Delete the pixel */
                        db()->where('pixel_id', $pixel_id)->delete('pixels');

                        /* Clear the cache */
                        cache()->deleteItem('pixels?user_id=' . $user_id);
                        cache()->deleteItem('pixels_total?user_id=' . $user_id);
                    }

                    break;
            }

            session_start();

            /* Set a nice success message */
            Alerts::add_success(l('bulk_delete_modal.success_message'));

        }

        redirect('admin/pixels');
    }

    public function delete() {

        $pixel_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

        if(!\Altum\Csrf::check('global_token')) {
            Alerts::add_error(l('global.error_message.invalid_csrf_token'));
        }

        if(!$pixel = db()->where('pixel_id', $pixel_id)->getOne('pixels', ['pixel_id', 'name'])) {
            redirect('admin/pixels');
        }

        if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

            $user_id = db()->where('pixel_id', $pixel_id)->getValue('pixels', 'user_id');

            /* Delete the pixel */
            db()->where('pixel_id', $pixel->pixel_id)->delete('pixels');

            /* Clear the cache */
            cache()->deleteItem('pixels?user_id=' . $user_id);
            cache()->deleteItem('pixels_total?user_id=' . $user_id);

            /* Set a nice success message */
            Alerts::add_success(sprintf(l('global.success_message.delete1'), '<strong>' . $pixel->name . '</strong>'));

        }

        redirect('admin/pixels');
    }

}
