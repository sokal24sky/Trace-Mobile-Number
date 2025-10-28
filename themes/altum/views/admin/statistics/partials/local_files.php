<?php defined('ALTUMCODE') || die() ?>

<?php ob_start() ?>
<div class="alert alert-info">
    <?= l('admin_statistics.local_files.info') ?>
</div>

<div class="mb-3 row justify-content-between">
    <div class="col-12 col-sm-6 p-3">
        <div class="card d-flex flex-row h-100 overflow-hidden">
            <div class="card-body">
                <div class="row">
                    <div class="col text-truncate">
                        <small class="text-muted font-weight-bold"><?= l('admin_statistics.local_files.total_files') ?></small>
                    </div>

                    <div class="col-auto">
                        <span class="p-2 bg-primary-100 rounded">
                            <i class="fas fa-fw fa-sm fa-copy text-primary"></i>
                        </span>
                    </div>
                </div>

                <div class="mt-2 text-break">
                    <span class="h4">
                        <?= nr($data->total_statistics['total_files']) ?>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 p-3">
        <div class="card d-flex flex-row h-100 overflow-hidden">
            <div class="card-body">
                <div class="row">
                    <div class="col text-truncate">
                        <small class="text-muted font-weight-bold"><?= l('admin_statistics.local_files.total_size') ?></small>
                    </div>

                    <div class="col-auto">
                        <span class="p-2 bg-primary-100 rounded">
                            <i class="fas fa-fw fa-sm fa-hard-drive text-primary"></i>
                        </span>
                    </div>
                </div>

                <div class="mt-2 text-break">
                    <span class="h4" id="shortened_links">
                        <?= get_formatted_bytes($data->total_statistics['total_size']) ?>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card mb-5">
    <div class="card-body">
        <h2 class="h4 mb-4"><i class="fas fa-fw fa-copy fa-xs text-primary-900 mr-2"></i> <?= l('admin_statistics.local_files.header') ?></h2>

        <?php //ALTUMCODE:DEMO if(DEMO) echo 'hidden on demo'; ?>
        <?php //ALTUMCODE:DEMO if(!DEMO): ?>

        <?php foreach($data->folders as $folder_name => $stats): ?>
            <button class="btn btn-block btn-gray-200 mb-4" type="button" data-toggle="collapse" data-target="<?= '#' . preg_replace('/[^a-zA-Z0-9]/', '_', $folder_name) ?>" aria-expanded="false" aria-controls="<?= preg_replace('/[^a-zA-Z0-9]/', '_', $folder_name) ?>">
                <?= $folder_name ?>

                <i class="fas fa-fw fa-xs fa-chevron-right text-muted mx-1"></i>

                <?= sprintf(l('admin_statistics.local_files.files_x'), $stats['total_files']) ?>

                <i class="fas fa-fw fa-xs fa-chevron-right text-muted mx-1"></i>

                <?= get_formatted_bytes($stats['total_size']) ?>
            </button>

            <div class="collapse" id="<?= preg_replace('/[^a-zA-Z0-9]/', '_', $folder_name) ?>">
                <?php arsort($stats['extensions']); ?>

                <?php if(count($stats['extensions'])): ?>
                    <?php foreach ($stats['extensions'] as $extension => $count): ?>
                        <?php $percentage = $stats['total_files'] > 0 ? round(($count / $stats['total_files']) * 100, 1) : 0; ?>

                        <div class="mb-4">
                            <div class="d-flex justify-content-between mb-1">
                                <div class="text-truncate">
                                    <span class=""><?= $extension ?></span>
                                </div>

                                <div>
                                    <small class="text-muted"><?= $percentage . '%' ?></small>
                                    <span class="ml-3"><?= nr($count) ?></span>
                                </div>
                            </div>

                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar" role="progressbar" style="width: <?= $percentage . '%' ?>;" aria-valuenow="<?= $percentage ?>" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    <?php endforeach ?>
                <?php else: ?>
                    <?= l('global.none') ?>
                <?php endif ?>
            </div>
        <?php endforeach ?>

        <?php //ALTUMCODE:DEMO endif ?>
    </div>
</div>

<?php $html = ob_get_clean() ?>


<?php ob_start() ?>
<?php $javascript = ob_get_clean() ?>

<?php return (object) ['html' => $html, 'javascript' => $javascript, 'has_datepicker' => false] ?>
