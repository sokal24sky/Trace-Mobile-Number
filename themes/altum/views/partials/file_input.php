<?php defined('ALTUMCODE') || die() ?>

<div class="row">
    <div class="col">
        <input id="<?= $data->file_key ?>" type="file" name="<?= $data->file_key ?>" accept="<?= \Altum\Uploads::get_whitelisted_file_extensions_accept($data->uploads_file_key) ?>" class="form-control-file altum-file-input" <?= $data->input_data ?? null ?> <?= ($data->is_required ?? false) ? 'required="required"' : null ?> />
    </div>

    <div id="<?= $data->file_key . '_preview' ?>" class="col-3 <?= !empty($data->already_existing_file) ? null : 'd-none' ?>">
        <a href="<?= $data->already_existing_file ? \Altum\Uploads::get_full_url($data->uploads_file_key) . $data->already_existing_file : '#' ?>" id="file_url" target="_blank" data-toggle="tooltip" title="<?= l('global.view') ?>" data-tooltip-hide-on-click>
            <div class="card h-100 d-flex justify-content-center align-items-center bg-gray-100">
                <div class="card-body">
                    <i class="fas fa-fw fa-external-link"></i>
                </div>
            </div>
        </a>
    </div>

    <div id="<?= $data->file_key . '_remove_wrapper' ?>" class="col-12 <?= !empty($data->already_existing_file) ? null : 'd-none' ?>">
        <div class="custom-control custom-checkbox my-2">
            <input id="<?= $data->file_key . '_remove' ?>" name="<?= $data->file_key . '_remove' ?>" type="checkbox" class="custom-control-input" <?= $data->input_data ?? null ?>>
            <label class="custom-control-label" for="<?= $data->file_key . '_remove' ?>">
                <span class="text-muted"><?= l('global.delete_file') ?></span>
            </label>
        </div>
    </div>

    <div id="<?= $data->file_key . '_remove_selected_file_wrapper' ?>" class="col-12 d-none">
        <label href="#" role="button" id="<?= $data->file_key . '_remove_selected_file' ?>" type="button" class="my-2 text-muted text-decoration-none">
            <i class="fas fa-fw fa-sm fa-trash-alt mr-1"></i> <?= l('global.remove_selected_file') ?>
        </label>
    </div>
</div>
