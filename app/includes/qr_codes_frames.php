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

return [
    'circle_simple_bottom_text' => [
        'svg' => '<svg width="%1$s" height="%2$s" viewBox="0 0 %1$s %2$s" fill="%4$s" xmlns="http://www.w3.org/2000/svg">
                    <g transform="scale(%3$s)">
                        <path d="M12,1c6.07,0,11,4.93,11,11s-4.93,11-11,11S1,18.07,1,12S5.93,1,12,1 M12,0C5.37,0,0,5.37,0,12s5.37,12,12,12s12-5.37,12-12S18.63,0,12,0L12,0z M24,26.17L24,26.17c0-1.01-0.82-1.83-1.83-1.83H1.83C0.82,24.35,0,25.16,0,26.17v0C0,27.18,0.82,28,1.83,28h20.35C23.18,28,24,27.18,24,26.17z M24,26.17L24,26.17c0-1.01-0.82-1.83-1.83-1.83H1.83C0.82,24.35,0,25.16,0,26.17v0C0,27.18,0.82,28,1.83,28h20.35C23.18,28,24,27.18,24,26.17z"></path>
                    </g>
                </svg>',
        'frame_height_scale' => 1.18,
        'frame_scale' => 24,
        'frame_translate_x' => 0,
        'frame_translate_y' => 0,

        'qr_background_scale' => 1.06,
        'qr_background_x' => 33.33,
        'qr_background_y' => 33.33,
        'qr_background_type' => 'circle',

        'qr_scale' => .6,
        'qr_translate_x' => 3,
        'qr_translate_y' => 3,

        'frame_text_x' => 50, // %
        'frame_text_y' => 93, // %
        'frame_text_size_scale' => 13,
        'frame_text_size_min_scale' => 50,
    ],

    'round_simple_bottom_text' => [
        'svg' => '<svg width="%1$s" height="%2$s" viewBox="0 0 %1$s %2$s" fill="%4$s" xmlns="http://www.w3.org/2000/svg">
                    <g transform="scale(%3$s)">
                        <path d="M21,1c1.1,0,2,0.9,2,2v18c0,1.1-0.9,2-2,2H3c-1.1,0-2-0.9-2-2V3c0-1.1,0.9-2,2-2H21 M21,0H3C1.34,0,0,1.34,0,3v18c0,1.66,1.34,3,3,3h18c1.66,0,3-1.34,3-3V3C24,1.34,22.66,0,21,0L21,0z"></path>
                    </g>
                </svg>',
        'frame_height_scale' => 1.2,
        'frame_scale' => 24,
        'frame_translate_x' => 0,
        'frame_translate_y' => 0,

        'qr_background_scale' => 1.06,
        'qr_background_x' => 33.33,
        'qr_background_y' => 33.33,
        'qr_background_type' => 'square',

        'qr_scale' => .85,
        'qr_translate_x' => 11.5,
        'qr_translate_y' => 11.5,

        'frame_text_x' => 50, // %
        'frame_text_y' => 92, // %
        'frame_text_size_scale' => 13,
        'frame_text_size_min_scale' => 50,
    ],

    'straight_simple_bottom_text' => [
        'svg' => '<svg width="%1$s" height="%2$s" viewBox="0 0 %1$s %2$s" fill="%4$s" xmlns="http://www.w3.org/2000/svg">
                    <g transform="scale(%3$s)">
                        <path d="M23,1v22H1V1H23 M24,0H0v24h24V0L24,0z"></path>
                    </g>
                </svg>',
        'frame_height_scale' => 1.2,
        'frame_scale' => 24,
        'frame_translate_x' => 0,
        'frame_translate_y' => 0,

        'qr_background_scale' => 1.06,
        'qr_background_x' => 33.33,
        'qr_background_y' => 33.33,
        'qr_background_type' => 'square',

        'qr_scale' => .85,
        'qr_translate_x' => 11.5,
        'qr_translate_y' => 11.5,

        'frame_text_x' => 50, // %
        'frame_text_y' => 92, // %
        'frame_text_size_scale' => 13,
        'frame_text_size_min_scale' => 50,
    ],

    'round_card_bottom_text' => [
        'svg' => '<svg xmlns="http://www.w3.org/2000/svg" xml:space="preserve" xmlns:xlink="http://www.w3.org/1999/xlink" width="%1$s" height="%2$s" viewBox="0 0 %1$s %2$s" fill="%4$s">
                    <g transform="scale(%3$s)">
                        <path d="m1.5332 0c-0.84952 1.4803e-16 -1.5332 0.68368-1.5332 1.5332v25.934c-1.4803e-16 0.84952 0.68368 1.5332 1.5332 1.5332h20.934c0.84952 0 1.5332-0.68368 1.5332-1.5332v-25.934c0-0.84952-0.68368-1.5332-1.5332-1.5332h-20.934zm10.447 2a10 10 0 0 1 0.019531 0 10 10 0 0 1 10 10 10 10 0 0 1-10 10 10 10 0 0 1-10-10 10 10 0 0 1 9.9805-10z"/>
                    </g>
                </svg>',
        'frame_height_scale' => 1.21,
        'frame_scale' => 24,
        'frame_translate_x' => 0,
        'frame_translate_y' => 0,

        'qr_background_scale' => 1.06,
        'qr_background_x' => 33.33,
        'qr_background_y' => 33.33,
        'qr_background_type' => 'square',

        'qr_scale' => .55,
        'qr_translate_x' => 2.5,
        'qr_translate_y' => 2.5,

        'frame_text_x' => 50, // %
        'frame_text_y' => 88, // %
        'frame_text_size_scale' => 13,
        'frame_text_size_min_scale' => 50,
    ],

    'straight_bottom_text' => [
        'svg' => '<svg width="%1$s" height="%2$s" viewBox="0 0 %1$s %2$s" fill="%4$s" xmlns="http://www.w3.org/2000/svg">
                    <g transform="scale(%3$s) translate(0 0)">
                        <path d="M24,28H0V0h24V28z M23,0.94H1v22h22V0.94z"></path>
                    </g>
                </svg>',
        'frame_height_scale' => 1.167,
        'frame_scale' => 24,
        'frame_translate_x' => 0,
        'frame_translate_y' => 0,

        'qr_background_scale' => 1.06,
        'qr_background_x' => 33.33,
        'qr_background_y' => 33.33,
        'qr_background_type' => 'square',

        'qr_scale' => .85,
        'qr_translate_x' => 11.5,
        'qr_translate_y' => 11.5,

        'frame_text_x' => 50, // %
        'frame_text_y' => 92, // %
        'frame_text_size_scale' => 13,
        'frame_text_size_min_scale' => 50,
    ],

    'straight_top_text' => [
        'svg' => '<svg width="%1$s" height="%2$s" viewBox="0 0 %1$s %2$s" fill="%4$s" xmlns="http://www.w3.org/2000/svg">
                        <g transform="scale(%3$s) translate(0 0)">
                            <path d="M0,0h24v28H0V0z M1,27.06h22v-22H1V27.06z"></path>
                        </g>
                    </svg>',
        'frame_height_scale' => 1.167,
        'frame_scale' => 24,
        'frame_translate_x' => 0,
        'frame_translate_y' => 0,

        'qr_background_scale' => 1.06,
        'qr_background_x' => 33.33,
        'qr_background_y' => 5,
        'qr_background_type' => 'square',

        'qr_scale' => .85,
        'qr_translate_x' => 11.5,
        'qr_translate_y' => 3.5,

        'frame_text_x' => 50, // %
        'frame_text_y' => 10, // %
        'frame_text_size_scale' => 15,
        'frame_text_size_min_scale' => 50,
    ],

    'round_bottom_text' => [
        'svg' => '<svg width="%1$s" height="%2$s" viewBox="0 0 %1$s %2$s" fill="%4$s" xmlns="http://www.w3.org/2000/svg">
                        <g transform="scale(%3$s) translate(0 0)">
                            <path d="M22.7,0H1.3C0.6,0,0,0.6,0,1.3v25.3C0,27.4,0.6,28,1.3,28h21.3c0.7,0,1.3-0.6,1.3-1.3V1.3C24,0.6,23.4,0,22.7,0 z M23,22c0,0.6-0.5,1-1,1H2c-0.6,0-1-0.5-1-1V2c0-0.6,0.5-1,1-1h20c0.6,0,1,0.5,1,1V22z"></path>
                        </g>
                    </svg>',
        'frame_height_scale' => 1.167,
        'frame_scale' => 24,
        'frame_translate_x' => 0,
        'frame_translate_y' => 0,

        'qr_background_scale' => 1.06,
        'qr_background_x' => 33.33,
        'qr_background_y' => 33.33,
        'qr_background_type' => 'square',

        'qr_scale' => .85,
        'qr_translate_x' => 11.5,
        'qr_translate_y' => 11.5,

        'frame_text_x' => 50, // %
        'frame_text_y' => 92, // %
        'frame_text_size_scale' => 13,
        'frame_text_size_min_scale' => 50,
    ],

    'round_top_text' => [
        'svg' => '<svg width="%1$s" height="%2$s" viewBox="0 0 %1$s %2$s" fill="%4$s" xmlns="http://www.w3.org/2000/svg">
                        <g transform="scale(%3$s) translate(0 0)">
                            <path d="M1.3,28L22.6,28c0.7,0,1.3-0.6,1.3-1.3L24,1.4c0-0.7-0.6-1.3-1.3-1.3L1.4,0C0.7,0,0.1,0.6,0,1.3L0,26.6 C-0.1,27.4,0.5,28,1.3,28z M1,6c0-0.6,0.5-1,1-1L22,5c0.6,0,1,0.5,1,1L23,26c0,0.6-0.5,1-1,1L2,27c-0.6,0-1-0.5-1-1L1,6z"></path>
                        </g>
                    </svg>',
        'frame_height_scale' => 1.167,
        'frame_scale' => 24,
        'frame_translate_x' => 0,
        'frame_translate_y' => 0,

        'qr_background_scale' => 1.06,
        'qr_background_x' => 33.33,
        'qr_background_y' => 5,
        'qr_background_type' => 'square',

        'qr_scale' => .85,
        'qr_translate_x' => 11.5,
        'qr_translate_y' => 3.5,

        'frame_text_x' => 50, // %
        'frame_text_y' => 10, // %
        'frame_text_size_scale' => 15,
        'frame_text_size_min_scale' => 50,
    ],

    'tooltip_bottom_text' => [
        'svg' => '<svg width="%1$s" height="%2$s" viewBox="0 0 %1$s %2$s" fill="%4$s" xmlns="http://www.w3.org/2000/svg">
                        <g transform="scale(%3$s) translate(0 0)">
                            <path d="M1.3,24l21.3,0c0.7,0,1.3-0.6,1.3-1.3l0-21.3C24,0.6,23.4,0,22.7,0L1.3,0C0.6,0,0,0.6,0,1.3l0,21.3 C0,23.4,0.6,24,1.3,24z M1,2c0-0.6,0.5-1,1-1l20,0c0.6,0,1,0.5,1,1v20c0,0.6-0.5,1-1,1L2,23c-0.6,0-1-0.5-1-1V2z"></path>
                            <path d="M1,30h22c0.5,0,1-0.4,1-1v-3c0-0.5-0.4-1-1-1H13l-1-1l-1,1H1c-0.5,0-1,0.4-1,1v3C0,29.6,0.4,30,1,30z"></path>
                        </g>
                    </svg>',
        'frame_height_scale' => 1.25,
        'frame_scale' => 24,
        'frame_translate_x' => 0,
        'frame_translate_y' => 0,

        'qr_background_scale' => 1.06,
        'qr_background_x' => 33.33,
        'qr_background_y' => 33.33,
        'qr_background_type' => 'square',

        'qr_scale' => .85,
        'qr_translate_x' => 11.5,
        'qr_translate_y' => 11.5,

        'frame_text_x' => 50, // %
        'frame_text_y' => 92, // %
        'frame_text_size_scale' => 13,
        'frame_text_size_min_scale' => 50,
    ],

    'tooltip_top_text' => [
        'svg' => '<svg width="%1$s" height="%2$s" viewBox="0 0 %1$s %2$s" fill="%4$s" xmlns="http://www.w3.org/2000/svg">
                        <g transform="scale(%3$s) translate(0 0)">
                            <path d="M22.7,6L1.3,6C0.6,6,0,6.6,0,7.3l0,21.3C0,29.4,0.6,30,1.3,30l21.3,0c0.7,0,1.3-0.6,1.3-1.3l0-21.3 C24,6.6,23.4,6,22.7,6z M23,28c0,0.6-0.5,1-1,1L2,29c-0.6,0-1-0.5-1-1V8c0-0.6,0.5-1,1-1l20,0c0.6,0,1,0.5,1,1V28z"></path><path d="M23,0H1C0.4,0,0,0.4,0,1v3c0,0.5,0.4,1,1,1h10l1,1l1-1h10c0.5,0,1-0.4,1-1V1C24,0.4,23.6,0,23,0z"></path>
                        </g>
                    </svg>',
        'frame_height_scale' => 1.25,
        'frame_scale' => 24,
        'frame_translate_x' => 0,
        'frame_translate_y' => 0,

        'qr_background_scale' => 1.06,
        'qr_background_x' => 33.33,
        'qr_background_y' => 3.5,
        'qr_background_type' => 'square',

        'qr_scale' => .85,
        'qr_translate_x' => 11.5,
        'qr_translate_y' => 2.6,

        'frame_text_x' => 50, // %
        'frame_text_y' => 8.5, // %
        'frame_text_size_scale' => 15,
        'frame_text_size_min_scale' => 50,
    ],

    'ribbon_bottom_text' => [
        'svg' => '<svg width="%1$s" height="%2$s" viewBox="0 0 %1$s %2$s" fill="%4$s" xmlns="http://www.w3.org/2000/svg">
                        <g transform="scale(%3$s) translate(0 0)">
                            <path d="M24,21h-1.7V1.7H1.7V21H0l1,2l-1,2h1v2h22v-2h1l-1-2L24,21z M2,2h20v19v1H2v-1V2z"></path>
                        </g>
                    </svg>',
        'frame_height_scale' => 1.1,
        'frame_scale' => 24,
        'frame_translate_x' => 0,
        'frame_translate_y' => 0,

        'qr_background_scale' => 1.17,
        'qr_background_x' => 13.88,
        'qr_background_y' => 13.88,
        'qr_background_type' => 'square',

        'qr_scale' => .8,
        'qr_translate_x' => 8,
        'qr_translate_y' => 8,

        'frame_text_x' => 50, // %
        'frame_text_y' => 92, // %
        'frame_text_size_scale' => 13,
        'frame_text_size_min_scale' => 50,
    ],

    'ribbon_top_text' => [
        'svg' => '<svg width="%1$s" height="%2$s" viewBox="0 0 %1$s %2$s" fill="%4$s" xmlns="http://www.w3.org/2000/svg">
                        <g transform="scale(%3$s) translate(0 0)">
                            <path d="M0,6h1.7v19.3h20.7V6H24l-1-2l1-2h-1V0H1v2H0l1,2L0,6z M22,25H2V6V5h20v1V25z"></path>
                        </g>
                    </svg>',
        'frame_height_scale' => 1.1,
        'frame_scale' => 24,
        'frame_translate_x' => 0,
        'frame_translate_y' => 0,

        'qr_background_scale' => 1.17,
        'qr_background_x' => 13.88,
        'qr_background_y' => 5,
        'qr_background_type' => 'square',

        'qr_scale' => .8,
        'qr_translate_x' => 8,
        'qr_translate_y' => 3.55,

        'frame_text_x' => 50, // %
        'frame_text_y' => 10, // %
        'frame_text_size_scale' => 15,
        'frame_text_size_min_scale' => 50,
    ],

    'tooltip_snap_top_text' => [
        'svg' => '<svg width="%1$s" height="%2$s" viewBox="0 0 %1$s %2$s" fill="%4$s" xmlns="http://www.w3.org/2000/svg">
                        <g transform="scale(%3$s) translate(0 0)">
                            <g transform="matrix(2.399191, 0, 0, 2.399191, -109.938606, -7.37865)" style="">
                              <path d="M224.88,93.12h19.39a5,5,0,0,1,5,5v18.73H254V98.12a9.68,9.68,0,0,0-9.68-9.68H224.88Z"></path>
                              <path d="M50.73,116.85V98.12a5,5,0,0,1,5-5H73.8V88.44H55.73a9.68,9.68,0,0,0-9.68,9.68v18.73Z"></path>
                              <path d="M73.8,291.67H55.73a5,5,0,0,1-5-5V267.94H46.05v18.73a9.68,9.68,0,0,0,9.68,9.68H73.8Z"></path>
                              <path d="M249.27,267.94v18.73a5,5,0,0,1-5,5H224.88v4.68h19.39a9.68,9.68,0,0,0,9.68-9.68V267.94Z"></path>
                              <path d="M244.75,3.65H55.45A9.25,9.25,0,0,0,46.2,12.9V54.46a9.25,9.25,0,0,0,9.25,9.26H126a2.32,2.32,0,0,1,1.64.67l20.74,20.74a2.33,2.33,0,0,0,3.28,0l20.75-20.74a2.28,2.28,0,0,1,1.64-.67h70.58a9.25,9.25,0,0,0,9.25-9.26V12.9A9.18,9.18,0,0,0,244.75,3.65Z"></path>
                            </g>
                        </g>
                    </svg>',
        'frame_height_scale' => 1.41,
        'frame_scale' => 500,
        'frame_translate_x' => 0,
        'frame_translate_y' => 0,

        'qr_background_scale' => 1.041,
        'qr_background_x' => 62.5,
        'qr_background_y' => 2.325,
        'qr_background_type' => 'square',

        'qr_scale' => .85,
        'qr_translate_x' => 11.75,
        'qr_translate_y' => 1.75,

        'frame_text_x' => 50, // %
        'frame_text_y' => 10, // %
        'frame_text_size_scale' => 15,
        'frame_text_size_min_scale' => 50,
    ],

    'tooltip_snap_bottom_text' => [
        'svg' => '<svg width="%1$s" height="%2$s" viewBox="0 0 %1$s %2$s" fill="%4$s" xmlns="http://www.w3.org/2000/svg">
                        <g transform="scale(%3$s) translate(0 0)">
                            <g transform="matrix(2.399191, 0, 0, 2.399191, -109.938606, -7.37865)" style="">
                              <path d="M 225.107 7.755 L 244.497 7.755 C 247.258 7.755 249.497 9.994 249.497 12.755 L 249.497 31.485 L 254.227 31.485 L 254.227 12.755 C 254.227 7.409 249.893 3.075 244.547 3.075 L 225.107 3.075 L 225.107 7.755 Z"></path>
                              <path d="M 50.393 31.485 L 50.393 12.755 C 50.393 9.994 52.632 7.755 55.393 7.755 L 73.463 7.755 L 73.463 3.075 L 55.393 3.075 C 50.047 3.075 45.713 7.409 45.713 12.755 L 45.713 31.485 L 50.393 31.485 Z"></path>
                              <path d="M 73.573 206.799 L 55.503 206.799 C 52.742 206.799 50.503 204.56 50.503 201.799 L 50.503 183.069 L 45.823 183.069 L 45.823 201.799 C 45.823 207.145 50.157 211.479 55.503 211.479 L 73.573 211.479 L 73.573 206.799 Z"></path>
                              <path d="M 249.547 183.069 L 249.547 201.799 C 249.547 204.56 247.308 206.799 244.547 206.799 L 225.157 206.799 L 225.157 211.479 L 244.547 211.479 C 249.893 211.479 254.227 207.145 254.227 201.799 L 254.227 183.069 L 249.547 183.069 Z"></path>
                              <path d="M 244.68 214.77 L 55.38 214.77 C 50.272 214.77 46.13 218.911 46.13 224.02 L 46.13 265.58 C 46.125 270.692 50.268 274.84 55.38 274.84 L 125.93 274.84 C 126.544 274.837 127.134 275.078 127.57 275.51 L 148.31 296.25 C 149.219 297.15 150.682 297.15 151.59 296.25 L 172.34 275.51 C 172.774 275.075 173.366 274.833 173.98 274.84 L 244.56 274.84 C 249.673 274.84 253.816 270.692 253.81 265.58 L 253.81 224.02 C 253.849 218.942 249.758 214.797 244.68 214.77 Z" style="transform-box: fill-box; transform-origin: 50% 50%;" transform="matrix(-1, 0, 0, -1, -0.000006, -0.000011)"></path>
                            </g>
                        </g>
                    </svg>',
        'frame_height_scale' => 1.41,
        'frame_scale' => 500,
        'frame_translate_x' => 0,
        'frame_translate_y' => 0,

        'qr_background_scale' => 1.041,
        'qr_background_x' => 50,
        'qr_background_y' => 50,
        'qr_background_type' => 'square',

        'qr_scale' => .85,
        'qr_translate_x' => 11.75,
        'qr_translate_y' => 11.75,

        'frame_text_x' => 50, // %
        'frame_text_y' => 90, // %
        'frame_text_size_scale' => 13,
        'frame_text_size_min_scale' => 50,
    ],

    'straight_semi_bordered_bottom_text' => [
        'svg' => '<svg width="%1$s" height="%2$s" viewBox="0 0 %1$s %2$s" fill="%4$s" xmlns="http://www.w3.org/2000/svg">
                        <g transform="scale(%3$s)">
                            <path d="M24,24h-6v-1h5v-5h1V24z M6,23H1v-5H0v6h6V23z M1,1h5V0H0v6h1V1z M24,0h-6v1h5v5h1V0z"></path>
                        </g>
                    </svg>',
        'frame_height_scale' => 1.2,
        'frame_scale' => 24,
        'frame_translate_x' => 0,
        'frame_translate_y' => 0,

        'qr_background_scale' => 1.04,
        'qr_background_x' => 50,
        'qr_background_y' => 50,
        'qr_background_type' => 'square',

        'qr_scale' => .85,
        'qr_translate_x' => 11.75,
        'qr_translate_y' => 11.75,

        'frame_text_x' => 50, // %
        'frame_text_y' => 92, // %
        'frame_text_size_scale' => 13,
        'frame_text_size_min_scale' => 50,
    ],

    'round_semi_bordered_bottom_text' => [
        'svg' => '<svg width="%1$s" height="%2$s" viewBox="0 0 %1$s %2$s" fill="%4$s" xmlns="http://www.w3.org/2000/svg">
                        <g transform="scale(%3$s)">
                            <path d="M6.28,0.5c0,0.28-0.22,0.5-0.5,0.5H2C1.45,1,1,1.45,1,2v3.74c0,0.01,0.01,0.02,0.01,0.04c0,0.28-0.22,0.5-0.5,0.5
                                c-0.28,0-0.5-0.22-0.5-0.5H0V2c0-1.1,0.9-2,2-2h3.78C6.06,0,6.28,0.22,6.28,0.5z M24,5.78V2c0-1.1-0.9-2-2-2h-3.78v0.01
                                c-0.28,0-0.5,0.22-0.5,0.5c0,0.28,0.22,0.5,0.5,0.5c0.01,0,0.03-0.01,0.04-0.01H22c0.55,0,1,0.45,1,1v3.78c0,0.28,0.22,0.5,0.5,0.5
                                C23.78,6.28,24,6.06,24,5.78z M18.22,24H22c1.1,0,2-0.9,2-2v-3.78h-0.01c0-0.28-0.22-0.5-0.5-0.5c-0.28,0-0.5,0.22-0.5,0.5
                                c0,0.01,0.01,0.03,0.01,0.04V22c0,0.55-0.45,1-1,1h-3.78c-0.28,0-0.5,0.22-0.5,0.5C17.72,23.78,17.94,24,18.22,24z M0,18.22V22
                                c0,1.1,0.9,2,2,2h3.78v-0.01c0.28,0,0.5-0.22,0.5-0.5c0-0.28-0.22-0.5-0.5-0.5C5.77,22.99,5.76,23,5.74,23H2c-0.55,0-1-0.45-1-1
                                v-3.78c0-0.28-0.22-0.5-0.5-0.5C0.22,17.72,0,17.94,0,18.22z"></path>
                        </g>
                    </svg>',
        'frame_height_scale' => 1.2,
        'frame_scale' => 24,
        'frame_translate_x' => 0,
        'frame_translate_y' => 0,

        'qr_background_scale' => 1.04,
        'qr_background_x' => 50,
        'qr_background_y' => 50,
        'qr_background_type' => 'square',

        'qr_scale' => .85,
        'qr_translate_x' => 11.75,
        'qr_translate_y' => 11.75,

        'frame_text_x' => 50, // %
        'frame_text_y' => 92, // %
        'frame_text_size_scale' => 13,
        'frame_text_size_min_scale' => 50,
    ],

    'hand_arrows_bottom_text' => [
        'svg' => '<svg width="%1$s" height="%2$s" viewBox="0 0 %1$s %2$s" fill="%4$s"  xmlns="http://www.w3.org/2000/svg" xml:space="preserve">
                    <g transform="translate(%5$s, %6$s)  scale(%3$s)">
                        <path d="M47.4,263.1c-1.6,0-2.9-1.3-2.9-2.9V103.7c0-5.2,3.9-9.5,8.8-9.5h138.5c1.6,0,2.9,1.3,2.9,2.9c0,1.6-1.3,2.9-2.9,2.9H53.2
	c-1.7,0-3,1.7-3,3.8v156.5C50.2,261.8,49,263.1,47.4,263.1z"/>
	<path d="M266.7,325.2H128.2c-1.6,0-2.9-1.3-2.9-2.9c0-1.6,1.3-2.9,2.9-2.9h138.5c1.7,0,3-1.7,3-3.8V159.2c0-1.6,1.3-2.9,2.9-2.9
	c1.6,0,2.9,1.3,2.9,2.9v156.5C275.5,320.9,271.6,325.2,266.7,325.2L266.7,325.2z"/>
	<path d="M100,311.9H62.1c-3.3,0-5.6-0.6-6.8-1.9c-0.7-0.8-1-1.8-1-3.3c0.2-3,0.4-5.9,0.6-8.4c0.5-5.2,0.8-9.4,0.2-12.6
	c-0.8-4.3-3.3-6.8-7.4-7.4c-3.7-0.6-6.8,0.9-9,4.3c-1.7,2.6-2.8,6-4.2,9.9c-1,2.9-2,6-3.4,9.1c-0.4,0.9-1.3,1.4-3,2.3
	c-2.2,1.1-5.1,2.6-7.6,6.1c-0.4,0.6-0.9,1.3-1.2,1.9H0v5.8h16.7c-1.5,4.2-2.2,7.9-2.2,8.1c-0.3,1.6,0.8,3,2.3,3.3
	c1.6,0.3,3-0.8,3.3-2.3c0-0.1,1.5-8.5,5.1-13.4c1.7-2.3,3.6-3.4,5.6-4.4c2.1-1.1,4.4-2.3,5.7-5.1c1.5-3.3,2.6-6.7,3.6-9.6
	c2.8-8.3,4.1-10.7,6.8-10.3c1.7,0.3,2.3,0.9,2.7,2.8c0.4,2.5,0.1,6.3-0.3,11.1c-0.2,2.6-0.5,5.4-0.7,8.6c-0.2,3,0.7,5.6,2.5,7.6
	c2.3,2.4,6,3.7,11,3.7H100c2.2,0,3.7,1.7,3.7,4.1c0,2.4-1.9,3.8-3.7,3.8H77c-0.5-0.1-1-0.1-1.5-0.1H54.4c-5.3,0-9.6,4.3-9.6,9.6
	c0,3.5,1.9,6.6,4.8,8.3c-1,1.5-1.5,3.3-1.5,5.2c0,3.3,1.7,6.2,4.2,7.9c-1.1,1.6-1.7,3.5-1.7,5.5c0,1.3,0.3,2.6,0.8,3.8
	c-20.5-0.3-21.5-1.7-26-7.6c-1.2-1.7-2.4-3.9-3.3-6.8c-0.5-1.5-2.1-2.3-3.6-1.8c-1.5,0.5-2.3,2.1-1.8,3.6c0.4,1.2,0.8,2.2,1.3,3.3H0
	v5.8h21.1c2.8,3.8,5.2,6.5,11.3,7.9c5,1.1,12.5,1.4,27.3,1.4c0.1,0,0.2,0,0.3,0h15.4c5.3,0,9.6-4.3,9.6-9.6c0-2.6-1.1-5-2.8-6.7
	c1.7-1.7,2.8-4.1,2.8-6.7c0-2.6-1.1-5-2.8-6.7c1.7-1.7,2.8-4.1,2.8-6.7c0-1.3-0.3-2.6-0.7-3.7H100c5.2,0,9.5-4.3,9.5-9.6
	C109.5,316.1,105.4,311.9,100,311.9L100,311.9z M79.3,348.4c0,2.1-1.7,3.8-3.8,3.8H57.6c-2.1,0-3.8-1.7-3.8-3.8s1.7-3.8,3.8-3.8
	h17.8C77.6,344.6,79.3,346.3,79.3,348.4z M75.4,365.7H65.3c-1.9,0-3.7,0-5.3,0c-2-0.1-3.7-1.8-3.7-3.8c0-2.1,1.7-3.8,3.8-3.8h15.4
	c2.1,0,3.8,1.7,3.8,3.8S77.6,365.7,75.4,365.7z M79.3,335c0,2.1-1.7,3.8-3.8,3.8H54.3c-2.1,0-3.8-1.7-3.8-3.8s1.7-3.8,3.8-3.8h20.2
	c0.3,0.1,0.5,0.1,0.8,0.1h1C78.1,331.7,79.3,333.2,79.3,335L79.3,335z"/>
	<path d="M320,55.9h-21.1c-2.9-3.8-5.2-6.5-11.3-7.9c-5.6-1.2-14.3-1.4-32.9-1.4h-10.1c-5.3,0-9.6,4.3-9.6,9.6c0,2.6,1.1,5,2.8,6.7
	c-1.7,1.7-2.8,4.1-2.8,6.7s1.1,5,2.8,6.7c-1.7,1.7-2.8,4.1-2.8,6.7c0,1.3,0.3,2.6,0.7,3.7H220c-5.2,0-9.5,4.3-9.5,9.6
	c0,5.6,4.1,9.8,9.5,9.8h37.9c3.3,0,5.6,0.6,6.8,1.9c0.7,0.8,1,1.8,1,3.3c-0.2,3-0.4,5.9-0.6,8.4c-0.5,5.2-0.8,9.4-0.2,12.6
	c0.8,4.3,3.3,6.8,7.4,7.4c0.5,0.1,1.1,0.1,1.6,0.1c3,0,5.5-1.5,7.4-4.4c1.7-2.6,2.8-6,4.2-9.9c1-2.9,2-6,3.4-9.1
	c0.4-0.9,1.3-1.4,3-2.3c2.2-1.1,5.1-2.6,7.6-6.1c0.4-0.6,0.9-1.2,1.2-2h19.3v-5.8h-16.7c1.5-4.2,2.2-7.9,2.2-8.1
	c0.3-1.6-0.8-3.1-2.3-3.3c-1.6-0.3-3,0.8-3.3,2.3c0,0.1-1.5,8.5-5.1,13.4c-1.7,2.3-3.6,3.4-5.6,4.4c-2.1,1.1-4.4,2.3-5.7,5.1
	c-1.5,3.3-2.6,6.7-3.6,9.6c-2.8,8.3-4.1,10.7-6.8,10.3c-1.7-0.3-2.3-0.9-2.7-2.8c-0.4-2.5-0.1-6.3,0.3-11.1c0.2-2.6,0.5-5.4,0.6-8.6
	c0.2-3-0.7-5.6-2.5-7.6c-2.3-2.4-6-3.7-11-3.7H220c-2.2,0-3.7-1.7-3.7-4.1s1.9-3.8,3.7-3.8h23c0.5,0.1,1,0.1,1.5,0.1
	c1.2,0,2.2-0.7,2.7-1.8c0.2-0.4,0.3-0.8,0.3-1.2c0-1.6-1.3-2.9-2.9-2.9h-1c-1.7-0.4-2.9-1.9-2.9-3.7c0-2.1,1.7-3.8,3.8-3.8
	c1.6,0,2.9-1.3,2.9-2.9c0-1.6-1.3-2.9-2.9-2.9c-2.1,0-3.8-1.7-3.8-3.8c0-2.1,1.7-3.8,3.8-3.8c1.6,0,2.9-1.3,2.9-2.9
	c0-1.6-1.3-2.9-2.9-2.9c-2.1,0-3.8-1.7-3.8-3.8c0-2.1,1.7-3.8,3.8-3.8h10.1c34.3,0,34.7,0.6,40.1,7.7c1.2,1.7,2.4,3.9,3.3,6.8
	c0.5,1.5,2.1,2.3,3.6,1.8c1.5-0.5,2.3-2.1,1.8-3.6c-0.4-1.2-0.8-2.3-1.3-3.3h17.7L320,55.9L320,55.9z"/>
                    </g>
                </svg>',
        'frame_height_scale' => 1.2,
        'frame_scale' => 320,
        'frame_translate_x' => 0,
        'frame_translate_y' => -0.14,

        'qr_background_scale' => 1.55,
        'qr_background_x' => 5.65 ,
        'qr_background_y' => 5.2 ,
        'qr_background_type' => 'square',

        'qr_scale' => .6,
        'qr_translate_x' => 3,
        'qr_translate_y' => 2.8,

        'frame_text_x' => 50, // %
        'frame_text_y' => 92.5, // %
        'frame_text_size_scale' => 15,
        'frame_text_size_min_scale' => 50,
    ],

    'broken_ticket_bottom_text' => [
        'svg' => '<svg width="%1$s" height="%2$s" viewBox="0 0 %1$s %2$s" fill="%4$s"  xmlns="http://www.w3.org/2000/svg" xml:space="preserve">
                    <g transform="translate(%5$s, %6$s)  scale(%3$s)">
                        <path d="M317.3,21.3c0-11.3-6.4-17.7-17.6-17.7c-93.1,0-186.3,0-279.4,0C9.4,3.6,3,10,2.8,21
	c-0.1,8.3-0.1,16.6-0.1,24.9c0,113,0,226,0,339c0,3,0,5.9,1.9,8.5c3.9,5.6,7.5,11.4,11.3,17c3.6,5.3,6.7,5.4,10.3,0.2
	c3.5-5,6.9-10.1,10.5-15.4c3.6,5.4,6.8,10.5,10.2,15.4c3.6,5.2,6.6,5.2,10.3-0.1c3.5-5,6.8-10,10.4-15.3c3.7,5.4,6.9,10.5,10.3,15.4
	c3.4,4.9,6.5,5.1,10,0.1s6.8-10.1,10.5-15.6c3.7,5.6,6.9,10.7,10.3,15.6c3.4,4.9,6.6,4.9,10-0.1c2.9-4.2,5.8-8.5,8.5-12.9
	c1.5-2.4,2.3-2.1,3.7,0.1c2.5,3.9,5.2,7.7,7.8,11.6c4.6,6.8,7.3,6.8,11.7,0.1c2.5-3.7,5.1-7.4,7.4-11.2c1.5-2.5,2.4-3.2,4.2-0.2
	c2.6,4.4,5.6,8.6,8.6,12.8c3.4,4.7,6.4,4.7,9.7,0c3-4.4,6-8.8,8.8-13.3c1.3-2.1,2.1-2,3.4,0c2.9,4.5,5.8,8.9,8.9,13.2
	c3.5,4.9,6.6,4.8,10-0.2c2.7-4,5.6-7.9,7.9-12.1c2-3.6,3.1-2.6,4.8,0.2c2.5,4.1,5.3,8,8.1,12c3.5,4.9,6.6,4.9,10.1,0
	c3-4.4,5.9-8.8,8.8-13.3c1.2-1.9,1.9-1.8,3.1,0c3,4.6,6,9.2,9.2,13.6c3.3,4.6,6,4.6,9.3,0c3.1-4.3,6.1-8.7,8.9-13.2
	c1.4-2.2,2.2-2.5,3.7-0.1c2.8,4.5,5.8,8.9,8.9,13.2c3.3,4.6,6.1,4.7,9.3,0.1c4.2-6,8.1-12.1,12.2-18.2c1.1-1.7,1.6-3.7,1.4-5.7
	C317.3,265.2,317.3,143.3,317.3,21.3z M308.5,392.2c-3.2,4.6-6.2,9.4-9.6,14.6c-3.8-5.6-7.2-10.9-10.8-16.1c-3.1-4.5-6.2-4.5-9.3,0
	c-3.6,5.2-7,10.5-10.8,16.1c-3.7-5.5-7.2-10.9-10.8-16.1c-3.1-4.5-6.2-4.5-9.3,0c-3.6,5.2-7,10.5-10.8,16.1c-3.3-5-6.4-9.8-9.6-14.6
	c-4.4-6.6-7.2-6.6-11.7,0.1c-3.1,4.5-6.1,9.1-9.1,13.7c-0.2,0.2-0.4,0.4-0.7,0.6c-3.4-5.2-6.8-10.5-10.2-15.6c-3.3-4.9-6.6-4.9-10,0
	c-3.4,4.9-6.8,10.2-10.6,15.8c-3.5-5.4-6.9-10.6-10.3-15.7c-3.3-5-6.6-5-10-0.1c-3.6,5.2-7,10.4-10.6,15.8c-3.4-5.1-6.5-10-9.7-14.9
	c-4.1-6.1-7.1-6.2-11.1-0.2c-3.2,4.8-6.3,9.7-9.8,15c-3.5-5.3-6.8-10.2-10.1-15c-4.1-6-7-6-11.1,0.2c-3.2,4.8-6.3,9.7-9.7,14.9
	c-3.8-5.6-7.2-10.9-10.7-16c-3.2-4.6-6.5-4.5-9.7,0.1C59,396,55.8,401,52.1,406.8C48.4,401.3,45,396,41.5,391
	c-3.4-4.9-6.6-4.9-10,0.1s-6.7,10.2-10.3,15.7c-3.8-5.8-7.3-11.2-10.9-16.5c-1.1-1.5-1-3.1-1-4.8c0-121.2,0-242.3,0-363.4
	c0-9,2.9-11.8,12-11.8h277c9.5,0,12.3,2.8,12.3,12.6c0,120.8,0,241.6,0,362.5C310.8,387.7,310,390.2,308.5,392.2L308.5,392.2z"/>
                    </g>
                </svg>',
        'frame_height_scale' => 1.3,
        'frame_scale' => 320,
        'frame_translate_x' => 0,
        'frame_translate_y' => 0,

        'qr_background_scale' => 1.05,
        'qr_background_x' => 35 ,
        'qr_background_y' => 35 ,
        'qr_background_type' => 'square',

        'qr_scale' => .9,
        'qr_translate_x' => 17.5,
        'qr_translate_y' => 17.5,

        'frame_text_x' => 50, // %
        'frame_text_y' => 82.5, // %
        'frame_text_size_scale' => 15,
        'frame_text_size_min_scale' => 50,
    ],

    'smooth_ribbon_bottom_text' => [
        'svg' => '<svg width="%1$s" height="%2$s" viewBox="0 0 %1$s %2$s" fill="%4$s"  xmlns="http://www.w3.org/2000/svg" xml:space="preserve">
                    <g transform="translate(%5$s, %6$s)  scale(%3$s)">
                        <path d="M256.3,83.3c1.7,0,3.2,1.4,3.2,3.2v195.8h-199V86.4c0-1.7,1.4-3.2,3.2-3.2H256.3 M256.3,73.8H63.7c-7,0-12.6,5.6-12.6,12.6
                            v205.3h217.9V86.4C268.9,79.4,263.3,73.8,256.3,73.8L256.3,73.8z"/>
                            <polygon opacity="0.9" points="51.1,302 2.3,302 20.9,271 2.3,240 51.1,240 "/>
                            <path opacity="0.4" d="M51.1,285v-32.5c0,0-16.8,6.6-16.8,15.3C34.3,285,51.1,285,51.1,285L51.1,285z"/>
                            <polygon opacity="0.9" points="268.9,302 317.7,302 299.1,271 317.7,240 268.9,240 "/>
                            <path opacity="0.4" d="M268.9,285v-32.5c0,0,16.8,6.6,16.8,15.3C285.7,285,268.9,285,268.9,285L268.9,285z"/>
                            <g>
                                <path d="M34.3,267.9v-0.9C34.3,267.2,34.3,267.6,34.3,267.9z"/>
                                <path d="M34.3,266.9L34.3,266.9C34.3,266.9,34.3,266.9,34.3,266.9L34.3,266.9z"/>
                                <path d="M285.7,266.9v0.9C285.7,267.6,285.7,267.2,285.7,266.9z"/>
                                <path d="M285.7,307.1v-39.2c-0.5,16.8-31.6,14.4-48.7,14.4H82.9c-17.1,0-48.1,2.4-48.7-14.4v39.2c0,0.1,0,0.2,0,0.3v21
                                c0,8.7,7.1,15.8,15.8,15.8H270c8.7,0,15.8-7.1,15.8-15.8v-21C285.7,307.3,285.7,307.2,285.7,307.1L285.7,307.1z"/>
                            </g>
                    </g>
                </svg>',
        'frame_height_scale' => 1,
        'frame_scale' => 320,
        'frame_translate_x' => 0,
        'frame_translate_y' => -0.15,

        'qr_background_scale' => 1.6,
        'qr_background_x' => 5.4 ,
        'qr_background_y' => 9.4 ,
        'qr_background_type' => 'square',

        'qr_scale' => .585,
        'qr_translate_x' => 2.9,
        'qr_translate_y' => 4.7,

        'frame_text_x' => 50, // %
        'frame_text_y' => 82.5, // %
        'frame_text_size_scale' => 15,
        'frame_text_size_min_scale' => 50,
    ],
];
