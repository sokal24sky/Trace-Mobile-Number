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

defined('ALTUMCODE') || die();

class BarcodeGenerator extends Controller {

    public function index() {

        if(empty($_POST)) {
            redirect();
        }

        /* :) */
        $available_barcodes = require APP_PATH . 'includes/enabled_barcodes.php';

        if(isset($_POST['json'])) {
            $_POST = json_decode($_POST['json'], true);
        }

        $_POST['type'] = isset($_POST['type']) && array_key_exists($_POST['type'], $available_barcodes) ? $_POST['type'] : 'C32';

        /* Check for the API Key if needed */
        if(!isset($_POST['api_key']) || (isset($_POST['api_key']) && empty($_POST['api_key']))) {
            /* Check the guest plan */
            if(!$this->user->plan_settings->enabled_barcodes->{$_POST['type']}) {
                die();
            }
        } else {
            $user = db()->where('api_key', $_POST['api_key'])->where('status', 1)->getOne('users');

            if(!$user) {
                die();
            }
        }

        /* Process variables */
        $_POST['foreground_color'] = isset($_POST['foreground_color']) && verify_hex_color($_POST['foreground_color']) ? $_POST['foreground_color'] : '#000000';
        $_POST['background_color'] = isset($_POST['background_color']) && verify_hex_color($_POST['background_color']) ? $_POST['background_color'] : '#ffffff';
        $_POST['width_scale'] = isset($_POST['width_scale']) && in_array($_POST['width_scale'], range(1, 10)) ? (int) $_POST['width_scale'] : 2;
        $_POST['height'] = isset($_POST['height']) && in_array($_POST['height'], range(30, 1000)) ? (int) $_POST['height'] : 30;
        $_POST['display_text'] = (int) (bool) ($_POST['display_text'] ?? 0);

        $_POST['is_bulk'] = (int) (bool) ($_POST['is_bulk'] ?? 0);
        if($_POST['is_bulk']) {
            $_POST['value'] = preg_split('/\r\n|\r|\n/', $_POST['value'])[0];
        }

        $data = trim($_POST['value']);

        /* Check if data is empty */
        if(!trim($data)) {
            $data = 1;
        }

        /* Preprocessing */
        $data = $this->filter_barcode_data($data, $_POST['type']);

        if(!$data) {
            Response::json(l('barcodes.invalid_error_message'), 'error');
        }

        /* Generate the first SVG */
        try {
            /* build the full class name */
            $class_name = '\Picqer\Barcode\Types\\' . $available_barcodes[$_POST['type']]['source'];

            /* Barcode initiation */
            $barcode = (new $class_name())->getBarcode($data);

            /* SVG barcode renderer */
            $renderer = new \Picqer\Barcode\Renderers\SvgRenderer();
            $renderer->setSvgType($renderer::TYPE_SVG_STANDALONE);

            /* Set colors */
            $foreground_color_rgb = hex_to_rgb($_POST['foreground_color']);
            $renderer->setForegroundColor([$foreground_color_rgb['r'], $foreground_color_rgb['g'], $foreground_color_rgb['b']]);

            $background_color_rgb = hex_to_rgb($_POST['background_color']);
            $renderer->setBackgroundColor([$background_color_rgb['r'], $background_color_rgb['g'], $background_color_rgb['b']]);

            $svg = $renderer->render($barcode, $barcode->getWidth() * $_POST['width_scale'], $_POST['height']);

            if($_POST['display_text']) {
                $svg_text_element = '<text x="50%" y="' . ($_POST['height'] + 10) . '" dominant-baseline="middle" text-anchor="middle" font-size="10" font-family="Arial" fill="' . $_POST['foreground_color'] .'">' . e($data) . '</text>';

                /* inject the text before closing </svg> */
                $svg = str_replace('height="' . $_POST['height'] . '" viewBox', 'height="' . ($_POST['height'] + 15) . '" preserveAspectRatio="xMinYMin meet" viewBox', $svg);
                $svg = str_replace('</svg>', $svg_text_element . '</svg>', $svg);
            }

        } catch (\Exception $exception) {
            Response::json($exception->getMessage(), 'error');
        }

        $image_data = 'data:image/svg+xml;base64,' . base64_encode($svg);

        Response::json('', 'success', ['data' => $image_data, 'embedded_data' => $data]);

    }

    private function filter_barcode_data($barcode_data, $barcode_type) {
        $barcode_patterns = [
            'C32' => '/[^0-9A-Z ]/',
            'C39' => '/[^0-9A-Z\-.* $\/+%]/',
            'C39PLUS' => '/[^0-9A-Z\-.* $\/+%]/',
            'C39E' => '/[^0-9A-Z\-.* $\/+%]/',
            'C39EPLUS' => '/[^0-9A-Z\-.* $\/+%]/',
            'C93' => '/[^0-9A-Z\- $%+\/]/',
            'S25' => '/[^0-9]/',
            'S25PLUS' => '/[^0-9]/',
            'I25' => '/[^0-9]/',
            'I25PLUS' => '/[^0-9]/',
            'ITF14' => '/[^0-9]/',
            'C128' => '/[^!-~]/',
            'C128A' => '/[^ !-_\\x20-\\x5F]/',
            'C128B' => '/[^ !-~]/',
            'C128C' => '/[^0-9]/',
            'EAN2' => '/[^0-9]/',
            'EAN5' => '/[^0-9]/',
            'EAN8' => '/[^0-9]/',
            'EAN13' => '/[^0-9]/',
            'UPCA' => '/[^0-9]/',
            'UPCE' => '/[^0-9]/',
            'MSI' => '/[^0-9]/',
            'MSIPLUS' => '/[^0-9]/',
            'POSTNET' => '/[^0-9]/',
            'PLANET' => '/[^0-9]/',
            'TELEPENALPHA' => '/[^A-Za-z0-9]/',
            'TELEPENNUMERIC' => '/[^0-9]/',
            'RMS4CC' => '/[^0-9A-Z]/',
            'KIX' => '/[^0-9A-Z]/',
            'IMB' => '/[^0-9A-Z]/',
            'CODABAR' => '/[^0-9A-D\-\$:\/.+]/',
            'CODE11' => '/[^0-9\-]/',
            'PHARMA' => '/[^0-9]/',
            'PHARMA2T' => '/[^0-9]/',
        ];

        /* normalize the type to match the array keys */
        $normalized_barcode_type = str_replace('+', 'PLUS', strtoupper($barcode_type));

        /* check if the barcode type is supported */
        if(!array_key_exists($normalized_barcode_type, $barcode_patterns)) {
            return false;
        }

        /* apply the specific pattern to filter out invalid characters */
        return preg_replace($barcode_patterns[$normalized_barcode_type], '', $barcode_data);
    }
}
