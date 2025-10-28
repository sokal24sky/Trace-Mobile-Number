<?php defined('ALTUMCODE') || die() ?>

<?php if(settings()->links->additional_domains_is_enabled): ?>
    <?php $additional_domains = (new \Altum\Models\Domain())->get_available_additional_domains(); ?>
<?php endif ?>

<div>
    <?php if(settings()->codes->ai_qr_codes_is_enabled): ?>
    <div class="d-flex justify-content-between align-items-center my-3">
        <div>
            <?= sprintf(l('global.plan_settings.ai_qr_codes_per_month_limit'), '<strong>' . ($data->plan_settings->ai_qr_codes_per_month_limit == -1 ? l('global.unlimited') : nr($data->plan_settings->ai_qr_codes_per_month_limit)) . '</strong>') ?>
        </div>

        <i class="fas fa-fw fa-sm <?= $data->plan_settings->ai_qr_codes_per_month_limit ? 'fa-check text-success' : 'fa-times text-muted' ?>"></i>
    </div>
    <?php endif ?>

    <?php if(settings()->codes->qr_codes_is_enabled): ?>
        <?php $enabled_qr_codes = array_filter((array) $data->plan_settings->enabled_qr_codes) ?>
        <?php $enabled_qr_codes_count = count($enabled_qr_codes) ?>
        <?php
        $enabled_qr_codes_string = implode(', ', array_map(function($key) {
            return l('qr_codes.type.' . mb_strtolower($key));
        }, array_keys($enabled_qr_codes)));
        ?>

        <div class="d-flex justify-content-between align-items-center my-3">
            <div>
                <?= sprintf(l('global.plan_settings.enabled_qr_codes'), '<strong>' . nr($enabled_qr_codes_count) . '</strong>') ?>
                <span class="mr-1" data-toggle="tooltip" title="<?= $enabled_qr_codes_string ?>"><i class="fas fa-fw fa-xs fa-circle-question text-gray-500"></i></span>
            </div>

            <i class="fas fa-fw fa-sm <?= $enabled_qr_codes_count ? 'fa-check text-success' : 'fa-times text-muted' ?>"></i>
        </div>

        <div class="d-flex justify-content-between align-items-center my-3">
            <div>
                <?= sprintf(l('global.plan_settings.qr_codes_limit'), '<strong>' . ($data->plan_settings->qr_codes_limit == -1 ? l('global.unlimited') : nr($data->plan_settings->qr_codes_limit)) . '</strong>') ?>
            </div>

            <i class="fas fa-fw fa-sm <?= $data->plan_settings->qr_codes_limit ? 'fa-check text-success' : 'fa-times text-muted' ?>"></i>
        </div>

        <div class="d-flex justify-content-between align-items-center my-3">
            <div>
                <?= sprintf(l('global.plan_settings.qr_codes_bulk_limit'), '<strong>' . ($data->plan_settings->qr_codes_bulk_limit == -1 ? l('global.unlimited') : nr($data->plan_settings->qr_codes_bulk_limit)) . '</strong>') ?>
            </div>

            <i class="fas fa-fw fa-sm <?= $data->plan_settings->qr_codes_bulk_limit ? 'fa-check text-success' : 'fa-times text-muted' ?>"></i>
        </div>
    <?php endif ?>

    <?php if(settings()->codes->barcodes_is_enabled): ?>
        <?php $enabled_barcodes = array_filter((array) $data->plan_settings->enabled_barcodes) ?>
        <?php $enabled_barcodes_count = count($enabled_barcodes) ?>
        <?php
        $enabled_barcodes_string = implode(', ', array_map(function($key) {
            return $key;
        }, array_keys($enabled_barcodes)));
        ?>

        <div class="d-flex justify-content-between align-items-center my-3">
            <div>
                <?= sprintf(l('global.plan_settings.enabled_barcodes'), '<strong>' . nr($enabled_barcodes_count) . '</strong>') ?>
                <span class="mr-1" data-toggle="tooltip" title="<?= $enabled_barcodes_string ?>"><i class="fas fa-fw fa-xs fa-circle-question text-gray-500"></i></span>
            </div>

            <i class="fas fa-fw fa-sm <?= $enabled_barcodes_count ? 'fa-check text-success' : 'fa-times text-muted' ?>"></i>
        </div>

        <div class="d-flex justify-content-between align-items-center my-3">
            <div>
                <?= sprintf(l('global.plan_settings.barcodes_limit'), '<strong>' . ($data->plan_settings->barcodes_limit == -1 ? l('global.unlimited') : nr($data->plan_settings->barcodes_limit)) . '</strong>') ?>
            </div>

            <i class="fas fa-fw fa-sm <?= $data->plan_settings->barcodes_limit ? 'fa-check text-success' : 'fa-times text-muted' ?>"></i>
        </div>

        <div class="d-flex justify-content-between align-items-center my-3">
            <div>
                <?= sprintf(l('global.plan_settings.barcodes_bulk_limit'), '<strong>' . ($data->plan_settings->barcodes_bulk_limit == -1 ? l('global.unlimited') : nr($data->plan_settings->barcodes_bulk_limit)) . '</strong>') ?>
            </div>

            <i class="fas fa-fw fa-sm <?= $data->plan_settings->barcodes_bulk_limit ? 'fa-check text-success' : 'fa-times text-muted' ?>"></i>
        </div>
    <?php endif ?>

    <div class="d-flex justify-content-between align-items-center my-3">
        <div>
            <?= sprintf(l('global.plan_settings.links_limit'), '<strong>' . ($data->plan_settings->links_limit == -1 ? l('global.unlimited') : nr($data->plan_settings->links_limit)) . '</strong>') ?>
        </div>

        <i class="fas fa-fw fa-sm <?= $data->plan_settings->links_limit ? 'fa-check text-success' : 'fa-times text-muted' ?>"></i>
    </div>

    <div class="d-flex justify-content-between align-items-center my-3">
        <div>
            <?= sprintf(l('global.plan_settings.links_bulk_limit'), '<strong>' . ($data->plan_settings->links_bulk_limit == -1 ? l('global.unlimited') : nr($data->plan_settings->links_bulk_limit)) . '</strong>') ?>
        </div>

        <i class="fas fa-fw fa-sm <?= $data->plan_settings->links_bulk_limit ? 'fa-check text-success' : 'fa-times text-muted' ?>"></i>
    </div>

    <?php if(settings()->links->projects_is_enabled): ?>
    <div class="d-flex justify-content-between align-items-center my-3">
        <div>
            <?= sprintf(l('global.plan_settings.projects_limit'), '<strong>' . ($data->plan_settings->projects_limit == -1 ? l('global.unlimited') : nr($data->plan_settings->projects_limit)) . '</strong>') ?>
        </div>

        <i class="fas fa-fw fa-sm <?= $data->plan_settings->projects_limit ? 'fa-check text-success' : 'fa-times text-muted' ?>"></i>
    </div>
    <?php endif ?>

    <?php if(settings()->links->pixels_is_enabled): ?>
    <div class="d-flex justify-content-between align-items-center my-3">
        <div>
            <?= sprintf(l('global.plan_settings.pixels_limit'), '<strong>' . ($data->plan_settings->pixels_limit == -1 ? l('global.unlimited') : nr($data->plan_settings->pixels_limit)) . '</strong>') ?>
        </div>

        <i class="fas fa-fw fa-sm <?= $data->plan_settings->pixels_limit ? 'fa-check text-success' : 'fa-times text-muted' ?>"></i>
    </div>
    <?php endif ?>

    <?php if(settings()->links->domains_is_enabled): ?>
        <div class="d-flex justify-content-between align-items-center my-3">
            <div>
                <?= sprintf(l('global.plan_settings.domains_limit'), '<strong>' . ($data->plan_settings->domains_limit == -1 ? l('global.unlimited') : nr($data->plan_settings->domains_limit)) . '</strong>') ?>
            </div>

            <i class="fas fa-fw fa-sm <?= $data->plan_settings->domains_limit ? 'fa-check text-success' : 'fa-times text-muted' ?>"></i>
        </div>
    <?php endif ?>

    <?php if(settings()->links->additional_domains_is_enabled): ?>
        <div class="d-flex justify-content-between align-items-center my-3 <?= count($data->plan_settings->additional_domains ?? []) ? null : 'text-muted' ?>">
            <div>
                <?= sprintf(l('global.plan_settings.additional_domains'), '<strong>' . nr(count($data->plan_settings->additional_domains ?? [])) . '</strong>') ?>
                <span class="mr-1" data-toggle="tooltip" title="<?= sprintf(l('global.plan_settings.additional_domains_help'), implode(', ', array_map(function($domain_id) use($additional_domains) { return $additional_domains[$domain_id]->host ?? null; }, $data->plan_settings->additional_domains ?? []))) ?>"><i class="fas fa-fw fa-xs fa-circle-question text-gray-500"></i></span>
            </div>

            <i class="fas fa-fw fa-sm <?= count($data->plan_settings->additional_domains ?? []) ? 'fa-check text-success' : 'fa-times' ?>"></i>
        </div>
    <?php endif ?>

    <?php if(\Altum\Plugin::is_active('teams')): ?>
        <div class="d-flex justify-content-between align-items-center my-3">
            <div>
                <?= sprintf(l('global.plan_settings.teams_limit'), '<strong>' . ($data->plan_settings->teams_limit == -1 ? l('global.unlimited') : nr($data->plan_settings->teams_limit)) . '</strong>') ?>

                <span class="ml-1" data-toggle="tooltip" data-html="true" title="<?= sprintf(l('global.plan_settings.team_members_limit'), '<strong>' . ($data->plan_settings->team_members_limit == -1 ? l('global.unlimited') : nr($data->plan_settings->team_members_limit)) . '</strong>') ?>"><i class="fas fa-fw fa-xs fa-circle-question text-gray-500"></i></span>
            </div>

            <i class="fas fa-fw fa-sm <?= $data->plan_settings->teams_limit ? 'fa-check text-success' : 'fa-times text-muted' ?>"></i>
        </div>
    <?php endif ?>

    <?php if(\Altum\Plugin::is_active('affiliate') && settings()->affiliate->is_enabled): ?>
        <div class="d-flex justify-content-between align-items-center my-3">
            <div>
                <?= sprintf(l('global.plan_settings.affiliate_commission_percentage'), '<strong>' . nr($data->plan_settings->affiliate_commission_percentage) . '%</strong>') ?>
            </div>

            <i class="fas fa-fw fa-sm <?= $data->plan_settings->affiliate_commission_percentage ? 'fa-check text-success' : 'fa-times text-muted' ?>"></i>
        </div>
    <?php endif ?>

    <div class="d-flex justify-content-between align-items-center my-3">
        <div data-toggle="tooltip" title="<?= ($data->plan_settings->statistics_retention == -1 ? '' : $data->plan_settings->statistics_retention . ' ' . l('global.date.days')) ?>">
            <?= sprintf(l('global.plan_settings.statistics_retention'), '<strong>' . ($data->plan_settings->statistics_retention == -1 ? l('global.unlimited') : \Altum\Date::days_format($data->plan_settings->statistics_retention)) . '</strong>') ?>
        </div>

        <i class="fas fa-fw fa-sm <?= $data->plan_settings->statistics_retention ? 'fa-check text-success' : 'fa-times text-muted' ?>"></i>
    </div>

    <div class="d-flex justify-content-between align-items-center my-3 <?= $data->plan_settings->analytics_is_enabled ? null : 'text-muted' ?>">
        <div>
            <?= l('global.plan_settings.analytics_is_enabled') ?>
            <span class="ml-1" data-toggle="tooltip" title="<?= l('global.plan_settings.analytics_is_enabled_help') ?>"><i class="fas fa-fw fa-xs fa-circle-question text-gray-500"></i></span>
        </div>

        <i class="fas fa-fw fa-sm <?= $data->plan_settings->analytics_is_enabled ? 'fa-check text-success' : 'fa-times' ?>"></i>
    </div>

    <div class="d-flex justify-content-between align-items-center my-3 <?= $data->plan_settings->password_protection_is_enabled ? null : 'text-muted' ?>">
        <div>
            <?= l('global.plan_settings.password_protection_is_enabled') ?>
            <span class="ml-1" data-toggle="tooltip" title="<?= l('global.plan_settings.password_protection_is_enabled_help') ?>"><i class="fas fa-fw fa-xs fa-circle-question text-gray-500"></i></span>
        </div>

        <i class="fas fa-fw fa-sm <?= $data->plan_settings->password_protection_is_enabled ? 'fa-check text-success' : 'fa-times' ?>"></i>
    </div>

    <div class="d-flex justify-content-between align-items-center my-3 <?= $data->plan_settings->sensitive_content_is_enabled ? null : 'text-muted' ?>">
        <div>
            <?= l('global.plan_settings.sensitive_content_is_enabled') ?>
            <span class="ml-1" data-toggle="tooltip" title="<?= l('global.plan_settings.sensitive_content_is_enabled_help') ?>"><i class="fas fa-fw fa-xs fa-circle-question text-gray-500"></i></span>
        </div>

        <i class="fas fa-fw fa-sm <?= $data->plan_settings->sensitive_content_is_enabled ? 'fa-check text-success' : 'fa-times' ?>"></i>
    </div>

    <div class="d-flex justify-content-between align-items-center my-3 <?= $data->plan_settings->cloaking_is_enabled ? null : 'text-muted' ?>">
        <div>
            <?= l('global.plan_settings.cloaking_is_enabled') ?>
            <span class="ml-1" data-toggle="tooltip" title="<?= l('global.plan_settings.cloaking_is_enabled_help') ?>"><i class="fas fa-fw fa-xs fa-circle-question text-gray-500"></i></span>
        </div>

        <i class="fas fa-fw fa-sm <?= $data->plan_settings->cloaking_is_enabled ? 'fa-check text-success' : 'fa-times' ?>"></i>
    </div>

    <div class="d-flex justify-content-between align-items-center my-3 <?= $data->plan_settings->app_linking_is_enabled ? null : 'text-muted' ?>">
        <div>
            <?= l('global.plan_settings.app_linking_is_enabled') ?>
            <span class="ml-1" data-toggle="tooltip" title="<?= l('global.plan_settings.app_linking_is_enabled_help') ?>"><i class="fas fa-fw fa-xs fa-circle-question text-gray-500"></i></span>
        </div>

        <i class="fas fa-fw fa-sm <?= $data->plan_settings->app_linking_is_enabled ? 'fa-check text-success' : 'fa-times' ?>"></i>
    </div>

    <div class="d-flex justify-content-between align-items-center my-3 <?= $data->plan_settings->targeting_is_enabled ? null : 'text-muted' ?>">
        <div>
            <?= l('global.plan_settings.targeting_is_enabled') ?>
            <span class="ml-1" data-toggle="tooltip" title="<?= l('global.plan_settings.targeting_is_enabled_help') ?>"><i class="fas fa-fw fa-xs fa-circle-question text-gray-500"></i></span>
        </div>

        <i class="fas fa-fw fa-sm <?= $data->plan_settings->targeting_is_enabled ? 'fa-check text-success' : 'fa-times' ?>"></i>
    </div>

    <div class="d-flex justify-content-between align-items-center my-3 <?= $data->plan_settings->custom_url_is_enabled ? null : 'text-muted' ?>">
        <div>
            <?= l('global.plan_settings.custom_url_is_enabled') ?>
            <span class="ml-1" data-toggle="tooltip" title="<?= l('global.plan_settings.custom_url_is_enabled_help') ?>"><i class="fas fa-fw fa-xs fa-circle-question text-gray-500"></i></span>
        </div>

        <i class="fas fa-fw fa-sm <?= $data->plan_settings->custom_url_is_enabled ? 'fa-check text-success' : 'fa-times' ?>"></i>
    </div>

    <div class="d-flex justify-content-between align-items-center my-3 <?= $data->plan_settings->utm_parameters_is_enabled ? null : 'text-muted' ?>">
        <div>
            <?= l('global.plan_settings.utm_parameters_is_enabled') ?>
            <span class="ml-1" data-toggle="tooltip" title="<?= l('global.plan_settings.utm_parameters_is_enabled_help') ?>"><i class="fas fa-fw fa-xs fa-circle-question text-gray-500"></i></span>
        </div>

        <i class="fas fa-fw fa-sm <?= $data->plan_settings->utm_parameters_is_enabled ? 'fa-check text-success' : 'fa-times' ?>"></i>
    </div>

    <?php if(settings()->codes->qr_reader_is_enabled): ?>
        <div class="d-flex justify-content-between align-items-center my-3 <?= $data->plan_settings->qr_reader_is_enabled ? null : 'text-muted' ?>">
            <div>
                <?= l('global.plan_settings.qr_reader_is_enabled') ?>
                <span class="ml-1" data-toggle="tooltip" title="<?= l('global.plan_settings.qr_reader_is_enabled_help') ?>"><i class="fas fa-fw fa-xs fa-circle-question text-gray-500"></i></span>
            </div>

            <i class="fas fa-fw fa-sm <?= $data->plan_settings->qr_reader_is_enabled ? 'fa-check text-success' : 'fa-times' ?>"></i>
        </div>
    <?php endif ?>

    <?php if(settings()->codes->barcode_reader_is_enabled): ?>
        <div class="d-flex justify-content-between align-items-center my-3 <?= $data->plan_settings->barcode_reader_is_enabled ? null : 'text-muted' ?>">
            <div>
                <?= l('global.plan_settings.barcode_reader_is_enabled') ?>
                <span class="ml-1" data-toggle="tooltip" title="<?= l('global.plan_settings.barcode_reader_is_enabled_help') ?>"><i class="fas fa-fw fa-xs fa-circle-question text-gray-500"></i></span>
            </div>

            <i class="fas fa-fw fa-sm <?= $data->plan_settings->barcode_reader_is_enabled ? 'fa-check text-success' : 'fa-times' ?>"></i>
        </div>
    <?php endif ?>

    <?php if(settings()->main->api_is_enabled): ?>
        <div class="d-flex justify-content-between align-items-center my-3 <?= $data->plan_settings->api_is_enabled ? null : 'text-muted' ?>">
            <div>
                <?= l('global.plan_settings.api_is_enabled') ?>
                <span class="ml-1" data-toggle="tooltip" title="<?= l('global.plan_settings.api_is_enabled_help') ?>"><i class="fas fa-fw fa-xs fa-circle-question text-gray-500"></i></span>
            </div>

            <i class="fas fa-fw fa-sm <?= $data->plan_settings->api_is_enabled ? 'fa-check text-success' : 'fa-times' ?>"></i>
        </div>
    <?php endif ?>

    <?php if(settings()->main->white_labeling_is_enabled): ?>
        <div class="d-flex justify-content-between align-items-center my-3 <?= $data->plan_settings->white_labeling_is_enabled ? null : 'text-muted' ?>">
            <div>
                <?= l('global.plan_settings.white_labeling_is_enabled') ?>
                <span class="ml-1" data-toggle="tooltip" title="<?= l('global.plan_settings.white_labeling_is_enabled_help') ?>"><i class="fas fa-fw fa-xs fa-circle-question text-gray-500"></i></span>
            </div>

            <i class="fas fa-fw fa-sm <?= $data->plan_settings->white_labeling_is_enabled ? 'fa-check text-success' : 'fa-times' ?>"></i>
        </div>
    <?php endif ?>

    <?php $enabled_exports_count = count(array_filter((array) $data->plan_settings->export)); ?>

    <?php ob_start() ?>
    <div class='d-flex flex-column'>
        <?php foreach(['csv', 'json', 'pdf'] as $key): ?>
            <?php if($data->plan_settings->export->{$key}): ?>
                <span class='my-1'><?= sprintf(l('global.export_to'), mb_strtoupper($key)) ?></span>
            <?php else: ?>
                <s class='my-1'><?= sprintf(l('global.export_to'), mb_strtoupper($key)) ?></s>
            <?php endif ?>
        <?php endforeach ?>
    </div>
    <?php $html = ob_get_clean() ?>

    <div class="d-flex justify-content-between align-items-center my-3 <?= $enabled_exports_count ? null : 'text-muted' ?>">
        <div>
            <?= sprintf(l('global.plan_settings.export'), $enabled_exports_count) ?>
            <span class="mr-1" data-html="true" data-toggle="tooltip" title="<?= $html ?>"><i class="fas fa-fw fa-xs fa-circle-question text-gray-500"></i></span>
        </div>

        <i class="fas fa-fw fa-sm <?= $enabled_exports_count ? 'fa-check text-success' : 'fa-times' ?>"></i>
    </div>

    <div class="d-flex justify-content-between align-items-center my-3 <?= $data->plan_settings->no_ads ? null : 'text-muted' ?>">
        <div>
            <?= l('global.plan_settings.no_ads') ?>
            <span class="ml-1" data-toggle="tooltip" title="<?= l('global.plan_settings.no_ads_help') ?>"><i class="fas fa-fw fa-xs fa-circle-question text-gray-500"></i></span>
        </div>

        <i class="fas fa-fw fa-sm <?= $data->plan_settings->no_ads ? 'fa-check text-success' : 'fa-times' ?>"></i>
    </div>

    <div class="d-flex justify-content-between align-items-center my-3 <?= $data->plan_settings->removable_branding ? null : 'text-muted' ?>">
        <div>
            <?= l('global.plan_settings.removable_branding') ?>
            <span class="ml-1" data-toggle="tooltip" title="<?= l('global.plan_settings.removable_branding_help') ?>"><i class="fas fa-fw fa-xs fa-circle-question text-gray-500"></i></span>
        </div>

        <i class="fas fa-fw fa-sm <?= $data->plan_settings->removable_branding ? 'fa-check text-success' : 'fa-times' ?>"></i>
    </div>
</div>
