<?php defined('ALTUMCODE') || die() ?>


<section class="container">
    <?= \Altum\Alerts::output_alerts() ?>

    <div class="row mb-4">
        <div class="col-12 col-lg d-flex align-items-center mb-3 mb-lg-0 text-truncate">
            <h1 class="h4 m-0 text-truncate"><i class="fas fa-fw fa-xs fa-robot mr-1"></i> <?= l('ai_qr_codes.header') ?></h1>

            <div class="ml-2">
                <span data-toggle="tooltip" title="<?= l('ai_qr_codes.subheader') ?>">
                    <i class="fas fa-fw fa-info-circle text-muted"></i>
                </span>
            </div>
        </div>

        <div class="col-12 col-lg-auto d-flex flex-wrap gap-3 d-print-none">
            <div>
                <?php if(($this->user->plan_settings->ai_qr_codes_per_month_limit != -1 && $data->ai_qr_codes_current_month >= $this->user->plan_settings->ai_qr_codes_per_month_limit)): ?>
                    <button type="button" class="btn btn-primary disabled" <?= get_plan_feature_limit_reached_info() ?>>
                        <i class="fas fa-fw fa-plus-circle fa-sm mr-1"></i> <?= l('ai_qr_codes.create') ?>
                    </button>
                <?php else: ?>
                    <a href="<?= url('ai-qr-code-create') ?>" class="btn btn-primary" data-toggle="tooltip" data-html="true" title="<?= get_plan_feature_limit_info($data->ai_qr_codes_current_month, $this->user->plan_settings->ai_qr_codes_per_month_limit, isset($data->filters) ? !$data->filters->has_applied_filters : true) ?>">
                        <i class="fas fa-fw fa-plus-circle fa-sm mr-1"></i> <?= l('ai_qr_codes.create') ?>
                    </a>
                <?php endif ?>
            </div>

            <div>
                <div class="dropdown">
                    <button type="button" class="btn btn-light dropdown-toggle-simple <?= count($data->ai_qr_codes) ? null : 'disabled' ?>" data-toggle="dropdown" data-boundary="viewport" data-tooltip title="<?= l('global.export') ?>" data-tooltip-hide-on-click>
                        <i class="fas fa-fw fa-sm fa-download"></i>
                    </button>

                    <div class="dropdown-menu dropdown-menu-right d-print-none">
                        <a href="<?= url('ai-qr-codes?' . $data->filters->get_get() . '&export=csv')  ?>" target="_blank" class="dropdown-item <?= $this->user->plan_settings->export->csv ? null : 'disabled pointer-events-all' ?>" <?= $this->user->plan_settings->export->csv ? null : get_plan_feature_disabled_info() ?>>
                            <i class="fas fa-fw fa-sm fa-file-csv mr-2"></i> <?= sprintf(l('global.export_to'), 'CSV') ?>
                        </a>
                        <a href="<?= url('ai-qr-codes?' . $data->filters->get_get() . '&export=json') ?>" target="_blank" class="dropdown-item <?= $this->user->plan_settings->export->json ? null : 'disabled pointer-events-all' ?>" <?= $this->user->plan_settings->export->json ? null : get_plan_feature_disabled_info() ?>>
                            <i class="fas fa-fw fa-sm fa-file-code mr-2"></i> <?= sprintf(l('global.export_to'), 'JSON') ?>
                        </a>
                        <a href="#" class="dropdown-item <?= $this->user->plan_settings->export->pdf ? null : 'disabled pointer-events-all' ?>" <?= $this->user->plan_settings->export->pdf ? $this->user->plan_settings->export->pdf ? 'onclick="event.preventDefault(); window.print();"' : 'disabled pointer-events-all' : get_plan_feature_disabled_info() ?>>
                            <i class="fas fa-fw fa-sm fa-file-pdf mr-2"></i> <?= sprintf(l('global.export_to'), 'PDF') ?>
                        </a>
                    </div>
                </div>
            </div>

            <div>
                <div class="dropdown">
                    <button type="button" class="btn <?= $data->filters->has_applied_filters ? 'btn-dark' : 'btn-light' ?> filters-button dropdown-toggle-simple <?= count($data->ai_qr_codes) || $data->filters->has_applied_filters ? null : 'disabled' ?>" data-toggle="dropdown" data-boundary="viewport" data-tooltip data-html="true" title="<?= l('global.filters.tooltip') ?>" data-tooltip-hide-on-click>
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
                                </select>
                            </div>

                            <?php if(settings()->links->projects_is_enabled): ?>
                            <div class="form-group px-4">
                                <div class="d-flex justify-content-between">
                                    <label for="filters_project_id" class="small"><?= l('projects.project_id') ?></label>
                                    <a href="<?= url('project-create') ?>" target="_blank" class="small mb-2"><i class="fas fa-fw fa-sm fa-plus mr-1"></i> <?= l('global.create') ?></a>
                                </div>
                                <select name="project_id" id="filters_project_id" class="custom-select custom-select-sm">
                                    <option value=""><?= l('global.all') ?></option>
                                    <?php foreach($data->projects as $row): ?>
                                        <option value="<?= $row->project_id ?>" <?= isset($data->filters->filters['project_id']) && $data->filters->filters['project_id'] == $row->project_id ? 'selected="selected"' : null ?>><?= $row->name ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                            <?php endif ?>

                            <div class="form-group px-4">
                                <label for="filters_order_by" class="small"><?= l('global.filters.order_by') ?></label>
                                <select name="order_by" id="filters_order_by" class="custom-select custom-select-sm">
                                    <option value="ai_qr_code_id" <?= $data->filters->order_by == 'ai_qr_code_id' ? 'selected="selected"' : null ?>><?= l('global.id') ?></option>
                                    <option value="datetime" <?= $data->filters->order_by == 'datetime' ? 'selected="selected"' : null ?>><?= l('global.filters.order_by_datetime') ?></option>
                                    <option value="last_datetime" <?= $data->filters->order_by == 'last_datetime' ? 'selected="selected"' : null ?>><?= l('global.filters.order_by_last_datetime') ?></option>
                                    <option value="name" <?= $data->filters->order_by == 'name' ? 'selected="selected"' : null ?>><?= l('global.name') ?></option>
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

            <div>
                <button id="bulk_enable" type="button" class="btn btn-light" data-toggle="tooltip" title="<?= l('global.bulk_actions') ?>"><i class="fas fa-fw fa-sm fa-list"></i></button>

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

    <?php if(count($data->ai_qr_codes)): ?>
        <form id="table" action="<?= SITE_URL . 'ai-qr-codes/bulk' ?>" method="post" role="form">
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
                        <th><?= l('global.name') ?></th>
                        <?php if(settings()->links->projects_is_enabled): ?>
                        <th></th>
                        <?php endif ?>
                        <th></th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>

                    <?php foreach($data->ai_qr_codes as $row): ?>
                        <tr>
                            <td data-bulk-table class="d-none">
                                <div class="custom-control custom-checkbox">
                                    <input id="selected_ai_qr_code_id_<?= $row->ai_qr_code_id ?>" type="checkbox" class="custom-control-input" name="selected[]" value="<?= $row->ai_qr_code_id ?>" />
                                    <label class="custom-control-label" for="selected_ai_qr_code_id_<?= $row->ai_qr_code_id ?>"></label>
                                </div>
                            </td>

                            <td class="text-nowrap">
                                <div class="d-flex align-items-center">
                                    <div class="mr-3" data-toggle="tooltip" title="<?= l('global.download') ?>">
                                        <a href="<?= \Altum\Uploads::get_full_url('ai_qr_codes') . $row->ai_qr_code ?>" download="<?= $row->name . '.png' ?>" target="_blank">
                                            <img src="<?= \Altum\Uploads::get_full_url('ai_qr_codes') . $row->ai_qr_code ?>" class="qr-code-avatar" loading="lazy" />
                                        </a>
                                    </div>

                                    <div class="d-flex flex-column">
                                        <div>
                                            <a href="<?= url('ai-qr-code-update/' . $row->ai_qr_code_id) ?>" class="font-weight-bold text-truncate"><?= $row->name ?></a>
                                        </div>
                                        <?php if(string_starts_with('http://', $row->content) || string_starts_with('https://', $row->content)): ?>
                                            <div class="d-flex align-items-center">
                                                <small class="d-inline-block text-truncate text-muted">
                                                    <?= remove_url_protocol_from_url($row->content) ?>
                                                </small>

                                                <?php if($row->link_id): ?>
                                                    <a href="<?= url('link-update/' . $row->link_id) ?>" class="btn btn-sm btn-link" data-toggle="tooltip" title="<?= l('global.update') ?>"><i class="fas fa-fw fa-pencil-alt"></i></a>
                                                    <a href="<?= url('link-statistics/' . $row->link_id) ?>" class="btn btn-sm btn-link" data-toggle="tooltip" title="<?= l('link_statistics.pageviews') ?>"><i class="fas fa-fw fa-chart-bar"></i></a>
                                                <?php endif ?>
                                            </div>
                                        <?php endif ?>
                                    </div>
                                </div>
                            </td>

                            <?php if(settings()->links->projects_is_enabled): ?>
                            <td class="text-nowrap">
                                <?php if($row->project_id): ?>
                                    <a href="<?= url('ai-qr-codes?project_id=' . $row->project_id) ?>" class="text-decoration-none" data-toggle="tooltip" title="<?= l('projects.project_id') ?>">
                                        <span class="badge badge-light" style="color: <?= $data->projects[$row->project_id]->color ?> !important;">
                                            <?= $data->projects[$row->project_id]->name ?>
                                        </span>
                                    </a>
                                <?php endif ?>
                            </td>
                            <?php endif ?>

                            <td class="text-nowrap text-muted">
                                <span class="mr-2" data-toggle="tooltip" data-html="true" title="<?= sprintf(l('global.datetime_tooltip'), '<br />' . \Altum\Date::get($row->datetime, 2) . '<br /><small>' . \Altum\Date::get($row->datetime, 3) . '</small>' . '<br /><small>(' . \Altum\Date::get_timeago($row->datetime) . ')</small>') ?>">
                                    <i class="fas fa-fw fa-calendar text-muted"></i>
                                </span>

                                <span class="mr-2" data-toggle="tooltip" data-html="true" title="<?= sprintf(l('global.last_datetime_tooltip'), ($row->last_datetime ? '<br />' . \Altum\Date::get($row->last_datetime, 2) . '<br /><small>' . \Altum\Date::get($row->last_datetime, 3) . '</small>' . '<br /><small>(' . \Altum\Date::get_timeago($row->last_datetime) . ')</small>' : '<br />' . l('global.na'))) ?>">
                                    <i class="fas fa-fw fa-history text-muted"></i>
                                </span>
                            </td>

                            <td>
                                <div class="d-flex justify-content-end">
                                    <div>
                                        <a href="<?= \Altum\Uploads::get_full_url('ai_qr_codes') . $row->ai_qr_code ?>" download="<?= $row->name . '.png' ?>" class="btn btn-block btn-link dropdown-toggle dropdown-toggle-simple" title="<?= sprintf(l('global.download_as'), 'PNG') ?>" data-tooltip data-tooltip-hide-on-click>
                                            <i class="fas fa-fw fa-sm fa-download"></i>
                                        </a>
                                    </div>

                                    <?= include_view(THEME_PATH . 'views/ai-qr-codes/ai_qr_code_dropdown_button.php', ['id' => $row->ai_qr_code_id, 'resource_name' => $row->name]) ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach ?>

                    </tbody>
                </table>
            </div>
        </form>

        <div class="mt-3"><?= $data->pagination ?></div>
    <?php else: ?>

        <?= include_view(THEME_PATH . 'views/partials/no_data.php', [
            'filters_get' => $data->filters->get ?? [],
            'name' => 'qr_codes',
            'has_secondary_text' => true,
        ]); ?>

    <?php endif ?>

</section>

<?php require THEME_PATH . 'views/qr-codes/js_qr_codes.php' ?>
<?php require THEME_PATH . 'views/partials/js_bulk.php' ?>
<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/partials/bulk_delete_modal.php'), 'modals'); ?>
<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/partials/bulk_download_modal.php'), 'modals'); ?>
