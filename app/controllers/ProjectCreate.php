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

class ProjectCreate extends Controller {

    public function index() {

        if(!settings()->links->projects_is_enabled) {
            redirect('not-found');
        }

        \Altum\Authentication::guard();

        /* Team checks */
        if(\Altum\Teams::is_delegated() && !\Altum\Teams::has_access('create.projects')) {
            Alerts::add_info(l('global.info_message.team_no_access'));
            redirect('projects');
        }

        /* Check for the plan limit */
        $total_rows = database()->query("SELECT COUNT(*) AS `total` FROM `projects` WHERE `user_id` = {$this->user->user_id}")->fetch_object()->total ?? 0;

        if($this->user->plan_settings->projects_limit != -1 && $total_rows >= $this->user->plan_settings->projects_limit) {
            Alerts::add_info(l('global.info_message.plan_feature_limit'));
            redirect('projects');
        }

        if(!empty($_POST)) {
            $_POST['name'] = trim(query_clean($_POST['name']));
            $_POST['color'] = !verify_hex_color($_POST['color']) ? '#000000' : $_POST['color'];

            //ALTUMCODE:DEMO if(DEMO) if($this->user->user_id == 1) Alerts::add_error('Please create an account on the demo to test out this function.');

            /* Check for any errors */
            $required_fields = ['name', 'color'];
            foreach($required_fields as $field) {
                if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                    Alerts::add_field_error($field, l('global.error_message.empty_field'));
                }
            }

            if(!\Altum\Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

                /* Database query */
                db()->insert('projects', [
                    'user_id' => $this->user->user_id,
                    'name' => $_POST['name'],
                    'color' => $_POST['color'],
                    'datetime' => get_date(),
                ]);

                /* Set a nice success message */
                Alerts::add_success(sprintf(l('global.success_message.create1'), '<strong>' . $_POST['name'] . '</strong>'));

                /* Clear the cache */
                cache()->deleteItem('projects?user_id=' . $this->user->user_id);
                cache()->deleteItem('projects_total?user_id=' . $this->user->user_id);

                redirect('projects');
            }
        }

        /* Generate random nice looking hex color */
        function generate_random_color() {
            /* Generate random hue */
            $hue = mt_rand(0, 360);

            /* Keep saturation and lightness balanced */
            $saturation = mt_rand(60, 80) / 100; /* rich but not too intense */
            $lightness = mt_rand(45, 60) / 100; /* middle brightness range */

            /* Convert HSL to RGB */
            $chroma = (1 - abs(2 * $lightness - 1)) * $saturation;
            $x = $chroma * (1 - abs(fmod($hue / 60, 2) - 1));
            $m = $lightness - ($chroma / 2);

            if ($hue < 60) {
                $red = $chroma; $green = $x; $blue = 0;
            } elseif ($hue < 120) {
                $red = $x; $green = $chroma; $blue = 0;
            } elseif ($hue < 180) {
                $red = 0; $green = $chroma; $blue = $x;
            } elseif ($hue < 240) {
                $red = 0; $green = $x; $blue = $chroma;
            } elseif ($hue < 300) {
                $red = $x; $green = 0; $blue = $chroma;
            } else {
                $red = $chroma; $green = 0; $blue = $x;
            }

            $red = ($red + $m) * 255;
            $green = ($green + $m) * 255;
            $blue = ($blue + $m) * 255;

            /* Convert to hex */
            return sprintf('#%02X%02X%02X', $red, $green, $blue);
        }

        $values = [
            'name' => $_POST['name'] ?? '',
            'color' => $_POST['color'] ?? generate_random_color(),
        ];

        /* Prepare the view */
        $data = [
            'values' => $values
        ];

        $view = new \Altum\View('project-create/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
