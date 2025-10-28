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

namespace Altum\helpers;

defined('ALTUMCODE') || die();

class PushNotifications {

    public static function send($content, $push_subscriber) {
        if(!\Altum\Plugin::is_active('push-notifications') || !settings()->push_notifications->is_enabled) {
            return true;
        }

        /* Prepare the web push */
        $auth = [
            'VAPID' => [
                'subject' => 'mailto:hey@example.com',
                'publicKey' => settings()->push_notifications->public_key,
                'privateKey' => settings()->push_notifications->private_key,
            ],
        ];

        $web_push = new \Minishlink\WebPush\WebPush($auth);
        $web_push->setAutomaticPadding(0);
        /* Set subscriber data */
        $subscriber = [
            'endpoint' => $push_subscriber->endpoint,
            'expirationTime' => null,
            'keys' => json_decode($push_subscriber->keys, true)
        ];

        /* Send the web push */
        if(settings()->push_notifications->icon) {
            $content['icon'] = \Altum\Uploads::get_full_url('push_notifications_icon') .  settings()->push_notifications->icon;
            $content['badge'] = \Altum\Uploads::get_full_url('push_notifications_icon') .  settings()->push_notifications->icon;
        }

        $report = $web_push->sendOneNotification(
            \Minishlink\WebPush\Subscription::create($subscriber),
            json_encode($content),
            ['TTL' => 5000]
        );

        /* Unsubscribe if push failed */
        return $report->getResponse()->getStatusCode() == 201;
    }

}
