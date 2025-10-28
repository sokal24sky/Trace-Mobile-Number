<?php defined('ALTUMCODE') || die() ?>

<div>
    <div class="form-group custom-control custom-switch">
        <input id="ad_blocker_detector_is_enabled" name="ad_blocker_detector_is_enabled" type="checkbox" class="custom-control-input" <?= settings()->ads->ad_blocker_detector_is_enabled ? 'checked="checked"' : null?>>
        <label class="custom-control-label" for="ad_blocker_detector_is_enabled"><i class="fas fa-fw fa-sm fa-eye text-muted mr-1"></i> <?= l('admin_settings.ads.ad_blocker_detector_is_enabled') ?></label>
        <small class="form-text text-muted"><?= l('admin_settings.ads.ad_blocker_detector_is_enabled_help') ?></small>
    </div>

    <div class="form-group custom-control custom-switch">
        <input id="ad_blocker_detector_lock_is_enabled" name="ad_blocker_detector_lock_is_enabled" type="checkbox" class="custom-control-input" <?= settings()->ads->ad_blocker_detector_lock_is_enabled ? 'checked="checked"' : null?>>
        <label class="custom-control-label" for="ad_blocker_detector_lock_is_enabled"><i class="fas fa-fw fa-sm fa-user-lock text-muted mr-1"></i> <?= l('admin_settings.ads.ad_blocker_detector_lock_is_enabled') ?></label>
    </div>

    <div class="form-group">
        <label for="ad_blocker_detector_delay"><?= l('admin_settings.ads.ad_blocker_detector_delay') ?></label>
        <div class="input-group">
            <input type="number" id="ad_blocker_detector_delay" name="ad_blocker_detector_delay" min="0" class="form-control" value="<?= settings()->ads->ad_blocker_detector_delay ?>" required="required" />
            <div class="input-group-append">
                <span class="input-group-text"><?= l('global.date.seconds') ?></span>
            </div>
        </div>
    </div>

    <div class="alert alert-info"><?= l('admin_settings.ads.ads_help') ?></div>

    <div class="form-group">
        <label for="header"><?= l('admin_settings.ads.header') ?></label>
        <textarea id="header" name="header" class="form-control"><?= settings()->ads->header ?></textarea>
    </div>

    <div class="form-group">
        <label for="footer"><?= l('admin_settings.ads.footer') ?></label>
        <textarea id="footer" name="footer" class="form-control"><?= settings()->ads->footer ?></textarea>
    </div>
</div>

<button type="submit" name="submit" class="btn btn-lg btn-block btn-primary mt-4"><?= l('global.update') ?></button>
