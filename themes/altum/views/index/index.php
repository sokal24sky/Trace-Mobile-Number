<?php defined('ALTUMCODE') || die() ?>

<div class="index-background py-9">
    <div class="container">
        <?= \Altum\Alerts::output_alerts() ?>

        <div class="row justify-content-center">
            <div class="col-11 col-md-10 col-lg-7">
                <h1 class="index-header text-center mb-2"><?= l('index.header') ?></h1>
            </div>

            <div class="col-10 col-sm-8 col-lg-6">
                <p class="index-subheader text-center mb-5"><?= l('index.subheader') ?></p>
            </div>
        </div>

        <div class="d-flex flex-column flex-lg-row justify-content-center">
            <?php if(settings()->codes->qr_codes_is_enabled): ?>
                <a href="<?= is_logged_in() ? url('qr-code-create') : url('qr/text') ?>" class="btn btn-primary index-button mb-3 mb-lg-0 mr-lg-3">
                    <i class="fas fa-fw fa-sm fa-qrcode mr-1"></i> <?= l('index.qr') ?>
                </a>
            <?php endif ?>

            <?php if(settings()->codes->barcodes_is_enabled): ?>
                <a href="<?= is_logged_in() ? url('barcode-create') : url('barcode') ?>" class="btn btn-dark index-button mb-3 mb-lg-0 mr-lg-3">
                    <i class="fas fa-fw fa-sm fa-barcode mr-1"></i> <?= l('index.barcode') ?>
                </a>
            <?php endif ?>
        </div>

        <?php if(settings()->codes->qr_reader_is_enabled || settings()->codes->barcode_reader_is_enabled): ?>
            <div class="d-flex flex-row justify-content-center mt-3">
                <?php if(settings()->codes->qr_reader_is_enabled): ?>
                    <a href="<?= url('qr-reader') ?>" class="btn btn-gray-200 index-button-secondary mr-3" data-toggle="tooltip" title="<?= l('qr_reader.menu') ?>">
                        <i class="fas fa-fw fa-sm fa-glasses"></i>
                    </a>
                <?php endif ?>

                <?php if(settings()->codes->barcode_reader_is_enabled): ?>
                    <a href="<?= url('barcode-reader') ?>" class="btn btn-gray-200 index-button-secondary mr-3" data-toggle="tooltip" title="<?= l('barcode_reader.menu') ?>">
                        <i class="fas fa-fw fa-sm fa-print"></i>
                    </a>
                <?php endif ?>
            </div>
        <?php endif ?>

    </div>
</div>

<div class="container">
    <div class="row justify-content-center mt-8" data-aos="fade-up">
        <div class="col-12">
            <img src="<?= get_custom_image_if_any('index/hero.png') ?>" class="img-fluid shadow rounded-lg zoom-animation-subtle" loading="lazy" alt="<?= l('index.hero_image_alt') ?>" />
        </div>
    </div>
</div>
<div class="my-5">&nbsp;</div>


