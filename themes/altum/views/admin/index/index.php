<?php defined('ALTUMCODE') || die() ?>

<h1 class="h3 mb-4 text-truncate"><?= sprintf(l('admin_index.header'), $this->user->name) ?></h1>

<div class="mb-5 row justify-content-between">
    <div class="col-12 col-sm-6 col-xl-3 p-3 position-relative">
        <div class="card d-flex flex-row h-100 overflow-hidden">
            <div class="card-body">
                <div class="row">
                    <div class="col text-truncate">
                        <small class="text-muted font-weight-bold"><?= l('admin_ai_qr_codes.menu') ?></small>
                    </div>

                    <div class="col-auto">
                    <span class="p-2 bg-primary-100 rounded">
                        <i class="fas fa-fw fa-sm fa-robot text-primary"></i>
                    </span>
                    </div>
                </div>

                <div class="mt-2 text-break">
                    <a href="<?= url('admin/ai-qr-codes') ?>" class="stretched-link text-reset text-decoration-none">
                        <span class="h4" id="ai_qr_codes">
                            <span class="spinner-border spinner-border-sm" role="status"></span>
                        </span>
                    </a>

                    <div class="mt-1 small text-muted">
                        <span id="ai_qr_codes_current_month">
                            <span class="spinner-border spinner-border-sm" role="status"></span>
                        </span>
                        <?= mb_strtolower(l('global.date.this_month')) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3 p-3 position-relative">
        <div class="card d-flex flex-row h-100 overflow-hidden">
            <div class="card-body">
                <div class="row">
                    <div class="col text-truncate">
                        <small class="text-muted font-weight-bold"><?= l('admin_qr_codes.menu') ?></small>
                    </div>

                    <div class="col-auto">
                    <span class="p-2 bg-primary-100 rounded">
                        <i class="fas fa-fw fa-sm fa-qrcode text-primary"></i>
                    </span>
                    </div>
                </div>

                <div class="mt-2 text-break">
                    <a href="<?= url('admin/qr-codes') ?>" class="stretched-link text-reset text-decoration-none">
                        <span class="h4" id="qr_codes">
                            <span class="spinner-border spinner-border-sm" role="status"></span>
                        </span>
                    </a>

                    <div class="mt-1 small text-muted">
                        <span id="qr_codes_current_month">
                            <span class="spinner-border spinner-border-sm" role="status"></span>
                        </span>
                        <?= mb_strtolower(l('global.date.this_month')) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3 p-3 position-relative">
        <div class="card d-flex flex-row h-100 overflow-hidden">
            <div class="card-body">
                <div class="row">
                    <div class="col text-truncate">
                        <small class="text-muted font-weight-bold"><?= l('admin_barcodes.menu') ?></small>
                    </div>

                    <div class="col-auto">
                    <span class="p-2 bg-primary-100 rounded">
                        <i class="fas fa-fw fa-sm fa-barcode text-primary"></i>
                    </span>
                    </div>
                </div>

                <div class="mt-2 text-break">
                    <a href="<?= url('admin/barcodes') ?>" class="stretched-link text-reset text-decoration-none">
                        <span class="h4" id="barcodes">
                            <span class="spinner-border spinner-border-sm" role="status"></span>
                        </span>
                    </a>

                    <div class="mt-1 small text-muted">
                        <span id="barcodes_current_month">
                            <span class="spinner-border spinner-border-sm" role="status"></span>
                        </span>
                        <?= mb_strtolower(l('global.date.this_month')) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3 p-3 position-relative">
        <div class="card d-flex flex-row h-100 overflow-hidden">
            <div class="card-body">
                <div class="row">
                    <div class="col text-truncate">
                        <small class="text-muted font-weight-bold"><?= l('admin_links.menu') ?></small>
                    </div>

                    <div class="col-auto">
                    <span class="p-2 bg-primary-100 rounded">
                        <i class="fas fa-fw fa-sm fa-link text-primary"></i>
                    </span>
                    </div>
                </div>

                <div class="mt-2 text-break">
                    <a href="<?= url('admin/links') ?>" class="stretched-link text-reset text-decoration-none">
                        <span class="h4" id="links">
                            <span class="spinner-border spinner-border-sm" role="status"></span>
                        </span>
                    </a>

                    <div class="mt-1 small text-muted">
                        <span id="links_current_month">
                            <span class="spinner-border spinner-border-sm" role="status"></span>
                        </span>
                        <?= mb_strtolower(l('global.date.this_month')) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3 p-3 position-relative">
        <div class="card d-flex flex-row h-100 overflow-hidden">
            <div class="card-body">
                <div class="row">
                    <div class="col text-truncate">
                        <small class="text-muted font-weight-bold"><?= l('admin_domains.menu') ?></small>
                    </div>

                    <div class="col-auto">
                        <span class="p-2 bg-primary-100 rounded">
                            <i class="fas fa-fw fa-sm fa-globe text-primary"></i>
                        </span>
                    </div>
                </div>

                <div class="mt-2 text-break">
                    <a href="<?= url('admin/domains') ?>" class="stretched-link text-reset text-decoration-none">
                        <span class="h4" id="domains">
                            <span class="spinner-border spinner-border-sm" role="status"></span>
                        </span>
                    </a>

                    <div class="mt-1 small text-muted">
                        <span id="domains_current_month">
                            <span class="spinner-border spinner-border-sm" role="status"></span>
                        </span>
                        <?= mb_strtolower(l('global.date.this_month')) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3 p-3 position-relative">
        <div class="card d-flex flex-row h-100 overflow-hidden">
            <div class="card-body">
                <div class="row">
                    <div class="col text-truncate">
                        <small class="text-muted font-weight-bold"><?= l('admin_users.menu') ?></small>
                    </div>

                    <div class="col-auto">
                        <span class="p-2 bg-primary-100 rounded">
                            <i class="fas fa-fw fa-sm fa-users text-primary"></i>
                        </span>
                    </div>
                </div>

                <div class="mt-2 text-break">
                    <a href="<?= url('admin/users') ?>" class="stretched-link text-reset text-decoration-none">
                        <span class="h4" id="users">
                            <span class="spinner-border spinner-border-sm" role="status"></span>
                        </span>
                    </a>

                    <div class="mt-1 small text-muted">
                        <span id="users_current_month">
                            <span class="spinner-border spinner-border-sm" role="status"></span>
                        </span>
                        <?= mb_strtolower(l('global.date.this_month')) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3 p-3 position-relative">
        <div class="card d-flex flex-row h-100 overflow-hidden">
            <div class="card-body">
                <div class="row">
                    <div class="col text-truncate">
                        <small class="text-muted font-weight-bold"><?= l('admin_payments.menu') ?></small>
                    </div>

                    <div class="col-auto">
                    <span class="p-2 bg-primary-100 rounded">
                        <i class="fas fa-fw fa-sm fa-funnel-dollar text-primary"></i>
                    </span>
                    </div>
                </div>

                <div class="mt-2 text-break">
                    <a href="<?= in_array(settings()->license->type, ['Extended License', 'extended']) ? url('admin/payments') : url('admin/settings/payment') ?>" class="stretched-link text-reset text-decoration-none">
                        <span class="h4" id="payments">
                            <span class="spinner-border spinner-border-sm" role="status"></span>
                        </span>
                    </a>

                    <div class="mt-1 small text-muted">
                        <span id="payments_current_month">
                            <span class="spinner-border spinner-border-sm" role="status"></span>
                        </span>
                        <?= mb_strtolower(l('global.date.this_month')) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3 p-3 position-relative">
        <div class="card d-flex flex-row h-100 overflow-hidden">
            <div class="card-body">
                <div class="row">
                    <div class="col text-truncate">
                        <small class="text-muted font-weight-bold"><?= l('admin_index.payments_total_amount') ?></small>
                    </div>

                    <div class="col-auto">
                        <span class="p-2 bg-primary-100 rounded">
                            <i class="fas fa-fw fa-sm fa-credit-card text-primary"></i>
                        </span>
                    </div>
                </div>

                <div class="mt-2 text-break">
                    <a href="<?= in_array(settings()->license->type, ['Extended License', 'extended']) ? url('admin/payments') : url('admin/settings/payment') ?>" class="stretched-link text-reset text-decoration-none">
                        <span class="h4" id="payments_total_amount">
                            <span class="spinner-border spinner-border-sm" role="status"></span>
                        </span>
                        <small><?= settings()->payment->default_currency ?></small>
                    </a>

                    <div class="mt-1 small text-muted">
                        <span id="payments_amount_current_month">
                            <span class="spinner-border spinner-border-sm" role="status"></span>
                        </span>
                        <?= settings()->payment->default_currency ?> <?= mb_strtolower(l('global.date.this_month')) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mb-5">
    <div class="d-flex flex-column flex-md-row justify-content-between mb-4">
        <h1 class="h3 mb-3 mb-md-0 text-truncate"><i class="fas fa-fw fa-xs fa-users text-primary-900 mr-2"></i> <?= l('admin_index.users') ?></h1>

        <div>
            <span class="badge badge-success" data-toggle="tooltip" title="<?= l('admin_index.active_users_tooltip') ?>">
                <i class="fas fa-xs fa-fw fa-circle fa-fade mr-1"></i>
                <span id="active_users" data-translation="<?= l('admin_index.active_users') ?>"><?= l('global.loading') ?></span>
            </span>
        </div>
    </div>

    <?php $result = database()->query("SELECT * FROM `users` ORDER BY `user_id` DESC LIMIT 5"); ?>
    <div class="table-responsive table-custom-container">
        <table class="table table-custom">
            <thead>
            <tr>
                <th><?= l('global.user') ?></th>
                <th><?= l('global.status') ?></th>
                <th><?= l('admin_users.plan_id') ?></th>
                <th><?= l('global.details') ?></th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php while($row = $result->fetch_object()): ?>
                <?php //ALTUMCODE:DEMO if(DEMO) {$row->email = 'hidden@demo.com'; $row->name = 'hidden on demo';} ?>
                <?php if(!isset($data->plans[$row->plan_id])) $data->plans[$row->plan_id] = (new \Altum\Models\Plan())->get_plan_by_id($row->plan_id) ?>
                <tr>
                    <td class="text-nowrap">
                        <div class="d-flex">
                            <a href="<?= url('admin/user-view/' . $row->user_id) ?>">
                                <img src="<?= get_user_avatar($row->avatar, $row->email) ?>" class="user-avatar rounded-circle mr-3" alt="" />
                            </a>

                            <div class="d-flex flex-column">
                                <div>
                                    <a href="<?= url('admin/user-view/' . $row->user_id) ?>" <?= $row->type == 1 ? 'class="font-weight-bold" data-toggle="tooltip" title="' . l('admin_users.type_admin') . '"' : null ?>><?= $row->name ?></a>
                                </div>

                                <span class="small text-muted"><?= $row->email ?></span>
                            </div>
                        </div>
                    </td>
                    <td class="text-nowrap">
                        <?php if($row->status == 0): ?>
                            <a href="<?= url('admin/users?status=0') ?>" class="badge badge-warning"><i class="fas fa-fw fa-sm fa-eye-slash mr-1"></i> <?= l('admin_users.status_unconfirmed') ?></a>
                        <?php elseif($row->status == 1): ?>
                            <a href="<?= url('admin/users?status=1') ?>" class="badge badge-success"><i class="fas fa-fw fa-sm fa-check mr-1"></i> <?= l('admin_users.status_active') ?></a>
                        <?php elseif($row->status == 2): ?>
                            <a href="<?= url('admin/users?status=2') ?>" class="badge badge-light"><i class="fas fa-fw fa-sm fa-times mr-1"></i> <?= l('admin_users.status_disabled') ?></a>
                        <?php endif ?>
                    </td>
                    <td class="text-nowrap">
                        <div class="d-flex flex-column">
                            <div>
                                <a href="<?= url('admin/plan-update/' . $row->plan_id) ?>" class="badge badge-light"><?= $data->plans[$row->plan_id]->name ?></a>
                            </div>

                            <?php if($row->plan_id != 'free'): ?>
                                <div>
                                    <small class="text-muted" data-toggle="tooltip" title="<?= l('admin_users.plan_expiration_date') ?>"><?= \Altum\Date::get($row->plan_expiration_date, 1) ?></small>
                                </div>
                            <?php endif ?>
                        </div>
                    </td>
                    <td class="text-nowrap">
                        <div class="d-flex align-items-center">
                            <span class="mr-2" data-toggle="tooltip" data-html="true" title="<?= l('admin_users.datetime') . '<br />' . \Altum\Date::get($row->datetime, 2) . '<br /><small>' . \Altum\Date::get($row->datetime, 3) . '</small>' . '<br /><small>(' . \Altum\Date::get_timeago($row->datetime) . ')</small>' ?>">
                                <i class="fas fa-fw fa-calendar text-muted"></i>
                            </span>

                            <a href="<?= url('admin/users?source=' . $row->source) ?>" class="mr-2" data-toggle="tooltip" title="<?= l('admin_users.source.' . $row->source) ?>">
                                <i class="fas fa-fw fa-sign-in-alt text-muted"></i>
                            </a>

                            <span class="mr-2" data-toggle="tooltip" data-html="true" title="<?= l('admin_users.last_activity') . '<br />' . \Altum\Date::get($row->last_activity, 2) . '<br /><small>' . \Altum\Date::get($row->last_activity, 3) . '</small>' . '<br /><small>(' . \Altum\Date::get_timeago($row->last_activity) . ')</small>' ?>">
                                <i class="fas fa-fw fa-history text-muted"></i>
                            </span>

                            <span class="mr-2" data-toggle="tooltip" title="<?= sprintf(l('admin_users.table.total_logins'), nr($row->total_logins)) ?>">
                                <i class="fas fa-fw fa-user-clock text-muted"></i>
                            </span>

                            <a href="<?= url('admin/users?continent_code=' . $row->continent_code) ?>" class="mr-2" data-toggle="tooltip" title="<?= get_continent_from_continent_code($row->continent_code ?? l('global.unknown')) ?>">
                                <i class="fas fa-fw fa-globe-europe text-muted"></i>
                            </a>

                            <a href="<?= url('admin/users?country=' . $row->country) ?>">
                                <?php if($row->country): ?>
                                    <img src="<?= ASSETS_FULL_URL . 'images/countries/' . mb_strtolower($row->country) . '.svg' ?>" class="icon-favicon mr-2" data-toggle="tooltip" title="<?= get_country_from_country_code($row->country) ?>" />
                                <?php else: ?>
                                    <span class="mr-2" data-toggle="tooltip" title="<?= l('global.unknown') ?>">
                                    <i class="fas fa-fw fa-flag text-muted"></i>
                                </span>
                                <?php endif ?>
                            </a>

                            <a href="<?= url('admin/users?city_name=' . $row->city_name) ?>" class="mr-2" data-toggle="tooltip" title="<?= $row->city_name ?? l('global.unknown') ?>">
                                <i class="fas fa-fw fa-city text-muted"></i>
                            </a>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex justify-content-end">
                            <?= include_view(THEME_PATH . 'views/admin/users/admin_user_dropdown_button.php', ['id' => $row->user_id, 'resource_name' => $row->name]) ?>
                        </div>
                    </td>
                </tr>
            <?php endwhile ?>

            <tr>
                <td colspan="5">
                    <a href="<?= url('admin/users') ?>" class="text-muted text-decoration-none small">
                        <i class="fas fa-angle-right fa-sm fa-fw mr-1"></i> <?= l('global.view_more') ?>
                    </a>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>

