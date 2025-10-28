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

defined('ALTUMCODE') || die();

class QrReader extends Controller {

    public function index() {

        if(!settings()->codes->qr_reader_is_enabled) {
            redirect('not-found');
        }

        if(!$this->user->plan_settings->qr_reader_is_enabled) {
            Alerts::add_info(l('global.info_message.plan_feature_no_access'));
            redirect();
        }

        /* Main View */
        $data = [];

        $view = new \Altum\View('qr-reader/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
