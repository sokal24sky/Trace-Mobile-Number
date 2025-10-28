<?php defined('ALTUMCODE') || die() ?>

<?php if(!settings()->content->blog_is_enabled): ?>
    <div class="alert alert-info">
        <i class="fas fa-fw fa-info-circle mr-1"></i>
        <?= sprintf(l('global.info_message.admin_feature_disabled'), url('admin/settings/content')) ?>
    </div>
<?php endif ?>

<?php if(count($data->blog_posts) || $data->filters->has_applied_filters): ?>

    <div class="d-flex flex-column flex-md-row justify-content-between mb-4">
        <h1 class="h3 mb-3 mb-md-0 text-truncate"><i class="fas fa-fw fa-xs fa-paste text-primary-900 mr-2"></i> <?= l('admin_blog_posts.header') ?></h1>

        <div class="d-flex position-relative">
            <div>
                <a href="<?= url('admin/blog-post-create') ?>" class="btn btn-primary text-nowrap"><i class="fas fa-fw fa-plus-circle fa-sm mr-1"></i> <?= l('admin_blog_posts.create') ?></a>
            </div>

            <div class="ml-3">
                <div class="dropdown">
                    <button type="button" class="btn <?= $data->filters->has_applied_filters ? 'btn-secondary' : 'btn-gray-300' ?> filters-button dropdown-toggle-simple" data-toggle="dropdown" data-boundary="viewport" data-tooltip data-html="true" title="<?= l('global.filters.tooltip') ?>" data-tooltip-hide-on-click>
                        <i class="fas fa-fw fa-sm fa-filter"></i>
                    </button>

                    <div class="dropdown-menu dropdown-menu-right filters-dropdown">
                        <div class="dropdown-header d-flex justify-content-between">
                            <span class="h6 m-0"><?= l('global.filters.header') ?></span>

                            <?php if($data->filters->has_applied_filters): ?>
                                <a href="<?= url(\Altum\Router::$original_request) ?>" class="text-muted"><?= l('global.filters.reset') ?></a>
                            <?php endif ?>
                        </div>

                        <div class="dropdown-divider"></div>

                        <form action="" method="get" role="form">
                            <div class="form-group px-4">
                                <label for="filters_search" class="small"><?= l('global.filters.search') ?></label>
                                <input type="search" name="search" id="filters_search" class="form-control form-control-sm" value="<?= $data->filters->search ?>" />
                            </div>

                            <div class="form-group px-4">
                                <label for="filters_search_by" class="small"><?= l('global.filters.search_by') ?></label>
                                <select name="search_by" id="filters_search_by" class="custom-select custom-select-sm">
                                    <option value="title" <?= $data->filters->search_by == 'title' ? 'selected="selected"' : null ?>><?= l('admin_blog.title') ?></option>
                                    <option value="url" <?= $data->filters->search_by == 'url' ? 'selected="selected"' : null ?>><?= l('global.url') ?></option>
                                    <option value="description" <?= $data->filters->search_by == 'description' ? 'selected="selected"' : null ?>><?= l('global.description') ?></option>
                                    <option value="keywords" <?= $data->filters->search_by == 'keywords' ? 'selected="selected"' : null ?>><?= l('admin_blog.keywords') ?></option>
                                </select>
                            </div>

                            <div class="form-group px-4">
                                <label for="filters_blog_posts_category_id" class="small"><?= l('admin_blog.blog_posts_category_id') ?></label>
                                <select name="blog_posts_category_id" id="filters_blog_posts_category_id" class="custom-select custom-select-sm">
                                    <option value=""><?= l('global.all') ?></option>
                                    <?php foreach($data->blog_posts_categories as $blog_posts_category): ?>
                                        <option value="<?= $blog_posts_category->blog_posts_category_id ?>" <?= isset($data->filters->filters['blog_posts_category_id']) && $data->filters->filters['blog_posts_category_id'] == $blog_posts_category->blog_posts_category_id ? 'selected="selected"' : null ?>><?= $blog_posts_category->title ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>

                            <div class="form-group px-4">
                                <label for="filters_is_published" class="small"><?= l('admin_blog.is_published') ?></label>
                                <select name="is_published" id="filters_is_published" class="custom-select custom-select-sm">
                                    <option value=""><?= l('global.all') ?></option>
                                    <option value="1" <?= isset($data->filters->filters['is_published']) && $data->filters->filters['is_published'] ? 'selected="selected"' : null ?>><?= l('global.yes') ?></option>
                                    <option value="0" <?= isset($data->filters->filters['is_published']) && !$data->filters->filters['is_published'] ? 'selected="selected"' : null ?>><?= l('global.no') ?></option>
                                </select>
                            </div>

                            <div class="form-group px-4">
                                <label for="filters_order_by" class="small"><?= l('global.filters.order_by') ?></label>
                                <select name="order_by" id="filters_order_by" class="custom-select custom-select-sm">
                                    <option value="blog_post_id" <?= $data->filters->order_by == 'blog_post_id' ? 'selected="selected"' : null ?>><?= l('global.id') ?></option>
                                    <option value="datetime" <?= $data->filters->order_by == 'datetime' ? 'selected="selected"' : null ?>><?= l('global.filters.order_by_datetime') ?></option>
                                    <option value="last_datetime" <?= $data->filters->search_by == 'last_datetime' ? 'selected="selected"' : null ?>><?= l('global.filters.order_by_last_datetime') ?></option>
                                </select>
                            </div>

                            <div class="form-group px-4">
                                <label for="filters_order_type" class="small"><?= l('global.filters.order_type') ?></label>
                                <select name="order_type" id="filters_order_type" class="custom-select custom-select-sm">
                                    <option value="ASC" <?= $data->filters->order_type == 'ASC' ? 'selected="selected"' : null ?>><?= l('global.filters.order_type_asc') ?></option>
                                    <option value="DESC" <?= $data->filters->order_type == 'DESC' ? 'selected="selected"' : null ?>><?= l('global.filters.order_type_desc') ?></option>
                                </select>
                            </div>

                            <div class="form-group px-4">
                                <label for="filters_results_per_page" class="small"><?= l('global.filters.results_per_page') ?></label>
                                <select name="results_per_page" id="filters_results_per_page" class="custom-select custom-select-sm">
                                    <?php foreach($data->filters->allowed_results_per_page as $key): ?>
                                        <option value="<?= $key ?>" <?= $data->filters->results_per_page == $key ? 'selected="selected"' : null ?>><?= $key ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>

                            <div class="form-group px-4 mt-4">
                                <button type="submit" name="submit" class="btn btn-sm btn-primary btn-block"><?= l('global.submit') ?></button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>

            <div class="ml-3">
                <button id="bulk_enable" type="button" class="btn btn-gray-300" data-toggle="tooltip" title="<?= l('global.bulk_actions') ?>"><i class="fas fa-fw fa-sm fa-list"></i></button>

                <div id="bulk_group" class="btn-group d-none" role="group">
                    <div class="btn-group dropdown" role="group">
                        <button id="bulk_actions" type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" data-boundary="viewport" aria-haspopup="true" aria-expanded="false">
                            <?= l('global.bulk_actions') ?> <span id="bulk_counter" class="d-none"></span>
                        </button>
                        <div class="dropdown-menu" aria-labelledby="bulk_actions">
                            <a href="#" class="dropdown-item" data-toggle="modal" data-target="#bulk_delete_modal"><i class="fas fa-fw fa-sm fa-trash-alt mr-2"></i> <?= l('global.delete') ?></a>
                        </div>
                    </div>

                    <button id="bulk_disable" type="button" class="btn btn-secondary" data-toggle="tooltip" title="<?= l('global.close') ?>"><i class="fas fa-fw fa-times"></i></button>
                </div>
            </div>
        </div>
    </div>

    <?= \Altum\Alerts::output_alerts() ?>

    <form id="table" action="<?= SITE_URL . 'admin/blog-posts/bulk' ?>" method="post" role="form">
        <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>" />
        <input type="hidden" name="type" value="" data-bulk-type />
        <input type="hidden" name="original_request" value="<?= base64_encode(\Altum\Router::$original_request) ?>" />
        <input type="hidden" name="original_request_query" value="<?= base64_encode(\Altum\Router::$original_request_query) ?>" />

        <div class="table-responsive table-custom-container">
            <table class="table table-custom">
                <thead>
                <tr>
                    <th data-bulk-table class="d-none">
                        <div class="custom-control custom-checkbox">
                            <input id="bulk_select_all" type="checkbox" class="custom-control-input" />
                            <label class="custom-control-label" for="bulk_select_all"></label>
                        </div>
                    </th>
                    <th><?= l('admin_blog_posts.table.blog_post') ?></th>
                    <th><?= l('global.language') ?></th>
                    <th><?= l('admin_blog.blog_posts_category_id') ?></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach($data->blog_posts as $row): ?>
                    <tr>
                        <td data-bulk-table class="d-none">
                            <div class="custom-control custom-checkbox">
                                <input id="selected_id_<?= $row->blog_post_id ?>" type="checkbox" class="custom-control-input" name="selected[]" value="<?= $row->blog_post_id ?>" />
                                <label class="custom-control-label" for="selected_id_<?= $row->blog_post_id ?>"></label>
                            </div>
                        </td>

                        <td class="text-nowrap">
                            <div class="d-flex">
                                <a href="<?= url('admin/blog-post-update/' . $row->blog_post_id) ?>" class="mr-3">
                                    <?php if($row->image): ?>
                                        <img src="<?= \Altum\Uploads::get_full_url('blog') . $row->image ?>" class="user-avatar rounded-circle" alt="" loading="lazy" />
                                    <?php else: ?>
                                        <div class="user-avatar rounded-circle"></div>
                                    <?php endif ?>
                                </a>

                                <div class="d-flex flex-column text-truncate">
                                    <div>
                                        <a href="<?= url('admin/blog-post-update/' . $row->blog_post_id) ?>" title="<?= $row->title ?>"><?= string_truncate($row->title, 32) ?></a>
                                    </div>

                                    <div class="small text-muted">
                                        <img referrerpolicy="no-referrer" src="<?= get_favicon_url_from_domain(parse_url($row->url, PHP_URL_HOST) ?? '') ?>" class="img-fluid icon-favicon-small mr-1" loading="lazy" />

                                        <span title="<?= remove_url_protocol_from_url($row->url) ?>"><?= string_truncate(remove_url_protocol_from_url($row->url), 32) ?></span>

                                        <a href="<?= SITE_URL . ($row->language ? \Altum\Language::$active_languages[$row->language] . '/' : null) . 'blog/' . $row->url ?>" target="_blank" rel="noreferrer">
                                            <i class="fas fa-fw fa-xs fa-external-link-alt text-muted ml-1"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </td>

                        <td class="text-nowrap">
                            <span class="badge badge-light">
                                <i class="fas fa-fw fa-sm fa-language mr-1"></i> <?= $row->language ?? l('global.all') ?>
                            </span>
                        </td>

                        <td class="text-nowrap">

                            <?php if($row->blog_posts_category_id): ?>
                                <a href="<?= url('admin/blog-posts-category-update/' . $row->blog_posts_category_id) ?>" class="badge badge-info">
                                    <?= $data->blog_posts_categories[$row->blog_posts_category_id]->title ?? null ?>
                                </a>
                            <?php else: ?>
                                <div class="badge badge-info">
                                    <?= l('global.none') ?>
                                </div>

                            <?php endif ?>
                        </td>

                        <td class="text-nowrap text-muted">
                            <div class="d-flex flex-column">
                                <span class="badge badge-light">
                                    <i class="fas fa-fw fa-sm fa-eye mr-1"></i> <?= sprintf(l('admin_blog_posts.total_views'), nr($row->total_views)) ?>
                                </span>

                                <span class="badge badge-warning mt-2" data-toggle="tooltip" title="<?= sprintf(l('admin_blog_posts.average_rating'), nr(($row->average_rating ?? 0), 2, false)) ?>">
                                    <i class="fas fa-fw fa-sm fa-star mr-1"></i> <?= sprintf(l('admin_blog_posts.total_ratings'), nr($row->total_ratings)) ?>
                                </span>
                            </div>
                        </td>

                        <td class="text-nowrap">
                            <div class="d-flex align-items-center">
                                <span class="mr-2" data-toggle="tooltip" data-html="true" title="<?= sprintf(l('global.datetime_tooltip'), '<br />' . \Altum\Date::get($row->datetime, 2) . '<br /><small>' . \Altum\Date::get($row->datetime, 3) . '</small>' . '<br /><small>(' . \Altum\Date::get_timeago($row->datetime) . ')</small>') ?>">
                                    <i class="fas fa-fw fa-calendar text-muted"></i>
                                </span>

                                <span class="mr-2" data-toggle="tooltip" data-html="true" title="<?= sprintf(l('global.last_datetime_tooltip'), ($row->last_datetime ? '<br />' . \Altum\Date::get($row->last_datetime, 2) . '<br /><small>' . \Altum\Date::get($row->last_datetime, 3) . '</small>' . '<br /><small>(' . \Altum\Date::get_timeago($row->last_datetime) . ')</small>' : '<br />' . l('global.na'))) ?>">
                                    <i class="fas fa-fw fa-history text-muted"></i>
                                </span>
                            </div>
                        </td>

                        <td>
                            <div class="d-flex justify-content-end">
                                <?= include_view(THEME_PATH . 'views/admin/blog-posts/admin_blog_post_dropdown_button.php', ['id' => $row->blog_post_id, 'resource_name' => $row->title, 'url' => $row->url, 'language' => $row->language]) ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </form>

    <div class="mt-3"><?= $data->pagination ?></div>

<?php else: ?>

    <?= \Altum\Alerts::output_alerts() ?>

    <div class="card">
        <div class="card-body">
            <div class="d-flex flex-column flex-md-row align-items-md-center">
                <div class="mb-3 mb-md-0 mr-md-5">
                    <i class="fas fa-fw fa-7x fa-paste text-primary-200"></i>
                </div>

                <div class="d-flex flex-column">
                    <h1 class="h3 m-0"><?= l('admin_blog_posts.header_no_data') ?></h1>
                    <p class="text-muted"><?= l('admin_blog_posts.subheader_no_data') ?></p>

                    <div>
                        <a href="<?= url('admin/blog-post-create') ?>" class="btn btn-primary text-nowrap"><i class="fas fa-fw fa-plus-circle fa-sm mr-1"></i> <?= l('admin_blog_posts.create') ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php endif ?>

<?php require THEME_PATH . 'views/partials/js_bulk.php' ?>
<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/partials/bulk_delete_modal.php'), 'modals'); ?>
<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/partials/universal_delete_modal_url.php', [
    'name' => 'blog_post',
    'resource_id' => 'blog_post_id',
    'has_dynamic_resource_name' => true,
    'path' => 'admin/blog-posts/delete/'
]), 'modals'); ?>

