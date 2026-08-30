<?php
$main_banner = $main_banners[0] ?? null;
$sub_banner = $sub_banners[0] ?? null;
$hero_image = $main_banner ? base_url($main_banner->image_path) : base_url('uploads/cars/510bfca199383fb64af89cc3ae5e362e.png');
$side_hero_image = $sub_banner ? base_url($sub_banner->image_path) : base_url('uploads/cars/ecf7d3c37b6bd786032bc74e27be4198.png');
$promo_image = !empty($featured_accessories[0]->primary_image) ? $featured_accessories[0]->primary_image : base_url('uploads/accessories/8c363ca300edfb045090c0c7e9533747.jpg');
$second_promo_image = !empty($featured_accessories[1]->primary_image) ? $featured_accessories[1]->primary_image : base_url('uploads/accessories/dd95596a3988be50848503dbf10ce9b4.jpeg');
$category_icons = ['bi-lightbulb', 'bi-fan', 'bi-tools', 'bi-snow', 'bi-disc', 'bi-fuel-pump', 'bi-steering-wheel', 'bi-gear-wide-connected'];
?>

<!-- 1. Slider -->
<section class="va-hero-grid">
    <a href="<?= $main_banner && $main_banner->link_url ? html_escape($main_banner->link_url) : base_url('accessories') ?>" class="va-hero va-hero-large" style="background-image:url('<?= $hero_image ?>');">
        <span class="va-pill">New Release</span>
        <h1>Get All Original Parts for Your Car</h1>
        <p>Starting from <strong>&#8377;599</strong></p>
        <em>Shop Now</em>
    </a>
    <a href="<?= $sub_banner && $sub_banner->link_url ? html_escape($sub_banner->link_url) : base_url('accessories') ?>" class="va-hero va-hero-side" style="background-image:url('<?= $side_hero_image ?>');">
        <span class="va-pill">New Release</span>
        <h2>Find Parts For Your Vehicle</h2>
        <p>Starting from <strong>&#8377;499</strong></p>
        <em>Shop Now</em>
    </a>
</section>

<!-- 2. Category -->
<?php if (!empty($accessory_categories)): ?>
<section class="va-section">
    <?php $this->load->view('home/partials/section_title', ['title' => 'Popular Categories', 'link' => base_url('accessories')]); ?>
    <div class="va-category-row">
        <?php foreach (array_slice($accessory_categories, 0, 8) as $i => $cat): ?>
        <a href="<?= base_url('accessories?category='.$cat->id) ?>" class="va-category">
            <span><i class="bi <?= $category_icons[$i % count($category_icons)] ?>"></i></span>
            <strong><?= html_escape($cat->name) ?></strong>
            <small>Shop parts</small>
        </a>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>
<!-- 3. Featured Products -->
<?php if (!empty($featured_accessories)): ?>
<section class="va-section">
    <?php $this->load->view('home/partials/section_title', ['title' => 'Featured Products', 'link' => base_url('accessories'), 'slider' => true]); ?>
    <div class="va-featured-layout">
        <div class="va-product-grid va-grid-5 va-slider-track">
            <?php foreach (array_slice($featured_accessories, 0, 12) as $product): ?>
                <?php $this->load->view('home/partials/product_card', ['product' => $product, 'badge' => 'Sale']); ?>
            <?php endforeach; ?>
        </div>
        <aside class="va-side-ad va-side-ad-light">
            <span>Premium Parts</span>
            <h3>Save 50% Off</h3>
            <p>Genuine car spare parts</p>
            <a href="<?= base_url('accessories') ?>">Shop All</a>
        </aside>
    </div>
</section>
<?php endif; ?>
<!-- 4. Sub banner -->
<section class="va-promo-grid">
    <?php $this->load->view('home/partials/promo_tile', [
        'eyebrow' => 'Featured',
        'title' => 'Interior Parts',
        'subtitle' => 'From 50% Off',
        'image' => $promo_image,
        'url' => base_url('accessories'),
    ]); ?>
    <?php $this->load->view('home/partials/promo_tile', [
        'eyebrow' => 'For Any Vehicle',
        'title' => 'Buy The Tires',
        'subtitle' => 'From 50% Off',
        'image' => $side_hero_image,
        'url' => base_url('accessories'),
    ]); ?>
    <?php $this->load->view('home/partials/promo_tile', [
        'eyebrow' => 'Hot Sale',
        'title' => 'Car Body Parts',
        'subtitle' => 'From 50% Off',
        'image' => $second_promo_image,
        'url' => base_url('accessories'),
    ]); ?>
</section>
<!-- 5. Car section -->
<?php $home_cars = !empty($home_cars) ? $home_cars : (!empty($featured_cars) ? $featured_cars : $latest_cars); ?>
<?php if (!empty($home_cars)): ?>
<section class="va-section">
    <div class="va-section-head">
        <h2>Listed Cars Segment</h2>
        <div class="va-head-actions">
            <a href="<?= base_url('cars') ?>">View All Cars</a>
            <div class="va-slider-nav">
                <button type="button" class="va-slider-prev" aria-label="Previous"><i class="bi bi-chevron-left"></i></button>
                <button type="button" class="va-slider-next" aria-label="Next"><i class="bi bi-chevron-right"></i></button>
            </div>
        </div>
    </div>
    <div class="va-product-grid va-grid-5 va-slider-track">
        <?php foreach (array_slice($home_cars, 0, 10) as $car): ?>
            <?php $this->load->view('home/partials/car_card', ['car' => $car]); ?>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>
<!-- 6. New Products -->
<?php if (!empty($new_products)): ?>
<section class="va-section">
    <?php $this->load->view('home/partials/section_title', ['title' => 'New Products', 'link' => base_url('accessories'), 'slider' => true]); ?>
    <div class="va-new-layout">
        <aside class="va-side-ad va-side-ad-light">
            <span>Brake Plates</span>
            <h3>Hydraulic Brakes</h3>
            <a href="<?= base_url('accessories') ?>">Shop All</a>
        </aside>
        <div class="va-product-grid va-grid-5 va-slider-track">
            <?php foreach (array_slice($new_products, 0, 18) as $product): ?>
                <?php $this->load->view('home/partials/product_card', ['product' => $product, 'badge' => 'New']); ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- 7. Why Choose Us -->
<section class="va-why-choose">
    <div class="va-why-copy">
        <span>Why Choose Us</span>
        <h2>Engineered For Excellence, Repaired With Care</h2>
        <p>We help keep every journey smooth with dependable parts, knowledgeable support, and service you can rely on.</p>
        <ul><li>Genuine quality parts for your vehicle</li><li>Expert guidance before and after purchase</li><li>Competitive prices and reliable delivery</li><li>Parts selected for everyday performance</li></ul>
        <a href="<?= base_url('about') ?>" class="btn va-why-button">Learn More</a>
    </div>
    <div class="va-why-image"><img src="<?= $hero_image ?>" alt="Professional vehicle service"></div>
</section>

<!-- 8. Hot Sold (Deal) -->
<?php if (!empty($hot_sold_products)): ?>
<section class="va-section">
    <?php $this->load->view('home/partials/section_title', ['title' => 'Hot Sold Products', 'link' => base_url('accessories'), 'slider' => true]); ?>
    <div class="va-product-grid va-grid-5 va-slider-track">
        <?php foreach (array_slice($hot_sold_products, 0, 12) as $product): ?>
            <?php $this->load->view('home/partials/product_card', ['product' => $product, 'badge' => 'Hot Sold']); ?>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>