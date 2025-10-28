<?php defined('ALTUMCODE') || die() ?>


<div class="container">
    <?= \Altum\Alerts::output_alerts() ?>

    <?php if(settings()->main->breadcrumbs_is_enabled): ?>
        <nav aria-label="breadcrumb">
            <ol class="custom-breadcrumbs small">
                <li>
                    <a href="<?= url('links') ?>"><?= l('links.breadcrumb') ?></a><i class="fas fa-fw fa-angle-right"></i>
                </li>
                <li class="active" aria-current="page"><?= l('link_create.breadcrumb') ?></li>
            </ol>
        </nav>
    <?php endif ?>

    <h1 class="h4 text-truncate"><i class="fas fa-fw fa-xs fa-link mr-1"></i> <?= l('link_create.header') ?></h1>
    <p></p>

    <div class="card">
        <div class="card-body">

            <form action="" method="post" role="form">
                <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>" />

                <div id="location_url_wrapper" class="form-group <?= $data->values['is_bulk'] ? 'd-none' : null ?>">
                    <label for="location_url"><i class="fas fa-fw fa-sm fa-link text-muted mr-1"></i> <?= l('links.input.location_url') ?></label>
                    <input type="url" id="location_url" name="location_url" class="form-control <?= \Altum\Alerts::has_field_errors('location_url') ? 'is-invalid' : null ?>" value="<?= $data->values['location_url'] ?>" maxlength="2048" placeholder="<?= l('global.url_placeholder') ?>" required="required" />
                    <?= \Altum\Alerts::output_field_error('location_url') ?>
                </div>

                <div id="location_urls_wrapper" class="form-group <?= !$data->values['is_bulk'] ? 'd-none' : null ?>">
                    <label for="location_urls"><i class="fas fa-fw fa-sm fa-link text-muted mr-1"></i> <?= l('links.input.location_urls') ?></label>
                    <textarea type="url" id="location_urls" name="location_urls" class="form-control <?= \Altum\Alerts::has_field_errors('location_urls') ? 'is-invalid' : null ?>" placeholder="<?= l('global.url_placeholder') ?>" required="required"><?= $data->values['location_urls'] ?></textarea>
                    <?= \Altum\Alerts::output_field_error('location_urls') ?>
                </div>

                <?php if(count($data->domains) && (settings()->links->domains_is_enabled || settings()->links->additional_domains_is_enabled)): ?>
                    <div class="form-group">
                        <label for="domain_id"><i class="fas fa-fw fa-sm fa-globe text-muted mr-1"></i> <?= l('links.input.domain_id') ?></label>
                        <select id="domain_id" name="domain_id" class="custom-select">
                            <?php if(settings()->links->main_domain_is_enabled || \Altum\Authentication::is_admin()): ?>
                                <option value=" "><?= remove_url_protocol_from_url(SITE_URL) ?></option>
                            <?php endif ?>

                            <?php foreach($data->domains as $row): ?>
                                <option value="<?= $row->domain_id ?>" <?= $data->values['domain_id'] && $data->values['domain_id'] == $row->domain_id ? 'selected="selected"' : null ?>><?= remove_url_protocol_from_url($row->url) ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>

                    <div <?= $this->user->plan_settings->custom_url_is_enabled ? null : get_plan_feature_disabled_info() ?>>
                        <div class="<?= $this->user->plan_settings->custom_url_is_enabled ? null : 'container-disabled' ?>">
                            <div class="form-group">
                                <label for="url"><i class="fas fa-fw fa-sm fa-bolt text-muted mr-1"></i> <?= l('links.input.url') ?></label>
                                <input type="text" id="url" name="url" class="form-control <?= \Altum\Alerts::has_field_errors('url') ? 'is-invalid' : null ?>" value="<?= $data->values['url'] ?>" maxlength="<?= ($this->user->plan_settings->url_maximum_characters ?? 64) ?>" onchange="update_this_value(this, get_slug)" onkeyup="update_this_value(this, get_slug)" placeholder="<?= l('global.url_slug_placeholder') ?>" />
                                <?= \Altum\Alerts::output_field_error('url') ?>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div <?= $this->user->plan_settings->custom_url_is_enabled ? null : get_plan_feature_disabled_info() ?>>
                        <div class="<?= $this->user->plan_settings->custom_url_is_enabled ? null : 'container-disabled' ?>">
                            <label for="url"><i class="fas fa-fw fa-sm fa-bolt text-muted mr-1"></i> <?= l('links.input.url') ?></label>
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><?= remove_url_protocol_from_url(SITE_URL) ?></span>
                                    </div>
                                    <input type="text" id="url" name="url" class="form-control <?= \Altum\Alerts::has_field_errors('url') ? 'is-invalid' : null ?>" value="<?= $data->values['url'] ?>" maxlength="<?= ($this->user->plan_settings->url_maximum_characters ?? 64) ?>" onchange="update_this_value(this, get_slug)" onkeyup="update_this_value(this, get_slug)" placeholder="<?= l('global.url_slug_placeholder') ?>" />
                                    <?= \Altum\Alerts::output_field_error('url') ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif ?>

                <div class="form-group">
                    <div <?= $this->user->plan_settings->links_bulk_limit ? null : get_plan_feature_disabled_info() ?>>
                        <div class="<?= $this->user->plan_settings->links_bulk_limit ? null : 'container-disabled' ?>">
                            <div class="custom-control custom-switch">
                                <input id="is_bulk" name="is_bulk" type="checkbox" class="custom-control-input" <?= $data->values['is_bulk'] ? 'checked="checked"' : null ?> />
                                <label class="custom-control-label" for="is_bulk"><?= l('links.input.is_bulk') ?></label>
                                <small class="form-text text-muted"><?= sprintf(l('links.input.is_bulk_help'), $this->user->plan_settings->links_bulk_limit) ?></small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group custom-control custom-switch">
                    <input id="is_enabled" name="is_enabled" type="checkbox" class="custom-control-input" <?= $data->values['is_enabled'] ? 'checked="checked"' : null?>>
                    <label class="custom-control-label" for="is_enabled"><?= l('links.input.is_enabled') ?></label>
                </div>

                <button class="btn btn-block btn-gray-200 my-4" type="button" data-toggle="collapse" data-target="#app_linking_container" aria-expanded="false" aria-controls="app_linking_container">
                    <i class="fas fa-fw fa-mobile-button fa-sm mr-1"></i> <?= l('links.input.app_linking') ?>
                </button>

                <div class="collapse" id="app_linking_container">
                    <div <?= $this->user->plan_settings->app_linking_is_enabled ? null : get_plan_feature_disabled_info() ?>>
                        <div class="<?= $this->user->plan_settings->app_linking_is_enabled ? null : 'container-disabled' ?>">
                            <div class="form-group custom-control custom-switch">
                                <input
                                        id="app_linking_is_enabled"
                                        name="app_linking_is_enabled"
                                        type="checkbox"
                                        class="custom-control-input"
                                    <?= $data->values['app_linking_is_enabled'] ? 'checked="checked"' : null ?>
                                    <?= $this->user->plan_settings->app_linking_is_enabled ? null : 'disabled="disabled"' ?>
                                >
                                <label class="custom-control-label" for="app_linking_is_enabled"><i class="fas fa-fw fa-mobile-screen-button fa-sm text-muted mr-1"></i> <?= l('links.input.app_linking_is_enabled') ?></label>
                                <small class="form-text text-muted"><?= l('links.input.app_linking_is_enabled_help') ?></small>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="h6"><?= l('links.input.app_linking_supported_os') ?></div>
                        <div class="row">
                            <div class="col-12 col-lg-6 mb-2 mb-lg-0">
                                <small class="badge badge-light mr-1">
                                    <i class="fab fa-apple fa-fw fa-sm"></i>
                                </small>

                                Apple
                            </div>

                            <div class="col-12 col-lg-6 mb-2 mb-lg-0">
                                <small class="badge badge-light mr-1">
                                    <i class="fab fa-android fa-fw fa-sm"></i>
                                </small>

                                Android
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="h6"><?= l('links.input.app_linking_supported_apps') ?></div>
                        <small class="form-text text-muted mb-3"><?= l('links.input.app_linking_supported_apps_help') ?></small>

                        <div id="app_linking_supported_apps" class="row">
                            <?php
                            $supported_apps = require APP_PATH . 'includes/app_linking.php';
                            ?>
                            <?php foreach($supported_apps as $app_key => $app): ?>
                                <?php
                                $tooltip_title = '<div class=\'p-3 text-left\'><p class=\'my-1\'>' . implode('</p> <p class=\'my-1\'>', array_map(function($key) {
                                        return $key;
                                    }, $app['display_formats'])) . '</p></div>';
                                ?>

                                <div id="<?= $app_key ?>" class="col-12 col-lg-6 mb-2">
                                    <small class="badge badge-light mr-1" data-toggle="tooltip" data-html="true" title="<?= $tooltip_title ?>">
                                        <i class="<?= $app['icon'] ?> fa-fw fa-sm" style="color: <?= $app['color'] ?>"></i>
                                    </small>

                                    <?= $app['name'] ?>

                                    <small class="badge badge-success ml-1 <?= ($data->values['app_linking']->app ?? null ) == $app_key ? null : 'd-none' ?>" data-app-linking-matched="<?= $app_key ?>">
                                        <i class="fas fa-check fa-fw fa-sm"></i> <?= l('links.input.app_linking_supported_apps.matched') ?>
                                    </small>
                                </div>
                            <?php endforeach ?>
                        </div>
                    </div>

                    <div id="app_linking_supported_apps_no_match" class="alert alert-info <?= $data->values['app_linking']->app ? 'd-none' : null ?>"><?= l('links.input.app_linking_supported_apps.no_match') ?></div>
                </div>

                <?php if(settings()->links->pixels_is_enabled): ?>
                    <button class="btn btn-block btn-gray-200 my-4" type="button" data-toggle="collapse" data-target="#pixels_container" aria-expanded="false" aria-controls="pixels_container">
                        <i class="fas fa-fw fa-adjust fa-sm mr-1"></i> <?= l('links.input.pixels') ?>
                    </button>

                    <div class="collapse" id="pixels_container">
                        <div class="form-group">
                            <div class="d-flex flex-wrap flex-row justify-content-between">
                                <label><i class="fas fa-fw fa-sm fa-adjust text-muted mr-1"></i> <?= l('links.input.pixels_ids') ?></label>
                                <a href="<?= url('pixel-create') ?>" target="_blank" class="small mb-2"><i class="fas fa-fw fa-sm fa-plus mr-1"></i> <?= l('pixels.create') ?></a>
                            </div>
                            <div class="row">
                                <?php $available_pixels = require APP_PATH . 'includes/l/pixels.php'; ?>
                                <?php foreach($data->pixels as $pixel): ?>
                                    <div class="col-12 col-lg-6">
                                        <div class="custom-control custom-checkbox my-2">
                                            <input id="pixel_id_<?= $pixel->pixel_id ?>" name="pixels_ids[]" value="<?= $pixel->pixel_id ?>" type="checkbox" class="custom-control-input" <?= in_array($pixel->pixel_id, $data->values['pixels_ids'] ?? []) ? 'checked="checked"' : null ?>>
                                            <label class="custom-control-label d-flex align-items-center" for="pixel_id_<?= $pixel->pixel_id ?>">
                                                <span class="text-truncate" title="<?= $pixel->name ?>"><?= $pixel->name ?></span>
                                                <small class="badge badge-light ml-1" data-toggle="tooltip" title="<?= $available_pixels[$pixel->type]['name'] ?>">
                                                    <i class="<?= $available_pixels[$pixel->type]['icon'] ?> fa-fw fa-sm" style="color: <?= $available_pixels[$pixel->type]['color'] ?>"></i>
                                                </small>
                                            </label>
                                        </div>
                                    </div>
                                <?php endforeach ?>
                            </div>
                        </div>
                    </div>
                <?php endif ?>

                <button class="btn btn-block btn-gray-200 my-4 <?= \Altum\Alerts::has_field_errors(['expiration_url']) ? 'border-danger' : null ?>" type="button" data-toggle="collapse" data-target="#temporary_url_container" aria-expanded="false" aria-controls="temporary_url_container">
                    <i class="fas fa-fw fa-clock fa-sm mr-1"></i> <?= l('links.input.temporary_url') ?>
                </button>

                <div class="collapse" id="temporary_url_container">
                    <div class="form-group custom-control custom-switch">
                        <input
                                id="schedule"
                                name="schedule"
                                type="checkbox"
                                class="custom-control-input"
                            <?= $data->values['schedule'] && !empty($data->values['start_date']) && !empty($data->values['end_date']) ? 'checked="checked"' : null ?>
                        >
                        <label class="custom-control-label" for="schedule"><?= l('links.input.schedule') ?></label>
                        <small class="form-text text-muted"><?= l('links.input.schedule_help') ?></small>
                    </div>

                    <div id="schedule_container" style="display: none;">
                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    <label><i class="fas fa-fw fa-hourglass-start fa-sm text-muted mr-1"></i> <?= l('links.input.start_date') ?></label>
                                    <input
                                            type="text"
                                            class="form-control"
                                            name="start_date"
                                            value="<?= \Altum\Date::get($data->values['start_date'], 1) ?>"
                                            placeholder="<?= l('links.input.start_date') ?>"
                                            autocomplete="off"
                                            data-daterangepicker
                                    />
                                </div>
                            </div>

                            <div class="col">
                                <div class="form-group">
                                    <label><i class="fas fa-fw fa-hourglass-end fa-sm text-muted mr-1"></i> <?= l('links.input.end_date') ?></label>
                                    <input
                                            type="text"
                                            class="form-control"
                                            name="end_date"
                                            value="<?= \Altum\Date::get($data->values['end_date'], 1) ?>"
                                            placeholder="<?= l('links.input.end_date') ?>"
                                            autocomplete="off"
                                            data-daterangepicker
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="pageviews_limit"><i class="fas fa-fw fa-mouse fa-sm text-muted mr-1"></i> <?= l('links.input.pageviews_limit') ?></label>
                        <input id="pageviews_limit" type="number" class="form-control" name="pageviews_limit" value="<?= $data->values['pageviews_limit'] ?>" />
                        <small class="form-text text-muted"><?= l('links.input.pageviews_limit_help') ?></small>
                    </div>

                    <div class="form-group">
                        <label for="expiration_url"><i class="fas fa-fw fa-hourglass-end fa-sm text-muted mr-1"></i> <?= l('links.input.expiration_url') ?></label>
                        <input id="expiration_url" type="url" class="form-control <?= \Altum\Alerts::has_field_errors('expiration_url') ? 'is-invalid' : null ?>" name="expiration_url" value="<?= $data->values['expiration_url'] ?>" maxlength="2048" />
                        <?= \Altum\Alerts::output_field_error('expiration_url') ?>
                        <small class="form-text text-muted"><?= l('links.input.expiration_url_help') ?></small>
                    </div>
                </div>

                <button class="btn btn-block btn-gray-200 my-4 <?= \Altum\Alerts::has_field_errors(['targeting_*']) ? 'border-danger' : null ?>" type="button" data-toggle="collapse" data-target="#targeting_container" aria-expanded="false" aria-controls="targeting_container">
                    <i class="fas fa-fw fa-bullseye fa-sm mr-1"></i> <?= l('links.input.targeting') ?>
                </button>

                <div class="collapse" id="targeting_container">
                    <div <?= $this->user->plan_settings->targeting_is_enabled ? null : get_plan_feature_disabled_info() ?>>
                        <div class="<?= $this->user->plan_settings->targeting_is_enabled ? null : 'container-disabled' ?>">

                            <div class="form-group">
                                <label for="targeting_type"><i class="fas fa-fw fa-bullseye fa-sm text-muted mr-1"></i> <?= l('links.input.targeting_type') ?></label>
                                <select id="targeting_type" name="targeting_type" class="custom-select">
                                    <option value="false" <?= $data->values['targeting_type'] == 'false' ? 'selected="selected"' : null?>>😊 <?= l('global.none') ?></option>
                                    <option value="continent_code" <?= $data->values['targeting_type'] == 'continent_code' ? 'selected="selected"' : null?>>🌍 <?= l('global.continent') ?></option>
                                    <option value="country_code" <?= $data->values['targeting_type'] == 'country_code' ? 'selected="selected"' : null?>>🇨🇺 <?= l('global.country') ?></option>
                                    <option value="city_name" <?= $data->values['targeting_type'] == 'city_name' ? 'selected="selected"' : null?>>🏙️ <?= l('global.city') ?></option>
                                    <option value="device_type" <?= $data->values['targeting_type'] == 'device_type' ? 'selected="selected"' : null?>>📱 <?= l('links.input.targeting_type_device_type') ?></option>
                                    <option value="os_name" <?= $data->values['targeting_type'] == 'os_name' ? 'selected="selected"' : null?>>💻 <?= l('links.input.targeting_type_os_name') ?></option>
                                    <option value="browser_name" <?= $data->values['targeting_type'] == 'browser_name' ? 'selected="selected"' : null?>>🌐 <?= l('links.input.targeting_type_browser_name') ?></option>
                                    <option value="browser_language" <?= $data->values['targeting_type'] == 'browser_language' ? 'selected="selected"' : null?>>🗣️ <?= l('links.input.targeting_type_browser_language') ?></option>
                                    <option value="rotation" <?= $data->values['targeting_type'] == 'rotation' ? 'selected="selected"' : null?>>🔄 <?= l('links.input.targeting_type_rotation') ?></option>
                                </select>
                            </div>

                            <div data-targeting-type="false" class="d-none"></div>

                            <div data-targeting-type="continent_code" class="d-none">
                                <p class="small text-muted"><?= l('links.input.targeting_type_continent_code_help') ?></p>

                                <div data-targeting-list="continent_code">
                                    <?php if(isset($data->values['targeting_continent_code']) && !empty($data->values['targeting_continent_code'])): ?>
                                        <?php foreach($data->values['targeting_continent_code'] as $key => $targeting): ?>
                                        <?php $targeting = (object) $targeting ?>
                                            <div class="form-row">
                                                <div class="form-group col-lg-5">
                                                    <select name="targeting_continent_code_key[<?= $key ?>]" class="custom-select">
                                                        <?php foreach(get_continents_array() as $continent_code => $continent_name): ?>
                                                            <option value="<?= $continent_code ?>" <?= $targeting->key == $continent_code ? 'selected="selected"' : null ?>><?= $continent_name ?></option>
                                                        <?php endforeach ?>
                                                    </select>
                                                </div>

                                                <div class="form-group col-lg-5">
                                                    <input type="url" name="targeting_continent_code_value[<?= $key ?>]" class="form-control <?= \Altum\Alerts::has_field_errors('targeting_continent_code_value[' . $key . ']') ? 'is-invalid' : null ?>" value="<?= $targeting->value ?>" maxlength="2048" placeholder="<?= l('global.url_placeholder') ?>" />
                                                    <?= \Altum\Alerts::output_field_error('targeting_continent_code_value[' . $key . ']') ?>
                                                </div>

                                                <div class="form-group col-lg-2 text-center">
                                                    <button type="button" data-targeting-remove="" class="btn btn-block btn-outline-danger" title="<?= l('global.delete') ?>"><i class="fas fa-fw fa-times"></i></button>
                                                </div>
                                            </div>
                                        <?php endforeach ?>
                                    <?php endif ?>
                                </div>

                                <div class="mb-3">
                                    <button data-targeting-add="continent_code" type="button" class="btn btn-sm btn-outline-success"><i class="fas fa-fw fa-plus-circle fa-sm mr-1"></i> <?= l('global.create') ?></button>
                                </div>
                            </div>

                            <div data-targeting-type="country_code" class="d-none">
                                <p class="small text-muted"><?= l('links.input.targeting_type_country_code_help') ?></p>

                                <div data-targeting-list="country_code">
                                    <?php if(isset($data->values['targeting_country_code']) && !empty($data->values['targeting_country_code'])): ?>
                                        <?php foreach($data->values['targeting_country_code'] as $key => $targeting): ?>
                                            <div class="form-row">
                                                <div class="form-group col-lg-5">
                                                    <select name="targeting_country_code_key[<?= $key ?>]" class="custom-select">
                                                        <?php foreach(get_countries_array() as $country => $country_name): ?>
                                                            <option value="<?= $country ?>" <?= $targeting->key == $country ? 'selected="selected"' : null ?>><?= $country_name ?></option>
                                                        <?php endforeach ?>
                                                    </select>
                                                </div>

                                                <div class="form-group col-lg-5">
                                                    <input type="url" name="targeting_country_code_value[<?= $key ?>]" class="form-control <?= \Altum\Alerts::has_field_errors('targeting_country_code_value[' . $key . ']') ? 'is-invalid' : null ?>" value="<?= $targeting->value ?>" maxlength="2048" placeholder="<?= l('global.url_placeholder') ?>" />
                                                    <?= \Altum\Alerts::output_field_error('targeting_country_code_value[' . $key . ']') ?>
                                                </div>

                                                <div class="form-group col-lg-2 text-center">
                                                    <button type="button" data-targeting-remove="" class="btn btn-block btn-outline-danger" title="<?= l('global.delete') ?>"><i class="fas fa-fw fa-times"></i></button>
                                                </div>
                                            </div>
                                        <?php endforeach ?>
                                    <?php endif ?>
                                </div>

                                <div class="mb-3">
                                    <button data-targeting-add="country_code" type="button" class="btn btn-sm btn-outline-success"><i class="fas fa-fw fa-plus-circle fa-sm mr-1"></i> <?= l('global.create') ?></button>
                                </div>
                            </div>

                            <div data-targeting-type="city_name" class="d-none">
                                <p class="small text-muted"><?= l('links.input.targeting_type_city_name_help') ?></p>

                                <div data-targeting-list="city_name">
                                    <?php if(isset($data->values['targeting_city_name']) && !empty($data->values['targeting_city_name'])): ?>
                                        <?php foreach($data->values['targeting_city_name'] as $key => $targeting): ?>
                                            <div class="form-row">
                                                <div class="form-group col-lg-5">
                                                    <input type="text" name="targeting_city_name_key[<?= $key ?>]" class="form-control" value="<?= $targeting->key ?>" placeholder="<?= l('links.input.targeting_type_city_name_placeholder') ?>" maxlength="128" />
                                                </div>

                                                <div class="form-group col-lg-5">
                                                    <input type="url" name="targeting_city_name_value[<?= $key ?>]" class="form-control <?= \Altum\Alerts::has_field_errors('targeting_city_name_value[' . $key . ']') ? 'is-invalid' : null ?>" value="<?= $targeting->value ?>" maxlength="2048" placeholder="<?= l('global.url_placeholder') ?>" />
                                                    <?= \Altum\Alerts::output_field_error('targeting_city_name_value[' . $key . ']') ?>
                                                </div>

                                                <div class="form-group col-lg-2 text-center">
                                                    <button type="button" data-targeting-remove="" class="btn btn-block btn-outline-danger" title="<?= l('global.delete') ?>"><i class="fas fa-fw fa-times"></i></button>
                                                </div>
                                            </div>
                                        <?php endforeach ?>
                                    <?php endif ?>
                                </div>

                                <div class="mb-3">
                                    <button data-targeting-add="city_name" type="button" class="btn btn-sm btn-outline-success"><i class="fas fa-fw fa-plus-circle fa-sm mr-1"></i> <?= l('global.create') ?></button>
                                </div>
                            </div>

                            <div data-targeting-type="device_type" class="d-none">
                                <p class="small text-muted"><?= l('links.input.targeting_type_device_type_help') ?></p>

                                <div data-targeting-list="device_type">
                                    <?php if(isset($data->values['targeting_device_type']) && !empty($data->values['targeting_device_type'])): ?>
                                        <?php foreach($data->values['targeting_device_type'] as $key => $targeting): ?>
                                            <div class="form-row">
                                                <div class="form-group col-lg-5">
                                                    <select name="targeting_device_type_key[<?= $key ?>]" class="custom-select">
                                                        <?php foreach(['desktop', 'tablet', 'mobile'] as $device_type): ?>
                                                            <option value="<?= $device_type ?>" <?= $targeting->key == $device_type ? 'selected="selected"' : null ?>><?= l('global.device.' . $device_type) ?></option>
                                                        <?php endforeach ?>
                                                    </select>
                                                </div>

                                                <div class="form-group col-lg-5">
                                                    <input type="url" name="targeting_device_type_value[<?= $key ?>]" class="form-control <?= \Altum\Alerts::has_field_errors('targeting_device_type_value[' . $key . ']') ? 'is-invalid' : null ?>" value="<?= $targeting->value ?>" maxlength="2048" placeholder="<?= l('global.url_placeholder') ?>" />
                                                    <?= \Altum\Alerts::output_field_error('targeting_device_type_value[' . $key . ']') ?>
                                                </div>

                                                <div class="form-group col-lg-2 text-center">
                                                    <button type="button" data-targeting-remove="" class="btn btn-block btn-outline-danger" title="<?= l('global.delete') ?>"><i class="fas fa-fw fa-times"></i></button>
                                                </div>
                                            </div>
                                        <?php endforeach ?>
                                    <?php endif ?>
                                </div>

                                <div class="mb-3">
                                    <button data-targeting-add="device_type" type="button" class="btn btn-sm btn-outline-success"><i class="fas fa-fw fa-plus-circle fa-sm mr-1"></i> <?= l('global.create') ?></button>
                                </div>
                            </div>

                            <div data-targeting-type="os_name" class="d-none">
                                <p class="small text-muted"><?= l('links.input.targeting_type_os_name_help') ?></p>

                                <div data-targeting-list="os_name">
                                    <?php if(isset($data->values['targeting_os_name']) && !empty($data->values['targeting_os_name'])): ?>
                                        <?php foreach($data->values['targeting_os_name'] as $key => $targeting): ?>
                                            <div class="form-row">
                                                <div class="form-group col-lg-5">
                                                    <select name="targeting_os_name_key[<?= $key ?>]" class="custom-select">
                                                        <?php foreach(['iOS', 'Android', 'Windows', 'OS X', 'Linux', 'Ubuntu', 'Chrome OS'] as $os_name): ?>
                                                            <option value="<?= $os_name ?>" <?= $targeting->key == $os_name ? 'selected="selected"' : null ?>><?= $os_name ?></option>
                                                        <?php endforeach ?>
                                                    </select>
                                                </div>

                                                <div class="form-group col-lg-5">
                                                    <input type="url" name="targeting_os_name_value[<?= $key ?>]" class="form-control <?= \Altum\Alerts::has_field_errors('targeting_os_name_value[' . $key . ']') ? 'is-invalid' : null ?>" value="<?= $targeting->value ?>" maxlength="2048" placeholder="<?= l('global.url_placeholder') ?>" />
                                                    <?= \Altum\Alerts::output_field_error('targeting_os_name_value[' . $key . ']') ?>
                                                </div>

                                                <div class="form-group col-lg-2 text-center">
                                                    <button type="button" data-targeting-remove="" class="btn btn-block btn-outline-danger" title="<?= l('global.delete') ?>"><i class="fas fa-fw fa-times"></i></button>
                                                </div>
                                            </div>
                                        <?php endforeach ?>
                                    <?php endif ?>
                                </div>

                                <div class="mb-3">
                                    <button data-targeting-add="os_name" type="button" class="btn btn-sm btn-outline-success"><i class="fas fa-fw fa-plus-circle fa-sm mr-1"></i> <?= l('global.create') ?></button>
                                </div>
                            </div>

                            <div data-targeting-type="browser_name" class="d-none">
                                <p class="small text-muted"><?= l('links.input.targeting_type_browser_name_help') ?></p>

                                <div data-targeting-list="browser_name">
                                    <?php if(isset($data->values['targeting_browser_name']) && !empty($data->values['targeting_browser_name'])): ?>
                                        <?php foreach($data->values['targeting_browser_name'] as $key => $targeting): ?>
                                            <div class="form-row">
                                                <div class="form-group col-lg-5">
                                                    <select name="targeting_browser_name_key[<?= $key ?>]" class="custom-select">
                                                        <?php foreach(['Chrome', 'Firefox', 'Safari', 'Edge', 'Opera', 'Samsung Internet'] as $browser_name): ?>
                                                            <option value="<?= $browser_name ?>" <?= $targeting->key == $browser_name ? 'selected="selected"' : null ?>><?= $browser_name ?></option>
                                                        <?php endforeach ?>
                                                    </select>
                                                </div>

                                                <div class="form-group col-lg-5">
                                                    <input type="url" name="targeting_browser_name_value[<?= $key ?>]" class="form-control <?= \Altum\Alerts::has_field_errors('targeting_browser_name_value[' . $key . ']') ? 'is-invalid' : null ?>" value="<?= $targeting->value ?>" maxlength="2048" placeholder="<?= l('global.url_placeholder') ?>" />
                                                    <?= \Altum\Alerts::output_field_error('targeting_browser_name_value[' . $key . ']') ?>
                                                </div>

                                                <div class="form-group col-lg-2 text-center">
                                                    <button type="button" data-targeting-remove="" class="btn btn-block btn-outline-danger" title="<?= l('global.delete') ?>"><i class="fas fa-fw fa-times"></i></button>
                                                </div>
                                            </div>
                                        <?php endforeach ?>
                                    <?php endif ?>
                                </div>

                                <div class="mb-3">
                                    <button data-targeting-add="browser_name" type="button" class="btn btn-sm btn-outline-success"><i class="fas fa-fw fa-plus-circle fa-sm mr-1"></i> <?= l('global.create') ?></button>
                                </div>
                            </div>

                            <div data-targeting-type="browser_language" class="d-none">
                                <p class="small text-muted"><?= l('links.input.targeting_type_browser_language_help') ?></p>

                                <div data-targeting-list="browser_language">
                                    <?php if(isset($data->values['targeting_browser_language']) && !empty($data->values['targeting_browser_language'])): ?>
                                        <?php foreach($data->values['targeting_browser_language'] as $key => $targeting): ?>
                                            <div class="form-row">
                                                <div class="form-group col-lg-5">
                                                    <select name="targeting_browser_language_key[<?= $key ?>]" class="custom-select">
                                                        <?php foreach(get_locale_languages_array() as $locale => $language): ?>
                                                            <option value="<?= $locale ?>" <?= $targeting->key == $locale ? 'selected="selected"' : null ?>><?= $language ?></option>
                                                        <?php endforeach ?>
                                                    </select>
                                                </div>

                                                <div class="form-group col-lg-5">
                                                    <input type="url" name="targeting_browser_language_value[<?= $key ?>]" class="form-control <?= \Altum\Alerts::has_field_errors('targeting_browser_language_value[' . $key . ']') ? 'is-invalid' : null ?>" value="<?= $targeting->value ?>" maxlength="2048" placeholder="<?= l('global.url_placeholder') ?>" />
                                                    <?= \Altum\Alerts::output_field_error('targeting_browser_language_value[' . $key . ']') ?>
                                                </div>

                                                <div class="form-group col-lg-2 text-center">
                                                    <button type="button" data-targeting-remove="" class="btn btn-block btn-outline-danger" title="<?= l('global.delete') ?>"><i class="fas fa-fw fa-times"></i></button>
                                                </div>
                                            </div>
                                        <?php endforeach ?>
                                    <?php endif ?>
                                </div>

                                <div class="mb-3">
                                    <button data-targeting-add="browser_language" type="button" class="btn btn-sm btn-outline-success"><i class="fas fa-fw fa-plus-circle fa-sm mr-1"></i> <?= l('global.create') ?></button>
                                </div>
                            </div>

                            <div data-targeting-type="rotation" class="d-none">
                                <p class="small text-muted"><?= l('links.input.targeting_type_rotation_help') ?></p>

                                <div data-targeting-list="rotation">
                                    <?php if(isset($data->values['targeting_rotation']) && !empty($data->values['targeting_rotation'])): ?>
                                        <?php foreach($data->values['targeting_rotation'] as $key => $targeting): ?>
                                            <div class="form-row">
                                                <div class="form-group col-lg-5">
                                                    <input type="number" min="0" max="100" name="targeting_rotation_key[<?= $key ?>]" class="form-control" value="<?= $targeting->key ?? 1 ?>" placeholder="<?= l('links.input.targeting_type_percentage') ?>" required="required" />
                                                </div>

                                                <div class="form-group col-lg-5">
                                                    <input type="url" name="targeting_rotation_value[<?= $key ?>]" class="form-control <?= \Altum\Alerts::has_field_errors('targeting_rotation_value[' . $key . ']') ? 'is-invalid' : null ?>" value="<?= $targeting->value ?>" maxlength="2048" placeholder="<?= l('global.url_placeholder') ?>" />
                                                    <?= \Altum\Alerts::output_field_error('targeting_rotation_value[' . $key . ']') ?>
                                                </div>

                                                <div class="form-group col-lg-2 text-center">
                                                    <button type="button" data-targeting-remove="" class="btn btn-block btn-outline-danger" title="<?= l('global.delete') ?>"><i class="fas fa-fw fa-times"></i></button>
                                                </div>
                                            </div>
                                        <?php endforeach ?>
                                    <?php endif ?>
                                </div>

                                <div class="mb-3">
                                    <button data-targeting-add="rotation" type="button" class="btn btn-sm btn-outline-success"><i class="fas fa-fw fa-plus-circle fa-sm mr-1"></i> <?= l('global.create') ?></button>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <button class="btn btn-block btn-gray-200 my-4" type="button" data-toggle="collapse" data-target="#utm_container" aria-expanded="false" aria-controls="utm_container">
                    <i class="fas fa-fw fa-keyboard fa-sm mr-1"></i> <?= l('links.input.utm') ?>
                </button>

                <div class="collapse" id="utm_container">
                    <div <?= $this->user->plan_settings->utm_parameters_is_enabled ? null : get_plan_feature_disabled_info() ?>>
                        <div class="<?= $this->user->plan_settings->utm_parameters_is_enabled ? null : 'container-disabled' ?>">
                            <div class="form-group">
                                <label for="utm_source"><i class="fas fa-fw fa-sitemap fa-sm text-muted mr-1"></i> <?= l('links.input.utm_source') ?></label>
                                <input id="utm_source" type="text" class="form-control" name="utm_source" value="<?= $data->values['utm_source'] ?? '' ?>" maxlength="128" placeholder="<?= l('links.input.utm_source_placeholder') ?>" />
                            </div>

                            <div class="form-group">
                                <label for="utm_medium"><i class="fas fa-fw fa-inbox fa-sm text-muted mr-1"></i> <?= l('links.input.utm_medium') ?></label>
                                <input id="utm_medium" type="text" class="form-control" name="utm_medium" value="<?= $data->values['utm_medium'] ?? '' ?>" maxlength="128" placeholder="<?= l('links.input.utm_medium_placeholder') ?>" />
                            </div>

                            <div class="form-group">
                                <label for="utm_campaign"><i class="fas fa-fw fa-bullhorn fa-sm text-muted mr-1"></i> <?= l('links.input.utm_campaign') ?></label>
                                <input id="utm_campaign" type="text" class="form-control" name="utm_campaign" value="<?= $data->values['utm_campaign'] ?? '' ?>" maxlength="128" placeholder="<?= l('links.input.utm_campaign_placeholder') ?>" />
                            </div>

                            <div class="form-group">
                                <label for="utm_preview"><i class="fas fa-fw fa-eye fa-sm text-muted mr-1"></i> <?= l('links.input.utm_preview') ?></label>
                                <input id="utm_preview" type="text" class="form-control-plaintext" name="utm_preview" readonly="readonly" />
                                <small class="form-text text-muted"><?= l('links.input.utm_preview_help') ?></small>
                            </div>
                        </div>
                    </div>
                </div>

                <button class="btn btn-block btn-gray-200 my-4" type="button" data-toggle="collapse" data-target="#protection_container" aria-expanded="false" aria-controls="protection_container">
                    <i class="fas fa-fw fa-user-shield fa-sm mr-1"></i> <?= l('links.input.protection') ?>
                </button>

                <div class="collapse" id="protection_container">
                    <div <?= $this->user->plan_settings->password_protection_is_enabled ? null : get_plan_feature_disabled_info() ?>>
                        <div class="form-group <?= $this->user->plan_settings->password_protection_is_enabled ? null : 'container-disabled' ?>" data-password-toggle-view data-password-toggle-view-show="<?= l('global.show') ?>" data-password-toggle-view-hide="<?= l('global.hide') ?>">
                            <label for="password"><i class="fas fa-fw fa-sm fa-lock text-muted mr-1"></i> <?= l('global.password') ?></label>
                            <input type="password" id="password" name="password" class="form-control" value="<?= $data->values['password'] ?>" autocomplete="new-password" />
                        </div>
                    </div>

                    <div <?= $this->user->plan_settings->sensitive_content_is_enabled ? null : get_plan_feature_disabled_info() ?>>
                        <div class="<?= $this->user->plan_settings->sensitive_content_is_enabled ? null : 'container-disabled' ?>">
                            <div class="form-group custom-control custom-switch">
                                <input
                                        type="checkbox"
                                        class="custom-control-input"
                                        id="sensitive_content"
                                        name="sensitive_content"
                                    <?= !$this->user->plan_settings->sensitive_content_is_enabled ? 'disabled="disabled"': null ?>
                                    <?= $data->values['sensitive_content'] ? 'checked="checked"' : null ?>
                                >
                                <label class="custom-control-label" for="sensitive_content"><?= l('links.input.sensitive_content') ?></label>
                                <small class="form-text text-muted"><?= l('links.input.sensitive_content_help') ?></small>
                            </div>
                        </div>
                    </div>
                </div>

                <button class="btn btn-block btn-gray-200 my-4" type="button" data-toggle="collapse" data-target="#cloaking_container" aria-expanded="false" aria-controls="cloaking_container">
                    <i class="fas fa-fw fa-eye fa-sm mr-1"></i> <?= l('links.input.cloaking') ?>
                </button>

                <div class="collapse" id="cloaking_container">
                    <div <?= $this->user->plan_settings->cloaking_is_enabled ? null : get_plan_feature_disabled_info() ?>>
                        <div class="<?= $this->user->plan_settings->cloaking_is_enabled ? null : 'container-disabled' ?>">
                            <div class="form-group custom-control custom-switch">
                                <input
                                        id="cloaking_is_enabled"
                                        name="cloaking_is_enabled"
                                        type="checkbox"
                                        class="custom-control-input"
                                    <?= $data->values['cloaking_is_enabled'] ? 'checked="checked"' : null ?>
                                    <?= $this->user->plan_settings->cloaking_is_enabled ? null : 'disabled="disabled"' ?>
                                >
                                <label class="custom-control-label" for="cloaking_is_enabled"><i class="fas fa-fw fa-user-tie fa-sm text-muted mr-1"></i> <?= l('links.input.cloaking_is_enabled') ?></label>
                                <small class="form-text text-muted"><?= l('links.input.cloaking_is_enabled_help') ?></small>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="cloaking_title"><i class="fas fa-fw fa-pen fa-sm text-muted mr-1"></i> <?= l('links.input.cloaking_title') ?></label>
                        <input id="cloaking_title" type="text" class="form-control" name="cloaking_title" value="<?= $data->values['cloaking_title'] ?>" maxlength="70" />
                    </div>

                    <div class="form-group">
                        <label for="cloaking_meta_description"><i class="fas fa-fw fa-paragraph fa-sm text-muted mr-1"></i> <?= l('links.input.cloaking_meta_description') ?></label>
                        <input id="cloaking_meta_description" type="text" class="form-control" name="cloaking_meta_description" value="<?= $data->values['cloaking_meta_description'] ?>" maxlength="160" />
                    </div>

                    <div class="form-group" data-file-image-input-wrapper data-file-input-wrapper-size-limit="<?= settings()->links->favicon_size_limit ?>" data-file-input-wrapper-size-limit-error="<?= sprintf(l('global.error_message.file_size_limit'), settings()->links->favicon_size_limit) ?>">
                        <label><i class="fas fa-fw fa-image fa-sm text-muted mr-1"></i> <?= l('links.input.cloaking_favicon') ?></label>
                        <?= include_view(THEME_PATH . 'views/partials/file_image_input.php', ['uploads_file_key' => 'favicons', 'file_key' => 'cloaking_favicon', 'already_existing_image' => null, 'input_data' => 'data-crop data-aspect-ratio="1"']) ?>
                        <?= \Altum\Alerts::output_field_error('cloaking_favicon') ?>
                        <small class="form-text text-muted"><?= sprintf(l('global.accessibility.whitelisted_file_extensions'), \Altum\Uploads::get_whitelisted_file_extensions_accept('favicons')) . ' ' . sprintf(l('global.accessibility.file_size_limit'), settings()->links->favicon_size_limit) ?></small>
                    </div>

                    <div class="form-group" data-file-image-input-wrapper data-file-input-wrapper-size-limit="<?= settings()->links->opengraph_size_limit ?>" data-file-input-wrapper-size-limit-error="<?= sprintf(l('global.error_message.file_size_limit'), settings()->links->opengraph_size_limit) ?>">
                        <label><i class="fas fa-fw fa-image fa-sm text-muted mr-1"></i> <?= l('links.input.cloaking_opengraph') ?></label>
                        <?= include_view(THEME_PATH . 'views/partials/file_image_input.php', ['uploads_file_key' => 'opengraphs', 'file_key' => 'cloaking_opengraph', 'already_existing_image' => null, 'input_data' => 'data-crop data-aspect-ratio="1.91"']) ?>
                        <?= \Altum\Alerts::output_field_error('cloaking_opengraph') ?>
                        <small class="form-text text-muted"><?= sprintf(l('global.accessibility.whitelisted_file_extensions'), \Altum\Uploads::get_whitelisted_file_extensions_accept('opengraphs')) . ' ' . sprintf(l('global.accessibility.file_size_limit'), settings()->links->opengraph_size_limit) ?></small>
                    </div>

                    <div <?= 1 ? null : get_plan_feature_disabled_info() ?>>
                        <div class="form-group <?= 1 ? null : 'container-disabled' ?>" data-character-counter="textarea">
                            <label for="cloaking_custom_js" class="d-flex justify-content-between align-items-center">
                                <span><i class="fab fa-fw fa-sm fa-js-square text-muted mr-1"></i> <?= l('global.custom_js') ?></span>
                                <small class="text-muted" data-character-counter-wrapper></small>
                            </label>
                            <textarea id="cloaking_custom_js" class="form-control" name="cloaking_custom_js" maxlength="10000" placeholder="<?= l('global.custom_js_placeholder') ?>"><?= $data->values['cloaking_custom_js'] ?></textarea>
                            <small class="form-text text-muted"><?= l('global.custom_js_help') ?></small>
                        </div>
                    </div>
                </div>

                <button class="btn btn-block btn-gray-200 my-4" type="button" data-toggle="collapse" data-target="#http_container" aria-expanded="false" aria-controls="http_container">
                    <i class="fas fa-fw fa-laptop-code fa-sm mr-1"></i> <?= l('links.input.http') ?>
                </button>

                <div class="collapse" id="http_container">
                    <div class="alert alert-info"><?= l('links.input.http_header_help') ?></div>

                    <div class="form-group custom-control custom-radio">
                        <input type="radio" id="http_status_code_301" name="http_status_code" value="301" class="custom-control-input" <?= $data->values['http_status_code'] == '301' ? 'checked="checked"' : null ?>>
                        <label class="custom-control-label" for="http_status_code_301"><?= l('links.input.http_status_code.301') ?></label>
                    </div>

                    <div class="form-group custom-control custom-radio">
                        <input type="radio" id="http_status_code_302" name="http_status_code" value="302" class="custom-control-input" <?= $data->values['http_status_code'] == '302' ? 'checked="checked"' : null ?>>
                        <label class="custom-control-label" for="http_status_code_302"><?= l('links.input.http_status_code.302') ?></label>
                    </div>

                    <div class="form-group custom-control custom-radio">
                        <input type="radio" id="http_status_code_307" name="http_status_code" value="307" class="custom-control-input" <?= $data->values['http_status_code'] == '307' ? 'checked="checked"' : null ?>>
                        <label class="custom-control-label" for="http_status_code_307"><?= l('links.input.http_status_code.307') ?></label>
                    </div>

                    <div class="form-group custom-control custom-radio">
                        <input type="radio" id="http_status_code_308" name="http_status_code" value="308" class="custom-control-input" <?= $data->values['http_status_code'] == '308' ? 'checked="checked"' : null ?>>
                        <label class="custom-control-label" for="http_status_code_308"><?= l('links.input.http_status_code.308') ?></label>
                    </div>
                </div>

                <button class="btn btn-block btn-gray-200 my-4" type="button" data-toggle="collapse" data-target="#advanced_container" aria-expanded="false" aria-controls="advanced_container">
                    <i class="fas fa-fw fa-user-tie fa-sm mr-1"></i> <?= l('links.input.advanced') ?>
                </button>

                <div class="collapse" id="advanced_container">
                    <?php if(settings()->links->projects_is_enabled): ?>
                        <div class="form-group">
                            <div class="d-flex flex-wrap flex-row justify-content-between">
                                <label for="project_id"><i class="fas fa-fw fa-sm fa-project-diagram text-muted mr-1"></i> <?= l('projects.project_id') ?></label>
                                <a href="<?= url('project-create') ?>" target="_blank" class="small mb-2"><i class="fas fa-fw fa-sm fa-plus mr-1"></i> <?= l('projects.create') ?></a>
                            </div>
                            <select id="project_id" name="project_id" class="custom-select">
                                <option value=" "><?= l('global.none') ?></option>
                                <?php foreach($data->projects as $project_id => $project): ?>
                                    <option value="<?= $project_id ?>" <?= $data->values['project_id'] == $project_id ? 'selected="selected"' : null ?>><?= $project->name ?></option>
                                <?php endforeach ?>
                            </select>
                            <small class="form-text text-muted"><?= l('projects.project_id_help') ?></small>
                        </div>
                    <?php endif ?>

                    <div class="form-group custom-control custom-switch">
                        <input
                                id="forward_query_parameters_is_enabled"
                                name="forward_query_parameters_is_enabled"
                                type="checkbox"
                                class="custom-control-input"
                            <?= $data->values['forward_query_parameters_is_enabled'] ? 'checked="checked"' : null ?>
                        >
                        <label class="custom-control-label" for="forward_query_parameters_is_enabled"><i class="fas fa-fw fa-forward fa-sm text-muted mr-1"></i> <?= l('links.input.forward_query_parameters_is_enabled') ?></label>
                        <small class="form-text text-muted"><?= l('links.input.forward_query_parameters_is_enabled_help') ?></small>
                    </div>
                </div>

                <button type="submit" name="submit" class="btn btn-block btn-primary mt-4"><?= l('global.create') ?></button>
            </form>

        </div>
    </div>
