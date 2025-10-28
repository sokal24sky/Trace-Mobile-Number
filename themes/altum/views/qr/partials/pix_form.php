<?php defined('ALTUMCODE') || die() ?>

<div>
    <div class="form-group" data-type="pix">
        <label for="pix_payee_key"><i class="fas fa-fw fa-fingerprint fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.pix_payee_key') ?></label>
        <input type="text" id="pix_payee_key" name="pix_payee_key" class="form-control <?= \Altum\Alerts::has_field_errors('pix_payee_key') ? 'is-invalid' : null ?>" value="<?= $data->values['settings']['pix_payee_key'] ?? null ?>" maxlength="<?= $data->available_qr_codes['pix']['payee_key']['max_length'] ?>" required="required" data-reload-qr-code />
        <?= \Altum\Alerts::output_field_error('pix_payee_key') ?>
        <small class="form-text text-muted"><?= l('qr_codes.input.pix_payee_key_help') ?></small>
    </div>

    <div class="form-group" data-type="pix">
        <label for="pix_payee_name"><i class="fas fa-fw fa-signature fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.pix_payee_name') ?></label>
        <input type="text" id="pix_payee_name" name="pix_payee_name" class="form-control <?= \Altum\Alerts::has_field_errors('pix_payee_name') ? 'is-invalid' : null ?>" value="<?= $data->values['settings']['pix_payee_name'] ?? null ?>" maxlength="<?= $data->available_qr_codes['pix']['payee_name']['max_length'] ?>" required="required" data-reload-qr-code />
        <?= \Altum\Alerts::output_field_error('pix_payee_name') ?>
    </div>

    <div class="form-group" data-type="pix">
        <label for="pix_amount"><i class="fas fa-fw fa-money-bill fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.pix_amount') ?></label>
        <input type="number" id="pix_amount" name="pix_amount" class="form-control <?= \Altum\Alerts::has_field_errors('pix_amount') ? 'is-invalid' : null ?>" value="<?= $data->values['settings']['pix_amount'] ?? null ?>" min="0" step="0.01" data-reload-qr-code />
        <?= \Altum\Alerts::output_field_error('pix_amount') ?>
    </div>

    <div class="form-group" data-type="pix">
        <label for="pix_currency"><i class="fas fa-fw fa-credit-card fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.pix_currency') ?></label>
        <input type="text" id="pix_currency" name="pix_currency" class="form-control <?= \Altum\Alerts::has_field_errors('pix_currency') ? 'is-invalid' : null ?>" value="BRL" maxlength="<?= $data->available_qr_codes['pix']['currency']['max_length'] ?>" required="required" readonly="readonly" data-reload-qr-code />
        <?= \Altum\Alerts::output_field_error('pix_currency') ?>
    </div>

    <div class="form-group" data-type="pix">
        <label for="pix_city"><i class="fas fa-fw fa-city fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.pix_city') ?></label>
        <input type="text" id="pix_city" name="pix_city" class="form-control <?= \Altum\Alerts::has_field_errors('pix_city') ? 'is-invalid' : null ?>" value="<?= $data->values['settings']['pix_city'] ?? null ?>" maxlength="<?= $data->available_qr_codes['pix']['city']['max_length'] ?>" data-reload-qr-code />
        <?= \Altum\Alerts::output_field_error('pix_city') ?>
    </div>

    <div class="form-group" data-type="pix">
        <label for="pix_transaction_id"><i class="fas fa-fw fa-receipt fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.pix_transaction_id') ?></label>
        <input type="text" id="pix_transaction_id" name="pix_transaction_id" class="form-control <?= \Altum\Alerts::has_field_errors('pix_transaction_id') ? 'is-invalid' : null ?>" value="<?= $data->values['settings']['pix_transaction_id'] ?? null ?>" maxlength="<?= $data->available_qr_codes['pix']['transaction_id']['max_length'] ?>" data-reload-qr-code />
        <?= \Altum\Alerts::output_field_error('pix_transaction_id') ?>
    </div>

    <div class="form-group" data-type="pix">
        <label for="pix_description"><i class="fas fa-fw fa-pen fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.pix_description') ?></label>
        <input type="text" id="pix_description" name="pix_description" class="form-control <?= \Altum\Alerts::has_field_errors('pix_description') ? 'is-invalid' : null ?>" value="<?= $data->values['settings']['pix_description'] ?? null ?>" maxlength="<?= $data->available_qr_codes['pix']['description']['max_length'] ?>" data-reload-qr-code />
        <?= \Altum\Alerts::output_field_error('pix_description') ?>
    </div>
</div>
