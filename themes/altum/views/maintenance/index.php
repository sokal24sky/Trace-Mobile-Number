<?php defined('ALTUMCODE') || die() ?>

<?= \Altum\Alerts::output_alerts() ?>

<h1 class="h5"><?= settings()->main->maintenance_title ?: l('maintenance.header') ?></h1>
<p class="text-muted mb-0"><?= settings()->main->maintenance_description ?: l('maintenance.subheader') ?></p>

<?php if(settings()->main->maintenance_button_text && settings()->main->maintenance_button_url): ?>
<a href="<?= settings()->main->maintenance_button_url ?>" class="mt-4 btn btn-block btn-primary" target="_blank" rel="nofollow noreferrer"><?= settings()->main->maintenance_button_text ?></a>
<?php endif ?>
