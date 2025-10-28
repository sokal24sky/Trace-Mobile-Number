<?php defined('ALTUMCODE') || die() ?>


<div class="container">
    <?= \Altum\Alerts::output_alerts() ?>

    <div class="d-print-none">
        <?php if(settings()->main->breadcrumbs_is_enabled): ?>
            <nav aria-label="breadcrumb">
                <ol class="custom-breadcrumbs small">
                    <li>
                        <a href="<?= url('barcodes') ?>"><?= l('barcodes.breadcrumb') ?></a><i class="fas fa-fw fa-angle-right"></i>
                    </li>
                    <li class="active" aria-current="page"><?= l('barcode_update.breadcrumb') ?></li>
                </ol>
            </nav>
        <?php endif ?>

        <div class="d-flex justify-content-between mb-4">
            <h1 class="h4 text-truncate mb-0 mr-2"><i class="fas fa-fw fa-xs fa-barcode mr-1"></i> <?= l('barcode_update.header') ?></h1>

            <?= include_view(THEME_PATH . 'views/barcodes/barcode_dropdown_button.php', ['id' => $data->barcode->barcode_id, 'resource_name' => $data->barcode->name]) ?>
        </div>
    </div>

    <form id="form" action="" method="post" role="form" enctype="multipart/form-data">
        <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>" />
        <input type="hidden" name="api_key" value="<?= $this->user->api_key ?>" />
        <input type="hidden" name="barcode" value="" />
        <input type="hidden" name="embedded_data" value="<?= $data->barcode->embedded_data ?? null ?>" />
        <input type="hidden" name="reload" value="" data-reload-barcode />

        <div class="row">
            <div class="col-12 col-xl-6 d-print-none mb-5 mb-xl-0">
                <div class="card">
                    <div class="card-body">
                        <div class="notification-container"></div>

                        <div class="form-group">
                            <label for="name"><i class="fas fa-fw fa-signature fa-sm text-muted mr-1"></i> <?= l('global.name') ?></label>
                            <input type="text" id="name" name="name" class="form-control <?= \Altum\Alerts::has_field_errors('name') ? 'is-invalid' : null ?>" value="<?= $data->barcode->name ?>" maxlength="64" required="required" />
                            <?= \Altum\Alerts::output_field_error('name') ?>
                        </div>

                        <div class="form-group">
                            <label for="type"><i class="fas fa-fw fa-qrcode fa-sm text-muted mr-1"></i> <?= l('barcodes.input.type') ?></label>
                            <select id="type" name="type" class="custom-select">
                                <?php foreach(array_keys($data->available_barcodes) as $type): ?>
                                    <?php if($this->user->plan_settings->enabled_barcodes->{$type}): ?>
                                        <option value="<?= $type ?>" <?= $data->barcode->type == $type ? 'selected="selected"' : null ?>><?= $type ?></option>
                                    <?php endif ?>
                                <?php endforeach ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="value"><i class="fas fa-fw fa-database fa-sm text-muted mr-1"></i> <?= l('barcodes.input.value') ?></label>
                            <textarea id="value" name="value" class="form-control <?= \Altum\Alerts::has_field_errors('value') ? 'is-invalid' : null ?>" required="required" data-reload-barcode><?= $data->barcode->value ?? null ?></textarea>
                            <?= \Altum\Alerts::output_field_error('value') ?>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input id="display_text" name="display_text" type="checkbox" class="custom-control-input" <?= ($data->barcode->settings->display_text ?? null) ? 'checked="checked"' : null ?> data-reload-barcode />
                                <label class="custom-control-label" for="display_text"><?= l('barcodes.input.display_text') ?></label>
                            </div>
                        </div>

                        <button class="btn btn-block btn-gray-200 my-4" type="button" data-toggle="collapse" data-target="#colors_container" aria-expanded="false" aria-controls="colors_container">
                            <i class="fas fa-fw fa-palette fa-sm mr-1"></i> <?= l('barcodes.input.colors') ?>
                        </button>

                        <div class="collapse" id="colors_container">
                            <div class="form-group">
                                <label for="foreground_color"><i class="fas fa-fw fa-paint-brush fa-sm text-muted mr-1"></i> <?= l('barcodes.input.foreground_color') ?></label>
                                <input type="hidden" id="foreground_color" name="foreground_color" class="form-control <?= \Altum\Alerts::has_field_errors('foreground_color') ? 'is-invalid' : null ?>" value="<?= $data->barcode->settings->foreground_color ?>" data-reload-barcode data-color-picker />
                                <?= \Altum\Alerts::output_field_error('foreground_color') ?>
                            </div>

                            <div class="form-group">
                                <label for="background_color"><i class="fas fa-fw fa-fill fa-sm text-muted mr-1"></i> <?= l('barcodes.input.background_color') ?></label>
                                <input type="hidden" id="background_color" name="background_color" class="form-control <?= \Altum\Alerts::has_field_errors('background_color') ? 'is-invalid' : null ?>" value="<?= $data->barcode->settings->background_color ?>" data-reload-barcode data-color-picker />
                                <?= \Altum\Alerts::output_field_error('background_color') ?>
                            </div>
                        </div>

                        <button class="btn btn-block btn-gray-200 my-4" type="button" data-toggle="collapse" data-target="#options_container" aria-expanded="false" aria-controls="options_container">
                            <i class="fas fa-fw fa-wrench fa-sm mr-1"></i> <?= l('barcodes.input.options') ?>
                        </button>

                        <div class="collapse" id="options_container">
                            <div class="form-group" data-range-counter data-range-counter-suffix="x">
                                <label for="width_scale"><i class="fas fa-fw fa-arrows-alt-h fa-sm text-muted mr-1"></i> <?= l('barcodes.input.width_scale') ?></label>
                                <input id="width_scale" type="range" min="1" max="10" step="1" name="width_scale" value="<?= $data->barcode->settings->width_scale ?>" class="form-control-range <?= \Altum\Alerts::has_field_errors('width_scale') ? 'is-invalid' : null ?>" data-reload-barcode />
                                <?= \Altum\Alerts::output_field_error('width_scale') ?>
                            </div>

                            <div class="form-group">
                                <label for="height"><i class="fas fa-fw fa-arrows-alt-v fa-sm text-muted mr-1"></i> <?= l('barcodes.input.height') ?></label>
                                <div class="input-group">
                                    <input id="height" type="number" min="30" max="1000" name="height" class="form-control <?= \Altum\Alerts::has_field_errors('height') ? 'is-invalid' : null ?>" value="<?= $data->barcode->settings->height ?>" data-reload-barcode />
                                    <div class="input-group-append">
                                        <span class="input-group-text">px</span>
                                    </div>
                                </div>
                                <?= \Altum\Alerts::output_field_error('height') ?>
                            </div>

                            <?php if(settings()->links->projects_is_enabled): ?>
                                <div class="form-group">
                                    <div class="d-flex flex-wrap flex-row justify-content-between">
                                        <label for="project_id"><i class="fas fa-fw fa-sm fa-project-diagram text-muted mr-1"></i> <?= l('projects.project_id') ?></label>
                                        <a href="<?= url('project-create') ?>" target="_blank" class="small mb-2"><i class="fas fa-fw fa-sm fa-plus mr-1"></i> <?= l('projects.create') ?></a>
                                    </div>
                                    <select id="project_id" name="project_id" class="custom-select">
                                        <option value=" "><?= l('global.none') ?></option>
                                        <?php foreach($data->projects as $row): ?>
                                            <option value="<?= $row->project_id ?>" <?= $data->barcode->project_id == $row->project_id ? 'selected="selected"' : null?>><?= $row->name ?></option>
                                        <?php endforeach ?>
                                    </select>
                                </div>
                            <?php endif ?>
                        </div>

                        <button type="submit" name="submit" class="btn btn-block btn-primary mt-4"><?= l('global.update') ?></button>
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-6">
                <div class="sticky">
                    <div class="mb-4">
                        <div class="card">
                            <div class="card-body">
                                <img id="barcode" src="<?= \Altum\Uploads::get_full_url('barcodes') . $data->barcode->barcode ?>" class="img-fluid barcode" loading="lazy" />
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4 d-print-none">
                        <div class="col-12 col-lg-6 mb-3 mb-lg-0">
                            <button type="button" class="btn btn-block btn-outline-secondary d-print-none <?= $this->user->plan_settings->export->pdf ? null : 'disabled' ?>" <?= $this->user->plan_settings->export->pdf ? 'onclick="window.print();return false;"' : get_plan_feature_disabled_info() ?>>
                                <i class="fas fa-fw fa-sm fa-file-pdf mr-1"></i> <?= l('barcodes.print') ?>
                            </button>
                        </div>

                        <div class="col-12 col-lg-6 mb-3 mb-lg-0 dropdown">
                            <button type="button" class="btn btn-block btn-primary d-print-none dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-fw fa-download fa-sm mr-1"></i> <?= l('global.download') ?>
                            </button>

                            <div class="dropdown-menu">
                                <a href="<?= \Altum\Uploads::get_full_url('barcodes') . $data->barcode->barcode ?>" id="download_svg" class="dropdown-item" download="<?= get_slug($data->barcode->name) . '.svg' ?>"><?= sprintf(l('global.download_as'), 'SVG') ?></a>
                                <button type="button" class="dropdown-item" onclick="convert_svg_barcode_to_others(null, 'png', '<?= get_slug($data->barcode->name) . '.png' ?>');"><?= sprintf(l('global.download_as'), 'PNG') ?></button>
                                <button type="button" class="dropdown-item" onclick="convert_svg_barcode_to_others(null, 'jpg', '<?= get_slug($data->barcode->name) . '.jpg' ?>');"><?= sprintf(l('global.download_as'), 'JPG') ?></button>
                                <button type="button" class="dropdown-item" onclick="convert_svg_barcode_to_others(null, 'webp', '<?= get_slug($data->barcode->name) . '.webp' ?>');"><?= sprintf(l('global.download_as'), 'WEBP') ?></button>
                            </div>
                        </div>
                    </div>

                    <button id="embedded_data_container_button" class="btn btn-block btn-light my-4 d-print-none" type="button" data-toggle="collapse" data-target="#embedded_data_container" aria-expanded="false" aria-controls="embedded_data_container">
                        <i class="fas fa-fw fa-bars fa-sm mr-1"></i> <?= l('barcodes.embedded_data') ?>
                    </button>

                    <div class="collapse" id="embedded_data_container">
                        <div class="card my-4">
                            <div class="card-body" id="embedded_data_display"><?= $data->barcode->embedded_data ?></div>
                        </div>
                    </div>

                    <div class="mb-4 text-center d-print-none">
                        <small>
                            <i class="fas fa-fw fa-info-circle text-muted mr-1"></i> <span class="text-muted"><?= l('barcodes.info') ?></span>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>


<?php require THEME_PATH . 'views/barcodes/js_barcodes.php' ?>

<?php include_view(THEME_PATH . 'views/partials/color_picker_js.php') ?>


