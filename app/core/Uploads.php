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

class Uploads {
    public static $uploads = null;

    private static function initialize() {
        if(!self::$uploads) {
            self::$uploads = require APP_PATH . 'includes/uploads.php';
        }
    }

    public static function get_path($key) {
        self::initialize();
        return self::$uploads[$key]['path'] ?? ($key . '/');
    }

    public static function get_full_path($key) {
        self::initialize();
        return UPLOADS_PATH . (self::$uploads[$key]['path'] ?? ($key . '/'));
    }

    public static function get_full_url($key) {
        self::initialize();
        return UPLOADS_FULL_URL . (self::$uploads[$key]['path'] ?? ($key . '/'));
    }

    public static function get_whitelisted_file_extensions($key) {
        self::initialize();
        return self::$uploads[$key]['whitelisted_file_extensions'];
    }

    public static function get_whitelisted_file_extensions_accept($key) {
        self::initialize();
        return self::array_to_list_format(self::$uploads[$key]['whitelisted_file_extensions']);
    }

    public static function array_to_list_format($array) {
        return implode(', ', array_map(function($value) { return '.' . $value; }, $array));
    }

    public static function process_upload_fake($uploads_file_key, $file_key, $error_response_type = 'error', $error_field = null) {
        /* Determine the error response */
        $return_error = null;
        switch($error_response_type) {
            case 'error':
                $return_error = function($message) {
                    Alerts::add_error($message);
                };
                break;

            case 'field_error':
                $return_error = function($message) use ($error_field) {
                    Alerts::add_field_error($message, $error_field);
                };
                break;

            case 'json_error':
                $return_error = function($message) use ($error_field) {
                    Response::json($message, 'error');
                };
                break;
        }

        $returned_file_name = null;

        if(!empty($_FILES[$file_key]['name'])) {
            $file_extension = mb_strtolower(pathinfo($_FILES[$file_key]['name'], PATHINFO_EXTENSION));
            $file_temp = $_FILES[$file_key]['tmp_name'];

            /* Generate new name for image */
            $image_new_name = md5(time() . rand() . rand()) . '.' . $file_extension;

            /* Try to compress the image */
            if(\Altum\Plugin::is_active('image-optimizer') && settings()->image_optimizer->is_enabled) {
                \Altum\Plugin\ImageOptimizer::optimize($file_temp, $image_new_name, $_FILES[$file_key]['name'], Uploads::get_path($uploads_file_key));
            }

            /* Sanitize SVG uploads */
            if($file_extension == 'svg') {
                $svg_sanitizer = new \enshrined\svgSanitize\Sanitizer();
                $dirty_svg = file_get_contents($file_temp);
                $clean_svg = $svg_sanitizer->sanitize($dirty_svg);
                file_put_contents($file_temp, $clean_svg);
            }

            if(!\Altum\Plugin::is_active('offload') || (\Altum\Plugin::is_active('offload') && !settings()->offload->uploads_url)) {
                if(!is_writable(UPLOADS_PATH . Uploads::get_path($uploads_file_key))) {
                    $return_error(sprintf(l('global.error_message.directory_not_writable'), UPLOADS_PATH . Uploads::get_path($uploads_file_key)));
                }
            }

            /* Offload uploading */
            if(\Altum\Plugin::is_active('offload') && settings()->offload->uploads_url) {
                try {
                    $s3 = new \Aws\S3\S3Client(get_aws_s3_config());

                    /* Upload image */
                    $result = $s3->putObject([
                        'Bucket' => settings()->offload->storage_name,
                        'Key' => UPLOADS_URL_PATH . Uploads::get_path($uploads_file_key) . $image_new_name,
                        'ContentType' => mime_content_type($file_temp),
                        'SourceFile' => $file_temp,
                        'ACL' => 'public-read'
                    ]);
                } catch (\Exception $exception) {
                    $return_error($exception->getMessage());
                }

                /* Delete current image */
                unlink($file_temp);
            }

            /* Local uploading */
            else {
                /* Upload the original */
                rename($file_temp, UPLOADS_PATH . Uploads::get_path($uploads_file_key) . $image_new_name);
            }

            /* Returned value */
            $returned_file_name = $image_new_name;
        }

        return $returned_file_name;
    }

