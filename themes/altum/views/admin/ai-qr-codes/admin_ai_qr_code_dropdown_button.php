<?php defined('ALTUMCODE') || die() ?>

<div class="dropdown">
    <button type="button" class="btn btn-link <?= $data->button_text_class ?? 'text-secondary' ?> dropdown-toggle dropdown-toggle-simple" data-toggle="dropdown" data-boundary="viewport">
        <i class="fas fa-fw fa-ellipsis-v"></i>
    </button>

    <div class="dropdown-menu dropdown-menu-right">
        <a href="#" data-toggle="modal" data-target="#ai_qr_code_transfer_modal" data-ai-qr-code-id="<?= $data->id ?>" data-resource-name="<?= $data->resource_name ?>" class="dropdown-item"><i class="fas fa-fw fa-sm fa-shuffle mr-2"></i> <?= l('global.transfer') ?></a>

        <a href="#" data-toggle="modal" data-target="#ai_qr_code_delete_modal" data-ai-qr-code-id="<?= $data->id ?>" data-resource-name="<?= $data->resource_name ?>" class="dropdown-item"><i class="fas fa-fw fa-sm fa-trash-alt mr-2"></i> <?= l('global.delete') ?></a>
    </div>
</div>

<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/partials/universal_delete_modal_url.php', [
    'name' => 'ai_qr_code',
    'resource_id' => 'ai_qr_code_id',
    'has_dynamic_resource_name' => true,
    'path' => 'admin/ai-qr-codes/delete/'
]), 'modals', 'ai_qr_code_delete_modal'); ?>

<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/partials/transfer_modal.php', [
    'name' => 'ai_qr_code',
    'resource_id' => 'ai_qr_code_id',
    'has_dynamic_resource_name' => true,
    'path' => 'admin/ai-qr-codes/transfer/'
]), 'modals', 'ai_qr_code_transfer_modal'); ?>
