<?php defined('ALTUMCODE') || die() ?>

<div>
    <div class="alert alert-info mb-3"><?= l('admin_settings.custom_images.info') ?></div>

    <?php
    $images_keys = [
        'index/hero.png',
        'index/dynamic.png',
        'index/privacy.png',
        'index/static.png',
    ];
    ?>

    <?php foreach($images_keys as $image_key): ?>
        <?php $image_key_id = str_replace('.', '_', get_slug($image_key)) ?>

        <div class="form-group" data-file-image-input-wrapper data-file-input-wrapper-size-limit="<?= get_max_upload() ?>" data-file-input-wrapper-size-limit-error="<?= sprintf(l('global.error_message.file_size_limit'), get_max_upload()) ?>">
            <label for="<?= $image_key_id ?>" class="d-flex align-items-center justify-content-between">
                <div><i class="fas fa-fw fa-sm fa-image text-muted mr-1"></i> <?= $image_key ?></div>

                <a href="<?= ASSETS_FULL_URL . 'images/' . $image_key ?>" target="_blank" class="text-muted"><i class="fas fa-fw fa-sm fa-external-link-alt ml-1"></i></a>
            </label>
            <?= include_view(THEME_PATH . 'views/partials/file_image_input.php', ['uploads_file_key' => 'custom_images', 'file_key' => $image_key_id , 'already_existing_image' => settings()->custom_images->{$image_key_id} ?? null]) ?>
            <small class="form-text text-muted"><?= sprintf(l('global.accessibility.whitelisted_file_extensions'), \Altum\Uploads::get_whitelisted_file_extensions_accept('custom_images')) . ' ' . sprintf(l('global.accessibility.file_size_limit'), get_max_upload()) ?></small>
        </div>
    <?php endforeach ?>
</div>

<button type="submit" name="submit" class="btn btn-lg btn-block btn-primary mt-4"><?= l('global.update') ?></button>

