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

use Altum\Models\Barcode;
use Altum\Response;
use Altum\Traits\Apiable;
use Altum\Uploads;
use Unirest\Request;

defined('ALTUMCODE') || die();

class ApiBarcodes extends Controller {
    use Apiable;

    public function index() {

        if(!settings()->codes->barcodes_is_enabled) {
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
        $filters->set_default_order_by($this->api_user->preferences->barcodes_default_order_by, $this->api_user->preferences->default_order_type ?? settings()->main->default_order_type);
        $filters->set_default_results_per_page($this->api_user->preferences->default_results_per_page ?? settings()->main->default_results_per_page);
        $filters->process();

        /* Prepare the paginator */
        $total_rows = database()->query("SELECT COUNT(*) AS `total` FROM `barcodes` WHERE `user_id` = {$this->api_user->user_id}")->fetch_object()->total ?? 0;
        $paginator = (new \Altum\Paginator($total_rows, $filters->get_results_per_page(), $_GET['page'] ?? 1, url('api/barcodes?' . $filters->get_get() . '&page=%d')));

        /* Get the data */
        $data = [];
        $data_result = database()->query("
            SELECT
                *
            FROM
                `barcodes`
            WHERE
                `user_id` = {$this->api_user->user_id}
                {$filters->get_sql_where()}
                {$filters->get_sql_order_by()}
                  
            {$paginator->get_sql_limit()}
        ");
        while($row = $data_result->fetch_object()) {

            /* Prepare the data */
            $row = [
                'id' => (int) $row->barcode_id,
                'user_id' => (int) $row->user_id,
                'project_id' => (int) $row->project_id,
                'type' => $row->type,
                'name' => $row->name,
                'value' => $row->value,
                'barcode' => \Altum\Uploads::get_full_url('barcodes') . $row->barcode,
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

        $barcode_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        /* Try to get details about the resource id */
        $barcode = db()->where('barcode_id', $barcode_id)->where('user_id', $this->api_user->user_id)->getOne('barcodes');

        /* We haven't found the resource */
        if(!$barcode) {
            $this->return_404();
        }

        /* Prepare the data */
        $data = [
            'id' => (int) $barcode->barcode_id,
            'user_id' => (int) $barcode->user_id,
            'project_id' => (int) $barcode->project_id,
            'name' => $barcode->name,
            'type' => $barcode->type,
            'value' => $barcode->value,
            'barcode' => \Altum\Uploads::get_full_url('barcodes') . $barcode->barcode,
            'settings' => json_decode($barcode->settings),
            'embedded_data' => $barcode->embedded_data,
            'last_datetime' => $barcode->last_datetime,
            'datetime' => $barcode->datetime,
        ];

        Response::jsonapi_success($data);

    }

    private function post() {

        /* Check for the plan limit */
        $total_rows = db()->where('user_id', $this->api_user->user_id)->getValue('barcodes', 'count(`barcode_id`)');

        if($this->api_user->plan_settings->barcodes_limit != -1 && $total_rows >= $this->api_user->plan_settings->barcodes_limit) {
            $this->response_error(l('global.info_message.plan_feature_limit'), 401);
        }

        $available_barcodes = require APP_PATH . 'includes/enabled_barcodes.php';

        /* Existing projects */
        $projects = (new \Altum\Models\Projects())->get_projects_by_user_id($this->api_user->user_id);

        $_POST['name'] = trim($_POST['name'] ?? null);
        $_POST['project_id'] = !empty($_POST['project_id']) && array_key_exists($_POST['project_id'], $projects) ? (int) $_POST['project_id'] : null;
        $_POST['type'] = isset($_POST['type']) && array_key_exists($_POST['type'], $available_barcodes) ? $_POST['type'] : 'text';
        $_POST['value'] = input_clean($_POST['value'], 64);
        $_POST['is_bulk'] = (int) isset($_POST['is_bulk']);

        /* Settings */
        $settings = [];

        $settings['foreground_color'] = $_POST['foreground_color'] = isset($_POST['foreground_color']) && verify_hex_color($_POST['foreground_color']) ? $_POST['foreground_color'] : '#000000';
        $settings['background_color'] = $_POST['background_color'] = isset($_POST['background_color']) && verify_hex_color($_POST['background_color']) ? $_POST['background_color'] : '#ffffff';
        $settings['width_scale'] = $_POST['width_scale'] = isset($_POST['width_scale']) && in_array($_POST['width_scale'], range(1, 10)) ? (int) $_POST['width_scale'] : 2;
        $settings['height'] = $_POST['height'] = isset($_POST['height']) && in_array($_POST['height'], range(30, 1000)) ? (int) $_POST['height'] : 30;
        $settings['display_text'] = $_POST['display_text'] = isset($_POST['display_text']) ? (int) (bool) $_POST['display_text'] : 0;

        /* Check for any errors */
        $required_fields = ['name', 'type', 'value'];
        foreach($required_fields as $field) {
            if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                $this->response_error(l('global.error_message.empty_fields'), 401);
                break 1;
            }
        }

        /* Bulk processing */
        if($_POST['is_bulk']) {
            $data_rows = preg_split('/\r\n|\r|\n/', $_POST['value']);

            /* Foreach row, generate one QR code */
            $i = 1;
            $data = [
                'ids' => []
            ];

            foreach($data_rows as $data_row) {
                $data_row = trim($data_row);

                /* Skip empty lines */
                if(empty(trim($data_row))) {
                    continue;
                }

                /* Generate the QR Code */
                $request_files = [];

                try {
                    $response = Request::post(
                        url('api/barcodes'),
                        ['Authorization' => 'Bearer ' . $this->api_user->api_key],
                        Request\Body::multipart(
                            array_merge([
                                'api_key' => $this->api_user->api_key,
                                'type' => $_POST['type'],
                                'value' => $data_row,
                                'project_id' => $_POST['project_id'],
                                'name' => $_POST['name'] . ' - #' . $i,
                            ], $settings),
                            $request_files
                        )
                    );
                } catch (\Exception $exception) {
                    $this->response_error($exception->getMessage(), 401);
                    break;
                }

                if(isset($response->body->errors)) {
                    $this->response_error($response->body->errors[0]->title, 401);
                    break;
                }

                if($response->code == 201) {
                    $data['ids'][] = $response->body->data->id;
                }

                /* Do not allow more than 10 at once */
                if($i >= $this->api_user->plan_settings->barcodes_bulk_limit) {
                    break;
                }
                $i++;
            }
        }

        else {
            /* Generate the barcode */
            $request_data = array_merge([
                'api_key' => $this->api_user->api_key,
                'type' => $_POST['type'],
                'value' => $_POST['value'],
            ], $settings);

            $request_data = json_encode($request_data);

            try {
                $response = Request::post(url('barcode-generator'), [], Request\Body::multipart(['json' => $request_data]));
            } catch (\Exception $exception) {
                $this->response_error($exception->getMessage(), 401);
            }

            if($response->body->status == 'error') {
                $this->response_error($response->body->message, 401);
            }

            /* Barcode image */
            $_POST['barcode'] = base64_decode(mb_substr($response->body->details->data, mb_strlen('data:image/svg+xml;base64,')));

            /* Embedded data */
            $_POST['embedded_data'] = input_clean($response->body->details->embedded_data, 10000);

            /* Generate new name for image */
            $image_new_name = md5(time() . rand()) . '.svg';

            /* Offload uploading */
            if(\Altum\Plugin::is_active('offload') && settings()->offload->uploads_url) {
                try {
                    $s3 = new \Aws\S3\S3Client(get_aws_s3_config());

                    /* Upload image */
                    $result = $s3->putObject([
                        'Bucket' => settings()->offload->storage_name,
                        'Key' => UPLOADS_URL_PATH . Uploads::get_path('barcodes') . $image_new_name,
                        'ContentType' => 'image/svg+xml',
                        'Body' => $_POST['barcode'],
                        'ACL' => 'public-read'
                    ]);
                } catch (\Exception $exception) {
                    $this->response_error($exception->getMessage(), 401);
                }
            } /* Local uploading */
            else {
                /* Upload the original */
                file_put_contents(Uploads::get_full_path('barcodes') . $image_new_name, $_POST['barcode']);
            }
            $barcode = $image_new_name;

            $settings = json_encode($settings);

            /* Database query */
            $barcode_id = db()->insert('barcodes', [
                'user_id' => $this->api_user->user_id,
                'project_id' => $_POST['project_id'],
                'name' => $_POST['name'],
                'type' => $_POST['type'],
                'value' => $_POST['value'],
                'settings' => $settings,
                'embedded_data' => $_POST['embedded_data'],
                'barcode' => $barcode,
                'datetime' => get_date(),
            ]);

            /* Clear the cache */
            cache()->deleteItem('barcodes_total?user_id=' . $this->api_user->user_id);
            cache()->deleteItem('barcodes_dashboard?user_id=' . $this->api_user->user_id);

            /* Prepare the data */
            $data = [
                'id' => $barcode_id
            ];
        }

        Response::jsonapi_success($data, null, 201);

    }

    private function patch() {

        $barcode_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        /* Try to get details about the resource id */
        $barcode = db()->where('barcode_id', $barcode_id)->where('user_id', $this->api_user->user_id)->getOne('barcodes');

        /* We haven't found the resource */
        if(!$barcode) {
            $this->return_404();
        }
        $barcode->settings = json_decode($barcode->settings ?? '');

        $available_barcodes = require APP_PATH . 'includes/enabled_barcodes.php';

        /* Existing projects */
        $projects = (new \Altum\Models\Projects())->get_projects_by_user_id($this->api_user->user_id);

        $_POST['name'] = trim($_POST['name'] ?? $barcode->name);
        $_POST['project_id'] = !empty($_POST['project_id']) && array_key_exists($_POST['project_id'], $projects) ? (int) $_POST['project_id'] : $barcode->project_id;
        $_POST['type'] = isset($_POST['type']) && array_key_exists($_POST['type'], $available_barcodes) ? $_POST['type'] : $barcode->type;
        $_POST['value'] = input_clean($_POST['value'] ?? $barcode->settings->value, 64);

        /* Settings & barcode */
        $settings = [];

        $settings['foreground_color'] = $_POST['foreground_color'] = isset($_POST['foreground_color']) && verify_hex_color($_POST['foreground_color']) ? $_POST['foreground_color'] : $barcode->settings->foreground_color;
        $settings['background_color'] = $_POST['background_color'] = isset($_POST['background_color']) && verify_hex_color($_POST['background_color']) ? $_POST['background_color'] : $barcode->settings->background_color;
        $settings['width_scale'] = $_POST['width_scale'] = isset($_POST['width_scale']) && in_array($_POST['width_scale'], range(1, 10)) ? (int) $_POST['width_scale'] : $barcode->settings->width_scale;
        $settings['height'] = $_POST['height'] = isset($_POST['height']) && in_array($_POST['height'], range(30, 1000)) ? (int) $_POST['height'] : $barcode->settings->height;
        $settings['display_text'] = $_POST['display_text'] = isset($_POST['display_text']) ? (int) (bool) $_POST['display_text'] : $barcode->settings->display_text;

        /* Generate the barcode */
        $request_data = array_merge([
            'api_key' => $this->api_user->api_key,
            'type' => $_POST['type'],
        ], $settings);

        $request_data = json_encode($request_data);

        try {
            $response = Request::post(url('barcode-generator'), [], Request\Body::multipart(['json' => $request_data]));
        } catch (\Exception $exception) {
            $this->response_error($exception->getMessage(), 401);
        }

        if($response->body->status == 'error') {
            $this->response_error($response->body->message, 401);
        }

        /* Barcode image */
        $_POST['barcode'] = base64_decode(mb_substr($response->body->details->data, mb_strlen('data:image/svg+xml;base64,')));

        /* Embedded data */
        $_POST['embedded_data'] = input_clean($response->body->details->embedded_data, 10000);

        /* Generate new name for image */
        $image_new_name = md5(time() . rand()) . '.svg';

        /* Offload uploading */
        if(\Altum\Plugin::is_active('offload') && settings()->offload->uploads_url) {
            try {
                $s3 = new \Aws\S3\S3Client(get_aws_s3_config());

                /* Delete current image */
                $s3->deleteObject([
                    'Bucket' => settings()->offload->storage_name,
                    'Key' => UPLOADS_URL_PATH . Uploads::get_path('barcodes') . $barcode->barcode,
                ]);

                /* Upload image */
                $result = $s3->putObject([
                    'Bucket' => settings()->offload->storage_name,
                    'Key' => UPLOADS_URL_PATH . Uploads::get_path('barcodes') . $image_new_name,
                    'ContentType' => 'image/svg+xml',
                    'Body' => $_POST['barcode'],
                    'ACL' => 'public-read'
                ]);
            } catch (\Exception $exception) {
                $this->response_error($exception->getMessage(), 401);
            }
        }

        /* Local uploading */
        else {
            /* Delete current image */
            if(!empty($barcode->barcode) && file_exists(Uploads::get_full_path('barcodes') . $barcode->barcode)) {
                unlink(Uploads::get_full_path('barcodes') . $barcode->barcode);
            }

            /* Upload the original */
            file_put_contents(Uploads::get_full_path('barcodes') . $image_new_name, $_POST['barcode']);
        }
        $barcode->barcode = $image_new_name;

        $settings = json_encode($settings);

        /* Database query */
        db()->where('barcode_id', $barcode->barcode_id)->update('barcodes', [
            'project_id' => $_POST['project_id'],
            'name' => $_POST['name'],
            'type' => $_POST['type'],
            'value' => $_POST['value'],
            'settings' => $settings,
            'embedded_data' => $_POST['embedded_data'],
            'barcode' => $barcode->barcode,
            'last_datetime' => get_date(),
        ]);

        /* Clear the cache */
        cache()->deleteItem('barcodes_dashboard?user_id=' . $this->api_user->user_id);

        /* Prepare the data */
        $data = [
            'id' => $barcode->barcode_id
        ];

        Response::jsonapi_success($data, null, 200);

    }

    private function delete() {

        $barcode_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        /* Try to get details about the resource id */
        $barcode = db()->where('barcode_id', $barcode_id)->where('user_id', $this->api_user->user_id)->getOne('barcodes');

        /* We haven't found the resource */
        if(!$barcode) {
            $this->return_404();
        }

        (new Barcode())->delete($barcode->barcode_id);

        http_response_code(200);
        die();

    }

}
