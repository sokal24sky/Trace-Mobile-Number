<?php defined('ALTUMCODE') || die(); ?>

<?php if(is_logged_in() && isset($_GET['welcome']) && user()->total_logins == 1): ?>
    <?php ob_start() ?>
    <script src="<?= ASSETS_FULL_URL ?>js/libraries/tsparticles.confetti.bundle.min.js?v=<?= PRODUCT_CODE ?>"></script>

    <script>
        'use strict';

        confetti({
            particleCount: 100,
            spread: 70,
            origin: { y: 0.6 },
        });
    </script>
    <?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>

    <?php if(!empty(settings()->custom->welcome_js)): ?>
        <?= get_settings_custom_head_js('welcome_js') ?>
    <?php endif ?>
<?php endif ?>
