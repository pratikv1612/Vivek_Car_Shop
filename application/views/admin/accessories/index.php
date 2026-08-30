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
                <a href="<?= base_url('admin/accessories/edit/'.$a->id) ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                <button class="btn btn-sm btn-outline-danger" onclick="deleteAccessory(<?= $a->id ?>)">Delete</button>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table></div>

<?php if ($total_pages > 1): ?>
<nav>
    <ul class="pagination">
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
        <li class="page-item <?= $i==$page ? 'active' : '' ?>">
            <a class="page-link" href="<?= base_url('admin/accessories/index/'.$i) ?>"><?= $i ?></a>
        </li>
        <?php endfor; ?>
    </ul>
</nav>
<?php endif; ?>

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
