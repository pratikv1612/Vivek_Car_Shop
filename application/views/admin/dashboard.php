<h1 class="h3 mb-4">Dashboard</h1>
<div class="row g-4 mb-4">
    <div class="col-md-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <h6 class="text-muted">Total Cars</h6>
                <h3><?= $total_cars ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <h6 class="text-muted">Pending Approval</h6>
                <h3><?= $pending_cars ?></h3>
                <?php if ($pending_cars): ?><a href="<?= base_url('admin/cars?status=pending') ?>">View</a><?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <h6 class="text-muted">Accessories</h6>
                <h3><?= $total_accessories ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <h6 class="text-muted">Total Inquiries</h6>
                <h3><?= $total_inquiries ?></h3>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-header"><strong>Recent Inquiries</strong></div>
    <div class="card-body">
        <?php if (empty($recent_inquiries)): ?>
        <p class="text-muted mb-0">No inquiries yet.</p>
        <?php else: ?>
        <div class="table-responsive"><table class="table table-sm">
            <thead><tr><th>Date</th><th>Type</th><th>Customer</th><th>Item</th></tr></thead>
            <tbody>
                <?php foreach ($recent_inquiries as $i): ?>
                <tr>
                    <td><?= date('d M Y H:i', strtotime($i->created_at)) ?></td>
                    <td><?= ucfirst($i->type) ?></td>
                    <td><?= html_escape($i->customer_name) ?></td>
                    <td><?= html_escape($i->car_variant ?? $i->accessory_name ?? '-') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table></div>
        <a href="<?= base_url('admin/inquiries') ?>" class="btn btn-sm btn-outline-primary">View All</a>
        <?php endif; ?>
    </div>
</div>
