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

/* For demo purposes only */
defined('ALTUMCODE') || die();
?>

<?php if(isset($data->demo_url)): ?>
    <script>
    'use strict';
     if(window.location !== window.parent.location){ window.top.location.href = <?= json_encode($data->demo_url) ?>; } </script>
<?php endif ?>

<style>
    .ac-wrapper {
        position: fixed;
        width: calc(100% - 1.5rem);
        z-index: 1001;
        bottom: 0;
        margin: .75rem;
        font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,"Noto Sans",sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol","Noto Color Emoji";
        min-height: 3rem;
        background: #161A38;
        padding: .5rem .75rem;
        display: flex;
        justify-content: space-between;
        flex-direction: row;
        align-items: center;
        font-size: .85rem;
        border-radius: .5rem;
        opacity: 0;
        animation: ac-fade-in-down .3s ease-in .6s forwards;
    }
    @media (min-width: 992px) {
        .ac-wrapper {
            border-radius: 5rem;
        }
    }
    .ac-altumcode-link {
        color: white;
        display: flex;
        align-items: center;
        align-self: center;
    }

    .ac-altumcode-link:hover {
        text-decoration: none;
        color: white;
    }

    .ac-altumcode-link:hover .ac-altumcode-image {
        transform: scale(1.15);
    }

    .ac-altumcode-link:active .ac-altumcode-image {
        transform: scale(.9);
    }

    .ac-altumcode-link-brand {
        font-weight: bold;
        background: linear-gradient(135deg, #3DEADE, #3082ee);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .ac-altumcode-image {
        width: 1rem;
        height:auto;
        margin-right: 1rem;
        transition: all .15s linear !important;
    }

    .ac-primary {
        padding: .25rem 1.25rem;
        background: #3f88fd;
        color: white;
        border-radius: 5rem;
        transition: all .15s linear !important;
        white-space: nowrap;
        font-weight: 500;
    }
    .ac-primary:hover {
        text-decoration: none;
        color: white;
        transform: scale(1.025);
    }
    .ac-primary:active {
        background: #295aa9;
        transform: scale(0.95);
    }
    .ac-secondary {
        padding: .25rem 0;
        color: hsl(255, 85%, 95%);
        border-radius: .25rem;
        transition: all .3s linear !important;
        white-space: nowrap;
        margin-right: 1.25rem;
    }
    .ac-secondary:hover {
        text-decoration: none;
        color: hsl(255, 85%, 85%);
    }
    .ac-cta-wrapper {
        display: flex;
        justify-content: space-around;
    }
    @media (min-width: 992px) {
        .ac-cta-wrapper {
            justify-content: initial;
        }
    }
    @keyframes ac-fade-in-down {
        from {
            opacity: 0;
            transform: translate3d(0, 100%, 0);
        }

        to {
            opacity: 1;
            transform: translate3d(0, 0, 0);
        }
    }
    /* Gradient background */
    .ac-bg-gradient {
        animation: ac-bg-gradient-animation 3s ease infinite alternate;
        background: linear-gradient(60deg, #e88d2a, #e3624c, #e33e72, #944e94, #46639e, #008b94, #009c86, #5ea773);
        background-size: 300% 300%;
    }

    @keyframes ac-bg-gradient-animation {
        0% {
            background-position: 0 50%;
        }
        50% {
            background-position: 100% 50%;
        }
        100% {
            background-position: 0 50%;
        }
    }
</style>
<div class="ac-wrapper">
    <a href="https://altumcode.com/" target="_blank" class="ac-altumcode-link">
        <img src="https://altumcode.com/themes/altum/assets/images/altumcode.svg" alt="AltumCode logo" class="ac-altumcode-image" />
        <span><?= $data->product_name . ' by <span class="ac-altumcode-link-brand">AltumCode</span>' ?></span>
    </a>

    <div class="ac-cta-wrapper">
        <a href="https://altumcode.com/contact" target="_blank" class="ac-secondary"><span class="d-none d-lg-inline">Any questions? ✉️</span><span class="d-lg-none">Support</span></a>
        <a href="<?= $data->product_buy_url ?>" class="ac-primary ac-bg-gradient" target="_blank"><span class="d-none d-lg-inline"><?= 'Buy ' . $data->product_name ?> 🎁</span><span class="d-lg-none">Buy 🎁</span></a>
    </div>
</div>
