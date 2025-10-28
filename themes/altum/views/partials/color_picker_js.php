<?php defined('ALTUMCODE') || die() ?>

<?php if(!\Altum\Event::exists_content_type_key('javascript', 'pickr')): ?>
    <?php if(!isset($data->exclude_css)): ?>
        <?php ob_start() ?>
        <link href="<?= ASSETS_FULL_URL . 'css/libraries/pickr.min.css?v=' . PRODUCT_CODE ?>" rel="stylesheet" media="screen">
        <?php \Altum\Event::add_content(ob_get_clean(), 'head') ?>
    <?php endif ?>

    <?php ob_start() ?>
    <?php if(!isset($data->exclude_js)): ?>
        <script src="<?= ASSETS_FULL_URL . 'js/libraries/pickr.min.js?v=' . PRODUCT_CODE ?>"></script>
    <?php endif ?>

    <script>
        'use strict';

        /* Initiate the color picker when needed */
        let initiate_color_pickers = () => {
            document.querySelectorAll('[data-color-picker]').forEach(element => {
                let has_input = !element.getAttribute('data-color-picker-no-input');
                let has_opacity = <?= json_encode(isset($data->opacity)) ?>;
                let data_has_opacity = element.getAttribute('data-color-picker-has-opacity');
                if(data_has_opacity) {
                    has_opacity = data_has_opacity === 'true';
                }

                let picker_element = document.createElement('div');
                element.insertAdjacentElement('afterend', picker_element);

                let delay_timer = null

                Pickr.create({
                    el: picker_element,
                    default: element.value,
                    comparison: false,
                    components: {
                        preview: true,
                        opacity: has_opacity,
                        hue: true,
                        comparison: false,
                        interaction: {
                            hex: true,
                            rgba: false,
                            hsla: false,
                            hsva: false,
                            cmyk: false,
                            input: has_input,
                            clear: false,
                            save: false,
                        }
                    }
                }).on('change', hsva => {
                    if(delay_timer) clearTimeout(delay_timer);

                    delay_timer = setTimeout(() => {
                        element.value = hsva.toHEXA();
                        element.dispatchEvent(new Event('change'));
                    }, 250);
                });
            });
        }

        initiate_color_pickers();
    </script>
    <?php \Altum\Event::add_content(ob_get_clean(), 'javascript', 'pickr') ?>
<?php endif ?>