<div class="container">
    <div class="row">
        <!-- QR Templates Widget -->
        <?php if(settings()->codes->qr_codes_is_enabled): ?>
            <div class="col-12 col-md-6 col-lg-4 p-4">
                <div class="card d-flex flex-column justify-content-between h-100 bg-gray-50" data-aos="fade-up" data-aos-delay="100">
                    <div class="card-body">
                        <div class="mt-3 mb-4">
                        <span class="p-3 rounded" style="background: #ecfdf5;">
                            <i class="fas fa-fw fa-lg fa-qrcode" style="color: #10b981;"></i>
                        </span>
                        </div>
                        <div class="mb-2">
                            <span class="h5"><?= l('index.qr_templates.header') ?></span>
                        </div>
                        <span class="text-muted"><?= sprintf(l('index.qr_templates.subheader'), count($data->available_qr_codes)) ?></span>
                    </div>
                </div>
            </div>

            <!-- Privacy Widget -->
            <div class="col-12 col-md-6 col-lg-4 p-4">
                <div class="card d-flex flex-column justify-content-between h-100 bg-gray-50" data-aos="fade-up" data-aos-delay="200">
                    <div class="card-body">
                        <div class="mt-3 mb-4">
                        <span class="p-3 rounded" style="background: #ecfeff;">
                            <i class="fas fa-fw fa-lg fa-user-secret" style="color: #06b6d4;"></i>
                        </span>
                        </div>
                        <div class="mb-2">
                            <span class="h5"><?= l('index.privacy.header') ?></span>
                        </div>
                        <span class="text-muted"><?= l('index.privacy.subheader') ?></span>
                    </div>
                </div>
            </div>

            <!-- Customization Widget -->
            <div class="col-12 col-md-6 col-lg-4 p-4">
                <div class="card d-flex flex-column justify-content-between h-100 bg-gray-50" data-aos="fade-up" data-aos-delay="300">
                    <div class="card-body">
                        <div class="mt-3 mb-4">
                        <span class="p-3 rounded" style="background: #eef2ff;">
                            <i class="fas fa-fw fa-lg fa-tools" style="color: #6366f1;"></i>
                        </span>
                        </div>
                        <div class="mb-2">
                            <span class="h5"><?= l('index.customization.header') ?></span>
                        </div>
                        <span class="text-muted"><?= l('index.customization.subheader') ?></span>
                    </div>
                </div>
            </div>
        <?php endif ?>

        <!-- Short URLs Widget -->
        <div class="col-12 col-md-6 col-lg-4 p-4">
            <div class="card d-flex flex-column justify-content-between h-100 bg-gray-50" data-aos="fade-up" data-aos-delay="400">
                <div class="card-body">
                    <div class="mt-3 mb-4">
                        <span class="p-3 rounded" style="background: #eef2ff;">
                            <i class="fas fa-fw fa-lg fa-link" style="color: #06b6d4;"></i>
                        </span>
                    </div>
                    <div class="mb-2">
                        <span class="h5"><?= l('index.short_urls.header') ?></span>
                    </div>
                    <span class="text-muted"><?= l('index.short_urls.subheader') ?></span>
                </div>
            </div>
        </div>

        <!-- Projects Widget -->
        <div class="col-12 col-md-6 col-lg-4 p-4">
            <div class="card d-flex flex-column justify-content-between h-100 bg-gray-50" data-aos="fade-up" data-aos-delay="500">
                <div class="card-body">
                    <div class="mt-3 mb-4">
                        <span class="p-3 rounded" style="background: #faf5ff;">
                            <i class="fas fa-fw fa-lg fa-tasks" style="color: #a855f7;"></i>
                        </span>
                    </div>
                    <div class="mb-2">
                        <span class="h5"><?= l('index.projects.header') ?></span>
                    </div>
                    <span class="text-muted"><?= l('index.projects.subheader') ?></span>
                </div>
            </div>
        </div>

        <!-- Domains Widget -->
        <div class="col-12 col-md-6 col-lg-4 p-4">
            <div class="card d-flex flex-column justify-content-between h-100 bg-gray-50" data-aos="fade-up" data-aos-delay="600">
                <div class="card-body">
                    <div class="mt-3 mb-4">
                        <span class="p-3 rounded" style="background: #fdf4ff;">
                            <i class="fas fa-fw fa-lg fa-globe" style="color: #d946ef;"></i>
                        </span>
                    </div>
                    <div class="mb-2">
                        <span class="h5"><?= l('index.domains.header') ?></span>
                    </div>
                    <span class="text-muted"><?= l('index.domains.subheader') ?></span>
                </div>
            </div>
        </div>
    </div>
</div>




