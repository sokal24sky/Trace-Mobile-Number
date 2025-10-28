<?php defined('ALTUMCODE') || die() ?>

<div class="container text-center d-print-none">
    <?= \Altum\Alerts::output_alerts() ?>

    <h1 class="index-header mb-2"><?= $data->type ? sprintf(l('barcode.header_dynamic'), $data->type) : l('barcode.header') ?></h1>
    <p class="index-subheader mb-5"><?= $data->type ? sprintf(l('barcode.subheader_dynamic'), $data->type) : l('barcode.subheader') ?></p>

    <div class="d-flex flex-wrap justify-content-center">
        <?php foreach($data->available_barcodes as $key => $value): ?>
            <div class="mr-3 mb-3" data-toggle="tooltip" <?= $this->user->plan_settings->enabled_barcodes->{$key} ? null : 'title="' . l('global.info_message.plan_feature_no_access') . '"' ?>>
                <a
                        href="<?= url('barcode/' . str_replace('+', '-plus', $key)) ?>"
                        class="btn <?= $data->type == $key ? 'btn-primary' : 'btn-light' ?> <?= $this->user->plan_settings->enabled_barcodes->{$key} ? null : 'disabled' ?>"
                >
                    <?= $key ?>
                </a>
            </div>
        <?php endforeach ?>
    </div>
</div>

