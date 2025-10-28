<?php defined('ALTUMCODE') || die() ?>

<div class="container">
    <?= \Altum\Alerts::output_alerts() ?>

    <?php if(settings()->main->breadcrumbs_is_enabled): ?>
        <nav aria-label="breadcrumb">
            <ol class="custom-breadcrumbs small">
                <li>
                    <a href="<?= url('teams-system') ?>"><?= l('teams_system.breadcrumb') ?></a><i class="fas fa-fw fa-angle-right"></i>
                </li>
                <li>
                    <a href="<?= url('teams') ?>"><?= l('teams.breadcrumb') ?></a><i class="fas fa-fw fa-angle-right"></i>
                </li>
                <li>
                    <a href="<?= url('team/' . $data->team->team_id) ?>"><?= l('team.breadcrumb') ?></a><i class="fas fa-fw fa-angle-right"></i>
                </li>
                <li class="active" aria-current="page"><?= l('team_member_create.breadcrumb') ?></li>
            </ol>
        </nav>
    <?php endif ?>

    <h1 class="h4 mb-4 text-truncate"><i class="fas fa-fw fa-xs fa-user-tag mr-1"></i> <?= l('team_member_create.header') ?></h1>

    <div class="card">
        <div class="card-body">

            <form action="" method="post" role="form">
                <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>" />

                <div class="form-group">
                    <label for="user_email"><i class="fas fa-fw fa-envelope fa-sm text-muted mr-1"></i> <?= l('global.email') ?></label>
                    <input type="email" id="user_email" name="user_email" class="form-control <?= \Altum\Alerts::has_field_errors('user_email') ? 'is-invalid' : null ?>" value="<?= $data->values['user_email'] ?>" placeholder="<?= l('global.email_placeholder') ?>" required="required" />
                    <?= \Altum\Alerts::output_field_error('user_email') ?>
                </div>

                <?php
/*
 * Copyright (c) 2025 AltumCode (https://altumcode.com/)
 *
 * This software is licensed exclusively by AltumCode and is sold only via https://altumcode.com/.
 * Unauthorized distribution, modification, or use of this software without a valid license is not permitted and may be subject to applicable legal actions.
 *
 * 🌍 View all other existing AltumCode projects via https://altumcode.com/
 * 📧 Get in touch for support or general queries via https://altumcode.com/contact
 * 📤 Download the latest version via https://altumcode.com/downloads
 *
 * 🐦 X/Twitter: https://x.com/AltumCode
 * 📘 Facebook: https://facebook.com/altumcode
 * 📸 Instagram: https://instagram.com/altumcode
 */

$icons = [
                    'read' => 'fa-eye',
                    'create' => 'fa-plus',
                    'update' => 'fa-edit',
                    'delete' => 'fa-trash'
                ]
                ?>

                <?php foreach($data->teams_access as $key => $value): ?>
                <div class="form-group">
                    <div class="d-flex align-items-center justify-content-between">
                        <label class="h6" for="access"><i class="fas fa-fw fa-sm <?= $icons[$key] ?> text-muted mr-1"></i> <?= sprintf(l('team_members.access'), l('team_members.access.' . $key)) ?></label>

                        <?php if($key != 'read'): ?>
                        <div>
                            <button type="button" class="btn btn-sm btn-light" data-toggle="tooltip" title="<?= l('global.select_all') ?>" data-tooltip-hide-on-click onclick="document.querySelectorAll(`<?= '#' . $key . '_container' ?> [name='access[]']`).forEach(element => element.checked ? null : element.checked = true)"><i class="fas fa-fw fa-check-square"></i></button>
                            <button type="button" class="btn btn-sm btn-light" data-toggle="tooltip" title="<?= l('global.deselect_all') ?>" data-tooltip-hide-on-click onclick="document.querySelectorAll(`<?= '#' . $key . '_container' ?> [name='access[]']`).forEach(element => element.checked ? element.checked = false : null)"><i class="fas fa-fw fa-minus-square"></i></button>
                        </div>
                        <?php endif ?>
                    </div>

                    <div id="<?= $key . '_container' ?>" class="row">
                        <?php foreach($data->teams_access[$key] as $access_key => $access_translation): ?>
                            <div class="col-12 col-lg-6">
                                <div class="custom-control custom-checkbox my-2">
                                    <input id="<?= 'access_' . $access_key ?>" name="access[]" value="<?= $access_key ?>" type="checkbox" class="custom-control-input" <?= in_array($access_key, $data->values['access']) ? 'checked="checked"' : null ?> <?= $key == 'read' ? 'disabled="disabled"' : null ?>>
                                    <label class="custom-control-label" for="<?= 'access_' . $access_key ?>">
                                        <span><?= $access_translation ?></span>
                                    </label>
                                </div>
                            </div>
                        <?php endforeach ?>
                    </div>
                </div>
                <?php endforeach ?>

                <div class="alert alert-info"><?= l('team_members.info_message.access') ?></div>

                <button type="submit" name="submit" class="btn btn-block btn-primary mt-3"><?= l('team_member_create.submit') ?></button>
            </form>

        </div>
    </div>
</div>