    public static function process_upload($already_existing_file_name, $uploads_file_key, $file_key, $file_key_remove, $size_limit, $error_response_type = 'error', $error_field = null) {
        /* Determine the error response */
        $return_error = null;
        switch($error_response_type) {
            case 'error':
                $return_error = function($message) {
                    Alerts::add_error($message);
                };
                break;

            case 'field_error':
                $return_error = function($message) use ($error_field) {
                    Alerts::add_field_error($message, $error_field);
                };
                break;

            case 'json_error':
                $return_error = function($message) use ($error_field) {
                    Response::json($message, 'error');
                };
                break;
        }

        $returned_file_name = $already_existing_file_name;

        if(!empty($_FILES[$file_key]['name'])) {
            $file_extension = mb_strtolower(pathinfo($_FILES[$file_key]['name'], PATHINFO_EXTENSION));
            $file_temp = $_FILES[$file_key]['tmp_name'];

            if($_FILES[$file_key]['error'] == UPLOAD_ERR_INI_SIZE) {
                $return_error(sprintf(l('global.error_message.file_size_limit'), get_max_upload()));
            }

            if($_FILES[$file_key]['error'] && $_FILES[$file_key]['error'] != UPLOAD_ERR_INI_SIZE) {
                $return_error(l('global.error_message.file_upload') . ' (' . $_FILES[$file_key]['error'] . ')');
            }

            if(!in_array($file_extension, Uploads::get_whitelisted_file_extensions($uploads_file_key))) {
                $return_error(l('global.error_message.invalid_file_type'));
            }

            if(!\Altum\Plugin::is_active('offload') || (\Altum\Plugin::is_active('offload') && !settings()->offload->uploads_url)) {
                if(!is_writable(UPLOADS_PATH . Uploads::get_path($uploads_file_key))) {
                    $return_error(sprintf(l('global.error_message.directory_not_writable'), UPLOADS_PATH . Uploads::get_path($uploads_file_key)));
                }
            }

            if($size_limit && $_FILES[$file_key]['size'] > $size_limit * 1000000) {
                $return_error(sprintf(l('global.error_message.file_size_limit'), $size_limit));
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

                /* Generate new name for image */
                $image_new_name = md5(time() . rand() . rand()) . '.' . $file_extension;

                /* Try to compress the image */
                if(\Altum\Plugin::is_active('image-optimizer') && settings()->image_optimizer->is_enabled) {
                    \Altum\Plugin\ImageOptimizer::optimize($file_temp, $image_new_name, $_FILES[$file_key]['name'], Uploads::get_path($uploads_file_key));
                }

                /* Sanitize SVG uploads */
                if($file_extension == 'svg') {
                    $svg_sanitizer = new \enshrined\svgSanitize\Sanitizer();
                    $dirty_svg = file_get_contents($file_temp);
                    $clean_svg = $svg_sanitizer->sanitize($dirty_svg);
                    file_put_contents($file_temp, $clean_svg);
                }

                /* Offload uploading */
                if(\Altum\Plugin::is_active('offload') && settings()->offload->uploads_url) {
                    try {
                        $s3 = new \Aws\S3\S3Client(get_aws_s3_config());

                        /* Delete current file */
                        if(!empty($already_existing_file_name)) {
                            $s3->deleteObject([
                                'Bucket' => settings()->offload->storage_name,
                                'Key' => UPLOADS_URL_PATH . Uploads::get_path($uploads_file_key) . $already_existing_file_name,
                            ]);
                        }

                        /* Upload image */
                        $result = $s3->putObject([
                            'Bucket' => settings()->offload->storage_name,
                            'Key' => UPLOADS_URL_PATH . Uploads::get_path($uploads_file_key) . $image_new_name,
                            'ContentType' => mime_content_type($file_temp),
                            'SourceFile' => $file_temp,
                            'ACL' => 'public-read'
                        ]);
                    } catch (\Exception $exception) {
                        $return_error($exception->getMessage());
                    }
                }

                /* Local uploading */
                else {
                    /* Delete current image */
                    if(!empty($already_existing_file_name) && file_exists(UPLOADS_PATH . Uploads::get_path($uploads_file_key) . $already_existing_file_name)) {
                        unlink(UPLOADS_PATH . Uploads::get_path($uploads_file_key) . $already_existing_file_name);
                    }

                    /* Upload the original */
                    move_uploaded_file($file_temp, UPLOADS_PATH . Uploads::get_path($uploads_file_key) . $image_new_name);
                }

                /* Returned value */
                $returned_file_name = $image_new_name;
            }
        }

        /* Check for the removal of the already uploaded file */
        if(isset($_POST[$file_key_remove])) {
            if(!empty($already_existing_file_name)) {
                /* Offload deleting */
                if(\Altum\Plugin::is_active('offload') && settings()->offload->uploads_url) {
                    $s3 = new \Aws\S3\S3Client(get_aws_s3_config());
                    $s3->deleteObject([
                        'Bucket' => settings()->offload->storage_name,
                        'Key' => UPLOADS_URL_PATH . Uploads::get_path($uploads_file_key) . $already_existing_file_name,
                    ]);
                }

                /* Local deleting */
                else if(file_exists(UPLOADS_PATH . Uploads::get_path($uploads_file_key) . $already_existing_file_name)) {
                    unlink(UPLOADS_PATH . Uploads::get_path($uploads_file_key) . $already_existing_file_name);
                }
            }

            /* Returned value */
            $returned_file_name = '';
        }

        return $returned_file_name;
    }