<?php if(settings()->internal_notifications->admins_is_enabled): ?>
    <?php if($data->internal_notifications): ?>
        <h1 class="h3 mb-4"><i class="fas fa-fw fa-xs fa-bell text-primary-900 mr-2"></i> <?= l('admin_index.admins_notifications') ?></h1>

        <div class="card mb-5">
            <div class="card-body py-2">
                <div>
                    <?php foreach($data->internal_notifications as $notification): ?>
                        <?php //ALTUMCODE:DEMO if(DEMO) {$notification->title = $notification->description = 'hidden on demo';} ?>

                        <div class="bg-gray-100 p-3 my-3 rounded <?= $notification->is_read ? null : 'border border-info' ?> position-relative">
                            <div class="d-flex align-items-center">
                                <div class="p-3 bg-gray-50 mr-3 rounded">
                                    <i class="<?= $notification->icon ?> fa-fw fa-lg text-primary-900"></i>
                                </div>

                                <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-lg-between flex-fill">
                                    <div class="d-flex flex-column">
                                        <div class="font-weight-bold mb-1">
                                            <?php if($notification->url): ?>
                                                <a href="<?= $notification->url ?>" class="stretched-link text-decoration-none text-body"><?= $notification->title ?></a>
                                            <?php else: ?>
                                                <?= $notification->title ?>
                                            <?php endif ?>
                                        </div>

                                        <small class="text-muted"><?= $notification->description ?></small>
                                    </div>

                                    <div>
                                        <small class="text-muted" data-toggle="tooltip" title="<?= \Altum\Date::get($notification->datetime, 1) ?>"><?= \Altum\Date::get_timeago($notification->datetime) ?></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach ?>
                </div>
            </div>
        </div>
    <?php endif ?>
