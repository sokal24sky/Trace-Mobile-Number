<?php defined('ALTUMCODE') || die() ?>

<?= \Altum\Alerts::output_alerts() ?>

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

//ALTUMCODE:DEMO if(DEMO) {
//ALTUMCODE:DEMO echo '<div class="card mb-4">';
//ALTUMCODE:DEMO echo '<div class="card-body">';
//ALTUMCODE:DEMO echo '<div class="h6">Demo</div>';
//ALTUMCODE:DEMO echo '<div><small class="text-muted">📱 Some features are disabled as this is a demo version.</small></div>';
//ALTUMCODE:DEMO echo '<div><small class="text-muted">🛠️ You can login as the admin with the prefilled credentials below.</small></div>';
//ALTUMCODE:DEMO echo '<div><small class="text-muted">👨‍💻 You can also register your own account to test it as a normal user</small></div>';
//ALTUMCODE:DEMO echo '</div>';
//ALTUMCODE:DEMO echo '</div>';
//ALTUMCODE:DEMO }
?>

<h1 class="h5"><?= sprintf(l('login.header'), settings()->main->title) ?></h1>

<form action="" method="post" class="mt-4" role="form">
    <?php if(isset($_SESSION['twofa_required']) && $data->user && $data->user->twofa_secret && $data->user->status == 1): ?>
        <input id="email" type="hidden" name="email" value="<?= $data->user ? $data->values['email'] : null ?>" required="required" />
        <input id="password" type="hidden" name="password" value="<?= $data->user ? $data->values['password'] : null ?>" required="required" />
        <input id="rememberme" type="hidden" name="rememberme" value="<?= $data->values['rememberme'] ? '1' : null ?>">

        <div class="form-group">
            <label for="twofa_token"><?= l('login.twofa_token') ?></label>
            <input id="twofa_token" type="text" name="twofa_token" class="form-control <?= \Altum\Alerts::has_field_errors('twofa_token') ? 'is-invalid' : null ?>" required="required" autocomplete="off" autofocus="autofocus" placeholder="123 456" maxlength="6" />
            <?= \Altum\Alerts::output_field_error('twofa_token') ?>
        </div>

        <div class="form-group mt-3">
            <button type="submit" name="submit" class="btn btn-primary btn-block my-1"><?= l('login.verify') ?></button>
        </div>
    <?php else: ?>

        <div class="form-group">
            <label for="email"><?= l('global.email') ?></label>
            <input id="email" type="text" name="email" class="form-control <?= \Altum\Alerts::has_field_errors('email') ? 'is-invalid' : null ?>" value="<?= $data->values['email'] ?>" required="required" autofocus="autofocus" />
            <?= \Altum\Alerts::output_field_error('email') ?>
        </div>

        <div class="form-group" data-password-toggle-view data-password-toggle-view-show="<?= l('global.show') ?>" data-password-toggle-view-hide="<?= l('global.hide') ?>">
            <label for="password"><?= l('global.password') ?></label>
            <input id="password" type="password" name="password" class="form-control <?= \Altum\Alerts::has_field_errors('password') ? 'is-invalid' : null ?>" value="<?= $data->user ? $data->values['password'] : null ?>" required="required" />
            <?= \Altum\Alerts::output_field_error('password') ?>
        </div>

        <?php if(settings()->captcha->login_is_enabled): ?>
            <div class="form-group">
                <?php $data->captcha->display() ?>
            </div>
        <?php endif ?>

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
            <div class="custom-control custom-checkbox" data-toggle="tooltip" title="<?= sprintf(l('login.remember_me_help'), settings()->users->login_rememberme_cookie_days ?? 30) ?>" data-tooltip-hide-on-click>
                <input type="checkbox" name="rememberme" class="custom-control-input" id="rememberme" <?= $data->values['rememberme'] ? 'checked="checked"' : null ?>>
                <label class="custom-control-label" for="rememberme"><small class="text-muted"><?= l('login.remember_me') ?></small></label>
            </div>

            <small class="text-muted">
                <a href="<?= url('lost-password' . $data->redirect_append) ?>" class="text-muted"><?= l('login.lost_password') ?></a>
                <?php if(settings()->users->email_confirmation): ?>
                    / <a href="<?= url('resend-activation' . $data->redirect_append) ?>" class="text-muted" role="button"><?= l('login.resend_activation') ?></a>
                <?php endif ?>
            </small>
        </div>

        <div class="form-group mt-4">
            <button type="submit" name="submit" class="btn btn-primary btn-block my-1" <?= isset($_COOKIE['login_lockout']) ? 'disabled="disabled"' : null ?>><?= l('login.login') ?></button>
        </div>
    <?php endif ?>

    <?php if(settings()->facebook->is_enabled || settings()->google->is_enabled || settings()->twitter->is_enabled || settings()->discord->is_enabled || settings()->linkedin->is_enabled || settings()->microsoft->is_enabled): ?>
        <hr class="border-gray-100 my-3" />

        <div>
            <?php if(settings()->facebook->is_enabled): ?>
                <div class="mt-2">
                    <a href="<?= url('login/facebook-initiate') ?>" class="btn btn-light btn-block">
                        <img src="<?= ASSETS_FULL_URL . 'images/facebook.svg' ?>" class="mr-1" />
                        <?= l('login.facebook') ?>
                    </a>
                </div>
            <?php endif ?>
            <?php if(settings()->google->is_enabled): ?>
                <div class="mt-2">
                    <a href="<?= url('login/google-initiate') ?>" class="btn btn-light btn-block">
                        <img src="<?= ASSETS_FULL_URL . 'images/google.svg' ?>" class="mr-1" />
                        <?= l('login.google') ?>
                    </a>
                </div>
            <?php endif ?>
            <?php if(settings()->twitter->is_enabled): ?>
                <div class="mt-2">
                    <a href="<?= url('login/twitter-initiate') ?>" class="btn btn-light btn-block">
                        <img src="<?= ASSETS_FULL_URL . 'images/x.svg' ?>" class="mr-1" />
                        <?= l('login.twitter') ?>
                    </a>
                </div>
            <?php endif ?>
            <?php if(settings()->discord->is_enabled): ?>
                <div class="mt-2">
                    <a href="<?= url('login/discord-initiate') ?>" class="btn btn-light btn-block">
                        <img src="<?= ASSETS_FULL_URL . 'images/discord.svg' ?>" class="mr-1" />
                        <?= l('login.discord') ?>
                    </a>
                </div>
            <?php endif ?>
            <?php if(settings()->linkedin->is_enabled): ?>
                <div class="mt-2">
                    <a href="<?= url('login/linkedin-initiate') ?>" class="btn btn-light btn-block">
                        <img src="<?= ASSETS_FULL_URL . 'images/linkedin.svg' ?>" class="mr-1" />
                        <?= l('login.linkedin') ?>
                    </a>
                </div>
            <?php endif ?>
            <?php if(settings()->microsoft->is_enabled): ?>
                <div class="mt-2">
                    <a href="<?= url('login/microsoft-initiate') ?>" class="btn btn-light btn-block">
                        <img src="<?= ASSETS_FULL_URL . 'images/microsoft.svg' ?>" class="mr-1" />
                        <?= l('login.microsoft') ?>
                    </a>
                </div>
            <?php endif ?>
        </div>
    <?php endif ?>
</form>

<?php if(settings()->users->register_is_enabled): ?>
    <div class="mt-5 text-center text-muted">
        <?= sprintf(l('login.register'), '<a href="' . url('register' . $data->redirect_append) . '" class="font-weight-bold">' . l('login.register_help') . '</a>') ?></a>
    </div>
<?php endif ?>

<?php ob_start() ?>
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "BreadcrumbList",
            "itemListElement": [
                {
                    "@type": "ListItem",
                    "position": 1,
                    "name": "<?= l('index.title') ?>",
                    "item": "<?= url() ?>"
                },
                {
                    "@type": "ListItem",
                    "position": 2,
                    "name": "<?= l('login.title') ?>",
                    "item": "<?= url('login') ?>"
                }
            ]
        }
    </script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>


