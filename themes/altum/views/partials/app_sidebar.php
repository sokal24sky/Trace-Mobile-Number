<?php defined('ALTUMCODE') || die() ?>

<div class="app-sidebar">
    <div class="app-sidebar-title text-truncate">
        <a
            href="<?= url() ?>"
            class="navbar-brand mr-0 text-truncate"
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
    </div>

    <div class="overflow-auto flex-grow-1">
        <ul class="app-sidebar-links">
            <li class="<?= \Altum\Router::$controller == 'Dashboard' ? 'active' : null ?> d-flex dropdown" id="internal_notifications">
                <a href="<?= url('dashboard') ?>"><i class="fas fa-fw fa-sm fa-th mr-2"></i> <?= l('dashboard.menu') ?></a>

                <?php if(settings()->internal_notifications->users_is_enabled): ?>
                    <a id="internal_notifications_link" href="#" class="default w-auto dropdown-toggle dropdown-toggle-simple ml-1" data-internal-notifications="user" data-tooltip data-tooltip-hide-on-click title="<?= l('internal_notifications.menu') ?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-boundary="window">
                        <span id="internal_notifications_icon_wrapper" class="fa-layers fa-fw">
                            <i class="fas fa-fw fa-bell"></i>
                            <?php if($this->user->has_pending_internal_notifications): ?>
                                <span class="fa-layers-counter text-danger internal-notification-icon">&nbsp;</span>
                            <?php endif ?>
                        </span>
                    </a>

                    <div id="internal_notifications_content" class="dropdown-menu dropdown-menu-right px-4 py-2" style="width: 550px;max-width: 550px;"></div>

                    <?php include_view(THEME_PATH . 'views/partials/internal_notifications_js.php', ['has_pending_internal_notifications' => $this->user->has_pending_internal_notifications]) ?>
                <?php endif ?>
            </li>

            <li class="<?= in_array(\Altum\Router::$controller, ['Links', 'LinkUpdate', 'LinkStatistics']) ? 'active' : null ?> d-flex">
                <a href="<?= url('links') ?>"><i class="fas fa-fw fa-sm fa-link mr-2"></i> <?= l('links.menu') ?></a>

                <a href="<?= url('link-create') ?>" class="<?= in_array(\Altum\Router::$controller, ['LinkCreate']) ? 'bg-primary text-white' : null ?> default w-auto ml-1" data-tooltip data-tooltip-hide-on-click title="<?= l('link_create.menu') ?>">
                    <i class="fas fa-fw fa-sm fa-plus-circle"></i>
                </a>
            </li>

            <li class="<?= in_array(\Altum\Router::$controller, ['LinksStatistics']) ? 'active' : null ?> d-flex">
                <a href="<?= url('links-statistics') ?>"><i class="fas fa-fw fa-sm fa-chart-bar mr-2"></i> <?= l('links_statistics.menu') ?></a>
            </li>

            <?php if(settings()->codes->ai_qr_codes_is_enabled): ?>
                <li class="<?= in_array(\Altum\Router::$controller, ['AiQrCodes', 'AiQrCodeUpdate']) ? 'active' : null ?> d-flex">
                    <a href="<?= url('ai-qr-codes') ?>"><i class="fas fa-fw fa-sm fa-robot mr-2"></i> <?= l('ai_qr_codes.menu') ?></a>

                    <a href="<?= is_logged_in() ? url('ai-qr-code-create') : url('ai-qr') ?>" class="<?= in_array(\Altum\Router::$controller, ['AiQrCodeCreate']) ? 'bg-primary text-white' : null ?> default w-auto ml-1" data-tooltip data-tooltip-hide-on-click title="<?= l('ai_qr.menu') ?>">
                        <i class="fas fa-fw fa-sm fa-plus-circle"></i>
                    </a>
                </li>
            <?php endif ?>

            <?php if(settings()->codes->qr_codes_is_enabled): ?>
            <li class="<?= in_array(\Altum\Router::$controller, ['QrCodes', 'QrCodeUpdate']) ? 'active' : null ?> d-flex">
                <a href="<?= url('qr-codes') ?>"><i class="fas fa-fw fa-sm fa-qrcode mr-2"></i> <?= l('qr_codes.menu') ?></a>

                <a href="<?= is_logged_in() ? url('qr-code-create') : url('qr') ?>" class="<?= in_array(\Altum\Router::$controller, ['QrCodeCreate']) ? 'bg-primary text-white' : null ?> default w-auto ml-1" data-tooltip data-tooltip-hide-on-click title="<?= l('qr.menu') ?>">
                    <i class="fas fa-fw fa-sm fa-plus-circle"></i>
                </a>
            </li>
            <?php endif ?>

            <?php if(settings()->codes->qr_reader_is_enabled): ?>
                <li class="<?= \Altum\Router::$controller == 'QrReader' ? 'active' : null ?>">
                    <a href="<?= url('qr-reader') ?>"><i class="fas fa-fw fa-sm fa-glasses mr-2"></i> <?= l('qr_reader.menu') ?></a>
                </li>
            <?php endif ?>

            <?php if(settings()->codes->barcodes_is_enabled): ?>
                <div class="divider-wrapper">
                    <div class="divider"></div>
                </div>

                <li class="<?= in_array(\Altum\Router::$controller, ['Barcodes', 'BarcodeUpdate']) ? 'active' : null ?> d-flex">
                    <a href="<?= url('barcodes') ?>"><i class="fas fa-fw fa-sm fa-barcode mr-2"></i> <?= l('barcodes.menu') ?></a>

                    <a href="<?= is_logged_in() ? url('barcode-create') : url('barcode') ?>" class="<?= in_array(\Altum\Router::$controller, ['BarcodeCreate']) ? 'bg-primary text-white' : null ?> default w-auto ml-1" data-tooltip data-tooltip-hide-on-click title="<?= l('barcode.menu') ?>">
                        <i class="fas fa-fw fa-sm fa-plus-circle"></i>
                    </a>
                </li>
            <?php endif ?>

            <?php if(settings()->codes->barcode_reader_is_enabled): ?>
                <li class="<?= \Altum\Router::$controller == 'BarcodeReader' ? 'active' : null ?>">
                    <a href="<?= url('barcode-reader') ?>"><i class="fas fa-fw fa-sm fa-print mr-2"></i> <?= l('barcode_reader.menu') ?></a>
                </li>
            <?php endif ?>

            <div class="divider-wrapper">
                <div class="divider"></div>
            </div>

            <?php if(settings()->links->projects_is_enabled): ?>
            <li class="<?= in_array(\Altum\Router::$controller, ['Projects', 'ProjectUpdate', 'ProjectCreate']) ? 'active' : null ?>">
                <a href="<?= url('projects') ?>"><i class="fas fa-fw fa-sm fa-project-diagram mr-2"></i> <?= l('projects.menu') ?></a>
            </li>
            <?php endif ?>

            <?php if(settings()->links->pixels_is_enabled): ?>
            <li class="<?= in_array(\Altum\Router::$controller, ['Pixels', 'PixelUpdate', 'PixelCreate']) ? 'active' : null ?>">
                <a href="<?= url('pixels') ?>"><i class="fas fa-fw fa-sm fa-adjust mr-2"></i> <?= l('pixels.menu') ?></a>
            </li>
            <?php endif ?>

            <?php if(settings()->links->domains_is_enabled): ?>
                <li class="<?= in_array(\Altum\Router::$controller, ['Domains', 'DomainUpdate', 'DomainCreate']) ? 'active' : null ?>">
                    <a href="<?= url('domains') ?>"><i class="fas fa-fw fa-sm fa-globe mr-2"></i> <?= l('domains.menu') ?></a>
                </li>
            <?php endif ?>

            <?php foreach($data->pages as $page): ?>
                <li>
                    <a href="<?= $page->url ?>" target="<?= $page->target ?>">
                        <?php if($page->icon): ?>
                            <i class="<?= $page->icon ?> fa-fw fa-sm mr-2"></i>
                        <?php endif ?>

                        <?= $page->title ?>
                    </a>
                </li>
            <?php endforeach ?>
        </ul>
    </div>

    <?php if(is_logged_in()): ?>

        <div class="app-sidebar-footer dropdown">
            <a href="#" class="dropdown-toggle dropdown-toggle-simple" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <div class="d-flex align-items-center app-sidebar-footer-block">
                    <img src="<?= get_user_avatar($this->user->avatar, $this->user->email) ?>" class="app-sidebar-avatar mr-3" loading="lazy" />

                    <div class="app-sidebar-footer-text d-flex flex-column text-truncate">
                        <span class="text-truncate"><?= $this->user->name ?></span>
                        <small class="text-truncate"><?= $this->user->email ?></small>
                    </div>
                </div>
            </a>

            <div class="dropdown-menu dropdown-menu-right">
                <?php if(!\Altum\Teams::is_delegated()): ?>
                    <?php if(\Altum\Authentication::is_admin()): ?>
                        <a class="dropdown-item" href="<?= url('admin') ?>"><i class="fas fa-fw fa-sm fa-fingerprint text-primary mr-2"></i> <?= l('global.menu.admin') ?></a>
                        <div class="dropdown-divider"></div>
                    <?php endif ?>

                    <a class="dropdown-item <?= in_array(\Altum\Router::$controller, ['Account']) ? 'active' : null ?>" href="<?= url('account') ?>"><i class="fas fa-fw fa-sm fa-user-cog mr-2"></i> <?= l('account.menu') ?></a>

                    <a class="dropdown-item <?= in_array(\Altum\Router::$controller, ['AccountPreferences']) ? 'active' : null ?>" href="<?= url('account-preferences') ?>"><i class="fas fa-fw fa-sm fa-sliders-h mr-2"></i> <?= l('account_preferences.menu') ?></a>

                    <a class="dropdown-item <?= in_array(\Altum\Router::$controller, ['AccountPlan']) ? 'active' : null ?>" href="<?= url('account-plan') ?>"><i class="fas fa-fw fa-sm fa-box-open mr-2"></i> <?= l('account_plan.menu') ?></a>

                    <?php if(settings()->payment->is_enabled): ?>
                        <a class="dropdown-item <?= in_array(\Altum\Router::$controller, ['AccountPayments']) ? 'active' : null ?>" href="<?= url('account-payments') ?>"><i class="fas fa-fw fa-sm fa-credit-card mr-2"></i> <?= l('account_payments.menu') ?></a>

                        <?php if(\Altum\Plugin::is_active('affiliate') && settings()->affiliate->is_enabled): ?>
                            <a class="dropdown-item <?= in_array(\Altum\Router::$controller, ['Referrals']) ? 'active' : null ?>" href="<?= url('referrals') ?>"><i class="fas fa-fw fa-sm fa-wallet mr-2"></i> <?= l('referrals.menu') ?></a>
                        <?php endif ?>
                    <?php endif ?>

                    <?php if(settings()->main->api_is_enabled): ?>
                        <a class="dropdown-item <?= in_array(\Altum\Router::$controller, ['AccountApi']) ? 'active' : null ?>" href="<?= url('account-api') ?>"><i class="fas fa-fw fa-sm fa-code mr-2"></i> <?= l('account_api.menu') ?></a>
                    <?php endif ?>

                    <?php if(\Altum\Plugin::is_active('teams')): ?>
                        <a class="dropdown-item <?= in_array(\Altum\Router::$controller, ['TeamsSystem', 'Teams', 'Team', 'TeamCreate', 'TeamUpdate', 'TeamsMember', 'TeamsMembers', 'TeamsMemberCreate', 'TeamsMemberUpdate']) ? 'active' : null ?>" href="<?= url('teams-system') ?>"><i class="fas fa-fw fa-sm fa-user-shield mr-2"></i> <?= l('teams_system.menu') ?></a>
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

    <?php else: ?>

        <ul class="app-sidebar-links">
            <li>
                <a class="nav-link" href="<?= url('login') ?>"><i class="fas fa-fw fa-sm fa-sign-in-alt mr-2"></i> <?= l('login.menu') ?></a>
            </li>

            <?php if(settings()->users->register_is_enabled): ?>
                <li><a class="nav-link" href="<?= url('register') ?>"><i class="fas fa-fw fa-sm fa-user-plus mr-2"></i> <?= l('register.menu') ?></a></li>
            <?php endif ?>
        </ul>

    <?php endif ?>
</div>
