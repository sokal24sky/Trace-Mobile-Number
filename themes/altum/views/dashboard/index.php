<?php defined('ALTUMCODE') || die() ?>


<div class="container">
    <?= \Altum\Alerts::output_alerts() ?>

    <div class="mb-3 d-flex justify-content-between">
        <div>
            <h1 class="h4 mb-0 text-truncate"><i class="fas fa-fw fa-xs fa-table-cells mr-1"></i> <?= l('dashboard.header') ?></h1>
        </div>
    </div>

    <div class="my-4">
        <div class="row m-n3">
            <?php if(settings()->codes->ai_qr_codes_is_enabled): ?>
                <div class="col-12 col-sm-6 col-xl-4 p-3 position-relative text-truncate">
                    <div id="total_ai_qr_codes_wrapper" class="card d-flex flex-row h-100 overflow-hidden" data-toggle="tooltip" data-html="true">
                        <div class="border-right border-gray-200 px-3 d-flex flex-column justify-content-center">
                            <a href="<?= url('ai-qr-codes') ?>" class="stretched-link">
                                <i class="fas fa-fw fa-robot text-primary-600"></i>
                            </a>
                        </div>

                        <div class="card-body text-truncate">
                            <div id="total_ai_qr_codes" class="text-truncate">
                                <span class="spinner-border spinner-border-sm" role="status"></span>
                            </div>
                            <div id="total_ai_qr_codes_progress" class="progress" style="height: .25rem;">
                                <div class="progress-bar <?= $this->user->plan_settings->ai_qr_codes_per_month_limit == -1 ? 'bg-success' : null ?>" role="progressbar" style="width: 0%" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif ?>

            <?php if(settings()->codes->qr_codes_is_enabled): ?>
                <div class="col-12 col-sm-6 col-xl-4 p-3 position-relative text-truncate">
                    <div id="total_qr_codes_wrapper" class="card d-flex flex-row h-100 overflow-hidden" data-toggle="tooltip" data-html="true">
                        <div class="border-right border-gray-200 px-3 d-flex flex-column justify-content-center">
                            <a href="<?= url('qr-codes') ?>" class="stretched-link">
                                <i class="fas fa-fw fa-qrcode text-primary-600"></i>
                            </a>
                        </div>

                        <div class="card-body text-truncate">
                            <div id="total_qr_codes" class="text-truncate">
                                <span class="spinner-border spinner-border-sm" role="status"></span>
                            </div>

                            <div id="total_qr_codes_progress" class="progress" style="height: .25rem;">
                                <div class="progress-bar <?= $this->user->plan_settings->qr_codes_limit == -1 ? 'bg-success' : null ?>" role="progressbar" style="width: 0%" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif ?>

            <?php if(settings()->codes->barcodes_is_enabled): ?>
                <div class="col-12 col-sm-6 col-xl-4 p-3 position-relative text-truncate">
                    <div id="total_barcodes_wrapper" class="card d-flex flex-row h-100 overflow-hidden" data-toggle="tooltip" data-html="true">
                        <div class="border-right border-gray-200 px-3 d-flex flex-column justify-content-center">
                            <a href="<?= url('barcodes') ?>" class="stretched-link">
                                <i class="fas fa-fw fa-barcode text-primary-600"></i>
                            </a>
                        </div>

                        <div class="card-body text-truncate">
                            <div id="total_barcodes" class="text-truncate">
                                <span class="spinner-border spinner-border-sm" role="status"></span>
                            </div>

                            <div id="total_barcodes_progress" class="progress" style="height: .25rem;">
                                <div class="progress-bar <?= $this->user->plan_settings->barcodes_limit == -1 ? 'bg-success' : null ?>" role="progressbar" style="width: 0%" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif ?>

            <div class="col-12 col-sm-6 col-xl-4 p-3 position-relative text-truncate">
                <div id="total_links_wrapper" class="card d-flex flex-row h-100 overflow-hidden" data-toggle="tooltip" data-html="true">
                    <div class="border-right border-gray-200 px-3 d-flex flex-column justify-content-center">
                        <a href="<?= url('links') ?>" class="stretched-link">
                            <i class="fas fa-fw fa-link text-primary-600"></i>
                        </a>
                    </div>

                    <div class="card-body text-truncate">
                        <div id="total_links" class="text-truncate">
                            <span class="spinner-border spinner-border-sm" role="status"></span>
                        </div>

                        <div id="total_links_progress" class="progress" style="height: .25rem;">
                            <div class="progress-bar <?= $this->user->plan_settings->links_limit == -1 ? 'bg-success' : null ?>" role="progressbar" style="width: 0%" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>

            <?php if(settings()->links->domains_is_enabled): ?>
                <div class="col-12 col-sm-6 col-xl-4 p-3 position-relative text-truncate">
                    <div id="total_domains_wrapper" class="card d-flex flex-row h-100 overflow-hidden" data-toggle="tooltip" data-html="true">
                        <div class="border-right border-gray-200 px-3 d-flex flex-column justify-content-center">
                            <a href="<?= url('domains') ?>" class="stretched-link">
                                <i class="fas fa-fw fa-globe text-primary-600"></i>
                            </a>
                        </div>

                        <div class="card-body text-truncate">
                            <div id="total_domains" class="text-truncate">
                                <span class="spinner-border spinner-border-sm" role="status"></span>
                            </div>

                            <div id="total_domains_progress" class="progress" style="height: .25rem;">
                                <div class="progress-bar <?= $this->user->plan_settings->domains_limit == -1 ? 'bg-success' : null ?>" role="progressbar" style="width: 0%" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif ?>

            <?php if(settings()->links->projects_is_enabled): ?>
                <div class="col-12 col-sm-6 col-xl-4 p-3 position-relative text-truncate">
                    <div id="total_projects_wrapper" class="card d-flex flex-row h-100 overflow-hidden" data-toggle="tooltip" data-html="true">
                        <div class="border-right border-gray-200 px-3 d-flex flex-column justify-content-center">
                            <a href="<?= url('projects') ?>" class="stretched-link">
                                <i class="fas fa-fw fa-diagram-project text-primary-600"></i>
                            </a>
                        </div>

                        <div class="card-body text-truncate">
                            <div id="total_projects" class="text-truncate">
                                <span class="spinner-border spinner-border-sm" role="status"></span>
                            </div>

                            <div id="total_projects_progress" class="progress" style="height: .25rem;">
                                <div class="progress-bar <?= $this->user->plan_settings->projects_limit == -1 ? 'bg-success' : null ?>" role="progressbar" style="width: 0%" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif ?>
        </div>
    </div>

    <?php $dashboard_features = ((array) $this->user->preferences->dashboard) + array_fill_keys(['ai_qr_codes', 'qr_codes', 'barcodes', 'links'], true) ?>

    <?php foreach($dashboard_features as $feature => $is_enabled): ?>

        <?php if($is_enabled && $feature == 'ai_qr_codes' && settings()->codes->ai_qr_codes_is_enabled): ?>
            <div class="mt-5 mb-5">
                <div class="d-flex align-items-center mb-3">
                    <h2 class="small font-weight-bold text-uppercase text-muted mb-0 mr-3"><i class="fas fa-fw fa-sm fa-robot mr-1"></i> <?= l('dashboard.ai_qr_codes.header') ?></h2>

                    <div class="flex-fill">
                        <hr class="border-gray-200" />
                    </div>

                    <div class="ml-3">
                        <a href="<?= url('ai-qr-code-create') ?>" class="btn btn-sm btn-outline-primary"><i class="fas fa-fw fa-plus-circle fa-sm mr-1"></i> <?= l('ai_qr_codes.create') ?></a>
                        <a href="<?= url('ai-qr-codes') ?>" class="btn btn-sm btn-primary" data-toggle="tooltip" title="<?= l('global.view_all') ?>"><i class="fas fa-fw fa-qrcode fa-sm"></i></a>
                    </div>
                </div>

                <?php if(count($data->ai_qr_codes)): ?>
                    <div class="table-responsive table-custom-container">
                        <table class="table table-custom">
                            <thead>
                            <tr>
                                <th><?= l('global.name') ?></th>
                                <th></th>
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
                <?php else: ?>

                    <?= include_view(THEME_PATH . 'views/partials/no_data.php', [
                        'filters_get' => $data->filters->get ?? [],
                        'name' => 'qr_codes',
                        'has_secondary_text' => true,
                    ]); ?>

                <?php endif ?>

            </div>
        <?php endif ?>

        <?php if($is_enabled && $feature == 'links'): ?>
            <div class="mt-5 mb-5">
                <div class="d-flex align-items-center mb-3">
                    <h2 class="small font-weight-bold text-uppercase text-muted mb-0 mr-3"><i class="fas fa-fw fa-sm fa-link mr-1"></i> <?= l('dashboard.links.header') ?></h2>

                    <div class="flex-fill">
                        <hr class="border-gray-200" />
                    </div>

                    <div class="ml-3">
                        <a href="<?= url('link-create') ?>" class="btn btn-sm btn-outline-primary"><i class="fas fa-fw fa-plus-circle fa-sm mr-1"></i> <?= l('links.create') ?></a>
                        <a href="<?= url('links') ?>" class="btn btn-sm btn-primary" data-toggle="tooltip" title="<?= l('global.view_all') ?>"><i class="fas fa-fw fa-link fa-sm"></i></a>
                    </div>
                </div>

                <?php if(count($data->links)): ?>
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
                                <th><?= l('links.table.link_id') ?></th>
                                <th></th>
                                <th><?= l('links.table.stats') ?></th>
                                <th></th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>

                            <?php foreach($data->links as $row): ?>

                                <tr>
                                    <td data-bulk-table class="d-none">
                                        <div class="custom-control custom-checkbox">
                                            <input id="selected_link_id_<?= $row->link_id ?>" type="checkbox" class="custom-control-input" name="selected[]" value="<?= $row->link_id ?>" />
                                            <label class="custom-control-label" for="selected_link_id_<?= $row->link_id ?>"></label>
                                        </div>
                                    </td>

                                    <td class="text-nowrap">
                                        <div class="d-flex flex-column">
                                            <div><a href="<?= url('link-update/' . $row->link_id) ?>"><?= $row->url ?></a></div>

                                            <div class="small text-muted">
                                                <img referrerpolicy="no-referrer" src="<?= get_favicon_url_from_domain(parse_url($row->location_url, PHP_URL_HOST)) ?>" class="img-fluid icon-favicon-small mr-1" loading="lazy" />

                                                <span title="<?= remove_url_protocol_from_url($row->location_url) ?>"><?= string_truncate(remove_url_protocol_from_url($row->location_url), 32) ?></span>

                                                <a href="<?= $row->location_url ?>" target="_blank" rel="noreferrer">
                                                    <i class="fas fa-fw fa-xs fa-external-link-alt text-muted ml-1"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </td>

                                    <?php if(settings()->links->projects_is_enabled): ?>
                                    <td class="text-nowrap">
                                        <?php if($row->project_id): ?>
                                            <a href="<?= url('links?project_id=' . $row->project_id) ?>" class="small text-decoration-none">
                                                <span class="badge badge-light" style="color: <?= $data->projects[$row->project_id]->color ?> !important;">
                                                    <?= $data->projects[$row->project_id]->name ?>
                                                </span>
                                            </a>
                                        <?php endif ?>
                                    </td>
                                    <?php endif ?>

                                    <td class="text-nowrap">
                                        <a href="<?= url('link-statistics/' . $row->link_id) ?>" class="badge badge-light" data-toggle="tooltip" title="<?= l('link_statistics.pageviews') ?>">
                                            <i class="fas fa-fw fa-chart-bar mr-1"></i> <?= nr($row->pageviews) ?>
                                        </a>
                                    </td>

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
                                            <button
                                                    id="url_copy"
                                                    type="button"
                                                    class="btn btn-link text-secondary"
                                                    data-toggle="tooltip"
                                                    title="<?= l('global.clipboard_copy') ?>"
                                                    aria-label="<?= l('global.clipboard_copy') ?>"
                                                    data-copy="<?= l('global.clipboard_copy') ?>"
                                                    data-copied="<?= l('global.clipboard_copied') ?>"
                                                    data-clipboard-text="<?= $row->full_url ?>"
                                            >
                                                <i class="fas fa-fw fa-sm fa-copy"></i>
                                            </button>

                                            <?= include_view(THEME_PATH . 'views/links/link_dropdown_button.php', ['id' => $row->link_id, 'link' => $row]) ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach ?>

                            </tbody>
                        </table>
                    </div>
                <?php else: ?>

                    <?= include_view(THEME_PATH . 'views/partials/no_data.php', [
                        'filters_get' => $data->filters->get ?? [],
                        'name' => 'links',
                        'has_secondary_text' => true,
                    ]); ?>

                <?php endif ?>

            </div>
        <?php endif ?>

        <?php if($is_enabled && $feature == 'qr_codes' && settings()->codes->qr_codes_is_enabled): ?>
            <div class="mt-5 mb-5">
                <div class="d-flex align-items-center mb-3">
                    <h2 class="small font-weight-bold text-uppercase text-muted mb-0 mr-3"><i class="fas fa-fw fa-sm fa-qrcode mr-1"></i> <?= l('dashboard.qr_codes.header') ?></h2>

                    <div class="flex-fill">
                        <hr class="border-gray-200" />
                    </div>

                    <div class="ml-3">
                        <a href="<?= url('qr-code-create') ?>" class="btn btn-sm btn-outline-primary"><i class="fas fa-fw fa-plus-circle fa-sm mr-1"></i> <?= l('qr_codes.create') ?></a>
                        <a href="<?= url('qr-codes') ?>" class="btn btn-sm btn-primary" data-toggle="tooltip" title="<?= l('global.view_all') ?>"><i class="fas fa-fw fa-qrcode fa-sm"></i></a>
                    </div>
                </div>

                <?php if(count($data->qr_codes)): ?>
                    <div class="table-responsive table-custom-container">
                        <table class="table table-custom">
                            <thead>
                            <tr>
                                <th><?= l('global.name') ?></th>
                                <th><?= l('global.type') ?></th>
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>

                            <?php foreach($data->qr_codes as $row): ?>
                                <tr>
                                    <td class="text-nowrap">
                                        <div class="d-flex align-items-center">
                                            <div class="mr-3" data-toggle="tooltip" title="<?= l('global.download') ?>">
                                                <a href="<?= \Altum\Uploads::get_full_url('qr_codes') . $row->qr_code ?>" download="<?= $row->name . '.svg' ?>" target="_blank">
                                                    <img src="<?= \Altum\Uploads::get_full_url('qr_codes') . $row->qr_code ?>" class="qr-code-avatar" loading="lazy" />
                                                </a>
                                            </div>

                                            <div class="d-flex flex-column ">
                                                <div>
                                                    <a href="<?= url('qr-code-update/' . $row->qr_code_id) ?>" class="font-weight-bold text-truncate"><?= $row->name ?></a>
                                                </div>
                                                <?php if($row->type == 'url'): ?>
                                                    <div class="d-flex align-items-center">
                                                        <small class="d-inline-block text-truncate text-muted">
                                                            <?= $row->settings->url ?>
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

                                    <td class="text-nowrap">
                                <span class="badge badge-light">
                                    <i class="<?= $data->available_qr_codes[$row->type]['icon'] ?> fa-fw fa-sm mr-1"></i>
                                    <?= l('qr_codes.type.' . $row->type) ?>
                                </span>
                                    </td>

                                    <?php if(settings()->links->projects_is_enabled): ?>
                                    <td class="text-nowrap">
                                        <?php if($row->project_id): ?>
                                            <a href="<?= url('qr-codes?project_id=' . $row->project_id) ?>" class="text-decoration-none" data-toggle="tooltip" title="<?= l('projects.project_id') ?>">
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
                                                <button type="button" class="btn btn-block btn-link dropdown-toggle dropdown-toggle-simple" title="<?= l('global.download') ?>" data-toggle="dropdown" aria-expanded="false" data-tooltip data-tooltip-hide-on-click>
                                                    <i class="fas fa-fw fa-sm fa-download"></i>
                                                </button>

                                                <div class="dropdown-menu">
                                                    <a href="<?= \Altum\Uploads::get_full_url('qr_codes') . $row->qr_code ?>" class="dropdown-item" download="<?= get_slug($row->name) . '.svg' ?>"><?= sprintf(l('global.download_as'), 'SVG') ?></a>
                                                    <button type="button" class="dropdown-item" onclick="convert_svg_qr_code_to_others('<?= \Altum\Uploads::get_full_url('qr_codes') . $row->qr_code ?>', 'png', '<?= get_slug($row->name) . '.png' ?>');"><?= sprintf(l('global.download_as'), 'PNG') ?></button>
                                                    <button type="button" class="dropdown-item" onclick="convert_svg_qr_code_to_others('<?= \Altum\Uploads::get_full_url('qr_codes') . $row->qr_code ?>', 'jpg', '<?= get_slug($row->name) . '.jpg' ?>');"><?= sprintf(l('global.download_as'), 'JPG') ?></button>
                                                    <button type="button" class="dropdown-item" onclick="convert_svg_qr_code_to_others('<?= \Altum\Uploads::get_full_url('qr_codes') . $row->qr_code ?>', 'webp', '<?= get_slug($row->name) . '.webp' ?>');"><?= sprintf(l('global.download_as'), 'WEBP') ?></button>
                                                </div>
                                            </div>

                                            <?= include_view(THEME_PATH . 'views/qr-codes/qr_code_dropdown_button.php', ['id' => $row->qr_code_id, 'resource_name' => $row->name]) ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach ?>

                            </tbody>
                        </table>
                    </div>
                <?php else: ?>

                    <?= include_view(THEME_PATH . 'views/partials/no_data.php', [
                        'filters_get' => $data->filters->get ?? [],
                        'name' => 'qr_codes',
                        'has_secondary_text' => true,
                    ]); ?>

                <?php endif ?>

            </div>
        <?php endif ?>

        <?php if($is_enabled && $feature == 'barcodes' && settings()->codes->barcodes_is_enabled): ?>
            <div class="mt-5">
                <div class="d-flex align-items-center mb-3">
                    <h2 class="small font-weight-bold text-uppercase text-muted mb-0 mr-3"><i class="fas fa-fw fa-sm fa-qrcode mr-1"></i> <?= l('dashboard.barcodes.header') ?></h2>

                    <div class="flex-fill">
                        <hr class="border-gray-200" />
                    </div>

                    <div class="ml-3">
                        <a href="<?= url('barcode-create') ?>" class="btn btn-sm btn-outline-primary"><i class="fas fa-fw fa-plus-circle fa-sm mr-1"></i> <?= l('barcodes.create') ?></a>
                        <a href="<?= url('barcodes') ?>" class="btn btn-sm btn-primary" data-toggle="tooltip" title="<?= l('global.view_all') ?>"><i class="fas fa-fw fa-barcode fa-sm"></i></a>
                    </div>
                </div>

                <?php if(count($data->barcodes)): ?>
                    <div class="table-responsive table-custom-container">
                        <table class="table table-custom">
                            <thead>
                            <tr>
                                <th><?= l('global.name') ?></th>
                                <th><?= l('global.type') ?></th>
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>

                            <?php foreach($data->barcodes as $row): ?>
                                <tr>
                                    <td class="text-nowrap">
                                        <div class="d-flex align-items-center">
                                            <div class="mr-3" data-toggle="tooltip" title="<?= l('global.download') ?>">
                                                <a href="<?= \Altum\Uploads::get_full_url('barcodes') . $row->barcode ?>" download="<?= $row->name . '.svg' ?>" target="_blank">
                                                    <img src="<?= \Altum\Uploads::get_full_url('barcodes') . $row->barcode ?>" class="barcode-avatar" loading="lazy" />
                                                </a>
                                            </div>

                                            <div>
                                                <a href="<?= url('barcode-update/' . $row->barcode_id) ?>" class="font-weight-bold text-truncate"><?= $row->name ?></a>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="text-nowrap">
                                    <span class="badge badge-light">
                                        <?= $row->type ?>
                                    </span>
                                    </td>

                                    <?php if(settings()->links->projects_is_enabled): ?>
                                    <td class="text-nowrap">
                                        <?php if($row->project_id): ?>
                                            <a href="<?= url('barcodes?project_id=' . $row->project_id) ?>" class="text-decoration-none" data-toggle="tooltip" title="<?= l('projects.project_id') ?>">
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
                                                <button type="button" class="btn btn-block btn-link dropdown-toggle dropdown-toggle-simple" title="<?= l('global.download') ?>" data-toggle="dropdown" aria-expanded="false" data-tooltip data-tooltip-hide-on-click>
                                                    <i class="fas fa-fw fa-sm fa-download"></i>
                                                </button>

                                                <div class="dropdown-menu">
                                                    <a href="<?= \Altum\Uploads::get_full_url('barcodes') . $row->barcode ?>" class="dropdown-item" download="<?= get_slug($row->name) . '.svg' ?>"><?= sprintf(l('global.download_as'), 'SVG') ?></a>
                                                    <button type="button" class="dropdown-item" onclick="convert_svg_barcode_to_others('<?= \Altum\Uploads::get_full_url('barcodes') . $row->barcode ?>', 'png', '<?= get_slug($row->name) . '.png' ?>');"><?= sprintf(l('global.download_as'), 'PNG') ?></button>
                                                    <button type="button" class="dropdown-item" onclick="convert_svg_barcode_to_others('<?= \Altum\Uploads::get_full_url('barcodes') . $row->barcode ?>', 'jpg', '<?= get_slug($row->name) . '.jpg' ?>');"><?= sprintf(l('global.download_as'), 'JPG') ?></button>
                                                    <button type="button" class="dropdown-item" onclick="convert_svg_barcode_to_others('<?= \Altum\Uploads::get_full_url('barcodes') . $row->barcode ?>', 'webp', '<?= get_slug($row->name) . '.webp' ?>');"><?= sprintf(l('global.download_as'), 'WEBP') ?></button>
                                                </div>
                                            </div>

                                            <?= include_view(THEME_PATH . 'views/barcodes/barcode_dropdown_button.php', ['id' => $row->barcode_id, 'resource_name' => $row->name]) ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach ?>

                            </tbody>
                        </table>
                    </div>
                <?php else: ?>

                    <?= include_view(THEME_PATH . 'views/partials/no_data.php', [
                        'filters_get' => $data->filters->get ?? [],
                        'name' => 'barcodes',
                        'has_secondary_text' => true,
                    ]); ?>

                <?php endif ?>

            </div>
        <?php endif ?>

    <?php endforeach ?>
