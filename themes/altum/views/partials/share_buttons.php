<?php defined('ALTUMCODE') || die() ?>

<?php if(($data->type ?? 'fontawesome') == 'fontawesome'): ?>
    <?php if(($data->copy_to_clipboard ?? false) && settings()->socials->share_buttons->copy): ?>
        <button type="button" class="<?= $data->class ?> rounded-2x" style="color: #808080; background-color: rgba(128, 128, 128, 0.1);" data-toggle="tooltip" title="<?= l('global.clipboard_copy') ?>" onclick="navigator.clipboard.writeText('<?= $data->url ?>')" data-tooltip-hide-on-click>
            <i class="fas fa-fw fa-sm fa-copy"></i>
        </button>
    <?php endif ?>

    <?php if(settings()->socials->share_buttons->share): ?>
        <button type="button" class="<?= $data->class ?> rounded-2x d-none" style="color: #3a18f2; background-color: rgba(58, 24, 242, 0.1);" data-toggle="tooltip" title="<?= sprintf(l('global.share_via'), l('global.device')) ?>" data-native-share>
            <i class="fas fa-fw fa-share"></i>
        </button>
    <?php endif ?>

    <?php if(settings()->socials->share_buttons->email): ?>
        <a href="mailto:?body=<?= $data->url ?>" target="_blank" class="<?= $data->class ?> rounded-2x" style="color: #3b5998; background-color: rgba(59, 89, 152, 0.1);" data-toggle="tooltip" title="<?= sprintf(l('global.share_via'), 'Email') ?>">
            <i class="fas fa-fw fa-envelope"></i>
        </a>
    <?php endif ?>

    <?php if(($data->print_is_enabled ?? true) && settings()->socials->share_buttons->print): ?>
        <button type="button" class="<?= $data->class ?> rounded-2x" style="color: #808080; background-color: rgba(128, 128, 128, 0.1);" data-toggle="tooltip" title="<?= l('page.print') ?>" onclick="window.print();return false;" data-tooltip-hide-on-click>
            <i class="fas fa-fw fa-sm fa-print"></i>
        </button>
    <?php endif ?>

    <?php if(settings()->socials->share_buttons->facebook): ?>
        <a href="https://www.facebook.com/sharer/sharer.php?u=<?= $data->url ?>" target="_blank" class="<?= $data->class ?> rounded-2x" style="color: #1877F2; background-color: rgba(24, 119, 242, 0.1);" data-toggle="tooltip" title="<?= sprintf(l('global.share_via'), 'Facebook') ?>">
            <i class="fab fa-fw fa-facebook"></i>
        </a>
    <?php endif ?>

    <?php if(settings()->socials->share_buttons->threads): ?>
        <a href="https://www.threads.net/intent/post?text=<?= $data->url ?>" target="_blank" class="<?= $data->class ?> rounded-2x" style="color: #808080; background-color: rgba(128, 128, 128, 0.1);" data-toggle="tooltip" title="<?= sprintf(l('global.share_via'), 'Threads') ?>">
            <i class="fab fa-fw fa-threads"></i>
        </a>
    <?php endif ?>

    <?php if(settings()->socials->share_buttons->x): ?>
        <a href="https://x.com/share?url=<?= $data->url ?>" target="_blank" class="<?= $data->class ?> rounded-2x" style="color: #1DA1F2; background-color: rgba(29, 161, 242, 0.1);" data-toggle="tooltip" title="<?= sprintf(l('global.share_via'), 'X') ?>">
            <i class="fab fa-fw fa-twitter"></i>
        </a>
    <?php endif ?>

    <?php if(settings()->socials->share_buttons->pinterest): ?>
        <a href="https://pinterest.com/pin/create/link/?url=<?= $data->url ?>" target="_blank" class="<?= $data->class ?> rounded-2x" style="color: #E60023; background-color: rgba(230, 0, 35, 0.1);" data-toggle="tooltip" title="<?= sprintf(l('global.share_via'), 'Pinterest') ?>">
            <i class="fab fa-fw fa-pinterest"></i>
        </a>
    <?php endif ?>

    <?php if(settings()->socials->share_buttons->linkedin): ?>
        <a href="https://linkedin.com/shareArticle?url=<?= $data->url ?>" target="_blank" class="<?= $data->class ?> rounded-2x" style="color: #0077B5; background-color: rgba(0, 119, 181, 0.1);" data-toggle="tooltip" title="<?= sprintf(l('global.share_via'), 'LinkedIn') ?>">
            <i class="fab fa-fw fa-linkedin"></i>
        </a>
    <?php endif ?>

    <?php if(settings()->socials->share_buttons->reddit): ?>
        <a href="https://www.reddit.com/submit?url=<?= $data->url ?>" target="_blank" class="<?= $data->class ?> rounded-2x" style="color: #FF4500; background-color: rgba(255, 69, 0, 0.1);" data-toggle="tooltip" title="<?= sprintf(l('global.share_via'), 'Reddit') ?>">
            <i class="fab fa-fw fa-reddit"></i>
        </a>
    <?php endif ?>

    <?php if(settings()->socials->share_buttons->whatsapp): ?>
        <a href="https://wa.me/?text=<?= $data->url ?>" class="<?= $data->class ?> rounded-2x" style="color: #25D366; background-color: rgba(37, 211, 102, 0.1);" data-toggle="tooltip" title="<?= sprintf(l('global.share_via'), 'Whatsapp') ?>">
            <i class="fab fa-fw fa-whatsapp"></i>
        </a>
    <?php endif ?>

    <?php if(settings()->socials->share_buttons->telegram): ?>
        <a href="https://t.me/share/url?url=<?= $data->url ?>" class="<?= $data->class ?> rounded-2x" style="color: #0088cc; background-color: rgba(0, 136, 204, 0.1);" data-toggle="tooltip" title="<?= sprintf(l('global.share_via'), 'Telegram') ?>">
            <i class="fab fa-fw fa-telegram"></i>
        </a>
    <?php endif ?>

    <?php if(settings()->socials->share_buttons->snapchat): ?>
        <a href="https://www.snapchat.com/scan?attachmentUrl=<?= $data->url ?>" class="<?= $data->class ?> rounded-2x" style="color: #FFB700; background-color: rgba(255, 183, 0, 0.1);" data-toggle="tooltip" title="<?= sprintf(l('global.share_via'), 'Snapchat') ?>">
            <i class="fab fa-fw fa-snapchat"></i>
        </a>
    <?php endif ?>

    <?php if(settings()->socials->share_buttons->microsoft_teams): ?>
        <a href="https://teams.microsoft.com/share?href=<?= urlencode($data->url) ?>" target="_blank" class="<?= $data->class ?> rounded-2x" style="color: #464EB8; background-color: rgba(70, 78, 184, 0.1);" data-toggle="tooltip" title="<?= sprintf(l('global.share_via'), 'Microsoft Teams') ?>">
            <i class="fab fa-fw fa-microsoft"></i>
        </a>
    <?php endif ?>
