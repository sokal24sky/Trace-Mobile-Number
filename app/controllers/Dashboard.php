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

defined('ALTUMCODE') || die();

class Dashboard extends Controller {

    public function index() {

        \Altum\Authentication::guard();

        $dashboard_features = ((array) $this->user->preferences->dashboard) + array_fill_keys(['ai_qr_codes', 'qr_codes', 'barcodes', 'links'], true);

        /* Get available projects */
        $projects = (new \Altum\Models\Projects())->get_projects_by_user_id($this->user->user_id);

        /* Links */
        if($dashboard_features['links']) {
            $links = \Altum\Cache::cache_function_result('links_dashboard?user_id=' . $this->user->user_id, 'user_id=' . $this->user->user_id, function () {
                $links = [];
                $links_result = database()->query("SELECT * FROM `links` WHERE `user_id` = {$this->user->user_id} ORDER BY `link_id` DESC LIMIT 5");
                while ($row = $links_result->fetch_object()) {
                    $links[] = $row;
                }

                return $links;
            });
        }

        /* AI QR Codes */
        if($dashboard_features['ai_qr_codes'] && settings()->codes->ai_qr_codes_is_enabled) {

            /* Get the QR codes */
            $ai_qr_codes = \Altum\Cache::cache_function_result('ai_qr_codes_dashboard?user_id=' . $this->user->user_id, 'user_id=' . $this->user->user_id, function() {
                $ai_qr_codes = [];
                $ai_qr_codes_result = database()->query("SELECT * FROM `ai_qr_codes` WHERE `user_id` = {$this->user->user_id} ORDER BY `ai_qr_code_id` DESC LIMIT 5");
                while ($row = $ai_qr_codes_result->fetch_object()) {
                    $ai_qr_codes[] = $row;
                }

                return $ai_qr_codes;
            });
        }

        /* QR Codes */
        if($dashboard_features['qr_codes'] && settings()->codes->qr_codes_is_enabled) {

            /* Get the QR codes */
            $qr_codes = \Altum\Cache::cache_function_result('qr_codes_dashboard?user_id=' . $this->user->user_id, 'user_id=' . $this->user->user_id, function() {
                $qr_codes = [];
                $qr_codes_result = database()->query("SELECT * FROM `qr_codes` WHERE `user_id` = {$this->user->user_id} ORDER BY `qr_code_id` DESC LIMIT 5");
                while ($row = $qr_codes_result->fetch_object()) {
                    $row->settings = json_decode($row->settings ?? '');
                    $qr_codes[] = $row;
                }

                return $qr_codes;
            });

            $available_qr_codes = require APP_PATH . 'includes/enabled_qr_codes.php';
        }

        /* Barcodes */
        if($dashboard_features['barcodes'] && settings()->codes->barcodes_is_enabled) {
            /* Get the barcodes */
            $barcodes = \Altum\Cache::cache_function_result('barcodes_dashboard?user_id=' . $this->user->user_id, 'user_id=' . $this->user->user_id, function() {
                $barcodes = [];
                $barcodes_result = database()->query("SELECT * FROM `barcodes` WHERE `user_id` = {$this->user->user_id} ORDER BY `barcode_id` DESC LIMIT 5");
                while ($row = $barcodes_result->fetch_object()) {
                    $row->settings = json_decode($row->settings ?? '');
                    $barcodes[] = $row;
                }

                return $barcodes;
            });

            $available_barcodes = require APP_PATH . 'includes/enabled_barcodes.php';
        }

        /* Prepare the view */
        $data = [
            'ai_qr_codes' => $ai_qr_codes ?? [],

            'qr_codes' => $qr_codes ?? [],
            'available_qr_codes'  => $available_qr_codes ?? [],

            'barcodes' => $barcodes ?? [],
            'available_barcodes'  => $available_barcodes ?? [],

            'projects' => $projects,
            'links' => $links ?? [],
            'total_projects' => count($projects),
        ];

        $view = new \Altum\View('dashboard/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function get_stats_ajax() {

        session_write_close();

        \Altum\Authentication::guard();

        if(!empty($_POST)) {
            redirect();
        }

        $dashboard_features = ((array) $this->user->preferences->dashboard) + array_fill_keys(['ai_qr_codes', 'qr_codes', 'barcodes', 'links'], true);

        if($dashboard_features['ai_qr_codes'] && settings()->codes->ai_qr_codes_is_enabled) {
            /* Get current monthly usage */
            $usage = db()->where('user_id', $this->user->user_id)->getOne('users', ['qrcode_ai_qr_codes_current_month']);
        }

        /* Get some stats */
        $total_links = \Altum\Cache::cache_function_result('links_total?user_id=' . $this->user->user_id, 'user_id=' . $this->user->user_id, function() {
            return db()->where('user_id', $this->user->user_id)->getValue('links', 'count(*)');
        });

        $total_domains = \Altum\Cache::cache_function_result('domains_total?user_id=' . $this->user->user_id, 'user_id=' . $this->user->user_id, function() {
            return db()->where('user_id', $this->user->user_id)->getValue('domains', 'count(*)');
        });

        /* QR Codes */
        if(settings()->codes->qr_codes_is_enabled) {
            $total_qr_codes = \Altum\Cache::cache_function_result('qr_codes_total?user_id=' . $this->user->user_id, 'user_id=' . $this->user->user_id, function () {
                return db()->where('user_id', $this->user->user_id)->getValue('qr_codes', 'count(*)');
            });
        }

        /* Barcodes */
        if(settings()->codes->barcodes_is_enabled) {
            $total_barcodes = \Altum\Cache::cache_function_result('barcodes_total?user_id=' . $this->user->user_id, 'user_id=' . $this->user->user_id, function () {
                return db()->where('user_id', $this->user->user_id)->getValue('barcodes', 'count(*)');
            });
        }

        /* Get available projects */
        if(settings()->links->projects_is_enabled) {
            $projects = (new \Altum\Models\Projects())->get_projects_by_user_id($this->user->user_id);
        }

        /* Prepare the data */
        $data = [
            'usage' => $usage ?? null,

            /* Widgets */
            'total_links' => $total_links ?? null,
            'total_domains' => $total_domains ?? null,
            'total_qr_codes' => $total_qr_codes ?? null,
            'total_barcodes' => $total_barcodes ?? null,
            'total_projects' => isset($projects) ? count($projects) : null,
        ];

        /* Set a nice success message */
        Response::json('', 'success', $data);

    }

}
