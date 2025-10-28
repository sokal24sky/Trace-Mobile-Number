<?php defined('ALTUMCODE') || die() ?>

<div>
    <div class="form-group" data-type="event">
        <label for="event"><i class="fas fa-fw fa-signature fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.event') ?></label>
        <input type="text" id="event" name="event" class="form-control" value="" maxlength="<?= $data->available_qr_codes['event']['max_length'] ?>" required="required" data-reload-qr-code />
    </div>

    <div class="form-group" data-type="event">
        <label for="event_location"><i class="fas fa-fw fa-map-pin fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.event_location') ?></label>
        <input type="text" id="event_location" name="event_location" class="form-control" value="" maxlength="<?= $data->available_qr_codes['event']['location']['max_length'] ?>" data-reload-qr-code />
    </div>

    <div class="form-group" data-type="event">
        <label for="event_url"><i class="fas fa-fw fa-link fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.event_url') ?></label>
        <input type="url" id="event_url" name="event_url" class="form-control" value="" maxlength="<?= $data->available_qr_codes['event']['url']['max_length'] ?>" data-reload-qr-code />
    </div>

    <div class="form-group" data-type="event">
        <label for="event_note"><i class="fas fa-fw fa-paragraph fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.event_note') ?></label>
        <textarea id="event_note" name="event_note" class="form-control" maxlength="<?= $data->available_qr_codes['event']['note']['max_length'] ?>" data-reload-qr-code></textarea>
    </div>

    <div class="form-group" data-type="event">
        <label for="event_start_datetime"><i class="fas fa-fw fa-calendar-day fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.event_start_datetime') ?></label>
        <input type="datetime-local" id="event_start_datetime" name="event_start_datetime" class="form-control" value="" required="required" data-reload-qr-code />
    </div>

    <div class="form-group" data-type="event">
        <label for="event_end_datetime"><i class="fas fa-fw fa-calendar-times fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.event_end_datetime') ?></label>
        <input type="datetime-local" id="event_end_datetime" name="event_end_datetime" class="form-control" value="" data-reload-qr-code />
    </div>

    <div class="form-group" data-type="event">
        <label for="event_first_alert_datetime"><i class="fas fa-fw fa-calendar-check fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.event_first_alert_datetime') ?></label>
        <input type="datetime-local" id="event_first_alert_datetime" name="event_first_alert_datetime" class="form-control" value="" data-reload-qr-code />
    </div>

    <div class="form-group" data-type="event">
        <label for="event_second_alert_datetime"><i class="fas fa-fw fa-calendar-alt fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.event_second_alert_datetime') ?></label>
        <input type="datetime-local" id="event_second_alert_datetime" name="event_second_alert_datetime" class="form-control" value="" data-reload-qr-code />
    </div>

    <div class="form-group" data-type="event">
        <label for="event_timezone"><i class="fas fa-fw fa-atlas fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.event_timezone') ?></label>
        <select id="event_timezone" name="event_timezone" class="custom-select" data-reload-qr-code>
            <?php foreach(DateTimeZone::listIdentifiers() as $timezone): ?>
                <option value="<?= $timezone ?>"><?= $timezone ?></option>
            <?php endforeach ?>
        </select>
    </div>
</div>