</div>


<template id="template_targeting_continent_code">
    <div class="form-row">
        <div class="form-group col-lg-5">
            <select name="targeting_continent_code_key[]" class="custom-select">
                <?php foreach(get_continents_array() as $continent_code => $continent_name): ?>
                    <option value="<?= $continent_code ?>"><?= $continent_name ?></option>
                <?php endforeach ?>
            </select>
        </div>

        <div class="form-group col-lg-5">
            <input type="url" name="targeting_continent_code_value[]" class="form-control" value="" maxlength="2048" placeholder="<?= l('global.url_placeholder') ?>" />
        </div>

        <div class="form-group col-lg-2 text-center">
            <button type="button" data-targeting-remove="" class="btn btn-block btn-outline-danger" title="<?= l('global.delete') ?>"><i class="fas fa-fw fa-times"></i></button>
        </div>
    </div>
</template>

<template id="template_targeting_country_code">
    <div class="form-row">
        <div class="form-group col-lg-5">
            <select name="targeting_country_code_key[]" class="custom-select">
                <?php foreach(get_countries_array() as $country => $country_name): ?>
                    <option value="<?= $country ?>"><?= $country_name ?></option>
                <?php endforeach ?>
            </select>
        </div>

        <div class="form-group col-lg-5">
            <input type="url" name="targeting_country_code_value[]" class="form-control" value="" maxlength="2048" placeholder="<?= l('global.url_placeholder') ?>" />
        </div>

        <div class="form-group col-lg-2 text-center">
            <button type="button" data-targeting-remove="" class="btn btn-block btn-outline-danger" title="<?= l('global.delete') ?>"><i class="fas fa-fw fa-times"></i></button>
        </div>
    </div>
