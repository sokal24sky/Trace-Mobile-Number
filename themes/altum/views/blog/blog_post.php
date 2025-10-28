<?php defined('ALTUMCODE') || die() ?>

<div class="container <?= settings()->content->blog_columns == 1 ? 'col-lg-8' : null ?>">
    <?php if(settings()->main->breadcrumbs_is_enabled): ?>
        <nav aria-label="breadcrumb">
            <ol class="custom-breadcrumbs small">
                <li><a href="<?= url() ?>"><?= l('index.breadcrumb') ?></a> <i class="fas fa-fw fa-angle-right"></i></li>
                <li><a href="<?= url('blog') ?>"><?= l('blog.breadcrumb') ?></a> <i class="fas fa-fw fa-angle-right"></i></li>
                <?php if($data->blog_posts_category): ?>
                    <li><a href="<?= url('blog/category/' . $data->blog_posts_category->url) ?>"><?= $data->blog_posts_category->title ?></a> <i class="fas fa-fw fa-angle-right"></i></li>
                <?php endif ?>
                <li class="active" aria-current="page"><?= $data->blog_post->title ?></li>
            </ol>
        </nav>
    <?php endif ?>

    <div class="row">
        <div class="<?= settings()->content->blog_columns == 1 ? 'col-12 mb-5' : 'col-12 col-lg-8 mb-lg-0' ?>">
            <div class="card">
                <div class="card-body">
                    <?php if($data->blog_post->image): ?>
                        <img src="<?= \Altum\Uploads::get_full_url('blog') . $data->blog_post->image ?>" class="blog-post-image img-fluid w-100 rounded mb-3" alt="<?= $data->blog_post->image_description ?>" />
                    <?php endif ?>

                    <h1 class="h4 mb-1"><?= $data->blog_post->title ?></h1>

                    <p class="small text-muted">
                        <span data-toggle="tooltip" title="<?= sprintf(l('global.last_datetime_tooltip'), \Altum\Date::get($data->blog_post->last_datetime, 2)) ?>"><?= sprintf(l('global.datetime_tooltip'), \Altum\Date::get($data->blog_post->datetime, 2)) ?></span>

                        <?php if($data->blog_posts_category): ?>
                            • <a href="<?= SITE_URL . ($data->blog_posts_category->language ? \Altum\Language::$active_languages[$data->blog_posts_category->language] . '/' : null) . 'blog/category/' . $data->blog_posts_category->url ?>" class="text-muted"><?= $data->blog_posts_category->title ?></a>
                        <?php endif ?>

                        <?php if(settings()->content->blog_views_is_enabled): ?>
                            <span> • <?= sprintf(l('blog.total_views'), nr($data->blog_post->total_views)) ?></span>
                        <?php endif ?>

                        <?php $estimated_reading_time = string_estimate_reading_time($data->blog_post->content) ?>
                        <?php if($estimated_reading_time->minutes > 0 || $estimated_reading_time->seconds > 0): ?>
                            <span>•
                                <?= $estimated_reading_time->minutes ? sprintf(l('blog.estimated_reading_time'), $estimated_reading_time->minutes . ' ' . l('global.date.minutes')) : null ?>
                                <?= $estimated_reading_time->minutes == 0 && $estimated_reading_time->seconds ? sprintf(l('blog.estimated_reading_time'), $estimated_reading_time->seconds . ' ' . l('global.date.seconds')) : null ?>
                            </span>
                        <?php endif ?>
                    </p>

                    <div class="blog-post-content">
                        <p><?= $data->blog_post->description ?></p>

                        <div class="<?= $data->blog_post->editor == 'wysiwyg' ? 'ql-content' : null ?>">
                            <?= $data->blog_post->content ?>
                        </div>
                    </div>

                    <?= include_view(THEME_PATH . 'views/blog/ratings.php', [
                        'blog_post' => $data->blog_post,
                    ]); ?>
                </div>
            </div>

            <?php if(settings()->content->blog_share_is_enabled): ?>
                <div class="card mt-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                            <?= include_view(THEME_PATH . 'views/partials/share_buttons.php', ['url' => url(\Altum\Router::$original_request), 'class' => 'btn btn-gray-100', 'copy_to_clipboard' => true]) ?>
                        </div>
                    </div>
                </div>
            <?php endif ?>
        </div>

        <?php if(settings()->content->blog_popular_widget_is_enabled || settings()->content->blog_categories_widget_is_enabled || settings()->content->blog_search_widget_is_enabled): ?>
            <div class="<?= settings()->content->blog_columns == 1 ? 'col-12' : 'col-12 col-lg-4' ?>">
                <?php if(settings()->content->blog_search_widget_is_enabled): ?>
                    <div class="card mb-4">
                        <div class="card-body">
                            <form action="<?= url('blog') ?>" method="get" role="form">
                                <input type="hidden" name="search_by" value="title" />

                                <div class="input-group">
                                    <input type="search" name="search" class="form-control" value="<?= !empty($_GET['search']) ? input_clean($_GET['search']) : null ?>" placeholder="<?= l('global.search') ?>" aria-label="<?= l('global.search') ?>" />

                                    <div class="input-group-append">
                                        <button class="btn btn-outline-gray-300 text-dark" type="submit" data-toggle="tooltip" title="<?= l('global.submit') ?>"><i class="fas fa-fw fa-search"></i></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endif ?>

                <?php if(settings()->content->blog_categories_widget_is_enabled && count($data->blog_posts_categories)): ?>
                    <div class="card mb-4">
                        <div class="card-body">
                            <h3 class="h5 mb-3"><?= l('blog.categories') ?></h3>

                            <ul class="list-style-none m-0">
                                <?php foreach($data->blog_posts_categories as $blog_post_category): ?>
                                    <li class="mb-2">
                                        <a href="<?= SITE_URL . ($blog_post_category->language ? \Altum\Language::$active_languages[$blog_post_category->language] . '/' : null) . 'blog/category/' . $blog_post_category->url ?>"><?= $blog_post_category->title ?></a>
                                    </li>
                                <?php endforeach ?>
                            </ul>
                        </div>
                    </div>
                <?php endif ?>

                <?php if(settings()->content->blog_popular_widget_is_enabled && count($data->blog_posts_popular)): ?>
                    <div class="card mb-4">
                        <div class="card-body">
                            <h3 class="h5 mb-3"><?= l('blog.popular') ?></h3>

                            <ul class="list-style-none m-0">
                                <?php $i = 800; ?>
                                <?php foreach($data->blog_posts_popular as $blog_post): ?>
                                    <li class="mb-3 d-flex align-items-center">
                                        <div class="mr-3 rounded <?= 'bg-gray-' . $i ?>" style="min-width: 1.75rem; min-height: 1.75rem;border-radius: 50% !important;">
                                            &nbsp;
                                        </div>

                                        <?php $i = $i - 100; ?>

                                        <div>
                                            <a href="<?= SITE_URL . ($blog_post->language ? \Altum\Language::$active_languages[$blog_post->language] . '/' : null) . 'blog/' . $blog_post->url ?>" class="font-size-small"><?= $blog_post->title ?></a>
                                            <div class="small">
                                                <?php if($blog_post->blog_posts_category_id && isset($data->blog_posts_categories[$blog_post->blog_posts_category_id])): ?>
                                                    <a href="<?= SITE_URL . ($data->blog_posts_categories[$blog_post->blog_posts_category_id]->language ? \Altum\Language::$active_languages[$data->blog_posts_categories[$blog_post->blog_posts_category_id]->language] . '/' : null) . 'blog/category/' . $data->blog_posts_categories[$blog_post->blog_posts_category_id]->url ?>" class="text-muted"><?= $data->blog_posts_categories[$blog_post->blog_posts_category_id]->title ?></a>
                                                    <?php if(settings()->content->blog_views_is_enabled): ?>
                                                        <span class="text-muted"> • </span>
                                                    <?php endif ?>
                                                <?php endif ?>

                                                <?php if(settings()->content->blog_views_is_enabled): ?>
                                                    <span class="text-muted"><?= sprintf(l('blog.total_views'), nr($blog_post->total_views)) ?></span>
                                                <?php endif ?>
                                            </div>
                                        </div>
                                    </li>
                                <?php endforeach ?>
                            </ul>
                        </div>
                    </div>
                <?php endif ?>
            </div>
        <?php endif ?>
    </div>
