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

class AdminPushNotificationUpdate extends Controller {

    public function index() {

        $push_notification_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        if(!$push_notification = db()->where('push_notification_id', $push_notification_id)->getOne('push_notifications')) {
            redirect('admin/push-notifications');
        }

        if($push_notification->status == 'processing') {
            Alerts::add_error(l('admin_push_notification_update.error_message.processing'));
            redirect('admin/push-notifications');
        }

        $push_notification->push_subscribers_ids = implode(',', json_decode($push_notification->push_subscribers_ids));

        if(!empty($_POST)) {
            /* Filter some of the variables */
            $_POST['title'] = input_clean($_POST['title'], 64);
            $_POST['description'] = input_clean($_POST['description'], 128);
            $_POST['url'] = get_url($_POST['url'], 512);
            $_POST['segment'] = in_array($_POST['segment'], ['all', 'custom', 'filter']) ? input_clean($_POST['segment']) : 'all';

            $_POST['push_subscribers_ids'] = trim($_POST['push_subscribers_ids'] ?? '');
            $_POST['push_subscribers_ids'] = array_filter(array_map('intval', explode(',', $_POST['push_subscribers_ids'])));
            $_POST['push_subscribers_ids'] = array_values(array_unique($_POST['push_subscribers_ids']));
            $_POST['push_subscribers_ids'] = $_POST['push_subscribers_ids'] ?: [0];

            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            if(!\Altum\Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            $required_fields = ['title', 'description'];
            foreach($required_fields as $field) {
                if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                    Alerts::add_field_error($field, l('global.error_message.empty_field'));
                }
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {
                $settings = [];

                /* Get all the users needed */
                switch($_POST['segment']) {
                    case 'all':
                        $push_subscribers = db()->get('push_subscribers', null, ['push_subscriber_id', 'user_id']);
                        break;

                    case 'custom':
                        $push_subscribers = db()->where('push_subscriber_id', $_POST['push_subscribers_ids'], 'IN')->get('push_subscribers', null, ['push_subscriber_id']);
                        break;

                    case 'filter':

                        $query = db();

                        $has_filters = false;

                        /* Is registered */
                        if(isset($_POST['filters_is_registered'])) {
                            $has_filters = true;

                            if(isset($_POST['filters_is_registered']['yes']) && !isset($_POST['filters_is_registered']['no'])) {
                                $query->where('user_id', NULL, 'IS NOT');
                            }

                            if(isset($_POST['filters_is_registered']['no']) && !isset($_POST['filters_is_registered']['yes'])) {
                                $query->where('user_id', NULL, 'IS');
                            }

                            if(isset($_POST['filters_is_registered']['no']) && isset($_POST['filters_is_registered']['yes'])) {
                                $query->where('user_id', NULL, 'IS NOT');
                                $query->orWhere('user_id', NULL, 'IS NOT');
                            }
                        }

                        /* Countries */
                        if(isset($_POST['filters_countries'])) {
                            $has_filters = true;
                            $query->where('country', $_POST['filters_countries'], 'IN');
                        }

                        /* Continents */
                        if(isset($_POST['filters_continents'])) {
                            $has_filters = true;
                            $query->where('continent_code', $_POST['filters_continents'], 'IN');
                        }

                        /* Device type */
                        if(isset($_POST['filters_device_type'])) {
                            $has_filters = true;
                            $query->where('device_type', $_POST['filters_device_type'], 'IN');
                        }

                        $push_subscribers = $has_filters ? $query->get('push_subscribers', null, ['push_subscriber_id']) : [];

                        break;

                }

                /* Get all the contacts ids */
                $push_subscribers_ids = array_column($push_subscribers, 'push_subscriber_id');
                $push_subscribers_count = count($push_subscribers_ids);

                /* Free memory */
                unset($push_subscribers);

                if($push_notification->status == 'sent') {
                    /* Database query */
                    db()->where('push_notification_id', $push_notification->push_notification_id)->update('push_notifications', [
                        'last_datetime' => get_date(),
                    ]);
                }

                else {
                    /* Database query */
                    db()->where('push_notification_id', $push_notification->push_notification_id)->update('push_notifications', [
                        'title' => $_POST['title'],
                        'description' => $_POST['description'],
                        'url' => $_POST['url'],
                        'segment' => $_POST['segment'],
                        'settings' => json_encode($settings),
                        'push_subscribers_ids' => json_encode($push_subscribers_ids),
                        'sent_push_subscribers_ids' => '[]',
                        'total_push_notifications' => $push_subscribers_count,
                        'status' => isset($_POST['save']) ? 'draft' : 'processing',
                        'last_datetime' => get_date(),
                    ]);
                }

                if(isset($_POST['save'])) {
                    /* Set a nice success message */
                    Alerts::add_success(sprintf(l('admin_push_notification_create.success_message.save'), '<strong>' . $_POST['title'] . '</strong>'));
                } else {
                    /* Set a nice success message */
                    Alerts::add_success(sprintf(l('admin_push_notification_create.success_message.send'), '<strong>' . $_POST['title'] . '</strong>'));

                    redirect('admin/push-notifications');
                }

                /* Refresh the page */
                redirect('admin/push-notification-update/' . $push_notification_id);

            }

        }

        /* Main View */
        $data = [
            'push_notification_id' => $push_notification_id,
            'push_notification' => $push_notification,
        ];

        $view = new \Altum\View('admin/push-notification-update/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
