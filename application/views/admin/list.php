<?php
$entity = $entity ?? 'users';

$render_pagination = function ($base, $page, $total_pages, $query = []) {
    if ($total_pages <= 1) {
        return;
    }
    $query = is_array($query) ? $query : [];
    $qs = http_build_query($query);
    ?>
    <nav>
        <ul class="pagination">
            <?php for ($p = 1; $p <= $total_pages; $p++): ?>
                <li class="page-item <?= $p == $page ? 'active' : '' ?>">
                    <a class="page-link" href="<?= $base . $p ?><?= $qs !== '' ? '?' . $qs : '' ?>"><?= $p ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
    <?php
};

switch ($entity) {
    case 'users':
        ?>
        <h1 class="h3 mb-4">Manage Users</h1>
        <form method="get" class="mb-3">
            <div class="input-group" style="max-width:400px">
                <input type="text" name="keyword" class="form-control" value="<?= html_escape($filters['keyword'] ?? '') ?>" placeholder="Search by name or email">
                <button type="submit" class="btn btn-primary">Search</button>
            </div>
        </form>
        <div class="table-responsive"><table class="table table-bordered">
            <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Joined</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                <tr>
                    <td><?= $u->id ?></td>
                    <td><?= html_escape($u->name) ?></td>
                    <td><?= html_escape($u->email) ?></td>
                    <td><?= html_escape($u->phone ?? '-') ?></td>
                    <td><?= date('d M Y', strtotime($u->created_at)) ?></td>
                    <td><span class="badge bg-<?= $u->is_blocked ? 'danger' : 'success' ?>"><?= $u->is_blocked ? 'Blocked' : 'Active' ?></span></td>
                    <td><a href="<?= base_url('admin/users/toggle_block/' . $u->id) ?>" class="btn btn-sm btn-<?= $u->is_blocked ? 'success' : 'warning' ?>"><?= $u->is_blocked ? 'Unblock' : 'Block' ?></a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table></div>
        <?php
        $render_pagination(base_url('admin/users/index/'), $page, $total_pages, $filters);
        break;

    case 'inquiries':
        ?>
        <h1 class="h3 mb-4">Inquiries</h1>
        <form method="get" class="row g-3 mb-4">
            <div class="col-auto"><select name="type" class="form-select form-select-sm"><option value="">All Types</option><option value="car" <?= (isset($filters['type']) && $filters['type'] == 'car') ? 'selected' : '' ?>>Car</option><option value="accessory" <?= (isset($filters['type']) && $filters['type'] == 'accessory') ? 'selected' : '' ?>>Accessory</option><option value="contact" <?= (isset($filters['type']) && $filters['type'] == 'contact') ? 'selected' : '' ?>>Contact</option></select></div>
            <div class="col-auto"><input type="date" name="from_date" class="form-control form-control-sm" value="<?= $filters['from_date'] ?? '' ?>" placeholder="From"></div>
            <div class="col-auto"><input type="date" name="to_date" class="form-control form-control-sm" value="<?= $filters['to_date'] ?? '' ?>" placeholder="To"></div>
            <div class="col-auto"><button type="submit" class="btn btn-sm btn-primary">Filter</button></div>
        </form>
        <div class="table-responsive"><table class="table table-bordered table-sm">
            <thead><tr><th>Date</th><th>Type</th><th>Customer</th><th>Phone</th><th>Item</th><th>Message</th></tr></thead>
            <tbody>
                <?php foreach ($inquiries as $i): ?>
                <tr>
                    <td><?= date('d M Y H:i', strtotime($i->created_at)) ?></td>
                    <td><?= ucfirst($i->type) ?></td>
                    <td><?= html_escape($i->customer_name) ?><?php if (!empty($i->customer_email)): ?><br><small class="text-muted"><?= html_escape($i->customer_email) ?></small><?php endif; ?></td>
                    <td><?= html_escape($i->customer_phone ?? '-') ?></td>
                    <td><?= html_escape($i->car_variant ?? $i->accessory_name ?? '-') ?></td>
                    <td><?= html_escape(character_limiter($i->message ?? '', 50)) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table></div>
        <?php
        $render_pagination(base_url('admin/inquiries/index/'), $page, $total_pages, $filters);
        break;

    case 'banners':
        ?>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Manage Banners</h1>
            <a href="<?= base_url('admin/banners/create') ?>" class="btn btn-primary">
                <i class="bi bi-plus"></i> Add Banner
            </a>
        </div>

        <?php if (empty($banners)): ?>
        <div class="alert alert-info">No banners yet. Add one and choose where it should appear.</div>
        <?php else: ?>
        <div class="table-responsive"><table class="table table-bordered align-middle">
            <thead>
                <tr>
                    <th width="120">Image</th>
                    <th>Title</th>
                    <th>Link</th>
                    <th>Placement</th>
                    <th width="90">Order</th>
                    <th width="90">Status</th>
                    <th width="160">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($banners as $banner): ?>
                <tr>
                    <td>
                        <img src="<?= base_url($banner->image_path) ?>" alt="<?= html_escape($banner->title) ?>" style="width:100px;height:56px;object-fit:cover;border-radius:4px;">
                    </td>
                    <td><?= html_escape(ucwords(str_replace('_', ' ', $banner->placement ?? 'home_main'))) ?></td>
                    <td><?= html_escape($banner->title ?: 'Untitled banner') ?></td>
                    <td>
                        <?php if ($banner->link_url): ?>
                            <span class="text-break"><?= html_escape($banner->link_url) ?></span>
                        <?php else: ?>
                            <span class="text-muted">No link</span>
                        <?php endif; ?>
                    </td>
                    <td><?= (int) $banner->display_order ?></td>
                    <td>
                        <span class="badge bg-<?= $banner->is_active ? 'success' : 'secondary' ?>">
                            <?= $banner->is_active ? 'Active' : 'Hidden' ?>
                        </span>
                    </td>
                    <td>
                        <a href="<?= base_url('admin/banners/edit/' . $banner->id) ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteBanner(<?= (int) $banner->id ?>)">Delete</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table></div>
        <?php endif; ?>

        <div class="modal fade" id="deleteModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirm Delete</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">Are you sure you want to delete this banner?</div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button class="btn btn-danger" id="confirmDeleteBtn">Yes, Delete</button>
                    </div>
                </div>
            </div>
        </div>

        <script>
        let deleteId = null;
        let deleteModal = null;

        document.addEventListener('DOMContentLoaded', function () {
            deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        });

        function deleteBanner(id) {
            deleteId = id;
            deleteModal.show();
        }

        document.getElementById('confirmDeleteBtn').addEventListener('click', function () {
            fetch("<?= base_url('admin/banners/delete/') ?>" + deleteId, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.json())
            .then(data => {
                deleteModal.hide();
                if (data.status) {
                    location.reload();
                } else {
                    alert(data.message || 'Delete failed');
                }
            })
            .catch(() => alert('Error occurred'));
        });
        </script>
        <?php
        break;

    case 'cars':
        ?>
        <h1 class="h3 mb-4">Manage Cars</h1>

        <div class="mb-3">
            <a href="<?= base_url('admin/cars/create') ?>" class="btn btn-primary">
                <i class="bi bi-plus"></i> Add Car
            </a>

            <a href="<?= base_url('admin/cars') ?>" class="btn btn-outline-secondary">All</a>
            <a href="<?= base_url('admin/cars?status=pending') ?>" class="btn btn-outline-warning">Pending</a>
            <a href="<?= base_url('admin/cars?status=approved') ?>" class="btn btn-outline-success">Approved</a>
        </div>

        <div class="table-responsive"><table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Car</th>
                    <th>Price</th>
                    <th>City</th>
                    <th>Status</th>
                    <th width="150">Actions</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($cars as $c): ?>
                <tr>
                    <td><?= $c->id ?></td>

                    <td>
                        <?= html_escape($c->brand_name . ' ' . $c->model_name) ?>
                        <?= $c->variant ? ' ' . $c->variant : '' ?>
                    </td>

                    <td>
                        ₹ <?= number_format($c->price / 100000, 1) ?> L
                    </td>

                    <td><?= $c->city_name ?></td>

                    <td>
                        <span class="badge bg-<?= $c->status == 'approved' ? 'success' : ($c->status == 'pending' ? 'warning' : 'secondary') ?>">
                            <?= $c->status ?>
                        </span>
                    </td>

                    <td>
                        <a href="<?= base_url('admin/cars/edit/' . $c->id) ?>"
                           class="btn btn-sm btn-outline-primary">
                           Edit
                        </a>

                        <button class="btn btn-sm btn-outline-danger"
                                onclick="deleteCar(<?= $c->id ?>)">
                            Delete
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table></div>

        <?php
        $render_pagination(base_url('admin/cars/index/'), $page, $total_pages, $_GET);
        ?>

        <div class="modal fade" id="deleteModal" tabindex="-1">
          <div class="modal-dialog">
            <div class="modal-content">

              <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>

              <div class="modal-body">
                Are you sure you want to delete this car?
              </div>

              <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-danger" id="confirmDeleteBtn">Yes, Delete</button>
              </div>

            </div>
          </div>
        </div>

        <script>
            let deleteId = null;
            let deleteModal = null;

            document.addEventListener("DOMContentLoaded", function () {
                deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            });

            function deleteCar(id) {
                deleteId = id;
                deleteModal.show();
            }

            document.getElementById("confirmDeleteBtn").addEventListener("click", function () {
                fetch("<?= base_url('admin/cars/delete/') ?>" + deleteId, {
                    method: "POST",
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => {
                    if (!res.ok) {
                        throw new Error('HTTP ' + res.status);
                    }
                    return res.json();
                })
                .then(data => {

                    deleteModal.hide();

                    if (data.status) {
                        alert("Deleted successfully");
                        location.reload();
                    } else {
                        alert("Delete failed");
                    }
                })
                .catch(err => {
                    console.log(err);
                    alert("Error occurred");
                });
            });
        </script>
        <?php
        break;

    case 'products':
        ?>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Manage Products</h1>
            <a href="<?= base_url('admin/products/create') ?>" class="btn btn-primary">
                <i class="bi bi-plus"></i> Add Product
            </a>
        </div>

        <div class="mb-3 d-flex flex-wrap gap-2">
            <a href="<?= base_url('admin/products') ?>" class="btn btn-sm btn-outline-secondary">All</a>
            <a href="<?= base_url('admin/products?featured=1') ?>" class="btn btn-sm btn-outline-warning"><i class="bi bi-star-fill"></i> Featured</a>
            <a href="<?= base_url('admin/products?hot_sold=1') ?>" class="btn btn-sm btn-outline-danger"><i class="bi bi-fire"></i> Hot Sold</a>
            <a href="<?= base_url('admin/products?new=1') ?>" class="btn btn-sm btn-outline-success">New Products</a>
            <a href="<?= base_url('admin/products?status=sold') ?>" class="btn btn-sm btn-outline-dark">Sold</a>
        </div>

        <div class="table-responsive"><table class="table table-bordered align-middle">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Product</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Labels</th>
                    <th>Status</th>
                    <th width="170">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($products)): ?>
                <tr>
                    <td colspan="7" class="text-center text-muted">No products found.</td>
                </tr>
                <?php else: ?>
                <?php foreach ($products as $p): ?>
                <tr>
                    <td><?= (int) $p->id ?></td>
                    <td><?= html_escape($p->name) ?></td>
                    <td><?= html_escape($p->category_name ?: '-') ?></td>
                    <td>&#8377; <?= number_format($p->price) ?></td>
                    <td>
                        <?php if ($p->is_featured): ?><span class="badge bg-warning text-dark"><i class="bi bi-star-fill"></i> Featured</span><?php endif; ?>
                        <?php if ($p->is_hot_sold): ?><span class="badge bg-danger"><i class="bi bi-fire"></i> Hot Sold</span><?php endif; ?>
                        <?php if ($p->is_new): ?><span class="badge bg-success">New</span><?php endif; ?>
                        <?php if (!$p->is_featured && !$p->is_hot_sold && !$p->is_new): ?><span class="text-muted">-</span><?php endif; ?>
                    </td>
                    <td>
                        <span class="badge bg-<?= $p->product_status === 'sold' ? 'dark' : ($p->product_status === 'hidden' ? 'secondary' : 'primary') ?>">
                            <?= ucfirst($p->product_status) ?>
                        </span>
                    </td>
                    <td>
                        <a href="<?= base_url('admin/products/edit/' . $p->id) ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteProduct(<?= (int) $p->id ?>)">Delete</button>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table></div>

        <?php
        $render_pagination(base_url('admin/products/index/'), $page, $total_pages, []);
        ?>

        <div class="modal fade" id="deleteModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirm Delete</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">Are you sure you want to delete this product?</div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Yes, Delete</button>
                    </div>
                </div>
            </div>
        </div>

        <script>
        let deleteProductId = null;
        let deleteModal = null;

        document.addEventListener('DOMContentLoaded', function () {
            deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        });

        function deleteProduct(id) {
            deleteProductId = id;
            deleteModal.show();
        }

        document.getElementById('confirmDeleteBtn').addEventListener('click', function () {
            fetch('<?= base_url('admin/products/delete/') ?>' + deleteProductId, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.json())
            .then(data => {
                deleteModal.hide();
                if (data.status) {
                    location.reload();
                } else {
                    alert(data.message || 'Delete failed');
                }
            })
            .catch(() => alert('Delete failed'));
        });
        </script>
        <?php
        break;

    case 'accessories':
        ?>
        <h1 class="h3 mb-4">Manage Accessories</h1>

        <?php if (!empty($success_message)): ?>
        <div class="alert alert-success"><?= html_escape($success_message) ?></div>
        <?php endif; ?>

        <div class="mb-3">
            <a href="<?= base_url('admin/accessories/create') ?>" class="btn btn-primary">
                <i class="bi bi-plus"></i> Add Accessory
            </a>
        </div>

        <div class="table-responsive"><table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Available</th>
                    <th width="170">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($accessories)): ?>
                <tr>
                    <td colspan="6" class="text-center">No accessories found.</td>
                </tr>
                <?php else: ?>
                <?php foreach ($accessories as $a): ?>
                <tr>
                    <td><?= $a->id ?></td>
                    <td><?= html_escape($a->name) ?></td>
                    <td><?= html_escape($a->category_name ?: '-') ?></td>
                    <td>₹ <?= number_format($a->price) ?></td>
                    <td>
                        <span class="badge bg-<?= $a->is_available ? 'success' : 'secondary' ?>">
                            <?= $a->is_available ? 'Yes' : 'No' ?>
                        </span>
                    </td>
                    <td>
                        <a href="<?= base_url('admin/accessories/edit/' . $a->id) ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteAccessory(<?= $a->id ?>)">Delete</button>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table></div>

        <?php
        $render_pagination(base_url('admin/accessories/index/'), $page, $total_pages, []);
        ?>

        <div class="modal fade" id="deleteModal" tabindex="-1">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">Are you sure you want to delete this accessory?</div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Yes, Delete</button>
              </div>
            </div>
          </div>
        </div>

        <script>
            let deleteAccessoryId = null;
            let deleteModal = null;

            document.addEventListener('DOMContentLoaded', function () {
                deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            });

            function deleteAccessory(id) {
                deleteAccessoryId = id;
                deleteModal.show();
            }

            document.getElementById('confirmDeleteBtn').addEventListener('click', function () {
                fetch('<?= base_url('admin/accessories/delete/') ?>' + deleteAccessoryId, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => {
                    if (!res.ok) {
                        throw new Error('HTTP ' + res.status);
                    }
                    return res.json();
                })
                .then(data => {
                    deleteModal.hide();
                    if (data.status) {
                        alert('Accessory deleted successfully');
                        location.reload();
                    } else {
                        alert('Delete failed');
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('Delete failed');
                });
            });
        </script>
        <?php
        break;

    default:
        echo '<div class="alert alert-warning">No list configured for this section.</div>';
        break;
}
