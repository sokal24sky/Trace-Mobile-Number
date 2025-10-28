<?php defined('ALTUMCODE') || die() ?>

<?php ob_start() ?>
<script>
    'use strict';
    
/* On change regenerated qr */
    let barcode_delay_timer = null;

    let apply_reload_barcode_event_listeners = () => {
        document.querySelectorAll('[data-reload-barcode]').forEach(element => {
            let events = ['paste', 'keyup', 'change'];
            events.forEach(event_type => {
                element.removeEventListener(event_type, reload_barcode_event_listener);
                element.addEventListener(event_type, reload_barcode_event_listener);
            })
        });
    }

    let reload_barcode_event_listener = event => {

        let targeted_element = event.currentTarget;

        /* determine the value to store based on element type */
        let storage_value = targeted_element.type === 'checkbox' ? (targeted_element.checked ? '1' : '0') : targeted_element.value;

        /* check if the stored value is the same as the current one */
        if(sessionStorage.getItem(targeted_element.id) !== storage_value) {
            sessionStorage.setItem(targeted_element.id, storage_value);
        }

        /* Add the preloader, hide the barcode */
        document.querySelector('#barcode').classList.add('qr-code-loading');

        /* Disable the submit button */
        if(document.querySelector('button[type="submit"]')) {
            document.querySelector('button[type="submit"]').classList.add('disabled');
            document.querySelector('button[type="submit"]').setAttribute('disabled','disabled');
        }

        clearTimeout(barcode_delay_timer);

        barcode_delay_timer = setTimeout(() => {

            /* Send the request to the server */
            let form = document.querySelector('#form');
            let form_data = new FormData(form);
            form_data.delete('barcode');

            let notification_container = form.querySelector('.notification-container');
            notification_container.innerHTML = '';

            fetch(`${url}barcode-generator`, {
                method: 'POST',
                body: form_data,
            })
                .then(response => response.ok ? response.json() : Promise.reject(response))
                .then(data => {
                    if(data.status == 'error') {
                        display_notifications(data.message, 'error', notification_container);
                    }

                    else if(data.status == 'success') {
                        display_notifications(data.message, 'success', notification_container);

                        document.querySelector('#barcode').src = data.details.data;
                        document.querySelector('#download_svg').href = data.details.data;
                        if(document.querySelector('input[name="barcode"]')) {
                            document.querySelector('input[name="barcode"]').value = data.details.data;
                        }

                        /* Display embedded data */
                        if(document.querySelector('#embedded_data_container_button')) {
                            document.querySelector('#embedded_data_container_button').classList.remove('d-none');
                            document.querySelector('#embedded_data_display').innerText = data.details.embedded_data;
                            if(document.querySelector('input[name="embedded_data"]')) {
                                document.querySelector('input[name="embedded_data"]').value = data.details.embedded_data;
                            }
                        }

                        /* Enable the submit button */
                        if(document.querySelector('button[type="submit"]')) {
                            document.querySelector('button[type="submit"]').classList.remove('disabled');
                            document.querySelector('button[type="submit"]').removeAttribute('disabled');
                        }

                    }

                    /* Hide the preloader, display the barcode */
                    document.querySelector('#barcode').classList.remove('qr-code-loading');
                })
                .catch(error => {
                    console.log(error);
                });

        }, 750);
    }

    apply_reload_barcode_event_listeners();

    /* SVG to PNG, WEBP, JPG download handler */
    let convert_svg_barcode_to_others = (svg_data, type, name) => {
        svg_data = !svg_data && document.querySelector('#download_svg') ? document.querySelector('#download_svg').href : svg_data;

        let image = new Image;
        image.crossOrigin = 'anonymous';

        /* Convert SVG data to others */
        image.onload = function() {
            let canvas = document.createElement('canvas');

            canvas.width = image.naturalWidth;
            canvas.height = image.naturalHeight;

            let context = canvas.getContext('2d');
            context.drawImage(image, 0, 0, canvas.width, canvas.height);

            let data = canvas.toDataURL(`image/${type}`, 1);

            /* Download */
            let link = document.createElement('a');
            link.download = name;
            link.style.opacity = '0';
            document.body.appendChild(link);
            link.href = data;
            link.click();
            link.remove();
        }

        /* Add SVG data */
        image.src = svg_data;
    }

    <?php if(isset($_GET['name'])): ?>
    document.querySelector('select[name="type"]').dispatchEvent(new Event('change'));
    document.querySelector('input[name="reload"]').dispatchEvent(new Event('change'));
    <?php endif ?>
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
