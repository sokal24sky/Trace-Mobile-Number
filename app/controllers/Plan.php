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


use Altum\Title;

defined('ALTUMCODE') || die();

class Plan extends Controller {

    public function index() {

        if(!settings()->payment->is_enabled) {
            redirect('not-found');
        }

        $type = isset($this->params[0]) && in_array($this->params[0], ['renew', 'upgrade', 'new']) ? $this->params[0] : 'new';

        /* If the user is not logged in when trying to upgrade or renew, make sure to redirect them */
        if(in_array($type, ['renew', 'upgrade']) && !is_logged_in()) {
            redirect('plan/new');
        }

        

        /* Set a custom title */
        Title::set(l('plan.header_' . $type));

        /* Plans View */
        $data = [];

        $view = new \Altum\View('partials/plans', (array) $this);

        $this->add_view_content('plans', $view->run($data));


        /* Prepare the view */
        $data = [
            'type' => $type
        ];

        $view = new \Altum\View('plan/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
