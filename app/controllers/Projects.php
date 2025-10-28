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

class Projects extends Controller {

    public function index() {

        if(!settings()->links->projects_is_enabled) {
            redirect('not-found');
        }

        \Altum\Authentication::guard();

        /* Prepare the filtering system */
        $filters = (new \Altum\Filters([], ['name'], ['project_id', 'last_datetime', 'name', 'datetime']));
        $filters->set_default_order_by($this->user->preferences->projects_default_order_by, $this->user->preferences->default_order_type ?? settings()->main->default_order_type);
        $filters->set_default_results_per_page($this->user->preferences->default_results_per_page ?? settings()->main->default_results_per_page);

        /* Prepare the paginator */
        $total_rows = database()->query("SELECT COUNT(*) AS `total` FROM `projects` WHERE `user_id` = {$this->user->user_id} {$filters->get_sql_where()}")->fetch_object()->total ?? 0;
        $paginator = (new \Altum\Paginator($total_rows, $filters->get_results_per_page(), $_GET['page'] ?? 1, url('projects?' . $filters->get_get() . '&page=%d')));

        /* Get the projects list for the user */
        $projects = [];
        $projects_result = database()->query("SELECT * FROM `projects` WHERE `user_id` = {$this->user->user_id} {$filters->get_sql_where()} {$filters->get_sql_order_by()} {$paginator->get_sql_limit()}");
        while($row = $projects_result->fetch_object()) $projects[] = $row;

        /* Export handler */
        process_export_csv($projects, ['project_id', 'user_id', 'name', 'color', 'last_datetime', 'datetime'], sprintf(l('projects.title')));
        process_export_json($projects, ['project_id', 'user_id', 'name', 'color', 'last_datetime', 'datetime'], sprintf(l('projects.title')));

        /* Prepare the pagination view */
        $pagination = (new \Altum\View('partials/pagination', (array) $this))->run(['paginator' => $paginator]);

        /* Prepare the view */
        $data = [
            'projects' => $projects,
            'total_projects' => $total_rows,
            'pagination' => $pagination,
            'filters' => $filters,
        ];

        $view = new \Altum\View('projects/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function bulk() {

        \Altum\Authentication::guard();

        /* Check for any errors */
        if(empty($_POST)) {
            redirect('projects');
        }

        if(empty($_POST['selected'])) {
            redirect('projects');
        }

        if(!isset($_POST['type'])) {
            redirect('projects');
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

                    /* Team checks */
                    if(\Altum\Teams::is_delegated() && !\Altum\Teams::has_access('delete.projects')) {
                        Alerts::add_info(l('global.info_message.team_no_access'));
                        redirect('projects');
                    }

                    foreach($_POST['selected'] as $project_id) {
                        if($project = db()->where('project_id', $project_id)->where('user_id', $this->user->user_id)->getOne('projects', ['project_id'])) {
                            db()->where('project_id', $project_id)->delete('projects');
                        }
                    }

                    break;
            }

            /* Clear the cache */
            cache()->deleteItem('projects?user_id=' . $this->user->user_id);
            cache()->deleteItem('projects_total?user_id=' . $this->user->user_id);

            session_start();

            /* Set a nice success message */
            Alerts::add_success(l('bulk_delete_modal.success_message'));

        }

        redirect('projects');
    }

    public function delete() {

        \Altum\Authentication::guard();

        /* Team checks */
        if(\Altum\Teams::is_delegated() && !\Altum\Teams::has_access('delete.projects')) {
            Alerts::add_info(l('global.info_message.team_no_access'));
            redirect('projects');
        }

        if(empty($_POST)) {
            redirect('projects');
        }

        $project_id = (int) query_clean($_POST['project_id']);

        //ALTUMCODE:DEMO if(DEMO) if($this->user->user_id == 1) Alerts::add_error('Please create an account on the demo to test out this function.');

        if(!\Altum\Csrf::check()) {
            Alerts::add_error(l('global.error_message.invalid_csrf_token'));
        }

        if(!$project = db()->where('project_id', $project_id)->where('user_id', $this->user->user_id)->getOne('projects', ['project_id', 'name'])) {
            redirect('projects');
        }

        if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

            /* Delete the resource */
            db()->where('project_id', $project_id)->delete('projects');

            /* Set a nice success message */
            Alerts::add_success(sprintf(l('global.success_message.delete1'), '<strong>' . $project->name . '</strong>'));

            /* Clear the cache */
            cache()->deleteItem('projects?user_id=' . $this->user->user_id);
            cache()->deleteItem('projects_total?user_id=' . $this->user->user_id);

            redirect('projects');
        }

        redirect('projects');
    }
}
