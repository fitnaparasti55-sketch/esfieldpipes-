<?php
/**
 * Product Catalog Management
 * Esfield Pipe Platform
 */
$pageTitle = "Product Catalog";
require_once __DIR__ . '/includes/header.php';

$db = get_db();

// Search & Filter parameters
$search = trim($_GET['search'] ?? '');
$categoryId = !empty($_GET['category']) ? (int)$_GET['category'] : 0;
$stiffness = trim($_GET['stiffness'] ?? '');
$status = trim($_GET['status'] ?? '');

$query = "
    SELECT p.*, c.name as category_name
    FROM `products` p
    LEFT JOIN `categories` c ON p.category_id = c.id
    WHERE 1=1
";
$params = [];

if (!empty($search)) {
    $query .= " AND (p.name LIKE ? OR p.slug LIKE ? OR p.raw_material LIKE ?)";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
}

if ($categoryId > 0) {
    $query .= " AND p.category_id = ?";
    $params[] = $categoryId;
}

if (!empty($stiffness)) {
    $query .= " AND p.stiffness_class = ?";
    $params[] = $stiffness;
}

if (!empty($status)) {
    $query .= " AND p.status = ?";
    $params[] = $status;
}

$query .= " ORDER BY p.inner_diameter_mm ASC, p.created_at DESC";

$stmt = $db->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll();

// Fetch all categories for filter dropdown
$categories = $db->query("SELECT id, name FROM `categories` ORDER BY `name` ASC")->fetchAll();
?>

<!-- Header Actions -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h4 class="fw-black mb-1 text-dark">DWC Pipe Catalog</h4>
        <p class="text-muted small mb-0">Manage high-density polyethylene corrugated pipe inventory and specifications.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="product-add.php" class="btn btn-primary px-4 py-2 fw-bold">
            <i class="fa-solid fa-circle-plus me-1"></i> Add New Product
        </a>
    </div>
</div>

<!-- Search and Filter Bar -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form method="GET" action="products.php" class="row g-2 align-items-center">
            <div class="col-md-4">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Search size, name or resin..." value="<?= htmlspecialchars($search) ?>">
                </div>
            </div>
            <div class="col-md-3">
                <select name="category" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Categories</option>
                    <?php foreach($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= $categoryId === (int)$cat['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <select name="stiffness" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Stiffness</option>
                    <option value="SN4" <?= $stiffness === 'SN4' ? 'selected' : '' ?>>SN4 (>= 4 kN/m²)</option>
                    <option value="SN8" <?= $stiffness === 'SN8' ? 'selected' : '' ?>>SN8 (>= 8 kN/m²)</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Active Only</option>
                    <option value="inactive" <?= $status === 'inactive' ? 'selected' : '' ?>>Inactive Only</option>
                </select>
            </div>
            <div class="col-md-1 d-flex gap-1">
                <button type="submit" class="btn btn-sm btn-dark w-100 fw-bold">Filter</button>
                <?php if (!empty($search) || $categoryId > 0 || !empty($stiffness) || !empty($status)): ?>
                    <a href="products.php" class="btn btn-sm btn-outline-secondary" title="Reset Filters"><i class="fa-solid fa-rotate-left"></i></a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<!-- Products Table Card -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-4" style="width: 70px;">Image</th>
                        <th>Product Specs & Material</th>
                        <th>Category</th>
                        <th>Stiffness</th>
                        <th>Price Ex-Works</th>
                        <th>Stock</th>
                        <th>Featured</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($products)): ?>
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-boxes-stacked fs-1 text-secondary opacity-50 mb-3 d-block"></i>
                                No pipe products found matching your filter criteria.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($products as $p): ?>
                        <tr>
                            <td class="ps-4">
                                <img src="<?= BASE_URL . ($p['image'] ?: 'assets/images/logo.svg') ?>" alt="Product" class="rounded border" style="width: 50px; height: 50px; object-fit: contain; background: #fff;">
                            </td>
                            <td>
                                <div class="fw-bold text-dark"><?= htmlspecialchars($p['name']) ?></div>
                                <div class="small text-muted">
                                    <span class="badge bg-dark text-warning"><?= $p['inner_diameter_mm'] ?> mm ID</span>
                                    <span class="badge bg-secondary text-light"><?= $p['outer_diameter_mm'] ?> mm OD</span>
                                    <span class="text-muted ms-1"><?= htmlspecialchars($p['raw_material'] ?? '') ?></span>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border"><?= htmlspecialchars($p['category_name'] ?? 'General') ?></span>
                            </td>
                            <td>
                                <span class="badge <?= $p['stiffness_class'] === 'SN8' ? 'bg-primary' : 'bg-info text-dark' ?> fw-bold">
                                    <?= $p['stiffness_class'] ?>
                                </span>
                            </td>
                            <td>
                                <div class="fw-bold text-primary">₹<?= number_format($p['price_per_meter'], 2) ?> <span class="text-muted fw-normal small">/m</span></div>
                                <div class="small text-muted">₹<?= number_format($p['price_per_pipe'], 2) ?> / 6m pipe</div>
                            </td>
                            <td>
                                <span class="badge <?= $p['stock_quantity'] < 50 ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success' ?>">
                                    <?= number_format($p['stock_quantity']) ?> pcs
                                </span>
                            </td>
                            <td>
                                <form action="<?= BASE_URL ?>api/admin-products.php" method="POST" class="d-inline">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="action" value="toggle_featured">
                                    <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                                    <button type="submit" class="btn btn-sm p-0 border-0 bg-transparent" title="Toggle Featured">
                                        <?php if ($p['featured']): ?>
                                            <i class="fa-solid fa-star text-warning fs-5"></i>
                                        <?php else: ?>
                                            <i class="fa-regular fa-star text-muted fs-5"></i>
                                        <?php endif; ?>
                                    </button>
                                </form>
                            </td>
                            <td>
                                <form action="<?= BASE_URL ?>api/admin-products.php" method="POST" class="d-inline">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="action" value="toggle_status">
                                    <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                                    <button type="submit" class="badge rounded-pill border-0 <?= $p['status'] === 'active' ? 'bg-success text-white' : 'bg-secondary text-white' ?>" style="cursor: pointer;">
                                        <?= ucfirst($p['status']) ?>
                                    </button>
                                </form>
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group">
                                    <a href="product-edit.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-outline-primary" title="Edit">
                                        <i class="fa-solid fa-pencil"></i>
                                    </a>
                                    <a href="<?= BASE_URL ?>product-detail.php?id=<?= $p['id'] ?>" target="_blank" class="btn btn-sm btn-outline-secondary" title="View on Frontend">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDeleteProduct(<?= $p['id'] ?>, '<?= htmlspecialchars(addslashes($p['name'])) ?>')" title="Delete">
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

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteProductModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="<?= BASE_URL ?>api/admin-products.php" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="product_id" id="deleteProductId">

                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-danger"><i class="fa-solid fa-triangle-exclamation me-2"></i> Delete Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body py-4">
                    <p class="mb-0">Are you sure you want to permanently delete <strong id="deleteProductName"></strong> from the catalog? This will safely remove associated images as well.</p>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger fw-bold">Delete Product</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function confirmDeleteProduct(id, name) {
    document.getElementById('deleteProductId').value = id;
    document.getElementById('deleteProductName').textContent = name;
    new bootstrap.Modal(document.getElementById('deleteProductModal')).show();
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