<?php if(settings()->codes->qr_codes_is_enabled): ?>
    <div class="my-5">&nbsp;</div>

    <div class="container">
        <div class="row" data-aos="fade-up">
            <div class="col-lg-7 mb-5">
                <img src="<?= get_custom_image_if_any('index/static.png') ?>" class="img-fluid shadow rounded" loading="lazy" alt="<?= l('index.static_qr_image_alt') ?>" />
            </div>

            <div class="col-lg-5 mb-5 d-flex align-items-center">
                <div>
                <span class="p-3 bg-primary-100 rounded">
                    <i class="fas fa-fw fa-lg fa-qrcode text-primary"></i>
                </span>

                    <h2 class="mt-4"><?= l('index.static.header') ?></h2>
                    <p class="text-muted mt-3"><?= l('index.static.subheader') ?></p>

                    <ul class="list-style-none mt-4">
                        <li class="d-flex align-items-center mb-2">
                            <i class="fas fa-fw fa-sm fa-check-circle text-success mr-3"></i>
                            <div><?= l('index.static.feature1') ?></div>
                        </li>
                        <li class="d-flex align-items-center mb-2">
                            <i class="fas fa-fw fa-sm fa-check-circle text-success mr-3"></i>
                            <div><?= l('index.static.feature2') ?></div>
                        </li>
                        <li class="d-flex align-items-center mb-2">
                            <i class="fas fa-fw fa-sm fa-check-circle text-success mr-3"></i>
                            <div><?= l('index.static.feature3') ?></div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="my-5">&nbsp;</div>

    <div class="container">
        <div class="row" data-aos="fade-up">
            <div class="col-lg-5 mb-5 d-flex align-items-center order-1 order-lg-0">
                <div>
                <span class="p-3 bg-primary-100 rounded">
                    <i class="fas fa-fw fa-lg fa-link text-primary"></i>
                </span>

                    <h2 class="mt-4"><?= l('index.dynamic.header') ?></h2>
                    <p class="text-muted mt-3"><?= l('index.dynamic.subheader') ?></p>

                    <ul class="list-style-none mt-4">
                        <li class="d-flex align-items-center mb-2">
                            <i class="fas fa-fw fa-sm fa-check-circle text-success mr-3"></i>
                            <div><?= l('index.dynamic.feature1') ?></div>
                        </li>
                        <li class="d-flex align-items-center mb-2">
                            <i class="fas fa-fw fa-sm fa-check-circle text-success mr-3"></i>
                            <div><?= l('index.dynamic.feature2') ?></div>
                        </li>
                        <li class="d-flex align-items-center mb-2">
                            <i class="fas fa-fw fa-sm fa-check-circle text-success mr-3"></i>
                            <div><?= l('index.dynamic.feature3') ?></div>
                        </li>
                        <li class="d-flex align-items-center mb-2">
                            <i class="fas fa-fw fa-sm fa-check-circle text-success mr-3"></i>
                            <div><?= l('index.dynamic.feature4') ?></div>
                        </li>
                        <li class="d-flex align-items-center mb-2">
                            <i class="fas fa-fw fa-sm fa-check-circle text-success mr-3"></i>
                            <div><?= l('index.dynamic.feature5') ?></div>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="col-lg-7 mb-5 order-0 order-lg-1">
                <img src="<?= get_custom_image_if_any('index/dynamic.png') ?>" class="img-fluid shadow rounded" loading="lazy" alt="<?= l('index.dynamic_qr_image_alt') ?>" />
            </div>
        </div>
    </div>
<?php endif ?>

<?php if(settings()->codes->ai_qr_codes_is_enabled): ?>
    <div class="my-5">&nbsp;</div>

    <div class="container">
        <h2 class="text-center mb-5"><?= l('index.ai_qr_codes.header') ?> <i class="fas fa-fw fa-xs fa-robot text-primary ml-1"></i></h2>

        <div class="">
            <?php $ai_array = range(1, 12); ?>
            <?php $groups = array_chunk($ai_array, 6, true); ?>

            <?php for ($i = 0; $i < 2; $i++): ?>
                <div class="index-marquee-wrapper">
                    <?php $j = 1 ?>
                    <?php foreach($groups[$i] as $key => $value): ?>
                        <img src="<?= ASSETS_FULL_URL . 'images/index/ai/' . $value . '.png' ?>" class="img-fluid rounded index-marquee-item mx-3 my-2 zoom-animation-subtle index-marquee-item-<?= $i % 2 == 0 ? 'left' : 'right' ?>" loading="lazy" alt="<?= sprintf(l('index.ai_qr_codes.alt'), $j) ?>" style="--n: <?= $j++ ?>" />
                    <?php endforeach ?>
                </div>
            <?php endfor ?>
        </div>
    </div>
<?php endif ?>

