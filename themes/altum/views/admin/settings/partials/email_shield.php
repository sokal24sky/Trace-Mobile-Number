<?php defined('ALTUMCODE') || die() ?>

<div>
    <div <?= !\Altum\Plugin::is_active('email-shield') ? 'data-toggle="tooltip" title="' . sprintf(l('admin_plugins.no_access'), \Altum\Plugin::get('email-shield')->name ?? 'email-shield') . '"' : null ?>>
        <div class="<?= !\Altum\Plugin::is_active('email-shield') ? 'container-disabled' : null ?>">
            <div class="form-group custom-control custom-switch">
                <input id="is_enabled" name="is_enabled" type="checkbox" class="custom-control-input" <?= \Altum\Plugin::is_active('email-shield') && settings()->email_shield->is_enabled ? 'checked="checked"' : null?>>
                <label class="custom-control-label" for="is_enabled"><?= l('admin_settings.email_shield.is_enabled') ?></label>
            </div>

            <div class="form-group custom-control custom-switch">
                <input id="statistics_is_enabled" name="statistics_is_enabled" type="checkbox" class="custom-control-input" <?= \Altum\Plugin::is_active('email-shield') && settings()->email_shield->statistics_is_enabled ? 'checked="checked"' : null?>>
                <label class="custom-control-label" for="statistics_is_enabled"><?= l('admin_settings.email_shield.statistics_is_enabled') ?></label>
                <small class="form-text text-muted"><?= l('admin_settings.email_shield.statistics_is_enabled_help') ?></small>
            </div>

            <div class="form-group">
                <label for="email_shield_api_key"><i class="fas fa-fw fa-sm fa-key text-muted mr-1"></i> <?= l('admin_settings.email_shield.email_shield_api_key') ?></label>
                <input id="email_shield_api_key" type="text" name="email_shield_api_key" class="form-control" value="<?= settings()->email_shield->email_shield_api_key ?>" />
                <small class="form-text text-muted"><?= l('admin_settings.email_shield.email_shield_api_key_help') ?></small>
                <small class="form-text text-muted"><?= l('admin_settings.email_shield.email_shield_api_key_help2') ?></small>
            </div>

            <div class="form-group">
                <label for="whitelisted_domains"><i class="fas fa-fw fa-sm fa-check-circle text-muted mr-1"></i> <?= l('admin_settings.email_shield.whitelisted_domains') ?></label>
                <textarea id="whitelisted_domains" name="whitelisted_domains" class="form-control"><?= implode(',', settings()->email_shield->whitelisted_domains ?? []) ?></textarea>
                <small class="form-text text-muted"><?= l('admin_settings.email_shield.whitelisted_domains_help') ?></small>
            </div>

        </div>
    </div>
</div>

<?php if(\Altum\Plugin::is_active('email-shield')): ?>
    <button type="submit" name="submit" class="btn btn-lg btn-block btn-primary mt-4"><?= l('global.update') ?></button>
<?php endif ?>

<?php ob_start() ?>
<script>
    'use strict';
    
const is_enabled = document.getElementById('is_enabled');
    const email_shield_api_key = document.getElementById('email_shield_api_key');

    /* function to toggle required attributes */
    const toggle_required_fields = () => {
        if (is_enabled.checked) {
            email_shield_api_key.setAttribute('required', 'required');
        } else {
            email_shield_api_key.removeAttribute('required');
        }
    };

    /* run on page load */
    toggle_required_fields();

    /* run on checkbox toggle */
    is_enabled.addEventListener('change', toggle_required_fields);
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
