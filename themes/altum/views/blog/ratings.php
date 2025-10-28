<?php defined('ALTUMCODE') || die() ?>

<?php if(settings()->content->blog_ratings_is_enabled): ?>
    <div id="rating" class="mt-3 d-flex">
        <div class="d-flex align-items-center flex-row-reverse" style="justify-content: start;">
            <?php $style = $data->blog_post->average_rating >= 4.5 ? null : 'style="opacity: 0.5;"' ?>
            <span class="rating-star" data-rating="5">
                <i class="fas fa-fw fa-star mr-1" <?= $style ?>></i>
            </span>

            <?php $style = $data->blog_post->average_rating >= 3.5 ? null : 'style="opacity: 0.5;"' ?>
            <span class="rating-star" data-rating="4">
                <i class="fas fa-fw fa-star mr-1" <?= $style ?>></i>
            </span>

            <?php $style = $data->blog_post->average_rating >= 2.5 ? null : 'style="opacity: 0.5;"' ?>
            <span class="rating-star" data-rating="3">
                <i class="fas fa-fw fa-star mr-1" <?= $style ?>></i>
            </span>

            <?php $style = $data->blog_post->average_rating >= 1.5 ? null : 'style="opacity: 0.5;"' ?>
            <span class="rating-star" data-rating="2">
                <i class="fas fa-fw fa-star mr-1" <?= $style ?>></i>
            </span>

            <?php $style = $data->blog_post->average_rating >= 0.5 ? null : 'style="opacity: 0.5;"' ?>
            <span class="rating-star" data-rating="1">
                <i class="fas fa-fw fa-star mr-1" <?= $style ?>></i>
            </span>
        </div>

        <div class="ml-2">
            <small class="text-muted">
                <?= sprintf(l('tools.rating'),
                    '<span id="average-rating">' . nr($data->blog_post->average_rating, 2, false) . '</span>',
                    '<span id="total-ratings">' . nr($data->blog_post->total_ratings, 2, false) . '</span>') ?>
            </small>
        </div>
    </div>

    <?php ob_start() ?>
    <script>
        'use strict'

        let current_rating = localStorage.getItem('<?= md5(SITE_URL) . '_blog_' . $data->blog_post->blog_post_id ?>_rating');
        if(current_rating) {
            document.querySelector(`[data-rating="${current_rating}"]`).classList.add('rating-star-chosen');
        }

        document.querySelectorAll('[data-rating]').forEach(star => {
            star.addEventListener('click', async event => {
                let cooldown_ms = 5000; /* 5 second cooldown */
                let last_rating_time = parseInt(localStorage.getItem('<?= md5(SITE_URL) . '_blog_' . $data->blog_post->blog_post_id ?>_rating_time') || '0', 10);
                let now = Date.now();

                /* Block if user is within cooldown */
                if(now - last_rating_time < cooldown_ms) {
                    return;
                }

                localStorage.setItem('<?= md5(SITE_URL) . '_blog_' . $data->blog_post->blog_post_id ?>_rating_time', now);

                let element = event.currentTarget;
                let rating = element.getAttribute('data-rating');
                let blog_post_id = <?= json_encode($data->blog_post->blog_post_id) ?>;

                /* Prepare form data */
                let form = new FormData();
                form.set('global_token', global_token)
                form.set('blog_post_id', blog_post_id);
                form.set('rating', rating);

                const response = await fetch(`${url}blog/ratings_ajax`, {
                    method: 'post',
                    body: form
                });

                let data = null;
                try {
                    data = await response.json();
                } catch (error) {}

                if(!response.ok) {}

                if(data.status == 'error') {

                } else if(data.status == 'success') {
                    localStorage.setItem('<?= md5(SITE_URL) . '_blog_' . $data->blog_post->blog_post_id ?>_rating', rating);

                    /* Remove previous chosen star if needed */
                    let chosen_rating = document.querySelector('.rating-star-chosen');
                    if(chosen_rating) {
                        chosen_rating.classList.remove('rating-star-chosen');
                    }

                    /* Add class to show that it has been rated */
                    element.classList.add('rating-star-chosen');

                    /* Update ratings and avg */
                    document.querySelector('#average-rating').textContent = data.details.new_average_rating;
                    document.querySelector('#total-ratings').textContent = data.details.new_total_ratings;
                }
            });
        });
    </script>
    <?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
<?php endif ?>
