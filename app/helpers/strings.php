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

defined('ALTUMCODE') || die();


function e($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

function input_clean($string, $max_characters = null) {
    $wrapper_function = $max_characters ? function($string) use ($max_characters) { return mb_substr($string, 0, $max_characters); } : fn($string) => $string;
    return $wrapper_function(trim(strip_tags(filter_var_filter_string_polyfill($string ?? ''))));
}

function input_clean_name($string, $max_characters = null) {
    /* Allow valid name chars */
    $string = preg_replace('/[^\p{L}\p{M}\s\'\.\-]/u', '', $string);

    /* Remove domain-like patterns */
    $string = preg_replace('/\b\w+\.\w{2,}\b/u', '', $string);

    /* trim to maximum length if needed */
    if ($max_characters !== null) {
        $string = mb_substr($string, 0, $max_characters);
    }

    return $string;
}

function input_clean_email($string) {
    return mb_substr(mb_strtolower(filter_var($_POST['email'], FILTER_SANITIZE_EMAIL)), 0, 320);
}

function query_clean($string, $max_characters = null) {
    return mysql_escape_stringg(input_clean($string, $max_characters));
}

function array_query_clean($array) {
    return array_map('query_clean', $array);
}

function mysql_escape_stringg($unescaped_string) {
    $replacementMap = [
        "\0" => "\\0",
        "\n" => "\\n",
        "\r" => "\\r",
        "\t" => "\\t",
        chr(26) => "\\Z",
        chr(8) => "\\b",
        '"' => '\"',
        "'" => "\'",
        '\\' => '\\\\'
    ];

    return \strtr($unescaped_string, $replacementMap);
}

function filter_var_filter_string_polyfill($string) {
    $str = preg_replace('/\x00|<[^>]*>?/', '', $string);
    return str_replace(["'", '"'], ['&#39;', '&#34;'], $str);
}

function string_truncate($string, $maxchar, $ending = '..') {
    $length = mb_strlen($string ?? '');
    if($length > $maxchar) {
        $cutsize = -($length-$maxchar);
        $string  = mb_substr($string, 0, $cutsize);
        $string  = $string . $ending;
    }
    return $string;
}

function string_filter_alphanumeric($string) {

    $string = preg_replace('/[^a-zA-Z0-9\s]+/', '', $string);

    $string = preg_replace('/\s+/', ' ', $string);

    return $string;
}

function string_generate($length) {
    $characters = str_split('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz');
    $content = '';

    for($i = 1; $i <= $length; $i++) {
        $content .= $characters[array_rand($characters, 1)];
    }

    return $content;
}

function string_starts_with($needle, $haystack) {
    return mb_substr($haystack, 0, mb_strlen($needle)) === $needle;
}

function string_ends_with($needle, $haystack) {
    return mb_substr($haystack, -mb_strlen($needle)) === $needle;
}

function string_estimate_reading_time($string) {
    $total_words = str_word_count(strip_tags($string));

    /* 200 is the total amount of words read per minute */
    $minutes = floor($total_words / 200);
    $seconds = floor($total_words / 200 / (200 / 60));

    return (object) [
        'minutes' => $minutes,
        'seconds' => $seconds
    ];
}

function process_spintax($string) {
    return preg_replace_callback('/\{[^{}]*\|[^{}]*\}/', function ($match) {
        $content = substr($match[0], 1, -1);
        $words = explode('|', $content);
        return $words[array_rand($words)];
    }, $string);
}

/* validate and sanitize a hex color string */
function verify_hex_color($color) {
    /* check if input matches allowed hex color formats */
    if(preg_match('/^#(?:[A-Fa-f0-9]{3}|[A-Fa-f0-9]{4}|[A-Fa-f0-9]{6}|[A-Fa-f0-9]{8})$/', $color)) {
        return $color;
    }

    return false;
}

function output_blog_post_content($blog_post_content) {
    if (strip_tags($blog_post_content) != $blog_post_content) {
        /* Content has HTML, output as is */
        return $blog_post_content;
    } else {
        /* Content is plain text, nl2br */
        return nl2br($blog_post_content);
    }
}