<?php if(settings()->codes->qr_codes_is_enabled): ?>
    <div class="my-5">&nbsp;</div>

    <div class="container">
        <div class="text-center mb-5">
            <h2><?= l('index.qr_codes.header') ?></h2>
            <p class="text-muted mt-3"><?= l('index.qr_codes.subheader') ?></p>
        </div>

        <div class="row">
            <?php foreach($data->available_qr_codes as $key => $value): ?>
                <div class="col-12 col-md-6 col-lg-4 p-4">
                    <div class="card position-relative icon-zoom-animation h-100" data-toggle="tooltip" title="<?= l('qr_codes.type.' . $key . '_description') ?>">
                        <div class="card-body bg-gray-50 text-center d-flex flex-column justify-content-center">
                            <div class="mb-4"><i class="<?= $value['icon'] ?> fa-fw fa-2x text-primary"></i></div>
                            <h3 class="h4"><?= l('qr_codes.type.' . $key) ?></h3>

                            <a href="<?= url('qr/' . $key) ?>" class="btn btn-block btn-sm btn-light mt-4 text-muted stretched-link">
                                <?= sprintf(l('index.qr_codes.choose'), l('qr_codes.type.' . $key)) ?>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach ?>
        </div>
    </div>
<?php endif ?>



<?php if(settings()->codes->barcodes_is_enabled): ?>
    <div class="my-5">&nbsp;</div>

    <div class="container">
        <div class="text-center mb-5">
            <h2><i class="fas fa-fw fa-xs fa-barcode text-primary mr-2"></i><?= l('index.barcodes.header') ?></h2>
            <p class="text-muted mt-3"><?= l('index.barcodes.subheader') ?></p>
        </div>

        <div class="row">
            <?php foreach($data->available_barcodes as $key => $value): ?>
                <div class="col-12 col-md-6 col-lg-4 p-4">
                    <div class="card position-relative icon-zoom-animation h-100">
                        <div class="card-body bg-gray-50 text-center d-flex flex-column justify-content-center">
                            <h3 class="h4"><?= $key ?></h3>

                            <div class="d-flex justify-content-center mt-3 barcode-background">
                                <?php
                                $generator = new Picqer\Barcode\BarcodeGeneratorSVG();
                                echo $generator->getBarcode($value['example_value'], $key);
                                ?>
                            </div>

                            <a href="<?= url('barcode/' . str_replace('+', '-plus', $key)) ?>" class="btn btn-block btn-sm btn-light mt-4 text-muted stretched-link">
                                <?= sprintf(l('index.barcodes.choose'), $key) ?>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach ?>
        </div>
    </div>
<?php endif ?>

<div class="my-5">&nbsp;</div>

<div class="container">
    <div class="card rounded-2x py-4 bg-gray-900 border-0">
        <div class="card-body">
            <div class="text-center mb-4">
                <h2 class="text-white"><?= l('index.shortener_app_linking.header') ?></h2>
                <p class="text-muted"><?= l('index.shortener_app_linking.subheader') ?></p>
            </div>

            <div class="d-flex flex-wrap justify-content-center">
                <?php foreach(require APP_PATH . 'includes/app_linking.php' as $app_key => $app): ?>
                    <div class="bg-gray-800 rounded w-fit-content p-3 m-4 icon-zoom-animation" data-toggle="tooltip" title="<?= $app['name'] ?>">
                        <span title="<?= $app['name'] ?>"><i class="<?= $app['icon'] ?> fa-fw fa-xl mx-1" style="color: <?= $app['color'] ?>"></i></span>
                    </div>
                <?php endforeach ?>
            </div>
        </div>
    </div>
</div>

<?php if(settings()->links->pixels_is_enabled): ?>
    <div class="my-5">&nbsp;</div>

    <div class="container">
        <div class="card py-4 border-0">
            <div class="card-body">
                <div class="text-center mb-4">
                    <h2><?= l('index.pixels.header') ?></h2>
                    <p class="text-muted"><?= l('index.pixels.subheader') ?></p>
                </div>

                <div class="row no-gutters">
                    <?php $i = 0; ?>
                    <?php foreach(require APP_PATH . 'includes/l/pixels.php' as $item): ?>
                        <div class="col-12 col-md-6 col-lg-4 p-4" data-aos="fade-up" data-aos-delay="<?= $i * 100 ?>">
                            <div class="bg-gray-100 rounded-3x w-100 p-3 icon-zoom-animation text-truncate">
                                <i class="<?= $item['icon'] ?> fa-fw fa-lg mx-1" style="color: <?= $item['color'] ?>"></i>
                                <span class="h6"><?= $item['name'] ?></span>
                            </div>
                        </div>
                        <?php $i++ ?>
                    <?php endforeach ?>
                </div>
            </div>
        </div>
    </div>
