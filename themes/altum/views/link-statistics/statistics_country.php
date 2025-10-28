<?php defined('ALTUMCODE') || die() ?>

<?php if(count($data->rows)): ?>
    <div class="card my-3">
        <div class="card-body">
            <div id="countries_map"></div>
        </div>
    </div>
<?php endif ?>

<div class="card my-3">
    <div class="card-body">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="d-flex align-items-center">
                <h3 class="h5 text-truncate m-0"><?= isset($_GET['continent_code']) ? sprintf(l('link_statistics.country_from_continent_code'), get_continent_from_continent_code($data->continent_code)) : l('global.countries') ?></h3>

                <div class="ml-2">
                    <span data-toggle="tooltip" title="<?= l('link_statistics.country_help') ?>">
                        <i class="fas fa-fw fa-info-circle text-muted"></i>
                    </span>
                </div>
            </div>

            <div class="d-flex align-items-center col-auto p-0">
                <div class="dropdown">
                    <button type="button" class="btn btn-light dropdown-toggle-simple" data-toggle="dropdown" data-boundary="viewport" data-tooltip title="<?= l('global.export') ?>" data-tooltip-hide-on-click>
                        <i class="fas fa-fw fa-sm fa-download"></i>
                    </button>

                    <div class="dropdown-menu dropdown-menu-right d-print-none">
                        <a href="<?= url('link-statistics/' . $data->link->link_id . '?' . \Altum\Router::$original_request_query . '&export=csv') ?>" target="_blank" class="dropdown-item">
                            <i class="fas fa-fw fa-sm fa-file-csv mr-2"></i> <?= sprintf(l('global.export_to'), 'CSV') ?>
                        </a>
                        <a href="<?= url('link-statistics/' . $data->link->link_id . '?' . \Altum\Router::$original_request_query . '&export=json') ?>" target="_blank" class="dropdown-item <?= $this->user->plan_settings->export->json ? null : 'disabled pointer-events-all' ?>" <?= $this->user->plan_settings->export->json ? null : get_plan_feature_disabled_info() ?>>
                            <i class="fas fa-fw fa-sm fa-file-code mr-2"></i> <?= sprintf(l('global.export_to'), 'JSON') ?>
                    </a>
                    <a href="#" class="dropdown-item <?= $this->user->plan_settings->export->pdf ? null : 'disabled pointer-events-all' ?>" <?= $this->user->plan_settings->export->pdf ? $this->user->plan_settings->export->pdf ? 'onclick="event.preventDefault(); window.print();"' : 'disabled pointer-events-all' : get_plan_feature_disabled_info() ?>>
                        <i class="fas fa-fw fa-sm fa-file-pdf mr-2"></i> <?= sprintf(l('global.export_to'), 'PDF') ?>
                    </a>
                    </div>
                </div>
            </div>
        </div>

        <?php if(!count($data->rows)): ?>
            <?= include_view(THEME_PATH . 'views/partials/no_data.php', [
            'filters_get' => $data->filters->get ?? [],
            'name' => 'global',
            'has_secondary_text' => false,
            'has_wrapper' => false,
        ]); ?>
        <?php else: ?>

            <?php $countries_map = [] ?>
            <?php foreach($data->rows as $row): ?>
                <?php $percentage = round($row->total / $data->total_sum * 100, 2) ?>
                <?php $countries_map[$row->country_code] = ['pageviews' => $row->total]; ?>

                <div class="mt-4">
                    <div class="d-flex justify-content-between mb-1">
                        <div class="text-truncate">
                            <img src="<?= ASSETS_FULL_URL . 'images/countries/' . ($row->country_code ? mb_strtolower($row->country_code) : 'unknown') . '.svg' ?>" class="img-fluid icon-favicon mr-1" />
                            <?php if($row->country_code): ?>
                                <a href="<?= url('link-statistics/' . $data->link->link_id . '?type=city_name&country_code=' . $row->country_code . '&start_date=' . $data->datetime['start_date'] . '&end_date=' . $data->datetime['end_date']) ?>" title="<?= $row->country_code ?>" class=""><?= get_country_from_country_code($row->country_code) ?></a>
                            <?php else: ?>
                                <span class=""><?= $row->country_code ? get_country_from_country_code($row->country_code) : l('global.unknown') ?></span>
                            <?php endif ?>
                        </div>

                        <div>
                            <small class="text-muted"><?= nr($percentage, 2, false) . '%' ?></small>
                            <span class="ml-3"><?= nr($row->total) ?></span>
                        </div>
                    </div>

                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar" role="progressbar" style="width: <?= $percentage ?>%;" aria-valuenow="<?= $percentage ?>" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            <?php endforeach ?>

        <?php endif ?>
    </div>
</div>

<?php ob_start() ?>
    <link href="<?= ASSETS_FULL_URL . 'css/libraries/svgMap.min.css?v=' . PRODUCT_CODE ?>" rel="stylesheet" media="screen">
<?php \Altum\Event::add_content(ob_get_clean(), 'head') ?>

<?php ob_start() ?>
    <script src="<?= ASSETS_FULL_URL . 'js/libraries/svgMap.min.js?v=' . PRODUCT_CODE ?>"></script>

    <script>
        'use strict';

let hsl_to_hex = (hsl_string) => {
        /* Extract values */
        const match = hsl_string.match(/hsl\(\s*(\d+),\s*(\d+)%?,\s*(\d+)%?\s*\)/);
        if (!match) return null;
        let hue = parseInt(match[1], 10);
        let saturation = parseInt(match[2], 10) / 100;
        let lightness = parseInt(match[3], 10) / 100;

        /* Convert to rgb */
        let chroma = (1 - Math.abs(2 * lightness - 1)) * saturation;
        let x = chroma * (1 - Math.abs((hue / 60) % 2 - 1));
        let m = lightness - chroma / 2;
        let red1 = 0, green1 = 0, blue1 = 0;

        if (hue < 60) { red1 = chroma; green1 = x; blue1 = 0; }
        else if (hue < 120) { red1 = x; green1 = chroma; blue1 = 0; }
        else if (hue < 180) { red1 = 0; green1 = chroma; blue1 = x; }
        else if (hue < 240) { red1 = 0; green1 = x; blue1 = chroma; }
        else if (hue < 300) { red1 = x; green1 = 0; blue1 = chroma; }
        else { red1 = chroma; green1 = 0; blue1 = x; }

        let red = Math.round((red1 + m) * 255);
        let green = Math.round((green1 + m) * 255);
        let blue = Math.round((blue1 + m) * 255);

        /* Convert to hex */
        return '#' + [red, green, blue].map(x =>
            x.toString(16).padStart(2, '0')
        ).join('');
    }

        let css = window.getComputedStyle(document.body)

        /* Create the map */
        new svgMap({
            targetElementID: 'countries_map',
            data: {
                data: {
                    pageviews: {
                        name: '',
                        format: '{0} <?= l('link_statistics.pageviews') ?>',
                        thousandSeparator: thousands_separator,
                    },
                },
                applyData: 'pageviews',
                values: <?= json_encode($countries_map) ?>,
            },
            colorMin: css.getPropertyValue('--primary-100'),
            colorMax: css.getPropertyValue('--primary-800'),
            colorNoData: css.getPropertyValue('--gray-200'),
            flagType: 'emoji',
            noDataText: <?= json_encode(l('global.no_data')) ?>
        });
    </script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
