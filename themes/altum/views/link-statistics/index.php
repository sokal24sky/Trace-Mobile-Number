<?php defined('ALTUMCODE') || die() ?>


<div class="container">
    <?= \Altum\Alerts::output_alerts() ?>

    <?php if(settings()->main->breadcrumbs_is_enabled): ?>
        <nav aria-label="breadcrumb">
            <ol class="custom-breadcrumbs small">
                <li>
                    <a href="<?= url('links') ?>"><?= l('links.breadcrumb') ?></a><i class="fas fa-fw fa-angle-right"></i>
                </li>
                <li>
                    <a href="<?= url('link-update/' . $data->link->link_id) ?>"><?= l('link_update.breadcrumb') ?></a><i class="fas fa-fw fa-angle-right"></i>
                </li>
                <li class="active" aria-current="page"><?= l('link_statistics.breadcrumb') ?></li>
            </ol>
        </nav>
    <?php endif ?>

    <div class="d-flex justify-content-between align-items-center mb-2">
        <h1 class="h4 text-truncate mb-0"><i class="fas fa-fw fa-xs fa-link mr-1"></i> <?= sprintf(l('link_statistics.header'), $data->link->url) ?></h1>

        <div class="d-flex align-items-center col-auto p-0">
            <div data-toggle="tooltip" title="<?= l('statistics_reset_modal.header') ?>">
                <button
                        type="button"
                        class="btn btn-link text-secondary"
                        data-toggle="modal"
                        data-target="#link_statistics_reset_modal"
                        aria-label="<?= l('statistics_reset_modal.header') ?>"
                        data-link-id="<?= $data->link->link_id ?>"
                        data-start-date="<?= $data->datetime['start_date'] ?>"
                        data-end-date="<?= $data->datetime['end_date'] ?>"
                >
                    <i class="fas fa-fw fa-sm fa-eraser"></i>
                </button>
            </div>

            <div>
                <button
                        id="url_copy"
                        type="button"
                        class="btn btn-link text-secondary"
                        data-toggle="tooltip"
                        title="<?= l('global.clipboard_copy') ?>"
                        aria-label="<?= l('global.clipboard_copy') ?>"
                        data-copy="<?= l('global.clipboard_copy') ?>"
                        data-copied="<?= l('global.clipboard_copied') ?>"
                        data-clipboard-text="<?= $data->link->full_url ?>"
                >
                    <i class="fas fa-fw fa-sm fa-copy"></i>
                </button>
            </div>

            <div>
                <button
                        id="daterangepicker"
                        type="button"
                        class="btn btn-sm btn-light"
                        data-min-date="<?= \Altum\Date::get($data->link->datetime, 4) ?>"
                        data-max-date="<?= \Altum\Date::get('', 4) ?>"
                >
                    <i class="fas fa-fw fa-calendar mr-lg-1"></i>
                    <span class="d-none d-lg-inline-block">
                        <?php if($data->datetime['start_date'] == $data->datetime['end_date']): ?>
                            <?= \Altum\Date::get($data->datetime['start_date'], 6, \Altum\Date::$default_timezone) ?>
                        <?php else: ?>
                            <?= \Altum\Date::get($data->datetime['start_date'], 6, \Altum\Date::$default_timezone) . ' - ' . \Altum\Date::get($data->datetime['end_date'], 6, \Altum\Date::$default_timezone) ?>
                        <?php endif ?>
                    </span>
                    <i class="fas fa-fw fa-caret-down d-none d-lg-inline-block ml-lg-1"></i>
                </button>
            </div>

            <?= include_view(THEME_PATH . 'views/links/link_dropdown_button.php', ['id' => $data->link->link_id, 'link' => $data->link]) ?>
        </div>
    </div>

    <p class="text-truncate">
        <a href="<?= $data->link->full_url ?>" target="_blank">
            <i class="fas fa-fw fa-sm fa-external-link-alt text-muted mr-1"></i> <?= remove_url_protocol_from_url($data->link->full_url) ?>
        </a>
    </p>

    <div class="row row-cols-2 row-cols-md-3 row-cols-xl-6 mx-lg-n2 mb-3">
        <div class="p-1 p-lg-2 text-truncate">
            <a class="btn btn-block btn-custom text-truncate <?= $data->type == 'overview' ? 'active' : null ?>" href="<?= url('link-statistics/' . $data->link->link_id . '?type=overview&start_date=' . $data->datetime['start_date'] . '&end_date=' . $data->datetime['end_date']) ?>">
                <i class="fas fa-fw fa-sm fa-list mr-1"></i>
                <?= l('link_statistics.overview') ?>
            </a>
        </div>

        <div class="p-1 p-lg-2 text-truncate">
            <a class="btn btn-block btn-custom text-truncate <?= $data->type == 'entries' ? 'active' : null ?>" href="<?= url('link-statistics/' . $data->link->link_id . '?type=entries&start_date=' . $data->datetime['start_date'] . '&end_date=' . $data->datetime['end_date']) ?>">
                <i class="fas fa-fw fa-sm fa-chart-bar mr-1"></i>
                <?= l('link_statistics.entries') ?>
            </a>
        </div>

        <div class="p-1 p-lg-2 text-truncate">
            <a class="btn btn-block btn-custom text-truncate <?= $data->type == 'continent_code' ? 'active' : null ?>" href="<?= url('link-statistics/' . $data->link->link_id . '?type=continent_code&start_date=' . $data->datetime['start_date'] . '&end_date=' . $data->datetime['end_date']) ?>">
                <i class="fas fa-fw fa-sm fa-globe-europe mr-1"></i>
                <?= l('global.continent') ?>
            </a>
        </div>

        <div class="p-1 p-lg-2 text-truncate">
            <a class="btn btn-block btn-custom text-truncate <?= $data->type == 'country' ? 'active' : null ?>" href="<?= url('link-statistics/' . $data->link->link_id . '?type=country&start_date=' . $data->datetime['start_date'] . '&end_date=' . $data->datetime['end_date']) ?>">
                <i class="fas fa-fw fa-sm fa-flag mr-1"></i>
                <?= l('global.countries') ?>
            </a>
        </div>

        <div class="p-1 p-lg-2 text-truncate">
            <a class="btn btn-block btn-custom text-truncate <?= $data->type == 'city_name' ? 'active' : null ?>" href="<?= url('link-statistics/' . $data->link->link_id . '?type=city_name&start_date=' . $data->datetime['start_date'] . '&end_date=' . $data->datetime['end_date']) ?>">
                <i class="fas fa-fw fa-sm fa-city mr-1"></i>
                <?= l('global.cities') ?>
            </a>
        </div>

        <div class="p-1 p-lg-2 text-truncate">
            <a class="btn btn-block btn-custom text-truncate <?= in_array($data->type, ['referrer_host', 'referrer_path']) ? 'active' : null ?>" href="<?= url('link-statistics/' . $data->link->link_id . '?type=referrer_host&start_date=' . $data->datetime['start_date'] . '&end_date=' . $data->datetime['end_date']) ?>">
                <i class="fas fa-fw fa-sm fa-random mr-1"></i>
                <?= l('link_statistics.referrer_host') ?>
            </a>
        </div>

        <div class="p-1 p-lg-2 text-truncate">
            <a class="btn btn-block btn-custom text-truncate <?= $data->type == 'device' ? 'active' : null ?>" href="<?= url('link-statistics/' . $data->link->link_id . '?type=device&start_date=' . $data->datetime['start_date'] . '&end_date=' . $data->datetime['end_date']) ?>">
                <i class="fas fa-fw fa-sm fa-laptop mr-1"></i>
                <?= l('link_statistics.device') ?>
            </a>
        </div>

        <div class="p-1 p-lg-2 text-truncate">
            <a class="btn btn-block btn-custom text-truncate <?= $data->type == 'os' ? 'active' : null ?>" href="<?= url('link-statistics/' . $data->link->link_id . '?type=os&start_date=' . $data->datetime['start_date'] . '&end_date=' . $data->datetime['end_date']) ?>">
                <i class="fas fa-fw fa-sm fa-server mr-1"></i>
                <?= l('link_statistics.os') ?>
            </a>
        </div>

        <div class="p-1 p-lg-2 text-truncate">
            <a class="btn btn-block btn-custom text-truncate <?= $data->type == 'browser' ? 'active' : null ?>" href="<?= url('link-statistics/' . $data->link->link_id . '?type=browser&start_date=' . $data->datetime['start_date'] . '&end_date=' . $data->datetime['end_date']) ?>">
                <i class="fas fa-fw fa-sm fa-window-restore mr-1"></i>
                <?= l('link_statistics.browser') ?>
            </a>
        </div>

        <div class="p-1 p-lg-2 text-truncate">
            <a class="btn btn-block btn-custom text-truncate <?= $data->type == 'language' ? 'active' : null ?>" href="<?= url('link-statistics/' . $data->link->link_id . '?type=language&start_date=' . $data->datetime['start_date'] . '&end_date=' . $data->datetime['end_date']) ?>">
                <i class="fas fa-fw fa-sm fa-language mr-1"></i>
                <?= l('link_statistics.language') ?>
            </a>
        </div>

        <div class="p-1 p-lg-2 text-truncate">
            <a class="btn btn-block btn-custom text-truncate <?= in_array($data->type, ['utm_source', 'utm_medium', 'utm_campaign']) ? 'active' : null ?>" href="<?= url('link-statistics/' . $data->link->link_id . '?type=utm_source&start_date=' . $data->datetime['start_date'] . '&end_date=' . $data->datetime['end_date']) ?>">
                <i class="fas fa-fw fa-sm fa-link mr-1"></i>
                <?= l('link_statistics.utms') ?>
            </a>
        </div>

        <div class="p-1 p-lg-2 text-truncate">
            <a class="btn btn-block btn-custom text-truncate <?= $data->type == 'hour' ? 'active' : null ?>" href="<?= url('link-statistics/' . $data->link->link_id . '?type=hour&start_date=' . $data->datetime['start_date'] . '&end_date=' . $data->datetime['end_date']) ?>">
                <i class="fas fa-fw fa-sm fa-clock mr-1"></i>
                <?= l('link_statistics.hour') ?>
            </a>
        </div>
    </div>

    <?php if(!$data->has_data): ?>

        <?= include_view(THEME_PATH . 'views/partials/no_data.php', [
            'filters_get' => $data->filters->get ?? [],
            'name' => 'link_statistics',
            'has_secondary_text' => true,
        ]); ?>

    <?php else: ?>

        <?= $this->views['statistics'] ?>

    <?php endif ?>

    <?php ob_start() ?>
    <link href="<?= ASSETS_FULL_URL . 'css/libraries/daterangepicker.min.css?v=' . PRODUCT_CODE ?>" rel="stylesheet" media="screen,print">
    <?php \Altum\Event::add_content(ob_get_clean(), 'head') ?>

    <?php ob_start() ?>
    <script src="<?= ASSETS_FULL_URL . 'js/libraries/moment.min.js?v=' . PRODUCT_CODE ?>"></script>
    <script src="<?= ASSETS_FULL_URL . 'js/libraries/daterangepicker.min.js?v=' . PRODUCT_CODE ?>"></script>
    <script src="<?= ASSETS_FULL_URL . 'js/libraries/moment-timezone-with-data-10-year-range.min.js?v=' . PRODUCT_CODE ?>"></script>

    <script>
        'use strict';

        moment.tz.setDefault(<?= json_encode($this->user->timezone) ?>);

        /* Daterangepicker */
        $('#daterangepicker').daterangepicker({
            startDate: <?= json_encode($data->datetime['start_date']) ?>,
            endDate: <?= json_encode($data->datetime['end_date']) ?>,
            minDate: $('#daterangepicker').data('min-date'),
            maxDate: $('#daterangepicker').data('max-date'),
            ranges: {
                <?= json_encode(l('global.date.today')) ?>: [moment(), moment()],
                <?= json_encode(l('global.date.yesterday')) ?>: [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                <?= json_encode(l('global.date.last_7_days')) ?>: [moment().subtract(6, 'days'), moment()],
                <?= json_encode(l('global.date.last_30_days')) ?>: [moment().subtract(29, 'days'), moment()],
                <?= json_encode(l('global.date.this_month')) ?>: [moment().startOf('month'), moment().endOf('month')],
                <?= json_encode(l('global.date.last_month')) ?>: [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                <?= json_encode(l('global.date.all_time')) ?>: [moment($('#daterangepicker').data('min-date')), moment()]
            },
            alwaysShowCalendars: true,
            linkedCalendars: false,
            singleCalendar: true,
            locale: <?= json_encode(require APP_PATH . 'includes/daterangepicker_translations.php') ?>,
        }, (start, end, label) => {

            <?php
            parse_str(\Altum\Router::$original_request_query, $original_request_query_array);
            $modified_request_query_array = array_diff_key($original_request_query_array, ['start_date' => '', 'end_date' => '']);
            ?>

            /* Redirect */
            redirect(`<?= url(\Altum\Router::$original_request . '?' . http_build_query($modified_request_query_array)) ?>&start_date=${start.format('YYYY-MM-DD')}&end_date=${end.format('YYYY-MM-DD')}`, true);

        });
    </script>
    <?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
</div>

<?php include_view(THEME_PATH . 'views/partials/clipboard_js.php') ?>

<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/links/link_delete_modal.php'), 'modals'); ?>

<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/partials/statistics_reset_modal.php', ['modal_id' => 'link_statistics_reset_modal', 'resource_id' => 'link_id', 'path' => 'link-statistics/reset']), 'modals'); ?>
