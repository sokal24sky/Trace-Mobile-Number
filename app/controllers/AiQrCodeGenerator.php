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
use Altum\Uploads;

defined('ALTUMCODE') || die();

class AiQrCodeGenerator extends Controller {

    public function index() {

        if(empty($_POST)) {
            redirect();
        }

        /* :) */
        if(isset($_POST['json'])) {
            $_POST = json_decode($_POST['json'], true);
        }

        /* Check for the API Key if needed */
        if(!isset($_POST['api_key']) || (isset($_POST['api_key']) && empty($_POST['api_key']))) {

        } else {
            $user = db()->where('api_key', $_POST['api_key'])->where('status', 1)->getOne('users');

            if(!$user) {
                die();
            }

            $this->user = $user;
            $user->plan_settings = json_decode($user->plan_settings);
        }

        /* Check for the plan limit */
        $ai_qr_codes_current_month = db()->where('user_id', $this->user->user_id)->getValue('users', '`qrcode_ai_qr_codes_current_month`');
        if($this->user->plan_settings->ai_qr_codes_per_month_limit != -1 && $ai_qr_codes_current_month >= $this->user->plan_settings->ai_qr_codes_per_month_limit) {
            Response::json(l('global.info_message.plan_feature_limit'), 'error');
        }

        /* Process variables */
        $data = $_POST['content'];
        if(string_starts_with('http://', $data) || string_starts_with('https://', $data)) {
            $data = get_url($_POST['content'] ?? null);
        }

        /* Check if data is empty */
        if(!trim($data)) {
            $data = get_domain_from_url(SITE_URL);
        }

        /* Send an API request to generate the AI image */
        $response = \Unirest\Request::post('https://api.replicate.com/v1/predictions',
            [
                'Prefer' => 'wait',
                'Authorization' => 'Bearer ' . settings()->codes->ai_qr_codes_replicate_api_key,
                'Accept' => 'application/json',
            ],
            \Unirest\Request\Body::json([
                'version' => '628e604e13cf63d8ec58bd4d238474e8986b054bc5e1326e50995fdbc851c557',
                'input' => [
                    'url' => $_POST['content'],
                    'prompt' => $_POST['prompt'],
                    'qr_conditioning_scale' => 1.3
                ]
            ])
        );

        if($response->code != 201) {
            error_log('Issue generating AI QR CODE: ' . $response->raw_body);
            Response::json(l('ai_qr_codes.error_message.api'), 'error');
        }

        $image_url = $response->body->output[0];

        /* Generate new name for image */
        $image_new_name = md5(time() . rand() . rand()) . '.' . 'png';

        /* Save the image locally temporarily */
        $image_data = file_get_contents($image_url);
        file_put_contents(Uploads::get_full_path('ai_qr_codes/temp') . $image_new_name, $image_data);

        /* Database query */
        db()->where('user_id', $this->user->user_id)->update('users', [
            'qrcode_ai_qr_codes_current_month' => db()->inc(1)
        ]);

        /* Return the image */
        Response::json('', 'success', ['data' => Uploads::get_full_url('ai_qr_codes/temp') . $image_new_name, 'ai_qr_code' => $image_new_name, 'embedded_data' => $data]);

    }

}