</template>

<template id="template_targeting_city_name">
    <div class="form-row">
        <div class="form-group col-lg-5">
            <input type="text" name="targeting_city_name_key[]" class="form-control" value="" placeholder="<?= l('links.input.targeting_type_city_name_placeholder') ?>" maxlength="128" />
        </div>

        <div class="form-group col-lg-5">
            <input type="url" name="targeting_city_name_value[]" class="form-control" value="" maxlength="2048" placeholder="<?= l('global.url_placeholder') ?>" />
        </div>

        <div class="form-group col-lg-2 text-center">
            <button type="button" data-targeting-remove="" class="btn btn-block btn-outline-danger" title="<?= l('global.delete') ?>"><i class="fas fa-fw fa-times"></i></button>
        </div>
    </div>
</template>

<template id="template_targeting_device_type">
    <div class="form-row">
        <div class="form-group col-lg-5">
            <select name="targeting_device_type_key[]" class="custom-select">
                <?php foreach(['desktop', 'tablet', 'mobile'] as $device_type): ?>
                    <option value="<?= $device_type ?>"><?= l('global.device.' . $device_type) ?></option>
                <?php endforeach ?>
            </select>
        </div>

        <div class="form-group col-lg-5">
            <input type="url" name="targeting_device_type_value[]" class="form-control" value="" maxlength="2048" placeholder="<?= l('global.url_placeholder') ?>" />
        </div>

        <div class="form-group col-lg-2 text-center">
            <button type="button" data-targeting-remove="" class="btn btn-block btn-outline-danger" title="<?= l('global.delete') ?>"><i class="fas fa-fw fa-times"></i></button>
        </div>
    </div>
