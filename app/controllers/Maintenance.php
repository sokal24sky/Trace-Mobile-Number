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

namespace Altum\controllers;

defined('ALTUMCODE') || die();

class Maintenance extends Controller {

    public function index() {

        if(!settings()->main->maintenance_is_enabled) {
            redirect();
        }

        header('HTTP/1.1 503 Service Unavailable');
        header('Retry-After: 3600');

        /* Prepare the view */
        $data = [];

        $view = new \Altum\View('maintenance/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
