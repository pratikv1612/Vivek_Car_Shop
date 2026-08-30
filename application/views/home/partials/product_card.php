<?php
$product_url = base_url('accessory/'.$product->id.'/'.$product->slug);
$image = !empty($product->primary_image) ? $product->primary_image : '';
$category = !empty($product->category_name) ? $product->category_name : 'Spare Part';
$old_price = $product->price * 1.18;
$badge = isset($badge) ? $badge : 'Sale';
$is_sold = !empty($product->product_status) && $product->product_status === 'sold';
?>
<article class="va-product-card <?= $is_sold ? 'is-sold' : '' ?>">
    <a href="<?= $product_url ?>" class="va-product-image" aria-label="<?= html_escape($product->name) ?>">
        <?php if ($image): ?>
            <img src="<?= $image ?>" alt="<?= html_escape($product->name) ?>">
        <?php else: ?>
            <img src="<?= base_url('assets/images/placeholder-product.svg') ?>" alt="<?= html_escape($product->name) ?>">
        <?php endif; ?>
        <span class="va-product-badge"><?= html_escape($badge) ?></span>
    </a>
    <div class="va-product-actions">
            <button type="button" class="shop-action" data-action="cart" data-product-id="<?= $product->id ?>" aria-label="Add to cart" <?= $is_sold ? 'disabled' : '' ?>><i class="bi bi-bag-plus"></i></button>
            <button type="button" class="shop-action" data-action="wishlist" data-product-id="<?= $product->id ?>" aria-label="Add to wishlist"><i class="bi bi-heart"></i></button>
            <button type="button" class="shop-action" data-action="compare" data-product-id="<?= $product->id ?>" aria-label="Compare"><i class="bi bi-shuffle"></i></button>
            <button type="button" class="shop-quick-view" data-name="<?= html_escape($product->name) ?>" data-price="&#8377; <?= number_format($product->price) ?>" data-image="<?= html_escape($image) ?>" aria-label="Quick view"><i class="bi bi-eye"></i></button>
    </div>
    <div class="va-product-body">
        <div class="va-product-category"><?= html_escape($category) ?></div>
        <h3><a href="<?= $product_url ?>"><?= html_escape($product->name) ?></a></h3>
        <div class="va-rating" aria-label="Rated 5 out of 5">
            <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
        </div>
        <div class="va-price">
            <span>&#8377; <?= number_format($product->price) ?></span>
            <del>&#8377; <?= number_format($old_price) ?></del>
        </div>
        <div class="va-stock"><i class="bi <?= $is_sold ? 'bi-x-circle-fill' : 'bi-check-circle-fill' ?>"></i> <?= $is_sold ? 'Sold Out' : 'In Stock' ?></div>
        <button type="button" class="btn va-card-button shop-action" data-action="cart" data-product-id="<?= $product->id ?>" <?= $is_sold ? 'disabled' : '' ?>>Add to Cart</button>
    </div>
</article>
