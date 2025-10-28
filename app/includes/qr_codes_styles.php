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
                     <rect x="2" y="2" width="20" height="20" fill="%s" />
                 </svg>',
    ],

    'dot' => [
        'svg' => '<svg width="25" height="25" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                 <circle cx="12" cy="12" r="10" fill="%s" />
             </svg>',
    ],

    'round' => [
        'svg' => '<svg width="25" height="25" viewBox="0 0 25 25" xmlns="http://www.w3.org/2000/svg">
                 <rect x="0" y="6" width="8" height="8" rx="2" ry="2" fill="%1$s"></rect>
                 <rect x="15" y="11" width="10" height="10" rx="2" ry="2" fill="%1$s"></rect>
                 <rect x="5" y="10" width="20" height="4" rx="1.5" ry="1.5" fill="%1$s"></rect>
             </svg>',
    ],

    'rounded' => [
        'svg' => '<svg width="25" height="25" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                     <rect x="2" y="2" width="20" height="20" rx="5" ry="5" fill="%s" />
                 </svg>',
    ],

    'elastic_square' => [
        'svg' => '<svg width="25" height="25" viewBox="0 0 24 24" fill="%s" xmlns="http://www.w3.org/2000/svg"><path d="M24,24C16.4,21.6,7.6,21.6,0,24C2.4,16.4,2.4,7.6,0,0C7.6,2.4,16.4,2.4,24,0C21.6,7.6,21.6,16.4,24,24Z"/></svg>',
    ],

    'heart' => [
        'svg' => '<svg width="25" height="25" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                     <path fill="%s" d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5
                         2 5.42 4.42 3 7.5 3c1.74 0 3.41 0.81 4.5 2.09
                         C13.09 3.81 14.76 3 16.5 3
                         19.58 3 22 5.42 22 8.5
                         c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                 </svg>',
    ],

    'diamond' => [
        'svg' => '<svg width="25" height="25" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                     <polygon fill="%s" points="12,2 22,12 12,22 2,12"/>
                 </svg>',
    ],

    'star' => [
        'svg' => '<svg width="25" height="25" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                     <path fill="%s" d="M12 17.27L18.18 21
                         16.54 13.97 22 9.24 14.81 8.63
                         12 2 9.19 8.63 2 9.24
                         7.46 13.97 5.82 21z"/>
                 </svg>',
    ],

    'triangle' => [
        'svg' => '<svg width="25" height="25" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                 <polygon fill="%s" points="12,4 20,20 4,20"/>
              </svg>',
    ],

    'hexagon' => [
        'svg' => '<svg width="25" height="25" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                 <polygon fill="%s" points="12,2 20,7 20,17 12,22 4,17 4,7" />
              </svg>',
    ],

    'spaced_square' => [
        'svg' => '<svg width="25" height="25" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                     <rect x="2" y="2" width="8" height="8" fill="%1$s" />
                     <rect x="14" y="2" width="8" height="8" fill="%1$s" />
                     <rect x="2" y="14" width="8" height="8" fill="%1$s" />
                     <rect x="14" y="14" width="8" height="8" fill="%1$s" />
                 </svg>',
    ],

    'octagon' => [
        'svg' => '<svg width="25" height="25" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                     <polygon points="7,2 17,2 22,7 22,17 17,22 7,22 2,17 2,7" fill="%s" />
                 </svg>',
    ],

    'cross_x' => [
        'svg' => '<svg width="25" height="25" viewBox="0 0 24 24" fill="%s" xmlns="http://www.w3.org/2000/svg"><polygon points="12,20.4 15.6,24 24,24 24,15.6 20.4,12 24,8.4 24,0 15.6,0 12,3.6 8.4,0 0,0 0,8.4 3.6,12 0,15.6 0,24 8.4,24"/></svg>',
    ],

    'curvy_x' => [
        'svg' => '<svg width="25" height="25" viewBox="0 0 6 6" fill="%s" xmlns="http://www.w3.org/2000/svg"><path d="M3,5.1l0.4,0.4C3.7,5.8,4.1,6,4.5,6h0C5.3,6,6,5.3,6,4.5v0c0-0.4-0.2-0.8-0.4-1.1L5.1,3l0.4-0.4 C5.8,2.3,6,1.9,6,1.5v0C6,0.7,5.3,0,4.5,0h0C4.1,0,3.7,0.2,3.4,0.4L3,0.9L2.6,0.4C2.3,0.2,1.9,0,1.5,0h0C0.7,0,0,0.7,0,1.5v0 c0,0.4,0.2,0.8,0.4,1.1L0.9,3L0.4,3.4C0.2,3.7,0,4.1,0,4.5v0C0,5.3,0.7,6,1.5,6h0c0.4,0,0.8-0.2,1.1-0.4L3,5.1z"></path></svg>',
    ],

    'rounded_cross' => [
        'svg' => '<svg width="25" height="25" viewBox="0 0 6 6" fill="%s" xmlns="http://www.w3.org/2000/svg"><path d="M4.5,1.5L4.5,1.5L4.5,1.5C4.5,0.7,3.8,0,3,0h0C2.2,0,1.5,0.7,1.5,1.5v0h0C0.7,1.5,0,2.2,0,3v0 c0,0.8,0.7,1.5,1.5,1.5h0v0C1.5,5.3,2.2,6,3,6h0c0.8,0,1.5-0.7,1.5-1.5v0h0C5.3,4.5,6,3.8,6,3v0C6,2.2,5.3,1.5,4.5,1.5z"></path></svg>'
    ],

    'ninja' => [
        'svg' => '<svg width="25" height="25" viewBox="0 0 6 6" fill="%s" xmlns="http://www.w3.org/2000/svg"><path d="M3.5,6C0.7,4.7,1.7,3,0,3.5C1.3,0.7,3,1.7,2.5,0C5.3,1.3,4.3,3,6,2.5C4.7,5.3,3,4.3,3.5,6z"></path></svg>',
    ],

    'sun' => [
        'svg' => '<svg width="25" height="25" viewBox="0 0 6 6" fill="%s" xmlns="http://www.w3.org/2000/svg"><polygon points="3,0 3.4,0.7 4,0.2 4.1,0.9 4.9,0.7 4.8,1.5 5.6,1.5 5.2,2.2 5.9,2.5 5.3,3 5.9,3.5 5.2,3.8 5.6,4.5 4.8,4.5 4.9,5.3 4.1,5.1 4,5.8 3.4,5.3 3,6 2.5,5.3 1.9,5.8 1.8,5.1 1,5.3 1.1,4.5 0.4,4.5 0.7,3.8 0,3.5 0.6,3 0,2.5 0.7,2.2 0.4,1.5 1.1,1.5 1,0.7 1.8,0.9 1.9,0.2 2.5,0.7"></polygon></svg>',
    ],

    'shine' => [
        'svg' => '<svg width="25" height="25" viewBox="0 0 6 6" fill="%s" xmlns="http://www.w3.org/2000/svg"><path d="M3-1L3-1c0,2.2-1.8,4-4,4h0h0c2.2,0,4,1.8,4,4v0v0c0-2.2,1.8-4,4-4h0h0C4.8,3,3,1.2,3-1L3-1z"></path></svg>',
    ],

    'bold_plus' => [
        'svg' => '<svg width="25" height="25" viewBox="0 0 6 6" fill="%s" xmlns="http://www.w3.org/2000/svg"><polygon points="6,1.5 4.5,1.5 4.5,0 1.5,0 1.5,1.5 0,1.5 0,4.5 1.5,4.5 1.5,6 4.5,6 4.5,4.5 6,4.5"></polygon></svg>',
    ],

    'teardrop' => [
        'svg' => '<svg width="25" height="25" viewBox="0 0 6 6" fill="%s" xmlns="http://www.w3.org/2000/svg"><path d="M3,5.5 C4.2,5.5 5.2,4.5 5.2,3.3 C5.2,1.8 3,0.5 3,0.5 C3,0.5 0.8,1.8 0.8,3.3 C0.8,4.5 1.8,5.5 3,5.5 Z" /></svg>',
    ],

    'corner_cut' => [
        'svg' => '<svg width="25" height="25" viewBox="0 0 6 6" fill="%s" xmlns="http://www.w3.org/2000/svg"><path d="M1.5,0 L6,0 L6,4.5 L4.5,6 L0,6 L0,1.5 Z" /></svg>',
    ],

    'randomized_square' => [
        'svg' => '<svg width="25" height="25" viewBox="0 0 6 6" fill="%s" xmlns="http://www.w3.org/2000/svg"><path d="M1.2,0.5 L4.9,0.3 L5.3,4.8 L0.7,5.2 Z" /></svg>',
    ],

    'bold_x' => [
        'svg' => '<svg width="25" height="25" viewBox="0 0 6 6" fill="%s" xmlns="http://www.w3.org/2000/svg">
                    <g transform="rotate(45, 3, 3)">
                        <polygon points="6,1.5 4.5,1.5 4.5,0 1.5,0 1.5,1.5 0,1.5 0,4.5 1.5,4.5 1.5,6 4.5,6 4.5,4.5 6,4.5"></polygon>
                    </g>
                </svg>',
    ],
];
