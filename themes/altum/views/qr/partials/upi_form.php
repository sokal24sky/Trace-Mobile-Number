<?php defined('ALTUMCODE') || die() ?>

<div>
    <div class="form-group" data-type="upi">
        <label for="upi_payee_id"><i class="fas fa-fw fa-fingerprint fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.upi_payee_id') ?></label>
        <input type="text" id="upi_payee_id" name="upi_payee_id" class="form-control" value="" maxlength="<?= $data->available_qr_codes['upi']['payee_id']['max_length'] ?>" required="required" data-reload-qr-code />
    </div>

    <div class="form-group" data-type="upi">
        <label for="upi_payee_name"><i class="fas fa-fw fa-signature fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.upi_payee_name') ?></label>
        <input type="text" id="upi_payee_name" name="upi_payee_name" class="form-control" value="" maxlength="<?= $data->available_qr_codes['upi']['payee_name']['max_length'] ?>" required="required" data-reload-qr-code />
    </div>

    <div class="form-group" data-type="upi">
        <label for="upi_amount"><i class="fas fa-fw fa-money-bill fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.upi_amount') ?></label>
        <input type="number" id="upi_amount" name="upi_amount" class="form-control" value="1" min="0" step="0.01" data-reload-qr-code />
    </div>

    <div class="form-group" data-type="upi">
        <label for="upi_currency"><i class="fas fa-fw fa-rupee-sign fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.upi_currency') ?></label>
        <input type="text" id="upi_currency" name="upi_currency" class="form-control" value="INR" maxlength="<?= $data->available_qr_codes['upi']['currency']['max_length'] ?>" required="required" readonly="readonly" data-reload-qr-code />
    </div>

    <div class="form-group" data-type="upi">
        <label for="upi_transaction_id"><i class="fas fa-fw fa-id-card fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.upi_transaction_id') ?></label>
        <input type="text" id="upi_transaction_id" name="upi_transaction_id" class="form-control" value="" maxlength="<?= $data->available_qr_codes['upi']['transaction_id']['max_length'] ?>" data-reload-qr-code />
    </div>

    <div class="form-group" data-type="upi">
        <label for="upi_transaction_reference"><i class="fas fa-fw fa-receipt fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.upi_transaction_reference') ?></label>
        <input type="text" id="upi_transaction_reference" name="upi_transaction_reference" class="form-control" value="" maxlength="<?= $data->available_qr_codes['upi']['transaction_reference']['max_length'] ?>" data-reload-qr-code />
    </div>

    <div class="form-group" data-type="upi">
        <label for="upi_transaction_note"><i class="fas fa-fw fa-sticky-note fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.upi_transaction_note') ?></label>
        <input type="text" id="upi_transaction_note" name="upi_transaction_note" class="form-control" value="" maxlength="<?= $data->available_qr_codes['upi']['transaction_note']['max_length'] ?>" data-reload-qr-code />
    </div>

    <div class="form-group" data-type="upi">
        <label for="upi_thank_you_url"><i class="fas fa-fw fa-link fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.upi_thank_you_url') ?></label>
        <input type="url" id="upi_thank_you_url" name="upi_thank_you_url" class="form-control" value="" maxlength="<?= $data->available_qr_codes['upi']['thank_you_url']['max_length'] ?>" data-reload-qr-code />
    </div>
</div>
