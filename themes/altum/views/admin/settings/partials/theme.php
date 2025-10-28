<?php defined('ALTUMCODE') || die() ?>

<?php
$fonts = [
    'Times New Roman, Times, serif',
    'serif',
    'Georgia, serif',
    'monospace',
    'Courier, monospace',
    'Arial, sans-serif',
    'Helvetica, Arial, sans-serif',
    'Verdana, sans-serif',
    'Tahoma, Geneva, sans-serif',
    'Trebuchet MS, sans-serif',
    'Courier New, Courier, monospace',
    'Monaco, monospace',
    'Comic Sans MS, cursive',
    'Impact, fantasy',
    'Baskerville, serif',
    'Papyrus, fantasy',
    'Lucida Console, Monaco, monospace',
    'Lucida Sans Unicode, Lucida Grande, sans-serif',
    'Garamond, serif',
    'Palatino Linotype, Book Antiqua, Palatino, serif',
    'Candara, Calibri, Segoe, sans-serif',
    'Segoe UI, Tahoma, Geneva, sans-serif'
];
?>

<div>
    <ul class="nav nav-pills d-flex flex-fill flex-column flex-lg-row mb-3" role="tablist">
        <li class="nav-item flex-fill text-center" role="presentation">
            <a class="nav-link active" id="pills-light-tab" data-toggle="pill" href="#pills-light" role="tab" aria-controls="pills-light" aria-selected="true">
                <i class="fas fa-fw fa-sm fa-sun mr-1"></i> <?= l('admin_settings.theme.light') ?>
            </a>
        </li>
        <li class="nav-item flex-fill text-center" role="presentation">
            <a class="nav-link" id="pills-dark-tab" data-toggle="pill" href="#pills-dark" role="tab" aria-controls="pills-dark" aria-selected="false">
                <i class="fas fa-fw fa-sm fa-moon mr-1"></i> <?= l('admin_settings.theme.dark') ?>
            </a>
        </li>
    </ul>

    <?php
    $defaults = [
        'light' => [
            'primary' => [
                '50'  => '#eff8ff',
                '100' => '#e1f2fe',
                '200' => '#b9e5fc',
                '300' => '#82d3fb',
                '400' => '#3fbaf8',
                '500' => '#0ea5ea',
                '600' => '#1180c5',
                '700' => '#0c68a1',
                '800' => '#0d5a86',
                '900' => '#0f496f',
            ],
            'gray' => [
                '50'  => '#fcfcfc',
                '100' => '#f6f7f8',
                '200' => '#f0f2f3',
                '300' => '#e5e7ea',
                '400' => '#a6afb9',
                '500' => '#9ba4b0',
                '600' => '#6b7789',
                '700' => '#4c5361',
                '800' => '#31363e',
                '900' => '#1d1f25',
            ]
        ],
        'dark' => [
            'primary' => [
                '900' => '#cde9fd',
                '800' => '#a1dcfc',
                '700' => '#6eccfb',
                '600' => '#2bb3f7',
                '500' => '#0e9ee1',
                '400' => '#0f76b6',
                '300' => '#0b5e93',
                '200' => '#0b4d74',
                '100' => '#0e4062',
                '50'  => '#010b12',
            ],
            'gray' => [
                '900' => '#f0f2f3',
                '800' => '#d7dbdf',
                '700' => '#b4bbc4',
                '600' => '#a0aab4',
                '500' => '#707d8e',
                '400' => '#606b7a',
                '300' => '#3c424d',
                '200' => '#2f333b',
                '100' => '#181a1f',
                '50'  => '#0d0e11',
                '25'  => '#0a0c10',
            ]
        ]
    ];
    ?>

    <?php $admin_primary_themes = require APP_PATH . 'includes/admin_primary_themes.php'; ?>
    <?php $admin_gray_themes = require APP_PATH . 'includes/admin_gray_themes.php'; ?>

    <div class="tab-content">
        <div class="tab-pane fade show active" id="pills-light" role="tabpanel" aria-labelledby="pills-light-tab">
            <?php $mode = 'light' ?>

            <div class="form-group custom-control custom-switch">
                <input id="<?= $mode . '_is_enabled' ?>" name="<?= $mode . '_is_enabled' ?>" type="checkbox" class="custom-control-input" <?= settings()->theme->{$mode . '_is_enabled'} ? 'checked="checked"' : null ?>>
                <label class="custom-control-label" for="<?= $mode . '_is_enabled' ?>"><?= l('global.enable') ?></label>
            </div>

            <div class="form-group">
                <label><?= l('admin_settings.theme.primary_theme') ?></label>
                <div class="row m-n2 d-flex align-items-stretch form-group">
                    <?php foreach($admin_primary_themes[$mode] as $key => $theme): ?>
                        <label class="col-4 p-2 custom-radio-box m-0">
                            <input
                                    type="radio"
                                    id="<?= $mode . '_primary_theme' ?>"
                                    name="<?= $mode . '_primary_theme' ?>"
                                    value="<?= $key ?>" class="custom-control-input"
                                <?= settings()->theme->{$mode . '_primary_theme'} == $key ? 'checked="checked"' : null ?>
                                <?php foreach($theme['colors'] as $shade => $color): ?>
                                    data-<?= $shade ?>="<?= $color ?>"
                                <?php endforeach ?>
                            >

                            <div class="card" style="background-color: <?= $theme['color'] ?>; color: white;">
                                <div class="card-body d-flex align-items-center justify-content-between">
                                    <div class="card-title mb-0"><?= $theme['name'] ?></div>

                                    <div>
                                        <small><?= $theme['color'] ?></small>
                                    </div>
                                </div>
                            </div>
                        </label>
                    <?php endforeach ?>

                    <label class="col-4 p-2 custom-radio-box m-0">
                        <input
                                type="radio"
                                id="<?= $mode . '_primary_theme' ?>"
                                name="<?= $mode . '_primary_theme' ?>"
                                value="custom" class="custom-control-input"
                            <?= settings()->theme->{$mode . '_primary_theme'} == 'custom' ? 'checked="checked"' : null ?>
                        >

                        <div class="card">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <div class="card-title mb-0"><?= l('admin_settings.theme.advanced_coloring') ?></div>
                            </div>
                        </div>
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label><?= l('admin_settings.theme.gray_theme') ?></label>
                <div class="row m-n2 d-flex align-items-stretch form-group">
                    <?php foreach($admin_gray_themes[$mode] as $key => $theme): ?>
                        <label class="col-4 p-2 custom-radio-box m-0">
                            <input
                                    type="radio"
                                    id="<?= $mode . '_gray_theme' ?>"
                                    name="<?= $mode . '_gray_theme' ?>"
                                    value="<?= $key ?>"
                                    class="custom-control-input"
                                <?= settings()->theme->{$mode . '_gray_theme'} == $key ? 'checked="checked"' : null ?>
                                <?php foreach($theme['colors'] as $shade => $color): ?>
                                    data-<?= $shade ?>="<?= $color ?>"
                                <?php endforeach ?>
                            >

                            <div class="card" style="background-color: <?= $theme['color'] ?>; color: white;">
                                <div class="card-body d-flex align-items-center justify-content-between">
                                    <div class="card-title mb-0"><?= $theme['name'] ?></div>

                                    <div>
                                        <small><?= $theme['color'] ?></small>
                                    </div>
                                </div>
                            </div>
                        </label>
                    <?php endforeach ?>

                    <label class="col-4 p-2 custom-radio-box m-0">
                        <input
                                type="radio"
                                id="<?= $mode . '_gray_theme' ?>"
                                name="<?= $mode . '_gray_theme' ?>"
                                value="custom"
                                class="custom-control-input"
                            <?= settings()->theme->{$mode . '_gray_theme'} == 'custom' ? 'checked="checked"' : null ?>
                        >

                        <div class="card">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <div class="card-title mb-0"><?= l('admin_settings.theme.advanced_coloring') ?></div>
                            </div>
                        </div>
                    </label>
                </div>
            </div>

            <button class="btn btn-block btn-gray-200 mb-4" type="button" data-toggle="collapse" data-target="#<?= $mode . '_advanced_container' ?>" aria-expanded="false" aria-controls="<?= $mode . '_advanced_container' ?>">
                <i class="fas fa-fw fa-paintbrush fa-sm mr-1"></i> <?= l('admin_settings.theme.advanced_coloring') ?>
            </button>

            <div class="collapse" id="<?= $mode . '_advanced_container' ?>">
                <h2 class="h6"><?= l('admin_settings.theme.primary') ?></h2>
                <p class="text-muted"><?= l('admin_settings.theme.primary_help') ?></p>

                <div id="<?= $mode . '_primary_advanced_container' ?>">
                    <?php foreach(['50', '100', '200', '300', '400', '500', '600', '700', '800', '900'] as $key): ?>
                        <div class="form-group">
                            <label for="<?= $mode . '_primary_' . $key ?>">Primary <?= $key ?></label>
                            <input id="<?= $mode . '_primary_' . $key ?>" type="hidden" name="<?= $mode . '_primary_' . $key ?>" class="form-control" value="<?= settings()->theme->{$mode . '_primary_' . $key} ?? $defaults[$mode]['primary'][$key] ?>" data-color-picker />
                        </div>
                    <?php endforeach ?>
                </div>

                <h2 class="h6"><?= l('admin_settings.theme.gray') ?></h2>
                <p class="text-muted"><?= l('admin_settings.theme.gray_help') ?></p>

                <div id="<?= $mode . '_gray_advanced_container' ?>">
                    <?php foreach(['50', '100', '200', '300', '400', '500', '600', '700', '800', '900'] as $key): ?>
                        <div class="form-group">
                            <label for="<?= $mode . '_gray_' . $key ?>">Gray <?= $key ?></label>
                            <input id="<?= $mode . '_gray_' . $key ?>" type="hidden" name="<?= $mode . '_gray_' . $key ?>" class="form-control" value="<?= settings()->theme->{$mode . '_gray_' . $key} ?? $defaults[$mode]['gray'][$key] ?>" data-color-picker />
                        </div>
                    <?php endforeach ?>
                </div>
            </div>

            <h2 class="h6"><?= l('admin_settings.theme.others') ?></h2>

            <div class="form-group">
                <label><?= l('admin_settings.theme.font_family') ?></label>
                <div class="row m-n2 d-flex align-items-stretch form-group">
                    <label class="col-4 p-2 custom-radio-box m-0">
                        <input
                                type="radio"
                                id="<?= $mode . '_font_family' ?>"
                                name="<?= $mode . '_font_family' ?>"
                                value="default"
                                class="custom-control-input"
                            <?= settings()->theme->{$mode . '_font_family'} == 'default' ? 'checked="checked"' : null ?>
                        >

                        <div class="card">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <div class="card-title mb-0"><?= l('admin_settings.theme.font_family_default') ?></div>
                            </div>
                        </div>
                    </label>

                    <?php foreach($fonts as $font_family): ?>
                        <label class="col-4 p-2 custom-radio-box m-0">
                            <input
                                    type="radio"
                                    id="<?= $mode . '_font_family' ?>"
                                    name="<?= $mode . '_font_family' ?>"
                                    value="<?= $font_family ?>"
                                    class="custom-control-input"
                                <?= settings()->theme->{$mode . '_font_family'} == $font_family ? 'checked="checked"' : null ?>
                            >

                            <div class="card" style="font-family: <?= $font_family ?> !important;">
                                <div class="card-body d-flex align-items-center justify-content-between">
                                    <div class="card-title text-truncate mb-0"><?= strtok($font_family, ',') ?></div>
                                </div>
                            </div>
                        </label>
                    <?php endforeach ?>

                    <label class="col-4 p-2 custom-radio-box m-0">
                        <input
                                type="radio"
                                id="<?= $mode . '_font_family' ?>"
                                name="<?= $mode . '_font_family' ?>"
                                value="custom"
                                class="custom-control-input"
                            <?= !in_array(settings()->theme->{$mode . '_font_family'}, $fonts) ? 'checked="checked"' : null ?>
                        >

                        <div class="card">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <div class="card-title mb-0"><?= l('admin_settings.theme.font_family_custom') ?></div>
                            </div>
                        </div>
                    </label>
                </div>
            </div>

            <div class="form-group" id="<?= $mode . '_font_family_container' ?>">
                <label for="<?= $mode . '_font_family_custom' ?>"><?= l('admin_settings.theme.font_family') ?></label>
                <input id="<?= $mode . '_font_family_custom' ?>" name="<?= $mode . '_font_family_custom' ?>" type="text" class="form-control" value="<?= settings()->theme->{$mode . '_font_family'} ?? null ?>" />
                <small class="form-text text-muted"><?= l('admin_settings.theme.font_family_help') ?></small>
            </div>

            <div class="form-group" data-range-counter data-range-counter-suffix="rem">
                <label for="<?= $mode . '_border_radius' ?>"><?= l('admin_settings.theme.border_radius') ?></label>
                <input id="<?= $mode . '_border_radius' ?>" name="<?= $mode . '_border_radius' ?>" type="range" step=".01" min="0" max="1" class="form-control-range" value="<?= settings()->theme->{$mode . '_border_radius'} ?? null ?>" />
            </div>

        </div>





        <div class="tab-pane fade" id="pills-dark" role="tabpanel" aria-labelledby="pills-dark-tab">
            <?php $mode = 'dark' ?>

            <div class="form-group custom-control custom-switch">
                <input id="<?= $mode . '_is_enabled' ?>" name="<?= $mode . '_is_enabled' ?>" type="checkbox" class="custom-control-input" <?= settings()->theme->{$mode . '_is_enabled'} ? 'checked="checked"' : null ?>>
                <label class="custom-control-label" for="<?= $mode . '_is_enabled' ?>"><?= l('global.enable') ?></label>
            </div>

            <div class="form-group">
                <label><?= l('admin_settings.theme.primary_theme') ?></label>
                <div class="row m-n2 d-flex align-items-stretch form-group">
                    <?php foreach($admin_primary_themes[$mode] as $key => $theme): ?>
                        <label class="col-4 p-2 custom-radio-box m-0">
                            <input
                                    type="radio"
                                    id="<?= $mode . '_primary_theme' ?>"
                                    name="<?= $mode . '_primary_theme' ?>"
                                    value="<?= $key ?>" class="custom-control-input"
                                <?= settings()->theme->{$mode . '_primary_theme'} == $key ? 'checked="checked"' : null ?>
                                <?php foreach($theme['colors'] as $shade => $color): ?>
                                    data-<?= $shade ?>="<?= $color ?>"
                                <?php endforeach ?>
                            >

                            <div class="card" style="background-color: <?= $theme['color'] ?>; color: white;">
                                <div class="card-body d-flex align-items-center justify-content-between">
                                    <div class="card-title mb-0"><?= $theme['name'] ?></div>

                                    <div>
                                        <small><?= $theme['color'] ?></small>
                                    </div>
                                </div>
                            </div>
                        </label>
                    <?php endforeach ?>

                    <label class="col-4 p-2 custom-radio-box m-0">
                        <input
                                type="radio"
                                id="<?= $mode . '_primary_theme' ?>"
                                name="<?= $mode . '_primary_theme' ?>"
                                value="custom" class="custom-control-input"
                            <?= settings()->theme->{$mode . '_primary_theme'} == 'custom' ? 'checked="checked"' : null ?>
                        >

                        <div class="card">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <div class="card-title mb-0"><?= l('admin_settings.theme.advanced_coloring') ?></div>
                            </div>
                        </div>
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label><?= l('admin_settings.theme.gray_theme') ?></label>
                <div class="row m-n2 d-flex align-items-stretch form-group">
                    <?php foreach($admin_gray_themes[$mode] as $key => $theme): ?>
                        <label class="col-4 p-2 custom-radio-box m-0">
                            <input
                                    type="radio"
                                    id="<?= $mode . '_gray_theme' ?>"
                                    name="<?= $mode . '_gray_theme' ?>"
                                    value="<?= $key ?>"
                                    class="custom-control-input"
                                <?= settings()->theme->{$mode . '_gray_theme'} == $key ? 'checked="checked"' : null ?>
                                <?php foreach($theme['colors'] as $shade => $color): ?>
                                    data-<?= $shade ?>="<?= $color ?>"
                                <?php endforeach ?>
                            >

                            <div class="card" style="background-color: <?= $theme['color'] ?>; color: white;">
                                <div class="card-body d-flex align-items-center justify-content-between">
                                    <div class="card-title mb-0"><?= $theme['name'] ?></div>

                                    <div>
                                        <small><?= $theme['color'] ?></small>
                                    </div>
                                </div>
                            </div>
                        </label>
                    <?php endforeach ?>

                    <label class="col-4 p-2 custom-radio-box m-0">
                        <input
                                type="radio"
                                id="<?= $mode . '_gray_theme' ?>"
                                name="<?= $mode . '_gray_theme' ?>"
                                value="custom"
                                class="custom-control-input"
                            <?= settings()->theme->{$mode . '_gray_theme'} == 'custom' ? 'checked="checked"' : null ?>
                        >

                        <div class="card">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <div class="card-title mb-0"><?= l('admin_settings.theme.advanced_coloring') ?></div>
                            </div>
                        </div>
                    </label>
                </div>
            </div>

            <button class="btn btn-block btn-gray-200 mb-4" type="button" data-toggle="collapse" data-target="#<?= $mode . '_advanced_container' ?>" aria-expanded="false" aria-controls="<?= $mode . '_advanced_container' ?>">
                <i class="fas fa-fw fa-paintbrush fa-sm mr-1"></i> <?= l('admin_settings.theme.advanced_coloring') ?>
            </button>

            <div class="collapse" id="<?= $mode . '_advanced_container' ?>">
                <h2 class="h6"><?= l('admin_settings.theme.primary') ?></h2>
                <p class="text-muted"><?= l('admin_settings.theme.primary_help') ?></p>

                <div id="<?= $mode . '_primary_advanced_container' ?>">
                    <?php foreach(array_reverse(['50', '100', '200', '300', '400', '500', '600', '700', '800', '900']) as $key): ?>
                        <div class="form-group">
                            <label for="<?= $mode . '_primary_' . $key ?>">Primary <?= $key ?></label>
                            <input id="<?= $mode . '_primary_' . $key ?>" type="hidden" name="<?= $mode . '_primary_' . $key ?>" class="form-control" value="<?= settings()->theme->{$mode . '_primary_' . $key} ?? $defaults[$mode]['primary'][$key] ?>" data-color-picker />
                        </div>
                    <?php endforeach ?>
                </div>

                <h2 class="h6"><?= l('admin_settings.theme.gray') ?></h2>
                <p class="text-muted"><?= l('admin_settings.theme.gray_help') ?></p>

                <div id="<?= $mode . '_gray_advanced_container' ?>">
                    <?php foreach(array_reverse(['50', '100', '200', '300', '400', '500', '600', '700', '800', '900']) as $key): ?>
                        <div class="form-group">
                            <label for="<?= $mode . '_gray_' . $key ?>">Gray <?= $key ?></label>
                            <input id="<?= $mode . '_gray_' . $key ?>" type="hidden" name="<?= $mode . '_gray_' . $key ?>" class="form-control" value="<?= settings()->theme->{$mode . '_gray_' . $key} ?? $defaults[$mode]['gray'][$key] ?>" data-color-picker />
                        </div>
                    <?php endforeach ?>
                </div>
            </div>

            <h2 class="h6"><?= l('admin_settings.theme.others') ?></h2>

            <div class="form-group">
                <label><?= l('admin_settings.theme.font_family') ?></label>
                <div class="row m-n2 d-flex align-items-stretch form-group">
                    <label class="col-4 p-2 custom-radio-box m-0">
                        <input
                                type="radio"
                                id="<?= $mode . '_font_family' ?>"
                                name="<?= $mode . '_font_family' ?>"
                                value="default"
                                class="custom-control-input"
                                <?= settings()->theme->{$mode . '_font_family'} == 'default' ? 'checked="checked"' : null ?>
                        >

                        <div class="card">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <div class="card-title mb-0"><?= l('admin_settings.theme.font_family_default') ?></div>
                            </div>
                        </div>
                    </label>

                    <?php foreach($fonts as $font_family): ?>
                        <label class="col-4 p-2 custom-radio-box m-0">
                            <input
                                    type="radio"
                                    id="<?= $mode . '_font_family' ?>"
                                    name="<?= $mode . '_font_family' ?>"
                                    value="<?= $font_family ?>"
                                    class="custom-control-input"
                                    <?= settings()->theme->{$mode . '_font_family'} == $font_family ? 'checked="checked"' : null ?>
                            >

                            <div class="card" style="font-family: <?= $font_family ?> !important;">
                                <div class="card-body d-flex align-items-center justify-content-between">
                                    <div class="card-title text-truncate mb-0"><?= strtok($font_family, ',') ?></div>
                                </div>
                            </div>
                        </label>
                    <?php endforeach ?>

                    <label class="col-4 p-2 custom-radio-box m-0">
                        <input
                                type="radio"
                                id="<?= $mode . '_font_family' ?>"
                                name="<?= $mode . '_font_family' ?>"
                                value="custom"
                                class="custom-control-input"
                                <?= !in_array(settings()->theme->{$mode . '_font_family'}, $fonts) ? 'checked="checked"' : null ?>
                        >

                        <div class="card">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <div class="card-title mb-0"><?= l('admin_settings.theme.font_family_custom') ?></div>
                            </div>
                        </div>
                    </label>
                </div>
            </div>

            <div class="form-group" id="<?= $mode . '_font_family_container' ?>">
                <label for="<?= $mode . '_font_family_custom' ?>"><?= l('admin_settings.theme.font_family') ?></label>
                <input id="<?= $mode . '_font_family_custom' ?>" name="<?= $mode . '_font_family_custom' ?>" type="text" class="form-control" value="<?= settings()->theme->{$mode . '_font_family'} ?? null ?>" />
                <small class="form-text text-muted"><?= l('admin_settings.theme.font_family_help') ?></small>
            </div>

            <div class="form-group" data-range-counter data-range-counter-suffix="rem">
                <label for="<?= $mode . '_border_radius' ?>"><?= l('admin_settings.theme.border_radius') ?></label>
                <input id="<?= $mode . '_border_radius' ?>" name="<?= $mode . '_border_radius' ?>" type="range" step=".01" min="0" max="1" class="form-control-range" value="<?= settings()->theme->{$mode . '_border_radius'} ?? null ?>" />
            </div>

        </div>
    </div>
