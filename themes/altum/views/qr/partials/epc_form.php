<?php defined('ALTUMCODE') || die() ?>

<div>
    <div class="form-group" data-type="epc">
        <label for="epc_iban"><i class="fas fa-fw fa-fingerprint fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.epc_iban') ?></label>
        <input type="text" id="epc_iban" name="epc_iban" class="form-control <?= \Altum\Alerts::has_field_errors('epc_iban') ? 'is-invalid' : null ?>" value="" maxlength="<?= $data->available_qr_codes['epc']['iban']['max_length'] ?>" required="required" data-reload-qr-code />
        <?= \Altum\Alerts::output_field_error('epc_iban') ?>
    </div>

    <div class="form-group" data-type="epc">
        <label for="epc_payee_name"><i class="fas fa-fw fa-signature fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.epc_payee_name') ?></label>
        <input type="text" id="epc_payee_name" name="epc_payee_name" class="form-control <?= \Altum\Alerts::has_field_errors('epc_payee_name') ? 'is-invalid' : null ?>" value="" maxlength="<?= $data->available_qr_codes['epc']['payee_name']['max_length'] ?>" required="required" data-reload-qr-code />
        <?= \Altum\Alerts::output_field_error('epc_payee_name') ?>
    </div>

    <div class="form-group" data-type="epc">
        <label for="epc_amount"><i class="fas fa-fw fa-money-bill fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.epc_amount') ?></label>
        <input type="number" id="epc_amount" name="epc_amount" class="form-control <?= \Altum\Alerts::has_field_errors('epc_amount') ? 'is-invalid' : null ?>" value="" min="0" step="0.01" data-reload-qr-code />
        <?= \Altum\Alerts::output_field_error('epc_amount') ?>
    </div>

    <div class="form-group" data-type="epc">
        <label for="epc_currency"><i class="fas fa-fw fa-euro-sign fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.epc_currency') ?></label>
        <input type="text" id="epc_currency" name="epc_currency" class="form-control <?= \Altum\Alerts::has_field_errors('epc_currency') ? 'is-invalid' : null ?>" value="EUR" maxlength="<?= $data->available_qr_codes['epc']['currency']['max_length'] ?>" required="required" readonly="readonly" data-reload-qr-code />
        <?= \Altum\Alerts::output_field_error('epc_currency') ?>
    </div>

    <div class="form-group" data-type="epc">
        <label for="epc_bic"><i class="fas fa-fw fa-id-card fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.epc_bic') ?></label>
        <input type="text" id="epc_bic" name="epc_bic" class="form-control <?= \Altum\Alerts::has_field_errors('epc_bic') ? 'is-invalid' : null ?>" value="" maxlength="<?= $data->available_qr_codes['epc']['bic']['max_length'] ?>" data-reload-qr-code />
        <?= \Altum\Alerts::output_field_error('epc_bic') ?>
    </div>

    <div class="form-group" data-type="epc">
        <label for="epc_remittance_reference"><i class="fas fa-fw fa-receipt fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.epc_remittance_reference') ?></label>
        <input type="text" id="epc_remittance_reference" name="epc_remittance_reference" class="form-control <?= \Altum\Alerts::has_field_errors('epc_remittance_reference') ? 'is-invalid' : null ?>" value="" maxlength="<?= $data->available_qr_codes['epc']['remittance_reference']['max_length'] ?>" data-reload-qr-code />
        <?= \Altum\Alerts::output_field_error('epc_remittance_reference') ?>
    </div>

    <div class="form-group" data-type="epc">
        <label for="epc_remittance_text"><i class="fas fa-fw fa-sticky-note fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.epc_remittance_text') ?></label>
        <input type="text" id="epc_remittance_text" name="epc_remittance_text" class="form-control <?= \Altum\Alerts::has_field_errors('epc_remittance_text') ? 'is-invalid' : null ?>" value="" maxlength="<?= $data->available_qr_codes['epc']['remittance_text']['max_length'] ?>" data-reload-qr-code />
        <?= \Altum\Alerts::output_field_error('epc_remittance_text') ?>
    </div>

    <div class="form-group" data-type="epc">
        <label for="epc_information"><i class="fas fa-fw fa-pen fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.epc_information') ?></label>
        <input type="text" id="epc_information" name="epc_information" class="form-control <?= \Altum\Alerts::has_field_errors('epc_information') ? 'is-invalid' : null ?>" value="" maxlength="<?= $data->available_qr_codes['epc']['information']['max_length'] ?>" data-reload-qr-code />
        <?= \Altum\Alerts::output_field_error('epc_information') ?>
    </div>
</div>
