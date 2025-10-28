<?php defined('ALTUMCODE') || die() ?>

<div>
    <div class="form-group">
        <label for="head_js"><i class="fab fa-fw fa-sm fa-js text-muted mr-1"></i> <?= l('admin_settings.custom.head_js') ?></label>
        <textarea id="head_js" name="head_js" class="form-control" data-code-editor data-mode="htmlmixed"><?= settings()->custom->head_js ?></textarea>
        <small class="form-text text-muted"><?= l('admin_settings.custom.head_js_help') ?></small>
        <small class="form-text text-muted"><?= sprintf(l('global.variables'), '<code data-copy>' . implode('</code> , <code data-copy>',  ['{{WEBSITE_TITLE}}', '{{USER:NAME}}', '{{USER:EMAIL}}', '{{USER:CONTINENT_NAME}}', '{{USER:COUNTRY_NAME}}', '{{USER:CITY_NAME}}', '{{USER:DEVICE_TYPE}}', '{{USER:OS_NAME}}', '{{USER:BROWSER_NAME}}', '{{USER:BROWSER_LANGUAGE}}', '{{USER:USER_ID}}', '{{USER:PLAN_ID}}']) . '</code>') ?></small>
    </div>

    <div class="form-group">
        <label for="welcome_js"><i class="fab fa-fw fa-sm fa-js text-muted mr-1"></i> <?= l('admin_settings.custom.welcome_js') ?></label>
        <textarea id="welcome_js" name="welcome_js" class="form-control" data-code-editor data-mode="htmlmixed"><?= settings()->custom->welcome_js ?></textarea>
        <small class="form-text text-muted"><?= l('admin_settings.custom.welcome_js_help') ?></small>
        <small class="form-text text-muted"><?= sprintf(l('global.variables'), '<code data-copy>' . implode('</code> , <code data-copy>',  ['{{WEBSITE_TITLE}}', '{{USER:NAME}}', '{{USER:EMAIL}}', '{{USER:CONTINENT_NAME}}', '{{USER:COUNTRY_NAME}}', '{{USER:CITY_NAME}}', '{{USER:DEVICE_TYPE}}', '{{USER:OS_NAME}}', '{{USER:BROWSER_NAME}}', '{{USER:BROWSER_LANGUAGE}}', '{{USER:USER_ID}}', '{{USER:PLAN_ID}}']) . '</code>') ?></small>
    </div>

    <div class="form-group">
        <label for="pay_thank_you_js"><i class="fab fa-fw fa-sm fa-js text-muted mr-1"></i> <?= l('admin_settings.custom.pay_thank_you_js') ?></label>
        <textarea id="pay_thank_you_js" name="pay_thank_you_js" class="form-control" data-code-editor data-mode="htmlmixed"><?= settings()->custom->pay_thank_you_js ?></textarea>
        <small class="form-text text-muted"><?= l('admin_settings.custom.pay_thank_you_js_help') ?></small>
        <small class="form-text text-muted"><?= sprintf(l('global.variables'), '<code data-copy>' . implode('</code> , <code data-copy>',  ['{{WEBSITE_TITLE}}', '{{USER:NAME}}', '{{USER:EMAIL}}', '{{USER:CONTINENT_NAME}}', '{{USER:COUNTRY_NAME}}', '{{USER:CITY_NAME}}', '{{USER:DEVICE_TYPE}}', '{{USER:OS_NAME}}', '{{USER:BROWSER_NAME}}', '{{USER:BROWSER_LANGUAGE}}', '{{USER:USER_ID}}', '{{USER:PLAN_ID}}', '{{PAYMENT:PROCESSOR}}', '{{PAYMENT:FREQUENCY}}', '{{PAYMENT:PLAN_ID}}', '{{PAYMENT:CODE}}', '{{PAYMENT:TYPE}}', '{{PAYMENT:TOTAL_AMOUNT}}']) . '</code>') ?></small>
    </div>

    <div class="form-group">
        <label for="head_css"><i class="fab fa-fw fa-sm fa-css3 text-muted mr-1"></i> <?= l('admin_settings.custom.head_css') ?></label>
        <textarea id="head_css" name="head_css" class="form-control" data-code-editor data-mode="css"><?= settings()->custom->head_css ?></textarea>
        <small class="form-text text-muted"><?= l('admin_settings.custom.head_css_help') ?></small>
    </div>

    <div class="form-group">
        <label for="body_content"><i class="fab fa-fw fa-sm fa-html5 text-muted mr-1"></i> <?= l('admin_settings.custom.body_content') ?></label>
        <textarea id="body_content" name="body_content" class="form-control" data-code-editor data-mode="htmlmixed"><?= settings()->custom->body_content ?></textarea>
        <small class="form-text text-muted"><?= l('admin_settings.custom.body_content_help') ?></small>
    </div>
</div>

<button type="submit" name="submit" class="btn btn-lg btn-block btn-primary mt-4"><?= l('global.update') ?></button>

<?php include_view(THEME_PATH . 'views/partials/codemirror_js.php') ?>
<?php include_view(THEME_PATH . 'views/partials/clipboard_js.php') ?>
