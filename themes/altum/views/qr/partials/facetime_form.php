<?php defined('ALTUMCODE') || die() ?>

<div>
    <div class="form-group" data-type="facetime">
        <label for="facetime"><i class="fas fa-fw fa-headset fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.facetime') ?></label>
        <input type="text" id="facetime" name="facetime" class="form-control" value="" maxlength="<?= $data->available_qr_codes['facetime']['max_length'] ?>" required="required" data-reload-qr-code />
    </div>
</div>
