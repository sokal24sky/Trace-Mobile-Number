<?php defined('ALTUMCODE') || die() ?>

<?php ob_start() ?>
<div class="card mb-5">
    <div class="card-body">
        <div id="countries_map"></div>
    </div>
</div>

<div class="card mb-5">
    <div class="card-body">
        <h2 class="h4 mb-4"><i class="fas fa-fw fa-globe-europe fa-xs text-primary-900 mr-2"></i> <?= l('global.continents') ?></h2>

        <div class="table-responsive table-custom-container">
            <table class="table table-custom">
                <thead>
                <tr>
                    <th><?= l('global.continent') ?></th>
                    <th><?= l('admin_statistics.percentage') ?></th>
                    <th><?= l('admin_statistics.users') ?></th>
                </tr>
                </thead>
                <tbody>
                <?php if(count($data->continents)): ?>
                    <?php foreach ($data->continents as $continent_code => $total): ?>
                        <tr>
                            <td class="text-nowrap">
                                <?= $continent_code ? get_continent_from_continent_code($continent_code) : l('global.unknown') ?>
                            </td>
                            <td class="text-nowrap">
                                <?= nr($total / $data->total['continents'] * 100, 2) . '%' ?>
                            </td>
                            <td class="text-nowrap">
                                <?= nr($total) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td class="text-nowrap text-muted" colspan="3"><?= l('global.no_data') ?></td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card mb-5">
    <div class="card-body">
        <h2 class="h4 mb-4"><i class="fas fa-fw fa-flag fa-xs text-primary-900 mr-2"></i> <?= l('global.countries') ?></h2>

        <div class="table-responsive table-custom-container">
            <table class="table table-custom">
                <thead>
                <tr>
                    <th><?= l('global.country') ?></th>
                    <th><?= l('admin_statistics.percentage') ?></th>
                    <th><?= l('admin_statistics.users') ?></th>
                </tr>
                </thead>
                <tbody>
                <?php if(count($data->countries)): ?>
                    <?php foreach ($data->countries as $country_code => $total): ?>
                        <tr>
                            <td class="text-nowrap">
                                <?php if($country_code): ?>
                                    <img src="<?= ASSETS_FULL_URL . 'images/countries/' . mb_strtolower($country_code) . '.svg' ?>" class="icon-favicon mr-2" title="<?= get_country_from_country_code($country_code) ?>" />
                                    <?= get_country_from_country_code($country_code) ?>
                                <?php else: ?>
                                    <?= l('global.unknown') ?>
                                <?php endif; ?>
                            </td>
                            <td class="text-nowrap">
                                <?= nr($total / $data->total['countries'] * 100, 2) . '%' ?>
                            </td>
                            <td class="text-nowrap">
                                <?= nr($total) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td class="text-nowrap text-muted" colspan="3"><?= l('global.no_data') ?></td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card mb-5">
    <div class="card-body">
        <h2 class="h4 mb-4"><i class="fas fa-fw fa-city fa-xs text-primary-900 mr-2"></i> <?= l('global.cities') ?></h2>

        <div class="table-responsive table-custom-container">
            <table class="table table-custom">
                <thead>
                <tr>
                    <th><?= l('global.city') ?></th>
                    <th><?= l('admin_statistics.percentage') ?></th>
                    <th><?= l('admin_statistics.users') ?></th>
                </tr>
                </thead>
                <tbody>
                <?php if(count($data->cities)): ?>
                    <?php foreach ($data->cities as $concatenated_data => $total): ?>
                        <?php
                        $exploded = explode('#', $concatenated_data);
                        $country_code = $exploded[0];
                        $city_name = $exploded[1];
                        ?>
                        <tr>
                            <td class="text-nowrap">
                                <?php if($city_name): ?>
                                    <img src="<?= ASSETS_FULL_URL . 'images/countries/' . mb_strtolower($country_code) . '.svg' ?>" class="icon-favicon mr-2" title="<?= get_country_from_country_code($country_code) ?>" />
                                    <?= $city_name ?>
                                <?php else: ?>
                                    <?= l('global.unknown') ?>
                                <?php endif; ?>
                            </td>
                            <td class="text-nowrap">
                                <?= nr($total / $data->total['cities'] * 100, 2) . '%' ?>
                            </td>
                            <td class="text-nowrap">
                                <?= nr($total) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td class="text-nowrap text-muted" colspan="3"><?= l('global.no_data') ?></td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php $html = ob_get_clean() ?>

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
    
    /* Create the map */
    new svgMap({
        targetElementID: 'countries_map',
        data: {
            data: {
                users: {
                    name: '',
                    format: '{0} <?= l('admin_statistics.users') ?>',
                    thousandSeparator: thousands_separator,
                },
            },
            applyData: 'users',
            values: <?= json_encode($data->countries_map) ?>,
        },
        colorMin: hsl_to_hex(css.getPropertyValue('--primary-100')),
        colorMax: hsl_to_hex(css.getPropertyValue('--primary-800')),
        colorNoData: hsl_to_hex(css.getPropertyValue('--gray-200')),
        flagType: 'emoji',
        noDataText: <?= json_encode(l('global.no_data')) ?>
    });
</script>
<?php $javascript = ob_get_clean() ?>

<?php return (object) ['html' => $html, 'javascript' => $javascript] ?>
