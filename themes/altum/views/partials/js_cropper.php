<?php defined('ALTUMCODE') || die(); ?>

<?php ob_start() ?>
<link href="<?= ASSETS_FULL_URL . 'css/libraries/cropper.min.css' . '?v=' . PRODUCT_CODE ?>" rel="stylesheet" media="screen,print">
<?php \Altum\Event::add_content(ob_get_clean(), 'head') ?>

<?php ob_start() ?>
<script src="<?= ASSETS_FULL_URL . 'js/libraries/cropper.min.js' . '?v=' . PRODUCT_CODE ?>"></script>

<script>
    'use strict';
    
/* expose cropper initializer globally */
    let initialize_image_cropper = () => {
        let cropper = null;
        let current_input = null;

        /* check if modal already exists to avoid duplicating it */
        if(!document.getElementById('image_cropper_modal')) {
            const modal_html = `
            <div class="modal fade" id="image_cropper_modal" data-backdrop="static" tabindex="-1" role="dialog">
              <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
                <div class="modal-content">

                  <div class="modal-header">
                    <h5 class="modal-title"><?= l('global.crop') ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="<?= l('global.close') ?>">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>

                  <div class="modal-body text-center">
                    <div class="position-relative">
                        <img id="image_cropper_preview" style="max-width: 90%; max-height: 60vh" class="rounded-2x">
                    </div>
                  </div>

                  <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-dismiss="modal"><?= l('global.no_crop') ?></button>

                    <button type="button" class="btn btn-primary" id="crop_image_submit">
                        <?= l('global.crop_selection') ?>
                        <span id="image_cropper_size" class="small"></span>
                    </button>
                  </div>
                </div>
              </div>
            </div>`;
            document.body.insertAdjacentHTML('beforeend', modal_html);
        }

        const cropper_modal = document.getElementById('image_cropper_modal');
        const preview_image = document.getElementById('image_cropper_preview');
        const crop_button = document.getElementById('crop_image_submit');

        /* remove previous listener to prevent stacking */
        crop_button.replaceWith(crop_button.cloneNode(true));
        const new_crop_button = document.getElementById('crop_image_submit');

        const crop_size_box = document.getElementById('image_cropper_size');

        /* handle crop button */
        /* handle crop button */
        new_crop_button.addEventListener('click', () => {
            if(!cropper || !current_input) return;

            /* get original file and detect svg */
            const original_file = current_input.files[0];
            const is_svg = original_file && (original_file.type === 'image/svg+xml' || /\.svg$/i.test(original_file.name));

            /* force png export for svg, keep original mime for others */
            const export_mime_type = is_svg ? 'image/png' : original_file.type;

            /* render cropped canvas with good smoothing */
            const cropped_canvas = cropper.getCroppedCanvas({
                imageSmoothingEnabled: true,
                imageSmoothingQuality: 'high',
                /* keep transparency for png */
                fillColor: 'transparent'
            });

            cropped_canvas.toBlob((blob) => {
                if(!blob) return;

                /* fix filename when source was svg */
                const new_file_name = is_svg
                    ? original_file.name.replace(/\.svg$/i, '.png')
                    : original_file.name;

                const new_file = new File([blob], new_file_name, {
                    type: export_mime_type,
                    lastModified: Date.now()
                });

                const data_transfer = new DataTransfer();
                data_transfer.items.add(new_file);
                current_input.files = data_transfer.files;

                current_input.dispatchEvent(new Event('change'));

                $(cropper_modal).modal('hide');
            }, export_mime_type);
        });

        /* attach change event to all crop inputs */
        document.querySelectorAll('input[type="file"][data-crop]').forEach((input) => {

            /* avoid attaching multiple times */
            input.removeEventListener('change', input._cropper_handler);

            input._cropper_handler = event => {
                const file = event.target.files[0];
                if(!file || !file.type.startsWith('image/')) return;

                const reader = new FileReader();
                reader.onload = event => {
                    preview_image.src = event.target.result;

                    $(cropper_modal).modal('show');

                    $(cropper_modal).on('shown.bs.modal', () => {
                        const aspect_ratio_attr = input.getAttribute('data-aspect-ratio');
                        const aspect_ratio = aspect_ratio_attr ? parseFloat(aspect_ratio_attr) : NaN;

                        cropper = new Cropper(preview_image, {
                            aspectRatio: aspect_ratio,
                            viewMode: 2,
                            restore: false,
                            crop: () => {
                                /* get cropped box data */
                                const crop_data = cropper.getData(true);

                                /* update size in modal */
                                crop_size_box.innerText = '(' + Math.round(crop_data.width) + '×' + Math.round(crop_data.height) + ' px)';
                            }
                        });

                    }).on('hidden.bs.modal', () => {
                        if(cropper) {
                            cropper.destroy();
                            cropper = null;
                        }
                        current_input = null;
                        crop_size_box.innerText = '';
                        $(cropper_modal).off('shown.bs.modal hidden.bs.modal');
                    });

                    current_input = input;
                };
                reader.readAsDataURL(file);
            };

            input.addEventListener('change', input._cropper_handler);
        });
    };

    /* auto-run once on load */
    initialize_image_cropper();
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript', 'cropper') ?>
