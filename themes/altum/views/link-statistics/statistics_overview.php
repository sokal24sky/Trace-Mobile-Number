<?php defined('ALTUMCODE') || die() ?>

<div class="mb-4">
    <div class="row m-n2">
        <div class="col-12 col-sm-6 p-2 position-relative text-truncate">
            <div class="card d-flex flex-row h-100 overflow-hidden">
                <div class="pl-3 d-flex flex-column justify-content-center">
                    <div class="p-2 rounded-2x index-widget-icon d-flex align-items-center justify-content-center bg-gray-100">
                        <i class="fas fa-fw fa-sm fa-eye"></i>
                    </div>
                </div>

                <div class="card-body text-truncate">
                    <span class="h6"><?= nr($data->totals['pageviews']) . ' ' . l('link_statistics.pageviews') ?></span>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 p-2 position-relative text-truncate">
            <div class="card d-flex flex-row h-100 overflow-hidden">
                <div class="pl-3 d-flex flex-column justify-content-center">
                    <div class="p-2 rounded-2x index-widget-icon d-flex align-items-center justify-content-center bg-gray-100">
                        <i class="fas fa-fw fa-sm fa-users"></i>
                    </div>
                </div>

                <div class="card-body text-truncate">
                    <span class="h6"><?= nr($data->totals['visitors']) . ' ' . l('link_statistics.visitors') ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card mt-3 mb-5">
    <div class="card-body">
        <div class="chart-container">
            <canvas id="pageviews_chart"></canvas>
        </div>
    </div>
</div>

<div class="d-flex align-items-center">
    <h2 class="small font-weight-bold text-uppercase text-muted mb-0 mr-3" data-toggle="tooltip" title="<?= sprintf(l('link_statistics.data_preview_info'), $this->user->preferences->default_results_per_page ?? settings()->main->default_results_per_page) ?>">
        <i class="fas fa-fw fa-sm fa-info-circle mr-1"></i> <?= l('link_statistics.data_preview') ?>
    </h2>

    <div class="flex-fill">
        <hr class="border-gray-100" />
    </div>
</div>

