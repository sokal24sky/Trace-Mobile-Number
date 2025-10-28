let toggle_admin_sidebar = () => {
    /* Open sidebar menu */
    let body = document.querySelector('body');
    body.classList.toggle('admin-sidebar-opened');

    /* Toggle overlay */
    let admin_overlay = document.querySelector('#admin_overlay');
    admin_overlay.style.display == 'none' ? admin_overlay.style.display = 'block' : admin_overlay.style.display = 'none';

    /* Change toggle button content */
    let button = document.querySelector('#admin_menu_toggler');

    if(body.classList.contains('admin-sidebar-opened')) {
        button.innerHTML = `<i class="fas fa-fw fa-times"></i>`;
    } else {
        button.innerHTML = `<i class="fas fa-fw fa-bars"></i>`;
    }
};

/* Toggler for the sidebar */
document.querySelector('#admin_menu_toggler').addEventListener('click', event => {
    event.preventDefault();

    toggle_admin_sidebar();

    let admin_sidebar_is_opened = document.querySelector('body').classList.contains('admin-sidebar-opened');

    if(admin_sidebar_is_opened) {
        document.querySelector('#admin_overlay').removeEventListener('click', toggle_admin_sidebar);
        document.querySelector('#admin_overlay').addEventListener('click', toggle_admin_sidebar);
    } else {
        document.querySelector('#admin_overlay').removeEventListener('click', toggle_admin_sidebar);
    }
});

/* Custom select implementation */
$('select:not([multiple="multiple"]):not([class="input-group-text"]):not([class="custom-select custom-select-sm"]):not([class^="ql"]):not([data-is-not-custom-select])').each(function() {
    let $select = $(this);
    $select.select2({
        dir: document.querySelector('html').dir,
        minimumResultsForSearch: 5,
    });

    /* Make sure to trigger the select when the label is clicked as well */
    let selectId = $select.attr('id');
    if(selectId) {
        $('label[for="' + selectId + '"]').on('click', function(event) {
            event.preventDefault();
            $select.select2('open');
        });
    }
});

document.querySelectorAll('form input, form select, form textarea').forEach(field => {
    field.addEventListener('invalid', () => {
        const invalid_field = field;

        /* find the nearest collapsed parent */
        const collapse_container = invalid_field.closest('.collapse');

        if (!collapse_container) {
            return;
        }

        /* if collapse already open, scroll immediately */
        if ($(collapse_container).hasClass('show')) {
            invalid_field.scrollIntoView({ behavior: 'smooth', block: 'center' });
            setTimeout(() => invalid_field.focus(), 100);
            return;
        }

        /* wait for the collapse animation to finish */
        $(collapse_container).one('shown.bs.collapse', () => {
            invalid_field.scrollIntoView({ behavior: 'smooth', block: 'center' });
            setTimeout(() => invalid_field.focus(), 100);
        });

        /* trigger the open animation */
        $(collapse_container).collapse('show');
    });
});
