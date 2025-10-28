<?php defined('ALTUMCODE') || die() ?>

<div>
    <div <?= !\Altum\Plugin::is_active('push-notifications') ? 'data-toggle="tooltip" title="' . sprintf(l('admin_plugins.no_access'), \Altum\Plugin::get('push-notifications')->name ?? 'push-notifications') . '"' : null ?>>
        <div class="<?= !\Altum\Plugin::is_active('push-notifications') ? 'container-disabled' : null ?>">
            <div class="form-group custom-control custom-switch">
                <input id="is_enabled" name="is_enabled" type="checkbox" class="custom-control-input" <?= \Altum\Plugin::is_active('push-notifications') && settings()->push_notifications->is_enabled ? 'checked="checked"' : null?>>
                <label class="custom-control-label" for="is_enabled"><?= l('admin_settings.push_notifications.is_enabled') ?></label>
            </div>

            <div class="form-group custom-control custom-switch">
                <input id="guests_is_enabled" name="guests_is_enabled" type="checkbox" class="custom-control-input" <?= \Altum\Plugin::is_active('push-notifications') && settings()->push_notifications->guests_is_enabled ? 'checked="checked"' : null?>>
                <label class="custom-control-label" for="guests_is_enabled"><?= l('admin_settings.push_notifications.guests_is_enabled') ?></label>
                <small class="form-text text-muted"><?= l('admin_settings.push_notifications.guests_is_enabled_help') ?></small>
            </div>

            <div class="form-group custom-control custom-switch">
                <input id="ask_to_subscribe_is_enabled" name="ask_to_subscribe_is_enabled" type="checkbox" class="custom-control-input" <?= \Altum\Plugin::is_active('push-notifications') && settings()->push_notifications->ask_to_subscribe_is_enabled ? 'checked="checked"' : null?>>
                <label class="custom-control-label" for="ask_to_subscribe_is_enabled"><?= l('admin_settings.push_notifications.ask_to_subscribe_is_enabled') ?></label>
                <small class="form-text text-muted"><?= l('admin_settings.push_notifications.ask_to_subscribe_is_enabled_help') ?></small>
            </div>

            <div class="form-group">
                <label for="ask_to_subscribe_delay"><?= l('admin_settings.push_notifications.ask_to_subscribe_delay') ?></label>
                <div class="input-group">
                    <input type="number" id="ask_to_subscribe_delay" name="ask_to_subscribe_delay" min="0" class="form-control" value="<?= settings()->push_notifications->ask_to_subscribe_delay ?>" required="required" />
                    <div class="input-group-append">
                        <span class="input-group-text"><?= l('global.date.seconds') ?></span>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="ask_to_subscribe_delay_minimum_pageviews_count"><?= l('admin_settings.push_notifications.ask_to_subscribe_delay_minimum_pageviews_count') ?></label>
                <div class="input-group">
                    <input type="number" id="ask_to_subscribe_delay_minimum_pageviews_count" name="ask_to_subscribe_delay_minimum_pageviews_count" min="0" class="form-control" value="<?= settings()->push_notifications->ask_to_subscribe_delay_minimum_pageviews_count ?? 3 ?>" required="required" />
                    <div class="input-group-append">
                        <span class="input-group-text"><?= l('admin_settings.push_notifications.pageviews') ?></span>
                    </div>
                </div>
            </div>

            <div class="form-group" data-file-image-input-wrapper data-file-input-wrapper-size-limit="<?= get_max_upload() ?>" data-file-input-wrapper-size-limit-error="<?= sprintf(l('global.error_message.file_size_limit'), get_max_upload()) ?>">
                <label for="icon"><i class="fas fa-fw fa-sm fa-image text-muted mr-1"></i> <?= l('admin_settings.push_notifications.icon') ?></label>
                <?= include_view(THEME_PATH . 'views/partials/file_image_input.php', ['uploads_file_key' => 'push_notifications_icon', 'file_key' => 'icon', 'already_existing_image' => settings()->push_notifications->icon]) ?>
                <small class="form-text text-muted"><?= sprintf(l('global.accessibility.whitelisted_file_extensions'), \Altum\Uploads::get_whitelisted_file_extensions_accept('push_notifications_icon')) . ' ' . sprintf(l('global.accessibility.file_size_limit'), get_max_upload()) ?></small>
            </div>

            <button class="btn btn-block btn-gray-200 mb-4" type="button" data-toggle="collapse" data-target="#cron_settings_container" aria-expanded="false" aria-controls="cron_settings_container">
                <i class="fas fa-fw fa-arrows-rotate fa-sm mr-1"></i> <?= l('admin_settings.cron.cron_settings') ?>
            </button>

            <div class="collapse" id="cron_settings_container">
                <div class="alert alert-danger mb-3"><?= l('admin_settings.cron.cron_settings_help') ?></div>

                <div class="form-group">
                    <label for="notifications_per_cron"><?= l('admin_settings.push_notifications.notifications_per_cron') ?></label>
                    <input id="notifications_per_cron" type="number" min="0" name="notifications_per_cron" class="form-control" value="<?= settings()->push_notifications->notifications_per_cron ?? 500 ?>" />
                </div>

                <div class="form-group">
                    <label for="notifications_per_cron_batch"><?= l('admin_settings.push_notifications.notifications_per_cron_batch') ?></label>
                    <input id="notifications_per_cron_batch" type="number" min="0" name="notifications_per_cron_batch" class="form-control" value="<?= settings()->push_notifications->notifications_per_cron_batch ?? 100 ?>" />
                </div>

                <div class="form-group">
                    <label for="notifications_per_cron_batch_concurrently"><?= l('admin_settings.push_notifications.notifications_per_cron_batch_concurrently') ?></label>
                    <input id="notifications_per_cron_batch_concurrently" type="number" min="0" name="notifications_per_cron_batch_concurrently" class="form-control" value="<?= settings()->push_notifications->notifications_per_cron_batch_concurrently ?? 25 ?>" />
                </div>
            </div>
        </div>
    </div>
</div>

<?php if(\Altum\Plugin::is_active('push-notifications')): ?>
<button type="submit" name="submit" class="btn btn-lg btn-block btn-primary mt-4"><?= l('global.update') ?></button>
<?php endif ?>