<div class="row mb-4">
    <div class="col-12 col-lg-6 my-3">
        <div class="card h-100">
            <div class="card-body">
                <h3 class="h5"><?= l('global.continents') ?></h3>
                <p></p>

                <?php $i = 0; foreach($data->statistics['continent_code'] as $key => $value): $i++; if($i > 5) break; ?>
                    <?php $percentage = round($value / $data->statistics['continent_code_total_sum'] * 100, 1) ?>

                    <div class="mt-4">
                        <div class="d-flex justify-content-between mb-1">
                            <div class="text-truncate">
                                <?php if($key): ?>
                                    <a href="<?= url('link-statistics/' . $data->link->link_id . '?type=country&continent_code=' . $key . '&start_date=' . $data->datetime['start_date'] . '&end_date=' . $data->datetime['end_date']) ?>" title="<?= $key ?>" class="">
                                        <?= get_continent_from_continent_code($key) ?>
                                    </a>
                                <?php else: ?>
                                    <span class=""><?= $key ? get_continent_from_continent_code($key) : l('global.unknown') ?></span>
                                <?php endif ?>
                            </div>

                            <div>
                                <small class="text-muted"><?= nr($percentage, 2, false) . '%' ?></small>
                                <span class="ml-3"><?= nr($value) ?></span>
                            </div>
                        </div>

                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar" role="progressbar" style="width: <?= $percentage ?>%;" aria-valuenow="<?= $percentage ?>" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>

            <div class="card-body small py-3 d-flex align-items-end">
                <a href="<?= url('link-statistics/' . $data->link->link_id . '?type=continent_code&start_date=' . $data->datetime['start_date'] . '&end_date=' . $data->datetime['end_date']) ?>" class="text-muted text-decoration-none"><i class="fas fa-angle-right fa-sm fa-fw mr-1"></i> <?= l('global.view_more') ?></a>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-6 my-3">
        <div class="card h-100">
            <div class="card-body">
                <h3 class="h5"><?= l('global.countries') ?></h3>
                <p></p>

                <?php $i = 0; foreach($data->statistics['country_code'] as $key => $value): $i++; if($i > 5) break; ?>
                    <?php $percentage = round($value / $data->statistics['country_code_total_sum'] * 100, 1) ?>

                    <div class="mt-4">
                        <div class="d-flex justify-content-between mb-1">
                            <div class="text-truncate">
                                <img src="<?= ASSETS_FULL_URL . 'images/countries/' . ($key ? mb_strtolower($key) : 'unknown') . '.svg' ?>" class="img-fluid icon-favicon mr-1" />
                                <?php if($key): ?>
                                    <a href="<?= url('link-statistics/' . $data->link->link_id . '?type=city_name&country_code=' . $key . '&start_date=' . $data->datetime['start_date'] . '&end_date=' . $data->datetime['end_date']) ?>" title="<?= $key ?>" class=""><?= get_country_from_country_code($key) ?></a>
                                <?php else: ?>
                                    <span class=""><?= $key ? get_country_from_country_code($key) : l('global.unknown') ?></span>
                                <?php endif ?>
                            </div>

                            <div>
                                <small class="text-muted"><?= nr($percentage, 2, false) . '%' ?></small>
                                <span class="ml-3"><?= nr($value) ?></span>
                            </div>
                        </div>

                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar" role="progressbar" style="width: <?= $percentage ?>%;" aria-valuenow="<?= $percentage ?>" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>

            <div class="card-body small py-3 d-flex align-items-end">
                <a href="<?= url('link-statistics/' . $data->link->link_id . '?type=country&start_date=' . $data->datetime['start_date'] . '&end_date=' . $data->datetime['end_date']) ?>" class="text-muted text-decoration-none"><i class="fas fa-angle-right fa-sm fa-fw mr-1"></i> <?= l('global.view_more') ?></a>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-6 my-3">
        <div class="card h-100">
            <div class="card-body">
                <h3 class="h5"><?= l('global.cities') ?></h3>
                <p></p>

                <?php $i = 0; foreach($data->statistics['city_name'] as $key => $value): $i++; if($i > 5) break; ?>
                    <?php $percentage = round($value / $data->statistics['city_name_total_sum'] * 100, 1) ?>

                    <div class="mt-4">
                        <div class="d-flex justify-content-between mb-1">
                            <div class="text-truncate">
                                <span class=""><?= $key ? $key : l('global.unknown') ?></span>
                            </div>

                            <div>
                                <small class="text-muted"><?= nr($percentage, 2, false) . '%' ?></small>
                                <span class="ml-3"><?= nr($value) ?></span>
                            </div>
                        </div>

                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar" role="progressbar" style="width: <?= $percentage ?>%;" aria-valuenow="<?= $percentage ?>" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>

            <div class="card-body small py-3 d-flex align-items-end">
                <a href="<?= url('link-statistics/' . $data->link->link_id . '?type=city_name&start_date=' . $data->datetime['start_date'] . '&end_date=' . $data->datetime['end_date']) ?>" class="text-muted text-decoration-none"><i class="fas fa-angle-right fa-sm fa-fw mr-1"></i> <?= l('global.view_more') ?></a>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-6 my-3">
        <div class="card h-100">
            <div class="card-body">
                <h3 class="h5"><?= l('link_statistics.referrer_host') ?></h3>
                <p></p>

                <?php $i = 0; foreach($data->statistics['referrer_host'] as $key => $value): $i++; if($i > 5) break; ?>
                    <?php $percentage = round($value / $data->statistics['referrer_host_total_sum'] * 100, 1) ?>

                    <div class="mt-4">
                        <div class="d-flex justify-content-between mb-1">
                            <div class="text-truncate">
                                <?php if(!$key): ?>
                                    <span><?= l('link_statistics.referrer_direct') ?></span>
                                <?php elseif($key == 'qr'): ?>
                                    <span><?= l('link_statistics.referrer_qr') ?></span>
                                <?php else: ?>
                                    <img referrerpolicy="no-referrer" src="<?= get_favicon_url_from_domain($key) ?>" class="img-fluid icon-favicon mr-1" loading="lazy" />
                                    <a href="<?= url('link-statistics/' . $data->link->link_id . '?type=referrer_path&referrer_host=' . $key . '&start_date=' . $data->datetime['start_date'] . '&end_date=' . $data->datetime['end_date']) ?>" title="<?= $key ?>" class=""><?= $key ?></a>
                                    <a href="<?= 'https://' . $key ?>" target="_blank" rel="nofollow noopener" class="text-muted ml-1"><i class="fas fa-fw fa-xs fa-external-link-alt"></i></a>
                                <?php endif ?>
                            </div>

                            <div>
                                <small class="text-muted"><?= nr($percentage, 2, false) . '%' ?></small>
                                <span class="ml-3"><?= nr($value) ?></span>
                            </div>
                        </div>

                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar" role="progressbar" style="width: <?= $percentage ?>%;" aria-valuenow="<?= $percentage ?>" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>

            <div class="card-body small py-3 d-flex align-items-end">
                <a href="<?= url('link-statistics/' . $data->link->link_id . '?type=referrer_host&start_date=' . $data->datetime['start_date'] . '&end_date=' . $data->datetime['end_date']) ?>" class="text-muted text-decoration-none"><i class="fas fa-angle-right fa-sm fa-fw mr-1"></i> <?= l('global.view_more') ?></a>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-6 my-3">
        <div class="card h-100">
            <div class="card-body">
                <h3 class="h5"><?= l('link_statistics.device') ?></h3>
                <p></p>

                <?php $i = 0; foreach($data->statistics['device_type'] as $key => $value): $i++; if($i > 5) break; ?>
                    <?php $percentage = round($value / $data->statistics['device_type_total_sum'] * 100, 1) ?>

                    <div class="mt-4">
                        <div class="d-flex justify-content-between mb-1">
                            <div class="text-truncate">
                                <?php if(!$key): ?>
                                    <span><?= l('global.unknown') ?></span>
                                <?php else: ?>
                                    <span><i class="fas fa-fw fa-sm fa-<?= $key ?> text-muted mr-1"></i> <?= l('global.device.' . $key) ?></span>
                                <?php endif ?>
                            </div>

                            <div>
                                <small class="text-muted"><?= nr($percentage, 2, false) . '%' ?></small>
                                <span class="ml-3"><?= nr($value) ?></span>
                            </div>
                        </div>

                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar" role="progressbar" style="width: <?= $percentage ?>%;" aria-valuenow="<?= $percentage ?>" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>

            <div class="card-body small py-3 d-flex align-items-end">
                <a href="<?= url('link-statistics/' . $data->link->link_id . '?type=device&start_date=' . $data->datetime['start_date'] . '&end_date=' . $data->datetime['end_date']) ?>" class="text-muted text-decoration-none"><i class="fas fa-angle-right fa-sm fa-fw mr-1"></i> <?= l('global.view_more') ?></a>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-6 my-3">
        <div class="card h-100">
            <div class="card-body">
                <h3 class="h5"><?= l('link_statistics.os') ?></h3>
                <p></p>

                <?php $i = 0; foreach($data->statistics['os_name'] as $key => $value): $i++; if($i > 5) break; ?>
                    <?php $percentage = round($value / $data->statistics['os_name_total_sum'] * 100, 1) ?>

                    <div class="mt-4">
                        <div class="d-flex justify-content-between mb-1">
                            <div class="text-truncate">
                                <img src="<?= ASSETS_FULL_URL . 'images/os/' . os_name_to_os_key($key) . '.svg' ?>" class="img-fluid icon-favicon mr-1" />
                                <span class=""><?= $key ?:  l('global.unknown') ?></span>
                            </div>

                            <div>
                                <small class="text-muted"><?= nr($percentage, 2, false) . '%' ?></small>
                                <span class="ml-3"><?= nr($value) ?></span>
                            </div>
                        </div>

                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar" role="progressbar" style="width: <?= $percentage ?>%;" aria-valuenow="<?= $percentage ?>" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>

            <div class="card-body small py-3 d-flex align-items-end">
                <a href="<?= url('link-statistics/' . $data->link->link_id . '?type=os&start_date=' . $data->datetime['start_date'] . '&end_date=' . $data->datetime['end_date']) ?>" class="text-muted text-decoration-none"><i class="fas fa-angle-right fa-sm fa-fw mr-1"></i> <?= l('global.view_more') ?></a>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-6 my-3">
        <div class="card h-100">
            <div class="card-body">
                <h3 class="h5"><?= l('link_statistics.browser') ?></h3>
                <p></p>

                <?php $i = 0; foreach($data->statistics['browser_name'] as $key => $value): $i++; if($i > 5) break; ?>
                    <?php $percentage = round($value / $data->statistics['browser_name_total_sum'] * 100, 1) ?>

                    <div class="mt-4">
                        <div class="d-flex justify-content-between mb-1">
                            <div class="text-truncate">
                                <img src="<?= ASSETS_FULL_URL . 'images/browsers/' . browser_name_to_browser_key($key) . '.svg' ?>" class="img-fluid icon-favicon mr-1" />
                                <span class=""><?= $key ?:  l('global.unknown') ?></span>
                            </div>

                            <div>
                                <small class="text-muted"><?= nr($percentage, 2, false) . '%' ?></small>
                                <span class="ml-3"><?= nr($value) ?></span>
                            </div>
                        </div>

                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar" role="progressbar" style="width: <?= $percentage ?>%;" aria-valuenow="<?= $percentage ?>" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>

            <div class="card-body small py-3 d-flex align-items-end">
                <a href="<?= url('link-statistics/' . $data->link->link_id . '?type=browser&start_date=' . $data->datetime['start_date'] . '&end_date=' . $data->datetime['end_date']) ?>" class="text-muted text-decoration-none"><i class="fas fa-angle-right fa-sm fa-fw mr-1"></i> <?= l('global.view_more') ?></a>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-6 my-3">
        <div class="card h-100">
            <div class="card-body">
                <h3 class="h5"><?= l('link_statistics.language') ?></h3>
                <p></p>

                <?php $i = 0; foreach($data->statistics['browser_language'] as $key => $value): $i++; if($i > 5) break; ?>
                    <?php $percentage = round($value / $data->statistics['browser_language_total_sum'] * 100, 1) ?>

                    <div class="mt-4">
                        <div class="d-flex justify-content-between mb-1">
                            <div class="text-truncate">
                                <?php if(!$key): ?>
                                    <span><?= l('global.unknown') ?></span>
                                <?php else: ?>
                                    <span><?= get_language_from_locale($key) ?></span>
                                <?php endif ?>
                            </div>

                            <div>
                                <small class="text-muted"><?= nr($percentage, 2, false) . '%' ?></small>
                                <span class="ml-3"><?= nr($value) ?></span>
                            </div>
                        </div>

                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar" role="progressbar" style="width: <?= $percentage ?>%;" aria-valuenow="<?= $percentage ?>" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>

            <div class="card-body small py-3 d-flex align-items-end">
                <a href="<?= url('link-statistics/' . $data->link->link_id . '?type=language&start_date=' . $data->datetime['start_date'] . '&end_date=' . $data->datetime['end_date']) ?>" class="text-muted text-decoration-none"><i class="fas fa-angle-right fa-sm fa-fw mr-1"></i> <?= l('global.view_more') ?></a>
            </div>
        </div>
    </div>
