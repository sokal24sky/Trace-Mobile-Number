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
use Altum\Meta;
use Altum\Title;

defined('ALTUMCODE') || die();

class Qr extends Controller {

    public function index() {

        if(!settings()->codes->qr_codes_is_enabled) {
            redirect('not-found');
        }

        if(is_logged_in()) {
            redirect('qr-code-create');
        }

        if(!settings()->plan_guest->status) {
            Alerts::add_info(l('global.info_message.plan_feature_no_access'));
            redirect('dashboard');
        }

        $available_qr_codes = require APP_PATH . 'includes/enabled_qr_codes.php';
        $frames = require APP_PATH . 'includes/qr_codes_frames.php';
        $frames_fonts = require APP_PATH . 'includes/qr_codes_frames_text_fonts.php';
        $styles = require APP_PATH . 'includes/qr_codes_styles.php';
        $inner_eyes = require APP_PATH . 'includes/qr_codes_inner_eyes.php';
        $outer_eyes = require APP_PATH . 'includes/qr_codes_outer_eyes.php';

        $type = isset($this->params[0]) && array_key_exists($this->params[0], $available_qr_codes) ? $this->params[0] : null;

        if($type) {
            if(!$this->user->plan_settings->enabled_qr_codes->{$type}) {
                Alerts::add_info(l('global.info_message.plan_feature_no_access'));
                redirect('qr');
            }

            /* Set a custom title */
            Title::set(sprintf(l('qr.title_dynamic'), l('qr_codes.type.' . $type)));
            Meta::set_description(l('qr_codes.type.' . $type . '_description'));
            Meta::set_keywords(l('qr_codes.type.' . $type . '_meta_keywords'));

            if($type == 'url' && is_logged_in()) {
                /* Existing links */
                $links = (new \Altum\Models\Link())->get_full_links_by_user_id($this->user->user_id);
            }

            /* Process dynamic view */
            $data = [
                'available_qr_codes' => $available_qr_codes,
                'frames_fonts' => $frames_fonts,
                'frames' => $frames,
                'styles' => $styles,
                'inner_eyes' => $inner_eyes,
                'outer_eyes' => $outer_eyes,
                'links' => $links ?? [],
            ];
            $view = new \Altum\View('qr/partials/' . $type . '_form', (array) $this);
            $this->add_view_content('qr_form', $view->run($data));
        }

        /* Main View */
        $data = [
            'type' => $type,
            'available_qr_codes' => $available_qr_codes,
            'frames_fonts' => $frames_fonts,
            'frames' => $frames,
            'styles' => $styles,
            'inner_eyes' => $inner_eyes,
            'outer_eyes' => $outer_eyes,
            'links' => $links ?? [],
        ];

        $view = new \Altum\View('qr/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
