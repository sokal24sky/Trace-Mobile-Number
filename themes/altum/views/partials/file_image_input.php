<?php defined('ALTUMCODE') || die() ?>

<div class="row">
    <div class="col">
        <input id="<?= $data->file_key ?>" type="file" name="<?= $data->file_key ?>" accept="<?= \Altum\Uploads::get_whitelisted_file_extensions_accept($data->uploads_file_key) ?>" class="form-control-file altum-file-input" <?= $data->input_data ?? null ?> />
    </div>

    <div id="<?= $data->file_key . '_preview' ?>" class="col-3 <?= !empty($data->already_existing_image) ? null : 'd-none' ?>">
        <div class="d-flex justify-content-center align-items-center">
            <a href="<?= $data->already_existing_image ? \Altum\Uploads::get_full_url($data->uploads_file_key) . $data->already_existing_image : '#' ?>" target="_blank" data-toggle="tooltip" title="<?= l('global.view') ?>" data-tooltip-hide-on-click>
                <img src="<?= $data->already_existing_image ? \Altum\Uploads::get_full_url($data->uploads_file_key) . $data->already_existing_image : null ?>" class="altum-file-input-preview rounded <?= !empty($data->already_existing_image) ? null : 'd-none' ?>" loading="lazy" onerror="image_on_error(this)" />
            </a>
        </div>
    </div>

    <div id="<?= $data->file_key . '_remove_wrapper' ?>" class="col-12 <?= !empty($data->already_existing_image) ? null : 'd-none' ?>">
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

<?php ob_start() ?>
<script>
    'use strict';
    
    let image_on_error = (image) => {
        const randomColor = `#${Math.floor(Math.random() * 16777215).toString(16).padStart(6, '0')}`;
        const svgImage = `
                <svg xmlns="http://www.w3.org/2000/svg" width="68" height="68">
                    <rect width="100%" height="100%" fill="${randomColor}" />
                    <text x="50%" y="50%" font-size="10" fill="white" font-family="Arial, sans-serif" text-anchor="middle" dominant-baseline="middle">
                        <?= l('global.image_error') ?>
                    </text>
                </svg>
            `;
        if(image.getAttribute('src')) {
            image.src = `data:image/svg+xml;base64,${btoa(svgImage)}`;
        }
    }
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'head', 'image_on_error') ?>
