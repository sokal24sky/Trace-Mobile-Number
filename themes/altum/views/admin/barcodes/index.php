<?php defined('ALTUMCODE') || die() ?>

<?php if(!settings()->codes->barcodes_is_enabled): ?>
    <div class="alert alert-info">
        <i class="fas fa-fw fa-info-circle mr-1"></i>
        <?= sprintf(l('global.info_message.admin_feature_disabled'), url('admin/settings/codes')) ?>
    </div>
<?php endif ?>

<div class="d-flex flex-column flex-md-row justify-content-between mb-4">
    <h1 class="h3 mb-3 mb-md-0 text-truncate"><i class="fas fa-fw fa-xs fa-barcode text-primary-900 mr-2"></i> <?= l('admin_barcodes.header') ?></h1>

    <div class="d-flex position-relative d-print-none">
        <div class="ml-3">
            <a href="<?= url('admin/statistics/barcodes') ?>" class="btn btn-gray-300" data-tooltip title="<?= l('global.statistics') ?>">
                <i class="fas fa-fw fa-sm fa-chart-bar"></i>
            </a>
        </div>

        <div class="ml-3">
            <div class="dropdown">
                <button type="button" class="btn btn-gray-300 dropdown-toggle-simple" data-toggle="dropdown" data-boundary="viewport" data-tooltip title="<?= l('global.export') ?>" data-tooltip-hide-on-click>
                    <i class="fas fa-fw fa-sm fa-download"></i>
                </button>

                <div class="dropdown-menu dropdown-menu-right d-print-none">
                    <a href="<?= url('admin/barcodes?' . $data->filters->get_get() . '&export=csv') ?>" target="_blank" class="dropdown-item <?= $this->user->plan_settings->export->csv ? null : 'disabled pointer-events-all' ?>" <?= $this->user->plan_settings->export->csv ? null : get_plan_feature_disabled_info() ?>>
                        <i class="fas fa-fw fa-sm fa-file-csv mr-2"></i> <?= sprintf(l('global.export_to'), 'CSV') ?>
                    </a>
                    <a href="<?= url('admin/barcodes?' . $data->filters->get_get() . '&export=json') ?>" target="_blank" class="dropdown-item <?= $this->user->plan_settings->export->json ? null : 'disabled pointer-events-all' ?>" <?= $this->user->plan_settings->export->json ? null : get_plan_feature_disabled_info() ?>>
                        <i class="fas fa-fw fa-sm fa-file-code mr-2"></i> <?= sprintf(l('global.export_to'), 'JSON') ?>
                    </a>
                    <a href="#" class="dropdown-item <?= $this->user->plan_settings->export->pdf ? null : 'disabled pointer-events-all' ?>" <?= $this->user->plan_settings->export->pdf ? $this->user->plan_settings->export->pdf ? 'onclick="event.preventDefault(); window.print();"' : 'disabled pointer-events-all' : get_plan_feature_disabled_info() ?>>
                        <i class="fas fa-fw fa-sm fa-file-pdf mr-2"></i> <?= sprintf(l('global.export_to'), 'PDF') ?>
                    </a>
                </div>
            </div>
        </div>

        <div class="ml-3">
            <div class="dropdown">
                <button type="button" class="btn <?= $data->filters->has_applied_filters ? 'btn-secondary' : 'btn-gray-300' ?> filters-button dropdown-toggle-simple" data-toggle="dropdown" data-boundary="viewport" data-tooltip data-html="true" title="<?= l('global.filters.tooltip') ?>" data-tooltip-hide-on-click>
                    <i class="fas fa-fw fa-sm fa-filter"></i>
                </button>

                <div class="dropdown-menu dropdown-menu-right filters-dropdown">
                    <div class="dropdown-header d-flex justify-content-between">
                        <span class="h6 m-0"><?= l('global.filters.header') ?></span>

                        <?php if($data->filters->has_applied_filters): ?>
                            <a href="<?= url(\Altum\Router::$original_request) ?>" class="text-muted"><?= l('global.filters.reset') ?></a>
                        <?php endif ?>
                    </div>

                    <div class="dropdown-divider"></div>

                    <form action="" method="get" role="form">
                        <div class="form-group px-4">
                            <label for="filters_search" class="small"><?= l('global.filters.search') ?></label>
                            <input type="search" name="search" id="filters_search" class="form-control form-control-sm" value="<?= $data->filters->search ?>" />
                        </div>

                        <div class="form-group px-4">
                            <label for="filters_search_by" class="small"><?= l('global.filters.search_by') ?></label>
                            <select name="search_by" id="filters_search_by" class="custom-select custom-select-sm">
                                <option value="name" <?= $data->filters->search_by == 'name' ? 'selected="selected"' : null ?>><?= l('global.name') ?></option>
                                <option value="value" <?= $data->filters->search_by == 'value' ? 'selected="selected"' : null ?>><?= l('barcodes.input.value') ?></option>
                            </select>
                        </div>

                        <div class="form-group px-4">
                            <label for="filters_type" class="small"><?= l('global.type') ?></label>
                            <select name="type" id="filters_type" class="custom-select custom-select-sm">
                                <option value=""><?= l('global.all') ?></option>
                                <?php foreach(array_keys((require APP_PATH . 'includes/barcodes.php')) as $type): ?>
                                    <option value="<?= $type ?>" <?= isset($data->filters->filters['type']) && $data->filters->filters['type'] == $type ? 'selected="selected"' : null ?>><?= $data->available_barcodes[$type]['emoji'] . ' ' . l('barcodes.type.' . $type) ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>

                        <div class="form-group px-4">
                            <label for="filters_order_by" class="small"><?= l('global.filters.order_by') ?></label>
                            <select name="order_by" id="filters_order_by" class="custom-select custom-select-sm">
                                <option value="barcode_id" <?= $data->filters->order_by == 'barcode_id' ? 'selected="selected"' : null ?>><?= l('global.id') ?></option>
                                <option value="datetime" <?= $data->filters->order_by == 'datetime' ? 'selected="selected"' : null ?>><?= l('global.filters.order_by_datetime') ?></option>
                                <option value="last_datetime" <?= $data->filters->order_by == 'last_datetime' ? 'selected="selected"' : null ?>><?= l('global.filters.order_by_last_datetime') ?></option>
                                <option value="name" <?= $data->filters->order_by == 'name' ? 'selected="selected"' : null ?>><?= l('global.name') ?></option>
                                <option value="type" <?= $data->filters->order_by == 'type' ? 'selected="selected"' : null ?>><?= l('global.type') ?></option>
                            </select>
                        </div>

                        <div class="form-group px-4">
                            <label for="filters_order_type" class="small"><?= l('global.filters.order_type') ?></label>
                            <select name="order_type" id="filters_order_type" class="custom-select custom-select-sm">
                                <option value="ASC" <?= $data->filters->order_type == 'ASC' ? 'selected="selected"' : null ?>><?= l('global.filters.order_type_asc') ?></option>
                                <option value="DESC" <?= $data->filters->order_type == 'DESC' ? 'selected="selected"' : null ?>><?= l('global.filters.order_type_desc') ?></option>
                            </select>
                        </div>

                        <div class="form-group px-4">
                            <label for="filters_results_per_page" class="small"><?= l('global.filters.results_per_page') ?></label>
                            <select name="results_per_page" id="filters_results_per_page" class="custom-select custom-select-sm">
                                <?php foreach($data->filters->allowed_results_per_page as $key): ?>
                                    <option value="<?= $key ?>" <?= $data->filters->results_per_page == $key ? 'selected="selected"' : null ?>><?= $key ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>

                        <div class="form-group px-4 mt-4">
                            <button type="submit" name="submit" class="btn btn-sm btn-primary btn-block"><?= l('global.submit') ?></button>
                        </div>
                    </form>

                </div>
            </div>
        </div>

        <div class="ml-3">
            <button id="bulk_enable" type="button" class="btn btn-gray-300" data-toggle="tooltip" title="<?= l('global.bulk_actions') ?>"><i class="fas fa-fw fa-sm fa-list"></i></button>

            <div id="bulk_group" class="btn-group d-none" role="group">
                <div class="btn-group dropdown" role="group">
                    <button id="bulk_actions" type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" data-boundary="viewport" aria-haspopup="true" aria-expanded="false">
                        <?= l('global.bulk_actions') ?> <span id="bulk_counter" class="d-none"></span>
                    </button>
                    <div class="dropdown-menu" aria-labelledby="bulk_actions">
                        <a href="#" class="dropdown-item" data-toggle="modal" data-target="#bulk_download_modal"><i class="fas fa-fw fa-sm fa-download mr-2"></i> <?= l('global.download') ?></a>
                        <a href="#" class="dropdown-item" data-toggle="modal" data-target="#bulk_delete_modal"><i class="fas fa-fw fa-sm fa-trash-alt mr-2"></i> <?= l('global.delete') ?></a>
                    </div>
                </div>

                <button id="bulk_disable" type="button" class="btn btn-secondary" data-toggle="tooltip" title="<?= l('global.close') ?>"><i class="fas fa-fw fa-times"></i></button>
            </div>
        </div>

    </div>