    public static function validate_upload($uploads_file_key, $file_key, $size_limit, $error_response_type = 'error', $error_field = null) {
        /* Determine the error response */
        $return_error = null;
        switch($error_response_type) {
            case 'error':
                $return_error = function($message) {
                    Alerts::add_error($message);
                };
                break;

            case 'field_error':
                $return_error = function($message) use ($error_field) {
                    Alerts::add_field_error($message, $error_field);
                };
                break;

            case 'json_error':
                $return_error = function($message) use ($error_field) {
                    Response::json($message, 'error');
                };
                break;
        }

        if(!empty($_FILES[$file_key]['name'])) {
            $file_extension = mb_strtolower(pathinfo($_FILES[$file_key]['name'], PATHINFO_EXTENSION));
            $file_temp = $_FILES[$file_key]['tmp_name'];

            if($_FILES[$file_key]['error'] == UPLOAD_ERR_INI_SIZE) {
                $return_error(sprintf(l('global.error_message.file_size_limit'), get_max_upload()));
            }

            if($_FILES[$file_key]['error'] && $_FILES[$file_key]['error'] != UPLOAD_ERR_INI_SIZE) {
                $return_error(l('global.error_message.file_upload') . ' (' . $_FILES[$file_key]['error'] . ')');
            }

            if(!in_array($file_extension, Uploads::get_whitelisted_file_extensions($uploads_file_key))) {
                $return_error(l('global.error_message.invalid_file_type'));
            }

            if(!\Altum\Plugin::is_active('offload') || (\Altum\Plugin::is_active('offload') && !settings()->offload->uploads_url)) {
                if(!is_writable(UPLOADS_PATH . Uploads::get_path($uploads_file_key))) {
                    $return_error(sprintf(l('global.error_message.directory_not_writable'), UPLOADS_PATH . Uploads::get_path($uploads_file_key)));
                }
            }

            if($size_limit && $_FILES[$file_key]['size'] > $size_limit * 1000000) {
                $return_error(sprintf(l('global.error_message.file_size_limit'), $size_limit));
            }
        }

        return null;
    }