</template>

<template id="template_targeting_os_name">
    <div class="form-row">
        <div class="form-group col-lg-5">
            <select name="targeting_os_name_key[]" class="custom-select">
                <?php foreach(['iOS', 'Android', 'Windows', 'OS X', 'Linux', 'Ubuntu', 'Chrome OS'] as $os_name): ?>
                    <option value="<?= $os_name ?>"><?= $os_name ?></option>
                <?php endforeach ?>
            </select>
        </div>

        <div class="form-group col-lg-5">
            <input type="url" name="targeting_os_name_value[]" class="form-control" value="" maxlength="2048" placeholder="<?= l('global.url_placeholder') ?>" />
        </div>

        <div class="form-group col-lg-2 text-center">
            <button type="button" data-targeting-remove="" class="btn btn-block btn-outline-danger" title="<?= l('global.delete') ?>"><i class="fas fa-fw fa-times"></i></button>
        </div>
    </div>
</template>

<template id="template_targeting_browser_name">
    <div class="form-row">
        <div class="form-group col-lg-5">
            <select name="targeting_browser_name_key[]" class="custom-select">
                <?php foreach(['Chrome', 'Firefox', 'Safari', 'Edge', 'Opera', 'Samsung Internet'] as $browser_name): ?>
                    <option value="<?= $browser_name ?>"><?= $browser_name ?></option>
                <?php endforeach ?>
            </select>
        </div>

        <div class="form-group col-lg-5">
            <input type="url" name="targeting_browser_name_value[]" class="form-control" value="" maxlength="2048" placeholder="<?= l('global.url_placeholder') ?>" />
        </div>

        <div class="form-group col-lg-2 text-center">
            <button type="button" data-targeting-remove="" class="btn btn-block btn-outline-danger" title="<?= l('global.delete') ?>"><i class="fas fa-fw fa-times"></i></button>
        </div>
    </div>
