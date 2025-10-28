<?php defined('ALTUMCODE') || die() ?>

<?php if((is_logged_in() && user()->type == 1 && settings()->main->admin_spotlight_is_enabled) || settings()->main->user_spotlight_is_enabled): ?>
    <div id="spotlight_wrapper" class="spotlight-wrapper d-none" aria-hidden="true">
        <div class="container spotlight-modal">

            <form id="spotlight_form" action="" method="get" role="form">
                <input type="hidden" name="global_token" value="<?= \Altum\Csrf::get('global_token') ?>" />

                <input id="spotlight_search" type="search" name="search" class="form-control" required="required" autocomplete="off" placeholder="<?= l('global.spotlight.search_placeholder') ?>" aria-label="<?= l('global.search') ?>" />

                <div id="spotlight_results" class="spotlight-results mt-3"></div>

                <div id="spotlight_no_data" class="my-3 p-3 bg-gray-50 rounded-2x position-relative text-center" style="display:none;">
                    <span class="text-muted"><?= l('global.no_data') ?></span>
                </div>
            </form>
        </div>
    </div>

    <?php ob_start() ?>
    <script>
        'use strict';

        /* ------------------------------
           DOM Elements & Data Storage
        ------------------------------ */
        const spotlight_wrapper = document.getElementById('spotlight_wrapper');
        const spotlight_modal = document.querySelector('.spotlight-modal');
        const spotlight_results = document.querySelector('#spotlight_results');
        let spotlight_results_array = [];

        /* ------------------------------
           Show / Hide Spotlight
        ------------------------------ */
        const spotlight_display = () => {
            spotlight_wrapper.classList.remove('d-none');
            requestAnimationFrame(() => {
                spotlight_wrapper.classList.add('show');
            });
            spotlight_wrapper.setAttribute('aria-hidden', 'false');
            document.querySelector('#spotlight_search').focus();
        };

        const spotlight_hide = () => {
            spotlight_wrapper.classList.remove('show');
            spotlight_wrapper.setAttribute('aria-hidden', 'true');

            setTimeout(() => {
                spotlight_wrapper.classList.add('d-none');
            }, 150);
        };

        /* ------------------------------
           Fetch Pages & Store Locally
        ------------------------------ */
        const spotlight_get_pages = async () => {
            const form = new FormData(document.querySelector('#spotlight_form'));
            const params = new URLSearchParams(form).toString();

            const response = await fetch(`${url}spotlight?global_token=${global_token}`, { method: 'get' });
            let data;

            try {
                data = await response.json();
            } catch (error) {
                return false;
            }

            if(!response.ok || data.status === 'error') {
                return false;
            } else {
                let results = data.details.map(obj => ({ ...obj, clicks: 0 }));
                localStorage.setItem('<?= md5(SITE_URL) ?>_spotlight_results', JSON.stringify(results));
            }

            return data.details;
        };

        /* ------------------------------
           Build & Insert All Results Once
        ------------------------------ */
        const build_all_spotlight_results = (pages) => {
            // Sort them once by clicks (descending)
            pages.sort((a, b) => b.clicks - a.clicks);

            // Build in one shot
            let html = '';
            for (const page of pages) {
                // Include lowercase name/url as data attrs for quick toggling
                html += `
            <a
                href="${url}${page.url || ''}"
                target="_blank"
                class="my-3 p-3 bg-gray-50 rounded-2x position-relative text-decoration-none text-reset"
                style="display: block"
                data-spotlight-result-url="${page.url}"
                data-spotlight-result-name="${page.name}"
            >
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span>${page.name}</span>
                        <div class="small text-muted">${page.url ? '/' + page.url : url}</div>
                    </div>
                    <div>
                        <i class="fas fa-fw fa-lg fa-arrow-right text-muted"></i>
                    </div>
                </div>
            </a>
        `;
            }

            spotlight_results.innerHTML = html;

            document.querySelector('#spotlight_no_data').style.display = pages.length ? 'none' : 'block';
        };

        /* ------------------------------
           Toggle Display Instead of Re-rendering
        ------------------------------ */
        const filter_spotlight_results = (search_value) => {
            const spotlight_no_data = document.querySelector('#spotlight_no_data');
            const lower_search = search_value.toLowerCase();
            const links = spotlight_results.querySelectorAll('a[data-spotlight-result-url]');
            let matches = 0;

            links.forEach(link => {
                const page_name = link.getAttribute('data-spotlight-result-name').toLowerCase();
                const page_url = link.getAttribute('data-spotlight-result-url').toLowerCase();

                if(page_name.includes(lower_search) || page_url.includes(lower_search)) {
                    link.style.display = 'block';
                    matches++;
                } else {
                    link.style.display = 'none';
                }
            });

            // If no matches, show #spotlight_no_data
            spotlight_no_data.style.display = matches ? 'none' : 'block';
        };

        /* ------------------------------
           Initial Load
        ------------------------------ */
        const spotlight_init = async () => {

            // Show spinner only once, on load
            spotlight_results.innerHTML = `
        <div class="my-3 p-3 bg-gray-50 rounded-2x position-relative d-flex justify-content-center">
            <div class="spinner-border spinner-border-lg" role="status"></div>
        </div>
    `;

            // If no local results, fetch from server
            if(!get_cookie('spotlight_has_results')) {
                localStorage.removeItem('<?= md5(SITE_URL) ?>_spotlight_results');
            }
            if(!localStorage.getItem('<?= md5(SITE_URL) ?>_spotlight_results')) {
                const fetched_pages = await spotlight_get_pages();
                spotlight_results_array = fetched_pages || [];
            } else {
                spotlight_results_array = JSON.parse(localStorage.getItem('<?= md5(SITE_URL) ?>_spotlight_results'));
            }

            set_cookie('spotlight_has_results', 1, 90, <?= json_encode(COOKIE_PATH) ?>);

            // Now build the entire set
            build_all_spotlight_results(spotlight_results_array);

            // Optional: highlight first result if you want
            const first_link = spotlight_results.querySelector('a');
            if(first_link) {
                first_link.dispatchEvent(new Event('mouseover', { bubbles: true }));
            }
        };

        /* ------------------------------
           Debounced Search on Input
        ------------------------------ */
        let search_timeout;
        const spotlight_process_search = () => {
            if(search_timeout) clearTimeout(search_timeout);
            search_timeout = setTimeout(() => {
                const search = document.querySelector('#spotlight_search').value;

                filter_spotlight_results(search);
            }, 100);
        };

        /* ------------------------------
           Track Clicks via Delegation
        ------------------------------ */
        spotlight_results.addEventListener('click', event => {
            const link = event.target.closest('a[data-spotlight-result-url]');
            if(!link) return;

            const result_url = link.getAttribute('data-spotlight-result-url');
            const page_obj = spotlight_results_array.find(p => p.url === result_url);

            if(page_obj) {
                page_obj.clicks += 1;
                localStorage.setItem('<?= md5(SITE_URL) ?>_spotlight_results', JSON.stringify(spotlight_results_array));
            }
        });

        /* ------------------------------
           Keyboard Navigation
        ------------------------------ */
        const navigate_results = direction => {
            // Only get visible links
            const focusable_results = Array
                .from(spotlight_results.querySelectorAll('a'))
                .filter(a => a.style.display !== 'none');

            if(!focusable_results.length) return;

            // Where is the current focus?
            let current_index = focusable_results.indexOf(document.activeElement);

            // If nothing is focused yet, treat it as -1
            if(current_index === -1) current_index = -1;

            // Move up or down among only visible items
            if(direction === 'down') {
                current_index = (current_index + 1) % focusable_results.length;
            } else if(direction === 'up') {
                current_index = (current_index - 1 + focusable_results.length) % focusable_results.length;
            }

            focusable_results[current_index].focus();
        };

        /* ------------------------------
           Global Keydown for CTRL+K, etc.
        ------------------------------ */
        document.addEventListener('keydown', event => {
            if((event.ctrlKey || event.metaKey) && event.key === 'k') {
                event.preventDefault();
                spotlight_wrapper.getAttribute('aria-hidden') === 'true' ? spotlight_display() : spotlight_hide();
            }

            if(spotlight_wrapper.getAttribute('aria-hidden') === 'false') {
                if(event.key === 'Escape') {
                    spotlight_hide();
                } else if(event.key === 'ArrowDown') {
                    event.preventDefault();
                    navigate_results('down');
                } else if(event.key === 'ArrowUp') {
                    event.preventDefault();
                    navigate_results('up');
                } else if(event.key === 'Enter') {
                    /* handle enter if needed */
                } else {
                    document.querySelector('#spotlight_search').focus();
                }
            }
        });

        /* ------------------------------
           Hide on Click Outside
        ------------------------------ */
        spotlight_wrapper.addEventListener('click', event => {
            if(!spotlight_wrapper.classList.contains('d-none') && !spotlight_modal.contains(event.target)) {
                spotlight_hide();
            }
        });

        /* ------------------------------
           Attach Debounced Search
        ------------------------------ */
        ['change','paste','keyup'].forEach(event_type => {
            document.querySelector('#spotlight_search').addEventListener(event_type, spotlight_process_search);
        });

        /* ------------------------------
           Submit Form -> Go to First Result
        ------------------------------ */
        document.querySelector('#spotlight_form').addEventListener('submit', event => {
            event.preventDefault();

            const first_visible = Array
                .from(document.querySelectorAll('#spotlight_results a'))
                .find(a => a.style.display !== 'none');

            if(first_visible) {
                first_visible.click();
            }
        });

        /* ------------------------------
           Kick off initial load
        ------------------------------ */
        (async () => {
            await spotlight_init();
        })();
    </script>
    <?php \Altum\Event::add_content(ob_get_clean(), 'javascript', 'spotlight'); ?>

    <?php ob_start() ?>
    <style>
        /* Spotlight search */
        .spotlight-wrapper {
            background: hsla(0, 100%, 100%, 0.5);
            position: fixed;
            width: 100%;
            height: 100%;
            z-index: 200;
            display: flex;
            justify-content: center;
            align-items: center;
            opacity: 0;
            transition: opacity .15s;
            pointer-events: none;
        }

        [data-theme-style="dark"] .spotlight-wrapper {
            background: hsla(0, 100%, 0%, 0.5);
        }

        .spotlight-wrapper.show {
            opacity: 1;
            pointer-events: auto;
        }

        .spotlight-modal {
            padding: 2rem;
            border-radius: calc(2*var(--border-radius));
            background: var(--gray-100);
        }

        .spotlight-results {
            overflow-y: scroll;
            max-height: 29rem;
        }

        .spotlight-results a {
            border-radius: calc(2*var(--border-radius));
        }

        .spotlight-results a:focus-visible {
            outline: none;
        }
    </style>
    <?php \Altum\Event::add_content(ob_get_clean(), 'javascript', 'spotlight_css'); ?>
<?php endif ?>