<?php endif ?>

<?php if(settings()->main->api_is_enabled): ?>
    <div class="py-6"></div>

    <div class="container">
        <div class="row align-items-center justify-content-between" data-aos="fade-up">
            <div class="col-12 col-lg-5 mb-5 mb-lg-0 d-flex flex-column justify-content-center">
                <div class="text-uppercase font-weight-bold text-primary mb-3"><?= l('index.api.name') ?></div>

                <div>
                    <h2 class="mb-2"><?= l('index.api.header') ?></h2>
                    <p class="text-muted mb-4"><?= l('index.api.subheader') ?></p>

                    <div class="position-relative">
                        <div class="index-fade"></div>
                        <div class="row">
                            <div class="col">
                                <?php if(settings()->codes->ai_qr_codes_is_enabled): ?>
                                <div class="small mb-2"><i class="fas fa-fw fa-check-circle text-success mr-1"></i> <?= l('ai_qr_codes.title') ?></div>
                                <?php endif ?>

                                <?php if(settings()->codes->qr_codes_is_enabled): ?>
                                <div class="small mb-2"><i class="fas fa-fw fa-check-circle text-success mr-1"></i> <?= l('qr_codes.title') ?></div>
                                <?php endif ?>

                                <?php if(settings()->codes->barcodes_is_enabled): ?>
                                <div class="small mb-2"><i class="fas fa-fw fa-check-circle text-success mr-1"></i> <?= l('barcodes.title') ?></div>
                                <?php endif ?>

                                <div class="small mb-2"><i class="fas fa-fw fa-check-circle text-success mr-1"></i> <?= l('api_documentation.links') ?></div>
                            </div>

                            <div class="col">
                                <div class="small mb-2"><i class="fas fa-fw fa-check-circle text-success mr-1"></i> <?= l('api_documentation.statistics') ?></div>

                                <?php if(settings()->links->projects_is_enabled): ?>
                                <div class="small mb-2"><i class="fas fa-fw fa-check-circle text-success mr-1"></i> <?= l('projects.title') ?></div>
                                <?php endif ?>

                                <?php if(settings()->links->pixels_is_enabled): ?>
                                <div class="small mb-2"><i class="fas fa-fw fa-check-circle text-success mr-1"></i> <?= l('pixels.title') ?></div>
                                <?php endif ?>

                                <?php if(settings()->links->domains_is_enabled): ?>
                                <div class="small mb-2"><i class="fas fa-fw fa-check-circle text-success mr-1"></i> <?= l('domains.title') ?></div>
                                <?php endif ?>
                            </div>
                        </div>
                    </div>

                    <a href="<?= url('api-documentation') ?>" class="btn btn-block btn-outline-primary mt-5">
                        <?= l('api_documentation.menu') ?> <i class="fas fa-fw fa-xs fa-code ml-1"></i>
                    </a>
                </div>
            </div>

            <div class="col-12 col-lg-6">
                <div class="card rounded-2x bg-dark text-white">
                    <div class="card-body p-4 text-monospace reveal-effect font-size-small" style="line-height: 1.75">
                        curl --request POST \<br />
                        --url '<?= SITE_URL ?>api/links' \<br />
                        --header 'Authorization: Bearer <span class="text-primary" <?= is_logged_in() ? 'data-toggle="tooltip" title="' . l('api_documentation.api_key') . '"' : null ?>><?= is_logged_in() ? $this->user->api_key : '{api_key}' ?></span>' \<br />
                        --header 'Content-Type: multipart/form-data' \<br />
                        --form 'url=<span class="text-primary">example</span>' \<br />
                        --form 'location_url=<span class="text-primary"><?= SITE_URL ?></span>' \<br />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* hide until words are wrapped to avoid flash */
        .reveal-effect { visibility: hidden; }

        /* base state for each word */
        .reveal-effect-prepared .reveal-effect-word {
            opacity: 0;
            filter: blur(6px);
            transform: translate3d(0, 8px, 0);
            display: inline-block;
            transition: opacity .5s ease, filter .5s ease, transform .5s ease;
        }

        /* animate in when container gets .reveal-effect-in */
        .reveal-effect-prepared.reveal-effect-in .reveal-effect-word {
            opacity: 1;
            filter: blur(0);
            transform: none;
        }
    </style>

    <script defer>
        /* wrap words in a text node while preserving existing HTML */
        const wrap_words_in_text_node = (text_node) => {
            /* split into words + spaces, keep spacing intact */
            const tokens = text_node.textContent.split(/(\s+)/);
            const fragment = document.createDocumentFragment();

            tokens.forEach((token) => {
                if (token.trim().length === 0) {
                    fragment.appendChild(document.createTextNode(token));
                } else {
                    const span_node = document.createElement('span');
                    span_node.className = 'reveal-effect-word';
                    span_node.textContent = token;
                    fragment.appendChild(span_node);
                }
            });

            text_node.parentNode.replaceChild(fragment, text_node);
        };

        /* prepare a container: wrap only pure text nodes, not tags */
        const prepare_reveal_container = (container_node) => {
            /* collect first to avoid live-walking issues while replacing */
            const walker = document.createTreeWalker(
                container_node,
                NodeFilter.SHOW_TEXT,
                { acceptNode: (node) => node.textContent.trim().length ? NodeFilter.FILTER_ACCEPT : NodeFilter.FILTER_REJECT }
            );
            const text_nodes = [];
            while (walker.nextNode()) { text_nodes.push(walker.currentNode); }
            text_nodes.forEach(wrap_words_in_text_node);

            /* add stagger */
            const word_nodes = container_node.querySelectorAll('.reveal-effect-word');
            word_nodes.forEach((word_node, index) => {
                word_node.style.transitionDelay = (index * 40) + 'ms';
            });

            /* mark as prepared and reveal visibility */
            container_node.classList.add('reveal-effect-prepared');
            container_node.style.visibility = 'visible';
        };

        /* set up scroll trigger */
        document.addEventListener('DOMContentLoaded', () => {
            const container_node = document.querySelector('.reveal-effect');
            if (!container_node) { return; }

            /* prepare once (preserves HTML) */
            prepare_reveal_container(container_node);

            /* trigger when in view */
            const on_intersect = (entries, observer) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        /* start the animation */
                        container_node.classList.add('reveal-effect-in');
                        observer.unobserve(container_node);
                    }
                });
            };

            const intersection_observer = new IntersectionObserver(on_intersect, {
                root: null,
                rootMargin: '0px 0px -10% 0px',
                threshold: 0.1
            });

            intersection_observer.observe(container_node);
        });
    </script>
