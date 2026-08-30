<?php
$classes = isset($classes) ? $classes : '';
$image_style = !empty($image) ? "background-image:url('".$image."');" : '';
?>
<a href="<?= !empty($url) ? $url : base_url('accessories') ?>" class="va-promo-tile <?= html_escape($classes) ?>" style="<?= $image_style ?>">
    <span><?= html_escape($eyebrow) ?></span>
    <h3><?= html_escape($title) ?></h3>
    <p><?= html_escape($subtitle) ?></p>
    <strong>Shop Now</strong>
</a>
