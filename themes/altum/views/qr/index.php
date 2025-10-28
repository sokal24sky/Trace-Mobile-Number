<?php defined('ALTUMCODE') || die() ?>

<div class="container text-center d-print-none">
    <?= \Altum\Alerts::output_alerts() ?>

    <h1 class="index-header mb-2"><?= $data->type ? sprintf(l('qr.header_dynamic'), l('qr_codes.type.' . $data->type)) : l('qr.header') ?></h1>
    <p class="index-subheader mb-5"><?= $data->type ? sprintf(l('qr.subheader_dynamic'), l('qr_codes.type.' . $data->type)) : l('qr.subheader') ?></p>

    <div class="d-flex flex-wrap justify-content-center">
        <?php foreach($data->available_qr_codes as $key => $value): ?>
            <div class="mr-3 mb-3" data-toggle="tooltip" <?= $this->user->plan_settings->enabled_qr_codes->{$key} ? 'title="' . l('qr_codes.type.' . $key . '_description') . '"' : 'title="' . l('global.info_message.plan_feature_no_access') . '"' ?>>
                <a
                        href="<?= url('qr/' . $key) ?>"
                        class="btn <?= $data->type == $key ? 'btn-primary' : 'btn-light' ?> <?= $this->user->plan_settings->enabled_qr_codes->{$key} ? null : 'disabled' ?>"
                >
                    <i class="<?= $value['icon'] ?> fa-fw fa-sm mr-1"></i> <?= l('qr_codes.type.' . $key) ?>
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
                        <form id="form" action="<?= url('qr-code-create') ?>" method="post" role="form" enctype="multipart/form-data">
                            <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>" />
                            <input type="hidden" name="api_key" value="<?= $this->user->api_key ?? null ?>" />
                            <input type="hidden" name="type" value="<?= $data->type ?>" />
                            <input type="hidden" name="reload" value="" data-reload-qr-code />
                            <input type="hidden" name="is_readable" value="" />

                            <?php if(is_logged_in()): ?>
                                <input type="hidden" name="qr_code" value="" />
                                <input type="hidden" name="embedded_data" value="<?= $data->values['embedded_data'] ?? null ?>" />
                                <input type="hidden" name="name" value="<?= $this->user->name ?>" />
                            <?php endif ?>

                            <div class="notification-container"></div>

                            <?= $this->views['qr_form'] ?>

                            <button class="btn btn-block btn-gray-200 my-4" type="button" data-toggle="collapse" data-target="#style_container" aria-expanded="false" aria-controls="style_container">
                                <i class="fas fa-fw fa-qrcode fa-sm mr-1"></i> <?= l('qr_codes.input.style') ?>
                            </button>

                            <div class="collapse" id="style_container" data-parent="#form">
                                <div class="form-group">
                                    <label for="style"><i class="fas fa-fw fa-qrcode fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.style') ?></label>
                                    <div class="row btn-group-toggle p-2" data-toggle="buttons">
                                        <?php foreach($data->styles as $key => $style): ?>
                                            <div class="col-3 p-2">
                                                <label class="btn btn-light btn-block mb-0 text-truncate <?= ($data->values['settings']['style'] ?? 'square') == $key ? 'active"' : null?>" data-toggle="tooltip" title="<?= l('qr_codes.input.style.' . $key) ?>" data-tooltip-hide-on-click>
                                                    <input type="radio" name="style" value="<?= $key ?>" class="custom-control-input" <?= ($data->values['settings']['style'] ?? 'square') == $key ? 'checked="checked"' : null?> required="required" data-reload-qr-code />
                                                    <div class="py-2">
                                                        <?= sprintf($style['svg'], 'var(--primary-800)') ?>
                                                    </div>
                                                </label>
                                            </div>
                                        <?php endforeach ?>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="inner_eye_style"><i class="fas fa-fw fa-circle fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.inner_eye_style') ?></label>
                                    <div class="row btn-group-toggle p-2" data-toggle="buttons">
                                        <?php foreach($data->inner_eyes as $key => $style): ?>
                                            <div class="col-3 p-2">
                                                <label class="btn btn-light btn-block mb-0 text-truncate <?= ($data->values['settings']['inner_eye_style'] ?? null) == $key ? 'active"' : null?>" data-toggle="tooltip" title="<?= l('qr_codes.input.style.' . $key) ?>" data-tooltip-hide-on-click>
                                                    <input type="radio" name="inner_eye_style" value="<?= $key ?>" class="custom-control-input" <?= ($data->values['settings']['inner_eye_style'] ?? null) == $key ? 'checked="checked"' : null?> required="required" data-reload-qr-code />
                                                    <div class="py-2">
                                                        <?= sprintf($style['svg'], 'var(--primary-800)') ?>
                                                    </div>
                                                </label>
                                            </div>
                                        <?php endforeach ?>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="outer_eye_style"><i class="fas fa-fw fa-dot-circle fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.outer_eye_style') ?></label>
                                    <div class="row btn-group-toggle p-2" data-toggle="buttons">
                                        <?php foreach($data->outer_eyes as $key => $style): ?>
                                            <div class="col-3 p-2">
                                                <label class="btn btn-light btn-block mb-0 text-truncate <?= ($data->values['settings']['outer_eye_style'] ?? null) == $key ? 'active"' : null?>" data-toggle="tooltip" title="<?= l('qr_codes.input.style.' . $key) ?>" data-tooltip-hide-on-click>
                                                    <input type="radio" name="outer_eye_style" value="<?= $key ?>" class="custom-control-input" <?= ($data->values['settings']['outer_eye_style'] ?? null) == $key ? 'checked="checked"' : null?> required="required" data-reload-qr-code />
                                                    <div class="py-2">
                                                        <?= sprintf($style['svg'], 'var(--primary-800)') ?>
                                                    </div>
                                                </label>
                                            </div>
                                        <?php endforeach ?>
                                    </div>
                                </div>
                            </div>

                            <button class="btn btn-block btn-gray-200 my-4" type="button" data-toggle="collapse" data-target="#colors_container" aria-expanded="false" aria-controls="colors_container">
                                <i class="fas fa-fw fa-palette fa-sm mr-1"></i> <?= l('qr_codes.input.colors') ?>
                            </button>

                            <div class="collapse" id="colors_container" data-parent="#form">
                                <div class="form-group">
                                    <label for="foreground_type"><i class="fas fa-fw fa-paint-roller fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.foreground_type') ?></label>
                                    <div class="row btn-group-toggle" data-toggle="buttons">
                                        <div class="col-6">
                                            <label class="btn btn-light btn-block active">
                                                <input type="radio" name="foreground_type" value="color" class="custom-control-input" checked="checked" required="required" data-reload-qr-code />
                                                <i class="fas fa-fw fa-eyedropper fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.foreground_type_color') ?>
                                            </label>
                                        </div>
                                        <div class="col-6">
                                            <label class="btn btn-light btn-block">
                                                <input type="radio" name="foreground_type" value="gradient" class="custom-control-input" required="required" data-reload-qr-code />
                                                <i class="fas fa-fw fa-fill-drip fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.foreground_type_gradient') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group" data-foreground-type="color">
                                    <label for="foreground_color"><i class="fas fa-fw fa-paint-brush fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.foreground_color') ?></label>
                                    <input type="hidden" id="foreground_color" name="foreground_color" class="form-control" value="<?= '#000000' ?>" data-reload-qr-code data-color-picker data-color-picker-has-opacity="true" />
                                </div>

                                <div class="form-group" data-foreground-type="gradient">
                                    <label for="foreground_gradient_style"><i class="fas fa-fw fa-brush fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.foreground_gradient_style') ?></label>
                                    <select id="foreground_gradient_style" name="foreground_gradient_style" class="custom-select" data-reload-qr-code>
                                        <?php foreach(['vertical', 'horizontal', 'diagonal', 'inverse_diagonal', 'radial'] as $style): ?>
                                            <option value="<?= $style ?>"><?= l('qr_codes.input.foreground_gradient_style_' . $style) ?></option>
                                        <?php endforeach ?>
                                    </select>
                                </div>

                                <div class="form-group" data-foreground-type="gradient">
                                    <label for="foreground_gradient_one"><i class="fas fa-fw fa-layer-group fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.foreground_gradient_one') ?></label>
                                    <input type="hidden" id="foreground_gradient_one" name="foreground_gradient_one" class="form-control" value="<?= '#000000' ?>" data-reload-qr-code data-color-picker />
                                </div>

                                <div class="form-group" data-foreground-type="gradient">
                                    <label for="foreground_gradient_two"><i class="fas fa-fw fa-layer-group fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.foreground_gradient_two') ?></label>
                                    <input type="hidden" id="foreground_gradient_two" name="foreground_gradient_two" class="form-control" value="<?= '#000000' ?>" data-reload-qr-code data-color-picker />
                                </div>

                                <div class="form-group">
                                    <label for="background_color"><i class="fas fa-fw fa-fill fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.background_color') ?></label>
                                    <input type="hidden" id="background_color" name="background_color" class="form-control" value="<?= '#ffffff' ?>" data-reload-qr-code data-color-picker />
                                </div>

                                <div class="form-group" data-range-counter data-range-counter-suffix="%">
                                    <label for="background_color_transparency"><i class="fas fa-fw fa-lightbulb fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.background_color_transparency') ?></label>
                                    <input id="background_color_transparency" type="range" min="0" max="100" step="5" name="background_color_transparency" value="<?= 0 ?>" class="form-control-range" data-reload-qr-code />
                                </div>

                                <div class="form-group custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="custom_eyes_color" name="custom_eyes_color" data-reload-qr-code />
                                    <label class="custom-control-label" for="custom_eyes_color">
                                        <i class="fas fa-fw fa-eye fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.custom_eyes_color') ?>
                                    </label>
                                </div>

                                <div class="form-group" data-custom-eyes-color="on">
                                    <label for="eyes_inner_color"><?= l('qr_codes.input.eyes_inner_color') ?></label>
                                    <input type="hidden" id="eyes_inner_color" name="eyes_inner_color" class="form-control" value="<?= '#000000' ?>" data-reload-qr-code data-color-picker data-color-picker-has-opacity="true" />
                                </div>

                                <div class="form-group" data-custom-eyes-color="on">
                                    <label for="eyes_outer_color"><?= l('qr_codes.input.eyes_outer_color') ?></label>
                                    <input type="hidden" id="eyes_outer_color" name="eyes_outer_color" class="form-control" value="<?= '#000000' ?>" data-reload-qr-code data-color-picker data-color-picker-has-opacity="true" />
                                </div>
                            </div>

                            <button class="btn btn-block btn-gray-200 my-4" type="button" data-toggle="collapse" data-target="#frame_container" aria-expanded="false" aria-controls="frame_container">
                                <i class="fas fa-fw fa-crop-alt fa-sm mr-1"></i> <?= l('qr_codes.input.frame') ?>
                            </button>

                            <div class="collapse" id="frame_container" data-parent="#form">

                                <div class="form-group">
                                    <label for="frame"><i class="fas fa-fw fa-qrcode fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.frame') ?></label>
                                    <div class="row btn-group-toggle" data-toggle="buttons">
                                        <div class="col-6 col-lg-4 mb-3">
                                            <label class="btn btn-light btn-block d-flex align-items-center justify-content-center active" data-toggle="tooltip" data-tooltip-hide-on-click title="<?= l('global.none') ?>" style="height: 125px;">
                                                <input type="radio" name="frame" value="" class="custom-control-input" checked="checked" required="required" data-reload-qr-code />
                                                <i class="fas fa-fw fa-3x fa-times"></i>
                                            </label>
                                        </div>

                                        <?php foreach($data->frames as $key => $frame): ?>
                                            <div class="col-6 col-lg-4 mb-3">
                                                <label class="btn btn-light btn-block d-flex align-items-center justify-content-center" style="height: 125px;">
                                                    <input type="radio" name="frame" value="<?= $key ?>" class="custom-control-input" required="required" data-reload-qr-code />
                                                    <?= sprintf($frame['svg'], 75, 75 * $frame['frame_height_scale'], 75 / $frame['frame_scale'], 'var(--gray-900)', 75 * $frame['frame_translate_x'], 75 * $frame['frame_translate_y']) ?>
                                                </label>
                                            </div>
                                        <?php endforeach ?>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="frame_text"><i class="fas fa-fw fa-signature fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.frame_text') ?></label>
                                    <input type="text" id="frame_text" name="frame_text" class="form-control <?= \Altum\Alerts::has_field_errors('frame_text') ? 'is-invalid' : null ?>" maxlength="64" data-reload-qr-code />
                                    <?= \Altum\Alerts::output_field_error('frame_text') ?>
                                </div>

                                <div class="form-group" data-range-counter>
                                    <label for="frame_text_size"><i class="fas fa-fw fa-text-height fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.frame_text_size') ?></label>
                                    <input id="frame_text_size" type="range" min="-5" max="5" name="frame_text_size" class="form-control-range <?= \Altum\Alerts::has_field_errors('frame_text_size') ? 'is-invalid' : null ?>" data-reload-qr-code />
                                    <?= \Altum\Alerts::output_field_error('frame_text_size') ?>
                                </div>

                                <div class="form-group">
                                    <label for="frame_text_font"><i class="fas fa-fw fa-pen-nib fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.frame_text_font') ?></label>
                                    <div class="row btn-group-toggle" data-toggle="buttons">
                                        <?php foreach($data->frames_fonts as $font_key => $font): ?>
                                            <div class="col-12 col-lg-4 p-2 h-100">
                                                <label class="btn btn-light btn-block text-truncate <?= $font_key == array_key_first($data->frames_fonts) ? 'active"' : null?>" style="font-family: <?= $font['font-family'] ?> !important;">
                                                    <input type="radio" name="frame_text_font" value="<?= $font_key ?>" class="custom-control-input" <?= $font_key == array_key_first($data->frames_fonts) ? 'checked="checked"' : null?> required="required" data-reload-qr-code />
                                                    <?= $font['name'] ?>
                                                </label>
                                            </div>
                                        <?php endforeach ?>
                                    </div>
                                </div>

                                <div class="form-group custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="frame_custom_colors" name="frame_custom_colors" data-reload-qr-code />
                                    <label class="custom-control-label" for="frame_custom_colors">
                                        <i class="fas fa-fw fa-tint fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.frame_custom_colors') ?>
                                    </label>
                                </div>

                                <div class="form-group" data-frame-custom-colors="on">
                                    <label for="frame_color"><?= l('qr_codes.input.frame_color') ?></label>
                                    <input type="hidden" id="frame_color" name="frame_color" class="form-control <?= \Altum\Alerts::has_field_errors('frame_color') ? 'is-invalid' : null ?>" value="#000000" data-reload-qr-code data-color-picker data-color-picker-has-opacity="true" />
                                    <?= \Altum\Alerts::output_field_error('frame_color') ?>
                                </div>

                                <div class="form-group" data-frame-custom-colors="on">
                                    <label for="frame_text_color"><?= l('qr_codes.input.frame_text_color') ?></label>
                                    <input type="hidden" id="frame_text_color" name="frame_text_color" class="form-control <?= \Altum\Alerts::has_field_errors('frame_text_color') ? 'is-invalid' : null ?>" value="#ffffff" data-reload-qr-code data-color-picker data-color-picker-has-opacity="true" />
                                    <?= \Altum\Alerts::output_field_error('frame_text_color') ?>
                                </div>
                            </div>

                            <button class="btn btn-block btn-gray-200 my-4" type="button" data-toggle="collapse" data-target="#branding_container" aria-expanded="false" aria-controls="branding_container">
                                <i class="fas fa-fw fa-copyright fa-sm mr-1"></i> <?= l('qr_codes.input.branding') ?>
                            </button>

                            <div class="collapse" id="branding_container" data-parent="#form">
                                <div class="form-group" data-file-image-input-wrapper data-file-input-wrapper-size-limit="<?= settings()->codes->logo_size_limit ?>" data-file-input-wrapper-size-limit-error="<?= sprintf(l('global.error_message.file_size_limit'), settings()->codes->logo_size_limit) ?>">
                                    <label for="qr_code_logo"><i class="fas fa-fw fa-sm fa-eye text-muted mr-1"></i> <?= l('qr_codes.input.qr_code_logo') ?></label>
                                    <?= include_view(THEME_PATH . 'views/partials/file_image_input.php', ['uploads_file_key' => 'qr_codes/logo', 'file_key' => 'qr_code_logo', 'already_existing_image' => null, 'input_data' => 'data-reload-qr-code']) ?>
                                    <?= \Altum\Alerts::output_field_error('qr_code_logo') ?>
                                    <small class="form-text text-muted"><?= sprintf(l('global.accessibility.whitelisted_file_extensions'), \Altum\Uploads::get_whitelisted_file_extensions_accept('qr_codes/logo')) . ' ' . sprintf(l('global.accessibility.file_size_limit'), settings()->codes->logo_size_limit) ?></small>
                                </div>

                                <div class="form-group" data-range-counter>
                                    <label for="qr_code_logo_size"><i class="fas fa-fw fa-expand-alt fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.qr_code_logo_size') ?></label>
                                    <input id="qr_code_logo_size" type="range" min="5" max="40" name="qr_code_logo_size" value="<?= $data->values['settings']['qr_code_logo_size'] ?? 25 ?>" class="form-control-range <?= \Altum\Alerts::has_field_errors('qr_code_logo_size') ? 'is-invalid' : null ?>" data-reload-qr-code />
                                    <?= \Altum\Alerts::output_field_error('qr_code_logo_size') ?>
                                </div>

                                <div class="form-group" data-file-image-input-wrapper data-file-input-wrapper-size-limit="<?= settings()->codes->background_size_limit ?>" data-file-input-wrapper-size-limit-error="<?= sprintf(l('global.error_message.file_size_limit'), settings()->codes->background_size_limit) ?>">
                                    <label for="qr_code_background"><i class="fas fa-fw fa-sm fa-image text-muted mr-1"></i> <?= l('qr_codes.input.qr_code_background') ?></label>
                                    <?= include_view(THEME_PATH . 'views/partials/file_image_input.php', ['uploads_file_key' => 'qr_code_background', 'file_key' => 'qr_code_background', 'already_existing_image' => null, 'input_data' => 'data-reload-qr-code']) ?>
                                    <?= \Altum\Alerts::output_field_error('qr_code_background') ?>
                                    <small class="form-text text-muted"><?= sprintf(l('global.accessibility.whitelisted_file_extensions'), \Altum\Uploads::get_whitelisted_file_extensions_accept('qr_code_background')) . ' ' . sprintf(l('global.accessibility.file_size_limit'), settings()->codes->background_size_limit) ?></small>
                                </div>

                                <div class="form-group" data-range-counter data-range-counter-suffix="%">
                                    <label for="qr_code_background_transparency"><i class="fas fa-fw fa-lightbulb fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.qr_code_background_transparency') ?></label>
                                    <input id="qr_code_background_transparency" type="range" min="0" max="95" step="5" name="qr_code_background_transparency" value="<?= $data->values['settings']['qr_code_background_transparency'] ?? 0 ?>" class="form-control-range <?= \Altum\Alerts::has_field_errors('qr_code_background_transparency') ? 'is-invalid' : null ?>" data-reload-qr-code />
                                    <?= \Altum\Alerts::output_field_error('qr_code_background_transparency') ?>
                                </div>

                                <div class="form-group" data-file-image-input-wrapper data-file-input-wrapper-size-limit="<?= settings()->codes->background_size_limit ?>" data-file-input-wrapper-size-limit-error="<?= sprintf(l('global.error_message.file_size_limit'), settings()->codes->background_size_limit) ?>">
                                    <label for="qr_code_foreground"><i class="fas fa-fw fa-sm fa-images text-muted mr-1"></i> <?= l('qr_codes.input.qr_code_foreground') ?></label>
                                    <?= include_view(THEME_PATH . 'views/partials/file_image_input.php', ['uploads_file_key' => 'qr_code_foreground', 'file_key' => 'qr_code_foreground', 'already_existing_image' => null, 'input_data' => 'data-reload-qr-code']) ?>
                                    <?= \Altum\Alerts::output_field_error('qr_code_foreground') ?>
                                    <small class="form-text text-muted"><?= sprintf(l('global.accessibility.whitelisted_file_extensions'), \Altum\Uploads::get_whitelisted_file_extensions_accept('qr_code_foreground')) . ' ' . sprintf(l('global.accessibility.file_size_limit'), settings()->codes->background_size_limit) ?></small>
                                </div>

                                <div class="form-group" data-range-counter data-range-counter-suffix="%">
                                    <label for="qr_code_foreground_transparency"><i class="fas fa-fw fa-lightbulb fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.qr_code_foreground_transparency') ?></label>
                                    <input id="qr_code_foreground_transparency" type="range" min="0" max="95" step="5" name="qr_code_foreground_transparency" value="<?= $data->values['settings']['qr_code_foreground_transparency'] ?? 0 ?>" class="form-control-range <?= \Altum\Alerts::has_field_errors('qr_code_foreground_transparency') ? 'is-invalid' : null ?>" data-reload-qr-code />
                                    <?= \Altum\Alerts::output_field_error('qr_code_foreground_transparency') ?>
                                </div>
                            </div>

                            <button class="btn btn-block btn-gray-200 my-4" type="button" data-toggle="collapse" data-target="#options_container" aria-expanded="false" aria-controls="options_container">
                                <i class="fas fa-fw fa-wrench fa-sm mr-1"></i> <?= l('qr_codes.input.options') ?>
                            </button>

                            <div class="collapse" id="options_container" data-parent="#form">
                                <div class="form-group">
                                    <label for="size"><i class="fas fa-fw fa-expand-arrows-alt fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.size') ?></label>
                                    <div class="input-group">
                                        <input id="size" type="number" min="50" max="2000" name="size" class="form-control" value="<?= 500 ?>" data-reload-qr-code />
                                        <div class="input-group-append">
                                            <span class="input-group-text">px</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="margin"><i class="fas fa-fw fa-expand fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.margin') ?></label>
                                    <input id="margin" type="number" min="0" max="25" name="margin" class="form-control" value="<?= 0 ?>" data-reload-qr-code />
                                </div>

                                <div class="form-group">
                                    <label for="ecc"><i class="fas fa-fw fa-check fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.ecc') ?></label>
                                    <select id="ecc" name="ecc" class="custom-select" data-reload-qr-code>
                                        <?php foreach(['L', 'M', 'Q', 'H'] as $level): ?>
                                            <option value="<?= $level ?>"><?= l('qr_codes.input.ecc_' . mb_strtolower($level)) ?></option>
                                        <?php endforeach ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="encoding"><i class="fas fa-fw fa-feather-alt fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.encoding') ?></label>
                                    <select id="encoding" name="encoding" class="custom-select" data-reload-qr-code>
                                        <?php foreach(['ISO-8859-1', 'ISO-8859-2', 'ISO-8859-3', 'ISO-8859-4', 'ISO-8859-5', 'ISO-8859-6', 'ISO-8859-7', 'ISO-8859-8', 'ISO-8859-9', 'ISO-8859-10', 'ISO-8859-11', 'ISO-8859-12', 'ISO-8859-13', 'ISO-8859-14', 'ISO-8859-15', 'ISO-8859-16', 'SHIFT-JIS', 'WINDOWS-1250', 'WINDOWS-1251', 'WINDOWS-1252', 'WINDOWS-1256', 'UTF-16BE', 'UTF-8', 'ASCII', 'GBK', 'EUC-KR'] as $encoding): ?>
                                            <option value="<?= $encoding ?>" <?= 'UTF-8' == $encoding ? 'selected="selected"' : null ?>><?= $encoding ?></option>
                                        <?php endforeach ?>
                                    </select>
                                </div>
                            </div>

                            <?php if(is_logged_in()): ?>
                                <button type="submit" name="submit" class="btn btn-block btn-primary mt-4"><?= l('global.create') ?></button>
                            <?php else: ?>
                                <?php if(settings()->users->register_is_enabled): ?>
                                    <a href="<?= url('register') ?>" class="btn btn-block btn-outline-primary mt-4"><i class="fas fa-fw fa-xs fa-plus mr-1"></i> <?= l('qr.register') ?></a>
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
                                <img id="qr_code" src="<?= settings()->codes->qr_codes_default_image ? \Altum\Uploads::get_full_url('qr_code_default_image') . settings()->codes->qr_codes_default_image : ASSETS_FULL_URL . 'images/qr_code.svg' ?>" class="img-fluid qr-code" loading="lazy" />
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4 d-print-none">
                        <div class="col-12 col-lg-6 mb-3 mb-lg-0">
                            <button type="button" class="btn btn-block btn-outline-secondary d-print-none <?= $this->user->plan_settings->export->pdf ? null : 'disabled' ?>" <?= $this->user->plan_settings->export->pdf ? 'onclick="window.print();return false;"' : get_plan_feature_disabled_info() ?>>
                                <i class="fas fa-fw fa-sm fa-file-pdf mr-1"></i> <?= l('qr_codes.print') ?>
                            </button>
                        </div>

                        <div class="col-12 col-lg-6 mb-3 mb-lg-0">
                            <button type="button" class="btn btn-block btn-primary d-print-none dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-fw fa-download fa-sm mr-1"></i> <?= l('global.download') ?>
                            </button>

                            <div class="dropdown-menu">
                                <a href="<?= settings()->codes->qr_codes_default_image ? \Altum\Uploads::get_full_url('qr_code_default_image') . settings()->codes->qr_codes_default_image : ASSETS_FULL_URL . 'images/qr_code.svg' ?>" id="download_svg" class="dropdown-item" download="<?= get_slug(settings()->main->title) . '.svg' ?>"><?= sprintf(l('global.download_as'), 'SVG') ?></a>
                                <button type="button" class="dropdown-item" onclick="convert_svg_qr_code_to_others(null, 'png', '<?= get_slug(settings()->main->title) . '.png' ?>');"><?= sprintf(l('global.download_as'), 'PNG') ?></button>
                                <button type="button" class="dropdown-item" onclick="convert_svg_qr_code_to_others(null, 'jpg', '<?= get_slug(settings()->main->title) . '.jpg' ?>');"><?= sprintf(l('global.download_as'), 'JPG') ?></button>
                                <button type="button" class="dropdown-item" onclick="convert_svg_qr_code_to_others(null, 'webp', '<?= get_slug(settings()->main->title) . '.webp' ?>');"><?= sprintf(l('global.download_as'), 'WEBP') ?></button>
                            </div>
                        </div>
                    </div>

                    <button id="embedded_data_container_button" class="btn btn-block btn-light my-4 d-none d-print-none" type="button" data-toggle="collapse" data-target="#embedded_data_container" aria-expanded="false" aria-controls="embedded_data_container">
                        <i class="fas fa-fw fa-bars fa-sm mr-1"></i> <?= l('qr_codes.embedded_data') ?>
                    </button>

                    <div class="collapse" id="embedded_data_container">
                        <div class="card my-4">
                            <div class="card-body" id="embedded_data_display"></div>
                        </div>
                    </div>

                    <div class="mb-4 text-center d-print-none">
                        <small>
                            <i class="fas fa-fw fa-info-circle fa-sm text-muted mr-1"></i> <span class="text-muted"><?= l('qr_codes.info') ?></span>
                        </small>

                        <div id="is_readable" class="d-none text-success small mt-2">
                            <i class="fas fa-fw fa-check-circle fa-sm mr-1"></i> <?= l('qr_codes.is_readable') ?>
                        </div>

                        <div id="is_not_readable" class="d-none text-warning small mt-2">
                            <i class="fas fa-fw fa-exclamation-circle fa-sm mr-1"></i> <?= l('qr_codes.is_not_readable') ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php if(l('qr_codes.type.' . $data->type . '_extra_content', null, true)): ?>
        <div class="mt-5">
            <div class="card">
                <div class="card-body">
                    <?= l('qr_codes.type.' . $data->type . '_extra_content') ?>
                </div>
            </div>
        </div>
        <?php endif ?>
    </div>

    <?php require THEME_PATH . 'views/qr-codes/js_qr_codes.php' ?>

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
                    "name": "<?= l('qr.title') ?>",
                    "item": "<?= url('qr') ?>"
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

    <?php if(l('qr.extra_content')): ?>
        <div class="container mt-4">
            <div class="card">
                <div class="card-body">
                    <?= l('qr.extra_content') ?>
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
                    "name": "<?= l('qr.title') ?>",
                    "item": "<?= url('qr') ?>"
                }
            ]
        }
    </script>
    <?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>

<?php endif ?>

