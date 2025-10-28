<?php defined('ALTUMCODE') || die() ?>

<div class="container">
    <?= \Altum\Alerts::output_alerts() ?>

    <?= $this->views['account_header_menu'] ?>

    <div class="d-flex align-items-center mb-3">
        <h1 class="h4 m-0"><?= l('account_preferences.header') ?></h1>

        <div class="ml-2">
            <span data-toggle="tooltip" title="<?= l('account_preferences.subheader') ?>">
                <i class="fas fa-fw fa-info-circle text-muted"></i>
            </span>
        </div>
    </div>

    <div class="card">
        <div class="card-body">

            <form action="" method="post" role="form" enctype="multipart/form-data">
                <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>" />

                <?php if(settings()->main->white_labeling_is_enabled): ?>
                    <button class="btn btn-block btn-gray-200 mb-4" type="button" data-toggle="collapse" data-target="#white_labeling_container" aria-expanded="false" aria-controls="white_labeling_container">
                        <i class="fas fa-fw fa-cube fa-sm mr-1"></i> <?= l('account_preferences.white_labeling') ?>
                    </button>

                    <div class="collapse" id="white_labeling_container">
                        <div <?= $this->user->plan_settings->white_labeling_is_enabled ? null : get_plan_feature_disabled_info() ?>>
                            <div class="<?= $this->user->plan_settings->white_labeling_is_enabled ? null : 'container-disabled' ?>">
                                <div class="form-group">
                                    <label for="white_label_title"><i class="fas fa-fw fa-sm fa-heading text-muted mr-1"></i> <?= l('account_preferences.white_label_title') ?></label>
                                    <input type="text" id="white_label_title" name="white_label_title" class="form-control <?= \Altum\Alerts::has_field_errors('white_label_title') ? 'is-invalid' : null ?>" value="<?= $this->user->preferences->white_label_title ?>" maxlength="32" />
                                    <?= \Altum\Alerts::output_field_error('white_label_title') ?>
                                </div>

                                <div class="form-group" data-file-image-input-wrapper data-file-input-wrapper-size-limit="<?= get_max_upload() ?>" data-file-input-wrapper-size-limit-error="<?= sprintf(l('global.error_message.file_size_limit'), get_max_upload()) ?>">
                                    <label for="white_label_logo_light"><i class="fas fa-fw fa-sm fa-sun text-muted mr-1"></i> <?= l('account_preferences.white_label_logo_light') ?></label>
                                    <?= include_view(THEME_PATH . 'views/partials/file_image_input.php', ['uploads_file_key' => 'users', 'file_key' => 'white_label_logo_light', 'already_existing_image' => $this->user->preferences->white_label_logo_light]) ?>
                                    <small class="form-text text-muted"><?= sprintf(l('global.accessibility.whitelisted_file_extensions'), \Altum\Uploads::get_whitelisted_file_extensions_accept('users')) . ' ' . sprintf(l('global.accessibility.file_size_limit'), get_max_upload()) ?></small>
                                </div>

                                <div class="form-group" data-file-image-input-wrapper data-file-input-wrapper-size-limit="<?= get_max_upload() ?>" data-file-input-wrapper-size-limit-error="<?= sprintf(l('global.error_message.file_size_limit'), get_max_upload()) ?>">
                                    <label for="white_label_logo_dark"><i class="fas fa-fw fa-sm fa-moon text-muted mr-1"></i> <?= l('account_preferences.white_label_logo_dark') ?></label>
                                    <?= include_view(THEME_PATH . 'views/partials/file_image_input.php', ['uploads_file_key' => 'users', 'file_key' => 'white_label_logo_dark', 'already_existing_image' => $this->user->preferences->white_label_logo_dark]) ?>
                                    <small class="form-text text-muted"><?= sprintf(l('global.accessibility.whitelisted_file_extensions'), \Altum\Uploads::get_whitelisted_file_extensions_accept('users')) . ' ' . sprintf(l('global.accessibility.file_size_limit'), get_max_upload()) ?></small>
                                </div>

                                <div class="form-group" data-file-image-input-wrapper data-file-input-wrapper-size-limit="<?= get_max_upload() ?>" data-file-input-wrapper-size-limit-error="<?= sprintf(l('global.error_message.file_size_limit'), get_max_upload()) ?>">
                                    <label for="white_label_favicon"><i class="fas fa-fw fa-sm fa-icons text-muted mr-1"></i> <?= l('account_preferences.white_label_favicon') ?></label>
                                    <?= include_view(THEME_PATH . 'views/partials/file_image_input.php', ['uploads_file_key' => 'users', 'file_key' => 'white_label_favicon', 'already_existing_image' => $this->user->preferences->white_label_favicon]) ?>
                                    <small class="form-text text-muted"><?= sprintf(l('global.accessibility.whitelisted_file_extensions'), \Altum\Uploads::get_whitelisted_file_extensions_accept('users')) . ' ' . sprintf(l('global.accessibility.file_size_limit'), get_max_upload()) ?></small>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif ?>


                <button class="btn btn-block btn-gray-200 mb-4" type="button" data-toggle="collapse" data-target="#default_settings_container" aria-expanded="false" aria-controls="default_settings_container">
                    <i class="fas fa-fw fa-wrench fa-sm mr-1"></i> <?= l('account_preferences.default_settings') ?>
                </button>

                <div class="collapse" id="default_settings_container">
                    <div class="form-group">
                        <label for="default_results_per_page"><i class="fas fa-fw fa-sm fa-list-ol text-muted mr-1"></i> <?= l('account_preferences.default_results_per_page') ?></label>
                        <select id="default_results_per_page" name="default_results_per_page" class="custom-select <?= \Altum\Alerts::has_field_errors('default_results_per_page') ? 'is-invalid' : null ?>">
                            <?php foreach([10, 25, 50, 100, 250, 500, 1000] as $key): ?>
                                <option value="<?= $key ?>" <?= ($this->user->preferences->default_results_per_page ?? settings()->main->default_results_per_page) == $key ? 'selected="selected"' : null ?>><?= $key ?></option>
                            <?php endforeach ?>
                        </select>
                        <?= \Altum\Alerts::output_field_error('default_results_per_page') ?>
                    </div>

                    <div class="form-group">
                        <label for="default_order_type"><i class="fas fa-fw fa-sm fa-sort text-muted mr-1"></i> <?= l('account_preferences.default_order_type') ?></label>
                        <select id="default_order_type" name="default_order_type" class="custom-select <?= \Altum\Alerts::has_field_errors('default_order_type') ? 'is-invalid' : null ?>">
                            <option value="ASC" <?= ($this->user->preferences->default_order_type ?? settings()->main->default_order_type) == 'ASC' ? 'selected="selected"' : null ?>><?= l('global.filters.order_type_asc') ?></option>
                            <option value="DESC" <?= ($this->user->preferences->default_order_type ?? settings()->main->default_order_type) == 'DESC' ? 'selected="selected"' : null ?>><?= l('global.filters.order_type_desc') ?></option>
                        </select>
                        <?= \Altum\Alerts::output_field_error('default_order_type') ?>
                    </div>

                    <div class="form-group">
                        <label for="links_default_order_by"><i class="fas fa-fw fa-sm fa-link text-muted mr-1"></i> <?= sprintf(l('account_preferences.default_order_by_x'), l('links.title')) ?></label>
                        <select id="links_default_order_by" name="links_default_order_by" class="custom-select <?= \Altum\Alerts::has_field_errors('links_default_order_by') ? 'is-invalid' : null ?>">
                            <option value="link_id" <?= $this->user->preferences->links_default_order_by == 'link_id' ? 'selected="selected"' : null ?>><?= l('global.id') ?></option>
                            <option value="datetime" <?= $this->user->preferences->links_default_order_by == 'datetime' ? 'selected="selected"' : null ?>><?= l('global.filters.order_by_datetime') ?></option>
                            <option value="last_datetime" <?= $this->user->preferences->links_default_order_by == 'last_datetime' ? 'selected="selected"' : null ?>><?= l('global.filters.order_by_last_datetime') ?></option>
                            <option value="url" <?= $this->user->preferences->links_default_order_by == 'url' ? 'selected="selected"' : null ?>><?= l('links.filters.order_by_url') ?></option>
                            <option value="location_url" <?= $this->user->preferences->links_default_order_by == 'location_url' ? 'selected="selected"' : null ?>><?= l('links.filters.order_by_location_url') ?></option>
                            <option value="pageviews" <?= $this->user->preferences->links_default_order_by == 'pageviews' ? 'selected="selected"' : null ?>><?= l('links.filters.order_by_pageviews') ?></option>
                        </select>
                        <?= \Altum\Alerts::output_field_error('links_default_order_by') ?>
                    </div>

                    <?php if(settings()->codes->ai_qr_codes_is_enabled): ?>
                        <div class="form-group">
                            <label for="ai_qr_codes_default_order_by"><i class="fas fa-fw fa-sm fa-robot text-muted mr-1"></i> <?= sprintf(l('account_preferences.default_order_by_x'), l('ai_qr_codes.title')) ?></label>
                            <select id="ai_qr_codes_default_order_by" name="ai_qr_codes_default_order_by" class="custom-select <?= \Altum\Alerts::has_field_errors('ai_qr_codes_default_order_by') ? 'is-invalid' : null ?>">
                                <option value="ai_qr_code_id" <?= $this->user->preferences->ai_qr_codes_default_order_by == 'ai_qr_code_id' ? 'selected="selected"' : null ?>><?= l('global.id') ?></option>
                                <option value="datetime" <?= $this->user->preferences->ai_qr_codes_default_order_by == 'datetime' ? 'selected="selected"' : null ?>><?= l('global.filters.order_by_datetime') ?></option>
                                <option value="last_datetime" <?= $this->user->preferences->ai_qr_codes_default_order_by == 'last_datetime' ? 'selected="selected"' : null ?>><?= l('global.filters.order_by_last_datetime') ?></option>
                                <option value="name" <?= $this->user->preferences->ai_qr_codes_default_order_by == 'name' ? 'selected="selected"' : null ?>><?= l('global.name') ?></option>
                            </select>
                            <?= \Altum\Alerts::output_field_error('ai_qr_codes_default_order_by') ?>
                        </div>
                    <?php endif ?>

                    <?php if(settings()->codes->qr_codes_is_enabled): ?>
                        <div class="form-group">
                            <label for="qr_codes_default_order_by"><i class="fas fa-fw fa-sm fa-qrcode text-muted mr-1"></i> <?= sprintf(l('account_preferences.default_order_by_x'), l('qr_codes.title')) ?></label>
                            <select id="qr_codes_default_order_by" name="qr_codes_default_order_by" class="custom-select <?= \Altum\Alerts::has_field_errors('qr_codes_default_order_by') ? 'is-invalid' : null ?>">
                                <option value="qr_code_id" <?= $this->user->preferences->qr_codes_default_order_by == 'qr_code_id' ? 'selected="selected"' : null ?>><?= l('global.id') ?></option>
                                <option value="datetime" <?= $this->user->preferences->qr_codes_default_order_by == 'datetime' ? 'selected="selected"' : null ?>><?= l('global.filters.order_by_datetime') ?></option>
                                <option value="last_datetime" <?= $this->user->preferences->qr_codes_default_order_by == 'last_datetime' ? 'selected="selected"' : null ?>><?= l('global.filters.order_by_last_datetime') ?></option>
                                <option value="name" <?= $this->user->preferences->qr_codes_default_order_by == 'name' ? 'selected="selected"' : null ?>><?= l('global.name') ?></option>
                                <option value="type" <?= $this->user->preferences->qr_codes_default_order_by == 'type' ? 'selected="selected"' : null ?>><?= l('global.type') ?></option>
                            </select>
                            <?= \Altum\Alerts::output_field_error('qr_codes_default_order_by') ?>
                        </div>
                    <?php endif ?>

                    <?php if(settings()->codes->barcodes_is_enabled): ?>
                        <div class="form-group">
                            <label for="barcodes_default_order_by"><i class="fas fa-fw fa-sm fa-barcode text-muted mr-1"></i> <?= sprintf(l('account_preferences.default_order_by_x'), l('barcodes.title')) ?></label>
                            <select id="barcodes_default_order_by" name="barcodes_default_order_by" class="custom-select <?= \Altum\Alerts::has_field_errors('barcodes_default_order_by') ? 'is-invalid' : null ?>">
                                <option value="barcode_id" <?= $this->user->preferences->barcodes_default_order_by == 'barcode_id' ? 'selected="selected"' : null ?>><?= l('global.id') ?></option>
                                <option value="datetime" <?= $this->user->preferences->barcodes_default_order_by == 'datetime' ? 'selected="selected"' : null ?>><?= l('global.filters.order_by_datetime') ?></option>
                                <option value="last_datetime" <?= $this->user->preferences->barcodes_default_order_by == 'last_datetime' ? 'selected="selected"' : null ?>><?= l('global.filters.order_by_last_datetime') ?></option>
                                <option value="name" <?= $this->user->preferences->barcodes_default_order_by == 'name' ? 'selected="selected"' : null ?>><?= l('global.name') ?></option>
                                <option value="type" <?= $this->user->preferences->barcodes_default_order_by == 'type' ? 'selected="selected"' : null ?>><?= l('global.type') ?></option>
                            </select>
                            <?= \Altum\Alerts::output_field_error('barcodes_default_order_by') ?>
                        </div>
                    <?php endif ?>

                    <?php if(settings()->links->projects_is_enabled): ?>
                        <div class="form-group">
                            <label for="projects_default_order_by"><i class="fas fa-fw fa-sm fa-project-diagram text-muted mr-1"></i> <?= sprintf(l('account_preferences.default_order_by_x'), l('projects.title')) ?></label>
                            <select id="projects_default_order_by" name="projects_default_order_by" class="custom-select <?= \Altum\Alerts::has_field_errors('projects_default_order_by') ? 'is-invalid' : null ?>">
                                <option value="project_id" <?= $this->user->preferences->projects_default_order_by == 'project_id' ? 'selected="selected"' : null ?>><?= l('global.id') ?></option>
                                <option value="datetime" <?= $this->user->preferences->projects_default_order_by == 'datetime' ? 'selected="selected"' : null ?>><?= l('global.filters.order_by_datetime') ?></option>
                                <option value="last_datetime" <?= $this->user->preferences->projects_default_order_by == 'last_datetime' ? 'selected="selected"' : null ?>><?= l('global.filters.order_by_last_datetime') ?></option>
                                <option value="name" <?= $this->user->preferences->projects_default_order_by == 'name' ? 'selected="selected"' : null ?>><?= l('global.name') ?></option>
                            </select>
                            <?= \Altum\Alerts::output_field_error('projects_default_order_by') ?>
                        </div>
                    <?php endif ?>

                    <?php if(settings()->links->pixels_is_enabled): ?>
                        <div class="form-group">
                            <label for="pixels_default_order_by"><i class="fas fa-fw fa-sm fa-adjust text-muted mr-1"></i> <?= sprintf(l('account_preferences.default_order_by_x'), l('pixels.title')) ?></label>
                            <select id="pixels_default_order_by" name="pixels_default_order_by" class="custom-select <?= \Altum\Alerts::has_field_errors('pixels_default_order_by') ? 'is-invalid' : null ?>">
                                <option value="pixel_id" <?= $this->user->preferences->pixels_default_order_by == 'pixel_id' ? 'selected="selected"' : null ?>><?= l('global.id') ?></option>
                                <option value="datetime" <?= $this->user->preferences->pixels_default_order_by == 'datetime' ? 'selected="selected"' : null ?>><?= l('global.filters.order_by_datetime') ?></option>
                                <option value="last_datetime" <?= $this->user->preferences->pixels_default_order_by == 'last_datetime' ? 'selected="selected"' : null ?>><?= l('global.filters.order_by_last_datetime') ?></option>
                                <option value="name" <?= $this->user->preferences->pixels_default_order_by == 'name' ? 'selected="selected"' : null ?>><?= l('global.name') ?></option>
                            </select>
                            <?= \Altum\Alerts::output_field_error('pixels_default_order_by') ?>
                        </div>
                    <?php endif ?>

                    <?php if(settings()->links->domains_is_enabled): ?>
                        <div class="form-group">
                            <label for="domains_default_order_by"><i class="fas fa-fw fa-sm fa-globe text-muted mr-1"></i> <?= sprintf(l('account_preferences.default_order_by_x'), l('domains.title')) ?></label>
                            <select id="domains_default_order_by" name="domains_default_order_by" class="custom-select <?= \Altum\Alerts::has_field_errors('domains_default_order_by') ? 'is-invalid' : null ?>">
                                <option value="domain_id" <?= $this->user->preferences->domains_default_order_by == 'domain_id' ? 'selected="selected"' : null ?>><?= l('global.id') ?></option>
                                <option value="datetime" <?= $this->user->preferences->domains_default_order_by == 'datetime' ? 'selected="selected"' : null ?>><?= l('global.filters.order_by_datetime') ?></option>
                                <option value="last_datetime" <?= $this->user->preferences->domains_default_order_by == 'last_datetime' ? 'selected="selected"' : null ?>><?= l('global.filters.order_by_last_datetime') ?></option>
                                <option value="host" <?= $this->user->preferences->domains_default_order_by == 'host' ? 'selected="selected"' : null ?>><?= l('domains.table.host') ?></option>
                            </select>
                            <?= \Altum\Alerts::output_field_error('domains_default_order_by') ?>
                        </div>
                    <?php endif ?>

                    <div class="form-group custom-control custom-switch">
                        <input id="links_auto_copy_link" name="links_auto_copy_link" type="checkbox" class="custom-control-input" <?= $this->user->preferences->links_auto_copy_link ? 'checked="checked"' : null ?>>
                        <label class="custom-control-label" for="links_auto_copy_link"><?= l('account_preferences.links_auto_copy_link') ?></label>
                        <?= \Altum\Alerts::output_field_error('links_auto_copy_link') ?>
                    </div>
                </div>


                <button class="btn btn-block btn-gray-200 mb-4" type="button" data-toggle="collapse" data-target="#dashboard_settings_container" aria-expanded="false" aria-controls="dashboard_settings_container">
                    <i class="fas fa-fw fa-table-cells fa-sm mr-1"></i> <?= l('account_preferences.dashboard_features') ?>
                </button>

                <div class="collapse" id="dashboard_settings_container">
                    <div class="form-group">
                        <label><i class="fas fa-fw fa-sm fa-table-cells text-muted mr-1"></i> <?= l('account_preferences.dashboard_features') ?></label>
                    </div>

                    <div id="dashboard_features">
                        <?php $dashboard_features = ((array) $this->user->preferences->dashboard) + array_fill_keys(['ai_qr_codes', 'qr_codes', 'barcodes', 'links'], true) ?>
                        <?php $index = 0; ?>
                        <?php foreach($dashboard_features as $feature => $is_enabled): ?>
                            <div class="d-flex">
                                <span class="mr-2">
                                    <i class="fas fa-fw fa-sm fa-bars text-muted cursor-grab drag"></i>
                                </span>

                                <div class="form-group custom-control custom-checkbox" data-dashboard-feature>
                                    <input id="<?= 'dashboard_' . $feature ?>" name="dashboard[<?= $index++ ?>]" value="<?= $feature ?>" type="checkbox" class="custom-control-input" <?= $is_enabled ? 'checked="checked"' : null ?>>
                                    <label class="custom-control-label" for="<?= 'dashboard_' . $feature ?>"><?= l('dashboard.' . $feature . '.header') ?></label>
                                </div>
                            </div>
                        <?php endforeach ?>
                    </div>
                </div>

                <button class="btn btn-block btn-gray-200 mb-4" type="button" data-toggle="collapse" data-target="#tracking_settings_container" aria-expanded="false" aria-controls="tracking_settings_container">
                    <i class="fas fa-fw fa-eye fa-sm mr-1"></i> <?= l('account_preferences.tracking_settings') ?>
                </button>

                <div class="collapse" id="tracking_settings_container">
                    <div class="form-group" data-character-counter="textarea">
                        <label for="excluded_ips" class="d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-fw fa-sm fa-eye-slash text-muted mr-1"></i> <?= l('account_preferences.excluded_ips') ?></span>
                            <small class="text-muted" data-character-counter-wrapper></small>
                        </label>
                        <textarea id="excluded_ips" class="form-control" name="excluded_ips" maxlength="500"><?= implode(',', $this->user->preferences->excluded_ips ?? []) ?></textarea>
                        <small class="form-text text-muted"><?= l('account_preferences.excluded_ips_help') ?></small>
                    </div>
                </div>

                <button type="submit" name="submit" class="btn btn-block btn-primary"><?= l('global.update') ?></button>
            </form>
        </div>
    </div>
</div>

<?php ob_start() ?>
<script src="<?= ASSETS_FULL_URL . 'js/libraries/sortable.js?v=' . PRODUCT_CODE ?>"></script>
<script>
    'use strict';
    
    let sortable = Sortable.create(document.getElementById('dashboard_features'), {
        animation: 150,
        handle: '.drag',
        onUpdate: event => {

            document.querySelectorAll('#dashboard_features > div').forEach((elm, i) => {
                let input = elm.querySelector('input[type="checkbox"]');
                if(input) {
                    input.setAttribute('name', `dashboard[${i}]`);
                }
            });

        }
    });
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
