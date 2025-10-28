<?php defined('ALTUMCODE') || die() ?>
<!DOCTYPE html>
<html lang="<?= \Altum\Language::$code ?>" dir="<?= l('direction') ?>" class="h-100">
    <head>
        <title><?= \Altum\Title::get() ?></title>
        <base href="<?= SITE_URL; ?>">
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

        <?php if(\Altum\Plugin::is_active('pwa') && settings()->pwa->is_enabled): ?>
            <meta name="theme-color" content="<?= settings()->pwa->theme_color ?>"/>
            <link rel="manifest" href="<?= SITE_URL . UPLOADS_URL_PATH . \Altum\Uploads::get_path('pwa') . 'manifest.json' ?>" />
        <?php endif ?>

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
        <?php foreach(['custom.css'] as $file): ?>
            <link href="<?= ASSETS_FULL_URL . 'css/' . $file . '?v=' . PRODUCT_CODE ?>" rel="stylesheet" media="screen">
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

    <body class="<?= l('direction') == 'rtl' ? 'rtl' : null ?> bg-gray-50 <?= \Altum\ThemeStyle::get() == 'dark' ? 'cc--darkmode' : null ?>" data-theme-style="<?= \Altum\ThemeStyle::get() ?>">
    <?php if(!empty(settings()->custom->body_content)): ?>
            <?= settings()->custom->body_content ?>
        <?php endif ?>

        <?php //ALTUMCODE:DEMO if(DEMO) echo include_view(THEME_PATH . 'views/partials/ac_banner.php', ['demo_url' => 'https://66qrcode.com/demo/', 'product_name' => PRODUCT_NAME, 'product_url' => PRODUCT_URL, 'product_buy_url' => PRODUCT_BUY_URL]) ?>

        <?php require THEME_PATH . 'views/partials/announcements.php' ?>
        <?php require THEME_PATH . 'views/partials/cookie_consent.php' ?>
        <?php if(settings()->main->admin_spotlight_is_enabled || settings()->main->user_spotlight_is_enabled) require THEME_PATH . 'views/partials/spotlight.php' ?>

        <main class="altum-animate altum-animate-fill-none altum-animate-fade-in py-6">
            <div class="container">
                <div class="d-flex flex-column align-items-center">
                    <div class="col-xs-12 col-md-10 col-lg-7 col-xl-6">

                        <div class="mb-5 text-center">
                            <a href="<?= url() ?>" class="text-decoration-none text-dark">
                                <?php if(settings()->main->{'logo_' . \Altum\ThemeStyle::get()} != ''): ?>
                                    <img src="<?= settings()->main->{'logo_' . \Altum\ThemeStyle::get() . '_full_url'} ?>" class="img-fluid navbar-logo" alt="<?= l('global.accessibility.logo_alt') ?>" />
                                <?php else: ?>
                                    <span class="h3"><?= settings()->main->title ?></span>
                                <?php endif ?>
                            </a>
                        </div>

                        <div class="card rounded-2x">
                            <div class="card-body p-5">
                                <?= $this->views['content'] ?>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </main>

        <?= \Altum\Event::get_content('modals') ?>

        <?php require THEME_PATH . 'views/partials/js_global_variables.php' ?>

        <?php foreach(['libraries/jquery.slim.min.js', 'libraries/popper.min.js', 'libraries/bootstrap.min.js', 'custom.js', 'libraries/fontawesome.min.js', 'libraries/fontawesome-solid.min.js', 'libraries/fontawesome-brands.modified.js'] as $file): ?>
            <script src="<?= ASSETS_FULL_URL ?>js/<?= $file ?>?v=<?= PRODUCT_CODE ?>"></script>
        <?php endforeach ?>

        <?= \Altum\Event::get_content('javascript') ?>
    </body>
</html>
