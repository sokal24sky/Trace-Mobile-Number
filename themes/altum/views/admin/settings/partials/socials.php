<?php defined('ALTUMCODE') || die() ?>

<div>
    <button class="btn btn-block btn-gray-200 mb-4" type="button" data-toggle="collapse" data-target="#footer_container" aria-expanded="false" aria-controls="footer_container">
        <i class="fas fa-fw fa-bars fa-sm mr-1"></i> <?= l('admin_settings.socials.footer') ?>
    </button>

    <div class="collapse" id="footer_container">
        <?php foreach(require APP_PATH . 'includes/admin_socials.php' AS $key => $value): ?>
            <div class="form-group">
                <label for="<?= $key ?>"><i class="<?= $value['icon'] ?> fa-fw fa-sm mr-1 text-muted"></i> <?= $value['name'] ?></label>
                <div class="input-group">
                    <?php if($value['input_display_format']): ?>
                        <div class="input-group-prepend">
                            <span class="input-group-text"><?= remove_url_protocol_from_url(str_replace('%s', '', $value['format'])) ?></span>
                        </div>
                    <?php endif ?>
                    <input id="<?= $key ?>" type="text" name="<?= $key ?>" class="form-control" value="<?= settings()->socials->{$key} ?>" placeholder="<?= $value['placeholder'] ?>" />
                </div>
            </div>
        <?php endforeach ?>
    </div>

    <button class="btn btn-block btn-gray-200 mb-4" type="button" data-toggle="collapse" data-target="#share_buttons_container" aria-expanded="false" aria-controls="share_buttons_container">
        <i class="fas fa-fw fa-external-link-alt fa-sm mr-1"></i> <?= l('admin_settings.socials.share_buttons') ?>
    </button>

    <div class="collapse" id="share_buttons_container">
        <?php
        $social_share_buttons = [
            'facebook' => 'Facebook',
            'threads' => 'Threads',
            'x' => 'X',
            'pinterest' => 'Pinterest',
            'linkedin' => 'LinkedIn',
            'reddit' => 'Reddit',
            'whatsapp' => 'Whatsapp',
            'telegram' => 'Telegram',
            'snapchat' => 'Snapchat',
            'microsoft_teams' => 'Microsoft Teams',
            'email' => 'Email',
            'copy' => l('global.clipboard_copy'),
            'share' => l('global.device'),
            'print' => l('page.print')
        ];
        ?>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="h5"><?= l('admin_settings.socials.share_buttons') . ' (' . count($social_share_buttons) . ')' ?></h3>

            <div>
                <button type="button" class="btn btn-sm btn-light" data-toggle="tooltip" title="<?= l('global.select_all') ?>" data-tooltip-hide-on-click onclick="document.querySelectorAll(`[name^='share_button_']`).forEach(element => element.checked ? null : element.checked = true)"><i class="fas fa-fw fa-check-square"></i></button>
                <button type="button" class="btn btn-sm btn-light" data-toggle="tooltip" title="<?= l('global.deselect_all') ?>" data-tooltip-hide-on-click onclick="document.querySelectorAll(`[name^='share_button_']`).forEach(element => element.checked ? element.checked = false : null)"><i class="fas fa-fw fa-minus-square"></i></button>
            </div>
        </div>

        <div class="row">
            <?php foreach($social_share_buttons as $key => $value): ?>
                <div class="col-12 col-lg-6">
                    <div class="custom-control custom-checkbox my-2">
                        <input id="<?= 'share_button_' . $key ?>" name="<?= 'share_button_' . $key ?>" type="checkbox" class="custom-control-input" <?= settings()->socials->share_buttons->{$key} ? 'checked="checked"' : null ?>>
                        <label class="custom-control-label d-flex align-items-center" for="<?= 'share_button_' . $key ?>">
                            <?= $value ?>
                        </label>
                    </div>
                </div>
            <?php endforeach ?>
        </div>
    </div>
</div>

<button type="submit" name="submit" class="btn btn-lg btn-block btn-primary mt-4"><?= l('global.update') ?></button>