<?php endif ?>

<?php if(settings()->main->display_index_testimonials): ?>
    <div class="my-5">&nbsp;</div>

    <div class="p-4 mt-5">
        <div class="py-7 bg-primary-100 rounded-2x">
            <div class="container">
                <div class="text-center">
                    <h2><?= l('index.testimonials.header') ?> <i class="fas fa-fw fa-xs fa-check-circle text-primary"></i></h2>
                </div>

                <?php
                $language_array = \Altum\Language::get(\Altum\Language::$name);
                if(\Altum\Language::$main_name != \Altum\Language::$name) {
                    $language_array = array_merge(\Altum\Language::get(\Altum\Language::$main_name), $language_array);
                }

                $testimonials_language_keys = [];
                foreach ($language_array as $key => $value) {
                    if(preg_match('/index\.testimonials\.(\w+)\./', $key, $matches)) {
                        $testimonials_language_keys[] = $matches[1];
                    }
                }

                $testimonials_language_keys = array_unique($testimonials_language_keys);
                ?>

                <div class="row mt-8 mx-n3">
                    <?php foreach($testimonials_language_keys as $key => $value): ?>
                        <div class="col-12 col-lg-4 mb-7 mb-lg-0 px-4" data-aos="fade-up" data-aos-delay="<?= $key * 100 ?>">
                            <div class="card border-0 zoom-animation-subtle">
                                <div class="card-body">
                                    <img src="<?= get_custom_image_if_any('index/testimonial-' . $value . '.webp') ?>" class="img-fluid index-testimonial-avatar" alt="<?= l('index.testimonials.' . $value . '.name') . ', ' . l('index.testimonials.' . $value . '.attribute') ?>" loading="lazy" />

                                    <p class="mt-5">
                                        <span class="text-gray-800 font-weight-bold text-muted h5">“</span>
                                        <span><?= l('index.testimonials.' . $value . '.text') ?></span>
                                        <span class="text-gray-800 font-weight-bold text-muted h5">”</span>
                                    </p>

                                    <div class="blockquote-footer mt-4">
                                        <span class="font-weight-bold"><?= l('index.testimonials.' . $value . '.name') ?></span><br /> <span class="text-muted index-testimonial-comment"><?= l('index.testimonials.' . $value . '.attribute') ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach ?>
                </div>
            </div>
        </div>
    </div>
