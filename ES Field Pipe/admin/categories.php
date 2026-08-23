<?php
/**
 * Category Management - Esfield Pipe
 */
$pageTitle = "Category Management";
require_once __DIR__ . '/includes/header.php';

$db = get_db();

// Fetch categories with live product counts
$categories = $db->query("
    SELECT c.*, COUNT(p.id) as product_count
    FROM `categories` c
    LEFT JOIN `products` p ON c.id = p.category_id
    GROUP BY c.id
    ORDER BY c.display_order ASC, c.name ASC
")->fetchAll();
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h4 class="fw-black mb-1 text-dark">DWC Application Categories</h4>
        <p class="text-muted small mb-0">Organize piping applications into intuitive industry sectors.</p>
    </div>
    <button type="button" class="btn btn-primary px-4 py-2 fw-bold" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
        <i class="fa-solid fa-folder-plus me-1"></i> Add Category
    </button>
</div>

<div class="row g-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted small text-uppercase">
                            <tr>
                                <th class="ps-4" style="width: 50px;">Icon</th>
                                <th>Category Name & Slug</th>
                                <th>Description</th>
                                <th>Products</th>
                                <th>Sort Order</th>
                                <th>Status</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($categories)): ?>
                                <tr><td colspan="7" class="text-center py-5 text-muted">No categories created yet.</td></tr>
                            <?php else: ?>
                                <?php foreach($categories as $cat): ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="bg-primary bg-opacity-10 text-primary rounded-3 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; font-size: 1.25rem;">
                                            <i class="<?= htmlspecialchars($cat['icon'] ?: 'fa-solid fa-water-ladder') ?>"></i>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($cat['name']) ?></div>
                                        <div class="small text-muted font-monospace">/products.php?category=<?= htmlspecialchars($cat['slug']) ?></div>
                                    </td>
                                    <td style="max-width: 320px;">
                                        <div class="small text-muted text-truncate"><?= htmlspecialchars($cat['description'] ?? 'No description') ?></div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border px-2.5 py-1.5 fw-bold">
                                            <?= $cat['product_count'] ?> products
                                        </span>
                                    </td>
                                    <td class="fw-bold text-muted"><?= $cat['display_order'] ?></td>
                                    <td>
                                        <form action="<?= BASE_URL ?>api/admin-categories.php" method="POST" class="d-inline">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="action" value="toggle_status">
                                            <input type="hidden" name="category_id" value="<?= $cat['id'] ?>">
                                            <button type="submit" class="badge rounded-pill border-0 <?= $cat['status'] === 'active' ? 'bg-success text-white' : 'bg-secondary text-white' ?>" style="cursor: pointer;">
                                                <?= ucfirst($cat['status']) ?>
                                            </button>
                                        </form>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="openEditCategoryModal(<?= htmlspecialchars(json_encode($cat)) ?>)" title="Edit">
                                                <i class="fa-solid fa-pencil"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDeleteCategory(<?= $cat['id'] ?>, '<?= htmlspecialchars(addslashes($cat['name'])) ?>', <?= $cat['product_count'] ?>)" title="Delete">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form action="<?= BASE_URL ?>api/admin-categories.php" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="add">

                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-dark"><i class="fa-solid fa-folder-plus text-primary me-2"></i> Add New Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body py-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Category Name *</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Underground Drainage & Sewerage" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Slug (Optional)</label>
                        <input type="text" name="slug" class="form-control" placeholder="e.g. underground-drainage-sewerage">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">FontAwesome Icon Class</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fa-solid fa-icons"></i></span>
                            <input type="text" name="icon" class="form-control" value="fa-solid fa-water-ladder" placeholder="fa-solid fa-road">
                        </div>
                        <small class="text-muted">Example: fa-solid fa-road, fa-solid fa-bolt, fa-solid fa-industry</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Description</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Overview of engineering use case..."></textarea>
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label fw-bold small">Display Order</label>
                            <input type="number" name="display_order" class="form-control" value="0">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold small">Status</label>
                            <select name="status" class="form-select">
                                <option value="active" selected>Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold px-4">Create Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Category Modal -->
<div class="modal fade" id="editCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form action="<?= BASE_URL ?>api/admin-categories.php" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="category_id" id="editCatId">

                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-dark"><i class="fa-solid fa-pencil text-primary me-2"></i> Edit Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body py-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Category Name *</label>
                        <input type="text" name="name" id="editCatName" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Slug *</label>
                        <input type="text" name="slug" id="editCatSlug" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">FontAwesome Icon</label>
                        <input type="text" name="icon" id="editCatIcon" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Description</label>
                        <textarea name="description" id="editCatDesc" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label fw-bold small">Display Order</label>
                            <input type="number" name="display_order" id="editCatOrder" class="form-control">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold small">Status</label>
                            <select name="status" id="editCatStatus" class="form-select">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold px-4">Update Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Category Modal -->
<div class="modal fade" id="deleteCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="<?= BASE_URL ?>api/admin-categories.php" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="category_id" id="deleteCatId">

                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-danger"><i class="fa-solid fa-triangle-exclamation me-2"></i> Delete Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body py-4">
                    <p class="mb-0">Are you sure you want to delete <strong id="deleteCatName"></strong>?</p>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger fw-bold">Delete Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openEditCategoryModal(cat) {
    document.getElementById('editCatId').value = cat.id;
    document.getElementById('editCatName').value = cat.name;
    document.getElementById('editCatSlug').value = cat.slug;
    document.getElementById('editCatIcon').value = cat.icon;
    document.getElementById('editCatDesc').value = cat.description || '';
    document.getElementById('editCatOrder').value = cat.display_order || 0;
    document.getElementById('editCatStatus').value = cat.status;
    new bootstrap.Modal(document.getElementById('editCategoryModal')).show();
}

function confirmDeleteCategory(id, name, count) {
    if (count > 0) {
        alert('This category currently contains ' + count + ' products. Please delete or reassign products first.');
        return;
    }
    document.getElementById('deleteCatId').value = id;
    document.getElementById('deleteCatName').textContent = name;
    new bootstrap.Modal(document.getElementById('deleteCategoryModal')).show();
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