</div>

<button type="submit" name="submit" class="btn btn-lg btn-block btn-primary mt-4"><?= l('global.update') ?></button>

<?php include_view(THEME_PATH . 'views/partials/color_picker_js.php') ?>

<?php ob_start() ?>
<script>
    'use strict'

    let themes = ['light', 'dark'];

    /* Fonts */
    let font_family_process = (theme) => {
        let font_family = document.querySelector(`input[name="${theme}_font_family"]:checked`)?.value ?? 'custom';

        if(font_family == 'custom') {
            document.querySelector(`#${theme}_font_family_container`).classList.remove('d-none');

        } else {
            document.querySelector(`#${theme}_font_family_container`).classList.add('d-none')

        }
    }

    font_family_process('light');
    font_family_process('dark');

    /* Event listener */
    themes.forEach(theme => {
        document.querySelectorAll(`input[name="${theme}_font_family"]`).forEach(element => {
            element.addEventListener('change', () => {
                font_family_process(theme)
            });
        })
    });

    /* Colors */
    let custom_coloring_process = () => {
        themes.forEach(theme => {
            /* Primary theme */
            let primary_theme = document.querySelector(`input[name="${theme}_primary_theme"]:checked`)?.value ?? 'custom';

            if(primary_theme == 'custom') {
                document.querySelector(`#${theme}_primary_advanced_container`).classList.remove('container-disabled');
            } else {
                document.querySelector(`#${theme}_primary_advanced_container`).classList.add('container-disabled')
            }

            /* Gray theme */
            let gray_theme = document.querySelector(`input[name="${theme}_gray_theme"]:checked`)?.value ?? 'custom';

            if(gray_theme == 'custom') {
                document.querySelector(`#${theme}_gray_advanced_container`).classList.remove('container-disabled');
            } else {
                document.querySelector(`#${theme}_gray_advanced_container`).classList.add('container-disabled')
            }
        })
    }

    custom_coloring_process();

    /* Event listener */
    themes.forEach(theme => {
        document.querySelectorAll(`input[name="${theme}_primary_theme"],input[name="${theme}_gray_theme"]`).forEach(element => {
            element.addEventListener('change', custom_coloring_process);

            element.addEventListener('click', event => {
                /* Process the preset colors if needed */
                let type = event.currentTarget.id.includes('primary') ? 'primary' : 'gray';

                [50, 100, 200, 300, 400, 500, 600, 700, 800, 900].forEach(shade => {
                    let color = event.currentTarget.getAttribute(`data-${shade}`);

                    if(color) document.querySelector(`#${theme}_${type}_${shade}`).value = color;
                });

                document.querySelectorAll('.pickr').forEach(element => element.remove());

                initiate_color_pickers();
            })
        })
    });
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
