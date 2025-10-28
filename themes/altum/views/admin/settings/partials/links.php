<?php defined('ALTUMCODE') || die() ?>

<div>
    <div class="form-group">
        <label for="random_url_length"><?= l('admin_settings.links.random_url_length') ?></label>
        <input id="random_url_length" type="number" min="4" step="1" name="random_url_length" class="form-control" value="<?= settings()->links->random_url_length ?? 7 ?>" />
        <small class="form-text text-muted"><?= l('admin_settings.links.random_url_length_help') ?></small>
    </div>

    <div class="form-group custom-control custom-switch">
        <input id="projects_is_enabled" name="projects_is_enabled" type="checkbox" class="custom-control-input" <?= settings()->links->projects_is_enabled ? 'checked="checked"' : null?>>
        <label class="custom-control-label" for="projects_is_enabled"><?= l('admin_settings.links.projects_is_enabled') ?></label>
        <small class="form-text text-muted"><?= l('admin_settings.links.projects_is_enabled_help') ?></small>
    </div>

    <div class="form-group custom-control custom-switch">
        <input id="domains_is_enabled" name="domains_is_enabled" type="checkbox" class="custom-control-input" <?= settings()->links->domains_is_enabled ? 'checked="checked"' : null?>>
        <label class="custom-control-label" for="domains_is_enabled"><?= l('admin_settings.links.domains_is_enabled') ?></label>
        <small class="form-text text-muted"><?= l('admin_settings.links.domains_is_enabled_help') ?></small>
    </div>

    <div class="form-group custom-control custom-switch">
        <input id="additional_domains_is_enabled" name="additional_domains_is_enabled" type="checkbox" class="custom-control-input" <?= settings()->links->additional_domains_is_enabled ? 'checked="checked"' : null?>>
        <label class="custom-control-label" for="additional_domains_is_enabled"><?= l('admin_settings.links.additional_domains_is_enabled') ?></label>
        <small class="form-text text-muted"><?= l('admin_settings.links.additional_domains_is_enabled_help') ?></small>
    </div>

    <div class="form-group custom-control custom-switch">
        <input id="main_domain_is_enabled" name="main_domain_is_enabled" type="checkbox" class="custom-control-input" <?= settings()->links->main_domain_is_enabled ? 'checked="checked"' : null?>>
        <label class="custom-control-label" for="main_domain_is_enabled"><?= l('admin_settings.links.main_domain_is_enabled') ?></label>
        <small class="form-text text-muted"><?= l('admin_settings.links.main_domain_is_enabled_help') ?></small>
    </div>

    <div class="form-group">
        <label for="domains_custom_main_ip"><?= l('admin_settings.links.domains_custom_main_ip') ?></label>
        <input id="domains_custom_main_ip" name="domains_custom_main_ip" type="text" class="form-control" value="<?= settings()->links->domains_custom_main_ip ?>" placeholder="<?= $_SERVER['SERVER_ADDR'] ?>">
        <small class="form-text text-muted"><?= l('admin_settings.links.domains_custom_main_ip_help') ?></small>
    </div>

    <div class="form-group">
        <label for="blacklisted_domains"><?= l('admin_settings.links.blacklisted_domains') ?></label>
        <textarea id="blacklisted_domains" class="form-control" name="blacklisted_domains"><?= implode(',', settings()->links->blacklisted_domains) ?></textarea>
        <small class="form-text text-muted"><?= l('admin_settings.links.blacklisted_domains_help') ?></small>
    </div>

    <div class="form-group">
        <label for="blacklisted_keywords"><?= l('admin_settings.links.blacklisted_keywords') ?></label>
        <textarea id="blacklisted_keywords" class="form-control" name="blacklisted_keywords"><?= implode(',', settings()->links->blacklisted_keywords) ?></textarea>
        <small class="form-text text-muted"><?= l('admin_settings.links.blacklisted_keywords_help') ?></small>
    </div>

    <div class="form-group custom-control custom-switch">
        <input id="pixels_is_enabled" name="pixels_is_enabled" type="checkbox" class="custom-control-input" <?= settings()->links->pixels_is_enabled ? 'checked="checked"' : null?>>
        <label class="custom-control-label" for="pixels_is_enabled"><?= l('admin_settings.links.pixels_is_enabled') ?></label>
        <small class="form-text text-muted"><?= l('admin_settings.links.pixels_is_enabled_help') ?></small>
    </div>

    <hr class="my-4">

    <?php foreach(['favicon', 'opengraph'] as $key): ?>
        <div class="form-group">
            <label for="<?= $key . '_size_limit' ?>"><?= l('admin_settings.links.' . $key . '_size_limit') ?></label>
            <div class="input-group">
                <input id="<?= $key . '_size_limit' ?>" type="number" min="0" max="<?= get_max_upload() ?>" step="any" name="<?= $key . '_size_limit' ?>" class="form-control" value="<?= settings()->links->{$key . '_size_limit'} ?>" />
                <div class="input-group-append">
                    <span class="input-group-text"><?= l('global.mb') ?></span>
                </div>
            </div>
            <small class="form-text text-muted"><?= l('global.accessibility.admin_file_size_limit_help') ?></small>
        </div>
    <?php endforeach ?>

    <hr class="my-4">

    <div class="form-group custom-control custom-switch">
        <input id="google_safe_browsing_is_enabled" name="google_safe_browsing_is_enabled" type="checkbox" class="custom-control-input" <?= settings()->links->google_safe_browsing_is_enabled ? 'checked="checked"' : null?>>
        <label class="custom-control-label" for="google_safe_browsing_is_enabled"><?= l('admin_settings.links.google_safe_browsing_is_enabled') ?></label>
        <small class="form-text text-muted"><?= l('admin_settings.links.google_safe_browsing_is_enabled_help') ?></small>
    </div>

    <div class="form-group">
        <label for="google_safe_browsing_api_key"><?= l('admin_settings.links.google_safe_browsing_api_key') ?></label>
        <input id="google_safe_browsing_api_key" type="text" name="google_safe_browsing_api_key" class="form-control" value="<?= settings()->links->google_safe_browsing_api_key ?>" />
    </div>
</div>

<button type="submit" name="submit" class="btn btn-lg btn-block btn-primary mt-4"><?= l('global.update') ?></button>