</template>

<template id="template_targeting_browser_language">
    <div class="form-row">
        <div class="form-group col-lg-5">
            <select name="targeting_browser_language_key[]" class="custom-select">
                <?php foreach(get_locale_languages_array() as $locale => $language): ?>
                    <option value="<?= $locale ?>"><?= $language ?></option>
                <?php endforeach ?>
            </select>
        </div>

        <div class="form-group col-lg-5">
            <input type="url" name="targeting_browser_language_value[]" class="form-control" value="" maxlength="2048" placeholder="<?= l('global.url_placeholder') ?>" />
        </div>

        <div class="form-group col-lg-2 text-center">
            <button type="button" data-targeting-remove="" class="btn btn-block btn-outline-danger" title="<?= l('global.delete') ?>"><i class="fas fa-fw fa-times"></i></button>
        </div>
    </div>
</template>

<template id="template_targeting_rotation">
    <div class="form-row">
        <div class="form-group col-lg-5">
            <input type="number" min="0" max="100" name="targeting_rotation_key[]" class="form-control" value="1" placeholder="<?= l('links.input.targeting_type_percentage') ?>" required="required" />
        </div>

        <div class="form-group col-lg-5">
            <input type="url" name="targeting_rotation_value[]" class="form-control" value="" maxlength="2048" placeholder="<?= l('global.url_placeholder') ?>" />
        </div>

        <div class="form-group col-lg-2 text-center">
            <button type="button" data-targeting-remove="" class="btn btn-block btn-outline-danger" title="<?= l('global.delete') ?>"><i class="fas fa-fw fa-times"></i></button>
        </div>
    </div>