</div>

<div>
    <div class="d-flex align-items-center mb-3">
        <h2 class="small font-weight-bold text-uppercase text-muted mb-0 mr-3"><i class="fas fa-fw fa-sm fa-chart-bar mr-1"></i> <?= l('link_statistics.latest') ?></h2>

        <div class="flex-fill">
            <hr class="border-gray-100" />
        </div>
    </div>

    <div class="table-responsive table-custom-container">
        <table class="table table-custom">
            <thead>
            <tr>
                <th class="">
                    <div><?= l('global.country') ?></div>
                    <div><?= l('global.city') ?></div>
                </th>
                <th class=""><?= l('link_statistics.table.device') ?></th>
                <th class="">
                    <div><?= l('link_statistics.table.os') ?></div>
                    <div><?= l('link_statistics.table.browser') ?></div>
                </th>
                <th class=""><?= l('link_statistics.table.referrer') ?></th>
                <th class=""><?= l('global.datetime') ?></th>
            </tr>
            </thead>

            <tbody>

            <?php $i = 1; ?>
            <?php foreach($data->latest as $row): ?>
                <?php if($i++ > 10) break ?>
                <tr>
                    <td class="text-nowrap">
                        <div class="d-flex align-items-center">
                            <div class="table-image-wrapper mr-3">
                                <img src="<?= ASSETS_FULL_URL . 'images/countries/' . ($row->country_code ? mb_strtolower($row->country_code) : 'unknown') . '.svg' ?>" class="img-fluid icon-favicon" />
                            </div>

                            <div class="d-flex flex-column">
                                <span class=""><?= $row->country_code ? get_country_from_country_code($row->country_code) : l('global.unknown') ?></span>
                                <span class="text-muted small"><?= $row->city_name ?? l('global.unknown') ?></span>
                            </div>
                        </div>
                    </td>

                    <td class="text-nowrap">
                        <span class="badge badge-light">
                            <?= $row->device_type ? '<i class="fas fa-fw fa-sm fa-' . $row->device_type . ' mr-1"></i>' . l('global.device.' . $row->device_type) : l('global.unknown') ?>
                        </span>
                    </td>

                    <td class="text-nowrap">
                        <div>
                            <img src="<?= ASSETS_FULL_URL . 'images/os/' . os_name_to_os_key($row->os_name) . '.svg' ?>" class="img-fluid icon-favicon-small mr-1" />
                            <span class="font-size-small"><?= $row->os_name ?: l('global.unknown') ?></span>
                        </div>
                        <div>
                            <img src="<?= ASSETS_FULL_URL . 'images/browsers/' . browser_name_to_browser_key($row->browser_name) . '.svg' ?>" class="img-fluid icon-favicon-small mr-1" />
                            <span class="font-size-small"><?= $row->browser_name ?: l('global.unknown') ?></span>
                        </div>
                    </td>

                    <td class="text-nowrap">
                        <?php if(!$row->referrer_host): ?>
                            <span><?= l('link_statistics.referrer_direct') ?></span>
                        <?php elseif($row->referrer_host == 'qr'): ?>
                            <span><?= l('link_statistics.referrer_qr') ?></span>
                        <?php else: ?>
                            <img referrerpolicy="no-referrer" src="<?= get_favicon_url_from_domain($row->referrer_host) ?>" class="img-fluid icon-favicon mr-1" loading="lazy" />
                            <a href="<?= url('link-statistics/' . $data->link->link_id . '?type=referrer_path&referrer_host=' . $row->referrer_host . '&start_date=' . $data->datetime['start_date'] . '&end_date=' . $data->datetime['end_date']) ?>" title="<?= $row->referrer_host ?>" class=""><?= $row->referrer_host ?></a>
                            <a href="<?= 'https://' . $row->referrer_host ?>" target="_blank" rel="nofollow noopener" class="text-muted ml-1"><i class="fas fa-fw fa-xs fa-external-link-alt"></i></a>
                        <?php endif ?>
                    </td>

                    <td class="text-nowrap">
                        <span class="text-muted" data-toggle="tooltip" title="<?= \Altum\Date::get($row->datetime, 1) ?>"><?= \Altum\Date::get_timeago($row->datetime) ?></span>
                    </td>
                </tr>
            <?php endforeach ?>

            <tr>
                <td colspan="7">
                    <a href="<?= url('link-statistics/' . $data->link->link_id . '?type=entries&start_date=' . $data->datetime['start_date'] . '&end_date=' . $data->datetime['end_date']) ?>" class="text-muted text-decoration-none small">
                        <i class="fas fa-angle-right fa-sm fa-fw mr-1"></i> <?= l('global.view_more') ?>
                    </a>
                </td>
            </tr>

            </tbody>
        </table>
    </div>
