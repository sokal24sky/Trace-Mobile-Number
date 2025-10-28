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

namespace Altum;

defined('ALTUMCODE') || die();

class Database {

    public static $database = null;
    public static $db = null;

    public static function initialize() {
        mysqli_report(MYSQLI_REPORT_OFF);

        self::$database = @new \mysqli(
            DATABASE_SERVER,
            DATABASE_USERNAME,
            DATABASE_PASSWORD,
            DATABASE_NAME
        );

        /* Debugging */
        if(self::$database->connect_error) {
            include THEME_PATH . 'views/partials/database_error.php';

            echo '<div>';
            //echo '<a href="https://altumcode.com/" target="_blank"><img src="https://altumcode.com/themes/altum/assets/images/altumcode.svg" class="altumcode-logo" alt="altumcode logo" loading="lazy" /></a>';
            echo '<h1 class="text-white">Database connection issues</h1>';
            /* Make sure script is actually installed */
            if(empty(DATABASE_SERVER) && empty(DATABASE_USERNAME) && empty(DATABASE_PASSWORD) && empty(DATABASE_NAME) && !file_exists(ROOT_PATH . 'install/installed')) {
                echo '<p>Empty database configuration file (config.php).</p>';
                echo '<p>Did you run the installer (/install path) of ' . PRODUCT_NAME . '?</p>';
            } else {
                echo '<p>Our database is having some issues.</p>';
                echo '<p>We are actively working on fixing the issue.</p>';
            }
            //echo '<p class="buttons"><smalsl><a href="' . PRODUCT_DOCUMENTATION_URL .'" target="_blank">📜 Read documentation</a> &nbsp;&bullet;&nbsp; <a href="https://altumcode.com/contact" target="_blank">📧 Contact support</a></smalsl></p>';
            echo '</div>';
            die();
        }

        /* Mysql profiling */
        if(MYSQL_DEBUG) {
            self::$database->query("set profiling_history_size=100");
            self::$database->query("set profiling=1");
        }

        self::$database->set_charset('utf8mb4');

        self::initialize_helper();

        return self::$database;
    }

    public static function initialize_helper() {
        self::$db = new \Altum\Helpers\MysqliDb(self::$database);
        self::$db->returnType = 'object';
    }

    public static function close() {

        if(!self::$database) return;

        if(MYSQL_DEBUG) {
            $result = self::$database->query("show profiles");

            while($profile = $result->fetch_object()) {
                echo $profile->Query_ID . ' - ' . round($profile->Duration, 10)  . ' s - ' . $profile->Query . '<br />';
            }
        }

        self::$database->close();
    }
}
