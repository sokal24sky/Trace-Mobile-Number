<?php defined('ALTUMCODE') || die() ?>

<div>
    <div class="form-group" data-type="phone">
        <label for="phone"><i class="fas fa-fw fa-phone-square-alt fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.phone') ?></label>
        <input type="text" id="phone" name="phone" class="form-control" value="" maxlength="<?= $data->available_qr_codes['phone']['max_length'] ?>" required="required" data-reload-qr-code />
    </div>
</div>
