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
    'square' => [
        'svg' => '<svg width="25" height="25" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                     <rect x="2" y="2" width="20" height="20" fill="none" stroke="%s" stroke-width="4"/>
                 </svg>',
    ],

    'circle' => [
        'svg' => '<svg width="25" height="25" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                     <circle cx="12" cy="12" r="10" fill="none" stroke="%s" stroke-width="4"/>
                 </svg>',
    ],

    'rounded' => [
        'svg' => '<svg width="25" height="25" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                     <rect x="2" y="2" width="20" height="20" rx="5" ry="5" fill="none" stroke="%s" stroke-width="4"/>
                 </svg>',
    ],

    'flower' => [
        'svg' => '<svg width="25" height="25" viewBox="0 0 14 14" fill="%s" xmlns="http://www.w3.org/2000/svg"><path d="M14,14V4.4C14,2,12,0,9.6,0H4.4C2,0,0,2,0,4.4v5.1C0,12,2,14,4.4,14H14z M4.8,2h4.4C10.7,2,12,3.3,12,4.8V12H4.8 C3.3,12,2,10.7,2,9.2V4.8C2,3.3,3.3,2,4.8,2z"></path></svg>',
    ],

    'leaf' => [
        'svg' => '<svg width="25" height="25" viewBox="0 0 14 14" fill="%s" xmlns="http://www.w3.org/2000/svg">
                    <g transform="rotate(90, 7, 7)">
                        <path d="M0,0l0,7c0,3.9,3.1,7,7,7h7V7c0-3.9-3.1-7-7-7H0z M12,12H7c-2.8,0-5-2.2-5-5V2h5c2.8,0,5,2.2,5,5V12z"></path>
                    </g>
                </svg>',

    ],

    'ninja' => [
        'svg' => '<svg width="25" height="25" viewBox="0 0 6 6" xmlns="http://www.w3.org/2000/svg">
                     <path d="M3.5,6C0.7,4.7,1.7,3,0,3.5C1.3,0.7,3,1.7,2.5,0C5.3,1.3,4.3,3,6,2.5C4.7,5.3,3,4.3,3.5,6z"
                           fill="none" stroke="%s" stroke-width="0.9"/>
                 </svg>',
    ],

    'hexagon' => [
        'svg' => '<svg width="25" height="25" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                     <polygon points="12,2 20,7 20,17 12,22 4,17 4,7" fill="none" stroke="%s" stroke-width="4"/>
                 </svg>',
    ],

    'octagon' => [
        'svg' => '<svg width="25" height="25" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                     <polygon points="7,2 17,2 22,7 22,17 17,22 7,22 2,17 2,7" fill="none" stroke="%s" stroke-width="4"/>
                 </svg>',
    ],
];
