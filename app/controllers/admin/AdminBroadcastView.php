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

class AdminBroadcastView extends Controller {

    public function index() {

        $broadcast_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        if(!$broadcast = db()->where('broadcast_id', $broadcast_id)->getOne('broadcasts')) {
            redirect('admin/broadcasts');
        }

        $broadcast->users_ids = implode(',', json_decode($broadcast->users_ids));

        $start_date = (new \DateTime($_GET['start_date'] ?? $broadcast->datetime))->format('Y-m-d');
        $datetime = \Altum\Date::get_start_end_dates_new($start_date);

        /* Get statistics */
        $statistics_chart = [];
        $convert_tz_sql = get_convert_tz_sql('`datetime`', $this->user->timezone);
        $result = database()->query("
            SELECT 
                `type`,
                COUNT(*) AS `total`,
                DATE_FORMAT({$convert_tz_sql}, '{$datetime['query_date_format']}') AS `formatted_date`
            FROM 
                `broadcasts_statistics`
            WHERE 
                `broadcast_id` = {$broadcast->broadcast_id} 
              AND {$convert_tz_sql} BETWEEN '{$datetime['query_start_date']}' 
              AND '{$datetime['query_end_date']}' 
            GROUP BY 
                `formatted_date`,
                `type`
        ");

        while($row = $result->fetch_object()) {
            $row->formatted_date = $datetime['process']($row->formatted_date, true);

            $statistics_chart[$row->formatted_date] =
                isset($statistics_chart[$row->formatted_date]) ?
                [
                    'clicks' => $statistics_chart[$row->formatted_date]['clicks'] + ($row->type == 'click' ? $row->total : 0),
                    'views' => $statistics_chart[$row->formatted_date]['views'] + ($row->type == 'view' ? $row->total : 0),
                ] :
                [
                    'clicks' => $row->type == 'click' ? $row->total : 0,
                    'views' => $row->type == 'view' ? $row->total : 0
                ];
        }

        $statistics_chart = get_chart_data($statistics_chart);

        /* Get last views */
        $users = [];
        $users_result = database()->query("
            SELECT
                `users`.`user_id`, `users`.`name`, `users`.`email`, `users`.`avatar`, `broadcasts_statistics`.`datetime`
            FROM
                `broadcasts_statistics`
            LEFT JOIN
                `users` ON `broadcasts_statistics`.`user_id` = `users`.`user_id`
            WHERE
                `broadcast_id` = {$broadcast->broadcast_id}
                AND `broadcasts_statistics`.`type` = 'view'
            ORDER BY
                `broadcasts_statistics`.`id` DESC
            LIMIT 5
        ");
        while($row = $users_result->fetch_object()) {
            $users[] = $row;
        }

        /* Get link clicks */
        $clicks = [];
        $clicks_result = database()->query("
            SELECT
                `target`, COUNT(*) AS `clicks`
            FROM
                `broadcasts_statistics`
            WHERE
                `broadcast_id` = {$broadcast->broadcast_id}
                AND `type` = 'click'
                AND `target` IS NOT NULL
            GROUP BY
                `target`
        ");
        while($row = $clicks_result->fetch_object()) {
            $clicks[] = $row;
        }

        /* Main View */
        $data = [
            'broadcast_id' => $broadcast_id,
            'broadcast' => $broadcast,
            'datetime' => $datetime,
            'statistics_chart' => $statistics_chart,
            'clicks' => $clicks,
            'users' => $users,
        ];

        $view = new \Altum\View('admin/broadcast-view/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
