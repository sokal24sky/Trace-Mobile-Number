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

use Altum\Models\AiQrCode;
use Altum\Response;
use Altum\Traits\Apiable;
use Altum\Uploads;
use Unirest\Request;

defined('ALTUMCODE') || die();

class ApiAiQrCodes extends Controller {
    use Apiable;

    public function index() {

        if(!settings()->codes->ai_qr_codes_is_enabled) {
            redirect('not-found');
        }

        $this->verify_request();

        /* Decide what to continue with */
        switch($_SERVER['REQUEST_METHOD']) {
            case 'GET':


                /* Detect if we only need an object, or the whole list */
                if(isset($this->params[0])) {
                    $this->get();
                } else {
                    $this->get_all();
                }

                break;

            case 'POST':

                /* Detect what method to use */
                if(isset($this->params[0])) {

                    $this->patch();
                } else {
                    $this->post();
                }

                break;

            case 'DELETE':


                $this->delete();

                break;
        }

        $this->return_404();
    }

    private function get_all() {

        /* Prepare the filtering system */
        $filters = (new \Altum\Filters([], [], []));
        $filters->set_default_order_by($this->api_user->preferences->ai_qr_codes_default_order_by, $this->api_user->preferences->default_order_type ?? settings()->main->default_order_type);
        $filters->set_default_results_per_page($this->api_user->preferences->default_results_per_page ?? settings()->main->default_results_per_page);
        $filters->process();

        /* Prepare the paginator */
        $total_rows = database()->query("SELECT COUNT(*) AS `total` FROM `ai_qr_codes` WHERE `user_id` = {$this->api_user->user_id}")->fetch_object()->total ?? 0;
        $paginator = (new \Altum\Paginator($total_rows, $filters->get_results_per_page(), $_GET['page'] ?? 1, url('api/ai-qr-codes?' . $filters->get_get() . '&page=%d')));

        /* Get the data */
        $data = [];
        $data_result = database()->query("
            SELECT
                *
            FROM
                `ai_qr_codes`
            WHERE
                `user_id` = {$this->api_user->user_id}
                {$filters->get_sql_where()}
                {$filters->get_sql_order_by()}
                  
            {$paginator->get_sql_limit()}
        ");
        while($row = $data_result->fetch_object()) {

            /* Prepare the data */
            $row = [
                'id' => (int) $row->ai_qr_code_id,
                'user_id' => (int) $row->user_id,
                'link_id' => (int) $row->link_id,
                'project_id' => (int) $row->project_id,
                'name' => $row->name,
                'content' => $row->content,
                'prompt' => $row->prompt,
                'ai_qr_code' => \Altum\Uploads::get_full_url('ai_qr_codes') . $row->ai_qr_code,
                'settings' => json_decode($row->settings),
                'embedded_data' => $row->embedded_data,
                'last_datetime' => $row->last_datetime,
                'datetime' => $row->datetime,
            ];

            $data[] = $row;
        }

        /* Prepare the data */
        $meta = [
            'page' => $_GET['page'] ?? 1,
            'total_pages' => $paginator->getNumPages(),
            'results_per_page' => $filters->get_results_per_page(),
            'total_results' => (int) $total_rows,
        ];

        /* Prepare the pagination links */
        $others = ['links' => [
            'first' => $paginator->getPageUrl(1),
            'last' => $paginator->getNumPages() ? $paginator->getPageUrl($paginator->getNumPages()) : null,
            'next' => $paginator->getNextUrl(),
            'prev' => $paginator->getPrevUrl(),
            'self' => $paginator->getPageUrl($_GET['page'] ?? 1)
        ]];

        Response::jsonapi_success($data, $meta, 200, $others);
    }

    private function get() {

        $ai_qr_code_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        /* Try to get details about the resource id */
        $ai_qr_code = db()->where('ai_qr_code_id', $ai_qr_code_id)->where('user_id', $this->api_user->user_id)->getOne('ai_qr_codes');

        /* We haven't found the resource */
        if(!$ai_qr_code) {
            $this->return_404();
        }

        /* Prepare the data */
        $data = [
            'id' => (int) $ai_qr_code->ai_qr_code_id,
            'user_id' => (int) $ai_qr_code->user_id,
            'link_id' => (int) $ai_qr_code->link_id,
            'project_id' => (int) $ai_qr_code->project_id,
            'name' => $ai_qr_code->name,
            'content' => $ai_qr_code->content,
            'prompt' => $ai_qr_code->prompt,
            'ai_qr_code' => \Altum\Uploads::get_full_url('ai_qr_codes') . $ai_qr_code->ai_qr_code,
            'settings' => json_decode($ai_qr_code->settings),
            'embedded_data' => $ai_qr_code->embedded_data,
            'last_datetime' => $ai_qr_code->last_datetime,
            'datetime' => $ai_qr_code->datetime,
        ];

        Response::jsonapi_success($data);

    }

    private function post() {

        /* Check for the plan limit */
        $ai_qr_codes_current_month = db()->where('user_id', $this->api_user->user_id)->getValue('users', '`qrcode_ai_qr_codes_current_month`');

        if($this->api_user->plan_settings->ai_qr_codes_per_month_limit != -1 && $ai_qr_codes_current_month >= $this->api_user->plan_settings->ai_qr_codes_per_month_limit) {
            $this->response_error(l('global.info_message.plan_feature_limit'), 401);
        }

        /* Existing projects */
        $projects = (new \Altum\Models\Projects())->get_projects_by_user_id($this->api_user->user_id);

        $settings = [];

        $_POST['name'] = trim($_POST['name'] ?? null);
        $_POST['project_id'] = !empty($_POST['project_id']) && array_key_exists($_POST['project_id'], $projects) ? (int) $_POST['project_id'] : null;
        $_POST['content'] = input_clean($_POST['content'], 512);
        $_POST['prompt'] = input_clean($_POST['prompt'], 512);

        /* Check for any errors */
        $required_fields = ['name', 'content', 'prompt'];
        foreach($required_fields as $field) {
            if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                $this->response_error(l('global.error_message.empty_fields'), 401);
                break 1;
            }
        }

        /* Generate the QR Code */
        $request_data = array_merge([
            'api_key' => $this->api_user->api_key,
            'content' => $_POST['content'],
            'prompt' => $_POST['prompt'],
        ], $settings);

        $request_data = json_encode($request_data);

        try {
            $response = Request::post(url('ai-qr-code-generator'), [], Request\Body::multipart(['json' => $request_data]));
        } catch (\Exception $exception) {
            $this->response_error($exception->getMessage(), 401);
        }

        if($response->body->status == 'error') {
            $this->response_error($response->body->message, 401);
        }

        /* Fake uploaded qr code */
        $_FILES['ai_qr_code'] = [
            'name' => 'altum.png',
            'tmp_name' => Uploads::get_full_path('ai_qr_codes/temp') . $response->body->details->ai_qr_code,
            'error' => null,
            'size' => 0,
        ];

        $ai_qr_code_image = \Altum\Uploads::process_upload_fake('ai_qr_codes', 'ai_qr_code', 'json');

        /* Embedded data */
        $_POST['embedded_data'] = input_clean($response->body->details->embedded_data, 10000);

        $settings = json_encode($settings);

        /* Database query */
        $ai_qr_code_id = db()->insert('ai_qr_codes', [
            'user_id' => $this->api_user->user_id,
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
        cache()->deleteItem('ai_qr_codes_total?user_id=' . $this->api_user->user_id);
        cache()->deleteItem('ai_qr_codes_dashboard?user_id=' . $this->api_user->user_id);

        /* Prepare the data */
        $data = [
            'id' => $ai_qr_code_id
        ];

        Response::jsonapi_success($data, null, 201);

    }

    private function patch() {

        $ai_qr_code_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        /* Try to get details about the resource id */
        $ai_qr_code = db()->where('ai_qr_code_id', $ai_qr_code_id)->where('user_id', $this->api_user->user_id)->getOne('ai_qr_codes');

        /* We haven't found the resource */
        if(!$ai_qr_code) {
            $this->return_404();
        }
        $ai_qr_code->settings = json_decode($ai_qr_code->settings ?? '');

        /* Existing projects */
        $projects = (new \Altum\Models\Projects())->get_projects_by_user_id($this->api_user->user_id);

        $settings = [];

        $should_regenerate = isset($_POST['prompt']);

        $_POST['name'] = trim($_POST['name'] ?? $ai_qr_code->name);
        $_POST['project_id'] = !empty($_POST['project_id']) && array_key_exists($_POST['project_id'], $projects) ? (int) $_POST['project_id'] : $ai_qr_code->project_id;
        $_POST['content'] = input_clean($_POST['content'] ?? $ai_qr_code->content, 512);
        $_POST['prompt'] = input_clean($_POST['prompt'] ?? $ai_qr_code->prompt, 512);

        $ai_qr_code_image = $ai_qr_code->ai_qr_code;
        $ai_qr_code_embedded = $ai_qr_code->embedded_data;

        /* Generate the QR Code */
        if($should_regenerate) {
            $request_data = array_merge([
                'api_key' => $this->api_user->api_key,
                'content' => $_POST['content'],
                'prompt' => $_POST['prompt'],
            ], $settings);

            $request_data = json_encode($request_data);

            try {
                $response = Request::post(url('ai-qr-code-generator'), [], Request\Body::multipart(['json' => $request_data]));
            } catch (\Exception $exception) {
                $this->response_error($exception->getMessage(), 401);
            }

            if($response->body->status == 'error') {
                $this->response_error($response->body->message, 401);
            }

            /* Fake uploaded synthesis */
            $_FILES['ai_qr_code'] = [
                'name' => 'altum.png',
                'tmp_name' => Uploads::get_full_path('ai_qr_codes/temp') . $response->body->details->ai_qr_code,
                'error' => null,
                'size' => 0,
            ];

            /* Delete old one */
            Uploads::delete_uploaded_file($ai_qr_code->ai_qr_code, 'ai_qr_codes');

            $ai_qr_code_image = \Altum\Uploads::process_upload_fake('ai_qr_codes', 'ai_qr_code', 'json');
            $ai_qr_code_embedded = $response->body->details->embedded_data;
        }

        $settings = json_encode($settings);

        /* Database query */
        db()->where('ai_qr_code_id', $ai_qr_code->ai_qr_code_id)->update('ai_qr_codes', [
            'link_id' => $_POST['link_id'] ?? null,
            'project_id' => $_POST['project_id'],
            'name' => $_POST['name'],
            'content' => $_POST['content'],
            'prompt' => $_POST['prompt'],
            'ai_qr_code' => $ai_qr_code_image,
            'settings' => $settings,
            'embedded_data' => $ai_qr_code_embedded,
            'last_datetime' => get_date(),
        ]);

        /* Clear the cache */
        cache()->deleteItem('ai_qr_codes_dashboard?user_id=' . $this->api_user->user_id);

        /* Prepare the data */
        $data = [
            'id' => $ai_qr_code->ai_qr_code_id
        ];

        Response::jsonapi_success($data, null, 200);

    }

    private function delete() {

        $ai_qr_code_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        /* Try to get details about the resource id */
        $ai_qr_code = db()->where('ai_qr_code_id', $ai_qr_code_id)->where('user_id', $this->api_user->user_id)->getOne('ai_qr_codes');

        /* We haven't found the resource */
        if(!$ai_qr_code) {
            $this->return_404();
        }

        (new AiQrCode())->delete($ai_qr_code->ai_qr_code_id);

        http_response_code(200);
        die();

    }

}
