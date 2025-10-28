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

namespace Altum;

use Altum\Traits\Paramsable;

defined('ALTUMCODE') || die();

class View {
    use Paramsable;

    public $view;
    public $view_path;

    public function __construct($view, Array $params = [], $is_full_path = false) {

        $this->view = $view;
        $this->view_path = $is_full_path ? $view . '.php' : THEME_PATH . 'views/' . $view . '.php';

        $this->add_params($params);

    }

    public function run($data = []) {

        $data = (object) $data;

        ob_start();

        require $this->view_path;

        return ob_get_clean();
    }

}