</div>

<?= \Altum\Alerts::output_alerts() ?>

<form id="table" action="<?= SITE_URL . 'admin/barcodes/bulk' ?>" method="post" role="form">
    <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>" />
    <input type="hidden" name="type" value="" data-bulk-type />
    <input type="hidden" name="original_request" value="<?= base64_encode(\Altum\Router::$original_request) ?>" />
    <input type="hidden" name="original_request_query" value="<?= base64_encode(\Altum\Router::$original_request_query) ?>" />

    <div class="table-responsive table-custom-container">
        <table class="table table-custom">
            <thead>
            <tr>
                <th data-bulk-table class="d-none">
                    <div class="custom-control custom-checkbox">
                        <input id="bulk_select_all" type="checkbox" class="custom-control-input" />
                        <label class="custom-control-label" for="bulk_select_all"></label>
                    </div>
                </th>
                <th><?= l('global.user') ?></th>
                <th><?= l('global.name') ?></th>
                <th><?= l('global.type') ?></th>
                <th></th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach($data->barcodes as $row): ?>
                <?php //ALTUMCODE:DEMO if(DEMO) {$row->user_email = 'hidden@demo.com'; $row->user_name = 'hidden on demo';} ?>
                <tr>
                    <td data-bulk-table class="d-none">
                        <div class="custom-control custom-checkbox">
                            <input id="selected_barcode_id_<?= $row->barcode_id ?>" type="checkbox" class="custom-control-input" name="selected[]" value="<?= $row->barcode_id ?>" />
                            <label class="custom-control-label" for="selected_barcode_id_<?= $row->barcode_id ?>"></label>
                        </div>
                    </td>

                    <td class="text-nowrap">
                        <div class="d-flex">
                            <a href="<?= url('admin/user-view/' . $row->user_id) ?>">
                                <img src="<?= get_user_avatar($row->user_avatar, $row->user_email) ?>" referrerpolicy="no-referrer" loading="lazy" class="user-avatar rounded-circle mr-3" alt="" />
                            </a>

                            <div class="d-flex flex-column">
                                <div>
                                    <a href="<?= url('admin/user-view/' . $row->user_id) ?>"><?= $row->user_name ?></a>
                                </div>

                                <span class="text-muted small"><?= $row->user_email ?></span>
                            </div>
                        </div>
                    </td>

                    <td class="text-nowrap">
                        <div class="d-flex align-items-center">
                            <div class="mr-3" data-toggle="tooltip" title="<?= l('global.download') ?>">
                                <a href="<?= \Altum\Uploads::get_full_url('barcodes') . $row->barcode ?>" download="<?= $row->name . '.svg' ?>" target="_blank">
                                    <img src="<?= \Altum\Uploads::get_full_url('barcodes') . $row->barcode ?>" class="user-avatar" loading="lazy" />
                                </a>
                            </div>

                            <div>
                                <?= $row->name ?>
                            </div>
                        </div>
                    </td>

                    <td class="text-nowrap">
                        <span class="badge badge-light">
                            <?= $row->type ?>
                        </span>
                    </td>

                    <td class="text-nowrap">
                        <span class="mr-2" data-toggle="tooltip" data-html="true" title="<?= sprintf(l('global.datetime_tooltip'), '<br />' . \Altum\Date::get($row->datetime, 2) . '<br /><small>' . \Altum\Date::get($row->datetime, 3) . '</small>' . '<br /><small>(' . \Altum\Date::get_timeago($row->datetime) . ')</small>') ?>">
                            <i class="fas fa-fw fa-calendar text-muted"></i>
                        </span>

                        <span class="mr-2" data-toggle="tooltip" data-html="true" title="<?= sprintf(l('global.last_datetime_tooltip'), ($row->last_datetime ? '<br />' . \Altum\Date::get($row->last_datetime, 2) . '<br /><small>' . \Altum\Date::get($row->last_datetime, 3) . '</small>' . '<br /><small>(' . \Altum\Date::get_timeago($row->last_datetime) . ')</small>' : '<br />' . l('global.na'))) ?>">
                            <i class="fas fa-fw fa-history text-muted"></i>
                        </span>
                    </td>

                    <td>
                        <div class="d-flex justify-content-end">
                            <?= include_view(THEME_PATH . 'views/admin/barcodes/admin_barcode_dropdown_button.php', ['id' => $row->barcode_id, 'resource_name' => $row->name]) ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach ?>

            </tbody>
        </table>
    </div>
</form>

<div class="mt-3"><?= $data->pagination ?></div>

<?php require THEME_PATH . 'views/partials/js_bulk.php' ?>
<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/partials/bulk_delete_modal.php'), 'modals'); ?>
<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/partials/bulk_download_modal.php'), 'modals'); ?>
