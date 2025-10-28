<?php defined('ALTUMCODE') || die() ?>

<?php ob_start() ?>
<div class="card mb-5">
    <div class="card-body">
        <div class="d-flex justify-content-between mb-4">
            <h2 class="h4 text-truncate mb-0"><i class="fas fa-fw fa-camera-alt fa-xs text-primary-900 mr-2"></i> <?= l('admin_statistics.image_optimizer.chart.total') ?></h2>

            <div>
                <span class="badge <?= $data->total['total'] > 0 ? 'badge-success' : 'badge-secondary' ?>" data-toggle="tooltip" title="<?= l('admin_statistics.image_optimizer.chart.total') ?>">
                    <?= nr($data->total['total']) ?>
                </span>
            </div>
        </div>

        <div class="chart-container <?= $data->total['total'] ? null : 'd-none' ?>">
            <canvas id="image_optimizer_total"></canvas>
        </div>
        <?= $data->total['total'] ? null : include_view(THEME_PATH . 'views/partials/no_chart_data.php', ['has_wrapper' => false]); ?>
    </div>
</div>

<div class="card mb-5">
    <div class="card-body">
        <div class="d-flex justify-content-between mb-4">
            <h2 class="h4 text-truncate mb-0"><i class="fas fa-fw fa-hard-drive fa-xs text-primary-900 mr-2"></i> <?= l('admin_statistics.image_optimizer.chart.saved_size') ?></h2>

            <div>
                <span class="badge <?= $data->total['saved_size'] > 0 ? 'badge-success' : 'badge-secondary' ?>" data-toggle="tooltip" title="<?= l('admin_statistics.image_optimizer.chart.saved_size') ?>">
                    <?= get_formatted_bytes($data->total['saved_size']) ?>
                </span>
            </div>
        </div>

        <div class="chart-container <?= $data->total['total'] ? null : 'd-none' ?>">
            <canvas id="image_optimizer_saved_size"></canvas>
        </div>
        <?= $data->total['total'] ? null : include_view(THEME_PATH . 'views/partials/no_chart_data.php', ['has_wrapper' => false]); ?>
    </div>
</div>

<div class="card mb-5">
    <div class="card-body">
        <div class="d-flex justify-content-between mb-4">
            <h2 class="h4 text-truncate mb-0"><i class="fas fa-fw fa-save fa-xs text-primary-900 mr-2"></i> <?= l('admin_statistics.image_optimizer.chart.average_percentage_difference') ?></h2>

            <div>
                <span class="badge <?= $data->total['average_percentage_difference'] < 0 ? 'badge-success' : 'badge-secondary' ?>" data-toggle="tooltip" title="<?= l('admin_statistics.image_optimizer.chart.average_percentage_difference') ?>">
                    <?= nr($data->total['average_percentage_difference']) . '%' ?>
                </span>
            </div>
        </div>

        <div class="chart-container <?= $data->total['total'] ? null : 'd-none' ?>">
            <canvas id="image_optimizer_average_percentage_difference"></canvas>
        </div>
        <?= $data->total['total'] ? null : include_view(THEME_PATH . 'views/partials/no_chart_data.php', ['has_wrapper' => false]); ?>
    </div>
</div>
<?php $html = ob_get_clean() ?>

<?php ob_start() ?>
    <script>
        'use strict';

        let color = css.getPropertyValue('--primary');
        let color_gradient = null;

        /* Display chart */
        let total_chart = document.getElementById('image_optimizer_total').getContext('2d');

        color_gradient = total_chart.createLinearGradient(0, 0, 0, 250);
        color_gradient.addColorStop(0, set_hex_opacity(color, 0.1));
        color_gradient.addColorStop(1, set_hex_opacity(color, 0.025));

        new Chart(total_chart, {
            type: 'line',
            data: {
                labels: <?= $data->chart['labels'] ?>,
                datasets: [
                    {
                        label: <?= json_encode(l('admin_statistics.image_optimizer.chart.total')) ?>,
                        data: <?= $data->chart['total'] ?? '[]' ?>,
                        backgroundColor: color_gradient,
                        borderColor: color,
                        fill: true
                    }
                ]
            },
            options: chart_options
        });

        /* Display chart */
        let saved_size_chart = document.getElementById('image_optimizer_saved_size').getContext('2d');

        color_gradient = saved_size_chart.createLinearGradient(0, 0, 0, 250);
        color_gradient.addColorStop(0, set_hex_opacity(color, 0.1));
        color_gradient.addColorStop(1, set_hex_opacity(color, 0.025));

        /* Tooltip titles */
        let get_formatted_bytes = (bytes) => {
            let selected_size = 0;
            let selected_unit = 'B';

            if(bytes > 0) {
                let units = ['TB', 'GB', 'MB', 'KB', 'B'];

                for (let i = 0; i < units.length; i++) {
                    let unit = units[i];
                    let cutoff = Math.pow(1000, 4 - i) / 10;

                    if(bytes >= cutoff) {
                        selected_size = bytes / Math.pow(1000, 4 - i);
                        selected_unit = unit;
                        break;
                    }
                }

                selected_size = Math.round(10 * selected_size) / 10;
            }

            return `${selected_size} ${selected_unit}`;
        }

        new Chart(saved_size_chart, {
            type: 'line',
            data: {
                labels: <?= $data->chart['labels'] ?>,
                datasets: [
                    {
                        label: <?= json_encode(l('admin_statistics.image_optimizer.chart.saved_size')) ?>,
                        data: <?= $data->chart['saved_size'] ?? '[]' ?>,
                        backgroundColor: color_gradient,
                        borderColor: color,
                        fill: true
                    }
                ]
            },
            options: {
                ...chart_options,
                ...{
                    plugins: {
                        ...chart_options.plugins,
                        tooltip: {
                            ...chart_options.plugins.tooltip,
                            callbacks: {
                                label: context => {
                                    return `${context.dataset.label}: ${get_formatted_bytes(context.raw)}`;
                                },
                                title: (context) => {
                                    return context[0].label;
                                }
                            }
                        },
                    }
                }
            }
        });


        /* Display chart */
        let average_percentage_difference_chart = document.getElementById('image_optimizer_average_percentage_difference').getContext('2d');

        color_gradient = average_percentage_difference_chart.createLinearGradient(0, 0, 0, 250);
        color_gradient.addColorStop(0, set_hex_opacity(color, 0.1));
        color_gradient.addColorStop(1, set_hex_opacity(color, 0.025));

        /* Tooltip titles */

        new Chart(average_percentage_difference_chart, {
            type: 'line',
            data: {
                labels: <?= $data->chart['labels'] ?>,
                datasets: [
                    {
                        label: <?= json_encode(l('admin_statistics.image_optimizer.chart.average_percentage_difference')) ?>,
                        data: <?= $data->chart['average_percentage_difference'] ?? '[]' ?>,
                        backgroundColor: color_gradient,
                        borderColor: color,
                        fill: true
                    }
                ]
            },
            options: {
                ...chart_options,
                ...{
                    plugins: {
                        ...chart_options.plugins,
                        tooltip: {
                            ...chart_options.plugins.tooltip,
                            callbacks: {
                                label: context => {
                                    return `${context.dataset.label}: ${nr(context.raw)}%`;
                                },
                                title: (context) => {
                                    return context[0].label;
                                }
                            }
                        },
                    }
                }
            }
        });
    </script>
<?php $javascript = ob_get_clean() ?>

<?php return (object) ['html' => $html, 'javascript' => $javascript] ?>
