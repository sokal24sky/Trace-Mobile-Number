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

use Altum\Models\User;

defined('ALTUMCODE') || die();

class LinkRedirect extends Controller {

    public function index() {

        $link_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        if(!$link = db()->where('link_id', $link_id)->getOne('links', ['link_id', 'domain_id', 'user_id', 'url'])) {
            redirect();
        }

        $this->link_user = (new User())->get_user_by_user_id($link->user_id);

        /* Genereate the link full URL base */
        $link->full_url = (new \Altum\Models\Link())->get_link_full_url($link, $this->link_user);

        header('Location: ' . $link->full_url);

        die();

    }
}
