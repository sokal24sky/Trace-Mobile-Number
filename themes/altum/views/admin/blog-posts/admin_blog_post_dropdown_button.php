<?php defined('ALTUMCODE') || die() ?>

<div class="dropdown">
    <button type="button" class="btn btn-link <?= $data->button_text_class ?? 'text-secondary' ?> dropdown-toggle dropdown-toggle-simple" data-toggle="dropdown" data-boundary="viewport">
        <i class="fas fa-fw fa-ellipsis-v"></i>
    </button>

    <div class="dropdown-menu dropdown-menu-right">
        <a class="dropdown-item" href="<?= SITE_URL . ($data->language ? \Altum\Language::$active_languages[$data->language] . '/' : null) . 'blog/' . $data->url ?>" target="_blank" rel="noreferrer"><i class="fas fa-fw fa-sm fa-eye mr-2"></i> <?= l('global.view') ?></a>
        <a class="dropdown-item" href="admin/blog-post-update/<?= $data->id ?>"><i class="fas fa-fw fa-sm fa-pencil-alt mr-2"></i> <?= l('global.edit') ?></a>
        <a href="#" data-toggle="modal" data-target="#blog_post_delete_modal" data-blog-post-id="<?= $data->id ?>" data-resource-name="<?= $data->resource_name ?>" class="dropdown-item"><i class="fas fa-fw fa-sm fa-trash-alt mr-2"></i> <?= l('global.delete') ?></a>
    </div>
</div>
