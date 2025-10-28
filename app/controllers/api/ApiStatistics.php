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

use Altum\Response;
use Altum\Traits\Apiable;

defined('ALTUMCODE') || die();

class ApiStatistics extends Controller {
    use Apiable;
    public $link;
    public $datetime;

    public function index() {

        $this->verify_request();

        /* Decide what to continue with */
        switch($_SERVER['REQUEST_METHOD']) {
            case 'GET':

                /* Detect if we only need an object, or the whole list */
                if(isset($this->params[0])) {
                    $this->get();
                }

            break;
        }

        $this->return_404();
    }

    private function get() {

        $link_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        /* Try to get details about the resource id */
        $this->link = $link = db()->where('link_id', $link_id)->where('user_id', $this->api_user->user_id)->getOne('links');

        /* We haven't found the resource */
        if(!$link) {
            $this->return_404();
        }

        /* :) */
        $this->datetime = \Altum\Date::get_start_end_dates_new();

        $type = isset($_GET['type']) && in_array($_GET['type'], [
            'overview',
            'referrer_host',
            'referrer_path',
            'continent_code',
            'country_code',
            'city_name',
            'os_name',
            'browser_name',
            'device_type',
            'browser_language',
            'utm_source',
            'utm_medium',
            'utm_campaign',
            'hour'
        ]) ? query_clean($_GET['type']) : 'overview';

        /* :) */
        $data = [];

        switch($type) {
            case 'overview':

                $convert_tz_sql = get_convert_tz_sql('`datetime`', \Altum\Date::$default_timezone);

                $result = database()->query("
                    SELECT
                        COUNT(`id`) AS `pageviews`,
                        SUM(`is_unique`) AS `visitors`,
                        DATE_FORMAT({$convert_tz_sql}, '{$this->datetime['query_date_format']}') AS `formatted_date`
                    FROM
                         `statistics`
                    WHERE
                        `link_id` = {$this->link->link_id}
                        AND ({$convert_tz_sql} BETWEEN '{$this->datetime['query_start_date']}' AND '{$this->datetime['query_end_date']}')
                    GROUP BY
                        `formatted_date`
                    ORDER BY
                        `formatted_date`
                ");

                while($row = $result->fetch_object()) {
                    $data[] = [
                        'pageviews' => (int) $row->pageviews,
                        'visitors' => (int) $row->visitors,
                        'formatted_date' => $this->datetime['process']($row->formatted_date, true),
                    ];
                }

                break;

            case 'referrer_host':
            case 'continent_code':
            case 'country_code':
            case 'os_name':
            case 'browser_name':
            case 'device_type':
            case 'browser_language':

                $result = database()->query("
                    SELECT
                        `{$type}`,
                        COUNT(*) AS `pageviews`
                    FROM
                         `statistics`
                    WHERE
                        `link_id` = {$this->link->link_id}
                        AND (`datetime` BETWEEN '{$this->datetime['query_start_date']}' AND '{$this->datetime['query_end_date']}')
                    GROUP BY
                        `{$type}`
                    ORDER BY
                        `pageviews` DESC
                    
                ");

                while($row = $result->fetch_object()) {
                    $data[] = [
                        $type => $row->{$type},
                        'pageviews' => (int) $row->pageviews
                    ];
                }

                break;

            case 'referrer_path':

                $referrer_host = trim(query_clean($_GET['referrer_host']));

                $result = database()->query("
                    SELECT
                        `referrer_host`,
                        `referrer_path`,
                        COUNT(*) AS `pageviews`
                    FROM
                         `statistics`
                    WHERE
                        `link_id` = {$this->link->link_id}
                        AND `referrer_host` = '{$referrer_host}'
                        AND (`datetime` BETWEEN '{$this->datetime['query_start_date']}' AND '{$this->datetime['query_end_date']}')
                    GROUP BY
                        `referrer_host`,
                        `referrer_path`
                    ORDER BY
                        `pageviews` DESC
                    
                ");

                while($row = $result->fetch_object()) {
                    $data[] = [
                        'referrer_host' => $row->referrer_host,
                        $type => $row->{$type},
                        'pageviews' => (int) $row->pageviews
                    ];
                }

                break;

            case 'city_name':

                $country_code = isset($_GET['country_code']) ? trim(query_clean($_GET['country_code'])) : null;

                $result = database()->query("
                    SELECT
                        `country_code`,
                        `city_name`,
                        COUNT(*) AS `pageviews`
                    FROM
                         `statistics`
                    WHERE
                        `link_id` = {$this->link->link_id}
                        " . ($country_code ? "AND `country_code` = '{$country_code}'" : null) . "
                        AND (`datetime` BETWEEN '{$this->datetime['query_start_date']}' AND '{$this->datetime['query_end_date']}')
                    GROUP BY
                        `country_code`,
                        `city_name`
                    ORDER BY
                        `pageviews` DESC
                    
                ");

                while($row = $result->fetch_object()) {
                    $data[] = [
                        'country_code' => $row->country_code,
                        'country_name' => $row->country_code ? get_country_from_country_code($row->country_code) : null,
                        $type => $row->{$type},
                        'pageviews' => (int) $row->pageviews
                    ];
                }

                break;

            case 'utm_source':

                $result = database()->query("
                    SELECT
                        `utm_source`,
                        COUNT(*) AS `pageviews`
                    FROM
                         `statistics`
                    WHERE
                        `link_id` = {$this->link->link_id}
                        AND (`datetime` BETWEEN '{$this->datetime['query_start_date']}' AND '{$this->datetime['query_end_date']}')
                        AND `utm_source` IS NOT NULL
                    GROUP BY
                        `utm_source`
                    ORDER BY
                        `pageviews` DESC
                    
                ");

                while($row = $result->fetch_object()) {
                    $data[] = [
                        $type => $row->{$type},
                        'pageviews' => (int) $row->pageviews
                    ];
                }

                break;

            case 'utm_medium':

                $utm_source = trim(query_clean($_GET['utm_source']));

                $result = database()->query("
                    SELECT
                        `utm_source`,
                        `utm_medium`,
                        COUNT(*) AS `pageviews`
                    FROM
                         `statistics`
                    WHERE
                        `link_id` = {$this->link->link_id}
                        AND `utm_source` = '{$utm_source}'
                        AND (`datetime` BETWEEN '{$this->datetime['query_start_date']}' AND '{$this->datetime['query_end_date']}')
                    GROUP BY
                        `utm_source`,
                        `utm_medium`
                    ORDER BY
                        `pageviews` DESC
                    
                ");

                while($row = $result->fetch_object()) {
                    $data[] = [
                        'utm_source' => $row->utm_source,
                        $type => $row->{$type},
                        'pageviews' => (int) $row->pageviews
                    ];
                }

                break;

            case 'utm_campaign':

                $utm_source = trim(query_clean($_GET['utm_source']));
                $utm_medium = trim(query_clean($_GET['utm_medium']));

                $result = database()->query("
                    SELECT
                        `utm_source`,
                        `utm_medium`,
                        `utm_campaign`,
                        COUNT(*) AS `pageviews`
                    FROM
                         `statistics`
                    WHERE
                        `link_id` = {$this->link->link_id}
                        AND `utm_source` = '{$utm_source}'
                        AND `utm_medium` = '{$utm_medium}'
                        AND (`datetime` BETWEEN '{$this->datetime['query_start_date']}' AND '{$this->datetime['query_end_date']}')
                    GROUP BY
                        `utm_source`,
                        `utm_campaign`,
                        `utm_campaign`
                    ORDER BY
                        `pageviews` DESC
                    
                ");

                while($row = $result->fetch_object()) {
                    $data[] = [
                        'utm_source' => $row->utm_source,
                        'utm_medium' => $row->utm_medium,
                        $type => $row->{$type},
                        'pageviews' => (int) $row->pageviews
                    ];
                }

                break;

            case 'hour':

                /* Group by HOUR after timezone adjustment */
                $result = database()->query("
                    SELECT 
                        HOUR(`datetime`) AS `hour`,
                        COUNT(*) AS `pageviews`
                    FROM
                        `statistics`
                    WHERE
                        `link_id` = {$this->link->link_id}
                        AND (`datetime` BETWEEN '{$this->datetime['query_start_date']}' AND '{$this->datetime['query_end_date']}')
                    GROUP BY
                        `hour`
                    ORDER BY
                        `hour`
                ");

                while($row = $result->fetch_object()) {
                    $hour_start = sprintf('%02d:00', $row->hour);
                    $hour_end = sprintf('%02d:00', ($row->hour + 1) % 24);
                    $label = $hour_start . ' - ' . $hour_end;

                    $data[] = [
                        $type => $label,
                        'pageviews' => (int) $row->pageviews
                    ];
                }

                break;
        }

        Response::jsonapi_success($data);

    }

}