<?php endif ?>

<?php if(settings()->main->display_index_plans): ?>
    <div class="my-5">&nbsp;</div>

    <div id="plans" class="container">
        <div class="text-center mb-5">
            <h2><?= l('index.pricing.header') ?></h2>
            <p class="text-muted"><?= l('index.pricing.subheader') ?></p>
        </div>

        <?= $this->views['plans'] ?>
    </div>
<?php endif ?>

<?php if(settings()->main->display_index_faq): ?>
    <div class="my-5">&nbsp;</div>

    <div class="container">
        <div class="text-center mb-5">
            <h2><?= l('index.faq.header') ?></h2>
        </div>

        <?php
        $language_array = \Altum\Language::get(\Altum\Language::$name);
        if(\Altum\Language::$main_name != \Altum\Language::$name) {
            $language_array = array_merge(\Altum\Language::get(\Altum\Language::$main_name), $language_array);
        }

        $faq_language_keys = [];
        foreach ($language_array as $key => $value) {
            if(preg_match('/index\.faq\.(\w+)\./', $key, $matches)) {
                $faq_language_keys[] = $matches[1];
            }
        }

        $faq_language_keys = array_unique($faq_language_keys);
        ?>

        <div class="accordion index-faq" id="faq_accordion">
            <?php foreach($faq_language_keys as $key): ?>
                <div class="card">
                    <div class="card-body">
                        <div class="" id="<?= 'faq_accordion_' . $key ?>">
                            <h3 class="mb-0">
                                <button class="btn btn-lg font-weight-bold btn-block d-flex justify-content-between text-gray-800 px-0 icon-zoom-animation" type="button" data-toggle="collapse" data-target="<?= '#faq_accordion_answer_' . $key ?>" aria-expanded="true" aria-controls="<?= 'faq_accordion_answer_' . $key ?>">
                                    <span class="text-left"><?= l('index.faq.' . $key . '.question') ?></span>

                                    <span data-icon>
                                        <i class="fas fa-fw fa-circle-chevron-down"></i>
                                    </span>
                                </button>
                            </h3>
                        </div>

                        <div id="<?= 'faq_accordion_answer_' . $key ?>" class="collapse text-muted mt-2" aria-labelledby="<?= 'faq_accordion_' . $key ?>" data-parent="#faq_accordion">
                            <?= l('index.faq.' . $key . '.answer') ?>
                        </div>
                    </div>
                </div>
            <?php endforeach ?>
        </div>
    </div>

    <?php ob_start() ?>
    <script>
        'use strict';

        $('#faq_accordion').on('show.bs.collapse', event => {
            let svg = event.target.parentElement.querySelector('[data-icon] svg')
            svg.style.transform = 'rotate(180deg)';
            svg.style.color = 'var(--primary)';
        })

        $('#faq_accordion').on('hide.bs.collapse', event => {
            let svg = event.target.parentElement.querySelector('[data-icon] svg')
            svg.style.color = 'var(--primary-800)';
            svg.style.removeProperty('transform');
        })
    </script>
    <?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
<?php endif ?>

