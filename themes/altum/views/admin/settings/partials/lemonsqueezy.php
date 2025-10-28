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
            <input id="is_enabled" name="is_enabled" type="checkbox" class="custom-control-input" <?= settings()->lemonsqueezy->is_enabled ? 'checked="checked"' : null?>>
            <label class="custom-control-label" for="is_enabled"><?= l('admin_settings.lemonsqueezy.is_enabled') ?></label>
        </div>

        <div class="form-group">
            <label for="api_key"><?= l('admin_settings.lemonsqueezy.api_key') ?></label>
            <input id="api_key" type="text" name="api_key" class="form-control" value="<?= settings()->lemonsqueezy->api_key ?>" />
        </div>

        <div class="form-group">
            <label for="signing_secret"><?= l('admin_settings.lemonsqueezy.signing_secret') ?></label>
            <input id="signing_secret" type="text" name="signing_secret" class="form-control" value="<?= settings()->lemonsqueezy->signing_secret ?>" />
        </div>

        <div class="form-group">
            <label for="store_id"><?= l('admin_settings.lemonsqueezy.store_id') ?></label>
            <input id="store_id" type="text" name="store_id" class="form-control" value="<?= settings()->lemonsqueezy->store_id ?>" />
        </div>

        <div class="form-group">
            <label for="one_time_monthly_variant_id"><?= l('admin_settings.lemonsqueezy.one_time_monthly_variant_id') ?></label>
            <input id="one_time_monthly_variant_id" type="text" name="one_time_monthly_variant_id" class="form-control" value="<?= settings()->lemonsqueezy->one_time_monthly_variant_id ?>" />
        </div>

        <div class="form-group">
            <label for="one_time_quarterly_variant_id"><?= l('admin_settings.lemonsqueezy.one_time_quarterly_variant_id') ?></label>
            <input id="one_time_quarterly_variant_id" type="text" name="one_time_quarterly_variant_id" class="form-control" value="<?= settings()->lemonsqueezy->one_time_quarterly_variant_id ?>" />
        </div>

        <div class="form-group">
            <label for="one_time_biannual_variant_id"><?= l('admin_settings.lemonsqueezy.one_time_biannual_variant_id') ?></label>
            <input id="one_time_biannual_variant_id" type="text" name="one_time_biannual_variant_id" class="form-control" value="<?= settings()->lemonsqueezy->one_time_biannual_variant_id ?>" />
        </div>

        <div class="form-group">
            <label for="one_time_annual_variant_id"><?= l('admin_settings.lemonsqueezy.one_time_annual_variant_id') ?></label>
            <input id="one_time_annual_variant_id" type="text" name="one_time_annual_variant_id" class="form-control" value="<?= settings()->lemonsqueezy->one_time_annual_variant_id ?>" />
        </div>

        <div class="form-group">
            <label for="one_time_lifetime_variant_id"><?= l('admin_settings.lemonsqueezy.one_time_lifetime_variant_id') ?></label>
            <input id="one_time_lifetime_variant_id" type="text" name="one_time_lifetime_variant_id" class="form-control" value="<?= settings()->lemonsqueezy->one_time_lifetime_variant_id ?>" />
        </div>


        <div class="form-group">
            <label for="recurring_monthly_variant_id"><?= l('admin_settings.lemonsqueezy.recurring_monthly_variant_id') ?></label>
            <input id="recurring_monthly_variant_id" type="text" name="recurring_monthly_variant_id" class="form-control" value="<?= settings()->lemonsqueezy->recurring_monthly_variant_id ?>" />
        </div>

        <div class="form-group">
            <label for="recurring_quarterly_variant_id"><?= l('admin_settings.lemonsqueezy.recurring_quarterly_variant_id') ?></label>
            <input id="recurring_quarterly_variant_id" type="text" name="recurring_quarterly_variant_id" class="form-control" value="<?= settings()->lemonsqueezy->recurring_quarterly_variant_id ?>" />
        </div>

        <div class="form-group">
            <label for="recurring_biannual_variant_id"><?= l('admin_settings.lemonsqueezy.recurring_biannual_variant_id') ?></label>
            <input id="recurring_biannual_variant_id" type="text" name="recurring_biannual_variant_id" class="form-control" value="<?= settings()->lemonsqueezy->recurring_biannual_variant_id ?>" />
        </div>

        <div class="form-group">
            <label for="recurring_annual_variant_id"><?= l('admin_settings.lemonsqueezy.recurring_annual_variant_id') ?></label>
            <input id="recurring_annual_variant_id" type="text" name="recurring_annual_variant_id" class="form-control" value="<?= settings()->lemonsqueezy->recurring_annual_variant_id ?>" />
        </div>


        <div class="form-group">
            <label><i class="fas fa-fw fa-sm fa-coins text-muted mr-1"></i> <?= l('admin_settings.payment.currencies') ?></label>
            <div class="row">
                <?php foreach((array) settings()->payment->currencies as $currency => $currency_data): ?>
                    <div class="col-12 col-lg-4">
                        <div class="custom-control custom-checkbox my-2">
                            <input id="<?= 'currency_' . $currency ?>" name="currencies[]" value="<?= $currency ?>" type="checkbox" class="custom-control-input" <?= in_array($currency, settings()->lemonsqueezy->currencies ?? []) ? 'checked="checked"' : null ?>>
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
            <input type="text" id="webhook_url" value="<?= SITE_URL . 'webhook-lemonsqueezy' ?>" class="form-control" onclick="this.select();" readonly="readonly" />
        </div>
    </div>
</div>

<button type="submit" name="submit" class="btn btn-lg btn-block btn-primary mt-4"><?= l('global.update') ?></button>
