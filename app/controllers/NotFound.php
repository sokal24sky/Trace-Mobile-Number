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

defined('ALTUMCODE') || die();

class NotFound extends Controller {

    public function index() {

        /* Custom 404 redirect if set */
        if(!empty(settings()->main->not_found_url)) {
            header('Location: ' . settings()->main->not_found_url); die();
        }

        header('HTTP/1.0 404 Not Found');

        $view = new \Altum\View('notfound/index', (array) $this);

        $this->add_view_content('content', $view->run());

    }

}
