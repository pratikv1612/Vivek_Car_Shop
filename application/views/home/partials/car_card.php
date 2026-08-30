<?php
$car_url = base_url('car/'.$car->id.'/'.url_title($car->brand_name.'-'.$car->model_name, '-', TRUE));
$car_name = $car->brand_name.' '.$car->model_name.($car->variant ? ' '.$car->variant : '');
$image = !empty($car->primary_image) ? $car->primary_image : base_url('assets/images/placeholder-car.svg');
?>
<article class="va-product-card va-car-card">
    <a href="<?= $car_url ?>" class="va-product-image" aria-label="<?= html_escape($car_name) ?>">
        <img src="<?= $image ?>" alt="<?= html_escape($car_name) ?>">
        <span class="va-product-badge">Featured</span>
    </a>
    <div class="va-product-actions">
        <a href="<?= $car_url ?>" class="va-car-view-link" title="View car" aria-label="View car"><i class="bi bi-car-front"></i></a>
        <button type="button" class="car-save-action" data-car-id="<?= $car->id ?>" aria-label="Save vehicle"><i class="bi bi-heart"></i></button>
        <button type="button" class="shop-quick-view" data-name="<?= html_escape($car_name) ?>" data-price="&#8377; <?= number_format($car->price / 100000, 1) ?> Lakh" data-image="<?= html_escape($image) ?>" aria-label="Quick view"><i class="bi bi-eye"></i></button>
    </div>
    <div class="va-product-body">
        <div class="va-product-category"><?= html_escape($car->fuel_type ?? 'Car') ?></div>
        <h3><a href="<?= $car_url ?>"><?= html_escape($car_name) ?></a></h3>
        <div class="va-rating" aria-label="Rated 5 out of 5">
            <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
        </div>
        <div class="va-price">
            <span>&#8377; <?= number_format($car->price / 100000, 1) ?> Lakh</span>
            <del>&#8377; <?= number_format(($car->price * 1.06) / 100000, 1) ?> Lakh</del>
        </div>
        <div class="va-stock"><i class="bi bi-check-circle-fill"></i> Available now</div>
        <a href="<?= $car_url ?>" class="btn va-card-button">View Details</a>
    </div>
</article>