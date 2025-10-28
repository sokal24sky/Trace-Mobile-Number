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
use Altum\Title;

defined('ALTUMCODE') || die();

class Barcode extends Controller {

    public function index() {

        if(!settings()->codes->barcodes_is_enabled) {
            redirect('not-found');
        }

        if(is_logged_in()) {
            redirect('barcode-create');
        }

        if(!settings()->plan_guest->status) {
            Alerts::add_info(l('global.info_message.plan_feature_no_access'));
            redirect('dashboard');
        }

        $available_barcodes = require APP_PATH . 'includes/enabled_barcodes.php';
        $type = null;

        if(isset($this->params[0])) {
            $key = str_replace('-plus', '+', $this->params[0]);
            $type = array_key_exists($key, $available_barcodes) ? $key : null;
        }

        if($type) {
            if(!$this->user->plan_settings->enabled_barcodes->{$type}) {
                Alerts::add_info(l('global.info_message.plan_feature_no_access'));
                redirect('barcode');
            }

            /* Set a custom title */
            Title::set(sprintf(l('barcode.title_dynamic'), $type));
        }

        $settings = [
            'width_scale' => 2,
            'height' => 30,
            'foreground_color' => '#000000',
            'background_color' => '#ffffff',
            'display_text' => false,
        ];

        /* Set default values */
        $settings['value'] = $settings['value'] ?? $_GET['value'] ?? null;

        $values = [
            'settings' => $settings,
        ];

        /* Prepare the view */
        $data = [
            'type' => $type,
            'values' => $values,
            'available_barcodes' => $available_barcodes,
        ];

        $view = new \Altum\View('barcode/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
