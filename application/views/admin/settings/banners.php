<h1 class="h3 mb-4">Homepage Banners</h1>
<p><a href="<?= base_url('admin/settings/add_banner') ?>" class="btn btn-primary"><i class="bi bi-plus"></i> Add Banner</a></p>
<?php if (empty($banners)): ?>
<p class="text-muted">No banners. Add one to show on homepage.</p>
<?php else: ?>
<div class="table-responsive"><table class="table table-bordered">
    <thead><tr><th>Image</th><th>Title</th><th>Link</th><th>Order</th><th>Actions</th></tr></thead>
    <tbody>
        <?php foreach ($banners as $b): ?>
        <tr>
            <td><img src="<?= base_url($b->image_path) ?>" alt="" style="max-height:60px"></td>
            <td><?= html_escape($b->title) ?></td>
            <td><?= html_escape($b->link_url) ?></td>
            <td><?= $b->display_order ?></td>
            <td><a href="<?= base_url('admin/settings/delete_banner/'.$b->id) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete?')">Delete</a></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table></div>
<?php endif; ?>
