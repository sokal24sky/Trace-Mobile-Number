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

class BarcodeUpdate extends Controller {

    public function index() {

        \Altum\Authentication::guard();

        if(!settings()->codes->barcodes_is_enabled) {
            redirect('not-found');
        }

        /* Team checks */
        if(\Altum\Teams::is_delegated() && !\Altum\Teams::has_access('update.barcodes')) {
            Alerts::add_info(l('global.info_message.team_no_access'));
            redirect('barcodes');
        }

        $barcode_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        if(!$barcode = db()->where('barcode_id', $barcode_id)->where('user_id', $this->user->user_id)->getOne('barcodes')) {
            redirect('barcodes');
        }
        $barcode->settings = json_decode($barcode->settings ?? '');

        $available_barcodes = require APP_PATH . 'includes/enabled_barcodes.php';

        /* Existing projects */
        $projects = (new \Altum\Models\Projects())->get_projects_by_user_id($this->user->user_id);

        if(!empty($_POST)) {
            $required_fields = ['name', 'type', 'value'];
            $settings = [];

            $_POST['name'] = trim(query_clean($_POST['name']));
            $_POST['project_id'] = !empty($_POST['project_id']) && array_key_exists($_POST['project_id'], $projects) ? (int) $_POST['project_id'] : null;
            $_POST['embedded_data'] = input_clean($_POST['embedded_data'], 10000);
            $_POST['type'] = isset($_POST['type']) && array_key_exists($_POST['type'], $available_barcodes) ? $_POST['type'] : array_key_first($available_barcodes);
            $_POST['value'] = input_clean($_POST['value'], 64);

            $settings['foreground_color'] = $_POST['foreground_color'] = isset($_POST['foreground_color']) && verify_hex_color($_POST['foreground_color']) ? $_POST['foreground_color'] : '#000000';
            $settings['background_color'] = $_POST['background_color'] = isset($_POST['background_color']) && verify_hex_color($_POST['background_color']) ? $_POST['background_color'] : '#ffffff';
            $settings['width_scale'] = $_POST['width_scale'] = isset($_POST['width_scale']) && in_array($_POST['width_scale'], range(1, 10)) ? (int) $_POST['width_scale'] : 2;
            $settings['height'] = $_POST['height'] = isset($_POST['height']) && in_array($_POST['height'], range(30, 1000)) ? (int) $_POST['height'] : 30;
            $settings['display_text'] = $_POST['display_text'] = (int) isset($_POST['display_text']);

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

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {
                /* Bar image */
                if($_POST['barcode']) {
                    $_POST['barcode'] = base64_decode(mb_substr($_POST['barcode'], mb_strlen('data:image/svg+xml;base64,')));

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
                            Alerts::add_error($exception->getMessage());
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
                }

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
                cache()->deleteItem('barcodes_dashboard?user_id=' . $this->user->user_id);

                /* Set a nice success message */
                Alerts::add_success(sprintf(l('global.success_message.update1'), '<strong>' . $_POST['name'] . '</strong>'));

                redirect('barcode-update/' . $barcode_id);
            }
        }

        /* Prepare the view */
        $data = [
            'available_barcodes' => $available_barcodes,
            'barcode' => $barcode,
            'projects' => $projects,
        ];

        $view = new \Altum\View('barcode-update/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
