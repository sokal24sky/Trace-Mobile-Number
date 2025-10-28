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


defined('ALTUMCODE') || die();

class Broadcast extends Controller {

    public function index() {

        function return_image() {
            header('Content-Type: image/gif');
            echo base64_decode('R0lGODlhAQABAIAAAP///wAAACwAAAAAAQABAAACAkQBADs=');
            die();
        }

        if(!isset($_GET['id'])) {
            redirect();
        }

        /* Decode the base64 id */
        $id = base64_decode($_GET['id']);

        /* Parse the parameters */
        parse_str($id, $parameters);

        /* Make sure all parameters are present */
        if(!isset($parameters['broadcast_id'], $parameters['user_id'])) {
            redirect();
        }

        $parameters['broadcast_id'] = (int) $parameters['broadcast_id'];
        $parameters['user_id'] = (int) $parameters['user_id'];
        $url = isset($_GET['url']) ? get_url($_GET['url']) : null;

        /* Make sure the broadcast & user exists properly */
        if(!$broadcast = db()->where('broadcast_id', $parameters['broadcast_id'])->getOne('broadcasts')) {
            redirect();
        }

        if(!in_array($broadcast->status, ['sent', 'processing'])) {
            redirect();
        }

        $broadcast->users_ids = json_decode($broadcast->users_ids);

        if(!$user_id = db()->where('user_id', $parameters['user_id'])->getValue('users', 'user_id')) {
            redirect();
        }

        /* Make sure the user is included in the broadcast */
        if(!in_array($user_id, $broadcast->users_ids)) {
            redirect();
        }

        /* Prepare for database insertion */
        $type = $url ? 'click' : 'view';
        $target = $url ?? null;

        /* Make sure the log was not already created */
        $broadcast_statistic = db()
            ->where('broadcast_id', $parameters['broadcast_id'])
            ->where('user_id', $parameters['user_id'])
            ->where('type', $type)
            ->where('target', $target)
            ->getValue('broadcasts_statistics', 'id');

        if($broadcast_statistic && $type == 'view') {
            return_image();
        }

        if($broadcast_statistic && $type == 'click') {
            header('Location: ' . $url); die();
        }

        if($type == 'click' && !str_contains($broadcast->content, $url)) {
            redirect();
        }

        /* Insert log and update stats */
        db()->insert('broadcasts_statistics', [
            'broadcast_id' => $parameters['broadcast_id'],
            'user_id' => $parameters['user_id'],
            'type' => $type,
            'target' => $target,
            'datetime' => get_date(),
        ]);

        switch($type) {
            case 'view':
                db()->where('broadcast_id', $parameters['broadcast_id'])->update('broadcasts', [
                    'views' => db()->inc()
                ]);

                return_image();
                break;

            case 'click':
                db()->where('broadcast_id', $parameters['broadcast_id'])->update('broadcasts', [
                    'clicks' => db()->inc()
                ]);

                header('Location: ' . $url);
                break;
        }

        die();
    }

}
