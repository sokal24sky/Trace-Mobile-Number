<?php defined('ALTUMCODE') || die() ?>


<div class="container">
    <?= \Altum\Alerts::output_alerts() ?>

    <div class="row mb-4">
        <div class="col-12 col-lg d-flex align-items-center mb-3 mb-lg-0 text-truncate">
            <h1 class="h4 m-0 text-truncate"><i class="fas fa-fw fa-xs fa-link mr-1"></i> <?= l('links.header') ?></h1>

            <div class="ml-2">
                <span data-toggle="tooltip" title="<?= l('links.subheader') ?>">
                    <i class="fas fa-fw fa-info-circle text-muted"></i>
                </span>
            </div>
        </div>

        <div class="col-12 col-lg-auto d-flex flex-wrap gap-3 d-print-none">
            <div>
                <?php if($this->user->plan_settings->links_limit != -1 && $data->total_links >= $this->user->plan_settings->links_limit): ?>
                    <button type="button" class="btn btn-primary disabled" <?= get_plan_feature_limit_reached_info() ?>>
                        <i class="fas fa-fw fa-plus-circle fa-sm mr-1"></i> <?= l('links.create') ?>
                    </button>
                <?php else: ?>
                    <a href="<?= url('link-create') ?>" class="btn btn-primary" data-toggle="tooltip" data-html="true" title="<?= get_plan_feature_limit_info($data->total_links, $this->user->plan_settings->links_limit, isset($data->filters) ? !$data->filters->has_applied_filters : true) ?>">
                        <i class="fas fa-fw fa-plus-circle fa-sm mr-1"></i> <?= l('links.create') ?>
                    </a>
                <?php endif ?>
            </div>

            <div>
                <div class="dropdown">
                    <button type="button" class="btn btn-light dropdown-toggle-simple <?= count($data->links) ? null : 'disabled' ?>" data-toggle="dropdown" data-boundary="viewport" data-tooltip title="<?= l('global.export') ?>" data-tooltip-hide-on-click>
                        <i class="fas fa-fw fa-sm fa-download"></i>
                    </button>

                    <div class="dropdown-menu dropdown-menu-right d-print-none">
                        <a href="<?= url('links?' . $data->filters->get_get() . '&export=csv')  ?>" target="_blank" class="dropdown-item <?= $this->user->plan_settings->export->csv ? null : 'disabled pointer-events-all' ?>" <?= $this->user->plan_settings->export->csv ? null : get_plan_feature_disabled_info() ?>>
                            <i class="fas fa-fw fa-sm fa-file-csv mr-2"></i> <?= sprintf(l('global.export_to'), 'CSV') ?>
                        </a>
                        <a href="<?= url('links?' . $data->filters->get_get() . '&export=json') ?>" target="_blank" class="dropdown-item <?= $this->user->plan_settings->export->json ? null : 'disabled pointer-events-all' ?>" <?= $this->user->plan_settings->export->json ? null : get_plan_feature_disabled_info() ?>>
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
                    <button type="button" class="btn <?= $data->filters->has_applied_filters ? 'btn-dark' : 'btn-light' ?> filters-button dropdown-toggle-simple <?= count($data->links) || $data->filters->has_applied_filters ? null : 'disabled' ?>" data-toggle="dropdown" data-boundary="viewport" data-tooltip data-html="true" title="<?= l('global.filters.tooltip') ?>" data-tooltip-hide-on-click>
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
                                    <option value="url" <?= $data->filters->search_by == 'url' ? 'selected="selected"' : null ?>><?= l('links.filters.search_by_url') ?></option>
                                    <option value="location_url" <?= $data->filters->search_by == 'location_url' ? 'selected="selected"' : null ?>><?= l('links.filters.search_by_location_url') ?></option>
                                </select>
                            </div>

                            <div class="form-group px-4">
                                <label for="filters_is_enabled" class="small"><?= l('global.status') ?></label>
                                <select name="is_enabled" id="filters_is_enabled" class="custom-select custom-select-sm">
                                    <option value=""><?= l('global.all') ?></option>
                                    <option value="1" <?= isset($data->filters->filters['is_enabled']) && $data->filters->filters['is_enabled'] == '1' ? 'selected="selected"' : null ?>><?= l('global.active') ?></option>
                                    <option value="0" <?= isset($data->filters->filters['is_enabled']) && $data->filters->filters['is_enabled'] == '0' ? 'selected="selected"' : null ?>><?= l('global.disabled') ?></option>
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
                                    <?php foreach($data->projects as $project_id => $project): ?>
                                        <option value="<?= $project_id ?>" <?= isset($data->filters->filters['project_id']) && $data->filters->filters['project_id'] == $project_id ? 'selected="selected"' : null ?>><?= $project->name ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                            <?php endif ?>

                            <?php if(settings()->links->domains_is_enabled): ?>
                                <div class="form-group px-4">
                                    <div class="d-flex justify-content-between">
                                        <label for="filters_domain_id" class="small"><?= l('domains.domain_id') ?></label>
                                        <a href="<?= url('domain-create') ?>" target="_blank" class="small mb-2"><i class="fas fa-fw fa-sm fa-plus mr-1"></i> <?= l('global.create') ?></a>
                                    </div>
                                    <select name="domain_id" id="filters_domain_id" class="custom-select custom-select-sm">
                                        <option value=""><?= l('global.all') ?></option>
                                        <?php foreach($data->domains as $domain_id => $domain): ?>
                                            <option value="<?= $domain_id ?>" <?= isset($data->filters->filters['domain_id']) && $data->filters->filters['domain_id'] == $domain_id ? 'selected="selected"' : null ?>><?= $domain->host ?></option>
                                        <?php endforeach ?>
                                    </select>
                                </div>
                            <?php endif ?>

                            <div class="form-group px-4">
                                <label for="filters_order_by" class="small"><?= l('global.filters.order_by') ?></label>
                                <select name="order_by" id="filters_order_by" class="custom-select custom-select-sm">
                                    <option value="link_id" <?= $data->filters->order_by == 'link_id' ? 'selected="selected"' : null ?>><?= l('global.id') ?></option>
                                    <option value="datetime" <?= $data->filters->order_by == 'datetime' ? 'selected="selected"' : null ?>><?= l('global.filters.order_by_datetime') ?></option>
                                    <option value="last_datetime" <?= $data->filters->order_by == 'last_datetime' ? 'selected="selected"' : null ?>><?= l('global.filters.order_by_last_datetime') ?></option>
                                    <option value="url" <?= $data->filters->order_by == 'url' ? 'selected="selected"' : null ?>><?= l('links.filters.order_by_url') ?></option>
                                    <option value="location_url" <?= $data->filters->order_by == 'location_url' ? 'selected="selected"' : null ?>><?= l('links.filters.order_by_location_url') ?></option>
                                    <option value="pageviews" <?= $data->filters->order_by == 'pageviews' ? 'selected="selected"' : null ?>><?= l('links.filters.order_by_pageviews') ?></option>
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
                            <a href="#" class="dropdown-item" data-toggle="modal" data-target="#bulk_delete_modal"><i class="fas fa-fw fa-sm fa-trash-alt mr-2"></i> <?= l('global.delete') ?></a>
                        </div>
                    </div>

                    <button id="bulk_disable" type="button" class="btn btn-secondary" data-toggle="tooltip" title="<?= l('global.close') ?>"><i class="fas fa-fw fa-times"></i></button>
                </div>
            </div>
        </div>
    </div>

    <?php if(count($data->links)): ?>
        <?php if($data->links_chart): ?>
        <div class="card mb-4">
            <div class="card-body">
                <div class="chart-container <?= !$data->links_chart['is_empty'] ? null : 'd-none' ?>">
                    <canvas id="pageviews_chart"></canvas>
                </div>
                <?= !$data->links_chart['is_empty'] ? null : include_view(THEME_PATH . 'views/partials/no_chart_data.php', ['has_wrapper' => false]); ?>

                <?php if(!$data->links_chart['is_empty'] && settings()->main->chart_cache ?? 12): ?>
                    <small class="text-muted">
                        <span data-toggle="tooltip" title="<?= sprintf(l('global.chart_help'), settings()->main->chart_cache ?? 12, settings()->main->chart_days ?? 30) ?>"><i class="fas fa-fw fa-sm fa-info-circle mr-1"></i></span>
                        <span class="d-lg-none"><?= sprintf(l('global.chart_help'), settings()->main->chart_cache ?? 12, settings()->main->chart_days ?? 30) ?></span>
                    </small>
                <?php endif ?>
            </div>
        </div>

    <?php require THEME_PATH . 'views/partials/js_chart_defaults.php' ?>

    <?php ob_start() ?>

        <script>
    'use strict';
    
            if(document.getElementById('pageviews_chart')) {
                let css = window.getComputedStyle(document.body);
                let pageviews_color = css.getPropertyValue('--primary');
                let visitors_color = css.getPropertyValue('--gray-400');
                let pageviews_color_gradient = null;
                let visitors_color_gradient = null;

                /* Chart */
                let pageviews_chart = document.getElementById('pageviews_chart').getContext('2d');

                /* Colors */
                pageviews_color_gradient = pageviews_chart.createLinearGradient(0, 0, 0, 250);
                pageviews_color_gradient.addColorStop(0, set_hex_opacity(pageviews_color, 0.6));
                pageviews_color_gradient.addColorStop(1, set_hex_opacity(pageviews_color, 0.1));

                visitors_color_gradient = pageviews_chart.createLinearGradient(0, 0, 0, 250);
                visitors_color_gradient.addColorStop(0, set_hex_opacity(visitors_color, 0.6));
                visitors_color_gradient.addColorStop(1, set_hex_opacity(visitors_color, 0.1));

                new Chart(pageviews_chart, {
                    type: 'line',
                    data: {
                        labels: <?= $data->links_chart['labels'] ?? '[]' ?>,
                        datasets: [
                            {
                                label: <?= json_encode(l('link_statistics.pageviews')) ?>,
                                data: <?= $data->links_chart['pageviews'] ?? '[]' ?>,
                                backgroundColor: pageviews_color_gradient,
                                borderColor: pageviews_color,
                                fill: true
                            },
                            {
                                label: <?= json_encode(l('link_statistics.visitors')) ?>,
                                data: <?= $data->links_chart['visitors'] ?? '[]' ?>,
                                backgroundColor: visitors_color_gradient,
                                borderColor: visitors_color,
                                fill: true
                            }
                        ]
                    },
                    options: chart_options
                });
            }
        </script>
    <?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
    <?php endif ?>

        <form id="table" action="<?= SITE_URL . 'links/bulk' ?>" method="post" role="form">
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
        </form>

        <div class="mt-3"><?= $data->pagination ?></div>
    <?php else: ?>

        <?= include_view(THEME_PATH . 'views/partials/no_data.php', [
            'filters_get' => $data->filters->get ?? [],
            'name' => 'links',
            'has_secondary_text' => true,
        ]); ?>

    <?php endif ?>
</div>

<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/links/link_delete_modal.php'), 'modals'); ?>
<?php include_view(THEME_PATH . 'views/partials/clipboard_js.php') ?>
<?php require THEME_PATH . 'views/partials/js_bulk.php' ?>
<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/partials/bulk_delete_modal.php'), 'modals'); ?>
