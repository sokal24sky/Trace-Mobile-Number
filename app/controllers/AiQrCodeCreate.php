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

class AiQrCodeCreate extends Controller {

    public function index() {

        if(!settings()->codes->ai_qr_codes_is_enabled) {
            redirect('not-found');
        }

        \Altum\Authentication::guard();

        /* Team checks */
        if(\Altum\Teams::is_delegated() && !\Altum\Teams::has_access('create.ai_qr_codes')) {
            Alerts::add_info(l('global.info_message.team_no_access'));
            redirect('ai-qr-codes');
        }

        /* Check for the plan limit */
        $ai_qr_codes_current_month = db()->where('user_id', $this->user->user_id)->getValue('users', '`qrcode_ai_qr_codes_current_month`');

        if($this->user->plan_settings->ai_qr_codes_per_month_limit != -1 && $ai_qr_codes_current_month >= $this->user->plan_settings->ai_qr_codes_per_month_limit) {
            Alerts::add_info(l('global.info_message.plan_feature_limit'));
            redirect('ai-qr-codes');
        }

        /* Get available custom domains */
        $domains = (new \Altum\Models\Domain())->get_available_domains_by_user($this->user);

        /* Existing projects */
        $projects = (new \Altum\Models\Projects())->get_projects_by_user_id($this->user->user_id);

        /* Existing links */
        $links = (new \Altum\Models\Link())->get_full_links_by_user_id($this->user->user_id);

        $settings = [];

        if(!empty($_POST)) {
            $required_fields = ['name', 'content', 'prompt', 'ai_qr_code'];

            $_POST['name'] = trim(query_clean($_POST['name']));
            $_POST['project_id'] = !empty($_POST['project_id']) && array_key_exists($_POST['project_id'], $projects) ? (int) $_POST['project_id'] : null;
            $_POST['embedded_data'] = input_clean($_POST['embedded_data'], 10000);
            $_POST['content'] = input_clean($_POST['content'], 512);
            $_POST['prompt'] = input_clean($_POST['prompt'], 512);
            $_POST['ai_qr_code'] = input_clean($_POST['ai_qr_code'], 64);

            if(isset($_POST['link_id']) && isset($_POST['url_dynamic'])) {
                $link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links', ['link_id']);

                if(!$link) {
                    unset($_POST['link_id']);
                }
            } else {
                unset($_POST['link_id']);
                unset($_POST['url_dynamic']);
            }

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

            /* Fake uploaded qr code */
            $_FILES['ai_qr_code'] = [
                'name' => 'altum.png',
                'tmp_name' => Uploads::get_full_path('ai_qr_codes/temp') . $_POST['ai_qr_code'],
                'error' => null,
                'size' => 0,
            ];

            $ai_qr_code_image = \Altum\Uploads::process_upload_fake('ai_qr_codes', 'ai_qr_code');

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

                $settings = json_encode($settings);

                /* Database query */
                $ai_qr_code_id = db()->insert('ai_qr_codes', [
                    'user_id' => $this->user->user_id,
                    'link_id' => $_POST['link_id'] ?? null,
                    'project_id' => $_POST['project_id'],
                    'name' => $_POST['name'],
                    'content' => $_POST['content'],
                    'prompt' => $_POST['prompt'],
                    'ai_qr_code' => $ai_qr_code_image,
                    'settings' => $settings,
                    'embedded_data' => $_POST['embedded_data'],
                    'datetime' => get_date(),
                ]);

                /* Clear the cache */
                cache()->deleteItem('ai_qr_codes_total?user_id=' . $this->user->user_id);
                cache()->deleteItem('ai_qr_codes_dashboard?user_id=' . $this->user->user_id);

                /* Set a nice success message */
                Alerts::add_success(sprintf(l('global.success_message.create1'), '<strong>' . $_POST['name'] . '</strong>'));

                redirect('ai-qr-code-update/' . $ai_qr_code_id);
            }
        }

        /* Set default values */
        $values = [
            'name' => $_POST['name'] ?? $_GET['name'] ?? '',
            'project_id' => $_POST['project_id'] ?? $_GET['project_id'] ?? '',
            'content' => $_POST['content'] ?? $_GET['content'] ?? '',
            'prompt' => $_POST['prompt'] ?? $_GET['prompt'] ?? '',
            'url_dynamic' => $_POST['url_dynamic'] ?? $_GET['url_dynamic'] ?? null,
            'link_id' => $_POST['link_id'] ?? $_GET['link_id'] ?? '',
            'embedded_data' => $_POST['embedded_data'] ?? $_GET['embedded_data'] ?? '',
            'settings' => $settings,
        ];

        /* Prepare the view */
        $data = [
            'domains' => $domains,
            'links' => $links,
            'projects' => $projects,
            'values' => $values
        ];

        $view = new \Altum\View('ai-qr-code-create/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