<?php else: ?>
    <?php if(($data->copy_to_clipboard ?? false) && settings()->socials->share_buttons->copy): ?>
        <button type="button" class="<?= $data->class ?> rounded-2x d-none" style="color: #808080; background-color: rgba(128, 128, 128, 0.1);" title="<?= l('global.clipboard_copy') ?>" onclick="navigator.clipboard.writeText('<?= $data->url ?>')">
            <div class="svg-sm d-flex"><?= include_view(ASSETS_PATH . '/images/icons/copy.svg') ?></div>
        </button>
    <?php endif ?>

    <?php if(settings()->socials->share_buttons->share): ?>
        <button type="button" class="<?= $data->class ?> rounded-2x d-none" style="color: #3a18f2; background-color: rgba(58, 24, 242, 0.1);" title="<?= sprintf(l('global.share_via'), l('global.device')) ?>" data-native-share>
            <div class="svg-sm d-flex"><?= include_view(ASSETS_PATH . '/images/icons/globe-alt.svg') ?></div>
        </button>
    <?php endif ?>

    <?php if(settings()->socials->share_buttons->email): ?>
        <a href="mailto:?body=<?= $data->url ?>" target="_blank" class="<?= $data->class ?> rounded-2x" style="color: #3b5998; background-color: rgba(59, 89, 152, 0.1);" title="<?= sprintf(l('global.share_via'), 'Email') ?>">
            <div class="svg-sm d-flex"><?= include_view(ASSETS_PATH . '/images/icons/email.svg') ?></div>
        </a>
    <?php endif ?>

    <?php if(($data->print_is_enabled ?? true) && settings()->socials->share_buttons->print): ?>
        <button type="button" class="<?= $data->class ?> rounded-2x" style="color: #808080; background-color: rgba(128, 128, 128, 0.1);" title="<?= l('page.print') ?>" onclick="window.print();return false;" data-tooltip-hide-on-click>
            <div class="svg-sm d-flex"><?= include_view(ASSETS_PATH . '/images/icons/pdf.svg') ?></div>
        </button>
    <?php endif ?>

    <?php if(settings()->socials->share_buttons->facebook): ?>
        <a href="https://www.facebook.com/sharer/sharer.php?u=<?= $data->url ?>" target="_blank" class="<?= $data->class ?> rounded-2x" style="color: #1877F2; background-color: rgba(24, 119, 242, 0.1);" title="<?= sprintf(l('global.share_via'), 'Facebook') ?>">
            <div class="svg-sm d-flex"><?= include_view(ASSETS_PATH . '/images/icons/facebook.svg') ?></div>
        </a>
    <?php endif ?>

    <?php if(settings()->socials->share_buttons->threads): ?>
        <a href="https://www.threads.net/intent/post?text=<?= $data->url ?>" target="_blank" class="<?= $data->class ?> rounded-2x" style="color: #808080; background-color: rgba(128, 128, 128, 0.1);" title="<?= sprintf(l('global.share_via'), 'Threads') ?>">
            <div class="svg-sm d-flex"><?= include_view(ASSETS_PATH . '/images/icons/threads.svg') ?></div>
        </a>
    <?php endif ?>

    <?php if(settings()->socials->share_buttons->x): ?>
        <a href="https://x.com/share?url=<?= $data->url ?>" target="_blank" class="<?= $data->class ?> rounded-2x" style="color: #1DA1F2; background-color: rgba(29, 161, 242, 0.1);" title="<?= sprintf(l('global.share_via'), 'X') ?>">
            <div class="svg-sm d-flex"><?= include_view(ASSETS_PATH . '/images/icons/x.svg') ?></div>
        </a>
    <?php endif ?>

    <?php if(settings()->socials->share_buttons->pinterest): ?>
        <a href="https://pinterest.com/pin/create/link/?url=<?= $data->url ?>" target="_blank" class="<?= $data->class ?> rounded-2x" style="color: #E60023; background-color: rgba(230, 0, 35, 0.1);" title="<?= sprintf(l('global.share_via'), 'Pinterest') ?>">
            <div class="svg-sm d-flex"><?= include_view(ASSETS_PATH . '/images/icons/pinterest.svg') ?></div>
        </a>
    <?php endif ?>

    <?php if(settings()->socials->share_buttons->linkedin): ?>
        <a href="https://linkedin.com/shareArticle?url=<?= $data->url ?>" target="_blank" class="<?= $data->class ?> rounded-2x" style="color: #0077B5; background-color: rgba(0, 119, 181, 0.1);" title="<?= sprintf(l('global.share_via'), 'LinkedIn') ?>">
            <div class="svg-sm d-flex"><?= include_view(ASSETS_PATH . '/images/icons/linkedin.svg') ?></div>
        </a>
    <?php endif ?>

    <?php if(settings()->socials->share_buttons->reddit): ?>
        <a href="https://www.reddit.com/submit?url=<?= $data->url ?>" target="_blank" class="<?= $data->class ?> rounded-2x" style="color: #FF4500; background-color: rgba(255, 69, 0, 0.1);" title="<?= sprintf(l('global.share_via'), 'Reddit') ?>">
            <div class="svg-sm d-flex"><?= include_view(ASSETS_PATH . '/images/icons/reddit.svg') ?></div>
        </a>
    <?php endif ?>

    <?php if(settings()->socials->share_buttons->whatsapp): ?>
        <a href="https://wa.me/?text=<?= $data->url ?>" class="<?= $data->class ?> rounded-2x" style="color: #25D366; background-color: rgba(37, 211, 102, 0.1);" title="<?= sprintf(l('global.share_via'), 'Whatsapp') ?>">
            <div class="svg-sm d-flex"><?= include_view(ASSETS_PATH . '/images/icons/whatsapp.svg') ?></div>
        </a>
    <?php endif ?>

    <?php if(settings()->socials->share_buttons->telegram): ?>
        <a href="https://t.me/share/url?url=<?= $data->url ?>" class="<?= $data->class ?> rounded-2x" style="color: #0088cc; background-color: rgba(0, 136, 204, 0.1);" title="<?= sprintf(l('global.share_via'), 'Telegram') ?>">
            <div class="svg-sm d-flex"><?= include_view(ASSETS_PATH . '/images/icons/telegram.svg') ?></div>
        </a>
    <?php endif ?>

    <?php if(settings()->socials->share_buttons->snapchat): ?>
        <a href="https://www.snapchat.com/scan?attachmentUrl=<?= $data->url ?>" class="<?= $data->class ?> rounded-2x" style="color: #FFB700; background-color: rgba(255, 183, 0, 0.1);" title="<?= sprintf(l('global.share_via'), 'Snapchat') ?>">
            <div class="svg-sm d-flex"><?= include_view(ASSETS_PATH . '/images/icons/snapchat.svg') ?></div>
        </a>
    <?php endif ?>

    <?php if(settings()->socials->share_buttons->microsoft_teams): ?>
        <a href="https://teams.microsoft.com/share?href=<?= urlencode($data->url) ?>" target="_blank" class="<?= $data->class ?> rounded-2x" style="color: #464EB8; background-color: rgba(70, 78, 184, 0.1);" title="<?= sprintf(l('global.share_via'), 'Microsoft Teams') ?>">
            <div class="svg-sm d-flex"><?= include_view(ASSETS_PATH . '/images/icons/teams.svg') ?></div>
        </a>
    <?php endif ?>
<?php endif ?>

<?php ob_start() ?>
    <script>
    'use strict';
    
        document.querySelectorAll('[data-native-share]').forEach(element => {
            if(navigator.share) {
                element.classList.remove('d-none');
                element.addEventListener('click', event => {
                    navigator.share({
                        title: document.title,
                        url: "<?= $data->url ?>"
                    }).catch(error => {});
                })
            }
        })
    </script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript', 'native_share') ?>
