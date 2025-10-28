<?php defined('ALTUMCODE') || die() ?>
<!DOCTYPE html>
<html lang="<?= \Altum\Language::$code ?>" dir="<?= l('direction') ?>">
    <head>
        <title><?= \Altum\Title::get() ?></title>
        <base href="<?= SITE_URL; ?>">
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

        <?php if(\Altum\Plugin::is_active('pwa') && settings()->pwa->is_enabled): ?>
            <meta name="theme-color" content="<?= settings()->pwa->theme_color ?>"/>
            <link rel="manifest" href="<?= SITE_URL . UPLOADS_URL_PATH . \Altum\Uploads::get_path('pwa') . 'manifest.json' ?>" />
        <?php endif ?>

        <?php if(\Altum\Meta::$description): ?>
            <meta name="description" content="<?= \Altum\Meta::$description ?>" />
        <?php endif ?>
        <?php if(\Altum\Meta::$keywords): ?>
            <meta name="keywords" content="<?= \Altum\Meta::$keywords ?>" />
        <?php endif ?>

        <?php \Altum\Meta::output() ?>

        <?php if(\Altum\Meta::$canonical): ?>
            <link rel="canonical" href="<?= \Altum\Meta::$canonical ?>" />
        <?php endif ?>

        <?php if(\Altum\Meta::$robots): ?>
            <meta name="robots" content="<?= \Altum\Meta::$robots ?>">
        <?php endif ?>

        <?php if(\Altum\Meta::$link_alternate): ?>
            <link rel="alternate" href="<?= SITE_URL . \Altum\Router::$original_request ?>" hreflang="x-default" />
            <?php if(count(\Altum\Language::$active_languages) > 1): ?>
                <?php foreach(\Altum\Language::$active_languages as $language_name => $language_code): ?>
                    <?php if(settings()->main->default_language != $language_name): ?>
                        <link rel="alternate" href="<?= SITE_URL . $language_code . '/' . \Altum\Router::$original_request ?>" hreflang="<?= $language_code ?>" />
                    <?php endif ?>
                <?php endforeach ?>
            <?php endif ?>
        <?php endif ?>

        <?php if(!empty(settings()->main->favicon)): ?>
            <link href="<?= settings()->main->favicon_full_url ?>" rel="icon" />
        <?php endif ?>

        <link href="<?= ASSETS_FULL_URL . 'css/' . \Altum\ThemeStyle::get_file() . '?v=' . PRODUCT_CODE ?>" id="css_theme_style" rel="stylesheet" media="screen,print">
        <?php foreach(['custom.css', 'libraries/select2.css'] as $file): ?>
            <link href="<?= ASSETS_FULL_URL . 'css/' . $file . '?v=' . PRODUCT_CODE ?>" rel="stylesheet" media="screen,print">
        <?php endforeach ?>

        <?= \Altum\Event::get_content('head') ?>

        <?php if(is_logged_in() && !user()->plan_settings->export->pdf): ?>
            <style>@media print { body { display: none; } }</style>
        <?php endif ?>

        <?php if(!empty(settings()->custom->head_js)): ?>
            <?= get_settings_custom_head_js() ?>
        <?php endif ?>

        <?php if(!empty(settings()->custom->head_css)): ?>
            <style><?= settings()->custom->head_css ?></style>
        <?php endif ?>
    </head>

    <body class="<?= l('direction') == 'rtl' ? 'rtl' : null ?> app <?= \Altum\ThemeStyle::get() == 'dark' ? 'cc--darkmode' : null ?>" data-theme-style="<?= \Altum\ThemeStyle::get() ?>">
        <?php if(!empty(settings()->custom->body_content)): ?>
            <?= settings()->custom->body_content ?>
        <?php endif ?>

        <?php //ALTUMCODE:DEMO if(DEMO) echo include_view(THEME_PATH . 'views/partials/ac_banner.php', ['demo_url' => 'https://66qrcode.com/demo/', 'product_name' => PRODUCT_NAME, 'product_url' => PRODUCT_URL, 'product_buy_url' => PRODUCT_BUY_URL]) ?>
        <?php if(settings()->main->admin_spotlight_is_enabled || settings()->main->user_spotlight_is_enabled) require THEME_PATH . 'views/partials/spotlight.php' ?>
        <?php if(\Altum\Plugin::is_active('pwa') && settings()->pwa->is_enabled && settings()->pwa->display_install_bar) require \Altum\Plugin::get('pwa')->path . 'views/partials/pwa.php' ?>

        <div id="app_overlay" class="app-overlay" style="display: none"></div>

        <div class="app-container">
            <?= $this->views['app_sidebar'] ?>

            <section class="app-content">
                <?php require THEME_PATH . 'views/partials/js_welcome.php' ?>
                <?php require THEME_PATH . 'views/partials/admin_impersonate_user.php' ?>
                <?php require THEME_PATH . 'views/partials/team_delegate_access.php' ?>
                <?php require THEME_PATH . 'views/partials/announcements.php' ?>
                <?php require THEME_PATH . 'views/partials/cookie_consent.php' ?>
                <?php require THEME_PATH . 'views/partials/ad_blocker_detector.php' ?>
                <?php if(\Altum\Plugin::is_active('push-notifications') && settings()->push_notifications->is_enabled) require \Altum\Plugin::get('push-notifications')->path . 'views/partials/push_notifications_js.php' ?>

                <?= $this->views['app_menu'] ?>

                <div class="py-4 p-lg-5">
                    <?php require THEME_PATH . 'views/partials/ads_header.php' ?>

                    <main class="altum-animate altum-animate-fill-none altum-animate-fade-in">
                        <?= $this->views['content'] ?>
                    </main>

                    <?php require THEME_PATH . 'views/partials/ads_footer.php' ?>
                </div>

                <div class="px-lg-5">
                    <div class="container d-print-none">
                        <footer class="app-footer">
                            <?= $this->views['footer'] ?>
                        </footer>
                    </div>
                </div>
            </section>
        </div>

        <?= \Altum\Event::get_content('modals') ?>

        <?php require THEME_PATH . 'views/partials/js_global_variables.php' ?>

        <?php foreach(['libraries/jquery.min.js', 'libraries/popper.min.js', 'libraries/bootstrap.min.js', 'custom.js', 'libraries/select2.min.js'] as $file): ?>
            <script src="<?= ASSETS_FULL_URL ?>js/<?= $file ?>?v=<?= PRODUCT_CODE ?>"></script>
        <?php endforeach ?>

        <?php foreach(['libraries/fontawesome.min.js', 'libraries/fontawesome-solid.min.js', 'libraries/fontawesome-brands.modified.js'] as $file): ?>
            <script src="<?= ASSETS_FULL_URL ?>js/<?= $file ?>?v=<?= PRODUCT_CODE ?>" defer></script>
        <?php endforeach ?>

        <?= \Altum\Event::get_content('javascript') ?>

        <script>
    'use strict';
    
            let toggle_app_sidebar = () => {
                /* Open sidebar menu */
                let body = document.querySelector('body');
                body.classList.toggle('app-sidebar-opened');

                /* Toggle overlay */
                let app_overlay = document.querySelector('#app_overlay');
                app_overlay.style.display == 'none' ? app_overlay.style.display = 'block' : app_overlay.style.display = 'none';

                /* Change toggle button content */
                let button = document.querySelector('#app_menu_toggler');

                if(body.classList.contains('app-sidebar-opened')) {
                    button.innerHTML = `<i class="fas fa-fw fa-times"></i>`;
                } else {
                    button.innerHTML = `<i class="fas fa-fw fa-bars"></i>`;
                }
            };

            /* Toggler for the sidebar */
            document.querySelector('#app_menu_toggler').addEventListener('click', event => {
                event.preventDefault();

                toggle_app_sidebar();

                let app_sidebar_is_opened = document.querySelector('body').classList.contains('app-sidebar-opened');

                if(app_sidebar_is_opened) {
                    document.querySelector('#app_overlay').removeEventListener('click', toggle_app_sidebar);
                    document.querySelector('#app_overlay').addEventListener('click', toggle_app_sidebar);
                } else {
                    document.querySelector('#app_overlay').removeEventListener('click', toggle_app_sidebar);
                }
            });

            /* Custom select implementation */
            $('select:not([multiple="multiple"]):not([class="input-group-text"]):not([class="custom-select custom-select-sm"]):not([class^="ql"]):not([data-is-not-custom-select])').each(function() {
                let $select = $(this);
                $select.select2({
                    placeholder: <?= json_encode(l('global.no_data')) ?>,
                    dir: <?= json_encode(l('direction')) ?>,
                    minimumResultsForSearch: 5,
                });

                /* Make sure to trigger the select when the label is clicked as well */
                let selectId = $select.attr('id');
                if(selectId) {
                    $('label[for="' + selectId + '"]').on('click', function(event) {
                        event.preventDefault();
                        $select.select2('open');
                    });
                }
            });
        </script>
    </body>
</html>
