<?php defined('ALTUMCODE') || die() ?>
<?= '<?xml version="1.0" encoding="UTF-8"?>' ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <?php foreach ($data->sitemap_urls as $url) : ?>
        <url>
            <loc><?= $url ?></loc>
        </url>
    <?php endforeach ?>
</urlset>
