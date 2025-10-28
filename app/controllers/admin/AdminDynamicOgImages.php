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

class AdminDynamicOgImages extends Controller {

    public function index() {

        /* Clear files caches */
        clearstatcache();

        /* Get all .webp and .pending files */
        $all_images = [];
        foreach(glob(UPLOADS_PATH . \Altum\Uploads::get_path('dynamic_og_images') . '*.{webp,pending}', GLOB_BRACE) as $file_path) {

            /* Extract file name and extension */
            $file_name_with_extension = basename($file_path);
            $file_extension = pathinfo($file_name_with_extension, PATHINFO_EXTENSION);
            $file_name = pathinfo($file_name_with_extension, PATHINFO_FILENAME);

            /* Get last modified time */
            $file_last_modified = filemtime($file_path);

            /* Build image object */
            $all_images[] = (object) [
                'name' => $file_name,
                'full_name' => $file_name_with_extension,
                'extension' => $file_extension,
                'status' => $file_extension == 'webp' ? 'processed' : 'pending',
                'size' => filesize($file_path),
                'last_modified' => date('Y-m-d H:i:s', $file_last_modified),
            ];
        }

        /* Sort images: .pending first, then .webp; both by last_modified desc */
        usort($all_images, function($first_image, $second_image) {
            if($first_image->extension === 'pending' && $second_image->extension !== 'pending') {
                return -1;
            } elseif($first_image->extension !== 'pending' && $second_image->extension === 'pending') {
                return 1;
            }

            return strtotime($second_image->last_modified) - strtotime($first_image->last_modified);
        });

        /* Prepare the paginator */
        $total_rows = count($all_images);
        $paginator = new \Altum\Paginator(
            $total_rows,
            $this->user->preferences->default_results_per_page ?? settings()->main->default_results_per_page,
            $_GET['page'] ?? 1,
            url('admin/dynamic-og-images?page=%d')
        );

        /* Slice images for current page */
        $images = array_slice($all_images, $paginator->getSqlOffset(), $paginator->getItemsPerPage());

        /* Prepare the pagination view */
        $pagination = (new \Altum\View('partials/admin_pagination', (array) $this))->run(['paginator' => $paginator]);

        /* Main View */
        $data = [
            'images' => $images,
            'pagination' => $pagination
        ];

        $view = new \Altum\View('admin/dynamic-og-images/index', (array) $this);

        $this->add_view_content('content', $view->run($data));
    }

    public function bulk() {

        /* Check for any errors */
        if(empty($_POST)) {
            redirect('admin/dynamic-og-images');
        }

        if(empty($_POST['selected'])) {
            redirect('admin/dynamic-og-images');
        }

        if(!isset($_POST['type'])) {
            redirect('admin/dynamic-og-images');
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

                    foreach($_POST['selected'] as $full_name) {

                        /* Delete locally */
                        unlink(\Altum\Uploads::get_full_path('dynamic_og_images') . $full_name);

                        /* Delete externally if needed */
                        if(\Altum\Plugin::is_active('offload') && settings()->offload->uploads_url) {
                            try {
                                $s3 = new \Aws\S3\S3Client(get_aws_s3_config());

                                /* Delete current file */
                                $s3->deleteObject([
                                    'Bucket' => settings()->offload->storage_name,
                                    'Key' => UPLOADS_URL_PATH . \Altum\Uploads::get_path('dynamic_og_images') . $full_name,
                                ]);
                            } catch (\Exception $exception) {
                                /* :) */
                            }
                        }

                    }
                    break;
            }

            session_start();

            /* Set a nice success message */
            Alerts::add_success(l('bulk_delete_modal.success_message'));

        }

        redirect('admin/dynamic-og-images');
    }

    public function delete() {

        $full_name = isset($this->params[0]) ? input_clean($this->params[0]) : null;

        if(!$full_name) {
            redirect('admin/dynamic-og-images');
        }

        //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

        if(!\Altum\Csrf::check('global_token')) {
            Alerts::add_error(l('global.error_message.invalid_csrf_token'));
        }

        if(!file_exists(\Altum\Uploads::get_full_path('dynamic_og_images') . $full_name)) {
            redirect('admin/dynamic-og-images');
        }

        if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

            /* Delete locally */
            unlink(\Altum\Uploads::get_full_path('dynamic_og_images') . $full_name);

            /* Delete externally if needed */
            if(\Altum\Plugin::is_active('offload') && settings()->offload->uploads_url) {
                try {
                    $s3 = new \Aws\S3\S3Client(get_aws_s3_config());

                    /* Delete current file */
                    $s3->deleteObject([
                        'Bucket' => settings()->offload->storage_name,
                        'Key' => UPLOADS_URL_PATH . \Altum\Uploads::get_path('dynamic_og_images') . $full_name,
                    ]);
                } catch (\Exception $exception) {
                    /* :) */
                }
            }

            /* Set a nice success message */
            Alerts::add_success(sprintf(l('global.success_message.delete1'), '<strong>' . $full_name . '</strong>'));

        }

        redirect('admin/dynamic-og-images');
    }

}
