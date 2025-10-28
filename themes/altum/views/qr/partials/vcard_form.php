<?php defined('ALTUMCODE') || die() ?>

<div>
    <div class="row">
        <div class="col-6">
            <div class="form-group" data-type="vcard">
                <label for="vcard_first_name"><i class="fas fa-fw fa-signature fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.vcard_first_name') ?></label>
                <input type="text" id="vcard_first_name" name="vcard_first_name" class="form-control" value="" maxlength="<?= $data->available_qr_codes['vcard']['first_name']['max_length'] ?>" data-reload-qr-code />
            </div>
        </div>
        <div class="col-6">
            <div class="form-group" data-type="vcard">
                <label for="vcard_last_name"><i class="fas fa-fw fa-signature fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.vcard_last_name') ?></label>
                <input type="text" id="vcard_last_name" name="vcard_last_name" class="form-control" value="" maxlength="<?= $data->available_qr_codes['vcard']['last_name']['max_length'] ?>" data-reload-qr-code />
            </div>
        </div>
    </div>

    <div class="form-group" data-type="vcard">
        <label for="vcard_email"><i class="fas fa-fw fa-envelope fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.vcard_email') ?></label>
        <input type="email" id="vcard_email" name="vcard_email" class="form-control" value="" maxlength="<?= $data->available_qr_codes['vcard']['email']['max_length'] ?>" data-reload-qr-code />
    </div>

    <div class="form-group" data-type="vcard">
        <label for="vcard_url"><i class="fas fa-fw fa-link fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.vcard_url') ?></label>
        <input type="url" id="vcard_url" name="vcard_url" class="form-control" value="" maxlength="<?= $data->available_qr_codes['vcard']['url']['max_length'] ?>" data-reload-qr-code />
    </div>

    <div class="form-group" data-type="vcard">
        <label for="vcard_company"><i class="fas fa-fw fa-building fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.vcard_company') ?></label>
        <input type="text" id="vcard_company" name="vcard_company" class="form-control" value="" maxlength="<?= $data->available_qr_codes['vcard']['company']['max_length'] ?>" data-reload-qr-code />
    </div>

    <div class="form-group" data-type="vcard">
        <label for="vcard_job_title"><i class="fas fa-fw fa-user-tie fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.vcard_job_title') ?></label>
        <input type="text" id="vcard_job_title" name="vcard_job_title" class="form-control" value="" maxlength="<?= $data->available_qr_codes['vcard']['job_title']['max_length'] ?>" data-reload-qr-code />
    </div>

    <div class="form-group" data-type="vcard">
        <label for="vcard_birthday"><i class="fas fa-fw fa-birthday-cake fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.vcard_birthday') ?></label>
        <input type="date" id="vcard_birthday" name="vcard_birthday" class="form-control" value="" data-reload-qr-code />
    </div>

    <div class="form-group" data-type="vcard">
        <label for="vcard_street"><i class="fas fa-fw fa-road fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.vcard_street') ?></label>
        <input type="text" id="vcard_street" name="vcard_street" class="form-control" value="" maxlength="<?= $data->available_qr_codes['vcard']['street']['max_length'] ?>" data-reload-qr-code />
    </div>

    <div class="form-group" data-type="vcard">
        <label for="vcard_city"><i class="fas fa-fw fa-city fa-sm text-muted mr-1"></i> <?= l('global.city') ?></label>
        <input type="text" id="vcard_city" name="vcard_city" class="form-control" value="" maxlength="<?= $data->available_qr_codes['vcard']['city']['max_length'] ?>" data-reload-qr-code />
    </div>

    <div class="form-group" data-type="vcard">
        <label for="vcard_zip"><i class="fas fa-fw fa-mail-bulk fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.vcard_zip') ?></label>
        <input type="text" id="vcard_zip" name="vcard_zip" class="form-control" value="" maxlength="<?= $data->available_qr_codes['vcard']['zip']['max_length'] ?>" data-reload-qr-code />
    </div>

    <div class="form-group" data-type="vcard">
        <label for="vcard_region"><i class="fas fa-fw fa-flag fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.vcard_region') ?></label>
        <input type="text" id="vcard_region" name="vcard_region" class="form-control" value="" maxlength="<?= $data->available_qr_codes['vcard']['region']['max_length'] ?>" data-reload-qr-code />
    </div>

    <div class="form-group" data-type="vcard">
        <label for="vcard_country"><i class="fas fa-fw fa-globe fa-sm text-muted mr-1"></i> <?= l('global.country') ?></label>
        <input type="text" id="vcard_country" name="vcard_country" class="form-control" value="" maxlength="<?= $data->available_qr_codes['vcard']['country']['max_length'] ?>" data-reload-qr-code />
    </div>

    <div class="form-group" data-type="vcard">
        <label for="vcard_note"><i class="fas fa-fw fa-paragraph fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.vcard_note') ?></label>
        <textarea id="vcard_note" name="vcard_note" class="form-control" maxlength="<?= $data->available_qr_codes['vcard']['note']['max_length'] ?>" data-reload-qr-code></textarea>
    </div>

    <button class="btn btn-block btn-gray-200 my-4" type="button" data-toggle="collapse" data-target="#vcard_phone_numbers_container" aria-expanded="false" aria-controls="vcard_phone_numbers_container" data-type="vcard">
        <i class="fas fa-fw fa-phone-square-alt fa-sm mr-1"></i> <?= l('qr_codes.input.vcard_phone_numbers') ?>
    </button>

    <div class="collapse" id="vcard_phone_numbers_container" data-type="vcard">
        <div id="vcard_phone_numbers">
            <?php foreach($data->values['settings']['vcard_phone_numbers'] ?? [] as $key => $phone_number): ?>
                <div class="mb-4">
                    <div class="form-group">
                        <label for="<?= 'vcard_phone_number_label_' . $key ?>"><i class="fas fa-fw fa-bookmark fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.vcard_phone_number_label') ?></label>
                        <input id="<?= 'vcard_phone_number_label_' . $key ?>" type="text" name="vcard_phone_number_label[<?= $key ?>]" class="form-control" value="<?= $phone_number->label ?>" maxlength="<?= $data->available_qr_codes['vcard']['phone_number_label']['max_length'] ?>" data-reload-qr-code />
                        <small class="form-text text-muted"><?= l('qr_codes.input.vcard_phone_number_label_help') ?></small>
                    </div>

                    <div class="form-group">
                        <label for="<?= 'vcard_phone_number_value_' . $key ?>"><i class="fas fa-fw fa-phone-square-alt fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.vcard_phone_number_value') ?></label>
                        <input id="<?= 'vcard_phone_number_value_' . $key ?>" type="text" name="vcard_phone_number_value[<?= $key ?>]" value="<?= $phone_number->value ?>" class="form-control" maxlength="<?= $data->available_qr_codes['vcard']['phone_number_value']['max_length'] ?>" required="required" data-reload-qr-code />
                    </div>

                    <button type="button" data-remove="vcard_phone_numbers" class="btn btn-sm btn-block btn-outline-danger"><i class="fas fa-fw fa-times"></i> <?= l('global.delete') ?></button>
                </div>
            <?php endforeach ?>
        </div>

        <div class="mb-3">
            <button data-add="vcard_phone_numbers" type="button" class="btn btn-sm btn-outline-success"><i class="fas fa-fw fa-plus-circle fa-sm mr-1"></i> <?= l('global.create') ?></button>
        </div>
    </div>

    <button class="btn btn-block btn-gray-200 my-4" type="button" data-toggle="collapse" data-target="#vcard_socials_container" aria-expanded="false" aria-controls="vcard_socials_container" data-type="vcard">
        <i class="fas fa-fw fa-share-alt fa-sm mr-1"></i> <?= l('qr_codes.input.vcard_socials') ?>
    </button>

    <div class="collapse" id="vcard_socials_container" data-type="vcard">
        <div id="vcard_socials"></div>

        <div class="mb-3">
            <button data-add="vcard_social" type="button" class="btn btn-sm btn-outline-success"><i class="fas fa-fw fa-plus-circle fa-sm mr-1"></i> <?= l('global.create') ?></button>
        </div>
    </div>
