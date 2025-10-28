<?php defined('ALTUMCODE') || die() ?>

<div>
    <div <?= !\Altum\Plugin::is_active('image-optimizer') ? 'data-toggle="tooltip" title="' . sprintf(l('admin_plugins.no_access'), \Altum\Plugin::get('image-optimizer')->name ?? 'image-optimizer') . '"' : null ?>>
        <div class="<?= !\Altum\Plugin::is_active('image-optimizer') ? 'container-disabled' : null ?>">
            <div class="form-group custom-control custom-switch">
                <input id="is_enabled" name="is_enabled" type="checkbox" class="custom-control-input" <?= \Altum\Plugin::is_active('image-optimizer') && settings()->image_optimizer->is_enabled ? 'checked="checked"' : null?>>
                <label class="custom-control-label" for="is_enabled"><?= l('admin_settings.image_optimizer.is_enabled') ?></label>
            </div>

            <div class="form-group custom-control custom-switch">
                <input id="statistics_is_enabled" name="statistics_is_enabled" type="checkbox" class="custom-control-input" <?= \Altum\Plugin::is_active('image-optimizer') && settings()->image_optimizer->statistics_is_enabled ? 'checked="checked"' : null?>>
                <label class="custom-control-label" for="statistics_is_enabled"><?= l('admin_settings.image_optimizer.statistics_is_enabled') ?></label>
                <small class="form-text text-muted"><?= l('admin_settings.image_optimizer.statistics_is_enabled_help') ?></small>
            </div>

            <div class="form-group">
                <label for="provider"><i class="fas fa-fw fa-sm fa-fingerprint text-muted mr-1"></i> <?= l('admin_settings.image_optimizer.provider') ?></label>
                <select id="provider" name="provider" class="custom-select">
                    <option value="local" <?= settings()->image_optimizer->provider == 'local' ? 'selected="selected"' : null ?>><?= l('admin_settings.image_optimizer.provider.local') ?></option>
                    <option value="resmushit" <?= settings()->image_optimizer->provider == 'resmushit' ? 'selected="selected"' : null ?>><?= l('admin_settings.image_optimizer.provider.resmushit') ?></option>
                    <option value="imagerypro" <?= settings()->image_optimizer->provider == 'imagerypro' ? 'selected="selected"' : null ?>><?= l('admin_settings.image_optimizer.provider.imagerypro') ?></option>
                </select>
                <small class="form-text text-muted" data-provider="local"><?= l('admin_settings.image_optimizer.provider.local_help') ?></small>
                <small class="form-text text-muted" data-provider="resmushit"><?= l('admin_settings.image_optimizer.provider.resmushit_help') ?></small>
                <small class="form-text text-muted" data-provider="imagerypro"><?= l('admin_settings.image_optimizer.provider.imagerypro_help') ?></small>
                <small class="form-text text-muted" data-provider="imagerypro"><?= l('admin_settings.image_optimizer.provider.imagerypro_help2') ?></small>
            </div>

            <div class="form-group" data-provider="imagerypro">
                <label for="imagerypro_api_key"><i class="fas fa-fw fa-sm fa-key text-muted mr-1"></i> <?= l('admin_settings.image_optimizer.imagerypro_api_key') ?></label>
                <input id="imagerypro_api_key" type="text" name="imagerypro_api_key" class="form-control" value="<?= settings()->image_optimizer->imagerypro_api_key ?>" />
                <small class="form-text text-muted" data-provider="imagerypro"><?= l('admin_settings.image_optimizer.imagerypro_api_key_help') ?></small>
            </div>

            <div class="form-group">
                <label for="quality"><i class="fas fa-fw fa-sm fa-image text-muted mr-1"></i> <?= l('admin_settings.image_optimizer.quality') ?></label>
                <div class="input-group">
                    <input id="quality" name="quality" type="number" min="50" max="100" class="form-control" value="<?= settings()->image_optimizer->quality ?? 75 ?>" />
                    <div class="input-group-append">
                        <span class="input-group-text">
                            %
                        </span>
                    </div>
                </div>
                <small class="form-text text-muted"><?= l('admin_settings.image_optimizer.quality_help') ?></small>
            </div>
        </div>
    </div>
</div>

<?php if(\Altum\Plugin::is_active('image-optimizer')): ?>
    <button type="submit" name="submit" class="btn btn-lg btn-block btn-primary mt-4"><?= l('global.update') ?></button>
<?php endif ?>

<?php ob_start() ?>
<script>
    'use strict';
    
    type_handler('#provider', 'data-provider');
    document.querySelector('#provider') && document.querySelectorAll('#provider').forEach(element => element.addEventListener('change', () => { type_handler('#provider', 'data-provider'); }));
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
