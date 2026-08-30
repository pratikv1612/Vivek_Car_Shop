<div class="va-section-head">
    <h2><?= html_escape($title) ?></h2>
    <div class="va-head-actions">
        <?php if (!empty($link)): ?>
            <a href="<?= $link ?>">View All Products</a>
        <?php endif; ?>
        <?php if (!empty($slider)): ?>
            <div class="va-slider-nav">
                <button type="button" class="va-slider-prev" aria-label="Previous"><i class="bi bi-chevron-left"></i></button>
                <button type="button" class="va-slider-next" aria-label="Next"><i class="bi bi-chevron-right"></i></button>
            </div>
        <?php endif; ?>
    </div>
</div>
