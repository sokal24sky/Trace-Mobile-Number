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
use Altum\Title;

defined('ALTUMCODE') || die();

class LinkStatistics extends Controller {

    public function index() {

        \Altum\Authentication::guard();

        if(!$this->user->plan_settings->analytics_is_enabled) {
            Alerts::add_info(l('global.info_message.plan_feature_no_access'));
            redirect('links');
        }

        $link_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        if(!$link = db()->where('link_id', $link_id)->where('user_id', $this->user->user_id)->getOne('links')) {
            redirect('links');
        }

        /* Genereate the link full URL base */
        $link->full_url = (new \Altum\Models\Link())->get_link_full_url($link, $this->user);

        /* Statistics related variables */
        $type = isset($_GET['type']) && in_array($_GET['type'], ['overview', 'entries', 'referrer_host', 'referrer_path', 'continent_code', 'country', 'city_name', 'os', 'browser', 'device', 'language', 'utm_source', 'utm_medium', 'utm_campaign', 'hour']) ? input_clean($_GET['type']) : 'overview';

        $datetime = \Altum\Date::get_start_end_dates_new();

        /* Get data based on what statistics are needed */
        switch($type) {
            case 'overview':

                /* Get the required statistics */
                $pageviews = [];
                $pageviews_chart = [];
                $totals = [
                    'pageviews' => 0,
                    'visitors' => 0,
                ];

                $convert_tz_sql = get_convert_tz_sql('`datetime`', $this->user->timezone);

                $pageviews_result = database()->query("
                    SELECT
                        COUNT(`id`) AS `pageviews`,
                        SUM(`is_unique`) AS `visitors`,
                        DATE_FORMAT({$convert_tz_sql}, '{$datetime['query_date_format']}') AS `formatted_date`
                    FROM
                         `statistics`
                    WHERE
                        `link_id` = {$link->link_id}
                        AND ({$convert_tz_sql} BETWEEN '{$datetime['query_start_date']}' AND '{$datetime['query_end_date']}')
                    GROUP BY
                        `formatted_date`
                    ORDER BY
                        `formatted_date`
                ");

                /* Generate the raw chart data and save pageviews for later usage */
                while($row = $pageviews_result->fetch_object()) {
                    $pageviews[] = $row;

                    $row->formatted_date = $datetime['process']($row->formatted_date, true);

                    $pageviews_chart[$row->formatted_date] = [
                        'pageviews' => $row->pageviews,
                        'visitors' => $row->visitors
                    ];

                    $totals['pageviews'] += $row->pageviews;
                    $totals['visitors'] += $row->visitors;
                }

                $pageviews_chart = get_chart_data($pageviews_chart);

                $limit = $this->user->preferences->default_results_per_page ?? settings()->main->default_results_per_page;
                $result = database()->query("
                    SELECT
                        *
                    FROM
                        `statistics`
                    WHERE
                        `link_id` = {$link->link_id}
                        AND (`datetime` BETWEEN '{$datetime['query_start_date']}' AND '{$datetime['query_end_date']}')
                    ORDER BY
                        `datetime` DESC
                    LIMIT {$limit}
                ");

                break;

            case 'entries':

                /* Prepare the filtering system */
                $filters = (new \Altum\Filters([], [], ['datetime']));
                $filters->set_default_order_by('id', $this->user->preferences->default_order_type ?? settings()->main->default_order_type);
                $filters->set_default_results_per_page($this->user->preferences->default_results_per_page ?? settings()->main->default_results_per_page);

                /* Prepare the paginator */
                $total_rows = database()->query("SELECT COUNT(*) AS `total` FROM `statistics` WHERE `link_id` = {$link->link_id} AND (`datetime` BETWEEN '{$datetime['query_start_date']}' AND '{$datetime['query_end_date']}') {$filters->get_sql_where()}")->fetch_object()->total ?? 0;
                $paginator = (new \Altum\Paginator($total_rows, $filters->get_results_per_page(), $_GET['page'] ?? 1, url('link-statistics/' . $link->link_id . '?type=' . $type . '&start_date=' . $datetime['start_date'] . '&end_date=' . $datetime['end_date'] . $filters->get_get() . '&page=%d')));

                $result = database()->query("
                    SELECT
                        *
                    FROM
                        `statistics`
                    WHERE
                        `link_id` = {$link->link_id}
                        AND (`datetime` BETWEEN '{$datetime['query_start_date']}' AND '{$datetime['query_end_date']}')
                    {$filters->get_sql_where()}
                    {$filters->get_sql_order_by()}
                    {$paginator->get_sql_limit()}
                ");

                break;

            case 'referrer_host':
            case 'continent_code':
            case 'os':
            case 'browser':
            case 'device':
            case 'language':

                $columns = [
                    'referrer_host' => 'referrer_host',
                    'referrer_path' => 'referrer_path',
                    'continent_code' => 'continent_code',
                    'country' => 'country_code',
                    'city_name' => 'city_name',
                    'os' => 'os_name',
                    'browser' => 'browser_name',
                    'device' => 'device_type',
                    'language' => 'browser_language'
                ];

                $result = database()->query("
                    SELECT
                        `{$columns[$type]}`,
                        COUNT(*) AS `total`
                    FROM
                         `statistics`
                    WHERE
                        `link_id` = {$link->link_id}
                        AND (`datetime` BETWEEN '{$datetime['query_start_date']}' AND '{$datetime['query_end_date']}')
                    GROUP BY
                        `{$columns[$type]}`
                    ORDER BY
                        `total` DESC
                    
                ");

                break;

            case 'referrer_path':

                $referrer_host = input_clean($_GET['referrer_host']);

                $result = database()->query("
                    SELECT
                        `referrer_path`,
                        COUNT(*) AS `total`
                    FROM
                         `statistics`
                    WHERE
                        `link_id` = {$link->link_id}
                        AND `referrer_host` = '{$referrer_host}'
                        AND (`datetime` BETWEEN '{$datetime['query_start_date']}' AND '{$datetime['query_end_date']}')
                    GROUP BY
                        `referrer_path`
                    ORDER BY
                        `total` DESC
                    
                ");

                break;

            case 'country':

                $continent_code = isset($_GET['continent_code']) ? input_clean($_GET['continent_code']) : null;

                $result = database()->query("
                    SELECT
                        `country_code`,
                        " . ($continent_code ? "`continent_code`," : null) . "
                        COUNT(*) AS `total`
                    FROM
                         `statistics`
                    WHERE
                        `link_id` = {$link->link_id}
                        " . ($continent_code ? "AND `continent_code` = '{$continent_code}'" : null) . "
                        AND (`datetime` BETWEEN '{$datetime['query_start_date']}' AND '{$datetime['query_end_date']}')
                    GROUP BY
                        " . ($continent_code ? "`continent_code`," : null) . "
                        `country_code`
                    ORDER BY
                        `total` DESC
                    
                ");

                break;

            case 'city_name':

                $country_code = isset($_GET['country_code']) ? input_clean($_GET['country_code']) : null;

                $result = database()->query("
                    SELECT
                        `country_code`,
                        `city_name`,
                        COUNT(*) AS `total`
                    FROM
                         `statistics`
                    WHERE
                        `link_id` = {$link->link_id}
                        " . ($country_code ? "AND `country_code` = '{$country_code}'" : null) . "
                        AND (`datetime` BETWEEN '{$datetime['query_start_date']}' AND '{$datetime['query_end_date']}')
                    GROUP BY
                        `country_code`,
                        `city_name`
                    ORDER BY
                        `total` DESC
                    
                ");


                break;

            case 'utm_source':

                $result = database()->query("
                    SELECT
                        `utm_source`,
                        COUNT(*) AS `total`
                    FROM
                         `statistics`
                    WHERE
                        `link_id` = {$link->link_id}
                        AND (`datetime` BETWEEN '{$datetime['query_start_date']}' AND '{$datetime['query_end_date']}')
                        AND `utm_source` IS NOT NULL
                    GROUP BY
                        `utm_source`
                    ORDER BY
                        `total` DESC
                    
                ");

                break;

            case 'utm_medium':

                $utm_source = input_clean($_GET['utm_source']);

                $result = database()->query("
                    SELECT
                        `utm_medium`,
                        COUNT(*) AS `total`
                    FROM
                         `statistics`
                    WHERE
                        `link_id` = {$link->link_id}
                        AND `utm_source` = '{$utm_source}'
                        AND (`datetime` BETWEEN '{$datetime['query_start_date']}' AND '{$datetime['query_end_date']}')
                    GROUP BY
                        `utm_medium`
                    ORDER BY
                        `total` DESC
                    
                ");

                break;

            case 'utm_campaign':

                $utm_source = input_clean($_GET['utm_source']);
                $utm_medium = input_clean($_GET['utm_medium']);

                $result = database()->query("
                    SELECT
                        `utm_campaign`,
                        COUNT(*) AS `total`
                    FROM
                         `statistics`
                    WHERE
                        `link_id` = {$link->link_id}
                        AND `utm_source` = '{$utm_source}'
                        AND `utm_medium` = '{$utm_medium}'
                        AND (`datetime` BETWEEN '{$datetime['query_start_date']}' AND '{$datetime['query_end_date']}')
                    GROUP BY
                        `utm_campaign`
                    ORDER BY
                        `total` DESC
                    
                ");

                break;

            case 'hour':

                /* Get the timezone conversion SQL */
                $convert_tz_sql = get_convert_tz_sql('`datetime`', $this->user->timezone);

                /* Group by HOUR after timezone adjustment */
                $result = database()->query("
                    SELECT 
                        HOUR({$convert_tz_sql}) AS `hour`,
                        COUNT(*) AS `total`
                    FROM
                        `statistics`
                    WHERE
                        `link_id` = {$link->link_id}
                        AND ({$convert_tz_sql} BETWEEN '{$datetime['query_start_date']}' AND '{$datetime['query_end_date']}')
                    GROUP BY
                        `hour`
                    ORDER BY
                        `total` DESC
                ");

                break;
        }

        switch($type) {
            case 'overview':

                $statistics_keys = [
                    'continent_code',
                    'country_code',
                    'city_name',
                    'referrer_host',
                    'device_type',
                    'os_name',
                    'browser_name',
                    'browser_language'
                ];

                $latest = [];
                $statistics = [];
                foreach($statistics_keys as $key) {
                    $statistics[$key] = [];
                    $statistics[$key . '_total_sum'] = 0;
                }

                $has_data = $result->num_rows;

                /* Start processing the rows from the database */
                while($row = $result->fetch_object()) {
                    foreach($statistics_keys as $key) {

                        $statistics[$key][$row->{$key}] = isset($statistics[$key][$row->{$key}]) ? $statistics[$key][$row->{$key}] + 1 : 1;

                        $statistics[$key . '_total_sum']++;

                    }

                    $latest[] = $row;
                }

                foreach($statistics_keys as $key) {
                    arsort($statistics[$key]);
                }

                /* Prepare the statistics method View */
                $data = [
                    'statistics' => $statistics,
                    'link' => $link,
                    'datetime' => $datetime,
                    'latest' => $latest,
                    'pageviews' => $pageviews,
                    'pageviews_chart' => $pageviews_chart,
                    'totals' => $totals,
                ];

                break;

            case 'entries':

                /* Store all the results from the database */
                $statistics = [];

                while($row = $result->fetch_object()) {
                    $statistics[] = $row;
                }

                /* Prepare the pagination view */
                $pagination = (new \Altum\View('partials/pagination', (array) $this))->run(['paginator' => $paginator]);

                /* Prepare the statistics method View */
                $data = [
                    'rows' => $statistics,
                    'link' => $link,
                    'datetime' => $datetime,
                    'pagination' => $pagination,
                    'filters' => $filters,
                ];

                $has_data = count($statistics);

                break;

            case 'referrer_host':
            case 'continent_code':
            case 'country':
            case 'city_name':
            case 'os':
            case 'browser':
            case 'device':
            case 'language':
            case 'referrer_path':
            case 'utm_source':
            case 'utm_medium':
            case 'utm_campaign':
            case 'hour':

                /* Store all the results from the database */
                $statistics = [];
                $statistics_total_sum = 0;

                while($row = $result->fetch_object()) {
                    $statistics[] = $row;

                    $statistics_total_sum += $row->total;
                }

                /* Prepare the statistics method View */
                $data = [
                    'rows' => $statistics,
                    'total_sum' => $statistics_total_sum,
                    'link' => $link,
                    'datetime' => $datetime,

                    'referrer_host' => $referrer_host ?? null,
                    'continent_code' => $continent_code ?? null,
                    'country_code' => $country_code ?? null,
                    'utm_source' => $utm_source ?? null,
                    'utm_medium' => $utm_medium ?? null,
                ];

                $has_data = count($statistics);

                break;
        }

        /* Set a custom title */
        Title::set(sprintf(l('link_statistics.title'), $link->url));

        /* Export handler */
        process_export_csv($statistics);
        process_export_json($statistics);

        $data['type'] = $type;
        $view = new \Altum\View('link-statistics/statistics_' . $type, (array) $this);
        $this->add_view_content('statistics', $view->run($data));

        /* Delete Modal */
        $view = new \Altum\View('links/link_delete_modal', (array) $this);
        \Altum\Event::add_content($view->run(), 'modals');

        /* Prepare the view */
        $data = [
            'link' => $link,
            'type' => $type,
            'datetime' => $datetime,
            'has_data' => $has_data,
        ];

        $view = new \Altum\View('link-statistics/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function reset() {

        \Altum\Authentication::guard();

        if(!$this->user->plan_settings->analytics_is_enabled) {
            Alerts::add_info(l('global.info_message.plan_feature_no_access'));
            redirect('links');
        }

        if(empty($_POST)) {
            redirect('links');
        }

        $link_id = (int) query_clean($_POST['link_id']);
        $datetime = \Altum\Date::get_start_end_dates_new($_POST['start_date'], $_POST['end_date']);

        /* Make sure the link id is created by the logged in user */
        if(!$link = db()->where('link_id', $link_id)->where('user_id', $this->user->user_id)->getOne('links', ['link_id'])) {
            redirect('links');
        }

        /* Team checks */
        if(\Altum\Teams::is_delegated() && !\Altum\Teams::has_access('delete.links')) {
            Alerts::add_info(l('global.info_message.team_no_access'));
            redirect('link-statistics/' . $link->link_id);
        }

        //ALTUMCODE:DEMO if(DEMO) if($this->user->user_id == 1) Alerts::add_error('Please create an account on the demo to test out this function.');

        if(!\Altum\Csrf::check()) {
            Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            redirect('link-statistics/' . $link->link_id);
        }

        if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

            /* Clear statistics data */
            database()->query("DELETE FROM `statistics` WHERE `link_id` = {$link->link_id} AND (`datetime` BETWEEN '{$datetime['query_start_date']}' AND '{$datetime['query_end_date']}')");

            /* Set a nice success message */
            Alerts::add_success(l('global.success_message.update2'));

            redirect('link-statistics/' . $link->link_id);

        }

        redirect('link-statistics/' . $link->link_id);

    }

}
