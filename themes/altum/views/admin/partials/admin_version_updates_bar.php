<?php defined('ALTUMCODE') || die(); ?>

<?php if(ALTUMCODE == 66): ?>
<?php //ALTUMCODE:DEMO if(!DEMO) { ?>
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

$product_info = \Altum\Cache::cache_function_result('admin_product_info', null, function() {
    try {
        \Unirest\Request::timeout(3);
        $response = \Unirest\Request::get('https://66qrcode.com/info.php');

        if($response->code == 200) {
            return $response->body;
        } else {
            return null;
        }
    } catch (\Exception $exception) {
        return null;
    }
}, 86400 * 2);
?>

<?php if(
        $product_info
        && isset($product_info->latest_release_version)
        && !isset($_COOKIE['dismiss_version_updates'])
        && $product_info->latest_release_version_code != PRODUCT_CODE
): ?>
<div class="alert alert-success mb-4">
        <div class="mb-3">
            <i class="fas fa-fw fa-check-circle text-success mr-1"></i>
            <strong>Version <?= $product_info->latest_release_version ?> is now out! You are currently on version <?= PRODUCT_VERSION ?>.</strong>
        </div>

        <div class="mb-3">
            <span>You can now download it and update as per the <a href="<?= PRODUCT_DOCUMENTATION_URL ?>" target="_blank">documentation</a> of the product, or <a href="https://altumcode.com/contact" target="_blank">hire me (altumcode)</a> to update it for you.</span>
        </div>

        <div>
            <button type="button" class="btn btn-sm btn-light" data-dismiss="alert" data-dismiss-version-updates>
                <i class="fas fa-fw fa-sm fa-times mr-1"></i> Dismiss notification
            </button>

            <a href="https://altumco.de/<?= PRODUCT_KEY ?>-changelog" target="_blank" class="btn btn-sm btn-dark ml-3">
                <i class="fas fa-fw fa-sm fa-scroll mr-1"></i> View changelog
            </a>

            <a href="https://altumco.de/downloads" target="_blank" class="btn btn-sm btn-primary font-weight-bold ml-3">
                <i class="fas fa-fw fa-sm fa-download mr-1"></i> Download updates
            </a>
        </div>

        <?php ob_start() ?>
        <script>
            'use strict';
            document.querySelector('[data-dismiss-version-updates]').addEventListener('click', event => {
                set_cookie('dismiss_version_updates', 1, 7, <?= json_encode(COOKIE_PATH) ?>);
            });
        </script>
        <?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
    </div>
<?php endif ?>
<?php //ALTUMCODE:DEMO } ?>
<?php endif ?>
