<?php defined('ALTUMCODE') || die() ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div class="d-flex align-items-center">
        <h3 class="h5 text-truncate m-0"><?= l('link_statistics.entries') ?></h3>

        <div class="ml-2">
            <span data-toggle="tooltip" title="<?= l('link_statistics.entries_help') ?>">
                <i class="fas fa-fw fa-info-circle text-muted"></i>
            </span>
        </div>
    </div>

    <div class="d-flex align-items-center col-auto p-0">
        <div class="dropdown">
            <button type="button" class="btn btn-light dropdown-toggle-simple" data-toggle="dropdown" data-boundary="viewport" data-tooltip title="<?= l('global.export') ?>" data-tooltip-hide-on-click>
                <i class="fas fa-fw fa-sm fa-download"></i>
            </button>

            <div class="dropdown-menu dropdown-menu-right d-print-none">
                <a href="<?= url('link-statistics/' . $data->link->link_id . '?' . \Altum\Router::$original_request_query . '&export=csv') ?>" target="_blank" class="dropdown-item">
                    <i class="fas fa-fw fa-sm fa-file-csv mr-2"></i> <?= sprintf(l('global.export_to'), 'CSV') ?>
                </a>
                <a href="<?= url('link-statistics/' . $data->link->link_id . '?' . \Altum\Router::$original_request_query . '&export=json') ?>" target="_blank" class="dropdown-item <?= $this->user->plan_settings->export->json ? null : 'disabled pointer-events-all' ?>" <?= $this->user->plan_settings->export->json ? null : get_plan_feature_disabled_info() ?>>
                    <i class="fas fa-fw fa-sm fa-file-code mr-2"></i> <?= sprintf(l('global.export_to'), 'JSON') ?>
                </a>
                <a href="#" class="dropdown-item <?= $this->user->plan_settings->export->pdf ? null : 'disabled pointer-events-all' ?>" <?= $this->user->plan_settings->export->pdf ? $this->user->plan_settings->export->pdf ? 'onclick="event.preventDefault(); window.print();"' : 'disabled pointer-events-all' : get_plan_feature_disabled_info() ?>>
                    <i class="fas fa-fw fa-sm fa-file-pdf mr-2"></i> <?= sprintf(l('global.export_to'), 'PDF') ?>
                </a>
            </div>
        </div>
    </div>
</div>

<?php if(!count($data->rows)): ?>
    <div class="card my-3">
        <div class="card-body">
            <?= include_view(THEME_PATH . 'views/partials/no_data.php', [
                'filters_get' => $data->filters->get ?? [],
                'name' => 'global',
                'has_secondary_text' => false,
                'has_wrapper' => false,
            ]); ?>
        </div>
    </div>
<?php else: ?>

    <div class="table-responsive table-custom-container">
        <table class="table table-custom">
            <thead>
            <tr>
                <th class="">
                    <div><?= l('global.country') ?></div>
                    <div><?= l('global.city') ?></div>
                </th>
                <th class=""><?= l('link_statistics.table.device') ?></th>
                <th class="">
                    <div><?= l('link_statistics.table.os') ?></div>
                    <div><?= l('link_statistics.table.browser') ?></div>
                </th>
                <th class=""><?= l('link_statistics.table.referrer') ?></th>
                <th class=""><?= l('global.datetime') ?></th>
            </tr>
            </thead>

            <tbody>
            <?php foreach($data->rows as $row): ?>
                <tr>
                    <td class="text-nowrap">
                        <div class="d-flex align-items-center">
                            <div class="table-image-wrapper mr-3">
                                <img src="<?= ASSETS_FULL_URL . 'images/countries/' . ($row->country_code ? mb_strtolower($row->country_code) : 'unknown') . '.svg' ?>" class="img-fluid icon-favicon" />
                            </div>

                            <div class="d-flex flex-column">
                                <span class=""><?= $row->country_code ? get_country_from_country_code($row->country_code) : l('global.unknown') ?></span>
                                <span class="text-muted small"><?= $row->city_name ?? l('global.unknown') ?></span>
                            </div>
                        </div>
                    </td>

                    <td class="text-nowrap">
                        <span class="badge badge-light">
                            <?= $row->device_type ? '<i class="fas fa-fw fa-sm fa-' . $row->device_type . ' mr-1"></i>' . l('global.device.' . $row->device_type) : l('global.unknown') ?>
                        </span>
                    </td>

                    <td class="text-nowrap">
                        <div>
                            <img src="<?= ASSETS_FULL_URL . 'images/os/' . os_name_to_os_key($row->os_name) . '.svg' ?>" class="img-fluid icon-favicon-small mr-1" />
                            <span class="font-size-small"><?= $row->os_name ?: l('global.unknown') ?></span>
                        </div>
                        <div>
                            <img src="<?= ASSETS_FULL_URL . 'images/browsers/' . browser_name_to_browser_key($row->browser_name) . '.svg' ?>" class="img-fluid icon-favicon-small mr-1" />
                            <span class="font-size-small"><?= $row->browser_name ?: l('global.unknown') ?></span>
                        </div>
                    </td>

                    <td class="text-nowrap">
                        <?php if(!$row->referrer_host): ?>
                            <span><?= l('link_statistics.referrer_direct') ?></span>
                        <?php elseif($row->referrer_host == 'qr'): ?>
                            <span><?= l('link_statistics.referrer_qr') ?></span>
                        <?php else: ?>
                            <img referrerpolicy="no-referrer" src="<?= get_favicon_url_from_domain($row->referrer_host) ?>" class="img-fluid icon-favicon mr-1" loading="lazy" />
                            <a href="<?= url('link-statistics/' . $data->link->link_id . '?type=referrer_path&referrer_host=' . $row->referrer_host . '&start_date=' . $data->datetime['start_date'] . '&end_date=' . $data->datetime['end_date']) ?>" title="<?= $row->referrer_host ?>" class=""><?= $row->referrer_host ?></a>
                            <a href="<?= 'https://' . $row->referrer_host ?>" target="_blank" rel="nofollow noopener" class="text-muted ml-1"><i class="fas fa-fw fa-xs fa-external-link-alt"></i></a>
                        <?php endif ?>
                    </td>

                    <td class="text-nowrap">
                        <span class="text-muted" data-toggle="tooltip" title="<?= \Altum\Date::get($row->datetime, 1) ?>"><?= \Altum\Date::get_timeago($row->datetime) ?></span>
                    </td>
                </tr>
            <?php endforeach ?>

            </tbody>
        </table>
    </div>

    <div class="mt-3"><?= $data->pagination ?></div>

<?php endif ?>
