<?php defined('ALTUMCODE') || die() ?>

<body class="link-body">
    <div class="container altum-animate altum-animate-fill-both altum-animate-fade-in">
        <div class="row justify-content-center mt-5 mt-lg-10">
            <div class="col-md-8">

                <div class="mb-4 d-flex">
                    <div class="text-center">
                        <h1 class="h3 mb-4"><?= l('l_link.password.header')  ?></h1>
                        <span class="text-muted">
                            <?= l('l_link.password.subheader') ?>
                        </span>
                    </div>
                </div>

                <?= \Altum\Alerts::output_alerts() ?>

                <form action="" method="post" role="form">
                    <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>" />
                    <input type="hidden" name="type" value="password" />

                    <div class="form-group" data-password-toggle-view data-password-toggle-view-show="<?= l('global.show') ?>" data-password-toggle-view-hide="<?= l('global.hide') ?>">
                        <label for="password"><?= l('global.password') ?></label>
                        <input type="password" id="password" name="password" value="" class="form-control <?= \Altum\Alerts::has_field_errors('password') ? 'is-invalid' : null ?>" required="required" />
                        <?= \Altum\Alerts::output_field_error('password') ?>
                    </div>

                    <button type="submit" name="submit" class="btn btn-block btn-primary mt-4"><?= l('global.submit') ?></button>
                </form>

            </div>
        </div>
    </div>
</body>