</div>

<?php require THEME_PATH . 'views/qr-codes/js_qr_codes.php' ?>
<?php require THEME_PATH . 'views/barcodes/js_barcodes.php' ?>


<?php ob_start() ?>
<script>
    'use strict';
    
    (async function fetch_statistics() {
        /* Send request to server */
        let response = await fetch(`${url}dashboard/get_stats_ajax`, {
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

            /* update total_ai_qr_codes */
            const total_ai_qr_codes_element = document.querySelector('#total_ai_qr_codes');
            if (total_ai_qr_codes_element) {
                let total_ai_qr_codes_translation = <?= json_encode(l('dashboard.total_ai_qr_codes')) ?>;
                let total_ai_qr_codes = data.details.ai_qr_codes_current_month ? data.details.ai_qr_codes_current_month : 0;
                let total_ai_qr_codes_html = total_ai_qr_codes_translation.replace('%s', `<span class='h6' id='total_ai_qr_codes'>${nr(total_ai_qr_codes)}</span>`);

                let ai_qr_codes_plan_limit = <?= (int) $this->user->plan_settings->ai_qr_codes_per_month_limit ?>;

                /* calculate progress */
                let progress = 0;
                if (ai_qr_codes_plan_limit > 0) {
                    progress = Math.min((total_ai_qr_codes / ai_qr_codes_plan_limit) * 100, 100);
                }

                document.querySelector('#total_ai_qr_codes_progress .progress-bar').style.width = `${progress}%`;

                document.querySelector('#total_ai_qr_codes_wrapper').setAttribute('title', get_plan_feature_limit_info(total_ai_qr_codes, ai_qr_codes_plan_limit, true, <?= json_encode(l('global.info_message.plan_feature_limit_month_info')) ?>));
                total_ai_qr_codes_element.innerHTML = total_ai_qr_codes_html;
            }

            /* update total_links */
            const total_links_element = document.querySelector('#total_links');
            if (total_links_element) {
                let total_links_translation = <?= json_encode(l('dashboard.total_links')) ?>;
                let total_links = data.details.total_links ? data.details.total_links : 0;
                let total_links_html = total_links_translation.replace('%s', `<span class="h6" id="total_links">${nr(total_links)}</span>`);

                let links_plan_limit = <?= (int) $this->user->plan_settings->links_limit ?>;

                /* calculate progress */
                let progress = 0;
                if (links_plan_limit > 0) {
                    progress = Math.min((total_links / links_plan_limit) * 100, 100);
                }

                document.querySelector('#total_links_progress .progress-bar').style.width = `${progress}%`;

                document.querySelector('#total_links_wrapper').setAttribute('title', get_plan_feature_limit_info(total_links, <?= $this->user->plan_settings->links_limit ?>, true, <?= json_encode(l('global.info_message.plan_feature_limit_info')) ?>));
                total_links_element.innerHTML = total_links_html;
            }

            /* update total_domains */
            const total_domains_element = document.querySelector('#total_domains');
            if (total_domains_element) {
                let total_domains_translation = <?= json_encode(l('dashboard.total_domains')) ?>;
                let total_domains = data.details.total_domains ? data.details.total_domains : 0;
                let total_domains_html = total_domains_translation.replace('%s', `<span class='h6' id='total_domains'>${nr(total_domains)}</span>`);
                let domains_plan_limit = <?= (int) $this->user->plan_settings->domains_limit ?>;

                /* calculate progress */
                let progress = 0;
                if (domains_plan_limit > 0) {
                    progress = Math.min((total_domains / domains_plan_limit) * 100, 100);
                }

                document.querySelector('#total_domains_progress .progress-bar').style.width = `${progress}%`;
                document.querySelector('#total_domains_wrapper').setAttribute('title', get_plan_feature_limit_info(total_domains, domains_plan_limit, true, <?= json_encode(l('global.info_message.plan_feature_limit_info')) ?>));
                total_domains_element.innerHTML = total_domains_html;
            }

            /* update total_qr_codes */
            const total_qr_codes_element = document.querySelector('#total_qr_codes');
            if (total_qr_codes_element) {
                let total_qr_codes_translation = <?= json_encode(l('dashboard.total_qr_codes')) ?>;
                let total_qr_codes = data.details.total_qr_codes ? data.details.total_qr_codes : 0;
                let total_qr_codes_html = total_qr_codes_translation.replace('%s', `<span class='h6' id='total_qr_codes'>${nr(total_qr_codes)}</span>`);
                let qr_codes_plan_limit = <?= (int) $this->user->plan_settings->qr_codes_limit ?>;

                /* calculate progress */
                let progress = 0;
                if (qr_codes_plan_limit > 0) {
                    progress = Math.min((total_qr_codes / qr_codes_plan_limit) * 100, 100);
                }

                document.querySelector('#total_qr_codes_progress .progress-bar').style.width = `${progress}%`;
                document.querySelector('#total_qr_codes_wrapper').setAttribute('title', get_plan_feature_limit_info(total_qr_codes, qr_codes_plan_limit, true, <?= json_encode(l('global.info_message.plan_feature_limit_info')) ?>));
                total_qr_codes_element.innerHTML = total_qr_codes_html;
            }

            /* update total_barcodes */
            const total_barcodes_element = document.querySelector('#total_barcodes');
            if (total_barcodes_element) {
                let total_barcodes_translation = <?= json_encode(l('dashboard.total_barcodes')) ?>;
                let total_barcodes = data.details.total_barcodes ? data.details.total_barcodes : 0;
                let total_barcodes_html = total_barcodes_translation.replace('%s', `<span class='h6' id='total_barcodes'>${nr(total_barcodes)}</span>`);
                let barcodes_plan_limit = <?= (int) $this->user->plan_settings->barcodes_limit ?>;

                /* calculate progress */
                let progress = 0;
                if (barcodes_plan_limit > 0) {
                    progress = Math.min((total_barcodes / barcodes_plan_limit) * 100, 100);
                }

                document.querySelector('#total_barcodes_progress .progress-bar').style.width = `${progress}%`;
                document.querySelector('#total_barcodes_wrapper').setAttribute('title', get_plan_feature_limit_info(total_barcodes, barcodes_plan_limit, true, <?= json_encode(l('global.info_message.plan_feature_limit_info')) ?>));
                total_barcodes_element.innerHTML = total_barcodes_html;
            }

            /* update total_projects */
            const total_projects_element = document.querySelector('#total_projects');
            if (total_projects_element) {
                let total_projects_translation = <?= json_encode(l('dashboard.total_projects')) ?>;
                let total_projects = data.details.total_projects ? data.details.total_projects : 0;
                let total_projects_html = total_projects_translation.replace('%s', `<span class='h6' id='total_projects'>${nr(total_projects)}</span>`);
                let barcodes_plan_limit = <?= (int) $this->user->plan_settings->barcodes_limit ?>;

                /* calculate progress */
                let progress = 0;
                if (barcodes_plan_limit > 0) {
                    progress = Math.min((total_projects / barcodes_plan_limit) * 100, 100);
                }

                document.querySelector('#total_projects_progress .progress-bar').style.width = `${progress}%`;
                document.querySelector('#total_projects_wrapper').setAttribute('title', get_plan_feature_limit_info(total_projects, barcodes_plan_limit, true, <?= json_encode(l('global.info_message.plan_feature_limit_info')) ?>));
                total_projects_element.innerHTML = total_projects_html;
            }

            tooltips_initiate()
        }
    })();
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
