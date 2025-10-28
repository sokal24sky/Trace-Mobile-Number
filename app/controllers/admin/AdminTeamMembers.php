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

class AdminTeamMembers extends Controller {

    public function index() {

        /* Prepare the filtering system */
        $filters = (new \Altum\Filters(['user_id', 'team_id', 'status'], ['user_email'], ['team_member_id', 'last_datetime', 'datetime', 'user_email']));
        $filters->set_default_order_by('team_member_id', $this->user->preferences->default_order_type ?? settings()->main->default_order_type);
        $filters->set_default_results_per_page($this->user->preferences->default_results_per_page ?? settings()->main->default_results_per_page);

        /* Prepare the paginator */
        $total_rows = database()->query("SELECT COUNT(*) AS `total` FROM `teams_members` WHERE 1 = 1 {$filters->get_sql_where()}")->fetch_object()->total ?? 0;
        $paginator = (new \Altum\Paginator($total_rows, $filters->get_results_per_page(), $_GET['page'] ?? 1, url('admin/team-members?' . $filters->get_get() . '&page=%d')));

        /* Get the data */
        $team_members = [];
        $team_members_result = database()->query("
            SELECT
                `teams_members`.*,
                `teams_members`.`user_email` AS `invited_email`,
                `teams`.`name`,
                `users`.`name` AS `user_name`, `users`.`email` AS `user_email`, `users`.`avatar` AS `user_avatar`
            FROM
                `teams_members`
            LEFT JOIN 
                `teams` ON `teams`.`team_id` = `teams_members`.`team_id` 
            LEFT JOIN
                `users` ON `teams_members`.`user_id` = `users`.`user_id`
            WHERE
                1 = 1
            {$filters->get_sql_where('teams_members')}
            {$filters->get_sql_order_by('teams_members')}
            {$paginator->get_sql_limit()}
        ");
        while($row = $team_members_result->fetch_object()) {
            $row->access = json_decode($row->access);
            $team_members[] = $row;
        }

        /* Export handler */
        process_export_csv($team_members, ['team_member_id', 'team_id', 'user_id', 'invited_email', 'name', 'status', 'datetime', 'last_datetime'], sprintf(l('admin_team_members.title')));
        process_export_json($team_members, ['team_member_id', 'team_id', 'user_id', 'invited_email', 'name', 'access', 'status', 'datetime', 'last_datetime'], sprintf(l('admin_team_members.title')));

        /* Prepare the pagination view */
        $pagination = (new \Altum\View('partials/admin_pagination', (array) $this))->run(['paginator' => $paginator]);

        /* Main View */
        $data = [
            'team_members' => $team_members,
            'filters' => $filters,
            'pagination' => $pagination,
            'teams_access' => require APP_PATH . 'includes/teams_access.php',
        ];

        $view = new \Altum\View('admin/team-members/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function bulk() {

        /* Check for any errors */
        if(empty($_POST)) {
            redirect('admin/team-members');
        }

        if(empty($_POST['selected'])) {
            redirect('admin/team-members');
        }

        if(!isset($_POST['type'])) {
            redirect('admin/team-members');
        }

        //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

        if(!\Altum\Csrf::check()) {
            Alerts::add_error(l('global.error_message.invalid_csrf_token'));
        }

        if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

            set_time_limit(0);

            session_write_close();

            switch($_POST['type']) {
                case 'delete':

                    foreach($_POST['selected'] as $team_member_id) {
                        if($team_member = db()->where('team_member_id', $team_member_id)->getOne('teams_members', ['team_id', 'team_member_id', 'user_id', 'user_email'])) {
                            /* Delete the resource */
                            db()->where('team_member_id', $team_member_id)->delete('teams_members');

                            /* Clear the cache */
                            cache()->deleteItem('team_member?team_id=' . $team_member->team_id . '&user_id=' . $team_member->user_id);
                        }
                    }

                    break;
            }

            session_start();

            /* Set a nice success message */
            Alerts::add_success(l('bulk_delete_modal.success_message'));

        }

        redirect('admin/team-members');
    }

    public function delete() {

        $team_member_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

        if(!\Altum\Csrf::check('global_token')) {
            Alerts::add_error(l('global.error_message.invalid_csrf_token'));
        }

        if(!$team_member = db()->where('team_member_id', $team_member_id)->getOne('teams_members', ['team_id', 'team_member_id', 'user_id', 'user_email'])) {
            redirect('admin/team-members');
        }

        if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

            /* Delete the resource */
            db()->where('team_member_id', $team_member->team_member_id)->delete('teams_members');

            /* Clear the cache */
            cache()->deleteItem('team_member?team_id=' . $team_member->team_id . '&user_id=' . $team_member->user_id);

            /* Set a nice success message */
            Alerts::add_success(sprintf(l('global.success_message.delete1'), '<strong>' . $team_member->user_email . '</strong>'));

        }

        redirect('admin/team-members');
    }

}
