<?php defined('ALTUMCODE') || die() ?>

<div class="container">
    <?= \Altum\Alerts::output_alerts() ?>

    <div class="d-print-none">
        <?php if(settings()->main->breadcrumbs_is_enabled): ?>
            <nav aria-label="breadcrumb">
                <ol class="custom-breadcrumbs small">
                    <li>
                        <a href="<?= url('qr-codes') ?>"><?= l('ai_qr_codes.breadcrumb') ?></a><i class="fas fa-fw fa-angle-right"></i>
                    </li>
                    <li class="active" aria-current="page"><?= l('ai_qr_code_create.breadcrumb') ?></li>
                </ol>
            </nav>
        <?php endif ?>

        <div class="d-flex align-items-center mb-4">
            <h1 class="h4 text-truncate mb-0 mr-2"><i class="fas fa-fw fa-xs fa-robot mr-1"></i> <?= l('ai_qr_code_create.header') ?></h1>
        </div>
    </div>

    <form id="form" action="" method="post" role="form" enctype="multipart/form-data">
        <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>" />
        <input type="hidden" name="api_key" value="<?= $this->user->api_key ?>" />
        <input type="hidden" name="ai_qr_code" value="<?= $data->values['ai_qr_code'] ?? null ?>" />
        <input type="hidden" name="embedded_data" value="<?= $data->values['embedded_data'] ?? null ?>" />

        <div class="row">
            <div class="col-12 col-xl-6 d-print-none mb-5 mb-xl-0">
                <div class="card">
                    <div class="card-body">
                        <div class="notification-container"></div>

                        <div class="form-group">
                            <label for="name"><i class="fas fa-fw fa-signature fa-sm text-muted mr-1"></i> <?= l('global.name') ?></label>
                            <input type="text" id="name" name="name" class="form-control <?= \Altum\Alerts::has_field_errors('name') ? 'is-invalid' : null ?>" value="<?= $data->values['name'] ?? null ?>" maxlength="64" required="required" />
                            <?= \Altum\Alerts::output_field_error('name') ?>
                        </div>

                        <div class="form-group">
                            <label for="prompt"><i class="fas fa-fw fa-robot fa-sm text-muted mr-1"></i> <?= l('ai_qr_codes.prompt') ?></label>
                            <input type="text" id="prompt" name="prompt" class="form-control <?= \Altum\Alerts::has_field_errors('prompt') ? 'is-invalid' : null ?>" value="<?= $data->values['prompt'] ?? null ?>" maxlength="512" required="required" placeholder="<?= l('ai_qr_codes.prompt_placeholder') ?>" />
                            <?= \Altum\Alerts::output_field_error('prompt') ?>
                        </div>

                        <div class="form-group" data-url>
                            <label for="content"><i class="fas fa-fw fa-paragraph fa-sm text-muted mr-1"></i> <?= l('ai_qr_codes.content') ?></label>
                            <input type="text" id="content" name="content" class="form-control <?= \Altum\Alerts::has_field_errors('content') ? 'is-invalid' : null ?>" value="<?= $data->values['content'] ?? null ?>" maxlength="512" required="required" placeholder="<?= l('global.url_placeholder') ?>" />
                            <?= \Altum\Alerts::output_field_error('content') ?>
                        </div>

                        <div class="form-group" data-link-id>
                            <div class="d-flex flex-wrap flex-row justify-content-between">
                                <label for="link_id"><i class="fas fa-fw fa-link fa-sm text-muted mr-1"></i> <?= l('qr_codes.input.link_id') ?></label>
                                <a href="<?= url('link-create') ?>" target="_blank" class="small mb-2"><i class="fas fa-fw fa-sm fa-plus mr-1"></i> <?= l('global.create') ?></a>
                            </div>
                            <select id="link_id" name="link_id" class="custom-select" required="required">
                                <?php foreach($data->links as $row): ?>
                                    <option value="<?= $row->link_id ?>" <?= ($data->values['link_id'] ?? null) == $row->link_id ? 'selected="selected"' : null?> data-url="<?= $row->full_url ?>">
                                        <?= remove_url_protocol_from_url($row->full_url) . ' -> ' . remove_url_protocol_from_url($row->location_url) ?>
                                    </option>
                                <?php endforeach ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input id="url_dynamic" name="url_dynamic" type="checkbox" class="custom-control-input" <?= ($data->values['url_dynamic'] ?? null) ? 'checked="checked"' : null ?> />
                                <label class="custom-control-label" for="url_dynamic"><?= l('qr_codes.input.url_dynamic') ?></label>
                                <small class="form-text text-muted"><?= l('qr_codes.input.url_dynamic_help') ?></small>
                            </div>
                        </div>

                        <?php if(settings()->links->projects_is_enabled): ?>
                        <button class="btn btn-block btn-gray-200 my-4" type="button" data-toggle="collapse" data-target="#options_container" aria-expanded="false" aria-controls="options_container">
                            <i class="fas fa-fw fa-wrench fa-sm mr-1"></i> <?= l('qr_codes.input.options') ?>
                        </button>

                        <div class="collapse" id="options_container" data-parent="#form">
                            <div class="form-group">
                                <div class="d-flex flex-wrap flex-row justify-content-between">
                                    <label for="project_id"><i class="fas fa-fw fa-sm fa-project-diagram text-muted mr-1"></i> <?= l('projects.project_id') ?></label>
                                    <a href="<?= url('project-create') ?>" target="_blank" class="small mb-2"><i class="fas fa-fw fa-sm fa-plus mr-1"></i> <?= l('projects.create') ?></a>
                                </div>
                                <select id="project_id" name="project_id" class="custom-select">
                                    <option value=" "><?= l('global.none') ?></option>
                                    <?php foreach($data->projects as $row): ?>
                                        <option value="<?= $row->project_id ?>" <?= ($data->values['project_id'] ?? null) == $row->project_id ? 'selected="selected"' : null?>><?= $row->name ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                        <?php endif ?>

                        <button id="generate" type="submit" name="generate" class="btn btn-block btn-outline-primary mt-4"><?= l('ai_qr_code_create.generate') ?></button>
                        <button id="save" type="submit" name="save" class="btn btn-block btn-primary mt-3 disabled"><?= l('ai_qr_code_create.save') ?></button>
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-6">
                <div class="sticky">
                    <div class="mb-4">
                        <div class="card">
                            <div class="card-body">
                                <img id="ai_qr_code" src="<?= (settings()->codes->ai_qr_codes_default_image ? \Altum\Uploads::get_full_url('ai_qr_code_default_image') . settings()->codes->ai_qr_codes_default_image : ASSETS_FULL_URL . 'images/ai_qr_code.png') ?>" class="img-fluid qr-code rounded" loading="lazy" />
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
                            <a href="#" id="download" class="btn btn-block btn-primary d-print-none disabled" download="<?= get_slug($data->values['name'] ?? settings()->main->title) . '.png' ?>">
                                <i class="fas fa-fw fa-download fa-sm mr-1"></i> <?= sprintf(l('global.download_as'), 'PNG') ?>
                            </a>
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
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<?php require THEME_PATH . 'views/ai-qr-codes/js_qr_codes.php' ?>
