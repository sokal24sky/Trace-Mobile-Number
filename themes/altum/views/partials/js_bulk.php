<?php defined('ALTUMCODE') || die() ?>

<?php ob_start() ?>
<script>
    'use strict';
    
/* Cache elements */
    let bulk_select_all_element = document.querySelector('#bulk_select_all');
    let bulk_counter_element = document.querySelector('#bulk_counter');
    let bulk_actions_element = document.querySelector('#bulk_actions');
    let bulk_enable_element = document.querySelector('#bulk_enable');
    let bulk_disable_element = document.querySelector('#bulk_disable');
    let bulk_group_element = document.querySelector('#bulk_group');

    /* Function to update bulk counter */
    let bulk_counter_handler = () => {
        let bulk_inputs = document.querySelectorAll('td[data-bulk-table] input');
        let available_count = bulk_inputs.length;
        let selected_count = document.querySelectorAll('td[data-bulk-table] input:checked').length;

        if(selected_count) {
            bulk_counter_element.textContent = `(${nr(selected_count)})`;
            bulk_counter_element.classList.remove('d-none');
            bulk_actions_element.classList.remove('disabled');
        } else {
            bulk_counter_element.classList.add('d-none');
            bulk_actions_element.classList.add('disabled');
        }

        bulk_select_all_element.checked = selected_count === available_count;
    };

    /* Select all handler */
    if(bulk_select_all_element) {
        bulk_select_all_element.addEventListener('click', event => {
            let is_checked = event.currentTarget.checked;
            let bulk_inputs = document.querySelectorAll('td[data-bulk-table] input');
            bulk_inputs.forEach(input_element => {
                input_element.checked = is_checked;
            });
            bulk_counter_handler();
        });
    }

    /* Attach event once using delegation if many rows, otherwise keep as is */
    let bulk_inputs = document.querySelectorAll('td[data-bulk-table] input');
    bulk_inputs.forEach(input_element => {
        input_element.addEventListener('click', bulk_counter_handler);
    });

    /* Handler to toggle the bulk actions on */
    if(bulk_enable_element) {
        bulk_enable_element.addEventListener('click', event => {
            let bulk_table_elements = document.querySelectorAll('[data-bulk-table]');
            if(bulk_table_elements.length) {
                bulk_enable_element.classList.add('d-none');
                bulk_group_element.classList.remove('d-none');
                bulk_table_elements.forEach(element => element.classList.remove('d-none'));
                bulk_counter_handler();
            }
        });
    }

    /* Handler to toggle the bulk actions off */
    if(bulk_disable_element) {
        bulk_disable_element.addEventListener('click', event => {
            let bulk_table_elements = document.querySelectorAll('[data-bulk-table]');
            bulk_group_element.classList.add('d-none');
            bulk_enable_element.classList.remove('d-none');
            bulk_table_elements.forEach(element => element.classList.add('d-none'));
        });
    }
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