<?php endif ?>


<?php if(in_array(settings()->license->type, ['SPECIAL', 'Extended License', 'extended'])): ?>
    <?php $result = database()->query("SELECT `payments`.*, `users`.`name` AS `user_name`, `users`.`email` AS `user_email`, `users`.`avatar` AS `user_avatar` FROM `payments` LEFT JOIN `users` ON `payments`.`user_id` = `users`.`user_id` ORDER BY `id` DESC LIMIT 5"); ?>

    <?php if($result->num_rows): ?>
        <div class="mb-5">
            <h1 class="h3 mb-4"><i class="fas fa-fw fa-xs fa-credit-card text-primary-900 mr-2"></i> <?= l('admin_index.payments') ?></h1>

            <div class="table-responsive table-custom-container">
                <table class="table table-custom">
                    <thead>
                    <tr>
                        <th><?= l('global.user') ?></th>
                        <th><?= l('admin_payments.plan') ?></th>
                        <th><?= l('admin_payments.total_amount') ?></th>
                        <th><?= l('global.type') ?></th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php while($row = $result->fetch_object()): ?>
                        <?php //ALTUMCODE:DEMO if(DEMO) {$row->email = $row->user_email = 'hidden@demo.com'; $row->user_name = $row->name = 'hidden on demo';} ?>
                        <?php $row->taxes_ids = json_decode($row->taxes_ids ?? ''); ?>

                        <tr>
                            <td class="text-nowrap">
                                <div class="d-flex align-items-center">
                                    <?php if($row->user_name || $row->user_email): ?>
                                        <a href="<?= url('admin/user-view/' . $row->user_id) ?>">
                                            <img src="<?= get_user_avatar($row->user_avatar, $row->user_email) ?>" referrerpolicy="no-referrer" loading="lazy" class="user-avatar rounded-circle mr-3" alt="" />
                                        </a>

                                        <div class="d-flex flex-column">
                                            <div>
                                                <a href="<?= url('admin/user-view/' . $row->user_id) ?>"><?= $row->user_name ?></a>
                                            </div>

                                            <span class="text-muted small"><?= $row->user_email ?></span>
                                        </div>
                                    <?php else: ?>
                                        <img src="<?= get_user_avatar($row->user_avatar, $row->user_email) ?>" referrerpolicy="no-referrer" loading="lazy" class="user-avatar rounded-circle mr-3" alt="" />

                                        <div class="text-muted">
                                            <?= l('global.unknown') ?>
                                        </div>
                                    <?php endif ?>
                                </div>
                            </td>

                            <td class="text-nowrap">
                                <?php if(isset($data->plans[$row->plan_id])): ?>
                                    <a href="<?= url('admin/plan-update/' . $row->plan_id) ?>" class="badge badge-light">
                                        <?= $data->plans[$row->plan_id]->name ?>
                                    </a>
                                <?php else: ?>
                                    <span class="badge badge-light"><?= $row->plan->name ?? l('global.unknown') ?></span>
                                <?php endif ?>
                            </td>

                            <td class="text-nowrap">
                                <span class="badge badge-success"><?= nr($row->total_amount, 2) . ' ' . $row->currency ?></span>
                            </td>

                            <td class="text-nowrap">
                                <div class="d-flex flex-column">
                                    <span><?= l('pay.custom_plan.' . $row->type . '_type') ?></span>

                                    <div>
                                        <span class="small text-muted"><?= l('pay.custom_plan.' . $row->frequency) ?></span>
                                    </div>
                                </div>
                            </td>

                            <td class="text-nowrap">
                                <a href="<?= url('admin/payments?processor=' . $row->processor) ?>" class="badge badge-light">
                                    <i class="<?= $data->payment_processors[$row->processor]['icon'] ?> fa-fw fa-sm mr-1" style="color: <?= $data->payment_processors[$row->processor]['color'] ?>"></i>
                                    <?= l('pay.custom_plan.' . $row->processor) ?>
                                </a>
                            </td>

                            <td class="text-nowrap">
                                <span class="mr-2 <?= $row->code ? null : 'opacity-0' ?>" data-toggle="tooltip" title="<?= $row->code ? $row->code . ' (-' . nr($row->discount_amount, 2) . ' ' . $row->currency . ')' : null ?>">
                                    <i class="fas fa-fw fa-sm fa-tag text-muted"></i>
                                </span>

                                <?php
                                $taxes_html = null;
                                if(count($row->taxes_ids ?? [])) {
                                    $taxes_html = l('admin_taxes.menu') . ': ';
                                    foreach($row->taxes_ids as $tax_id) {
                                        $taxes_html .= '<a href=\'' . url('admin/tax-update/' . $tax_id) . '\' target=\'_blank\' class=\'mr-1\'>' . $tax_id . '</a>';
                                    }
                                }
                                ?>
                                <a href="#" onclick="return false;" class="mr-2 text-decoration-none <?= $taxes_html ? null : 'opacity-0' ?>" data-toggle="popover" data-placement="top" data-container="body" data-html="true" data-content="<?= $taxes_html ?>">
                                    <i class="fas fa-fw fa-sm fa-paperclip text-muted"></i>
                                </a>

                                <span class="mr-2" data-toggle="tooltip" data-html="true" title="<?= sprintf(l('global.datetime_tooltip'), '<br />' . \Altum\Date::get($row->datetime, 2) . '<br /><small>' . \Altum\Date::get($row->datetime, 3) . '</small>' . '<br /><small>(' . \Altum\Date::get_timeago($row->datetime) . ')</small>') ?>">
                                    <i class="fas fa-fw fa-calendar text-muted"></i>
                                </span>
                            </td>
                            <td>
                                <div class="d-flex justify-content-end">
                                    <?= include_view(THEME_PATH . 'views/admin/payments/admin_payment_dropdown_button.php', [
                                        'id' => $row->id,
                                        'payment_proof' => $row->payment_proof,
                                        'processor' => $row->processor,
                                        'status' => $row->status
                                    ]) ?>
                                </div>
                            </td>
                        </tr>

                    <?php endwhile ?>

                    <tr>
                        <td colspan="6">
                            <a href="<?= url('admin/payments') ?>" class="text-muted text-decoration-none small">
                                <i class="fas fa-angle-right fa-sm fa-fw mr-1"></i> <?= l('global.view_more') ?>
                            </a>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif ?>
