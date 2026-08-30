<?php
$entity = $entity ?? 'product';

switch ($entity) {
    case 'banner':
        ?>
        <h1 class="h3 mb-4"><?= $banner ? 'Edit Banner' : 'Add Banner' ?></h1>

        <div id="msg"></div>

        <form id="bannerForm" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-7">
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" name="title" class="form-control" maxlength="200" value="<?= $banner ? html_escape($banner->title) : '' ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Image <?= $banner ? '' : '*' ?></label>
                        <input type="file" name="image" class="form-control" accept="image/*" <?= $banner ? '' : 'required' ?>>
                        <div class="form-text">Recommended size: wide hero image, up to 5MB.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Link URL</label>
                        <input type="text" name="link_url" class="form-control" maxlength="500" placeholder="cars or https://example.com" value="<?= $banner ? html_escape($banner->link_url) : '' ?>">
                    </div>

                    <?php $placement = $banner->placement ?? 'home_main'; ?>
                    <div class="mb-3">
                        <label class="form-label">Banner Placement *</label>
                        <select name="placement" id="bannerPlacement" class="form-select" required>
                            <option value="home_main" <?= $placement === 'home_main' ? 'selected' : '' ?>>Main Banner (Home)</option>
                            <option value="home_sub" <?= $placement === 'home_sub' ? 'selected' : '' ?>>Sub Banner (Home)</option>
                            <option value="new_products" <?= $placement === 'new_products' ? 'selected' : '' ?>>New Products Banner</option>
                            <option value="about" <?= $placement === 'about' ? 'selected' : '' ?>>About Page Banner</option>
                            <option value="contact" <?= $placement === 'contact' ? 'selected' : '' ?>>Contact Page Banner</option>
                            <option value="custom" <?= !in_array($placement, ['home_main', 'home_sub', 'new_products', 'about', 'contact'], true) ? 'selected' : '' ?>>Other Page / Custom Location</option>
                        </select>
                    </div>

                    <div class="mb-3" id="customPlacementWrap">
                        <label class="form-label">Custom Placement</label>
                        <input type="text" name="placement_key" class="form-control" maxlength="100" placeholder="e.g. services_page" value="<?= !in_array($placement, ['home_main', 'home_sub', 'new_products', 'about', 'contact'], true) ? html_escape($placement) : '' ?>">
                        <div class="form-text">Use this key when adding a banner area to another page.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Display Order</label>
                        <input type="number" name="display_order" class="form-control" value="<?= $banner ? (int) $banner->display_order : 0 ?>">
                    </div>

                    <div class="form-check mb-4">
                        <input type="checkbox" name="is_active" id="isActive" class="form-check-input" value="1" <?= (!$banner || $banner->is_active) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="isActive">Active</label>
                    </div>

                    <button type="submit" class="btn btn-primary"><?= $banner ? 'Update Banner' : 'Save Banner' ?></button>
                    <a href="<?= base_url('admin/banners') ?>" class="btn btn-secondary">Cancel</a>
                </div>

                <?php if ($banner): ?>
                <div class="col-md-5">
                    <label class="form-label">Current Image</label>
                    <div class="border rounded overflow-hidden">
                        <img src="<?= base_url($banner->image_path) ?>" alt="<?= html_escape($banner->title) ?>" class="img-fluid w-100" style="max-height:260px;object-fit:cover;">
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </form>

        <script>
        $('#bannerForm').submit(function(e) {
            e.preventDefault();

            let formData = new FormData(this);
            let $submitBtn = $(this).find('button[type="submit"]');
            let originalText = $submitBtn.text();

            $submitBtn.prop('disabled', true).text('Saving...');

            $.ajax({
                url: "<?= $banner ? base_url('admin/banners/update/' . $banner->id) : base_url('admin/banners/store') ?>",
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(res) {
                    let r = typeof res === 'string' ? JSON.parse(res) : res;
                    if (r.status) {
                        $('#msg').html('<div class="alert alert-success">' + r.message + '</div>');
                        setTimeout(function() {
                            window.location.href = "<?= base_url('admin/banners') ?>";
                        }, 500);
                    } else {
                        $('#msg').html('<div class="alert alert-danger">' + (r.errors || r.message) + '</div>');
                        $submitBtn.prop('disabled', false).text(originalText);
                    }
                },
                error: function() {
                    $('#msg').html('<div class="alert alert-danger">Request failed</div>');
                    $submitBtn.prop('disabled', false).text(originalText);
                }
            });
        });

        function toggleCustomPlacement() {
            $('#customPlacementWrap').toggle($('#bannerPlacement').val() === 'custom');
        }

        $('#bannerPlacement').on('change', toggleCustomPlacement);
        toggleCustomPlacement();
        </script>
        <?php
        break;

    case 'product':
        ?>
        <h1 class="h3 mb-4"><?= $product ? 'Edit Product' : 'Add Product' ?></h1>

        <div id="msg"></div>

        <form id="productForm" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Product Name *</label>
                        <input type="text" name="name" class="form-control" required value="<?= $product ? html_escape($product->name) : '' ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select name="category_id" class="form-select">
                            <option value="">Select</option>
                            <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat->id ?>" <?= ($product && $product->category_id == $cat->id) ? 'selected' : '' ?>><?= html_escape($cat->name) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Brand</label>
                        <select name="brand_id" class="form-select" id="productBrand">
                            <option value="">Select</option>
                            <?php foreach ($brands as $b): ?>
                            <option value="<?= $b->id ?>" <?= ($product && $product->brand_id == $b->id) ? 'selected' : '' ?>><?= html_escape($b->name) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Model</label>
                        <select name="model_id" class="form-select" id="productModel">
                            <option value="">Select brand first</option>
                            <?php foreach ($models as $m): ?>
                            <option value="<?= $m->id ?>" <?= ($product && $product->model_id == $m->id) ? 'selected' : '' ?>><?= html_escape($m->name) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Price *</label>
                        <input type="number" name="price" class="form-control" required value="<?= $product ? $product->price : '' ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Display Order</label>
                        <input type="number" name="display_order" class="form-control" value="<?= $product ? (int) $product->display_order : 0 ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="product_status" class="form-select">
                            <?php $status = $product ? $product->product_status : 'available'; ?>
                            <option value="available" <?= $status === 'available' ? 'selected' : '' ?>>Available</option>
                            <option value="sold" <?= $status === 'sold' ? 'selected' : '' ?>>Sold</option>
                            <option value="hidden" <?= $status === 'hidden' ? 'selected' : '' ?>>Hidden</option>
                        </select>
                    </div>

                    <div class="d-flex gap-4 mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="is_featured" class="form-check-input" id="isFeatured" value="1" <?= ($product && $product->is_featured) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="isFeatured"><i class="bi bi-star-fill text-warning"></i> Featured</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" name="is_hot_sold" class="form-check-input" id="isHotSold" value="1" <?= ($product && $product->is_hot_sold) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="isHotSold"><i class="bi bi-fire text-danger"></i> Hot Sold (Deal)</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" name="is_new" class="form-check-input" id="isNew" value="1" <?= (!$product || $product->is_new) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="isNew">New Product</label>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="4"><?= $product ? html_escape($product->description) : '' ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Compatible Models</label>
                        <textarea name="compatible_models" class="form-control" rows="3"><?= $product ? html_escape($product->compatible_models) : '' ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Images</label>
                        <input type="file" name="images[]" class="form-control" multiple accept="image/*">
                        <?php if ($product): ?><small class="text-muted">Uploading new images will replace existing ones.</small><?php endif; ?>
                    </div>

                    <?php if (!empty($product_images)): ?>
                    <div class="mb-3">
                        <label class="form-label">Current Images</label>
                        <div class="d-flex flex-wrap gap-2 mt-2">
                            <?php foreach ($product_images as $img): ?>
                            <div class="position-relative border rounded overflow-hidden" style="width:90px;height:90px;">
                                <img src="<?= base_url($img->image_path) ?>" alt="" style="width:100%;height:100%;object-fit:cover">
                                <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0" style="padding:2px 5px;font-size:12px;" onclick="deleteProductImage(<?= (int) $img->id ?>)">x</button>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <button type="submit" class="btn btn-primary"><?= $product ? 'Update Product' : 'Save Product' ?></button>
            <a href="<?= base_url('admin/products') ?>" class="btn btn-secondary">Cancel</a>
        </form>

        <script>
        $('#productForm').submit(function(e) {
            e.preventDefault();
            let formData = new FormData(this);
            let $submitBtn = $(this).find('button[type="submit"]');
            let originalText = $submitBtn.text();
            $submitBtn.prop('disabled', true).text('Saving...');

            $.ajax({
                url: "<?= $product ? base_url('admin/products/update/' . $product->id) : base_url('admin/products/store') ?>",
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(res) {
                    let r = typeof res === 'string' ? JSON.parse(res) : res;
                    if (r.status) {
                        $('#msg').html('<div class="alert alert-success">' + r.message + '</div>');
                        setTimeout(function() {
                            window.location.href = "<?= base_url('admin/products') ?>";
                        }, 500);
                    } else {
                        $('#msg').html('<div class="alert alert-danger">' + (r.errors || r.message) + '</div>');
                        $submitBtn.prop('disabled', false).text(originalText);
                    }
                },
                error: function() {
                    $('#msg').html('<div class="alert alert-danger">Request failed</div>');
                    $submitBtn.prop('disabled', false).text(originalText);
                }
            });
        });

        $('#productBrand').change(function() {
            let id = $(this).val();
            if (!id) {
                $('#productModel').html('<option value="">Select brand first</option>');
                return;
            }

            $.get("<?= base_url('admin/cars/models_by_brand') ?>?brand_id=" + id, function(res) {
                let data = typeof res === 'string' ? JSON.parse(res) : res;
                let html = '<option value="">Select</option>';
                data.forEach(function(m) {
                    html += '<option value="' + m.id + '">' + m.name + '</option>';
                });
                $('#productModel').html(html);
            });
        });

        function deleteProductImage(imageId) {
            if (!confirm('Are you sure you want to delete this image?')) return;

            $.ajax({
                url: "<?= base_url('admin/products/delete-image/') ?>" + imageId,
                type: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                success: function(res) {
                    let r = typeof res === 'string' ? JSON.parse(res) : res;
                    if (r.status) {
                        location.reload();
                    } else {
                        alert(r.message || 'Failed to delete image');
                    }
                },
                error: function() {
                    alert('Error deleting image');
                }
            });
        }
        </script>
        <?php
        break;

    default:
        echo '<div class="alert alert-warning">No form configured for this section.</div>';
        break;
}
