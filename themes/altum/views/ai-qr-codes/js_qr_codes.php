<?php defined('ALTUMCODE') || die() ?>

<?php ob_start() ?>
<div id="local_scanner" class="d-none"></div>
<script src="<?= ASSETS_FULL_URL . 'js/libraries/html5-qrcode.min.js?v=' . PRODUCT_CODE ?>"></script>

<script>
    'use strict';
    
/* QR scanner */
    const local_scanner = new Html5Qrcode('local_scanner', {
        formatsToSupport: [Html5QrcodeSupportedFormats.QR_CODE]
    });

    let scan_qr_code = (image_url) => {
        let file = new Image();
        file.src = image_url;

        local_scanner.scanFile(file, true)
            .then(decoded_text => {
                document.querySelector('input[name="is_readable"]').value = 1;
                document.querySelector('#is_readable').classList.remove('d-none');
                document.querySelector('#is_not_readable').classList.add('d-none');
            })
            .catch(err => {
                document.querySelector('input[name="is_readable"]').value = 0;
                document.querySelector('#is_readable').classList.add('d-none');
                document.querySelector('#is_not_readable').classList.remove('d-none');
            });
    }
</script>

<script>
    'use strict';
    
/* Url Dynamic handler */
    let url_dynamic_handler = () => {
        let url_dynamic = document.querySelector('#url_dynamic').checked;

        if(url_dynamic) {
            document.querySelector('#content').removeAttribute('required');
            document.querySelector('[data-url]').classList.add('d-none');
            document.querySelector('#link_id').setAttribute('required', 'required');
            document.querySelector('[data-link-id]').classList.remove('d-none');

            let link_id_element = document.querySelector('#link_id');
            if(link_id_element.options.length) {
                document.querySelector('#content').value = link_id_element.options[link_id_element.selectedIndex].getAttribute('data-url');
            }
        } else {
            document.querySelector('#link_id').removeAttribute('required');
            document.querySelector('[data-link-id]').classList.add('d-none');
            document.querySelector('#content').setAttribute('required', 'required');
            document.querySelector('[data-url]').classList.remove('d-none');
        }
    }

    if(document.querySelector('#url_dynamic')) {
        url_dynamic_handler();
        document.querySelector('#url_dynamic').addEventListener('change', url_dynamic_handler);
    }

    /* URL Dynamic Link_id handler */
    let link_id_handler = () => {
        let link_id_element = document.querySelector('#link_id');

        if(link_id_element && document.querySelector('#url_dynamic') && document.querySelector('#url_dynamic').checked) {
            document.querySelector('#content').value = link_id_element.options[link_id_element.selectedIndex].getAttribute('data-url');
        }
    }
    document.querySelector('#link_id') && document.querySelector('#link_id').addEventListener('change', link_id_handler);

    let generate_ai_qr_code = () => {
        /* Add the preloader, hide the QR */
        document.querySelector('#ai_qr_code').classList.add('qr-code-loading');

        /* Disable the save & generate button */
        if(document.querySelector('#generate')) {
            document.querySelector('#generate').classList.add('disabled');
            document.querySelector('#generate').setAttribute('disabled','disabled');
        }

        if(document.querySelector('#save')) {
            document.querySelector('#save').classList.add('disabled');
            document.querySelector('#save').setAttribute('disabled','disabled');
        }

        /* Send the request to the server */
        let form = document.querySelector('#form');
        let form_data = new FormData(form);

        let notification_container = form.querySelector('.notification-container');
        notification_container.innerHTML = '';

        fetch(`${url}ai-qr-code-generator`, {
            method: 'POST',
            body: form_data,
        })
            .then(response => response.ok ? response.json() : Promise.reject(response))
            .then(data => {
                if(data.status == 'error') {
                    display_notifications(data.message, 'error', notification_container);
                } else if(data.status == 'success') {
                    display_notifications(data.message, 'success', notification_container);

                    /* Display the QR code */
                    document.querySelector('#ai_qr_code').src = data.details.data;
                    document.querySelector('#download').href = data.details.data;

                    /* Save the name in the form */
                    if(document.querySelector('input[name="ai_qr_code"]')) {
                        document.querySelector('input[name="ai_qr_code"]').value = data.details.ai_qr_code;
                    }

                    /* Display embedded data */
                    if(document.querySelector('#embedded_data_container_button')) {
                        document.querySelector('#embedded_data_container_button').classList.remove('d-none');
                        document.querySelector('#embedded_data_display').innerText = data.details.embedded_data;
                        if(document.querySelector('input[name="embedded_data"]')) {
                            document.querySelector('input[name="embedded_data"]').value = data.details.embedded_data;
                        }
                    }

                    /* Enable the download button */
                    document.querySelector('#download').classList.remove('disabled');

                    /* Disable the save & generate button */
                    if(document.querySelector('#generate')) {
                        document.querySelector('#generate').classList.remove('disabled');
                        document.querySelector('#generate').removeAttribute('disabled');
                    }

                    if(document.querySelector('#save')) {
                        document.querySelector('#save').classList.remove('disabled');
                        document.querySelector('#save').removeAttribute('disabled');
                    }

                    /* Check if its readable */
                    //scan_qr_code(data.details.data);
                }

                /* Hide the preloader, display the QR */
                document.querySelector('#ai_qr_code').classList.remove('qr-code-loading');
            })
            .catch(error => {
                /* Enable generate button */
                document.querySelector('#generate').classList.remove('disabled');

                console.log(error);
            });
    }

    document.querySelector('#generate').addEventListener('click', event => {
        generate_ai_qr_code();
        event.preventDefault();
    });
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