</div>

<?php require THEME_PATH . 'views/partials/js_chart_defaults.php' ?>

<?php ob_start() ?>

    <script>
        'use strict';

        <?php if(count($data->pageviews)): ?>
        let css = window.getComputedStyle(document.body);
        let pageviews_color = css.getPropertyValue('--primary');
        let visitors_color = css.getPropertyValue('--gray-400');
        let pageviews_color_gradient = null;
        let visitors_color_gradient = null;

        /* Chart */
        let pageviews_chart = document.getElementById('pageviews_chart').getContext('2d');

        /* Colors */
        pageviews_color_gradient = pageviews_chart.createLinearGradient(0, 0, 0, 250);
        pageviews_color_gradient.addColorStop(0, set_hex_opacity(pageviews_color, 0.6));
        pageviews_color_gradient.addColorStop(1, set_hex_opacity(pageviews_color, 0.1));

        visitors_color_gradient = pageviews_chart.createLinearGradient(0, 0, 0, 250);
        visitors_color_gradient.addColorStop(0, set_hex_opacity(visitors_color, 0.6));
        visitors_color_gradient.addColorStop(1, set_hex_opacity(visitors_color, 0.1));

        /* Display chart */
        new Chart(pageviews_chart, {
            type: 'line',
            data: {
                labels: <?= $data->pageviews_chart['labels'] ?>,
                datasets: [
                    {
                        label: <?= json_encode(l('link_statistics.pageviews')) ?>,
                        data: <?= $data->pageviews_chart['pageviews'] ?? '[]' ?>,
                        backgroundColor: pageviews_color_gradient,
                        borderColor: pageviews_color,
                        fill: true
                    },
                    {
                        label: <?= json_encode(l('link_statistics.visitors')) ?>,
                        data: <?= $data->pageviews_chart['visitors'] ?? '[]' ?>,
                        backgroundColor: visitors_color_gradient,
                        borderColor: visitors_color,
                        fill: true
                    }
                ]
            },
            options: chart_options
        });

        <?php endif ?>
    </script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
