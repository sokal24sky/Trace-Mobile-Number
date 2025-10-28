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

class ApiDocumentation extends Controller {

    public function index() {

        if(!settings()->main->api_is_enabled) {
            redirect('not-found');
        }

        $endpoint = isset($this->params[0]) ? query_clean(str_replace('-', '_', $this->params[0])) : null;

        if($endpoint) {
            if(!file_exists(THEME_PATH . 'views/api-documentation/' . $endpoint . '.php')) {
                redirect('not-found');
            }

            $title = match($endpoint) {
                'statistics' => l('links_statistics.title'),
                'users_logs' => l('account_logs.title'),
                'payments' => l('account_payments.title'),
                'user' => l('api_documentation.user'),
                'team_members' => l('api_documentation.team_members'),
                'teams_member' => l('api_documentation.teams_member'),
                default => l($endpoint . '.title')
            };

            Title::set(sprintf(l('api_documentation.title_dynamic'), $title));

            /* Prepare the view */
            $view = new \Altum\View('api-documentation/' . $endpoint, (array) $this);
        } else {
            /* Prepare the view */
            $view = new \Altum\View('api-documentation/index', (array) $this);
        }



        $this->add_view_content('content', $view->run());

    }
}


