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

class AccountDelete extends Controller {

    public function index() {

        \Altum\Authentication::guard();

        if(!empty($_POST)) {

            //ALTUMCODE:DEMO if(DEMO) if($this->user->user_id == 1) Alerts::add_error('Please create an account on the demo to test out this function.');

            /* Check for any errors */
            if(!\Altum\Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            if(!password_verify($_POST['current_password'], $this->user->password)) {
                Alerts::add_field_error('current_password', l('account.error_message.invalid_current_password'));
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

                /* Delete the user */
                (new User())->delete($this->user->user_id);

                /* Send notification to admin if needed */
                if(settings()->email_notifications->delete_user && !empty(settings()->email_notifications->emails)) {
                    $email_template = get_email_template(
                        [],
                        l('global.emails.admin_delete_user_notification.subject'),
                        [
                            '{{NAME}}' => $this->user->name,
                            '{{EMAIL}}' => $this->user->email,
                            '{{SOURCE}}' => $this->user->source,
                            '{{IP}}' => $this->user->ip,
                            '{{COUNTRY_NAME}}' => $this->user->country ? get_country_from_country_code($this->user->country) : l('global.unknown'),
                            '{{CITY_NAME}}' => $this->user->city_name ?? l('global.unknown'),
                            '{{DEVICE_TYPE}}' => l('global.device.' . $this->user->device_type),
                            '{{OS_NAME}}' => $this->user->os_name,
                            '{{BROWSER_NAME}}' => $this->user->browser_name,
                            '{{ACCOUNT_AGE}}' => \Altum\Date::get_elapsed_time($this->user->datetime),
                        ],
                        l('global.emails.admin_delete_user_notification.body')
                    );

                    send_mail(explode(',', settings()->email_notifications->emails), $email_template->subject, $email_template->body);
                }

                if(settings()->internal_notifications->admins_is_enabled && settings()->internal_notifications->delete_user) {
                    db()->insert('internal_notifications', [
                        'for_who' => 'admin',
                        'from_who' => 'system',
                        'icon' => 'fas fa-user-slash',
                        'title' => l('global.notifications.delete_user.title'),
                        'description' => sprintf(l('global.notifications.delete_user.description'), $this->user->name, $this->user->email),
                        'datetime' => get_date(),
                    ]);
                }

                /* Update all websites if any */
//                if(settings()->sso->is_enabled && count((array) settings()->sso->websites)) {
//                    foreach(settings()->sso->websites as $website) {
//                        $response = \Unirest\Request::post(
//                            $website->url . 'admin-api/sso/delete',
//                            ['Authorization' => 'Bearer ' . $website->api_key],
//                            \Unirest\Request\Body::form(['email' => $this->user->email])
//                        );
//                    }
//                }

                /* Logout of the user */
                \Altum\Authentication::logout(false);

                /* Start a new session to set a deletion message */
                session_start();

                /* Set a nice success message */
                Alerts::add_success(l('account_delete.success_message'));

                redirect();

            }

        }

        /* Get the account header menu */
        $menu = new \Altum\View('partials/account_header_menu', (array) $this);
        $this->add_view_content('account_header_menu', $menu->run());

        /* Prepare the view */
        $view = new \Altum\View('account-delete/index', (array) $this);

        $this->add_view_content('content', $view->run([]));

    }

}
