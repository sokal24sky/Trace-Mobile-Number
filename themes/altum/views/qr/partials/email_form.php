<?php defined('ALTUMCODE') || die() ?>

<div>
    <div class="form-group" data-type="email">
        <label for="email"><i class="fas fa-fw fa-envelope fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.email') ?></label>
        <input type="text" id="email" name="email" class="form-control" value="" maxlength="<?= $data->available_qr_codes['email']['max_length'] ?>" required="required" data-reload-qr-code />
    </div>

    <div class="form-group" data-type="email">
        <label for="email_subject"><i class="fas fa-fw fa-heading fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.email_subject') ?></label>
        <input type="text" id="email_subject" name="email_subject" class="form-control" value="" maxlength="<?= $data->available_qr_codes['email']['body']['max_length'] ?>" data-reload-qr-code />
    </div>

    <div class="form-group" data-type="email">
        <label for="email_body"><i class="fas fa-fw fa-paragraph fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.email_body') ?></label>
        <textarea id="email_body" name="email_body" class="form-control" maxlength="<?= $data->available_qr_codes['email']['body']['max_length'] ?>" data-reload-qr-code></textarea>
    </div>
</div>
