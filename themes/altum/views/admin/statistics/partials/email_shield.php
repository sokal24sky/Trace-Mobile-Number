<?php defined('ALTUMCODE') || die() ?>

<?php ob_start() ?>
<div class="card mb-5">
    <div class="card-body">
        <div class="d-flex justify-content-between mb-4">
            <h2 class="h4 text-truncate mb-0"><i class="fas fa-fw fa-shield-alt fa-xs text-primary-900 mr-2"></i> <?= l('admin_statistics.email_shield.header') ?></h2>

            <div>
                <span class="badge <?= $data->total['valid'] > 0 ? 'badge-success' : 'badge-secondary' ?>" data-toggle="tooltip" title="<?= l('admin_statistics.email_shield.chart.valid') ?>">
                    <?= nr($data->total['valid']) ?> • <?= nr(get_percentage_between_two_numbers($data->total['valid'], $data->total['valid'] + $data->total['invalid']), 2, false) ?>%
                </span>
                <span class="badge <?= $data->total['invalid'] > 0 ? 'badge-danger' : 'badge-success' ?>" data-toggle="tooltip" title="<?= l('admin_statistics.email_shield.chart.invalid') ?>">
                    <?= nr($data->total['invalid']) ?> • <?= nr(get_percentage_between_two_numbers($data->total['invalid'], $data->total['valid'] + $data->total['invalid']), 2, false) ?>%
                </span>
            </div>
        </div>

        <div class="chart-container <?= $data->total['valid'] + $data->total['invalid'] ? null : 'd-none' ?>">
            <canvas id="email_shield"></canvas>
        </div>
        <?= $data->total['valid'] + $data->total['invalid'] ? null : include_view(THEME_PATH . 'views/partials/no_chart_data.php', ['has_wrapper' => false]); ?>
    </div>
</div>
<?php $html = ob_get_clean() ?>

<?php ob_start() ?>
    <script>
        'use strict';

        let valid_color = css.getPropertyValue('--success');
        let invalid_color = css.getPropertyValue('--danger');

        /* Display chart */
        let chart = document.getElementById('email_shield').getContext('2d');

        let invalid_color_gradient = chart.createLinearGradient(0, 0, 0, 250);
        invalid_color_gradient.addColorStop(0, set_hex_opacity(invalid_color, 0.1));
        invalid_color_gradient.addColorStop(1, set_hex_opacity(invalid_color, 0.025));

        let valid_color_gradient = chart.createLinearGradient(0, 0, 0, 250);
        valid_color_gradient.addColorStop(0, set_hex_opacity(valid_color, 0.1));
        valid_color_gradient.addColorStop(1, set_hex_opacity(valid_color, 0.025));

        new Chart(chart, {
            type: 'line',
            data: {
                labels: <?= $data->chart['labels'] ?>,
                datasets: [
                    {
                        label: <?= json_encode(l('admin_statistics.email_shield.chart.valid')) ?>,
                        data: <?= $data->chart['valid'] ?? '[]' ?>,
                        backgroundColor: valid_color_gradient,
                        borderColor: valid_color,
                        fill: true
                    },
                    {
                        label: <?= json_encode(l('admin_statistics.email_shield.chart.invalid')) ?>,
                        data: <?= $data->chart['invalid'] ?? '[]' ?>,
                        backgroundColor: invalid_color_gradient,
                        borderColor: invalid_color,
                        fill: true
                    }
                ]
            },
            options: chart_options
        });
    </script>
<?php $javascript = ob_get_clean() ?>

<?php return (object) ['html' => $html, 'javascript' => $javascript] ?>
