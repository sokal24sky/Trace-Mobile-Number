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
use Altum\Date;
use Altum\Uploads;

defined('ALTUMCODE') || die();

class QrCodeUpdate extends Controller {

    public function index() {

        \Altum\Authentication::guard();

        if(!settings()->codes->qr_codes_is_enabled) {
            redirect('not-found');
        }

        /* Team checks */
        if(\Altum\Teams::is_delegated() && !\Altum\Teams::has_access('update.qr_codes')) {
            Alerts::add_info(l('global.info_message.team_no_access'));
            redirect('qr-codes');
        }

        $qr_code_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        if(!$qr_code = db()->where('qr_code_id', $qr_code_id)->where('user_id', $this->user->user_id)->getOne('qr_codes')) {
            redirect('qr-codes');
        }
        $qr_code->settings = json_decode($qr_code->settings ?? '');

        $available_qr_codes = require APP_PATH . 'includes/enabled_qr_codes.php';
        $frames = require APP_PATH . 'includes/qr_codes_frames.php';
        $frames_fonts = require APP_PATH . 'includes/qr_codes_frames_text_fonts.php';
        $styles = require APP_PATH . 'includes/qr_codes_styles.php';
        $inner_eyes = require APP_PATH . 'includes/qr_codes_inner_eyes.php';
        $outer_eyes = require APP_PATH . 'includes/qr_codes_outer_eyes.php';

        /* Existing projects */
        $projects = (new \Altum\Models\Projects())->get_projects_by_user_id($this->user->user_id);

        /* Existing links */
        $links = (new \Altum\Models\Link())->get_full_links_by_user_id($this->user->user_id);

        if(!empty($_POST)) {
            $required_fields = ['name', 'type'];
            $settings = [];

            $_POST['name'] = trim(query_clean($_POST['name']));
            $_POST['project_id'] = !empty($_POST['project_id']) && array_key_exists($_POST['project_id'], $projects) ? (int) $_POST['project_id'] : null;
            $_POST['embedded_data'] = input_clean($_POST['embedded_data'], 10000);
            $_POST['type'] = isset($_POST['type']) && array_key_exists($_POST['type'], $available_qr_codes) ? $_POST['type'] : 'text';
            $settings['style'] = $_POST['style'] = isset($_POST['style']) && array_key_exists($_POST['style'], $styles) ? $_POST['style'] : 'square';
            $settings['inner_eye_style'] = $_POST['inner_eye_style'] = isset($_POST['inner_eye_style']) && array_key_exists($_POST['inner_eye_style'], $inner_eyes) ? $_POST['inner_eye_style'] : 'square';
            $settings['outer_eye_style'] = $_POST['outer_eye_style'] = isset($_POST['outer_eye_style']) && array_key_exists($_POST['outer_eye_style'], $outer_eyes) ? $_POST['outer_eye_style'] : 'square';            $settings['foreground_type'] = $_POST['foreground_type'] = isset($_POST['foreground_type']) && in_array($_POST['foreground_type'], ['color', 'gradient']) ? $_POST['foreground_type'] : 'color';
            switch($_POST['foreground_type']) {
                case 'color':
                    $settings['foreground_color'] = $_POST['foreground_color'] = isset($_POST['foreground_color']) && verify_hex_color($_POST['foreground_color']) ? $_POST['foreground_color'] : '#000000';
                    break;

                case 'gradient':
                    $settings['foreground_gradient_style'] = $_POST['foreground_gradient_style'] = isset($_POST['foreground_gradient_style']) && in_array($_POST['foreground_gradient_style'], ['vertical', 'horizontal', 'diagonal', 'inverse_diagonal', 'radial']) ? $_POST['foreground_gradient_style'] : 'horizontal';
                    $settings['foreground_gradient_one'] = $_POST['foreground_gradient_one'] = isset($_POST['foreground_gradient_one']) && verify_hex_color($_POST['foreground_gradient_one']) ? $_POST['foreground_gradient_one'] : '#000000';
                    $settings['foreground_gradient_two'] = $_POST['foreground_gradient_two'] = isset($_POST['foreground_gradient_two']) && verify_hex_color($_POST['foreground_gradient_two']) ? $_POST['foreground_gradient_two'] : '#000000';
                    break;
            }
            $settings['background_color'] = $_POST['background_color'] = isset($_POST['background_color']) && verify_hex_color($_POST['background_color']) ? $_POST['background_color'] : '#ffffff';
            $settings['background_color_transparency'] = $_POST['background_color_transparency'] = isset($_POST['background_color_transparency']) && in_array($_POST['background_color_transparency'], range(0, 100)) ? (int) $_POST['background_color_transparency'] : 0;
            $settings['custom_eyes_color'] = $_POST['custom_eyes_color'] = (int) isset($_POST['custom_eyes_color']);
            if($_POST['custom_eyes_color']) {
                $settings['eyes_inner_color'] = $_POST['eyes_inner_color'] = isset($_POST['eyes_inner_color']) && verify_hex_color($_POST['eyes_inner_color']) ? $_POST['eyes_inner_color'] : '#000000';
                $settings['eyes_outer_color'] = $_POST['eyes_outer_color'] = isset($_POST['eyes_outer_color']) && verify_hex_color($_POST['eyes_outer_color']) ? $_POST['eyes_outer_color'] : '#000000';
            }

            $settings['qr_code_logo_size'] = $_POST['qr_code_logo_size'] = isset($_POST['qr_code_logo_size']) && in_array($_POST['qr_code_logo_size'], range(5, 40)) ? (int) $_POST['qr_code_logo_size'] : 25;

            $settings['qr_code_background_transparency'] = $_POST['qr_code_background_transparency'] = isset($_POST['qr_code_background_transparency']) && in_array($_POST['qr_code_background_transparency'], range(0, 99)) ? (int) $_POST['qr_code_background_transparency'] : 0;
            $settings['qr_code_foreground_transparency'] = $_POST['qr_code_foreground_transparency'] = isset($_POST['qr_code_foreground_transparency']) && in_array($_POST['qr_code_foreground_transparency'], range(0, 99)) ? (int) $_POST['qr_code_foreground_transparency'] : 0;

            $settings['size'] = $_POST['size'] = isset($_POST['size']) && in_array($_POST['size'], range(50, 2000)) ? (int) $_POST['size'] : 500;
            $settings['margin'] = $_POST['margin'] = isset($_POST['margin']) && in_array($_POST['margin'], range(0, 25)) ? (int) $_POST['margin'] : 1;
            $settings['ecc'] = $_POST['ecc'] = isset($_POST['ecc']) && in_array($_POST['ecc'], ['L', 'M', 'Q', 'H']) ? $_POST['ecc'] : 'M';
            $settings['encoding'] = $_POST['encoding'] = isset($_POST['encoding']) && in_array($_POST['encoding'], [
                'ISO-8859-1',
                'ISO-8859-2',
                'ISO-8859-3',
                'ISO-8859-4',
                'ISO-8859-5',
                'ISO-8859-6',
                'ISO-8859-7',
                'ISO-8859-8',
                'ISO-8859-9',
                'ISO-8859-10',
                'ISO-8859-11',
                'ISO-8859-12',
                'ISO-8859-13',
                'ISO-8859-14',
                'ISO-8859-15',
                'ISO-8859-16',
                'SHIFT-JIS',
                'WINDOWS-1250',
                'WINDOWS-1251',
                'WINDOWS-1252',
                'WINDOWS-1256',
                'UTF-16BE',
                'UTF-8',
                'ASCII',
                'GBK',
                'EUC-KR',
            ]) ? $_POST['encoding'] : 'UTF-8';
            $settings['is_readable'] = $_POST['is_readable'] = (int) isset($_POST['is_readable']);

            /* Frame */
            $settings['frame'] = $_POST['frame'] = isset($_POST['frame']) && array_key_exists($_POST['frame'], $frames) ? input_clean($_POST['frame']) : null;
            $settings['frame_text'] = $_POST['frame_text'] = input_clean($_POST['frame_text'], 64);
            $settings['frame_text_font'] = $_POST['frame_text_font'] = isset($_POST['frame_text_font']) && array_key_exists($_POST['frame_text_font'], $frames_fonts) ? $_POST['frame_text_font'] : array_key_first($frames_fonts);
            $settings['frame_text_size'] = $_POST['frame_text_size'] = in_array($_POST['frame_text_size'] ?? 0, range(-5, 5)) ? (int) $_POST['frame_text_size'] : 0;
            $settings['frame_custom_colors'] = $_POST['frame_custom_colors'] = (int) isset($_POST['frame_custom_colors']);
            if($_POST['frame_custom_colors']) {
                $settings['frame_color'] = $_POST['frame_color'] = !verify_hex_color($_POST['frame_color']) ? null : $_POST['frame_color'];
                $settings['frame_text_color'] = $_POST['frame_text_color'] = !verify_hex_color($_POST['frame_text_color']) ? null : $_POST['frame_text_color'];
            }

            /* Type dependant vars */
            switch($_POST['type']) {
                case 'text':
                    $required_fields[] = 'text';
                    $settings['text'] = $_POST['text'] = input_clean($_POST['text'], $available_qr_codes['text']['max_length']);
                    break;

                case 'url':
                    $required_fields[] = 'url';
                    $settings['url'] = $_POST['url'] = input_clean($_POST['url'], $available_qr_codes['url']['max_length']);

                    if(isset($_POST['link_id']) && isset($_POST['url_dynamic'])) {
                        $link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links', ['link_id']);

                        if(!$link) {
                            unset($_POST['link_id']);
                        }
                    } else {
                        $_POST['link_id'] = null;
                    }

                    break;

                case 'phone':
                    $required_fields[] = 'phone';
                    $settings['phone'] = $_POST['phone'] = input_clean($_POST['phone'], $available_qr_codes['phone']['max_length']);
                    break;

                case 'sms':
                    $required_fields[] = 'sms';
                    $settings['sms'] = $_POST['sms'] = input_clean($_POST['sms'], $available_qr_codes['sms']['max_length']);
                    $settings['sms_body'] = $_POST['sms_body'] = input_clean($_POST['sms_body'], $available_qr_codes['sms']['body']['max_length']);
                    break;

                case 'email':
                    $required_fields[] = 'email';
                    $settings['email'] = $_POST['email'] = input_clean_email($_POST['email'] ?? '');
                    $settings['email_subject'] = $_POST['email_subject'] = input_clean($_POST['email_subject'], $available_qr_codes['email']['subject']['max_length']);
                    $settings['email_body'] = $_POST['email_body'] = input_clean($_POST['email_body'], $available_qr_codes['email']['body']['max_length']);
                    break;

                case 'whatsapp':
                    $required_fields[] = 'whatsapp';
                    $settings['whatsapp'] = $_POST['whatsapp'] = (int) input_clean($_POST['whatsapp'], $available_qr_codes['whatsapp']['max_length']);
                    $settings['whatsapp_body'] = $_POST['whatsapp_body'] = input_clean($_POST['whatsapp_body'], $available_qr_codes['whatsapp']['body']['max_length']);
                    break;

                case 'facetime':
                    $required_fields[] = 'facetime';
                    $settings['facetime'] = $_POST['facetime'] = input_clean($_POST['facetime'], $available_qr_codes['facetime']['max_length']);
                    break;

                case 'location':
                    $required_fields[] = 'location_latitude';
                    $required_fields[] = 'location_longitude';
                    $settings['location_latitude'] = $_POST['location_latitude'] = (float) input_clean($_POST['location_latitude'], $available_qr_codes['location']['latitude']['max_length']);
                    $settings['location_longitude'] = $_POST['location_longitude'] = (float) input_clean($_POST['location_longitude'], $available_qr_codes['location']['longitude']['max_length']);
                    break;

                case 'wifi':
                    $required_fields[] = 'wifi_ssid';
                    $settings['wifi_ssid'] = $_POST['wifi_ssid'] = input_clean($_POST['wifi_ssid'], $available_qr_codes['wifi']['ssid']['max_length']);
                    $settings['wifi_encryption'] = $_POST['wifi_encryption'] = isset($_POST['wifi_encryption']) && in_array($_POST['wifi_encryption'], ['nopass', 'WEP', 'WPA/WPA2']) ? $_POST['wifi_encryption'] : 'nopass';
                    $settings['wifi_password'] = $_POST['wifi_password'] = input_clean($_POST['wifi_password'], $available_qr_codes['wifi']['password']['max_length']);
                    $settings['wifi_is_hidden'] = $_POST['wifi_is_hidden'] = (int) $_POST['wifi_is_hidden'];
                    break;

                case 'event':
                    $required_fields[] = 'event';
                    $settings['event'] = $_POST['event'] = input_clean($_POST['event'], $available_qr_codes['event']['max_length']);
                    $settings['event_location'] = $_POST['event_location'] = input_clean($_POST['event_location'], $available_qr_codes['event']['location']['max_length']);
                    $settings['event_url'] = $_POST['event_url'] = input_clean($_POST['event_url'], $available_qr_codes['event']['url']['max_length']);
                    $settings['event_note'] = $_POST['event_note'] = input_clean($_POST['event_note'], $available_qr_codes['event']['note']['max_length']);
                    $settings['event_timezone'] = $_POST['event_timezone'] = in_array($_POST['event_timezone'], \DateTimeZone::listIdentifiers()) ? input_clean($_POST['event_timezone']) : Date::$default_timezone;
                    $settings['event_start_datetime'] = $_POST['event_start_datetime'] = (new \DateTime($_POST['event_start_datetime']))->format('Y-m-d\TH:i:s');
                    $settings['event_end_datetime'] = $_POST['event_end_datetime'] = (new \DateTime($_POST['event_end_datetime']))->format('Y-m-d\TH:i:s');
                    $settings['event_first_alert_datetime'] = $_POST['event_first_alert_datetime'] = (new \DateTime($_POST['event_first_alert_datetime']))->format('Y-m-d\TH:i:s');
                    $settings['event_second_alert_datetime'] = $_POST['event_second_alert_datetime'] = (new \DateTime($_POST['event_second_alert_datetime']))->format('Y-m-d\TH:i:s');
                    break;

                case 'crypto':
                    $required_fields[] = 'crypto_address';
                    $settings['crypto_coin'] = $_POST['crypto_coin'] = isset($_POST['crypto_coin']) && array_key_exists($_POST['crypto_coin'], $available_qr_codes['crypto']['coins']) ? $_POST['crypto_coin'] : array_key_first($available_qr_codes['crypto']['coins']);
                    $settings['crypto_address'] = $_POST['crypto_address'] = input_clean($_POST['crypto_address'], $available_qr_codes['crypto']['address']['max_length']);
                    $settings['crypto_amount'] = $_POST['crypto_amount'] = isset($_POST['crypto_amount']) ? (float) $_POST['crypto_amount'] : null;
                    break;

                case 'vcard':
                    $settings['vcard_first_name'] = $_POST['vcard_first_name'] = input_clean($_POST['vcard_first_name'], $available_qr_codes['vcard']['first_name']['max_length']);
                    $settings['vcard_last_name'] = $_POST['vcard_last_name'] = input_clean($_POST['vcard_last_name'], $available_qr_codes['vcard']['last_name']['max_length']);
                    $settings['vcard_email'] = $_POST['vcard_email'] = input_clean($_POST['vcard_email'], $available_qr_codes['vcard']['email']['max_length']);
                    $settings['vcard_url'] = $_POST['vcard_url'] = input_clean($_POST['vcard_url'], $available_qr_codes['vcard']['url']['max_length']);
                    $settings['vcard_company'] = $_POST['vcard_company'] = input_clean($_POST['vcard_company'], $available_qr_codes['vcard']['company']['max_length']);
                    $settings['vcard_job_title'] = $_POST['vcard_job_title'] = input_clean($_POST['vcard_job_title'], $available_qr_codes['vcard']['job_title']['max_length']);
                    $settings['vcard_birthday'] = $_POST['vcard_birthday'] = input_clean($_POST['vcard_birthday'], $available_qr_codes['vcard']['birthday']['max_length']);
                    $settings['vcard_street'] = $_POST['vcard_street'] = input_clean($_POST['vcard_street'], $available_qr_codes['vcard']['street']['max_length']);
                    $settings['vcard_city'] = $_POST['vcard_city'] = input_clean($_POST['vcard_city'], $available_qr_codes['vcard']['city']['max_length']);
                    $settings['vcard_zip'] = $_POST['vcard_zip'] = input_clean($_POST['vcard_zip'], $available_qr_codes['vcard']['zip']['max_length']);
                    $settings['vcard_region'] = $_POST['vcard_region'] = input_clean($_POST['vcard_region'], $available_qr_codes['vcard']['region']['max_length']);
                    $settings['vcard_country'] = $_POST['vcard_country'] = input_clean($_POST['vcard_country'], $available_qr_codes['vcard']['country']['max_length']);
                    $settings['vcard_note'] = $_POST['vcard_note'] = input_clean($_POST['vcard_note'], $available_qr_codes['vcard']['note']['max_length']);

                    /* Phone numbers */
                    if(!isset($_POST['vcard_phone_number_label'])) {
                        $_POST['vcard_phone_number_label'] = [];
                        $_POST['vcard_phone_number_value'] = [];
                    }
                    $vcard_phone_numbers = [];
                    foreach ($_POST['vcard_phone_number_label'] as $key => $value) {
                        if($key >= 20) continue;

                        $vcard_phone_numbers[] = [
                            'label' => input_clean($value, $available_qr_codes['vcard']['phone_number_value']['max_length']),
                            'value' => input_clean($_POST['vcard_phone_number_value'][$key], $available_qr_codes['vcard']['phone_number_value']['max_length'])
                        ];
                    }
                    $settings['vcard_phone_numbers'] = $vcard_phone_numbers;

                    $vcard_socials = [];
                    foreach ($_POST['vcard_social_label'] as $key => $value) {
                        if(empty(trim($value))) continue;
                        if($key >= 20) continue;

                        $vcard_socials[] = [
                            'label' => input_clean($value, $available_qr_codes['vcard']['social_value']['max_length']),
                            'value' => input_clean($_POST['vcard_social_value'][$key], $available_qr_codes['vcard']['social_value']['max_length'])
                        ];
                    }
                    $settings['vcard_socials'] = $vcard_socials;
                    break;

                case 'paypal':
                    $required_fields[] = 'paypal_email';
                    $required_fields[] = 'paypal_title';
                    $required_fields[] = 'paypal_currency';
                    $required_fields[] = 'paypal_price';
                    $settings['paypal_type'] = $_POST['paypal_type'] = isset($_POST['paypal_type']) && array_key_exists($_POST['paypal_type'], $available_qr_codes['paypal']['type']) ? $_POST['paypal_type'] : array_key_first($available_qr_codes['paypal']['type']);
                    $settings['paypal_email'] = $_POST['paypal_email'] = input_clean($_POST['paypal_email'], $available_qr_codes['paypal']['email']['max_length']);
                    $settings['paypal_title'] = $_POST['paypal_title'] = input_clean($_POST['paypal_title'], $available_qr_codes['paypal']['title']['max_length']);
                    $settings['paypal_currency'] = $_POST['paypal_currency'] = input_clean($_POST['paypal_currency'], $available_qr_codes['paypal']['currency']['max_length']);
                    $settings['paypal_price'] = $_POST['paypal_price'] = (float) $_POST['paypal_price'];
                    $settings['paypal_thank_you_url'] = $_POST['paypal_thank_you_url'] = input_clean($_POST['paypal_thank_you_url'], $available_qr_codes['paypal']['thank_you_url']['max_length']);
                    $settings['paypal_cancel_url'] = $_POST['paypal_cancel_url'] = input_clean($_POST['paypal_cancel_url'], $available_qr_codes['paypal']['cancel_url']['max_length']);
                    break;

                case 'upi':
                    $required_fields[] = 'upi_payee_id';
                    $required_fields[] = 'upi_payee_name';
                    $settings['upi_payee_id'] = $_POST['upi_payee_id'] = input_clean($_POST['upi_payee_id'], $available_qr_codes['upi']['payee_id']['max_length']);
                    $settings['upi_payee_name'] = $_POST['upi_payee_name'] = input_clean($_POST['upi_payee_name'], $available_qr_codes['upi']['payee_name']['max_length']);
                    $settings['upi_currency'] = $_POST['upi_currency'] = in_array($_POST['upi_currency'], ['INR']) ? $_POST['upi_currency'] : 'INR';
                    $settings['upi_amount'] = isset($_POST['upi_amount']) ? (float) $_POST['upi_amount'] : null;
                    $settings['upi_transaction_id'] = $_POST['upi_transaction_id'] = input_clean($_POST['upi_transaction_id'], $available_qr_codes['upi']['transaction_id']['max_length']);
                    $settings['upi_transaction_note'] = $_POST['upi_transaction_note'] = input_clean($_POST['upi_transaction_note'], $available_qr_codes['upi']['transaction_note']['max_length']);
                    $settings['upi_transaction_reference'] = $_POST['upi_transaction_reference'] = input_clean($_POST['upi_transaction_reference'], $available_qr_codes['upi']['transaction_reference']['max_length']);
                    $settings['upi_thank_you_url'] = $_POST['upi_thank_you_url'] = input_clean($_POST['upi_thank_you_url'], $available_qr_codes['upi']['thank_you_url']['max_length']);
                    break;

                case 'epc':
                    $required_fields[] = 'epc_iban';
                    $required_fields[] = 'epc_payee_name';
                    $settings['epc_iban'] = $_POST['epc_iban'] = input_clean($_POST['epc_iban'], $available_qr_codes['epc']['iban']['max_length']);
                    $settings['epc_payee_name'] = $_POST['epc_payee_name'] = input_clean($_POST['epc_payee_name'], $available_qr_codes['epc']['payee_name']['max_length']);
                    $settings['epc_currency'] = $_POST['epc_currency'] = in_array($_POST['epc_currency'], ['EUR']) ? $_POST['epc_currency'] : 'EUR';
                    $settings['epc_amount'] = isset($_POST['epc_amount']) ? (float) $_POST['epc_amount'] : null;
                    $settings['epc_bic'] = $_POST['epc_bic'] = input_clean($_POST['epc_bic'], $available_qr_codes['epc']['bic']['max_length']);
                    $settings['epc_remittance_reference'] = $_POST['epc_remittance_reference'] = input_clean($_POST['epc_remittance_reference'], $available_qr_codes['epc']['remittance_reference']['max_length']);
                    $settings['epc_remittance_text'] = $_POST['epc_remittance_text'] = input_clean($_POST['epc_remittance_text'], $available_qr_codes['epc']['remittance_text']['max_length']);
                    $settings['information'] = $_POST['information'] = input_clean($_POST['information'], $available_qr_codes['epc']['information']['max_length']);
                    break;

                case 'pix':
                    $required_fields[] = 'pix_payee_key';
                    $required_fields[] = 'pix_payee_name';
                    $required_fields[] = 'pix_city';
                    $required_fields[] = 'pix_transaction_id';
                    $settings['pix_payee_key'] = $_POST['pix_payee_key'] = input_clean($_POST['pix_payee_key'], $available_qr_codes['pix']['payee_key']['max_length']);
                    $settings['pix_payee_name'] = $_POST['pix_payee_name'] = input_clean($_POST['pix_payee_name'], $available_qr_codes['pix']['payee_name']['max_length']);
                    $settings['pix_currency'] = $_POST['pix_currency'] = in_array($_POST['pix_currency'], ['BRL']) ? $_POST['pix_currency'] : 'BRL';
                    $settings['pix_amount'] = isset($_POST['pix_amount']) ? (float) $_POST['pix_amount'] : null;
                    $settings['pix_city'] = $_POST['pix_city'] = input_clean($_POST['pix_city'], $available_qr_codes['pix']['city']['max_length']);
                    $settings['pix_transaction_id'] = $_POST['pix_transaction_id'] = input_clean($_POST['pix_transaction_id'], $available_qr_codes['pix']['transaction_id']['max_length']);
                    $settings['pix_description'] = $_POST['pix_description'] = input_clean($_POST['pix_description'], $available_qr_codes['pix']['description']['max_length']);
                    break;
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

            $qr_code->qr_code_logo = \Altum\Uploads::process_upload($qr_code->qr_code_logo, 'qr_codes/logo', 'qr_code_logo', 'qr_code_logo_remove', settings()->codes->logo_size_limit);
            $qr_code->qr_code_background = \Altum\Uploads::process_upload($qr_code->qr_code_background, 'qr_code_background', 'qr_code_background', 'qr_code_background_remove', settings()->codes->background_size_limit);
            $qr_code->qr_code_foreground = \Altum\Uploads::process_upload($qr_code->qr_code_foreground, 'qr_code_foreground', 'qr_code_foreground', 'qr_code_foreground_remove', settings()->codes->background_size_limit);

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {
                /* QR code image */
                if($_POST['qr_code']) {
                    $_POST['qr_code'] = base64_decode(mb_substr($_POST['qr_code'], mb_strlen('data:image/svg+xml;base64,')));

                    /* Generate new name for image */
                    $image_new_name = md5(time() . rand()) . '.svg';

                    /* Offload uploading */
                    if(\Altum\Plugin::is_active('offload') && settings()->offload->uploads_url) {
                        try {
                            $s3 = new \Aws\S3\S3Client(get_aws_s3_config());

                            /* Delete current image */
                            $s3->deleteObject([
                                'Bucket' => settings()->offload->storage_name,
                                'Key' => UPLOADS_URL_PATH . Uploads::get_path('qr_codes') . $qr_code->qr_code,
                            ]);

                            /* Upload image */
                            $result = $s3->putObject([
                                'Bucket' => settings()->offload->storage_name,
                                'Key' => UPLOADS_URL_PATH . Uploads::get_path('qr_codes') . $image_new_name,
                                'ContentType' => 'image/svg+xml',
                                'Body' => $_POST['qr_code'],
                                'ACL' => 'public-read'
                            ]);
                        } catch (\Exception $exception) {
                            Alerts::add_error($exception->getMessage());
                        }
                    }

                    /* Local uploading */
                    else {
                        /* Delete current image */
                        if(!empty($qr_code->qr_code) && file_exists(Uploads::get_full_path('qr_codes') . $qr_code->qr_code)) {
                            unlink(Uploads::get_full_path('qr_codes') . $qr_code->qr_code);
                        }

                        /* Upload the original */
                        file_put_contents(Uploads::get_full_path('qr_codes') . $image_new_name, $_POST['qr_code']);
                    }

                    $qr_code->qr_code = $image_new_name;
                }

                $settings = json_encode($settings);

                /* Database query */
                db()->where('qr_code_id', $qr_code->qr_code_id)->update('qr_codes', [
                    'project_id' => $_POST['project_id'],
                    'link_id' => $_POST['link_id'],
                    'name' => $_POST['name'],
                    'type' => $_POST['type'],
                    'settings' => $settings,
                    'embedded_data' => $_POST['embedded_data'],
                    'qr_code' => $qr_code->qr_code,
                    'qr_code_logo' => $qr_code->qr_code_logo,
                    'qr_code_background' => $qr_code->qr_code_background,
                    'qr_code_foreground' => $qr_code->qr_code_foreground,
                    'last_datetime' => get_date(),
                ]);

                /* Clear the cache */
                cache()->deleteItem('qr_codes_dashboard?user_id=' . $this->user->user_id);

                /* Set a nice success message */
                Alerts::add_success(sprintf(l('global.success_message.update1'), '<strong>' . $_POST['name'] . '</strong>'));

                redirect('qr-code-update/' . $qr_code_id);
            }
        }

        /* Prepare the view */
        $data = [
            'available_qr_codes' => $available_qr_codes,
            'frames_fonts' => $frames_fonts,
            'frames' => $frames,
            'styles' => $styles,
            'inner_eyes' => $inner_eyes,
            'outer_eyes' => $outer_eyes,
            'qr_code' => $qr_code,
            'projects' => $projects,
            'links' => $links,
        ];

        $view = new \Altum\View('qr-code-update/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
