<div class="va-catalog-page">
    <div class="va-catalog-heading">
        <div>
            <span class="va-eyebrow">V Auto Spare</span>
            <h1><?= html_escape($catalog_title ?? 'Shop') ?></h1>
        </div>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0"><li class="breadcrumb-item"><a href="<?= base_url() ?>">Home</a></li><li class="breadcrumb-item active"><?= html_escape($catalog_title ?? 'Shop') ?></li></ol></nav>
    </div>

    <div class="va-catalog-layout">
        <aside class="va-filter-panel">
            <form method="get" action="<?= base_url($catalog_base_url ?? 'accessories') ?>">
                <section class="va-filter-section">
                    <h2>Shop by Categories <i class="bi bi-dash"></i></h2>
                    <div class="va-filter-options va-filter-scroll">
                        <?php foreach ($categories as $cat): ?>
                        <label><input type="radio" name="category" value="<?= $cat->id ?>" <?= ($filters['category_id'] ?? '') == $cat->id ? 'checked' : '' ?>> <span><?= html_escape($cat->name) ?></span></label>
                        <?php endforeach; ?>
                    </div>
                </section>
                <section class="va-filter-section">
                    <h2>Car Brand <i class="bi bi-dash"></i></h2>
                    <select name="brand" class="form-select form-select-sm" id="accBrand"><option value="">All brands</option><?php foreach ($brands as $brand): ?><option value="<?= $brand->id ?>" <?= ($filters['brand_id'] ?? '') == $brand->id ? 'selected' : '' ?>><?= html_escape($brand->name) ?></option><?php endforeach; ?></select>
                </section>
                <section class="va-filter-section">
                    <h2>Price Filter <i class="bi bi-dash"></i></h2>
                    <div class="row g-2"><div class="col-6"><input type="number" class="form-control form-control-sm" name="min_price" placeholder="Min" value="<?= html_escape($filters['min_price'] ?? '') ?>"></div><div class="col-6"><input type="number" class="form-control form-control-sm" name="max_price" placeholder="Max" value="<?= html_escape($filters['max_price'] ?? '') ?>"></div></div>
                </section>
                <section class="va-filter-section">
                    <h2>Search <i class="bi bi-dash"></i></h2>
                    <input type="search" name="keyword" class="form-control form-control-sm" placeholder="Search products" value="<?= html_escape($filters['keyword'] ?? '') ?>">
                </section>
                <button type="submit" class="btn va-filter-submit w-100">Apply Filters</button>
                <a href="<?= base_url($catalog_base_url ?? 'accessories') ?>" class="va-filter-reset">Reset all filters</a>
            </form>
        </aside>

        <section class="va-catalog-results">
            <div class="va-catalog-toolbar"><span>Showing <?= $total ? (($page - 1) * 12 + 1) : 0 ?>–<?= min($page * 12, $total) ?> of <?= $total ?> results</span><div><i class="bi bi-grid-3x3-gap-fill text-warning"></i> <span class="ms-2">Default sorting</span></div></div>
            <?php if (empty($accessories)): ?>
                <div class="va-empty-catalog"><i class="bi bi-search"></i><h2>No products found</h2><p>Try changing your filters or search term.</p></div>
            <?php else: ?>
                <div class="va-catalog-grid">
                    <?php foreach ($accessories as $acc): $image = $acc->primary_image ?? ''; $url = base_url('accessory/'.$acc->id.'/'.$acc->slug); ?>
                    <article class="va-catalog-card">
                        <a href="<?= $url ?>" class="va-catalog-image"><?php if ($image): ?><img src="<?= $image ?>" alt="<?= html_escape($acc->name) ?>"><?php else: ?><i class="bi bi-gear-wide-connected"></i><?php endif; ?><?php if (!empty($acc->is_new)): ?><span>New</span><?php endif; ?></a>
                        <div class="va-catalog-actions"><button class="shop-action" data-action="cart" data-product-id="<?= $acc->id ?>" aria-label="Add to cart"><i class="bi bi-bag-plus"></i></button><button class="shop-action" data-action="wishlist" data-product-id="<?= $acc->id ?>" aria-label="Add to wishlist"><i class="bi bi-heart"></i></button><button class="shop-action" data-action="compare" data-product-id="<?= $acc->id ?>" aria-label="Compare"><i class="bi bi-shuffle"></i></button><button class="shop-quick-view" data-name="<?= html_escape($acc->name) ?>" data-price="&#8377; <?= number_format($acc->price) ?>" data-image="<?= html_escape($image) ?>" aria-label="Quick view"><i class="bi bi-eye"></i></button></div>
                        <div class="va-catalog-card-body"><h2><a href="<?= $url ?>"><?= html_escape($acc->name) ?></a></h2><div class="va-catalog-rating">★★★★★</div><strong>&#8377; <?= number_format($acc->price) ?></strong><button type="button" class="btn va-card-button shop-action" data-action="cart" data-product-id="<?= $acc->id ?>">Add to Cart</button></div>
                    </article>
                    <?php endforeach; ?>
                </div>
                <?php if ($total_pages > 1): ?><nav class="va-catalog-pagination" aria-label="Products pages"><?php for ($i = 1; $i <= $total_pages; $i++): ?><a class="<?= $i == $page ? 'active' : '' ?>" href="<?= base_url(($catalog_base_url ?? 'accessories').'/'.$i).'?'.http_build_query($_GET) ?>"><?= $i ?></a><?php endfor; ?></nav><?php endif; ?>
            <?php endif; ?>
        </section>
    </div>
</div>
