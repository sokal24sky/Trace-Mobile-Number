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

class AdminProjects extends Controller {

    public function index() {

        /* Prepare the filtering system */
        $filters = (new \Altum\Filters(['user_id'], ['name'], ['project_id', 'last_datetime', 'datetime', 'name']));
        $filters->set_default_order_by($this->user->preferences->projects_default_order_by, $this->user->preferences->default_order_type ?? settings()->main->default_order_type);
        $filters->set_default_results_per_page($this->user->preferences->default_results_per_page ?? settings()->main->default_results_per_page);

        /* Prepare the paginator */
        $total_rows = database()->query("SELECT COUNT(*) AS `total` FROM `projects` WHERE 1 = 1 {$filters->get_sql_where()}")->fetch_object()->total ?? 0;
        $paginator = (new \Altum\Paginator($total_rows, $filters->get_results_per_page(), $_GET['page'] ?? 1, url('admin/projects?' . $filters->get_get() . '&page=%d')));

        /* Get the data */
        $projects = [];
        $projects_result = database()->query("
            SELECT
                `projects`.*, `users`.`name` AS `user_name`, `users`.`email` AS `user_email`, `users`.`avatar` AS `user_avatar`
            FROM
                `projects`
            LEFT JOIN
                `users` ON `projects`.`user_id` = `users`.`user_id`
            WHERE
                1 = 1
                {$filters->get_sql_where('projects')}
                {$filters->get_sql_order_by('projects')}

            {$paginator->get_sql_limit()}
        ");
        while($row = $projects_result->fetch_object()) {
            $projects[] = $row;
        }

        /* Export handler */
        process_export_csv($projects, ['project_id', 'user_id', 'name', 'color', 'last_datetime', 'datetime'], sprintf(l('admin_projects.title')));
        process_export_json($projects, ['project_id', 'user_id', 'name', 'color', 'last_datetime', 'datetime'], sprintf(l('admin_projects.title')));

        /* Prepare the pagination view */
        $pagination = (new \Altum\View('partials/admin_pagination', (array) $this))->run(['paginator' => $paginator]);

        /* Main View */
        $data = [
            'projects' => $projects,
            'filters' => $filters,
            'pagination' => $pagination
        ];

        $view = new \Altum\View('admin/projects/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function bulk() {

        /* Check for any errors */
        if(empty($_POST)) {
            redirect('admin/projects');
        }

        if(empty($_POST['selected'])) {
            redirect('admin/projects');
        }

        if(!isset($_POST['type'])) {
            redirect('admin/projects');
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

                    foreach($_POST['selected'] as $project_id) {

                        $user_id = db()->where('project_id', $project_id)->getValue('projects', 'user_id');

                        /* Delete the domain_name */
                        db()->where('project_id', $project_id)->delete('projects');

                        /* Clear the cache */
                        cache()->deleteItem('projects?user_id=' . $user_id);
                        cache()->deleteItem('projects_total?user_id=' . $user_id);

                    }

                    break;
            }

            session_start();

            /* Set a nice success message */
            Alerts::add_success(l('bulk_delete_modal.success_message'));

        }

        redirect('admin/projects');
    }

    public function delete() {

        $project_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

        if(!\Altum\Csrf::check('global_token')) {
            Alerts::add_error(l('global.error_message.invalid_csrf_token'));
        }

        if(!$project = db()->where('project_id', $project_id)->getOne('projects', ['project_id', 'name'])) {
            redirect('admin/projects');
        }

        if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

            $user_id = db()->where('project_id', $project->project_id)->getValue('projects', 'user_id');

            /* Delete the resource */
            db()->where('project_id', $project->project_id)->delete('projects');

            /* Clear the cache */
            cache()->deleteItem('projects?user_id=' . $user_id);
            cache()->deleteItem('projects_total?user_id=' . $user_id);

            /* Set a nice success message */
            Alerts::add_success(sprintf(l('global.success_message.delete1'), '<strong>' . $project->name . '</strong>'));

        }

        redirect('admin/projects');
    }

}
