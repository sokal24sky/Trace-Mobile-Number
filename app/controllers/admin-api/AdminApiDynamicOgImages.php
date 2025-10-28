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

use Altum\Response;
use Altum\Traits\Apiable;
use Altum\Uploads;

defined('ALTUMCODE') || die();

class AdminApiDynamicOgImages extends Controller {
    use Apiable;

    public function index() {

        $this->verify_request(true);

        /* Decide what to continue with */
        switch($_SERVER['REQUEST_METHOD']) {
            case 'POST':

                $this->post();

                break;
        }

        $this->return_404();

    }

    private function post() {

        $required_fields = ['image_name'];

        /* Check for any errors */
        foreach($required_fields as $field) {
            if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                $this->response_error(l('global.error_message.empty_fields'), 401);
                break 1;
            }
        }

        if(empty($_FILES['image']['name'])) {
            $this->response_error(l('global.error_message.empty_fields'), 401);
        }

        /* Determine the error response */
        $return_error = function($message) {
            Response::json($message, 'error');
        };

        $file_extension = mb_strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $file_temp = $_FILES['image']['tmp_name'];

        if($_FILES['image']['error'] == UPLOAD_ERR_INI_SIZE) {
            $return_error(sprintf(l('global.error_message.file_size_limit'), get_max_upload()));
        }

        if($_FILES['image']['error'] && $_FILES['image']['error'] != UPLOAD_ERR_INI_SIZE) {
            $return_error(l('global.error_message.file_upload') . ' (' . $_FILES['image']['error'] . ')');
        }

        if(!in_array($file_extension, Uploads::get_whitelisted_file_extensions('dynamic_og_images'))) {
            $return_error(l('global.error_message.invalid_file_type'));
        }

        if(!\Altum\Plugin::is_active('offload') || (\Altum\Plugin::is_active('offload') && !settings()->offload->uploads_url)) {
            if(!is_writable(UPLOADS_PATH . Uploads::get_path('dynamic_og_images'))) {
                $return_error(sprintf(l('global.error_message.directory_not_writable'), UPLOADS_PATH . Uploads::get_path('dynamic_og_images')));
            }
        }

        if(get_max_upload() && $_FILES['image']['size'] > get_max_upload() * 1000000) {
            $return_error(sprintf(l('global.error_message.file_size_limit'), get_max_upload()));
        }

        /* Generate new name for image */
        $image_new_name = get_slug($_POST['image_name']);

        /* Offload uploading */
        if(\Altum\Plugin::is_active('offload') && settings()->offload->uploads_url) {
            try {
                $s3 = new \Aws\S3\S3Client(get_aws_s3_config());

                /* Upload image */
                $result = $s3->putObject([
                    'Bucket' => settings()->offload->storage_name,
                    'Key' => UPLOADS_URL_PATH . Uploads::get_path('dynamic_og_images') . $image_new_name,
                    'ContentType' => mime_content_type($file_temp),
                    'SourceFile' => $file_temp,
                    'ACL' => 'public-read'
                ]);
            } catch (\Exception $exception) {
                $return_error($exception->getMessage());
            }
        }

        /* Upload the original */
        move_uploaded_file($file_temp, UPLOADS_PATH . Uploads::get_path('dynamic_og_images') . $image_new_name);

        /* Delete pending file */
        $image_pending_name = str_replace('.webp', '.pending', $image_new_name);
        if(file_exists(UPLOADS_PATH . Uploads::get_path('dynamic_og_images') . $image_pending_name)) unlink(UPLOADS_PATH . Uploads::get_path('dynamic_og_images') . $image_pending_name);

        Response::jsonapi_success([]);
    }

}
