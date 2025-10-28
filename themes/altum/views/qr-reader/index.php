<?php defined('ALTUMCODE') || die() ?>

<div class="container text-center">
    <?= \Altum\Alerts::output_alerts() ?>

    <h1 class="index-header mb-2"><?= l('qr_reader.header') ?></h1>
    <p class="index-subheader mb-5"><?= l('qr_reader.subheader') ?></p>
</div>

<div class="container mt-5">
    <div class="card mb-4">
        <div class="card-body">
            <form action="" method="post" role="form" enctype="multipart/form-data">
                <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>" />

                <div class="form-group" data-file-image-input-wrapper data-file-input-wrapper-size-limit="<?= get_max_upload() ?>" data-file-input-wrapper-size-limit-error="<?= sprintf(l('global.error_message.file_size_limit'), get_max_upload()) ?>">
                    <label for="image"><i class="fas fa-fw fa-qrcode fa-sm text-muted mr-1"></i> <?= l('global.image') ?></label>
                    <?= include_view(THEME_PATH . 'views/partials/file_image_input.php', ['uploads_file_key' => 'qr_code_reader', 'file_key' => 'image', 'already_existing_image' => null]) ?>
                    <small class="form-text text-muted"><?= sprintf(l('global.accessibility.whitelisted_file_extensions'), \Altum\Uploads::get_whitelisted_file_extensions_accept('qr_code_reader')) . ' ' . sprintf(l('global.accessibility.file_size_limit'), get_max_upload()) ?></small>
                </div>

                <button id="start_scanner" type="button" class="btn btn-sm btn-block btn-light d-none"><i class="fas fa-fw fa-sm fa-camera mr-1"></i> <?= l('qr_reader.start_camera') ?></button>
            </form>
        </div>
    </div>

    <div class="card mb-4 d-none" id="scanner_wrapper">
        <div class="card-body">

            <div id="scanner"></div>

            <div id="scanner_cameras" class="mt-3"></div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">

            <div class="form-group">
                <div class="d-flex justify-content-between align-items-center">
                    <label for="result"><i class="fas fa-fw fa-bars fa-sm text-muted mr-1"></i> <?= l('qr_reader.result') ?></label>
                    <div>
                        <button
                                type="button"
                                class="btn btn-link text-secondary"
                                data-toggle="tooltip"
                                title="<?= l('global.clipboard_copy') ?>"
                                aria-label="<?= l('global.clipboard_copy') ?>"
                                data-copy="<?= l('global.clipboard_copy') ?>"
                                data-copied="<?= l('global.clipboard_copied') ?>"
                                data-clipboard-target="#result"
                                data-clipboard-text
                        >
                            <i class="fas fa-fw fa-sm fa-copy"></i>
                        </button>
                    </div>
                </div>
                <textarea id="result" class="form-control"></textarea>
            </div>

        </div>
    </div>

    <?php if(l('qr_reader.extra_content')): ?>
    <div class="mt-4">
        <div class="card">
            <div class="card-body">
                <?= l('qr_reader.extra_content') ?>
            </div>
        </div>
    </div>
    <?php endif ?>
</div>

<?php ob_start() ?>
<script src="<?= ASSETS_FULL_URL . 'js/libraries/html5-qrcode.min.js?v=' . PRODUCT_CODE ?>"></script>

<script>
    'use strict';
    
/* Local scanner with file uploading */
    const local_scanner = new Html5Qrcode('image', {
        formatsToSupport: [Html5QrcodeSupportedFormats.QR_CODE]
    });

    const image = document.getElementById('image');
    image.addEventListener('change', event => {
        const file = image.files[0];

        if(!file) {
            return;
        }

        local_scanner.scanFile(file, true)
            .then(decoded_text => {
                document.querySelector('#result').value = decoded_text;
            })
            .catch(err => {
                document.querySelector('#result').value = <?= json_encode(l('global.no_data')) ?>;
            });
    });

    /* Video scanner with camera */
    if(navigator.mediaDevices && typeof navigator.mediaDevices.getUserMedia === 'function') {
        /* Enable start camera button */
        document.querySelector('#start_scanner').classList.remove('d-none');

        const video_scanner = new Html5Qrcode('scanner', {
            formatsToSupport: [ Html5QrcodeSupportedFormats.QR_CODE ]
        });

        document.querySelector('#start_scanner').addEventListener('click', async event => {
            let cameras = [];
            let selected_camera_id = null;

            try {
                cameras = await Html5Qrcode.getCameras();
            } catch (error) {
                alert(<?= json_encode(l('qr_reader.camera_error_message')) ?>);
            }

            if(cameras.length) {
                /* Disable start camera button */
                document.querySelector('#start_scanner').classList.add('d-none');

                /* Display the scanner wrapper */
                document.querySelector('#scanner_wrapper').classList.remove('d-none');

                /* Add stop button */
                cameras.unshift({
                    'id': 'false',
                    'label': <?= json_encode(l('qr_reader.stop_camera')) ?>
                });

                /* Create the buttons for the scanner */
                cameras.forEach(camera => {
                    let button = document.createElement('button');
                    button.classList.add('btn');
                    button.classList.add('btn-sm');
                    button.classList.add('btn-block');
                    button.classList.add('btn-light');
                    button.innerText = camera.label;
                    button.setAttribute('data-camera-id', camera.id);
                    button.addEventListener('click', async () => {
                        await choose_camera(camera.id);
                    })

                    document.querySelector('#scanner_cameras').appendChild(button);
                });

                let choose_camera = async camera_id => {
                    if(camera_id == selected_camera_id) {
                        return;
                    }

                    try {
                        await video_scanner.stop();
                    } catch (error) { /* :) */ }

                    if(camera_id !== 'false') {
                        await video_scanner.start(
                            camera_id,
                            {fps: 10, qrbox: {width: 250, height: 250}},
                            (decoded_text) => {
                                document.querySelector('#result').value = decoded_text;
                            }
                        );
                    }

                    /* Highlight the selected camera */
                    document.querySelectorAll('[data-camera-id]').forEach(element => {
                        if(element.getAttribute('data-camera-id') == camera_id) {
                            element.classList.remove('btn-light');
                            element.classList.add('btn-primary');
                        } else {
                            element.classList.remove('btn-primary');
                            element.classList.add('btn-light');
                        }
                    });

                    selected_camera_id = camera_id;
                }

                await choose_camera(cameras[1].id);
            }
        });
    }
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>

<?php include_view(THEME_PATH . 'views/partials/clipboard_js.php') ?>

<?php ob_start() ?>
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "BreadcrumbList",
            "itemListElement": [
                {
                    "@type": "ListItem",
                    "position": 1,
                    "name": "<?= l('index.title') ?>",
                    "item": "<?= url() ?>"
                },
                {
                    "@type": "ListItem",
                    "position": 2,
                    "name": "<?= l('qr_reader.title') ?>",
                    "item": "<?= url('qr-reader') ?>"
                }
            ]
        }
    </script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
