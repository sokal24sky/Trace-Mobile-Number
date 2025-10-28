<?php defined('ALTUMCODE') || die() ?>

<div>
    <div class="form-group" data-type="crypto">
        <label for="crypto_coin"><i class="fab fa-fw fa-bitcoin fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.crypto_coin') ?></label>
        <select id="crypto_coin" name="crypto_coin" class="custom-select" data-reload-qr-code>
            <?php foreach($data->available_qr_codes['crypto']['coins'] as $coin => $coin_name): ?>
                <option value="<?= $coin ?>"><?= $coin_name ?></option>
            <?php endforeach ?>
        </select>
    </div>

    <div class="form-group" data-type="crypto">
        <label for="crypto_address"><i class="fas fa-fw fa-map-marker-alt fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.crypto_address') ?></label>
        <input type="text" id="crypto_address" name="crypto_address" class="form-control" value="" maxlength="<?= $data->available_qr_codes['crypto']['address']['max_length'] ?>" data-reload-qr-code />
    </div>

    <div class="form-group" data-type="crypto">
        <label for="crypto_amount"><i class="fas fa-fw fa-coins fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.crypto_amount') ?></label>
        <input type="number" step="0.01" min="0.00000001" id="crypto_amount" name="crypto_amount" class="form-control" value="" data-reload-qr-code />
    </div>
</div>
