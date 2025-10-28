<?php defined('ALTUMCODE') || die() ?>

<div class="d-flex flex-column flex-md-row justify-content-between mb-4">
    <h1 class="h3 mb-3 mb-md-0 text-truncate"><i class="fas fa-fw fa-xs fa-x-ray text-primary-900 mr-2"></i> <?= l('admin_dynamic_og_images.header') ?></h1>

    <div class="d-flex position-relative d-print-none">
        <div>
            <a href="<?= url('admin/statistics/dynamic_og_images') ?>" class="btn btn-gray-300" data-tooltip title="<?= l('global.statistics') ?>">
                <i class="fas fa-fw fa-sm fa-chart-bar"></i>
            </a>
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

<form id="table" action="<?= SITE_URL . 'admin/dynamic-og-images/bulk' ?>" method="post" role="form">
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
                <th><?= l('global.status') ?></th>
                <th><?= l('admin_dynamic_og_images.size') ?></th>
                <th><?= l('admin_dynamic_og_images.last_modified') ?></th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php $i = 1; ?>
            <?php $total_size = 0; ?>
            <?php foreach($data->images as $row): ?>
                <tr>
                    <td data-bulk-table class="d-none">
                        <div class="custom-control custom-checkbox">
                            <input id="selected_id_<?= $row->full_name ?>" type="checkbox" class="custom-control-input" name="selected[]" value="<?= $row->full_name ?>" />
                            <label class="custom-control-label" for="selected_id_<?= $row->full_name ?>"></label>
                        </div>
                    </td>

                    <td class="text-nowrap">
                        <div class="d-flex align-items-center">
                            <?php if($row->status == 'pending'): ?>
                                <div class="user-avatar rounded-circle bg-gray-200 mr-3"></div>
                            <?php else: ?>
                                <a href="<?= \Altum\Uploads::get_full_url('dynamic_og_images') . $row->full_name ?>" target="_blank">
                                    <img src="<?= \Altum\Uploads::get_full_url('dynamic_og_images') . $row->full_name ?>" class="user-avatar rounded-circle mr-3" alt="" loading="lazy" />
                                </a>
                            <?php endif ?>

                            <div>
                                <span data-toggle="tooltip" title="<?= $row->name ?>"><?= string_truncate($row->name, 30) ?></span>

                                <?php if($row->status == 'processed'): ?>
                                    <a href="<?= \Altum\Uploads::get_full_url('dynamic_og_images') . $row->full_name ?>" target="_blank">
                                        <i class="fas fa-fw fa-xs fa-external-link-alt text-muted ml-1"></i>
                                    </a>
                                <?php endif ?>
                            </div>
                        </div>
                    </td>

                    <td class="text-nowrap">
                        <?php if($row->status == 'pending'): ?>
                            <span class="badge badge-warning"><i class="fas fa-fw fa-sm fa-spinner fa-spin mr-1"></i> <?= l('admin_dynamic_og_images.pending') ?></span>
                        <?php elseif($row->status == 'processed'): ?>
                            <span class="badge badge-success"><i class="fas fa-fw fa-sm fa-check mr-1"></i> <?= l('admin_dynamic_og_images.processed') ?></span>
                        <?php endif ?>
                    </td>

                    <td class="text-nowrap">
                        <span class="badge badge-light">
                            <i class="fas fa-fw fa-sm fa-hdd mr-1"></i>
                            <?= get_formatted_bytes($row->size) ?>
                            <?php $total_size += $row->size ?>
                        </span>
                    </td>

                    <td class="text-nowrap">
                        <span class="text-muted" data-toggle="tooltip" title="<?= \Altum\Date::get($row->last_modified) ?>">
                            <?= \Altum\Date::get_timeago($row->last_modified) ?>
                        </span>
                    </td>

                    <td>
                        <div class="d-flex justify-content-end">
                            <?= include_view(THEME_PATH . 'views/admin/dynamic-og-images/admin_dynamic_og_image_dropdown_button.php', ['id' => $row->full_name, 'resource_name' => $row->full_name]) ?>
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
