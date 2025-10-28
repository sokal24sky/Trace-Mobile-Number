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

class AdminLanguageCreate extends Controller {

    public function index() {

        /* Make sure to load up in memory the main language */
        Language::get(Language::$main_name);

        /* Default variables */
        $values = [];

        if(!empty($_POST)) {
            /* Clean some posted variables */
            $_POST['language_name'] = input_clean(preg_replace('/\s{2,}/', ' ', trim($_POST['language_name']), 64));
            $_POST['language_code'] = mb_strtolower(input_clean(preg_replace("/\s+/", '', $_POST['language_code'], 16)));
            $_POST['language_flag'] = mb_substr(trim(input_clean($_POST['language_flag'])), 0, 4, 'UTF-8');

            $_POST['status'] = (int) isset($_POST['status']);
            $_POST['order'] = (int) $_POST['order'];

            $language_content = function($language_strings) {
                return <<<ALTUM
<?php

return [
{$language_strings}
];
ALTUM;
            };

            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* Check for any errors */
            $required_fields = ['language_name', 'language_code'];
            foreach($required_fields as $field) {
                if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                    Alerts::add_field_error($field, l('global.error_message.empty_field'));
                }
            }

            if(!\Altum\Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            if(!is_writable(Language::$path)) {
                Alerts::add_error(sprintf(l('global.error_message.directory_not_writable'), Language::$path));
            }

            if(!is_writable(Language::$path . 'admin/')) {
                Alerts::add_error(sprintf(l('global.error_message.directory_not_writable'), Language::$path . 'admin/'));
            }

            if(array_key_exists($_POST['language_name'], Language::$languages)) {
                Alerts::add_error(sprintf(l('admin_languages.error_message.language_exists'), $_POST['language_name'], $_POST['language_code']));
            }

            foreach(Language::$languages as $lang) {
                if($lang['code'] == $_POST['language_code']) {
                    Alerts::add_error(sprintf(l('admin_languages.error_message.language_exists'), $_POST['language_name'], $_POST['language_code']));
                    break;
                }
            }

            /* If there are no errors, continue */
            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

                file_put_contents(Language::$path . $_POST['language_name'] . '#' . $_POST['language_code'] . '.php', $language_content("\t'direction' => 'ltr',"));
                file_put_contents(Language::$path . 'admin/' . $_POST['language_name'] . '#' . $_POST['language_code'] . '.php', $language_content(''));

                chmod(Language::$path . $_POST['language_name'] . '#' . $_POST['language_code'] . '.php', 0777);
                chmod(Language::$path . 'admin/' . $_POST['language_name'] . '#' . $_POST['language_code'] . '.php', 0777);

                /* Update all languages in the settings table */
                $settings_languages = [];
                foreach(Language::$languages as $lang) {
                    $settings_languages[$lang['name']] = [
                        'status' => $lang['name'] == $_POST['language_name'] ? $_POST['status'] : (settings()->languages->{$lang['name']}->status ?? true),
                        'order' => $lang['name'] == $_POST['language_name'] ? $_POST['order'] : (settings()->languages->{$lang['name']}->order ?? 1),
                        'language_flag' => $lang['name'] == $_POST['language_name'] ? $_POST['language_flag'] : (settings()->languages->{$lang['name']}->language_flag ?? ''),
                    ];
                }

                if(!isset($settings_languages[$_POST['language_name']])) {
                    $settings_languages[$_POST['language_name']] = [
                        'status' => $_POST['status'],
                        'order' => $_POST['order'],
                        'language_flag' => $_POST['language_flag'],
                    ];
                }

                /* Update the database */
                db()->where('`key`', 'languages')->update('settings', ['value' => json_encode($settings_languages)]);

                /* Clear the cache */
                cache()->deleteItem('settings');

                /* Set a nice success message */
                Alerts::add_success(sprintf(l('global.success_message.create1'), '<strong>' . $_POST['language_name'] . '</strong>'));

                /* Redirect */
                redirect('admin/language-update/' . replace_space_with_plus($_POST['language_name']));
            }

        }

        /* Default variables */
        $values['language_name'] = $_POST['language_name'] ?? null;
        $values['language_code'] = $_POST['language_code'] ?? null;
        $values['language_flag'] = $_POST['language_flag'] ?? null;
        $values['status'] = $_POST['status'] ?? true;
        $values['order'] = $_POST['order'] ?? 0;

        /* Main View */
        $data = [
            'values' => $values
        ];

        $view = new \Altum\View('admin/language-create/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
