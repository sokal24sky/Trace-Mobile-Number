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
use Altum\Models\User;

defined('ALTUMCODE') || die();

class AccountPlan extends Controller {

    public function index() {

        \Altum\Authentication::guard();

        /* Get the account header menu */
        $menu = new \Altum\View('partials/account_header_menu', (array) $this);
        $this->add_view_content('account_header_menu', $menu->run());

        /* Prepare the view */
        $view = new \Altum\View('account-plan/index', (array) $this);

        $this->add_view_content('content', $view->run());

    }

    public function cancel_subscription() {

        \Altum\Authentication::guard();

        if(!\Altum\Csrf::check()) {
            Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            redirect('account-plan');
        }

        if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

            try {
                (new User())->cancel_subscription($this->user->user_id);
            } catch (\Exception $exception) {
                Alerts::add_error($exception->getCode() . ':' . $exception->getMessage());
                redirect('account-plan');
            }

            /* Set a nice success message */
            Alerts::add_success(l('account_plan.success_message.subscription_canceled'));

            redirect('account-plan');

        }

    }

}
