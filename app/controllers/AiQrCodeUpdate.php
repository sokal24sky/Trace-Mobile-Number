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
use Altum\Uploads;

defined('ALTUMCODE') || die();

class AiQrCodeUpdate extends Controller {

    public function index() {

        \Altum\Authentication::guard();

        if(!settings()->codes->ai_qr_codes_is_enabled) {
            redirect('not-found');
        }

        /* Team checks */
        if(\Altum\Teams::is_delegated() && !\Altum\Teams::has_access('update.ai_qr_codes')) {
            Alerts::add_info(l('global.info_message.team_no_access'));
            redirect('ai-qr-codes');
        }

        $ai_qr_code_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        if(!$ai_qr_code = db()->where('ai_qr_code_id', $ai_qr_code_id)->where('user_id', $this->user->user_id)->getOne('ai_qr_codes')) {
            redirect('ai-qr-codes');
        }
        $ai_qr_code->settings = json_decode($ai_qr_code->settings ?? '');

        /* Existing projects */
        $projects = (new \Altum\Models\Projects())->get_projects_by_user_id($this->user->user_id);

        /* Existing links */
        $links = (new \Altum\Models\Link())->get_full_links_by_user_id($this->user->user_id);

        if(!empty($_POST)) {
            $required_fields = ['name', 'content', 'prompt', 'ai_qr_code'];
            $settings = [];

            $_POST['name'] = trim(query_clean($_POST['name']));
            $_POST['project_id'] = !empty($_POST['project_id']) && array_key_exists($_POST['project_id'], $projects) ? (int) $_POST['project_id'] : null;
            $_POST['embedded_data'] = input_clean($_POST['embedded_data'], 10000);
            $_POST['content'] = input_clean($_POST['content'], 512);
            $_POST['prompt'] = input_clean($_POST['prompt'], 512);
            $_POST['ai_qr_code'] = input_clean($_POST['ai_qr_code'], 64);

            //ALTUMCODE:DEMO if(DEMO) if($this->user->user_id == 1) Alerts::add_error('Please create an account on the demo to test out this function.');

            /* Check for any errors */
            foreach($required_fields as $field) {
                if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                    Alerts::add_field_error($field, l('global.error_message.empty_field'));
                }
            }

            if(!\Altum\Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            $ai_qr_code_image = $ai_qr_code->ai_qr_code;

            if($_POST['ai_qr_code'] && $ai_qr_code->ai_qr_code != $_POST['ai_qr_code']) {
                /* Fake uploaded synthesis */
                $_FILES['ai_qr_code'] = [
                    'name' => 'altum.png',
                    'tmp_name' => Uploads::get_full_path('ai_qr_codes/temp') . $_POST['ai_qr_code'],
                    'error' => null,
                    'size' => 0,
                ];

                /* Delete old one */
                Uploads::delete_uploaded_file($ai_qr_code->ai_qr_code, 'ai_qr_codes');

                $ai_qr_code_image = \Altum\Uploads::process_upload_fake('ai_qr_codes', 'ai_qr_code');
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {
                $settings = json_encode($settings);

                /* Database query */
                db()->where('ai_qr_code_id', $ai_qr_code->ai_qr_code_id)->update('ai_qr_codes', [
                    'project_id' => $_POST['project_id'],
                    'link_id' => $_POST['link_id'],
                    'name' => $_POST['name'],
                    'content' => $_POST['content'],
                    'prompt' => $_POST['prompt'],
                    'ai_qr_code' => $ai_qr_code_image,
                    'settings' => $settings,
                    'embedded_data' => $_POST['embedded_data'],
                    'last_datetime' => get_date(),
                ]);

                /* Clear the cache */
                cache()->deleteItem('ai_qr_codes_dashboard?user_id=' . $this->user->user_id);

                /* Set a nice success message */
                Alerts::add_success(sprintf(l('global.success_message.update1'), '<strong>' . $_POST['name'] . '</strong>'));

                redirect('ai-qr-code-update/' . $ai_qr_code_id);
            }
        }

        /* Prepare the view */
        $data = [
            'ai_qr_code' => $ai_qr_code,
            'projects' => $projects,
            'links' => $links,
        ];

        $view = new \Altum\View('ai-qr-code-update/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