</div>

<template id="template_vcard_social">
    <div class="mb-4">
        <div class="form-group">
            <label for=""><i class="fas fa-fw fa-bookmark fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.vcard_social_label') ?></label>
            <input id="" type="text" name="vcard_social_label[]" class="form-control" maxlength="<?= $data->available_qr_codes['vcard']['social_label']['max_length'] ?>" required="required" data-reload-qr-code />
        </div>

        <div class="form-group">
            <label for=""><i class="fas fa-fw fa-link fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.vcard_social_value') ?></label>
            <input id="" type="url" name="vcard_social_value[]" class="form-control" maxlength="<?= $data->available_qr_codes['vcard']['social_value']['max_length'] ?>" required="required" data-reload-qr-code />
        </div>

        <button type="button" data-remove="vcard_social" class="btn btn-sm btn-block btn-outline-danger"><i class="fas fa-fw fa-times"></i> <?= l('global.delete') ?></button>
    </div>
</template>

<template id="template_vcard_phone_numbers">
    <div class="mb-4">
        <div class="form-group">
            <label for=""><i class="fas fa-fw fa-bookmark fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.vcard_phone_number_label') ?></label>
            <input id="" type="text" name="vcard_phone_number_label[]" class="form-control" maxlength="<?= $data->available_qr_codes['vcard']['phone_number_label']['max_length'] ?>" data-reload-qr-code />
            <small class="form-text text-muted"><?= l('qr_codes.input.vcard_phone_number_label_help') ?></small>
        </div>

        <div class="form-group">
            <label for=""><i class="fas fa-fw fa-phone-square-alt fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.vcard_phone_number_value') ?></label>
            <input id="" type="text" name="vcard_phone_number_value[]" class="form-control" maxlength="<?= $data->available_qr_codes['vcard']['phone_number_value']['max_length'] ?>" required="required" data-reload-qr-code />
        </div>

        <button type="button" data-remove="vcard_phone_numbers" class="btn btn-sm btn-block btn-outline-danger"><i class="fas fa-fw fa-times"></i> <?= l('global.delete') ?></button>
    </div>
</template>