<?php if($data->type): ?>
    <div class="container mt-5">
        <div class="row">
            <div class="col-12 col-lg-7 d-print-none mb-5 mb-lg-0">
                <div class="card">
                    <div class="card-body">
                        <form id="form" action="<?= url('barcode-create') ?>" method="post" role="form" enctype="multipart/form-data">
                            <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>" />
                            <input type="hidden" name="api_key" value="<?= $this->user->api_key ?? null ?>" />
                            <input type="hidden" name="type" value="<?= $data->type ?>" />
                            <input type="hidden" name="reload" value="" data-reload-barcode />
                            <?php if(is_logged_in()): ?>
                                <input type="hidden" name="barcode" value="" />
                                <input type="hidden" name="embedded_data" value="<?= $data->values['embedded_data'] ?? null ?>" />
                                <input type="hidden" name="name" value="<?= $this->user->name ?>" />
                            <?php endif ?>

                            <div class="notification-container"></div>

                            <div class="form-group">
                                <label for="value"><i class="fas fa-fw fa-database fa-sm text-muted mr-1"></i> <?= l('barcodes.input.value') ?></label>
                                <textarea id="value" name="value" class="form-control <?= \Altum\Alerts::has_field_errors('value') ? 'is-invalid' : null ?>" required="required" data-reload-barcode><?= $data->values['settings']['value'] ?? null ?></textarea>
                                <?= \Altum\Alerts::output_field_error('value') ?>
                            </div>

                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input id="display_text" name="display_text" type="checkbox" class="custom-control-input" <?= ($data->values['settings']['display_text'] ?? null) ? 'checked="checked"' : null ?> data-reload-barcode />
                                    <label class="custom-control-label" for="display_text"><?= l('barcodes.input.display_text') ?></label>
                                </div>
                            </div>

                            <button class="btn btn-block btn-gray-200 my-4" type="button" data-toggle="collapse" data-target="#colors_container" aria-expanded="false" aria-controls="colors_container">
                                <i class="fas fa-fw fa-palette fa-sm mr-1"></i> <?= l('barcodes.input.colors') ?>
                            </button>

                            <div class="collapse" id="colors_container">
                                <div class="form-group">
                                    <label for="foreground_color"><i class="fas fa-fw fa-paint-brush fa-sm text-muted mr-1"></i> <?= l('barcodes.input.foreground_color') ?></label>
                                    <input type="hidden" id="foreground_color" name="foreground_color" class="form-control <?= \Altum\Alerts::has_field_errors('foreground_color') ? 'is-invalid' : null ?>" value="<?= $data->values['settings']['foreground_color'] ?? '#000000' ?>" data-reload-barcode data-color-picker />
                                    <?= \Altum\Alerts::output_field_error('foreground_color') ?>
                                </div>

                                <div class="form-group">
                                    <label for="background_color"><i class="fas fa-fw fa-fill fa-sm text-muted mr-1"></i> <?= l('barcodes.input.background_color') ?></label>
                                    <input type="hidden" id="background_color" name="background_color" class="form-control <?= \Altum\Alerts::has_field_errors('background_color') ? 'is-invalid' : null ?>" value="<?= $data->values['settings']['background_color'] ?? '#ffffff' ?>" data-reload-barcode data-color-picker />
                                    <?= \Altum\Alerts::output_field_error('background_color') ?>
                                </div>
                            </div>

                            <button class="btn btn-block btn-gray-200 my-4" type="button" data-toggle="collapse" data-target="#options_container" aria-expanded="false" aria-controls="options_container">
                                <i class="fas fa-fw fa-wrench fa-sm mr-1"></i> <?= l('barcodes.input.options') ?>
                            </button>

                            <div class="collapse" id="options_container">
                                <div class="form-group" data-range-counter data-range-counter-suffix="x">
                                    <label for="width_scale"><i class="fas fa-fw fa-arrows-alt-h fa-sm text-muted mr-1"></i> <?= l('barcodes.input.width_scale') ?></label>
                                    <input id="width_scale" type="range" min="1" max="10" step="1" name="width_scale" value="<?= $data->values['settings']['width_scale'] ?>" class="form-control-range <?= \Altum\Alerts::has_field_errors('width_scale') ? 'is-invalid' : null ?>" data-reload-barcode />
                                    <?= \Altum\Alerts::output_field_error('width_scale') ?>
                                </div>

                                <div class="form-group">
                                    <label for="height"><i class="fas fa-fw fa-arrows-alt-v fa-sm text-muted mr-1"></i> <?= l('barcodes.input.height') ?></label>
                                    <div class="input-group">
                                        <input id="height" type="number" min="30" max="1000" name="height" class="form-control <?= \Altum\Alerts::has_field_errors('height') ? 'is-invalid' : null ?>" value="<?= $data->values['settings']['height'] ?? 500 ?>" data-reload-barcode />
                                        <div class="input-group-append">
                                            <span class="input-group-text">px</span>
                                        </div>
                                    </div>
                                    <?= \Altum\Alerts::output_field_error('height') ?>
                                </div>
                            </div>

                            <?php if(is_logged_in()): ?>
                                <button type="submit" name="submit" class="btn btn-block btn-primary mt-4"><?= l('global.create') ?></button>
                            <?php else: ?>
                                <?php if(settings()->users->register_is_enabled): ?>
                                    <a href="<?= url('register') ?>" class="btn btn-block btn-outline-primary mt-4"><i class="fas fa-fw fa-xs fa-plus mr-1"></i> <?= l('barcode.register') ?></a>
                                <?php endif ?>
                            <?php endif ?>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-5">
                <div class="sticky">
                    <div class="mb-4">
                        <div class="card">
                            <div class="card-body">
                                <img id="barcode" src="<?= ASSETS_FULL_URL . 'images/barcode.svg' ?>" class="img-fluid barcode" loading="lazy" />
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4 d-print-none">
                        <div class="col-12 col-lg-6 mb-3 mb-lg-0">
                            <button type="button" class="btn btn-block btn-outline-secondary d-print-none <?= $this->user->plan_settings->export->pdf ? null : 'disabled' ?>" <?= $this->user->plan_settings->export->pdf ? 'onclick="window.print();return false;"' : get_plan_feature_disabled_info() ?>>
                                <i class="fas fa-fw fa-sm fa-file-pdf mr-1"></i> <?= l('barcodes.print') ?>
                            </button>
                        </div>

                        <div class="col-12 col-lg-6 mb-3 mb-lg-0">
                            <button type="button" class="btn btn-block btn-primary d-print-none dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-fw fa-download fa-sm mr-1"></i> <?= l('global.download') ?>
                            </button>

                            <div class="dropdown-menu">
                                <a href="<?= ASSETS_FULL_URL . 'images/barcode.svg' ?>" id="download_svg" class="dropdown-item" download="<?= get_slug(settings()->main->title) . '.svg' ?>"><?= sprintf(l('global.download_as'), 'SVG') ?></a>
                                <button type="button" class="dropdown-item" onclick="convert_svg_barcode_to_others(null, 'png', '<?= get_slug(settings()->main->title) . '.png' ?>');"><?= sprintf(l('global.download_as'), 'PNG') ?></button>
                                <button type="button" class="dropdown-item" onclick="convert_svg_barcode_to_others(null, 'jpg', '<?= get_slug(settings()->main->title) . '.jpg' ?>');"><?= sprintf(l('global.download_as'), 'JPG') ?></button>
                                <button type="button" class="dropdown-item" onclick="convert_svg_barcode_to_others(null, 'webp', '<?= get_slug(settings()->main->title) . '.webp' ?>');"><?= sprintf(l('global.download_as'), 'WEBP') ?></button>
                            </div>
                        </div>
                    </div>

                    <button id="embedded_data_container_button" class="btn btn-block btn-light my-4 d-none d-print-none" type="button" data-toggle="collapse" data-target="#embedded_data_container" aria-expanded="false" aria-controls="embedded_data_container">
                        <i class="fas fa-fw fa-bars fa-sm mr-1"></i> <?= l('barcodes.embedded_data') ?>
                    </button>

                    <div class="collapse" id="embedded_data_container">
                        <div class="card my-4">
                            <div class="card-body" id="embedded_data_display"></div>
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
    </div>

    <?php require THEME_PATH . 'views/barcodes/js_barcodes.php' ?>

    <?php include_view(THEME_PATH . 'views/partials/color_picker_js.php') ?>

    <?php ob_start() ?>
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "BreadcrumbList",
            "itemListElement": [
                {
                    "@type": "ListItem",
                    "position": 1,
                    "name": "<?= l('index.title') ?>",
                    "item": "<?= url() ?>"
                },
                {
                    "@type": "ListItem",
                    "position": 2,
                    "name": "<?= l('barcode.title') ?>",
                    "item": "<?= url('barcode') ?>"
                },
                {
                    "@type": "ListItem",
                    "position": 3,
                    "name": "<?= \Altum\Title::$page_title ?>",
                    "item": "<?= url(\Altum\Router::$original_request) ?>"
                }
            ]
        }
    </script>
    <?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>

<?php else: ?>

    <?php if(l('barcode.extra_content')): ?>
        <div class="container mt-4">
            <div class="card">
                <div class="card-body">
                    <?= l('barcode.extra_content') ?>
                </div>
            </div>
        </div>
    <?php endif ?>

    <?php ob_start() ?>
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "BreadcrumbList",
            "itemListElement": [
                {
                    "@type": "ListItem",
                    "position": 1,
                    "name": "<?= l('index.title') ?>",
                    "item": "<?= url() ?>"
                },
                {
                    "@type": "ListItem",
                    "position": 2,
                    "name": "<?= l('barcode.title') ?>",
                    "item": "<?= url('barcode') ?>"
                }
            ]
        }
    </script>
    <?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>

<?php endif ?>

