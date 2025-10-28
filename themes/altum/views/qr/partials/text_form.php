<?php defined('ALTUMCODE') || die() ?>

<div>
    <div class="form-group" data-type="text" data-character-counter="textarea">
        <label for="text" class="d-flex justify-content-between align-items-center">
            <span><i class="fas fa-fw fa-paragraph fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.text') ?></span>
            <small class="text-muted" data-character-counter-wrapper></small>
        </label>
        <textarea id="text" name="text" class="form-control" maxlength="<?= $data->available_qr_codes['text']['max_length'] ?>" required="required" data-reload-qr-code></textarea>
    </div>

    <?php if(settings()->users->register_is_enabled || is_logged_in()): ?>
    <div class="form-group" data-type="text">
        <div <?= is_logged_in() ? null : get_plan_feature_disabled_info() ?>>
            <div class="<?= is_logged_in() ? null : 'container-disabled' ?>">
                <div class="custom-control custom-checkbox">
                    <input id="is_bulk" name="is_bulk" type="checkbox" class="custom-control-input" <?= ($data->values['is_bulk'] ?? null) ? 'checked="checked"' : null ?> data-reload-qr-code />
                    <label class="custom-control-label" for="is_bulk"><?= l('qr_codes.input.is_bulk') ?></label>
                </div>
            </div>
        </div>
    </div>
    <?php endif ?>
</div>
