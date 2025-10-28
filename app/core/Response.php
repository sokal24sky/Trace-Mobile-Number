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

class Response {

    public static function json($message, $status = 'success', $details = []) {
        if(!is_array($message) && $message) $message = [$message];

        echo json_encode(
            [
                'message' 	=> $message,
                'status' 	=> $status,
                'details'	=> $details,
            ]
        );


        die();
    }

    /* jsonapi.org */
    public static function jsonapi_success($data, $meta = null, $response_code = 200, $others = null) {
        http_response_code($response_code);

        $response = [
            'data' => $data
        ];

        if($meta) {
            $response['meta'] = $meta;
        };

        if($others) {
            $response = array_merge($response, $others);
        }

        echo json_encode($response);

        die();
    }

    public static function jsonapi_error($errors, $meta = null, $response_code = 400) {
        http_response_code($response_code);

        $response = [
            'errors' => $errors
        ];

        if($meta) {
            $response['meta'] = $meta;
        };

        echo json_encode($response);

        die();
    }


    public static function simple_json($response) {

        echo json_encode($response);

        die();

    }

}
