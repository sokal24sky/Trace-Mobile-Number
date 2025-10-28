<?php defined('ALTUMCODE') || die() ?>

<div>
    <div class="alert alert-info mb-3"><?= sprintf(l('admin_settings.documentation'), '<a href="' . PRODUCT_DOCUMENTATION_URL . '#social-logins" target="_blank">', '</a>') ?></div>
    
    <div class="form-group custom-control custom-switch">
        <input id="is_enabled" name="is_enabled" type="checkbox" class="custom-control-input" <?= settings()->discord->is_enabled ? 'checked="checked"' : null?>>
        <label class="custom-control-label" for="is_enabled"><?= l('admin_settings.discord.is_enabled') ?></label>
    </div>

    <div class="form-group">
        <label for="client_id"><?= l('admin_settings.discord.client_id') ?></label>
        <input id="client_id" type="text" name="client_id" class="form-control" value="<?= settings()->discord->client_id ?>" />
    </div>

    <div class="form-group">
        <label for="client_secret"><?= l('admin_settings.discord.client_secret') ?></label>
        <input id="client_secret" type="text" name="client_secret" class="form-control" value="<?= settings()->discord->client_secret ?>" />
    </div>

    <div class="form-group">
        <label for="callback_url"><i class="fas fa-fw fa-sm fa-link text-muted mr-1"></i> <?= l('admin_settings.social_logins.callback_url') ?></label>
        <input type="text" id="callback_url" value="<?= SITE_URL . 'login/discord' ?>" class="form-control" onclick="this.select();" readonly="readonly" />
    </div>
</div>

<button type="submit" name="submit" class="btn btn-lg btn-block btn-primary mt-4"><?= l('global.update') ?></button>
