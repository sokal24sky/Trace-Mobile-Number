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
use Altum\Language;

defined('ALTUMCODE') || die();

class AdminLanguages extends Controller {

    public function index() {

        /* Get usage by users */
        $users_languages = [];
        $total_users = 0;
        $result = database()->query("SELECT COUNT(*) AS `total`, `language` FROM `users` GROUP BY `language`");
        while($row = $result->fetch_object()) {
            $users_languages[$row->language] = $row->total;
            $total_users += $row->total;
        }

        /* Main View */
        $data = [
            'users_languages' => $users_languages,
            'total_users' => $total_users,
        ];

        $view = new \Altum\View('admin/languages/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function delete() {

        $language_name = isset($this->params[0]) ? $this->params[0] : null;

        //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

        if(!array_key_exists($language_name, Language::$languages)) {
            redirect('admin/languages');
        }

        $language = Language::$languages[$language_name];

        if(!\Altum\Csrf::check('global_token')) {
            Alerts::add_error(l('global.error_message.invalid_csrf_token'));
        }

        if(!is_writable(Language::$path)) {
            Alerts::add_error(sprintf(l('global.error_message.directory_not_writable'), Language::$path));
        }

        if(!is_writable(Language::$path . 'admin/')) {
            Alerts::add_error(sprintf(l('global.error_message.directory_not_writable'), Language::$path . 'admin/'));
        }

        if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

            /* Delete the language */
            /* Old handling */
            if(file_exists(Language::$path . $language['name'] . '#' . $language['code'] . '.php')) {
                unlink(Language::$path . $language['name'] . '#' . $language['code'] . '.php');
                unlink(Language::$path . 'admin/' . $language['name'] . '#' . $language['code'] . '.php');
            }

            /* New handling */
            if(file_exists(Language::$path . $language['name'] . '#' . $language['code'] . '#' . $language['status'] . '.php')) {
                unlink(Language::$path . $language['name'] . '#' . $language['code'] . '#' . $language['status'] . '.php');
                unlink(Language::$path . 'admin/' . $language['name'] . '#' . $language['code'] . '#' . $language['status'] . '.php');
            }

            /* Update all users that used this language */
            $user_fallback_language_name = $language['name'] == settings()->main->default_language ? Language::$main_name : settings()->main->default_language;
            db()->where('language', $language['name'])->update('users', ['language' => $user_fallback_language_name]);

            /* Set a nice success message */
            Alerts::add_success(sprintf(l('global.success_message.delete1'), '<strong>' . $language['name'] . '</strong>'));

        }

        redirect('admin/languages');
    }

}
