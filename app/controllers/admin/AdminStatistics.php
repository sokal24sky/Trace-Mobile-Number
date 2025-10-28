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

use Altum\Title;

defined('ALTUMCODE') || die();

class AdminStatistics extends Controller {
    public $type;
    public $datetime;

    public function index() {

        $this->type = isset($this->params[0]) && method_exists($this, $this->params[0]) ? input_clean($this->params[0]) : 'growth';

        $this->datetime = \Altum\Date::get_start_end_dates_new();

        /* Process only data that is needed for that specific page */
        $type_data = $this->{$this->type}();

        /* Set a custom title */
        $dynamic_title = l('admin_statistics.' . $this->type . '.header', null, true) ?? l('admin_' . $this->type . '.title');
        Title::set(sprintf(l('admin_statistics.title'), $dynamic_title));

        /* Main View */
        $data = [
            'type' => $this->type,
            'datetime' => $this->datetime
        ];
        $data = array_merge($data, $type_data);

        $view = new \Altum\View('admin/statistics/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    protected function database() {
        //ALTUMCODE:DEMO if(DEMO) { \Altum\Alerts::add_error('This command is blocked on the demo.'); redirect('admin/statistics'); };

        /* Database details */
        $database_name = DATABASE_NAME;
        $tables = [];
        $result = database()->query("
            SELECT
                TABLE_NAME AS `table`,
                ROUND((DATA_LENGTH + INDEX_LENGTH)) AS `bytes`,
                TABLE_ROWS as 'rows'
            FROM
                information_schema.TABLES
            WHERE
                TABLE_SCHEMA = '{$database_name}'
            ORDER BY
                (DATA_LENGTH + INDEX_LENGTH)
            DESC;
        ");
        while($row = $result->fetch_object()) {

            $tables[] = $row;

        }

        return [
            'tables' => $tables,
        ];
    }

    protected function local_files() {
        //ALTUMCODE:DEMO if(DEMO) { \Altum\Alerts::add_error('This command is blocked on the demo.'); redirect('admin/statistics'); };

        $base_directory = UPLOADS_PATH;
        $folders = [];
        $total_statistics = [
            'total_files' => 0,
            'total_size' => 0,
        ];

        /* List only the main folders inside the base directory */
        foreach (new \DirectoryIterator($base_directory) as $folder_info) {
            if ($folder_info->isDot() || !$folder_info->isDir()) {
                continue;
            }

            $folder_name = $folder_info->getFilename();
            $folder_path = $base_directory . DIRECTORY_SEPARATOR . $folder_name;

            $folders[$folder_name] = [
                'total_files'      => 0,
                'total_size'       => 0,
                'extensions'       => [],
            ];

            /* Only iterate files directly inside the current main folder */
            foreach (new \DirectoryIterator($folder_path) as $file_info) {
                if ($file_info->isDot() || !$file_info->isFile()) {
                    continue;
                }

                $file_name = $file_info->getFilename();

                if($file_name == 'altumcode.com') {
                    continue;
                }

                $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                $file_size = $file_info->getSize();

                $folders[$folder_name]['total_files'] += 1;
                $folders[$folder_name]['total_size'] += $file_size;
                $folders[$folder_name]['extensions'][$file_extension] = ($folders[$folder_name]['extensions'][$file_extension] ?? 0) + 1;

                $total_statistics['total_files'] += 1;
                $total_statistics['total_size'] += $file_size;
            }

            uasort($folders, function ($folder_a, $folder_b) {
                /* Sort folders by total_size descending */
                if ($folder_a['total_size'] == $folder_b['total_size']) {
                    return 0;
                }
                return ($folder_a['total_size'] > $folder_b['total_size']) ? -1 : 1;
            });
        }

        return [
            'folders' => $folders,
            'total_statistics' => $total_statistics,
        ];
    }

    protected function growth() {

        $total = ['users' => 0, 'users_logs' => 0];

        $convert_tz_sql = get_convert_tz_sql('`datetime`', $this->user->timezone);

        /* Users */
        $users_chart = [];
        $result = database()->query("
            SELECT
                 COUNT(*) AS `total`,
                 DATE_FORMAT({$convert_tz_sql}, '{$this->datetime['query_date_format']}') AS `formatted_date`
            FROM
                 `users`
            WHERE
                {$convert_tz_sql} BETWEEN '{$this->datetime['query_start_date']}' AND '{$this->datetime['query_end_date']}'
            GROUP BY
                `formatted_date`
            ORDER BY
                `formatted_date`
        ");
        while($row = $result->fetch_object()) {
            $row->formatted_date = $this->datetime['process']($row->formatted_date, true);

            $users_chart[$row->formatted_date] = [
                'users' => $row->total
            ];

            $total['users'] += $row->total;
        }

        $users_chart = get_chart_data($users_chart);

        /* Users logs */
        $users_logs_chart = [];
        $result = database()->query("
            SELECT
                 COUNT(DISTINCT `user_id`) AS `total`,
                 DATE_FORMAT({$convert_tz_sql}, '{$this->datetime['query_date_format']}') AS `formatted_date`
            FROM
                 `users_logs`
            WHERE
                {$convert_tz_sql} BETWEEN '{$this->datetime['query_start_date']}' AND '{$this->datetime['query_end_date']}'
            GROUP BY
                `formatted_date`
            ORDER BY
                `formatted_date`
        ");
        while($row = $result->fetch_object()) {
            $row->formatted_date = $this->datetime['process']($row->formatted_date, true);

            $users_logs_chart[$row->formatted_date] = [
                'users_logs' => $row->total
            ];

            $total['users_logs'] += $row->total;
        }

        $users_logs_chart = get_chart_data($users_logs_chart);

        return [
            'total' => $total,
            'users_chart' => $users_chart,
            'users_logs_chart' => $users_logs_chart,
        ];
    }

    protected function users_map() {

        $total = ['continents' => 0, 'countries' => 0, 'cities' => 0,];

        /* Continents */
        $continents = [];
        $result = database()->query("
            SELECT
                 COUNT(*) AS `total`,
                 `continent_code`
            FROM
                 `users`
            WHERE
                `datetime` BETWEEN '{$this->datetime['query_start_date']}' AND '{$this->datetime['query_end_date']}'
            GROUP BY
                `continent_code`
            ORDER BY
                `total` DESC
        ");
        while($row = $result->fetch_object()) {
            $continents[$row->continent_code] = $row->total;
            $total['continents'] += $row->total;
        }

        /* Countries */
        $countries_map = [];
        $countries = [];
        $result = database()->query("
            SELECT
                 COUNT(*) AS `total`,
                 `country`
            FROM
                 `users`
            WHERE
                `datetime` BETWEEN '{$this->datetime['query_start_date']}' AND '{$this->datetime['query_end_date']}'
            GROUP BY
                `country`
            ORDER BY
                `total` DESC
        ");
        while($row = $result->fetch_object()) {
            $countries[$row->country] = $row->total;
            $countries_map[$row->country] = ['users' => $row->total];
            $total['countries'] += $row->total;
        }

        /* Cities */
        $cities = [];
        $result = database()->query("
            SELECT
                 COUNT(*) AS `total`,
                 `country`,
                 `city_name`
            FROM
                 `users`
            WHERE
                `datetime` BETWEEN '{$this->datetime['query_start_date']}' AND '{$this->datetime['query_end_date']}'
            GROUP BY
                `country`,
                `city_name`
            ORDER BY
                `total` DESC
        ");
        while($row = $result->fetch_object()) {
            $cities[$row->country . '#' . $row->city_name] = $row->total;
            $total['cities'] += $row->total;
        }

        return [
            'continents' => $continents,
            'countries' => $countries,
            'countries_map' => $countries_map,
            'cities' => $cities,
            'total' => $total,
        ];
    }

    protected function users() {

        $total = ['devices' => 0, 'sources' => 0, 'plans' => 0, 'operating_systems' => 0, 'browsers' => 0,];

        /* Device types */
        $devices = [];
        $result = database()->query("
            SELECT
                 COUNT(*) AS `total`,
                 `device_type`
            FROM
                 `users`
            WHERE
                `datetime` BETWEEN '{$this->datetime['query_start_date']}' AND '{$this->datetime['query_end_date']}'
            GROUP BY
                `device_type`
            ORDER BY
                `total` DESC
        ");
        while($row = $result->fetch_object()) {
            $devices[$row->device_type] = $row->total;
            $total['devices'] += $row->total;
        }

        /* Operating systems */
        $operating_systems = [];
        $result = database()->query("
            SELECT
                 COUNT(*) AS `total`,
                 `os_name`
            FROM
                 `users`
            WHERE
                `datetime` BETWEEN '{$this->datetime['query_start_date']}' AND '{$this->datetime['query_end_date']}'
            GROUP BY
                `os_name`
            ORDER BY
                `total` DESC
        ");
        while($row = $result->fetch_object()) {
            $operating_systems[$row->os_name] = $row->total;
            $total['operating_systems'] += $row->total;
        }

        /* Browsers */
        $browsers = [];
        $result = database()->query("
            SELECT
                 COUNT(*) AS `total`,
                 `browser_name`
            FROM
                 `users`
            WHERE
                `datetime` BETWEEN '{$this->datetime['query_start_date']}' AND '{$this->datetime['query_end_date']}'
            GROUP BY
                `browser_name`
            ORDER BY
                `total` DESC
        ");
        while($row = $result->fetch_object()) {
            $browsers[$row->browser_name] = $row->total;
            $total['browsers'] += $row->total;
        }

        /* Sources */
        $sources = [];
        $result = database()->query("
            SELECT
                 COUNT(*) AS `total`,
                 `source`
            FROM
                 `users`
            WHERE
                `datetime` BETWEEN '{$this->datetime['query_start_date']}' AND '{$this->datetime['query_end_date']}'
            GROUP BY
                `source`
            ORDER BY
                `total` DESC
        ");
        while($row = $result->fetch_object()) {
            $sources[$row->source] = $row->total;
            $total['sources'] += $row->total;
        }

        /* Plans */
        $plans = [];
        $result = database()->query("
            SELECT
                 COUNT(*) AS `total`,
                 `plan_id`
            FROM
                 `users`
            WHERE
                `datetime` BETWEEN '{$this->datetime['query_start_date']}' AND '{$this->datetime['query_end_date']}'
            GROUP BY
                `plan_id`
            ORDER BY
                `total` DESC
        ");
        while($row = $result->fetch_object()) {
            $plans[$row->plan_id] = $row->total;
            $total['plans'] += $row->total;
        }

        return [
            'devices' => $devices,
            'operating_systems' => $operating_systems,
            'browsers' => $browsers,
            'sources' => $sources,
            'plans' => $plans,
            'total' => $total,
        ];
    }

    protected function payments() {

        $total = ['total_amount' => 0, 'total_payments' => 0];

        $convert_tz_sql = get_convert_tz_sql('`datetime`', $this->user->timezone);

        $payments_chart = [];
        $result = database()->query("SELECT COUNT(*) AS `total_payments`, DATE_FORMAT({$convert_tz_sql}, '{$this->datetime['query_date_format']}') AS `formatted_date`, TRUNCATE(SUM(`total_amount_default_currency`), 2) AS `total_amount` FROM `payments` WHERE {$convert_tz_sql} BETWEEN '{$this->datetime['query_start_date']}' AND '{$this->datetime['query_end_date']}' GROUP BY `formatted_date`");
        while($row = $result->fetch_object()) {
            $row->formatted_date = $this->datetime['process']($row->formatted_date, true);

            $payments_chart[$row->formatted_date] = [
                'total_amount' => $row->total_amount,
                'total_payments' => $row->total_payments
            ];

            $total['total_amount'] += $row->total_amount;
            $total['total_payments'] += $row->total_payments;
        }

        $payments_chart = get_chart_data($payments_chart);

        /* Payment processors */
        $processors = [];
        $result = database()->query("
            SELECT
                 COUNT(*) AS `total`,
                 `processor`,
                 TRUNCATE(SUM(`total_amount_default_currency`), 2) AS `total_amount`
            FROM
                 `payments`
            WHERE
                `datetime` BETWEEN '{$this->datetime['query_start_date']}' AND '{$this->datetime['query_end_date']}'
            GROUP BY
                `processor`
            ORDER BY
                `total` DESC
        ");
        while($row = $result->fetch_object()) {
            $processors[] = [
                'processor' => $row->processor,
                'total' => $row->total,
                'total_amount' => $row->total_amount,
            ];
        }

        /* Plans */
        $payments_plans = [];
        $result = database()->query("
            SELECT
                 COUNT(*) AS `total`,
                 `plan_id`,
                 TRUNCATE(SUM(`total_amount_default_currency`), 2) AS `total_amount`
            FROM
                 `payments`
            WHERE
                `datetime` BETWEEN '{$this->datetime['query_start_date']}' AND '{$this->datetime['query_end_date']}'
            GROUP BY
                `plan_id`
            ORDER BY
                `total` DESC
        ");
        while($row = $result->fetch_object()) {
            $payments_plans[] = [
                'plan_id' => $row->plan_id,
                'total' => $row->total,
                'total_amount' => $row->total_amount,
            ];
        }

        /* Payment types */
        $types = [];
        $result = database()->query("
            SELECT
                 COUNT(*) AS `total`,
                 `type`,
                 TRUNCATE(SUM(`total_amount_default_currency`), 2) AS `total_amount`
            FROM
                 `payments`
            WHERE
                `datetime` BETWEEN '{$this->datetime['query_start_date']}' AND '{$this->datetime['query_end_date']}'
            GROUP BY
                `type`
            ORDER BY
                `total` DESC
        ");
        while($row = $result->fetch_object()) {
            $types[] = [
                'type' => $row->type,
                'total' => $row->total,
                'total_amount' => $row->total_amount,
            ];
        }

        /* Payment freuqencies */
        $frequencies = [];
        $result = database()->query("
            SELECT
                 COUNT(*) AS `total`,
                 `frequency`,
                 TRUNCATE(SUM(`total_amount_default_currency`), 2) AS `total_amount`
            FROM
                 `payments`
            WHERE
                `datetime` BETWEEN '{$this->datetime['query_start_date']}' AND '{$this->datetime['query_end_date']}'
            GROUP BY
                `frequency`
            ORDER BY
                `total` DESC
        ");
        while($row = $result->fetch_object()) {
            $frequencies[] = [
                'frequency' => $row->frequency,
                'total' => $row->total,
                'total_amount' => $row->total_amount,
            ];
        }

        return [
            'total' => $total,
            'payments_chart' => $payments_chart,
            'payments_plans' => $payments_plans,
            'frequencies' => $frequencies,
            'types' => $types,
            'processors' => $processors,
            'payment_processors' => require APP_PATH . 'includes/payment_processors.php',
            'plans' => (new \Altum\Models\Plan())->get_plans(),
        ];

    }

    protected function redeemed_codes() {

        $total = ['discount_codes' => 0, 'redeemable_codes' => 0];

        $convert_tz_sql = get_convert_tz_sql('`datetime`', $this->user->timezone);

        $chart = [];
        $result = database()->query("SELECT `type`, COUNT(`type`) AS `total`, DATE_FORMAT({$convert_tz_sql}, '{$this->datetime['query_date_format']}') AS `formatted_date` FROM `redeemed_codes` WHERE {$convert_tz_sql} BETWEEN '{$this->datetime['query_start_date']}' AND '{$this->datetime['query_end_date']}' GROUP BY `formatted_date`, `type`");
        while($row = $result->fetch_object()) {
            $row->formatted_date = $this->datetime['process']($row->formatted_date, true);

            if(isset($chart[$row->formatted_date])) {
                $chart[$row->formatted_date] = [
                    'discount' => $row->type == 'discount' ? $chart[$row->formatted_date]['discount'] + $row->total : $chart[$row->formatted_date]['discount'],
                    'redeemable' => $row->type == 'redeemable' ? $chart[$row->formatted_date]['redeemable'] + $row->total : $chart[$row->formatted_date]['redeemable'],
                ];
            } else {
                $chart[$row->formatted_date] = [
                    'discount' => $row->type == 'discount' ? $row->total : 0,
                    'redeemable' => $row->type == 'redeemable' ? $row->total : 0,
                ];
            }

            $total['discount_codes'] += $row->type == 'discount' ? $row->total : 0;
            $total['redeemable_codes'] += $row->type == 'redeemable' ? $row->total : 0;
        }

        $chart = get_chart_data($chart);

        return [
            'total' => $total,
            'chart' => $chart,
        ];

    }

    protected function affiliates_commissions() {

        $total = ['amount' => 0, 'total_affiliates_commissions' => 0];

        $convert_tz_sql = get_convert_tz_sql('`datetime`', $this->user->timezone);

        $affiliates_commissions_chart = [];
        $result = database()->query("SELECT COUNT(*) AS `total_affiliates_commissions`, DATE_FORMAT({$convert_tz_sql}, '{$this->datetime['query_date_format']}') AS `formatted_date`, TRUNCATE(SUM(`amount`), 2) AS `amount` FROM `affiliates_commissions` WHERE {$convert_tz_sql} BETWEEN '{$this->datetime['query_start_date']}' AND '{$this->datetime['query_end_date']}' GROUP BY `formatted_date`");
        while($row = $result->fetch_object()) {
            $row->formatted_date = $this->datetime['process']($row->formatted_date, true);

            $affiliates_commissions_chart[$row->formatted_date] = [
                'amount' => $row->amount,
                'total_affiliates_commissions' => $row->total_affiliates_commissions
            ];

            $total['amount'] += $row->amount;
            $total['total_affiliates_commissions'] += $row->total_affiliates_commissions;
        }

        $affiliates_commissions_chart = get_chart_data($affiliates_commissions_chart);

        return [
            'total' => $total,
            'affiliates_commissions_chart' => $affiliates_commissions_chart
        ];

    }
    protected function affiliates_withdrawals() {

        $total = ['amount' => 0, 'total_affiliates_withdrawals' => 0];

        $convert_tz_sql = get_convert_tz_sql('`datetime`', $this->user->timezone);

        $affiliates_withdrawals_chart = [];
        $result = database()->query("SELECT COUNT(*) AS `total_affiliates_withdrawals`, DATE_FORMAT({$convert_tz_sql}, '{$this->datetime['query_date_format']}') AS `formatted_date`, TRUNCATE(SUM(`amount`), 2) AS `amount` FROM `affiliates_withdrawals` WHERE {$convert_tz_sql} BETWEEN '{$this->datetime['query_start_date']}' AND '{$this->datetime['query_end_date']}' GROUP BY `formatted_date`");
        while($row = $result->fetch_object()) {
            $row->formatted_date = $this->datetime['process']($row->formatted_date, true);

            $affiliates_withdrawals_chart[$row->formatted_date] = [
                'amount' => $row->amount,
                'total_affiliates_withdrawals' => $row->total_affiliates_withdrawals
            ];

            $total['amount'] += $row->amount;
            $total['total_affiliates_withdrawals'] += $row->total_affiliates_withdrawals;
        }

        $affiliates_withdrawals_chart = get_chart_data($affiliates_withdrawals_chart);

        return [
            'total' => $total,
            'affiliates_withdrawals_chart' => $affiliates_withdrawals_chart
        ];

    }

    protected function broadcasts() {

        $total = ['broadcasts' => 0, 'sent_emails' => 0];

        $convert_tz_sql = get_convert_tz_sql('`datetime`', $this->user->timezone);

        $broadcasts_chart = [];
        $result = database()->query("SELECT COUNT(*) AS `total`, DATE_FORMAT({$convert_tz_sql}, '{$this->datetime['query_date_format']}') AS `formatted_date`, SUM(`sent_emails`) AS `sent_emails` FROM `broadcasts` WHERE {$convert_tz_sql} BETWEEN '{$this->datetime['query_start_date']}' AND '{$this->datetime['query_end_date']}' GROUP BY `formatted_date`");
        while($row = $result->fetch_object()) {
            $row->formatted_date = $this->datetime['process']($row->formatted_date, true);

            $broadcasts_chart[$row->formatted_date] = [
                'broadcasts' => $row->total,
                'sent_emails' => $row->sent_emails,
            ];

            $total['broadcasts'] += $row->total;
            $total['sent_emails'] += $row->sent_emails;
        }

        $broadcasts_chart = get_chart_data($broadcasts_chart);

        return [
            'total' => $total,
            'broadcasts_chart' => $broadcasts_chart,
        ];

    }

    protected function internal_notifications() {

        $total = ['internal_notifications' => 0, 'read_notifications' => 0];

        $convert_tz_sql = get_convert_tz_sql('`datetime`', $this->user->timezone);

        $internal_notifications_chart = [];
        $result = database()->query("SELECT COUNT(*) AS `total`, DATE_FORMAT({$convert_tz_sql}, '{$this->datetime['query_date_format']}') AS `formatted_date`, SUM(`is_read`) AS `read_notifications` FROM `internal_notifications` WHERE {$convert_tz_sql} BETWEEN '{$this->datetime['query_start_date']}' AND '{$this->datetime['query_end_date']}' GROUP BY `formatted_date`");
        while($row = $result->fetch_object()) {
            $row->formatted_date = $this->datetime['process']($row->formatted_date, true);

            $internal_notifications_chart[$row->formatted_date] = [
                'internal_notifications' => $row->total,
                'read_notifications' => $row->read_notifications,
            ];

            $total['internal_notifications'] += $row->total;
            $total['read_notifications'] += $row->read_notifications;
        }

        $internal_notifications_chart = get_chart_data($internal_notifications_chart);

        return [
            'total' => $total,
            'internal_notifications_chart' => $internal_notifications_chart,
        ];

    }

    protected function push_notifications() {

        $total = ['push_notifications' => 0, 'sent_push_notifications' => 0];

        $convert_tz_sql = get_convert_tz_sql('`datetime`', $this->user->timezone);

        $push_notifications_chart = [];
        $result = database()->query("SELECT COUNT(*) AS `total`, DATE_FORMAT({$convert_tz_sql}, '{$this->datetime['query_date_format']}') AS `formatted_date`, SUM(`sent_push_notifications`) AS `sent_push_notifications` FROM `push_notifications` WHERE {$convert_tz_sql} BETWEEN '{$this->datetime['query_start_date']}' AND '{$this->datetime['query_end_date']}' GROUP BY `formatted_date`");
        while($row = $result->fetch_object()) {
            $row->formatted_date = $this->datetime['process']($row->formatted_date, true);

            $push_notifications_chart[$row->formatted_date] = [
                'push_notifications' => $row->total,
                'sent_push_notifications' => $row->sent_push_notifications,
            ];

            $total['push_notifications'] += $row->total;
            $total['sent_push_notifications'] += $row->sent_push_notifications;
        }

        $push_notifications_chart = get_chart_data($push_notifications_chart);

        return [
            'total' => $total,
            'push_notifications_chart' => $push_notifications_chart,
        ];

    }

    protected function push_subscribers() {

        $total = ['push_subscribers' => 0];

        $convert_tz_sql = get_convert_tz_sql('`datetime`', $this->user->timezone);

        $push_subscribers_chart = [];
        $result = database()->query("SELECT COUNT(*) AS `total`, DATE_FORMAT({$convert_tz_sql}, '{$this->datetime['query_date_format']}') AS `formatted_date` FROM `push_subscribers` WHERE {$convert_tz_sql} BETWEEN '{$this->datetime['query_start_date']}' AND '{$this->datetime['query_end_date']}' GROUP BY `formatted_date`");
        while($row = $result->fetch_object()) {
            $row->formatted_date = $this->datetime['process']($row->formatted_date, true);

            $push_subscribers_chart[$row->formatted_date] = [
                'push_subscribers' => $row->total,
            ];

            $total['push_subscribers'] += $row->total;
        }

        $push_subscribers_chart = get_chart_data($push_subscribers_chart);

        return [
            'total' => $total,
            'push_subscribers_chart' => $push_subscribers_chart,
        ];

    }

    protected function teams() {

        $total = ['teams' => 0];

        $convert_tz_sql = get_convert_tz_sql('`datetime`', $this->user->timezone);

        $teams_chart = [];
        $result = database()->query("SELECT COUNT(*) AS `total`, DATE_FORMAT({$convert_tz_sql}, '{$this->datetime['query_date_format']}') AS `formatted_date` FROM `teams` WHERE {$convert_tz_sql} BETWEEN '{$this->datetime['query_start_date']}' AND '{$this->datetime['query_end_date']}' GROUP BY `formatted_date`");
        while($row = $result->fetch_object()) {
            $row->formatted_date = $this->datetime['process']($row->formatted_date);

            $teams_chart[$row->formatted_date] = [
                'teams' => $row->total,
            ];

            $total['teams'] += $row->total;
        }

        $teams_chart = get_chart_data($teams_chart);

        return [
            'total' => $total,
            'teams_chart' => $teams_chart,
        ];

    }

    protected function teams_members() {

        $total = ['teams_members' => 0];

        $convert_tz_sql = get_convert_tz_sql('`datetime`', $this->user->timezone);

        $teams_members_chart = [];
        $result = database()->query("SELECT COUNT(*) AS `total`, DATE_FORMAT({$convert_tz_sql}, '{$this->datetime['query_date_format']}') AS `formatted_date` FROM `teams_members` WHERE {$convert_tz_sql} BETWEEN '{$this->datetime['query_start_date']}' AND '{$this->datetime['query_end_date']}' GROUP BY `formatted_date`");
        while($row = $result->fetch_object()) {
            $row->formatted_date = $this->datetime['process']($row->formatted_date);

            $teams_members_chart[$row->formatted_date] = [
                'teams_members' => $row->total,
            ];

            $total['teams_members'] += $row->total;
        }

        $teams_members_chart = get_chart_data($teams_members_chart);

        return [
            'total' => $total,
            'teams_members_chart' => $teams_members_chart,
        ];

    }

    protected function email_shield() {

        $total = ['valid' => 0, 'invalid' => 0];

        $convert_tz_sql = get_convert_tz_sql('`date`', $this->user->timezone);

        $chart = [];
        $result = database()->query("SELECT `valid`, `invalid`, DATE_FORMAT({$convert_tz_sql}, '{$this->datetime['query_date_format']}') AS `formatted_date` FROM `email_shield` WHERE {$convert_tz_sql} BETWEEN '{$this->datetime['query_start_date']}' AND '{$this->datetime['query_end_date']}'");
        while($row = $result->fetch_object()) {
            $row->formatted_date = $this->datetime['process']($row->formatted_date, true);

            $chart[$row->formatted_date] = [
                'valid' => $row->valid,
                'invalid' => $row->invalid,
            ];

            $total['valid'] += $row->valid;
            $total['invalid'] += $row->invalid;
        }

        $chart = get_chart_data($chart);

        return [
            'total' => $total,
            'chart' => $chart,
        ];

    }

    protected function image_optimizer() {

        $total = [
            'total' => 0,
            'saved_size' => 0,
            'average_percentage_difference' => 0,
            'rows' => 0,
        ];

        $convert_tz_sql = get_convert_tz_sql('`datetime`', $this->user->timezone);

        $chart = [];
        $result = database()->query("
            SELECT 
                COUNT(*) AS `total`,
                SUM(`original_size`) AS `original_size`,
                SUM(`new_size`) AS `new_size`,
                AVG(`percentage_difference`) AS `average_percentage_difference`, 
                DATE_FORMAT({$convert_tz_sql}, '{$this->datetime['query_date_format']}') AS `formatted_date` 
            FROM 
                `image_optimizations`
            WHERE 
                {$convert_tz_sql} BETWEEN '{$this->datetime['query_start_date']}' AND '{$this->datetime['query_end_date']}'
            GROUP BY 
                `formatted_date`
        ");

        while($row = $result->fetch_object()) {
            $row->formatted_date = $this->datetime['process']($row->formatted_date, true);

            $chart[$row->formatted_date] = [
                'total' => $row->total,
                'saved_size' => ($row->original_size - $row->new_size),
                'average_percentage_difference' => $row->average_percentage_difference,
            ];

            $total['total'] += $row->total;
            $total['saved_size'] += ($row->original_size - $row->new_size);
            $total['average_percentage_difference'] += $row->average_percentage_difference;
            $total['rows']++;
        }

        $total['average_percentage_difference'] = $total['rows'] ? $total['average_percentage_difference'] / $total['rows'] : 0;

        $chart = get_chart_data($chart);

        return [
            'total' => $total,
            'chart' => $chart,
        ];

    }

    protected function ai_qr_codes() {

        $total = ['ai_qr_codes' => 0];

        /* QR codes */
        $convert_tz_sql = get_convert_tz_sql('`datetime`', $this->user->timezone);

        $ai_qr_codes_chart = [];
        $result = database()->query("
            SELECT
                COUNT(*) AS `total`,
                DATE_FORMAT({$convert_tz_sql}, '{$this->datetime['query_date_format']}') AS `formatted_date`
            FROM
                `ai_qr_codes`
            WHERE
                {$convert_tz_sql} BETWEEN '{$this->datetime['query_start_date']}' AND '{$this->datetime['query_end_date']}'
            GROUP BY
                `formatted_date`
            ORDER BY
                `formatted_date`
        ");
        while($row = $result->fetch_object()) {
            $row->formatted_date = $this->datetime['process']($row->formatted_date, true);

            $ai_qr_codes_chart[$row->formatted_date] = [
                'ai_qr_codes' => $row->total
            ];

            $total['ai_qr_codes'] += $row->total;
        }

        $ai_qr_codes_chart = get_chart_data($ai_qr_codes_chart);

        return [
            'total' => $total,
            'ai_qr_codes_chart' => $ai_qr_codes_chart,
        ];

    }

    protected function qr_codes() {

        $total = ['qr_codes' => 0];

        /* QR codes */
        $convert_tz_sql = get_convert_tz_sql('`datetime`', $this->user->timezone);

        $qr_codes_chart = [];
        $result = database()->query("
            SELECT
                COUNT(*) AS `total`,
                DATE_FORMAT({$convert_tz_sql}, '{$this->datetime['query_date_format']}') AS `formatted_date`
            FROM
                `qr_codes`
            WHERE
                {$convert_tz_sql} BETWEEN '{$this->datetime['query_start_date']}' AND '{$this->datetime['query_end_date']}'
            GROUP BY
                `formatted_date`
            ORDER BY
                `formatted_date`
        ");
        while($row = $result->fetch_object()) {
            $row->formatted_date = $this->datetime['process']($row->formatted_date, true);

            $qr_codes_chart[$row->formatted_date] = [
                'qr_codes' => $row->total
            ];

            $total['qr_codes'] += $row->total;
        }

        $qr_codes_chart = get_chart_data($qr_codes_chart);

        return [
            'total' => $total,
            'qr_codes_chart' => $qr_codes_chart,
        ];

    }

    protected function barcodes() {

        $total = ['barcodes' => 0];

        /* QR codes */
        $convert_tz_sql = get_convert_tz_sql('`datetime`', $this->user->timezone);

        $barcodes_chart = [];
        $result = database()->query("
            SELECT
                COUNT(*) AS `total`,
                DATE_FORMAT({$convert_tz_sql}, '{$this->datetime['query_date_format']}') AS `formatted_date`
            FROM
                `barcodes`
            WHERE
                {$convert_tz_sql} BETWEEN '{$this->datetime['query_start_date']}' AND '{$this->datetime['query_end_date']}'
            GROUP BY
                `formatted_date`
            ORDER BY
                `formatted_date`
        ");
        while($row = $result->fetch_object()) {
            $row->formatted_date = $this->datetime['process']($row->formatted_date, true);

            $barcodes_chart[$row->formatted_date] = [
                'barcodes' => $row->total
            ];

            $total['barcodes'] += $row->total;
        }

        $barcodes_chart = get_chart_data($barcodes_chart);

        return [
            'total' => $total,
            'barcodes_chart' => $barcodes_chart,
        ];

    }

    protected function links() {

        $total = ['links' => 0];

        /* Links */
        $convert_tz_sql = get_convert_tz_sql('`datetime`', $this->user->timezone);

        $links_chart = [];
        $result = database()->query("
            SELECT
                COUNT(*) AS `total`,
                DATE_FORMAT({$convert_tz_sql}, '{$this->datetime['query_date_format']}') AS `formatted_date`
            FROM
                `links`
            WHERE
                {$convert_tz_sql} BETWEEN '{$this->datetime['query_start_date']}' AND '{$this->datetime['query_end_date']}'
            GROUP BY
                `formatted_date`
            ORDER BY
                `formatted_date`
        ");
        while($row = $result->fetch_object()) {
            $row->formatted_date = $this->datetime['process']($row->formatted_date, true);

            $links_chart[$row->formatted_date] = [
                'links' => $row->total
            ];

            $total['links'] += $row->total;
        }

        $links_chart = get_chart_data($links_chart);

        return [
            'total' => $total,
            'links_chart' => $links_chart,
        ];

    }

    protected function statistics() {

        $total = ['statistics' => 0];

        /* vcards statistics */
        $convert_tz_sql = get_convert_tz_sql('`datetime`', $this->user->timezone);

        $statistics_chart = [];
        $result = database()->query("
            SELECT
                COUNT(*) AS `total`,
                DATE_FORMAT({$convert_tz_sql}, '{$this->datetime['query_date_format']}') AS `formatted_date`
            FROM
                `statistics`
            WHERE
                {$convert_tz_sql} BETWEEN '{$this->datetime['query_start_date']}' AND '{$this->datetime['query_end_date']}'
            GROUP BY
                `formatted_date`
            ORDER BY
                `formatted_date`
        ");
        while($row = $result->fetch_object()) {
            $row->formatted_date = $this->datetime['process']($row->formatted_date, true);

            $statistics_chart[$row->formatted_date] = [
                'statistics' => $row->total
            ];

            $total['statistics'] += $row->total;
        }

        $statistics_chart = get_chart_data($statistics_chart);

        return [
            'total' => $total,
            'statistics_chart' => $statistics_chart,
        ];

    }

    protected function projects() {

        $total = ['projects' => 0];

        /* Monitors */
        $convert_tz_sql = get_convert_tz_sql('`datetime`', $this->user->timezone);

        $projects_chart = [];
        $result = database()->query("
            SELECT
                COUNT(*) AS `total`,
                DATE_FORMAT({$convert_tz_sql}, '{$this->datetime['query_date_format']}') AS `formatted_date`
            FROM
                `projects`
            WHERE
                {$convert_tz_sql} BETWEEN '{$this->datetime['query_start_date']}' AND '{$this->datetime['query_end_date']}'
            GROUP BY
                `formatted_date`
            ORDER BY
                `formatted_date`
        ");
        while($row = $result->fetch_object()) {
            $row->formatted_date = $this->datetime['process']($row->formatted_date, true);

            $projects_chart[$row->formatted_date] = [
                'projects' => $row->total
            ];

            $total['projects'] += $row->total;
        }

        $projects_chart = get_chart_data($projects_chart);

        return [
            'total' => $total,
            'projects_chart' => $projects_chart,
        ];

    }

    protected function pixels() {

        $total = ['pixels' => 0];

        /* Monitors */
        $convert_tz_sql = get_convert_tz_sql('`datetime`', $this->user->timezone);

        $pixels_chart = [];
        $result = database()->query("
            SELECT
                COUNT(*) AS `total`,
                DATE_FORMAT({$convert_tz_sql}, '{$this->datetime['query_date_format']}') AS `formatted_date`
            FROM
                `pixels`
            WHERE
                {$convert_tz_sql} BETWEEN '{$this->datetime['query_start_date']}' AND '{$this->datetime['query_end_date']}'
            GROUP BY
                `formatted_date`
            ORDER BY
                `formatted_date`
        ");
        while($row = $result->fetch_object()) {
            $row->formatted_date = $this->datetime['process']($row->formatted_date, true);

            $pixels_chart[$row->formatted_date] = [
                'pixels' => $row->total
            ];

            $total['pixels'] += $row->total;
        }

        $pixels_chart = get_chart_data($pixels_chart);

        return [
            'total' => $total,
            'pixels_chart' => $pixels_chart,
        ];

    }

    protected function domains() {

        $total = ['domains' => 0];

        $convert_tz_sql = get_convert_tz_sql('`datetime`', $this->user->timezone);

        $domains_chart = [];
        $result = database()->query("SELECT COUNT(*) AS `total`, DATE_FORMAT({$convert_tz_sql}, '{$this->datetime['query_date_format']}') AS `formatted_date` FROM `domains` WHERE {$convert_tz_sql} BETWEEN '{$this->datetime['query_start_date']}' AND '{$this->datetime['query_end_date']}' GROUP BY `formatted_date`");
        while($row = $result->fetch_object()) {
            $row->formatted_date = $this->datetime['process']($row->formatted_date, true);

            $domains_chart[$row->formatted_date] = [
                'domains' => $row->total,
            ];

            $total['domains'] += $row->total;
        }

        $domains_chart = get_chart_data($domains_chart);

        return [
            'total' => $total,
            'domains_chart' => $domains_chart,
        ];

    }

}
