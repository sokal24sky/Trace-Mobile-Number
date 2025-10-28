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

defined('ALTUMCODE') || die();

class AiQrCodes extends Controller {

    public function index() {

        \Altum\Authentication::guard();

        if(!settings()->codes->ai_qr_codes_is_enabled) {
            redirect('not-found');
        }

        /* Prepare the filtering system */
        $filters = (new \Altum\Filters(['project_id'], ['name'], ['ai_qr_code_id', 'last_datetime', 'name', 'datetime']));
        $filters->set_default_order_by($this->user->preferences->ai_qr_codes_default_order_by, $this->user->preferences->default_order_type ?? settings()->main->default_order_type);
        $filters->set_default_results_per_page($this->user->preferences->default_results_per_page ?? settings()->main->default_results_per_page);

        /* Prepare the paginator */
        $total_rows = database()->query("SELECT COUNT(*) AS `total` FROM `ai_qr_codes` WHERE `user_id` = {$this->user->user_id} {$filters->get_sql_where()}")->fetch_object()->total ?? 0;
        $paginator = (new \Altum\Paginator($total_rows, $filters->get_results_per_page(), $_GET['page'] ?? 1, url('ai-qr-codes?' . $filters->get_get() . '&page=%d')));

        /* Get the ai_qr_codes list for the user */
        $ai_qr_codes = [];
        $ai_qr_codes_result = database()->query("SELECT * FROM `ai_qr_codes` WHERE `user_id` = {$this->user->user_id} {$filters->get_sql_where()} {$filters->get_sql_order_by()} {$paginator->get_sql_limit()}");
        while($row = $ai_qr_codes_result->fetch_object()) {
            $row->settings = json_decode($row->settings ?? '');
            $row->ai_qr_code_url = $row->ai_qr_code ?\Altum\Uploads::get_full_url('ai_qr_codes') . $row->ai_qr_code : null;
            $ai_qr_codes[] = $row;
        }

        /* Export handler */
        process_export_csv($ai_qr_codes, ['ai_qr_code_id', 'user_id', 'project_id', 'name', 'content', 'prompt', 'ai_qr_code', 'ai_qr_code_url', 'last_datetime', 'datetime'], sprintf(l('ai_qr_codes.title')));
        process_export_json($ai_qr_codes, ['ai_qr_code_id', 'user_id', 'project_id', 'name', 'content', 'prompt', 'ai_qr_code', 'ai_qr_code_url', 'embedded_data', 'settings','last_datetime', 'datetime'], sprintf(l('ai_qr_codes.title')));

        /* Prepare the pagination view */
        $pagination = (new \Altum\View('partials/pagination', (array) $this))->run(['paginator' => $paginator]);

        /* Existing projects */
        $projects = (new \Altum\Models\Projects())->get_projects_by_user_id($this->user->user_id);

        /* Available */
        $ai_qr_codes_current_month = db()->where('user_id', $this->user->user_id)->getValue('users', '`qrcode_ai_qr_codes_current_month`');

        /* Prepare the view */
        $data = [
            'ai_qr_codes_current_month' => $ai_qr_codes_current_month,
            'ai_qr_codes'               => $ai_qr_codes,
            'total_ai_qr_codes'         => $total_rows,
            'pagination'                => $pagination,
            'filters'                   => $filters,
            'projects'                  => $projects,
        ];

        $view = new \Altum\View('ai-qr-codes/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function duplicate() {
        \Altum\Authentication::guard();

        if(!settings()->codes->ai_qr_codes_is_enabled) {
            redirect('not-found');
        }

        /* Team checks */
        if(\Altum\Teams::is_delegated() && !\Altum\Teams::has_access('create.ai_qr_codes')) {
            Alerts::add_info(l('global.info_message.team_no_access'));
            redirect('ai-qr-codes');
        }

        if(empty($_POST)) {
            redirect('ai-qr-codes');
        }

        /* Make sure that the user didn't exceed the limit */
        $ai_qr_codes_current_month = db()->where('user_id', $this->user->user_id)->getValue('users', '`qrcode_ai_qr_codes_current_month`');

        if($this->user->plan_settings->ai_qr_codes_per_month_limit != -1 && $ai_qr_codes_current_month >= $this->user->plan_settings->ai_qr_codes_per_month_limit) {
            Alerts::add_info(l('global.info_message.plan_feature_limit'));
            redirect('ai-qr-codes');
        }

        $ai_qr_code_id = (int) $_POST['ai_qr_code_id'];

        //ALTUMCODE:DEMO if(DEMO) if($this->user->user_id == 1) Alerts::add_error('Please create an account on the demo to test out this function.');
        if(!\Altum\Csrf::check()) {
            Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            redirect('ai-qr-codes');
        }

        /* Verify the main resource */
        if(!$ai_qr_code = db()->where('ai_qr_code_id', $ai_qr_code_id)->where('user_id', $this->user->user_id)->getOne('ai_qr_codes')) {
            redirect('ai-qr-codes');
        }

        if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

            /* Duplicate the files */
            $ai_qr_code_image = \Altum\Uploads::copy_uploaded_file($ai_qr_code->ai_qr_code, \Altum\Uploads::get_path('ai_qr_codes'), \Altum\Uploads::get_path('ai_qr_codes'));

            /* Insert to database */
            $ai_qr_code_id = db()->insert('ai_qr_codes', [
                'user_id' => $this->user->user_id,
                'project_id' => $ai_qr_code->project_id,
                'name' => string_truncate($ai_qr_code->name . ' - ' . l('global.duplicated'), 64, null),
                'content' => $ai_qr_code->content,
                'prompt' => $ai_qr_code->prompt,
                'ai_qr_code' => $ai_qr_code_image,
                'settings' => $ai_qr_code->settings,
                'embedded_data' => $ai_qr_code->embedded_data,
                'datetime' => get_date(),
            ]);

            /* Clear the cache */
            cache()->deleteItem('ai_qr_codes_total?user_id=' . $this->user->user_id);
            cache()->deleteItem('ai_qr_codes_dashboard?user_id=' . $this->user->user_id);

            /* Set a nice success message */
            Alerts::add_success(sprintf(l('global.success_message.create1'), '<strong>' . input_clean($ai_qr_code->name) . '</strong>'));

            /* Redirect */
            redirect('ai-qr-code-update/' . $ai_qr_code_id);

        }

        redirect('ai-qr-codes');
    }

