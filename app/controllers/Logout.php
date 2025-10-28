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

class Logout extends Controller {

    public function index() {

        /* Exit admin impersonation */
        if(isset($_GET['admin_impersonate_user'])) {
            $admin_user_id = $_SESSION['admin_user_id'];

            /* Logout of the current users */
            \Altum\Authentication::logout(false);

            $admin_user = db()->where('user_id', $admin_user_id)->getOne('users', ['user_id', 'password']);

            if($admin_user) {
                /* Login as the admin */
                session_start();
                $_SESSION['user_id'] = $admin_user_id;
                $_SESSION['user_password_hash'] = md5($admin_user->password);
            }

            redirect('admin/users');
        }

        /* Exit team delegated access */
        else if(isset($_GET['team'])) {
            unset($_SESSION['team_id']);
            redirect('teams-member');
        }

        /* Normal logout */
        else {
            \Altum\Authentication::logout();
        }

    }

}