    public static function copy_uploaded_file($already_existing_file_name, $already_existing_folder_path, $destination_folder_path, $error_response_type = 'error', $error_field = null) {
        if(!$already_existing_file_name) return null;

        /* Determine the error response */
        $return_error = null;
        switch($error_response_type) {
            case 'error':
                $return_error = function($message) {
                    Alerts::add_error($message);
                };
                break;

            case 'field_error':
                $return_error = function($message) use ($error_field) {
                    Alerts::add_field_error($message, $error_field);
                };
                break;

            case 'json_error':
                $return_error = function($message) use ($error_field) {
                    Response::json($message, 'error');
                };
                break;
        }

        $file_extension = mb_strtolower(pathinfo($already_existing_file_name, PATHINFO_EXTENSION));
        $file_new_name = md5(time() . rand()) . '.' . $file_extension;

        /* Offload uploading */
        if(\Altum\Plugin::is_active('offload') && settings()->offload->uploads_url) {
            try {
                $s3 = new \Aws\S3\S3Client(get_aws_s3_config());

                /* Copy file */
                $s3->copyObject([
                    'Bucket' => settings()->offload->storage_name,
                    'Key' => UPLOADS_URL_PATH . $destination_folder_path . $file_new_name,
                    'CopySource' => settings()->offload->storage_name . '/' . UPLOADS_URL_PATH . $already_existing_folder_path . $already_existing_file_name,
                    'ACL' => 'public-read',
                ]);

            } catch (\Exception $exception) {
                $return_error($exception->getMessage());
            }
        }

        /* Local uploading */
        else {
            copy(UPLOADS_PATH . $already_existing_folder_path . $already_existing_file_name, UPLOADS_PATH . $destination_folder_path . $file_new_name);
        }

        return $file_new_name;
    }

    public static function delete_uploaded_file($already_existing_file_name, $uploads_file_key) {
        if(!empty($already_existing_file_name)) {
            /* Offload deleting */
            if(\Altum\Plugin::is_active('offload') && settings()->offload->uploads_url) {
                $s3 = new \Aws\S3\S3Client(get_aws_s3_config());

                /* Delete */
                $s3->deleteObject([
                    'Bucket' => settings()->offload->storage_name,
                    'Key' => UPLOADS_URL_PATH . Uploads::get_path($uploads_file_key) . $already_existing_file_name,
                ]);
            }

            /* Local deleting */
            else if(file_exists(UPLOADS_PATH . Uploads::get_path($uploads_file_key) . $already_existing_file_name)) {
                unlink(UPLOADS_PATH . Uploads::get_path($uploads_file_key) . $already_existing_file_name);
            }
        }
    }

    public static function download_files_as_zip($files = [], $file_name = null) {
        if(is_null($file_name) && \Altum\Title::get()) {
            $file_name = \Altum\Title::get();
        }

        /* Create zipstream object */
        $zip = new \ZipStream\ZipStream(
            \ZipStream\OperationMode::NORMAL,
            '',
            null,
            \ZipStream\CompressionMethod::DEFLATE,
            6,
            true,
            true,
            true,
            null,
            get_slug($file_name) . '.zip',
            flushOutput: true,
        );

        /* Output file data to be downloaded */
        if(!\Altum\Plugin::is_active('offload') || (\Altum\Plugin::is_active('offload') && !settings()->offload->uploads_url)) {

            foreach($files as $entry_name => $entry_path) {

                $full_path = UPLOADS_PATH . $entry_path . $entry_name;

                /* Handle folders recursively */
                if(is_dir($full_path)) {
                    self::add_folder_to_zip($zip, $full_path, $entry_name . '/');
                }

                /* Handle single file */
                elseif(file_exists($full_path)) {
                    $zip->addFileFromPath($entry_name, $full_path);
                }
            }
        }

        /* Offload storage (no support for folders via stream wrappers) */
        else {

            try {
                $s3 = new \Aws\S3\S3Client(get_aws_s3_config());
                $s3->registerStreamWrapper();
            } catch (\Exception $exception) {
                Alerts::add_error($exception->getMessage());
                redirect();
            }

            foreach($files as $file_name => $file_path) {

                $remote_path = 's3://' . settings()->offload->storage_name . '/' . UPLOADS_URL_PATH . $file_path . $file_name;

                /* Make sure it's a file (not a folder) */
                if(is_file($remote_path)) {
                    $file_source = @fopen($remote_path, 'rb');
                    if($file_source) {
                        $zip->addFileFromStream($file_name, $file_source);
                        fclose($file_source);
                    }
                }
            }
        }

        $zip->finish();
        die();
    }

