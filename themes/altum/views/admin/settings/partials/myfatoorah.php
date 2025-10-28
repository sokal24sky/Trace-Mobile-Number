<?php defined('ALTUMCODE') || die() ?>

<div>
    <?php if(!in_array(settings()->license->type, ['Extended License', 'extended'])): ?>
        <div class="alert alert-primary" role="alert">
            You need to own the Extended License in order to activate the payment system.
        </div>
    <?php endif ?>

    <div class="<?= !in_array(settings()->license->type, ['Extended License', 'extended']) ? 'container-disabled' : null ?>">
        <div class="alert alert-info mb-3"><?= sprintf(l('admin_settings.documentation'), '<a href="' . PRODUCT_DOCUMENTATION_URL . '#' . \Altum\Router::$method . '" target="_blank">', '</a>') ?></div>
        <div class="form-group custom-control custom-switch">
            <input id="is_enabled" name="is_enabled" type="checkbox" class="custom-control-input" <?= settings()->myfatoorah->is_enabled ? 'checked="checked"' : null?>>
            <label class="custom-control-label" for="is_enabled"><?= l('admin_settings.myfatoorah.is_enabled') ?></label>
        </div>

        <div class="form-group">
            <label for="api_endpoint"><?= l('admin_settings.myfatoorah.api_endpoint') ?></label>
            <select id="api_endpoint" name="api_endpoint" class="custom-select">
                <option value="api.myfatoorah.com" <?= settings()->myfatoorah->api_endpoint == 'api.myfatoorah.com' ? 'selected="selected"' : null ?>>api.myfatoorah.com</option>
                <option value="api-sa.myfatoorah.com" <?= settings()->myfatoorah->api_endpoint == 'api-sa.myfatoorah.com' ? 'selected="selected"' : null ?>>api-sa.myfatoorah.com</option>
                <option value="api-qa.myfatoorah.com" <?= settings()->myfatoorah->api_endpoint == 'api-qa.myfatoorah.com' ? 'selected="selected"' : null ?>>api-qa.myfatoorah.com</option>
                <option value="api-eg.myfatoorah.com" <?= settings()->myfatoorah->api_endpoint == 'api-eg.myfatoorah.com' ? 'selected="selected"' : null ?>>api-eg.myfatoorah.com</option>
                <option value="apitest.myfatoorah.com" <?= settings()->myfatoorah->api_endpoint == 'apitest.myfatoorah.com' ? 'selected="selected"' : null ?>>apitest.myfatoorah.com</option>
            </select>
        </div>

        <div class="form-group">
            <label for="api_key"><?= l('admin_settings.myfatoorah.api_key') ?></label>
            <input id="api_key" type="text" name="api_key" class="form-control" value="<?= settings()->myfatoorah->api_key ?>" />
        </div>

        <div class="form-group">
            <label for="secret_key"><?= l('admin_settings.myfatoorah.secret_key') ?></label>
            <input id="secret_key" type="text" name="secret_key" class="form-control" value="<?= settings()->myfatoorah->secret_key ?>" />
        </div>

        <div class="form-group">
            <label><i class="fas fa-fw fa-sm fa-coins text-muted mr-1"></i> <?= l('admin_settings.payment.currencies') ?></label>
            <div class="row">
                <?php foreach((array) settings()->payment->currencies as $currency => $currency_data): ?>
                    <div class="col-12 col-lg-4">
                        <div class="custom-control custom-checkbox my-2">
                            <input id="<?= 'currency_' . $currency ?>" name="currencies[]" value="<?= $currency ?>" type="checkbox" class="custom-control-input" <?= in_array($currency, settings()->myfatoorah->currencies ?? []) ? 'checked="checked"' : null ?>>
                            <label class="custom-control-label d-flex align-items-center" for="<?= 'currency_' . $currency ?>">
                                <span><?= $currency ?></span>
                            </label>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>
        </div>

        <div class="form-group">
            <label for="webhook_url"><i class="fas fa-fw fa-sm fa-link text-muted mr-1"></i> <?= l('admin_settings.payment.webhook_url') ?></label>
            <input type="text" id="webhook_url" value="<?= SITE_URL . 'webhook-myfatoorah' ?>" class="form-control" onclick="this.select();" readonly="readonly" />
        </div>
    </div>
</div>

<button type="submit" name="submit" class="btn btn-lg btn-block btn-primary mt-4"><?= l('global.update') ?></button>
