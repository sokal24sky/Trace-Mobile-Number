<?php defined('ALTUMCODE') || die() ?>

<div class="modal fade" id="link_delete_modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-fw fa-sm fa-trash-alt text-dark mr-2"></i>
                    <?= l('link_delete_modal.header') ?>
                </h5>
                <button type="button" class="close" data-dismiss="modal" title="<?= l('global.close') ?>">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form name="link_delete_modal" method="post" action="<?= url('links/delete') ?>" role="form">
                    <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>" required="required" />
                    <input type="hidden" name="link_id" value="" />

                    <div class="notification-container"></div>

                    <p class="text-muted"><?= l('link_delete_modal.subheader') ?></p>

                    <div class="mt-4">
                        <button type="submit" name="submit" class="btn btn-lg btn-block btn-danger"><?= l('global.delete') ?></button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<?php ob_start() ?>
<script>
    'use strict';
    
    /* On modal show load new data */
    $('#link_delete_modal').on('show.bs.modal', event => {
        let link_id = $(event.relatedTarget).data('link-id');

        $(event.currentTarget).find('input[name="link_id"]').val(link_id);
    });
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
