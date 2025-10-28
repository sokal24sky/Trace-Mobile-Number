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

defined('ALTUMCODE') || die();

class AdminPagesCategoryUpdate extends Controller {

    public function index() {

        $pages_category_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        /* Check if resource exists */
        if(!$pages_category = db()->where('pages_category_id', $pages_category_id)->getOne('pages_categories')) {
            redirect('admin/pages-categories');
        }

        if(!empty($_POST)) {
            /* Filter some of the variables */
            $_POST['url'] = input_clean(get_slug($_POST['url']), 256);
            $_POST['title'] = input_clean($_POST['title'], 256);
            $_POST['description'] = input_clean($_POST['description'], 256);
            $_POST['language'] = !empty($_POST['language']) ? input_clean($_POST['language']) : null;
            $_POST['icon'] = input_clean($_POST['icon']);
            $_POST['order'] = (int) $_POST['order'] ?? 0;

            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* Check for any errors */
            $required_fields = ['title', 'url'];
            foreach($required_fields as $field) {
                if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                    Alerts::add_field_error($field, l('global.error_message.empty_field'));
                }
            }

            if(!\Altum\Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            if(db()->where('pages_category_id', $pages_category->pages_category_id, '<>')->where('url', $_POST['url'])->where('language', $_POST['language'])->has('pages_categories')) {
                Alerts::add_field_error('url', l('admin_resources.error_message.url_exists'));
            }

            /* If there are no errors, continue */
            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

                /* Database query */
                db()->where('pages_category_id', $pages_category_id)->update('pages_categories', [
                    'url' => $_POST['url'],
                    'title' => $_POST['title'],
                    'description' => $_POST['description'],
                    'language' => $_POST['language'],
                    'icon' => $_POST['icon'],
                    'order' => $_POST['order'],
                    'last_datetime' => get_date(),
                ]);

                /* Clear the cache */
                cache()->deleteItemsByTag('pages_categories');

                /* Set a nice success message */
                Alerts::add_success(sprintf(l('global.success_message.update1'), '<strong>' . $_POST['title'] . '</strong>'));

                redirect('admin/pages-category-update/' . $pages_category->pages_category_id);

            }
        }

        /* Main View */
        $data = [
            'pages_category' => $pages_category
        ];

        $view = new \Altum\View('admin/pages-category-update/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