<?php endif ?>

<div class="row justify-content-between">
    <div class="col-12 col-sm-6 col-xl-4 p-3 position-relative">
        <div class="card d-flex flex-row h-100 overflow-hidden">
            <div class="card-body text-truncate">
                <small class="text-muted"><i class="fas fa-fw fa-sm fa-code mr-1"></i> <?= PRODUCT_NAME ?></small>

                <div class="mt-2"><span class="h6"><?= 'v' . PRODUCT_VERSION ?></span></div>
            </div>

            <div class="pr-4 d-flex flex-column justify-content-center">
                <a href="<?= PRODUCT_URL ?>" class="stretched-link">
                    <i class="fas fa-fw fa-angle-right text-gray-500"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-4 p-3 position-relative">
        <div class="card d-flex flex-row h-100 overflow-hidden">
            <div class="card-body text-truncate">
                <small class="text-muted"><i class="fas fa-fw fa-sm fa-book mr-1"></i> Read documentation</small>

                <div class="mt-2"><span class="h6">Docs</span></div>
            </div>

            <div class="pr-4 d-flex flex-column justify-content-center">
                <a href="<?= PRODUCT_DOCUMENTATION_URL ?>" class="stretched-link" target="_blank">
                    <i class="fas fa-fw fa-angle-right text-gray-500"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-4 p-3 position-relative">
        <div class="card d-flex flex-row h-100 overflow-hidden">
            <div class="card-body text-truncate">
                <small class="text-muted"><i class="fas fa-fw fa-sm fa-history mr-1"></i> Read changelog</small>

                <div class="mt-2"><span class="h6">Changelog</span></div>
            </div>

            <div class="pr-4 d-flex flex-column justify-content-center">
                <a href="<?= PRODUCT_CHANGELOG_URL ?>" class="stretched-link" target="_blank">
                    <i class="fas fa-fw fa-angle-right text-gray-500"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-4 p-3 position-relative">
        <div class="card d-flex flex-row h-100 overflow-hidden">
            <div class="card-body text-truncate">
                <small class="text-muted"><i class="fas fa-fw fa-sm fa-globe mr-1"></i> Official website</small>

                <div class="mt-2"><span class="h6">altumcode.com</span></div>
            </div>

            <div class="pr-4 d-flex flex-column justify-content-center">
                <a href="https://altumco.de/site" class="stretched-link" target="_blank">
                    <i class="fas fa-fw fa-angle-right text-gray-500"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-4 p-3 position-relative">
        <div class="card d-flex flex-row h-100 overflow-hidden">
            <div class="card-body text-truncate">
                <small class="text-muted"><i class="fas fa-fw fa-sm fa-envelope mr-1"></i> Get support</small>

                <div class="mt-2"><span class="h6">support@altumcode.com</span></div>
            </div>

            <div class="pr-4 d-flex flex-column justify-content-center">
                <a href="https://altumcode.com/contact" class="stretched-link" target="_blank">
                    <i class="fas fa-fw fa-angle-right text-gray-500"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-4 p-3 position-relative">
        <div class="card d-flex flex-row h-100 overflow-hidden">
            <div class="card-body text-truncate">
                <small class="text-muted"><i class="fab fa-fw fa-sm fa-twitter mr-1"></i> X</small>

                <div class="mt-2"><span class="h6">@altumcode</span></div>
            </div>

            <div class="pr-4 d-flex flex-column justify-content-center">
                <a href="https://altumco.de/twitter" class="stretched-link" target="_blank">
                    <i class="fas fa-fw fa-angle-right text-gray-500"></i>
                </a>
            </div>
        </div>
    </div>