</div>

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
                    "name": "<?= l('blog.title') ?>",
                    "item": "<?= url('blog') ?>"
                },
                {
                    "@type": "ListItem",
                    "position": 3,
                    "name": "<?= $data->blog_post->title ?>",
                    "item": "<?= SITE_URL . ($data->blog_post->language ? \Altum\Language::$active_languages[$data->blog_post->language] . '/' : null) . 'blog/' . $data->blog_post->url ?>"
                }
            ]
        }
</script>

<script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "BlogPosting",
        "headline": "<?= $data->blog_post->title ?>",
        "description": "<?= $data->blog_post->description ?>",
        "url": "<?= SITE_URL . ($data->blog_post->language ? \Altum\Language::$active_languages[$data->blog_post->language] . '/' : null) . 'blog/' . $data->blog_post->url ?>",
    <?php if($data->blog_post->image): ?>
        "image": "<?= \Altum\Uploads::get_full_url('blog') . $data->blog_post->image ?>",
        <?php endif ?>
    "author": {
        "@type": "Person",
        "name": "<?= settings()->main->title ?>",
            "url": "<?= SITE_URL ?>"
        },

    <?php if(settings()->content->blog_ratings_is_enabled && $data->blog_post->total_ratings > 0): ?>
        "aggregateRating": {
            "@type": "AggregateRating",
            "ratingValue": "<?= $data->blog_post->average_rating ?>",
            "reviewCount": "<?= $data->blog_post->total_ratings ?>",
            "itemReviewed" : {
                "@type": "Book",
                "name": "<?= $data->blog_post->title ?>"
            }
        },
        <?php endif ?>

    "publisher": {
        "@type": "Organization",
        "name": "<?= settings()->main->title ?>"
    <?php if(settings()->main->{'logo_' . \Altum\ThemeStyle::get()} != ''): ?>
            ,"logo": {
                "@type": "ImageObject",
                "url": "<?= settings()->main->{'logo_' . \Altum\ThemeStyle::get() . '_full_url'} ?>"
            }
            <?php endif ?>
    },
    "datePublished": "<?= (new \DateTime($data->blog_post->datetime))->format('Y-m-d\TH:i:sP') ?>",
        "dateModified": "<?= (new \DateTime($data->blog_post->last_datetime))->format('Y-m-d\TH:i:sP') ?>",
        "keywords": "<?= $data->blog_post->keywords ?>",
        "wordCount": "<?= str_word_count($data->blog_post->content ?? '') ?>",
        "mainEntityOfPage": {
            "@type": "WebPage",
            "@id": "<?= SITE_URL . ($data->blog_post->language ? \Altum\Language::$active_languages[$data->blog_post->language] . '/' : null) ?>"
        }
    }
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