<?php if(settings()->users->register_is_enabled): ?>
    <div class="my-5">&nbsp;</div>

    <div class="container">
        <div class="card border-0 index-cta py-5 py-lg-6 rounded-2x" data-aos="fade-up">
            <div class="card-body">
                <div class="row align-items-center justify-content-center">
                    <div class="col-12 col-lg-5">
                        <div class="text-center text-lg-left mb-4 mb-lg-0">
                            <h2 class="h1"><?= l('index.cta.header') ?></h2>
                            <p class="h5"><?= l('index.cta.subheader') ?></p>
                        </div>
                    </div>

                    <div class="col-12 col-lg-5 mt-4 mt-lg-0">
                        <div class="text-center text-lg-right">
                            <?php if(is_logged_in()): ?>
                                <a href="<?= url('dashboard') ?>" class="btn btn-primary zoom-animation">
                                    <?= l('dashboard.menu') ?> <i class="fas fa-fw fa-arrow-right"></i>
                                </a>
                            <?php else: ?>
                                <a href="<?= url('register') ?>" class="btn btn-primary zoom-animation">
                                    <?= l('index.cta.register') ?> <i class="fas fa-fw fa-arrow-right"></i>
                                </a>
                            <?php endif ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif ?>


<?php if(count($data->blog_posts)): ?>
    <div class="my-5">&nbsp;</div>

    <div class="container">
        <div class="text-center mb-5">
            <h2><?= sprintf(l('index.blog.header'), '<span class="text-primary">', '</span>') ?></h2>
        </div>

        <div class="row">
            <?php foreach($data->blog_posts as $blog_post): ?>
                <div class="col-12 col-md-6 col-lg-4 p-4">
                    <div class="card h-100 zoom-animation-subtle">
                        <div class="card-body">
                            <?php if($blog_post->image): ?>
                                <a href="<?= SITE_URL . ($blog_post->language ? \Altum\Language::$active_languages[$blog_post->language] . '/' : null) . 'blog/' . $blog_post->url ?>" aria-label="<?= $blog_post->title ?>">
                                    <img src="<?= \Altum\Uploads::get_full_url('blog') . $blog_post->image ?>" class="blog-post-image-small img-fluid w-100 rounded mb-4" alt="<?= $blog_post->image_description ?>" loading="lazy" />
                                </a>
                            <?php endif ?>

                            <a href="<?= SITE_URL . ($blog_post->language ? \Altum\Language::$active_languages[$blog_post->language] . '/' : null) . 'blog/' . $blog_post->url ?>">
                                <h3 class="h5 card-title mb-2"><?= $blog_post->title ?></h3>
                            </a>

                            <p class="text-muted mb-0"><?= $blog_post->description ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach ?>
        </div>
    </div>
<?php endif ?>


<?php ob_start() ?>
<link rel="stylesheet" href="<?= ASSETS_FULL_URL . 'css/libraries/aos.min.css?v=' . PRODUCT_CODE ?>">
<?php \Altum\Event::add_content(ob_get_clean(), 'head') ?>

<?php ob_start() ?>
<script src="<?= ASSETS_FULL_URL . 'js/libraries/aos.min.js?v=' . PRODUCT_CODE ?>"></script>

<script>
    'use strict';
    
    AOS.init({
        duration: 600
    });
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>

<?php ob_start() ?>
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "Organization",
            "name": "<?= settings()->main->title ?>",
            "url": "<?= url() ?>",
            <?php if(settings()->main->{'logo_' . \Altum\ThemeStyle::get()}): ?>
            "logo": "<?= settings()->main->{'logo_' . \Altum\ThemeStyle::get() . '_full_url'} ?>",
            <?php endif ?>
            "slogan": "<?= l('index.header') ?>",
            "contactPoint": {
                "@type": "ContactPoint",
                "url": "<?= url('contact') ?>",
                "contactType": "Contact us"
            }
        }
    </script>

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
                }
            ]
        }
    </script>

<?php if(settings()->main->display_index_faq): ?>
    <?php
    $faqs = [];
    foreach($faq_language_keys as $key) {
        $faqs[] = [
            '@type' => 'Question',
            'name' => l('index.faq.' . $key . '.question'),
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text' => l('index.faq.' . $key . '.answer'),
            ]
        ];
    }
    ?>
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "FAQPage",
            "mainEntity": <?= json_encode($faqs) ?>
        }
    </script>
<?php endif ?>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>

<?php ob_start() ?>
    <link href="<?= ASSETS_FULL_URL . 'css/index-custom.css?v=' . PRODUCT_CODE ?>" rel="stylesheet" media="screen,print">
<?php \Altum\Event::add_content(ob_get_clean(), 'head') ?>