</div>


<?php ob_start() ?>
    <script>
    'use strict';

        (async function fetch_statistics() {
            /* Send request to server */
            let response = await fetch(`${url}admin/index/get_stats_ajax`, {
                method: 'get',
            });

            let data = null;
            try {
                data = await response.json();
            } catch (error) {
                /* :)  */
            }


            if(!response.ok) {
                /* :)  */
            }

            if(data.status == 'error') {
                /* :)  */
            } else if(data.status == 'success') {

                document.querySelector('#ai_qr_codes').innerHTML = data.details.ai_qr_codes ? nr(data.details.ai_qr_codes) : 0;
                document.querySelector('#ai_qr_codes_current_month').innerHTML = data.details.ai_qr_codes_current_month ? nr(data.details.ai_qr_codes_current_month) : 0;

                document.querySelector('#qr_codes').innerHTML = data.details.qr_codes ? nr(data.details.qr_codes) : 0;
                document.querySelector('#qr_codes_current_month').innerHTML = data.details.qr_codes_current_month ? nr(data.details.qr_codes_current_month) : 0;

                document.querySelector('#links').innerHTML = data.details.links ? nr(data.details.links) : 0;
                document.querySelector('#links_current_month').innerHTML = data.details.links_current_month ? nr(data.details.links_current_month) : 0;

                document.querySelector('#barcodes').innerHTML = data.details.barcodes ? nr(data.details.barcodes) : 0;
                document.querySelector('#barcodes_current_month').innerHTML = data.details.barcodes_current_month ? nr(data.details.barcodes_current_month) : 0;

                document.querySelector('#domains').innerHTML = data.details.domains ? nr(data.details.domains) : 0;
                document.querySelector('#domains_current_month').innerHTML = data.details.domains_current_month ? nr(data.details.domains_current_month) : 0;

                document.querySelector('#payments_total_amount').innerHTML = data.details.payments_total_amount ? nr(data.details.payments_total_amount) : 0;
                document.querySelector('#users_current_month').innerHTML = data.details.users_current_month ? nr(data.details.users_current_month) : 0;

                document.querySelector('#users').innerHTML = data.details.users ? nr(data.details.users) : 0;
                document.querySelector('#payments_current_month').innerHTML = data.details.payments_current_month ? nr(data.details.payments_current_month) : 0;

                document.querySelector('#payments').innerHTML = data.details.payments ? nr(data.details.payments) : 0;
                document.querySelector('#payments_amount_current_month').innerHTML = data.details.payments_amount_current_month ? nr(data.details.payments_amount_current_month) : 0;

                let active_users = data.details.active_users ? nr(data.details.active_users) : 0;
                document.querySelector('#active_users').innerHTML = document.querySelector('#active_users').getAttribute('data-translation').replace('%s', active_users);

            }
        })();
    </script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
