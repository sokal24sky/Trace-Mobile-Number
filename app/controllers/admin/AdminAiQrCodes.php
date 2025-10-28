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
use Altum\Models\AiQrCode;
use Altum\Models\QrCode;

defined('ALTUMCODE') || die();

class AdminAiQrCodes extends Controller {

    public function index() {

        /* Prepare the filtering system */
        $filters = (new \Altum\Filters(['user_id', 'project_id'], ['name'], ['ai_qr_code_id', 'last_datetime', 'name', 'datetime']));
        $filters->set_default_order_by($this->user->preferences->ai_qr_codes_default_order_by, $this->user->preferences->default_order_type ?? settings()->main->default_order_type);
        $filters->set_default_results_per_page($this->user->preferences->default_results_per_page ?? settings()->main->default_results_per_page);

        /* Prepare the paginator */
        $total_rows = database()->query("SELECT COUNT(*) AS `total` FROM `ai_qr_codes` WHERE 1 = 1 {$filters->get_sql_where()}")->fetch_object()->total ?? 0;
        $paginator = (new \Altum\Paginator($total_rows, $filters->get_results_per_page(), $_GET['page'] ?? 1, url('admin/qr-codes?' . $filters->get_get() . '&page=%d')));

        /* Get the data */
        $ai_qr_codes = [];
        $ai_qr_codes_result = database()->query("
            SELECT
                `ai_qr_codes`.*, `users`.`name` AS `user_name`, `users`.`email` AS `user_email`, `users`.`avatar` AS `user_avatar`
            FROM
                `ai_qr_codes`
            LEFT JOIN
                `users` ON `ai_qr_codes`.`user_id` = `users`.`user_id`
            WHERE
                1 = 1
                {$filters->get_sql_where('ai_qr_codes')}
                {$filters->get_sql_order_by('ai_qr_codes')}

            {$paginator->get_sql_limit()}
        ");
        while($row = $ai_qr_codes_result->fetch_object()) {
            $row->settings = json_decode($row->settings ?? '');
            $row->ai_qr_code_url = $row->ai_qr_code ?\Altum\Uploads::get_full_url('ai_qr_codes') . $row->ai_qr_code : null;
            $ai_qr_codes[] = $row;
        }

        /* Export handler */
        process_export_csv($ai_qr_codes, ['ai_qr_code_id', 'user_id', 'project_id', 'name', 'content', 'prompt', 'ai_qr_code', 'ai_qr_code_url', 'last_datetime', 'datetime'], sprintf(l('ai_qr_codes.title')));
        process_export_json($ai_qr_codes, ['ai_qr_code_id', 'user_id', 'project_id', 'name', 'content', 'prompt', 'ai_qr_code', 'ai_qr_code_url', 'embedded_data', 'settings','last_datetime', 'datetime'], sprintf(l('ai_qr_codes.title')));

        /* Prepare the pagination view */
        $pagination = (new \Altum\View('partials/admin_pagination', (array) $this))->run(['paginator' => $paginator]);

        /* Main View */
        $data = [
            'ai_qr_codes' => $ai_qr_codes,
            'filters' => $filters,
            'pagination' => $pagination,
        ];

        $view = new \Altum\View('admin/ai-qr-codes/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function bulk() {

        //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

        /* Check for any errors */
        if(empty($_POST)) {
            redirect('admin/ai-qr-codes');
        }

        if(empty($_POST['selected'])) {
            redirect('admin/ai-qr-codes');
        }

        if(!isset($_POST['type'])) {
            redirect('admin/ai-qr-codes');
        }

        if(!\Altum\Csrf::check()) {
            Alerts::add_error(l('global.error_message.invalid_csrf_token'));
        }

        if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

            set_time_limit(0);

            session_write_close();

            switch($_POST['type']) {
                case 'delete':

                    foreach($_POST['selected'] as $ai_qr_code_id) {
                        /* Delete the ai_qr_code */
                        (new AiQrCode())->delete($ai_qr_code_id);
                    }

                    break;

                case 'download':

                    $files = [];

                    foreach($_POST['selected'] as $ai_qr_code_id) {
                        if($ai_qr_code = db()->where('ai_qr_code_id', $ai_qr_code_id)->getOne('ai_qr_codes', ['ai_qr_code'])) {
                            $files[$ai_qr_code->ai_qr_code] = \Altum\Uploads::get_path('ai_qr_codes');
                        }
                    }

                    \Altum\Uploads::download_files_as_zip($files, l('global.download'));

                    break;
            }

            session_start();

            /* Set a nice success message */
            Alerts::add_success(l('bulk_delete_modal.success_message'));

        }

        redirect('admin/ai-qr-codes');
    }

    public function delete() {

        $ai_qr_code_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

        if(!\Altum\Csrf::check('global_token')) {
            Alerts::add_error(l('global.error_message.invalid_csrf_token'));
        }

        if(!$ai_qr_code = db()->where('ai_qr_code_id', $ai_qr_code_id)->getOne('ai_qr_codes', ['ai_qr_code_id', 'name'])) {
            redirect('admin/ai-qr-codes');
        }

        if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

            /* Delete the ai_qr_code */
            (new QrCode())->delete($ai_qr_code->ai_qr_code_id);

            /* Set a nice success message */
            Alerts::add_success(sprintf(l('global.success_message.delete1'), '<strong>' . $ai_qr_code->name . '</strong>'));

        }

        redirect('admin/ai-qr-codes');
    }

    public function transfer() {

        if(empty($_POST)) {
            redirect('admin/ai-qr-codes');
        }

        $ai_qr_code_id = (int) $_POST['ai_qr_code_id'];
        $_POST['email'] = input_clean_email($_POST['email'] ?? '');

        //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

        if(!\Altum\Csrf::check()) {
            Alerts::add_error(l('global.error_message.invalid_csrf_token'));
        }

        if(!$ai_qr_code = db()->where('ai_qr_code_id', $ai_qr_code_id)->getOne('ai_qr_codes', ['ai_qr_code_id', 'user_id', 'name'])) {
            redirect('admin/ai-qr-codes');
        }

        if(!$current_user = db()->where('user_id', $ai_qr_code->user_id)->getOne('users', ['user_id', 'email'])) {
            redirect('admin/ai-qr-codes');
        }

        if(!$new_user = db()->where('email', $_POST['email'])->getOne('users', ['user_id', 'email'])) {
            redirect('admin/ai-qr-codes');
        }

        if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

            /* Update the database */
            db()->where('ai_qr_code_id', $ai_qr_code->ai_qr_code_id)->update('ai_qr_codes', [
                'user_id' => $new_user->user_id,
            ]);

            /* Set a nice success message */
            Alerts::add_success(sprintf(l('transfer_modal.success_message'), '<strong>' . input_clean($ai_qr_code->name) . '</strong>', '<strong>' . input_clean($current_user->email) . '</strong>', '<strong>' . input_clean($new_user->email) . '</strong>'));

            /* Clear the cache */
            cache()->deleteItemsByTag('user_id=' . $current_user->user_id);
            cache()->deleteItemsByTag('user_id=' . $new_user->user_id);

            /* Redirect */
            redirect('admin/ai-qr-codes');

        }

        redirect('admin/ai-qr-codes');
    }

}