</template>

<?php include_view(THEME_PATH . 'views/partials/clipboard_js.php') ?>

<?php ob_start() ?>
<link href="<?= ASSETS_FULL_URL . 'css/libraries/daterangepicker.min.css?v=' . PRODUCT_CODE ?>" rel="stylesheet" media="screen,print">
<?php \Altum\Event::add_content(ob_get_clean(), 'head') ?>

<?php ob_start() ?>
<script src="<?= ASSETS_FULL_URL . 'js/libraries/moment.min.js?v=' . PRODUCT_CODE ?>"></script>
<script src="<?= ASSETS_FULL_URL . 'js/libraries/daterangepicker.min.js?v=' . PRODUCT_CODE ?>"></script>
<script src="<?= ASSETS_FULL_URL . 'js/libraries/moment-timezone-with-data-10-year-range.min.js?v=' . PRODUCT_CODE ?>"></script>

<script>
    'use strict';
    
/* Bulk processing */
    let process_bulk = () => {
        let is_bulk = document.querySelector('input[name="is_bulk"]').checked;

        if(is_bulk) {
            document.querySelector('#location_url_wrapper').classList.add('d-none');
            document.querySelector('#location_url').removeAttribute('required');
            document.querySelector('#location_urls_wrapper').classList.remove('d-none');
            document.querySelector('#location_urls').setAttribute('required', 'required');
            document.querySelector('#url').setAttribute('disabled', 'disabled');
        } else {
            document.querySelector('#location_url_wrapper').classList.remove('d-none');
            document.querySelector('#location_url').setAttribute('required', 'required');
            document.querySelector('#location_urls_wrapper').classList.add('d-none');
            document.querySelector('#location_urls').removeAttribute('required');
            document.querySelector('#url').removeAttribute('disabled');
        }
    }

    document.querySelector('input[name="is_bulk"]').addEventListener('change', process_bulk);
    process_bulk();

    /* UTM */
    let process_utm = () => {

        let utm_source = document.querySelector('input[name="utm_source"]').value;
        let utm_medium = document.querySelector('input[name="utm_medium"]').value;
        let utm_campaign = document.querySelector('input[name="utm_campaign"]').value;
        let utm_preview = <?= json_encode(l('global.none')) ?>;

        if(utm_source || utm_medium || utm_campaign) {
            let link = new URL(<?= json_encode(SITE_URL) ?>);

            if(utm_source) link.searchParams.set('utm_source', utm_source.trim());
            if(utm_medium) link.searchParams.set('utm_medium', utm_medium.trim());
            if(utm_campaign) link.searchParams.set('utm_campaign', utm_campaign.trim());

            utm_preview = '?' + link.searchParams.toString();
        }

        document.querySelector('input[name="utm_preview"]').value = utm_preview;
    }

    document.querySelectorAll('input[name="utm_source"], input[name="utm_medium"], input[name="utm_campaign"]').forEach(element => {
        ['change', 'paste', 'keyup'].forEach(event_type => {
            element.addEventListener(event_type, process_utm);
        });
    })

    process_utm();

    /* Targeting */
    let targeting_type_handler = () => {
        let targeting_type = document.querySelector('#targeting_type').value;

        document.querySelectorAll('[data-targeting-type]').forEach(element => {
            let element_targeting_type = element.getAttribute('data-targeting-type');

            if(element_targeting_type == targeting_type) {
                document.querySelector(`[data-targeting-type="${element_targeting_type}"]`).classList.remove('d-none');
            } else {
                document.querySelector(`[data-targeting-type="${element_targeting_type}"]`).classList.add('d-none');
            }
        })
    }

    targeting_type_handler();
    document.querySelector('#targeting_type').addEventListener('change', targeting_type_handler);

    /* add new request header */
    let targeting_add = event => {
        let type = event.currentTarget.getAttribute('data-targeting-add');

        let clone = document.querySelector(`#template_targeting_${type}`).content.cloneNode(true);

        let request_headers_count = document.querySelectorAll(`[data-targeting-list="${type}"] .form-row`).length;

        clone.querySelector(`[name="targeting_${type}_key[]"`).setAttribute('name', `targeting_${type}_key[${request_headers_count}]`);
        clone.querySelector(`[name="targeting_${type}_value[]"`).setAttribute('name', `targeting_${type}_value[${request_headers_count}]`);

        document.querySelector(`[data-targeting-list="${type}"]`).appendChild(clone);

        targeting_remove_initiator();
    };

    document.querySelectorAll('[data-targeting-add]').forEach(element => {
        element.addEventListener('click', targeting_add);
    })

    /* remove request header */
    let targeting_remove = event => {
        event.currentTarget.closest('.form-row').remove();
    };

    let targeting_remove_initiator = () => {
        document.querySelectorAll('[data-targeting-remove]').forEach(element => {
            element.removeEventListener('click', targeting_remove);
            element.addEventListener('click', targeting_remove)
        })
    };

    targeting_remove_initiator();

    /* Schedule */
    let schedule_handler = () => {
        if($('#schedule').is(':checked')) {
            $('#schedule_container').show();
        } else {
            $('#schedule_container').hide();
        }
    };

    $('#schedule').on('change', schedule_handler);

    schedule_handler();

    /* Daterangepicker */
    let locale = <?= json_encode(require APP_PATH . 'includes/daterangepicker_translations.php') ?>;
    $('[data-daterangepicker]').daterangepicker({
        minDate: "<?= (new \DateTime('', new \DateTimeZone(\Altum\Date::$default_timezone)))->setTimezone(new \DateTimeZone($this->user->timezone))->format('Y-m-d H:i:s'); ?>",
        alwaysShowCalendars: true,
        singleCalendar: true,
        singleDatePicker: true,
        locale: {...locale, format: 'YYYY-MM-DD HH:mm:ss'},
        timePicker: true,
        timePicker24Hour: true,
        timePickerSeconds: true,
    }, (start, end, label) => {});
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>

<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/links/link_delete_modal.php'), 'modals'); ?>
<?php include_view(THEME_PATH . 'views/partials/js_cropper.php') ?>