    /* Helper function to recursively add folder contents to zip */
    private static function add_folder_to_zip($zip, $folder_path, $zip_path_prefix = '') {
        $folder_path = rtrim($folder_path, '/');
        $base_path_length = strlen($folder_path) + 1;

        $directory_iterator = new \RecursiveDirectoryIterator(
            $folder_path,
            \FilesystemIterator::SKIP_DOTS | \FilesystemIterator::CURRENT_AS_FILEINFO
        );

        $iterator = new \RecursiveIteratorIterator(
            $directory_iterator,
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach($iterator as $item) {
            $full_path = $item->getPathname();
            $relative_path = $zip_path_prefix . substr($full_path, $base_path_length);

            if($item->isDir()) {
                /* Add directory with trailing slash */
                $zip->addDirectory(rtrim($relative_path, '/') . '/');
            } elseif($item->isFile()) {
                $zip->addFileFromPath($relative_path, $full_path);
            }
        }
    }

    public static function download_image_from_url($image_url, $uploads_file_key, $save_file_name, $allowed_mime_types = [], $error_response_type = 'error', $error_field = null) {
        /* Determine the error response */
        $return_error = null;
        switch($error_response_type) {
            case 'error':
                $return_error = function($message) {
                    Alerts::add_error($message);
                };
                break;
            case 'field_error':
                $return_error = function($message) use ($error_field) {
                    Alerts::add_field_error($message, $error_field);
                };
                break;
            case 'json_error':
                $return_error = function($message) use ($error_field) {
                    Response::json($message, 'error');
                };
                break;
        }

        $returned_file_name = null;

        /* Validate the URL */
        if(!filter_var($image_url, FILTER_VALIDATE_URL)) {
            $return_error('Invalid image URL.');
            return null;
        }

        /* Get file extension from URL */
        $parsed_url = parse_url($image_url);
        $url_path = isset($parsed_url['path']) ? $parsed_url['path'] : '';
        $file_extension = mb_strtolower(pathinfo($url_path, PATHINFO_EXTENSION));

        if(!$file_extension) {
            $return_error('URL does not contain a file extension.');
            return null;
        }

        /* Set temp file path */
        $temp_file_path = sys_get_temp_dir() . '/' . md5(time() . rand() . rand()) . '.' . $file_extension;

        /* Check remote content-type header before download (not fully trusted) */
        $curl_handle = curl_init($image_url);
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl_handle, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl_handle, CURLOPT_TIMEOUT, 15);
        curl_setopt($curl_handle, CURLOPT_USERAGENT, 'Mozilla/5.0');
        curl_setopt($curl_handle, CURLOPT_HEADER, true);
        curl_setopt($curl_handle, CURLOPT_NOBODY, true);

        $curl_exec_result = curl_exec($curl_handle);
        $http_status = curl_getinfo($curl_handle, CURLINFO_HTTP_CODE);
        $remote_content_type = curl_getinfo($curl_handle, CURLINFO_CONTENT_TYPE);

        curl_close($curl_handle);

        if($http_status !== 200) {
            $return_error('Failed to fetch the image. HTTP status: ' . $http_status);
            return null;
        }

        if(!in_array($remote_content_type, $allowed_mime_types)) {
            $return_error('Remote mime type not allowed: ' . $remote_content_type);
            return null;
        }

        /* Download the image to temp file */
        $curl_handle = curl_init($image_url);
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl_handle, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl_handle, CURLOPT_TIMEOUT, 15);
        curl_setopt($curl_handle, CURLOPT_USERAGENT, 'Mozilla/5.0');
        $image_data = curl_exec($curl_handle);
        $http_status = curl_getinfo($curl_handle, CURLINFO_HTTP_CODE);
        curl_close($curl_handle);

