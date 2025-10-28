<?php defined('ALTUMCODE') || die() ?>

<div class="dropdown">
    <button type="button" class="btn btn-link <?= $data->button_text_class ?? 'text-secondary' ?> dropdown-toggle dropdown-toggle-simple" data-toggle="dropdown" data-boundary="viewport">
        <i class="fas fa-fw fa-ellipsis-v"></i>
    </button>

    <div class="dropdown-menu dropdown-menu-right">
        <?php if(str_ends_with($data->id, '.webp')): ?>
        <a class="dropdown-item" href="<?= \Altum\Uploads::get_full_url('dynamic_og_images') . $data->id ?>" target="_blank"><i class="fas fa-fw fa-sm fa-eye mr-2"></i> <?= l('global.view') ?></a>
        <a class="dropdown-item" href="<?= \Altum\Uploads::get_full_url('dynamic_og_images') . $data->id ?>" download="<?= $data->id ?>" target="_blank"><i class="fas fa-fw fa-sm fa-download mr-2"></i> <?= l('global.download') ?></a>
        <?php endif ?>
        <a href="#" data-toggle="modal" data-target="#dynamic_og_image_delete_modal" data-dynamic-og-image-id="<?= $data->id ?>" data-resource-name="<?= $data->resource_name ?>" class="dropdown-item"><i class="fas fa-fw fa-sm fa-trash-alt mr-2"></i> <?= l('global.delete') ?></a>
    </div>
</div>

<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/partials/universal_delete_modal_url.php', [
    'name' => 'dynamic_og_image',
    'resource_id' => 'dynamic_og_image_id',
    'has_dynamic_resource_name' => true,
    'path' => 'admin/dynamic-og-images/delete/'
]), 'modals', 'dynamic_og_image_delete_modal'); ?>