    public function bulk() {

        \Altum\Authentication::guard();

        //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

        /* Check for any errors */
        if(empty($_POST)) {
            redirect('ai-qr-codes');
        }

        if(empty($_POST['selected'])) {
            redirect('ai-qr-codes');
        }

        if(!isset($_POST['type'])) {
            redirect('ai-qr-codes');
        }

        if(!\Altum\Csrf::check()) {
            Alerts::add_error(l('global.error_message.invalid_csrf_token'));
        }

        if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

            set_time_limit(0);

            session_write_close();

            switch($_POST['type']) {
                case 'delete':

                    /* Team checks */
                    if(\Altum\Teams::is_delegated() && !\Altum\Teams::has_access('delete.ai_qr_codes')) {
                        Alerts::add_info(l('global.info_message.team_no_access'));
                        redirect('ai-qr-codes');
                    }

                    foreach($_POST['selected'] as $qr_code_id) {
                        if($ai_qr_code = db()->where('ai_qr_code_id', $ai_qr_code_id)->where('user_id', $this->user->user_id)->getOne('ai_qr_codes', ['ai_qr_code'])) {
                            /* Delete the ai_qr_code */
                            (new AiQrCode())->delete($ai_qr_code_id);
                        }
                    }

                    break;

                case 'download':

                    $files = [];

                    foreach($_POST['selected'] as $ai_qr_code_id) {
                        if($ai_qr_code = db()->where('ai_qr_code_id', $ai_qr_code_id)->where('user_id', $this->user->user_id)->getOne('ai_qr_codes', ['ai_qr_code'])) {
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

        redirect('ai-qr-codes');
    }

    public function delete() {
        \Altum\Authentication::guard();

        if(!settings()->codes->ai_qr_codes_is_enabled) {
            redirect('not-found');
        }

        /* Team checks */
        if(\Altum\Teams::is_delegated() && !\Altum\Teams::has_access('delete.ai_qr_codes')) {
            Alerts::add_info(l('global.info_message.team_no_access'));
            redirect('ai-qr-codes');
        }

        if(empty($_POST)) {
            redirect('ai-qr-codes');
        }

        $ai_qr_code_id = (int) query_clean($_POST['ai_qr_code_id']);

        //ALTUMCODE:DEMO if(DEMO) if($this->user->user_id == 1) Alerts::add_error('Please create an account on the demo to test out this function.');

        if(!\Altum\Csrf::check()) {
            Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            redirect('ai-qr-codes');
        }

        /* Make sure the vcard id is created by the logged in user */
        if(!$ai_qr_code = db()->where('ai_qr_code_id', $ai_qr_code_id)->where('user_id', $this->user->user_id)->getOne('ai_qr_codes', ['ai_qr_code_id', 'name'])) {
            redirect('ai-qr-codes');
        }

        if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

            (new AiQrCode())->delete($ai_qr_code->ai_qr_code_id);

            /* Set a nice success message */
            Alerts::add_success(sprintf(l('global.success_message.delete1'), '<strong>' . $ai_qr_code->name . '</strong>'));

            redirect('ai-qr-codes');

        }

        redirect('ai-qr-codes');
    }

}
