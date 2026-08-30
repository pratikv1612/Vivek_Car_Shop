<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">My Account</h1>
        <a href="<?= base_url('logout') ?>" class="btn btn-outline-danger btn-sm">Logout</a>
    </div>

    <div class="row">
        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><?= html_escape($user->name) ?></h5>
                    <p class="text-muted mb-0"><?= html_escape($user->email) ?></p>
                    <?php if ($user->phone): ?><p class="text-muted small"><?= html_escape($user->phone) ?></p><?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header"><strong>Saved Cars</strong></div>
                <div class="card-body">
                    <?php if (empty($saved_cars)): ?>
                    <p class="text-muted mb-0">No saved cars. <a href="<?= base_url('cars') ?>">Browse cars</a> and save your favorites.</p>
                    <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($saved_cars as $sc): 
                            $car_url = base_url('car/'.$sc->id.'/'.url_title($sc->brand_name.'-'.$sc->model_name,'-',TRUE));
                        ?>
                        <a href="<?= $car_url ?>" class="list-group-item list-group-item-action">
                            <?= html_escape($sc->brand_name.' '.$sc->model_name) ?> - ₹ <?= number_format($sc->price/100000, 1) ?> Lakh
                        </a>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header"><strong>Past Billing / Order History</strong></div>
                <div class="card-body">
                    <?php if (empty($billing_history)): ?>
                    <p class="text-muted mb-0">No past billing yet. Complete a checkout to see your order history here.</p>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($billing_history as $order): ?>
                                <tr>
                                    <td><?= html_escape($order['id']) ?></td>
                                    <td><?= html_escape($order['date']) ?></td>
                                    <td>₹ <?= number_format($order['amount']) ?></td>
                                    <td><?= html_escape($order['status']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><strong>Inquiry History</strong></div>
                <div class="card-body">
                    <?php if (empty($inquiries)): ?>
                    <p class="text-muted mb-0">No inquiries yet.</p>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead><tr><th>Date</th><th>Type</th><th>Item</th></tr></thead>
                            <tbody>
                                <?php foreach ($inquiries as $inq): ?>
                                <tr>
                                    <td><?= date('d M Y', strtotime($inq->created_at)) ?></td>
                                    <td><?= ucfirst($inq->type) ?></td>
                                    <td><?= html_escape($inq->car_variant ?? $inq->accessory_name ?? '-') ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
