<?php defined('ALTUMCODE') || die() ?>

<div>
    <div class="form-group">
        <label for="key"><i class="fas fa-fw fa-sm fa-life-ring text-muted mr-1"></i> <?= l('admin_settings.support.key') ?></label>
        <input id="key" name="key" type="text" class="form-control disabled" value="<?= settings()->support->key ?? '-' ?>" readonly="readonly" />
    </div>

    <?php
/*
 * Copyright (c) 2025 AltumCode (https://altumcode.com/)
 *
 * This software is licensed exclusively by AltumCode and is sold only via https://altumcode.com/.
 * Unauthorized distribution, modification, or use of this software without a valid license is not permitted and may be subject to applicable legal actions.
 *
 * 🌍 View all other existing AltumCode projects via https://altumcode.com/
 * 📧 Get in touch for support or general queries via https://altumcode.com/contact
 * 📤 Download the latest version via https://altumcode.com/downloads
 *
 * 🐦 X/Twitter: https://x.com/AltumCode
 * 📘 Facebook: https://facebook.com/altumcode
 * 📸 Instagram: https://instagram.com/altumcode
 */

if(isset(settings()->support->expiry_datetime)):
        $expiry_datetime = (new \DateTime(settings()->support->expiry_datetime ?? null));
        $is_active = (new \DateTime()) <= $expiry_datetime;
        ?>
        <div class="form-group">
            <label for="status"><i class="fas fa-fw fa-sm fa-circle-dot text-muted mr-1"></i> <?= l('global.status') ?></label>
            <input id="status" name="status" type="text" class="form-control disabled <?= ($is_active ? 'is-valid' : 'is-invalid') ?>" value="<?= sprintf(l('admin_settings.support.status.' . ($is_active ? 'active' : 'inactive')), $expiry_datetime->format('Y-m-d H:i:s')) ?>" readonly="readonly" />
            <small class="form-text <?= $is_active ? 'text-muted' : 'text-danger' ?>"><?= l('admin_settings.support.status.' . ($is_active ? 'active' : 'inactive') . '.help') ?></small>
        </div>

        <?php if(!$is_active): ?>
        <a href="https://altumco.de/club" target="_blank" class="btn btn-block btn-success mb-3"><?= l('admin_settings.support.extend') ?></a>
    <?php endif ?>
    <?php else: ?>
        <a href="https://altumco.de/club" target="_blank" class="btn btn-block btn-success mb-3"><?= l('admin_settings.support.extend') ?></a>
    <?php endif ?>

    <div class="form-group">
        <label for="new_key"><i class="fas fa-fw fa-sm fa-ticket-alt text-muted mr-1"></i> <?= l('admin_settings.support.new_key') ?></label>
        <input id="new_key" name="new_key" type="text" class="form-control" required="required" />
        <small class="form-text text-muted"><?= l('admin_settings.support.new_key_help') ?></small>
    </div>
</div>

<button type="submit" name="submit" class="btn btn-lg btn-block btn-primary mt-4"><?= l('global.update') ?></button>