        if($image_data === false || $http_status !== 200) {
            $return_error('Failed to download the image.');
            return null;
        }

        if(file_put_contents($temp_file_path, $image_data) === false) {
            $return_error('Failed to save the image to temp location.');
            return null;
        }

        /* Validate actual mime type after download */
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $actual_mime_type = finfo_file($finfo, $temp_file_path);
        finfo_close($finfo);

        if(!in_array($actual_mime_type, $allowed_mime_types)) {
            unlink($temp_file_path);
            $return_error('Downloaded file mime type not allowed: ' . $actual_mime_type);
            return null;
        }

        /* Validate that it's really an image (except SVG) */
        if($actual_mime_type !== 'image/svg+xml' && !@getimagesize($temp_file_path)) {
            unlink($temp_file_path);
            $return_error('Downloaded file is not a valid image.');
            return null;
        }

        /* SVG sanitize if needed */
        if($actual_mime_type === 'image/svg+xml' && class_exists('\enshrined\svgSanitize\Sanitizer')) {
            $svg_sanitizer = new \enshrined\svgSanitize\Sanitizer();
            $dirty_svg = file_get_contents($temp_file_path);
            $clean_svg = $svg_sanitizer->sanitize($dirty_svg);
            file_put_contents($temp_file_path, $clean_svg);
        }

        /* Try to compress/optimize the image if plugin enabled */
        if(\Altum\Plugin::is_active('image-optimizer') && settings()->image_optimizer->is_enabled) {
            \Altum\Plugin\ImageOptimizer::optimize($temp_file_path, $save_file_name . '.' . $file_extension, $save_file_name . '.' . $file_extension, Uploads::get_path($uploads_file_key));
        }

        /* Final file name with extension from URL */
        $final_file_name = $save_file_name . '.' . $file_extension;

        /* Offload/S3 uploading */
        if(\Altum\Plugin::is_active('offload') && settings()->offload->uploads_url) {
            try {
                $s3 = new \Aws\S3\S3Client(get_aws_s3_config());

                $s3->putObject([
                    'Bucket' => settings()->offload->storage_name,
                    'Key' => UPLOADS_URL_PATH . Uploads::get_path($uploads_file_key) . $final_file_name,
                    'ContentType' => $actual_mime_type,
                    'SourceFile' => $temp_file_path,
                    'ACL' => 'public-read'
                ]);
            } catch (\Exception $exception) {
                unlink($temp_file_path);
                $return_error($exception->getMessage());
                return null;
            }
            unlink($temp_file_path);
        }
        /* Local uploading */
        else {
            $save_directory_path = UPLOADS_PATH . Uploads::get_path($uploads_file_key);
            if(!is_writable($save_directory_path)) {
                unlink($temp_file_path);
                $return_error(sprintf(l('global.error_message.directory_not_writable'), $save_directory_path));
                return null;
            }

            $save_file_path = $save_directory_path . $final_file_name;

            if(file_exists($save_file_path)) {
                unlink($save_file_path);
            }

            if(rename($temp_file_path, $save_file_path) === false) {
                unlink($temp_file_path);
                $return_error('Failed to move the image to destination.');
                return null;
            }
        }

        $returned_file_name = $final_file_name;
        return $returned_file_name;
    }
}
