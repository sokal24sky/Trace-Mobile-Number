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

class AdminLogs extends Controller {

    public function index() {
        /* Clear files caches */
        clearstatcache();

        /* Get the data */
        $logs = [];

        foreach(glob(UPLOADS_PATH . 'logs/' . '*.log') as $file_path) {
            $file_path_exploded = explode('/', $file_path);
            $file_name = str_replace('.log', '', trim(end($file_path_exploded)));
            $file_last_modified = filemtime($file_path);

            $logs[$file_last_modified] = (object) [
                'name' => $file_name,
                'full_name' => $file_name . '.log',
                'extension' => 'log',
                'size' => filesize($file_path),
                'last_modified' => date('Y-m-d H:i:s', $file_last_modified),
            ];
        }

        krsort($logs);

        /* Main View */
        $data = [
            'logs' => $logs,
        ];

        $view = new \Altum\View('admin/logs/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function bulk() {

        /* Check for any errors */
        if(empty($_POST)) {
            redirect('admin/logs');
        }

        if(empty($_POST['selected'])) {
            redirect('admin/logs');
        }

        if(!isset($_POST['type'])) {
            redirect('admin/logs');
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
                        $id = preg_replace('/[^a-zA-Z0-9-]/', '', input_clean($id));
                        unlink(UPLOADS_PATH . 'logs/' . $id . '.log');
                    }
                    break;
            }

            session_start();

            /* Set a nice success message */
            Alerts::add_success(l('bulk_delete_modal.success_message'));

        }

        redirect('admin/logs');
    }

    public function delete() {

        $log_id = isset($this->params[0]) ? input_clean($this->params[0]) : null;

        if(!$log_id) {
            redirect('admin/logs');
        }

        $log_id = preg_replace('/[^a-zA-Z0-9-]/', '', $log_id);

        //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

        if(!\Altum\Csrf::check('global_token')) {
            Alerts::add_error(l('global.error_message.invalid_csrf_token'));
        }

        if(!file_exists(UPLOADS_PATH . 'logs/' . $log_id . '.log')) {
            redirect('admin/logs');
        }

        if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

            /* Delete the resource */
            unlink(UPLOADS_PATH . 'logs/' . $log_id . '.log');

            /* Set a nice success message */
            Alerts::add_success(sprintf(l('global.success_message.delete1'), '<strong>' . $log_id . '</strong>'));

        }

        redirect('admin/logs');
    }

}
