<?php defined('ALTUMCODE') || die() ?>

<div class="dropdown">
    <button type="button" class="btn btn-link <?= $data->button_text_class ?? 'text-secondary' ?> dropdown-toggle dropdown-toggle-simple" data-toggle="dropdown" data-boundary="viewport">
        <i class="fas fa-fw fa-ellipsis-v"></i>
    </button>

    <div class="dropdown-menu dropdown-menu-right">
        <a href="#" data-toggle="modal" data-target="#barcode_transfer_modal" data-barcode-id="<?= $data->id ?>" data-resource-name="<?= $data->resource_name ?>" class="dropdown-item"><i class="fas fa-fw fa-sm fa-shuffle mr-2"></i> <?= l('global.transfer') ?></a>

        <a href="#" data-toggle="modal" data-target="#barcode_delete_modal" data-barcode-id="<?= $data->id ?>" data-resource-name="<?= $data->resource_name ?>" class="dropdown-item"><i class="fas fa-fw fa-sm fa-trash-alt mr-2"></i> <?= l('global.delete') ?></a>
    </div>
</div>

<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/partials/universal_delete_modal_url.php', [
    'name' => 'barcode',
    'resource_id' => 'barcode_id',
    'has_dynamic_resource_name' => true,
    'path' => 'admin/barcodes/delete/'
]), 'modals', 'barcode_delete_modal'); ?>

<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/partials/transfer_modal.php', [
    'name' => 'barcode',
    'resource_id' => 'barcode_id',
    'has_dynamic_resource_name' => true,
    'path' => 'admin/barcodes/transfer/'
]), 'modals', 'barcode_transfer_modal'); ?>
