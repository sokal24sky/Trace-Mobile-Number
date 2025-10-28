<?php defined('ALTUMCODE') || die() ?>

<?php if(count($data->broadcasts) || $data->filters->has_applied_filters): ?>

    <div class="d-flex flex-column flex-md-row justify-content-between mb-4">
        <h1 class="h3 mb-3 mb-md-0 text-truncate"><i class="fas fa-fw fa-xs fa-mail-bulk text-primary-900 mr-2"></i> <?= l('admin_broadcasts.header') ?></h1>

        <div class="d-flex position-relative">
            <div>
                <a href="<?= url('admin/broadcast-create') ?>" class="btn btn-primary text-nowrap"><i class="fas fa-fw fa-plus-circle fa-sm mr-1"></i> <?= l('admin_broadcasts.create') ?></a>
            </div>

            <div class="ml-3">
                <a href="<?= url('admin/statistics/broadcasts') ?>" class="btn btn-gray-300" data-tooltip title="<?= l('global.statistics') ?>">
                    <i class="fas fa-fw fa-sm fa-chart-bar"></i>
                </a>
            </div>
            
            <div class="ml-3">
                <div class="dropdown">
                    <button type="button" class="btn btn-gray-300 dropdown-toggle-simple" data-toggle="dropdown" data-boundary="viewport" data-tooltip title="<?= l('global.export') ?>" data-tooltip-hide-on-click>
                        <i class="fas fa-fw fa-sm fa-download"></i>
                    </button>

                    <div class="dropdown-menu dropdown-menu-right d-print-none">
                        <a href="<?= url('admin/broadcasts?' . $data->filters->get_get() . '&export=csv') ?>" target="_blank" class="dropdown-item <?= $this->user->plan_settings->export->csv ? null : 'disabled pointer-events-all' ?>" <?= $this->user->plan_settings->export->csv ? null : get_plan_feature_disabled_info() ?>>
                            <i class="fas fa-fw fa-sm fa-file-csv mr-2"></i> <?= sprintf(l('global.export_to'), 'CSV') ?>
                        </a>
                        <a href="<?= url('admin/broadcasts?' . $data->filters->get_get() . '&export=json') ?>" target="_blank" class="dropdown-item <?= $this->user->plan_settings->export->json ? null : 'disabled pointer-events-all' ?>" <?= $this->user->plan_settings->export->json ? null : get_plan_feature_disabled_info() ?>>
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
                                    <option value="content" <?= $data->filters->search_by == 'content' ? 'selected="selected"' : null ?>><?= l('admin_broadcasts.content') ?></option>
                                </select>
                            </div>

                            <div class="form-group px-4">
                                <label for="filters_segment" class="small"><?= l('admin_broadcasts.segment') ?></label>
                                <select name="segment" id="filters_segment" class="custom-select custom-select-sm">
                                    <option value=""><?= l('global.all') ?></option>
                                    <option value="all" <?= isset($data->filters->filters['segment']) && $data->filters->filters['segment'] == 'all' ? 'selected="selected"' : null ?>><?= l('admin_broadcasts.segment.all') ?></option>
                                    <option value="subscribers" <?= isset($data->filters->filters['segment']) && $data->filters->filters['segment'] == 'subscribers' ? 'selected="selected"' : null ?>><?= l('admin_broadcasts.segment.subscribers') ?></option>
                                    <option value="custom" <?= isset($data->filters->filters['segment']) && $data->filters->filters['segment'] == 'custom' ? 'selected="selected"' : null ?>><?= l('admin_broadcasts.segment.custom') ?></option>
                                </select>
                            </div>

                            <div class="form-group px-4">
                                <label for="filters_status" class="small"><?= l('global.status') ?></label>
                                <select name="status" id="filters_status" class="custom-select custom-select-sm">
                                    <option value=""><?= l('global.all') ?></option>
                                    <option value="draft" <?= isset($data->filters->filters['status']) && $data->filters->filters['status'] == 'draft' ? 'selected="selected"' : null ?>><?= l('admin_broadcasts.status.draft') ?></option>
                                    <option value="processing" <?= isset($data->filters->filters['status']) && $data->filters->filters['status'] == 'processing' ? 'selected="selected"' : null ?>><?= l('admin_broadcasts.status.processing') ?></option>
                                    <option value="sent" <?= isset($data->filters->filters['status']) && $data->filters->filters['status'] == 'sent' ? 'selected="selected"' : null ?>><?= l('admin_broadcasts.status.sent') ?></option>
                                </select>
                            </div>

                            <div class="form-group px-4">
                                <label for="filters_order_by" class="small"><?= l('global.filters.order_by') ?></label>
                                <select name="order_by" id="filters_order_by" class="custom-select custom-select-sm">
                                    <option value="broadcast_id" <?= $data->filters->order_by == 'broadcast_id' ? 'selected="selected"' : null ?>><?= l('global.id') ?></option>
                                    <option value="datetime" <?= $data->filters->order_by == 'datetime' ? 'selected="selected"' : null ?>><?= l('global.filters.order_by_datetime') ?></option>
                                    <option value="last_datetime" <?= $data->filters->order_by == 'last_datetime' ? 'selected="selected"' : null ?>><?= l('global.filters.order_by_last_datetime') ?></option>
                                    <option value="name" <?= $data->filters->search_by == 'name' ? 'selected="selected"' : null ?>><?= l('global.name') ?></option>
                                    <option value="sent_emails" <?= $data->filters->search_by == 'sent_emails' ? 'selected="selected"' : null ?>><?= l('admin_broadcasts.sent_emails') ?></option>
                                    <option value="total_emails" <?= $data->filters->search_by == 'total_emails' ? 'selected="selected"' : null ?>><?= l('admin_broadcasts.total_emails') ?></option>
                                    <option value="views" <?= $data->filters->search_by == 'views' ? 'selected="selected"' : null ?>><?= l('admin_broadcasts.views') ?></option>
                                    <option value="clicks" <?= $data->filters->search_by == 'clicks' ? 'selected="selected"' : null ?>><?= l('admin_broadcasts.clicks') ?></option>
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
                            <a href="#" class="dropdown-item" data-toggle="modal" data-target="#bulk_delete_modal"><i class="fas fa-fw fa-sm fa-trash-alt mr-2"></i> <?= l('global.delete') ?></a>
                        </div>
                    </div>

                    <button id="bulk_disable" type="button" class="btn btn-secondary" data-toggle="tooltip" title="<?= l('global.close') ?>"><i class="fas fa-fw fa-times"></i></button>
                </div>
            </div>
        </div>
    </div>

    <?= \Altum\Alerts::output_alerts() ?>

    <form id="table" action="<?= SITE_URL . 'admin/broadcasts/bulk' ?>" method="post" role="form">
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
                    <th><?= l('admin_broadcasts.broadcast') ?></th>
                    <th><?= l('admin_broadcasts.segment') ?></th>
                    <th><?= l('admin_broadcasts.sent_emails') ?></th>
                    <?php if(settings()->main->broadcasts_statistics_is_enabled): ?>
                        <th><?= l('admin_broadcasts.views') ?></th>
                        <th><?= l('admin_broadcasts.clicks') ?></th>
                    <?php endif ?>
                    <th><?= l('global.status') ?></th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach($data->broadcasts as $row): ?>
                    <tr>
                        <td data-bulk-table class="d-none">
                            <div class="custom-control custom-checkbox">
                                <input id="selected_id_<?= $row->broadcast_id ?>" type="checkbox" class="custom-control-input" name="selected[]" value="<?= $row->broadcast_id ?>" />
                                <label class="custom-control-label" for="selected_id_<?= $row->broadcast_id ?>"></label>
                            </div>
                        </td>
                        <td class="text-nowrap">
                            <?php if($row->status == 'draft'): ?>
                                <a href="<?= url('admin/broadcast-update/' . $row->broadcast_id) ?>"><?= $row->name ?></a>
                            <?php elseif(in_array($row->status, ['sent', 'processing'])): ?>
                                <a href="<?= url('admin/broadcast-view/' . $row->broadcast_id) ?>"><?= $row->name ?></a>
                            <?php endif ?>
                        </td>
                        <td class="text-nowrap">
                            <span class="badge badge-light">
                                <?= l('admin_broadcasts.segment.' . $row->segment) ?>
                            </span>
                        </td>
                        <td class="text-nowrap">
                            <span class="badge badge-secondary" data-toggle="tooltip" title="<?= nr(get_percentage_between_two_numbers($row->sent_emails, $row->total_emails)) . '%' ?>">
                                <i class="fas fa-fw fa-sm fa-envelope mr-1"></i> <?= nr($row->sent_emails) . '/' . nr($row->total_emails) ?>
                            </span>
                        </td>

                        <?php if(settings()->main->broadcasts_statistics_is_enabled): ?>
                            <td class="text-nowrap">
                                <a href="<?= url('admin/broadcast-view/' . $row->broadcast_id) ?>" class="badge badge-info" data-toggle="tooltip" title="<?= nr(get_percentage_between_two_numbers($row->views, $row->total_emails)) . '%' ?>">
                                    <i class="fas fa-fw fa-sm fa-eye mr-1"></i> <?= nr($row->views) ?>
                                </a>
                            </td>

                            <td class="text-nowrap">
                                <a href="<?= url('admin/broadcast-view/' . $row->broadcast_id) ?>" class="badge badge-info" data-toggle="tooltip" title="<?= nr(get_percentage_between_two_numbers($row->clicks, $row->total_emails)) . '%' ?>">
                                    <i class="fas fa-fw fa-sm fa-mouse mr-1"></i> <?= nr($row->clicks) ?>
                                </a>
                            </td>
                        <?php endif ?>

                        <td class="text-nowrap">
                            <?php if($row->status == 'draft'): ?>
                                <span class="badge badge-light"><i class="fas fa-fw fa-sm fa-save mr-1"></i> <?= l('admin_broadcasts.status.draft') ?></span>
                            <?php elseif($row->status == 'processing'): ?>
                                <span class="badge badge-warning"><i class="fas fa-fw fa-sm fa-spinner fa-spin mr-1"></i> <?= l('admin_broadcasts.status.processing') ?></span>
                            <?php elseif($row->status == 'sent'): ?>
                                <span class="badge badge-success"><i class="fas fa-fw fa-sm fa-check mr-1"></i> <?= l('admin_broadcasts.status.sent') ?></span>
                            <?php endif ?>
                        </td>
                        <td class="text-nowrap">
                            <div class="d-flex align-items-center">
                                <span class="mr-2" data-toggle="tooltip" data-html="true" title="<?= sprintf(l('admin_broadcasts.last_sent_email_datetime'), ($row->last_sent_email_datetime ? '<br />' . \Altum\Date::get($row->last_sent_email_datetime, 2) . '<br /><small>' . \Altum\Date::get($row->last_sent_email_datetime, 3) . '</small>' : '<br />' . l('global.na'))) ?>">
                                    <i class="fas fa-fw fa-paper-plane text-muted"></i>
                                </span>

                                <span class="mr-2" data-toggle="tooltip" data-html="true" title="<?= sprintf(l('global.datetime_tooltip'), '<br />' . \Altum\Date::get($row->datetime, 2) . '<br /><small>' . \Altum\Date::get($row->datetime, 3) . '</small>' . '<br /><small>(' . \Altum\Date::get_timeago($row->datetime) . ')</small>') ?>">
                                    <i class="fas fa-fw fa-calendar text-muted"></i>
                                </span>

                                <span class="mr-2" data-toggle="tooltip" data-html="true" title="<?= sprintf(l('global.last_datetime_tooltip'), ($row->last_datetime ? '<br />' . \Altum\Date::get($row->last_datetime, 2) . '<br /><small>' . \Altum\Date::get($row->last_datetime, 3) . '</small>' . '<br /><small>(' . \Altum\Date::get_timeago($row->last_datetime) . ')</small>' : '<br />' . l('global.na'))) ?>">
                                    <i class="fas fa-fw fa-history text-muted"></i>
                                </span>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex justify-content-end">
                                <?= include_view(THEME_PATH . 'views/admin/broadcasts/admin_broadcast_dropdown_button.php', ['id' => $row->broadcast_id, 'resource_name' => $row->name]) ?>
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

    <?= \Altum\Alerts::output_alerts() ?>

    <div class="card">
        <div class="card-body">
            <div class="d-flex flex-column flex-md-row align-items-md-center">
                <div class="mb-3 mb-md-0 mr-md-5">
                    <i class="fas fa-fw fa-7x fa-mail-bulk text-primary-200"></i>
                </div>

                <div class="d-flex flex-column">
                    <h1 class="h3 m-0"><?= l('admin_broadcasts.header_no_data') ?></h1>
                    <p class="text-muted"><?= l('admin_broadcasts.subheader_no_data') ?></p>

                    <div>
                        <a href="<?= url('admin/broadcast-create') ?>" class="btn btn-primary text-nowrap"><i class="fas fa-fw fa-plus-circle fa-sm mr-1"></i> <?= l('admin_broadcasts.create') ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php endif ?>

<?php require THEME_PATH . 'views/partials/js_bulk.php' ?>
<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/partials/bulk_delete_modal.php'), 'modals'); ?>
