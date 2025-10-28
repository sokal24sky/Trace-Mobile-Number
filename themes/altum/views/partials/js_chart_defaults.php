<?php defined('ALTUMCODE') || die() ?>

<?php ob_start() ?>
<script src="<?= ASSETS_FULL_URL . 'js/libraries/Chart.bundle.min.js?v=' . PRODUCT_CODE ?>"></script>
<script src="<?= ASSETS_FULL_URL . 'js/libraries/chartjs-plugin-watermark.min.js?v=' . PRODUCT_CODE ?>"></script>

<script>
    'use strict';
    
/* Default chart settings */
    const set_hex_opacity = (color, alpha) => {
    if (color.startsWith("#")) {
        const r = parseInt(color.slice(1, 3), 16);
        const g = parseInt(color.slice(3, 5), 16);
        const b = parseInt(color.slice(5, 7), 16);
        return `rgba(${r}, ${g}, ${b}, ${alpha})`;
    }

    if (color.startsWith("hsl")) {
        const values = color.match(/[\d.]+/g).map(Number);
        const [h, s, l] = values;
        const k = n => (n + h / 30) % 12;
        const a_ = (s / 100) * Math.min(l / 100, 1 - l / 100);
        const f = n => Math.round((l / 100 - a_ * Math.max(-1, Math.min(k(n) - 3, Math.min(9 - k(n), 1)))) * 255);
        return `rgba(${f(0)}, ${f(8)}, ${f(4)}, ${alpha})`;
    }

    throw new Error("Unsupported color format");
};

    Chart.defaults.elements.line.borderWidth = 4;
    Chart.defaults.elements.point.radius = 3;
    Chart.defaults.elements.point.hoverRadius = 4;
    Chart.defaults.elements.point.borderWidth = 5;
    Chart.defaults.elements.point.hoverBorderWidth = 6;
    Chart.defaults.font.family = "-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,'Noto Sans',sans-serif,'Apple Color Emoji','Segoe UI Emoji','Segoe UI Symbol','Noto Color Emoji'";

    let chart_css = window.getComputedStyle(document.body);

    /* Default chart options */
    let chart_options = {
        // animation: false,
        // animations: {
        //     colors: false,
        //     x: false,
        // },
        // transitions: {
        //     active: {
        //         animation: {
        //             duration: 0
        //         }
        //     }
        // },

        responsiveAnimationDuration: 0,
        elements: {
            line: {
                tension: 0
            }
        },
        interaction: {
            intersect: false,
            mode: 'index',
        },
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                boxPadding: 8,
                boxHeight: 12,
                boxWidth: 12,

                padding: 18,
                backgroundColor: chart_css.getPropertyValue('--gray-900'),
                cornerRadius: 8,

                titleColor: chart_css.getPropertyValue('--white'),
                titleSpacing: 30,
                titleFont: {
                    size: 16,
                    weight: 'bold'
                },
                titleMarginBottom: 10,

                bodyColor: chart_css.getPropertyValue('--white'),
                bodyFont: {
                    size: 14,
                },
                bodySpacing: 10,

                footerMarginTop: 10,
                footerFont: {
                    size: 12,
                    weight: 'normal'
                },

                caretSize: 6,
                caretPadding: 20,

                callbacks: {
                    label: (context) => {
                        return `${context.dataset.label}: ${nr(context.raw)}`;
                    }
                }
            },
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    display: false
                },
                ticks: {
                    callback: (value, index, ticks) => {
                        if(Math.floor(value) === value) {
                            return nr(value);
                        }
                    },
                }
            },
            x: {
                grid: {
                    display: false
                },
            }
        },
        responsive: true,
        maintainAspectRatio: false,

        <?php if(settings()->main->{'logo_' . \Altum\ThemeStyle::get()}): ?>
        watermark: {
            image: <?= json_encode(settings()->main->{'logo_' . \Altum\ThemeStyle::get() . '_full_url'}) ?>,

            x: 50,
            y: 50,

            width: "5%",
            height: "auto",

            opacity: 0.05,

            alignX: "right",
            alignY: "bottom",

            alignToChartArea: false,
            position: "back",
        }
        <?php endif ?>
    };
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript', 'chartjs') ?>
