<?php defined('ALTUMCODE') || die() ?>

<div>
    <div class="form-group" data-type="wifi">
        <label for="wifi_ssid"><i class="fas fa-fw fa-signature fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.wifi_ssid') ?></label>
        <input type="text" id="wifi_ssid" name="wifi_ssid" class="form-control" value="" maxlength="<?= $data->available_qr_codes['wifi']['ssid']['max_length'] ?>" required="required" data-reload-qr-code />
    </div>

    <div class="form-group" data-type="wifi">
        <label for="wifi_encryption"><i class="fas fa-fw fa-user-shield fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.wifi_encryption') ?></label>
        <select id="wifi_encryption" name="wifi_encryption" class="custom-select" data-reload-qr-code>
            <option value="WEP">WEP</option>
            <option value="WPA/WPA2">WPA/WPA2</option>
            <option value="nopass"><?= l('qr_codes.input.wifi_encryption_nopass') ?></option>
        </select>
    </div>

    <div class="form-group" data-type="wifi">
        <label for="wifi_password"><i class="fas fa-fw fa-key fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.wifi_password') ?></label>
        <input type="text" id="wifi_password" name="wifi_password" class="form-control" value="" maxlength="<?= $data->available_qr_codes['wifi']['password']['max_length'] ?>" data-reload-qr-code />
    </div>

    <div class="form-group" data-type="wifi">
        <label for="wifi_is_hidden"><i class="fas fa-fw fa-user-secret fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.wifi_is_hidden') ?></label>
        <select id="wifi_is_hidden" name="wifi_is_hidden" class="custom-select" data-reload-qr-code>
            <option value="1"><?= l('global.yes') ?></option>
            <option value="0"><?= l('global.no') ?></option>
        </select>
    </div>
</div>
