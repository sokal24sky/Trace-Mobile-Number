<?php defined('ALTUMCODE') || die() ?>

<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom border-gray-200 <?= \Altum\Router::$controller_key == 'index' ? null : 'mb-6' ?>">
    <div class="container">
        <a
                href="<?= url() ?>"
                class="navbar-brand d-flex"
                data-logo
                data-light-value="<?= settings()->main->logo_light != '' ? settings()->main->logo_light_full_url : settings()->main->title ?>"
                data-light-class="<?= settings()->main->logo_light != '' ? 'img-fluid navbar-logo' : '' ?>"
                data-light-tag="<?= settings()->main->logo_light != '' ? 'img' : 'span' ?>"
                data-dark-value="<?= settings()->main->logo_dark != '' ? settings()->main->logo_dark_full_url : settings()->main->title ?>"
                data-dark-class="<?= settings()->main->logo_dark != '' ? 'img-fluid navbar-logo' : '' ?>"
                data-dark-tag="<?= settings()->main->logo_dark != '' ? 'img' : 'span' ?>"
        >
            <?php if(settings()->main->{'logo_' . \Altum\ThemeStyle::get()} != ''): ?>
                <img src="<?= settings()->main->{'logo_' . \Altum\ThemeStyle::get() . '_full_url'} ?>" class="img-fluid navbar-logo" alt="<?= l('global.accessibility.logo_alt') ?>" />
            <?php else: ?>
                <?= settings()->main->title ?>
            <?php endif ?>
        </a>

        <button class="btn navbar-custom-toggler d-lg-none" type="button" data-toggle="collapse" data-target="#main_navbar" aria-controls="main_navbar" aria-expanded="false" aria-label="<?= l('global.accessibility.toggle_navigation') ?>">
            <i class="fas fa-fw fa-bars"></i>
        </button>

        <div class="collapse navbar-collapse justify-content-end" id="main_navbar">
            <ul class="navbar-nav">

                <?php foreach($data->pages as $data): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $data->url ?>" target="<?= $data->target ?>">
                            <?php if($data->icon): ?>
                                <i class="<?= $data->icon ?> fa-fw fa-sm mr-1"></i>
                            <?php endif ?>

                            <?= $data->title ?>
                        </a>
                    </li>
                <?php endforeach ?>

                <?php if(settings()->payment->is_enabled): ?>
                    <li class="nav-item"><a class="nav-link" href="<?= url('plan') ?>"> <?= l('plan.menu') ?></a></li>
                <?php endif ?>

                <?php if(is_logged_in()): ?>

                    <li class="nav-item"><a class="nav-link" href="<?= url('dashboard') ?>"> <?= l('dashboard.menu') ?></a></li>

                    <?php if(settings()->internal_notifications->users_is_enabled): ?>
                        <li class="nav-item dropdown" id="internal_notifications">
                            <a id="internal_notifications_link" href="#" class="nav-link dropdown-toggle dropdown-toggle-simple" data-internal-notifications="user" data-tooltip data-tooltip-hide-on-click title="<?= l('internal_notifications.menu') ?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-boundary="window">
                                <span class="fa-layers fa-fw">
                                    <i class="fas fa-fw fa-bell"></i>
                                    <?php if($this->user->has_pending_internal_notifications): ?>
                                        <span class="fa-layers-counter text-danger internal-notification-icon">&nbsp;</span>
                                    <?php endif ?>
                                </span>
                                <span class="d-lg-none ml-1"><?= l('internal_notifications.menu') ?></span>
                            </a>

                            <div id="internal_notifications_content" class="dropdown-menu dropdown-menu-right px-4 py-2" style="width: 550px;max-width: 550px;"></div>
                        </li>

                        <?php include_view(THEME_PATH . 'views/partials/internal_notifications_js.php', ['has_pending_internal_notifications' => $this->user->has_pending_internal_notifications]) ?>
                    <?php endif ?>

                    <li class="dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" data-toggle="dropdown" href="#" aria-haspopup="true" aria-expanded="false">
                            <img src="<?= get_user_avatar($this->user->avatar, $this->user->email) ?>" class="navbar-avatar mr-2" loading="lazy" />
                            <?= $this->user->name ?>
                            <span class="ml-2 caret"></span>
                        </a>

                        <div class="dropdown-menu dropdown-menu-right">
                            <div class="d-flex flex-column flex-lg-row">
                                <div class="pr-lg-3">
                                    <div
                                            class="px-3 py-2 font-weight-bold"
                                            data-logo
                                            data-light-value="<?= settings()->main->logo_light != '' ? settings()->main->logo_light_full_url : settings()->main->title ?>"
                                            data-light-class="<?= settings()->main->logo_light != '' ? 'img-fluid navbar-logo-mini' : '' ?>"
                                            data-light-tag="<?= settings()->main->logo_light != '' ? 'img' : 'span' ?>"
                                            data-dark-value="<?= settings()->main->logo_dark != '' ? settings()->main->logo_dark_full_url : settings()->main->title ?>"
                                            data-dark-class="<?= settings()->main->logo_dark != '' ? 'img-fluid navbar-logo-mini' : '' ?>"
                                            data-dark-tag="<?= settings()->main->logo_dark != '' ? 'img' : 'span' ?>"
                                    >
                                        <?php if(settings()->main->{'logo_' . \Altum\ThemeStyle::get()} != ''): ?>
                                            <img src="<?= settings()->main->{'logo_' . \Altum\ThemeStyle::get() . '_full_url'} ?>" class="img-fluid navbar-logo-mini" alt="<?= l('global.accessibility.logo_alt') ?>" data-toggle="tooltip" title="<?= settings()->main->title ?>" />
                                        <?php else: ?>
                                            <?= settings()->main->title ?>
                                        <?php endif ?>
                                    </div>

                                    <div class="dropdown-divider"></div>

                                    <a href="<?= url('links') ?>" class="dropdown-item"><i class="fas fa-fw fa-sm fa-link mr-2"></i> <?= l('links.menu') ?></a>
                                    <a href="<?= url('links-statistics') ?>" class="dropdown-item"><i class="fas fa-fw fa-sm fa-chart-bar mr-2"></i> <?= l('links_statistics.menu') ?></a>

                                    <?php if(settings()->codes->ai_qr_codes_is_enabled): ?>
                                        <a href="<?= url('ai-qr-codes') ?>" class="dropdown-item"><i class="fas fa-fw fa-sm fa-robot mr-2"></i> <?= l('ai_qr_codes.menu') ?></a>
                                    <?php endif ?>

                                    <?php if(settings()->codes->qr_codes_is_enabled): ?>
                                        <a href="<?= url('qr-codes') ?>" class="dropdown-item"><i class="fas fa-fw fa-sm fa-qrcode mr-2"></i> <?= l('qr_codes.menu') ?></a>
                                    <?php endif ?>

                                    <?php if(settings()->codes->qr_reader_is_enabled): ?>
                                        <a href="<?= url('qr-reader') ?>" class="dropdown-item"><i class="fas fa-fw fa-sm fa-glasses mr-2"></i> <?= l('qr_reader.menu') ?></a>
                                    <?php endif ?>


                                    <?php if(settings()->codes->barcodes_is_enabled): ?>
                                        <div class="dropdown-divider"></div>

                                        <a href="<?= url('barcodes') ?>" class="dropdown-item"><i class="fas fa-fw fa-sm fa-barcode mr-2"></i> <?= l('barcodes.menu') ?></a>
                                    <?php endif ?>

                                    <?php if(settings()->codes->barcode_reader_is_enabled): ?>
                                        <a href="<?= url('barcode-reader') ?>" class="dropdown-item"><i class="fas fa-fw fa-sm fa-print mr-2"></i> <?= l('barcode_reader.menu') ?></a>
                                    <?php endif ?>

                                    <div class="dropdown-divider"></div>

                                    <?php if(settings()->links->projects_is_enabled): ?>
                                    <a href="<?= url('projects') ?>" class="dropdown-item"><i class="fas fa-fw fa-sm fa-project-diagram mr-2"></i> <?= l('projects.menu') ?></a>
                                    <?php endif ?>

                                    <?php if(settings()->links->pixels_is_enabled): ?>
                                    <a href="<?= url('pixels') ?>" class="dropdown-item"><i class="fas fa-fw fa-sm fa-adjust mr-2"></i> <?= l('pixels.menu') ?></a>
                                    <?php endif ?>

                                    <?php if(settings()->links->domains_is_enabled): ?>
                                        <a href="<?= url('domains') ?>" class="dropdown-item"><i class="fas fa-fw fa-sm fa-globe mr-2"></i> <?= l('domains.menu') ?></a>
                                    <?php endif ?>
                                </div>

                                <div>
                                    <?php if(!\Altum\Teams::is_delegated()): ?>
                                        <?php if(\Altum\Authentication::is_admin()): ?>
                                            <a class="dropdown-item" href="<?= url('admin') ?>"><i class="fas fa-fw fa-sm fa-fingerprint text-primary mr-2"></i> <?= l('global.menu.admin') ?></a>
                                            <div class="dropdown-divider"></div>
                                        <?php else: ?>
                                            <div class="px-3 py-2 font-weight-bold  d-flex align-items-center">
                                                <img src="<?= get_user_avatar($this->user->avatar, $this->user->email) ?>" class="navbar-logo-mini rounded mr-2" loading="lazy" />
                                                <div class="text-truncate d-inline-block"><?= $this->user->email ?></div>
                                            </div>

                                            <div class="dropdown-divider"></div>
                                        <?php endif ?>

                                        <a class="dropdown-item" href="<?= url('account') ?>"><i class="fas fa-fw fa-sm fa-user-cog mr-2"></i> <?= l('account.menu') ?></a>

                                        <a class="dropdown-item" href="<?= url('account-preferences') ?>"><i class="fas fa-fw fa-sm fa-sliders-h mr-2"></i> <?= l('account_preferences.menu') ?></a>

                                        <a class="dropdown-item" href="<?= url('account-plan') ?>"><i class="fas fa-fw fa-sm fa-box-open mr-2"></i> <?= l('account_plan.menu') ?></a>
                                        <?php if(settings()->payment->is_enabled): ?>
                                            <a class="dropdown-item" href="<?= url('account-payments') ?>"><i class="fas fa-fw fa-sm fa-credit-card mr-2"></i> <?= l('account_payments.menu') ?></a>

                                            <?php if(\Altum\Plugin::is_active('affiliate') && settings()->affiliate->is_enabled): ?>
                                                <a class="dropdown-item" href="<?= url('referrals') ?>"><i class="fas fa-fw fa-sm fa-wallet mr-2"></i> <?= l('referrals.menu') ?></a>
                                            <?php endif ?>
                                        <?php endif ?>

                                        <?php if(settings()->main->api_is_enabled): ?>
                                            <a class="dropdown-item" href="<?= url('account-api') ?>"><i class="fas fa-fw fa-sm fa-code mr-2"></i> <?= l('account_api.menu') ?></a>
                                        <?php endif ?>

                                        <?php if(\Altum\Plugin::is_active('teams')): ?>
                                            <a class="dropdown-item" href="<?= url('teams-system') ?>"><i class="fas fa-fw fa-sm fa-user-shield mr-2"></i> <?= l('teams_system.menu') ?></a>
                                        <?php endif ?>

                                        <?php if(settings()->sso->is_enabled && settings()->sso->display_menu_items && count((array) settings()->sso->websites)): ?>
                                            <div class="dropdown-divider"></div>

                                            <?php foreach(settings()->sso->websites as $website): ?>
                                                <a class="dropdown-item" href="<?= url('sso/switch?to=' . $website->id) ?>"><i class="<?= $website->icon ?> fa-fw fa-sm mr-2"></i> <?= sprintf(l('sso.menu'), $website->name) ?></a>
                                            <?php endforeach ?>
                                        <?php endif ?>

                                        <div class="dropdown-divider"></div>
                                    <?php endif ?>

                                    <a class="dropdown-item" href="<?= url('logout') ?>"><i class="fas fa-fw fa-sm fa-sign-out-alt mr-2"></i> <?= l('global.menu.logout') ?></a>
                                </div>
                            </div>
                        </div>
                    </li>

                <?php else: ?>

                    <li class="nav-item">
                        <a class="nav-link text-primary" href="<?= url('login') ?>">
                            <i class="fas fa-sign-in-alt fa-fw fa-sm mr-2"></i>
                            <?= l('login.menu') ?>
                        </a>
                    </li>

                    <?php if(settings()->users->register_is_enabled): ?>
                        <li class="nav-item">
                            <a class="nav-link text-primary" href="<?= url('register') ?>">
                                <i class="fas fa-user-plus fa-fw fa-sm mr-2"></i>
                                <?= l('register.menu') ?>
                            </a>
                        </li>
                    <?php endif ?>

                <?php endif ?>

            </ul>
        </div>
    </div>
</nav>
