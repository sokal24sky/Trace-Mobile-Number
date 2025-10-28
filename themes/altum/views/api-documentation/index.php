<?php defined('ALTUMCODE') || die() ?>

<div class="container">
    <?php if(settings()->main->breadcrumbs_is_enabled): ?>
        <nav aria-label="breadcrumb">
            <ol class="custom-breadcrumbs small">
                <li><a href="<?= url() ?>"><?= l('index.breadcrumb') ?></a> <i class="fas fa-fw fa-angle-right"></i></li>
                <li class="active" aria-current="page"><?= l('api_documentation.breadcrumb') ?></li>
            </ol>
        </nav>
    <?php endif ?>

    <h1 class="h4"><?= l('api_documentation.header') ?></h1>
    <p class="text-muted"><?= l('api_documentation.subheader') ?></p>

    <div class="card mb-5">
        <div class="card-body">
            <div class="mb-5">
                <?php if(is_logged_in()): ?>
                    <div class="form-group">
                        <label for="api_key"><?= l('api_documentation.api_key') ?></label>
                        <input type="text" id="api_key" value="<?= $this->user->api_key ?>" class="form-control" onclick="this.select();" readonly="readonly" />
                    </div>
                <?php else: ?>
                    <div class="mb-3">
                        <a href="<?= url('account-api') ?>" target="_blank" class="btn btn-block btn-outline-primary"><?= l('api_documentation.api_key') ?></a>
                    </div>
                <?php endif ?>

                <div class="form-group">
                    <label for="base_url"><?= l('api_documentation.base_url') ?></label>
                    <input type="text" id="base_url" value="<?= SITE_URL . 'api' ?>" class="form-control" onclick="this.select();" readonly="readonly" />
                </div>
            </div>

            <div class="mb-4">
                <h2 class="h5"><?= l('api_documentation.authentication.header') ?></h2>
                <p class="text-muted"><?= l('api_documentation.authentication.subheader') ?></p>
            </div>

            <div class="form-group">
                <label><?= l('api_documentation.example') ?></label>
                <div class="card bg-gray-50 border-0">
                    <div class="card-body">
                        curl --request GET \<br />
                        --url '<?= SITE_URL . 'api/' ?><span class="text-primary">{endpoint}</span>' \<br />
                        --header 'Authorization: Bearer <span class="text-primary" <?= is_logged_in() ? 'data-toggle="tooltip" title="' . l('api_documentation.api_key') . '"' : null ?>><?= is_logged_in() ? $this->user->api_key : '{api_key}' ?></span>' \
                    </div>
                </div>
            </div>

            <div class="alert alert-light">
                <i class="fas fa-fw fa-sm fa-info-circle mr-1"></i> <?= sprintf(l('api_documentation.timezone_info'), \Altum\Date::$default_timezone) ?>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-sm-6 col-xl-4 p-3 position-relative">
            <div class="card d-flex flex-row h-100 overflow-hidden">
                <div class="border-right border-gray-100 px-3 d-flex flex-column justify-content-center">
                    <a href="<?= url('api-documentation/user') ?>" class="stretched-link">
                        <i class="fas fa-fw fa-user text-primary-600"></i>
                    </a>
                </div>

                <div class="card-body d-flex align-items-center">
                    <?= l('api_documentation.user') ?>
                </div>
            </div>
        </div>

        <?php if(settings()->codes->ai_qr_codes_is_enabled): ?>
            <div class="col-12 col-sm-6 col-xl-4 p-3 position-relative">
                <div class="card d-flex flex-row h-100 overflow-hidden">
                    <div class="border-right border-gray-100 px-3 d-flex flex-column justify-content-center">
                        <a href="<?= url('api-documentation/ai-qr-codes') ?>" class="stretched-link">
                            <i class="fas fa-fw fa-robot text-primary-600"></i>
                        </a>
                    </div>

                    <div class="card-body">
                        <?= l('ai_qr_codes.title') ?>
                    </div>
                </div>
            </div>
        <?php endif ?>

        <?php if(settings()->codes->qr_codes_is_enabled): ?>
            <div class="col-12 col-sm-6 col-xl-4 p-3 position-relative">
                <div class="card d-flex flex-row h-100 overflow-hidden">
                    <div class="border-right border-gray-100 px-3 d-flex flex-column justify-content-center">
                        <a href="<?= url('api-documentation/qr-codes') ?>" class="stretched-link">
                            <i class="fas fa-fw fa-qrcode text-primary-600"></i>
                        </a>
                    </div>

                    <div class="card-body">
                        <?= l('qr_codes.title') ?>
                    </div>
                </div>
            </div>
        <?php endif ?>

        <?php if(settings()->codes->barcodes_is_enabled): ?>
            <div class="col-12 col-sm-6 col-xl-4 p-3 position-relative">
                <div class="card d-flex flex-row h-100 overflow-hidden">
                    <div class="border-right border-gray-100 px-3 d-flex flex-column justify-content-center">
                        <a href="<?= url('api-documentation/barcodes') ?>" class="stretched-link">
                            <i class="fas fa-fw fa-barcode text-primary-600"></i>
                        </a>
                    </div>

                    <div class="card-body">
                        <?= l('barcodes.title') ?>
                    </div>
                </div>
            </div>
        <?php endif ?>

        <div class="col-12 col-sm-6 col-xl-4 p-3 position-relative">
            <div class="card d-flex flex-row h-100 overflow-hidden">
                <div class="border-right border-gray-100 px-3 d-flex flex-column justify-content-center">
                    <a href="<?= url('api-documentation/links') ?>" class="stretched-link">
                        <i class="fas fa-fw fa-link text-primary-600"></i>
                    </a>
                </div>

                <div class="card-body d-flex align-items-center">
                    <?= l('api_documentation.links') ?>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-4 p-3 position-relative">
            <div class="card d-flex flex-row h-100 overflow-hidden">
                <div class="border-right border-gray-100 px-3 d-flex flex-column justify-content-center">
                    <a href="<?= url('api-documentation/statistics') ?>" class="stretched-link">
                        <i class="fas fa-fw fa-chart-bar text-primary-600"></i>
                    </a>
                </div>

                <div class="card-body d-flex align-items-center">
                    <?= l('api_documentation.statistics') ?>
                </div>
            </div>
        </div>

        <?php if(settings()->links->projects_is_enabled): ?>
        <div class="col-12 col-sm-6 col-xl-4 p-3 position-relative">
            <div class="card d-flex flex-row h-100 overflow-hidden">
                <div class="border-right border-gray-100 px-3 d-flex flex-column justify-content-center">
                    <a href="<?= url('api-documentation/projects') ?>" class="stretched-link">
                        <i class="fas fa-fw fa-project-diagram text-primary-600"></i>
                    </a>
                </div>

                <div class="card-body d-flex align-items-center">
                    <?= l('projects.title') ?>
                </div>
            </div>
        </div>
        <?php endif ?>

        <?php if(settings()->links->pixels_is_enabled): ?>
            <div class="col-12 col-sm-6 col-xl-4 p-3 position-relative">
                <div class="card d-flex flex-row h-100 overflow-hidden">
                    <div class="border-right border-gray-100 px-3 d-flex flex-column justify-content-center">
                        <a href="<?= url('api-documentation/pixels') ?>" class="stretched-link">
                            <i class="fas fa-fw fa-adjust text-primary-600"></i>
                        </a>
                    </div>

                    <div class="card-body">
                        <?= l('pixels.title') ?>
                    </div>
                </div>
            </div>
        <?php endif ?>

        <?php if(settings()->links->domains_is_enabled): ?>
            <div class="col-12 col-sm-6 col-xl-4 p-3 position-relative">
                <div class="card d-flex flex-row h-100 overflow-hidden">
                    <div class="border-right border-gray-100 px-3 d-flex flex-column justify-content-center">
                        <a href="<?= url('api-documentation/domains') ?>" class="stretched-link">
                            <i class="fas fa-fw fa-globe text-primary-600"></i>
                        </a>
                    </div>

                    <div class="card-body">
                        <?= l('domains.title') ?>
                    </div>
                </div>
            </div>
        <?php endif ?>

        <?php if(\Altum\Plugin::is_active('teams')): ?>
            <div class="col-12 col-sm-6 col-xl-4 p-3 position-relative">
                <div class="card d-flex flex-row h-100 overflow-hidden">
                    <div class="border-right border-gray-100 px-3 d-flex flex-column justify-content-center">
                        <a href="<?= url('api-documentation/teams') ?>" class="stretched-link">
                            <i class="fas fa-fw fa-user-cog text-primary-600"></i>
                        </a>
                    </div>

                    <div class="card-body">
                        <?= l('teams.title') ?>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-4 p-3 position-relative">
                <div class="card d-flex flex-row h-100 overflow-hidden">
                    <div class="border-right border-gray-100 px-3 d-flex flex-column justify-content-center">
                        <a href="<?= url('api-documentation/team-members') ?>" class="stretched-link">
                            <i class="fas fa-fw fa-users-cog text-primary-600"></i>
                        </a>
                    </div>

                    <div class="card-body">
                        <?= l('api_documentation.team_members') ?>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-4 p-3 position-relative">
                <div class="card d-flex flex-row h-100 overflow-hidden">
                    <div class="border-right border-gray-100 px-3 d-flex flex-column justify-content-center">
                        <a href="<?= url('api-documentation/teams-member') ?>" class="stretched-link">
                            <i class="fas fa-fw fa-user-tag text-primary-600"></i>
                        </a>
                    </div>

                    <div class="card-body">
                        <?= l('api_documentation.teams_member') ?>
                    </div>
                </div>
            </div>
        <?php endif ?>

        <div class="col-12 col-sm-6 col-xl-4 p-3 position-relative">
            <div class="card d-flex flex-row h-100 overflow-hidden">
                <div class="border-right border-gray-100 px-3 d-flex flex-column justify-content-center">
                    <a href="<?= url('api-documentation/payments') ?>" class="stretched-link">
                        <i class="fas fa-fw fa-credit-card text-primary-600"></i>
                    </a>
                </div>

                <div class="card-body d-flex align-items-center">
                    <?= l('account_payments.title') ?>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-4 p-3 position-relative">
            <div class="card d-flex flex-row h-100 overflow-hidden">
                <div class="border-right border-gray-100 px-3 d-flex flex-column justify-content-center">
                    <a href="<?= url('api-documentation/users-logs') ?>" class="stretched-link">
                        <i class="fas fa-fw fa-scroll text-primary-600"></i>
                    </a>
                </div>

                <div class="card-body d-flex align-items-center">
                    <?= l('account_logs.title') ?>
                </div>
            </div>
        </div>
    </div>
</div>

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
                    "name": "<?= l('api_documentation.title') ?>",
                    "item": "<?= url('api-documentation') ?>"
                }
            ]
        }
    </script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
