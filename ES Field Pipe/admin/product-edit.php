<?php
/**
 * Edit Product - Esfield Pipe
 */
$pageTitle = "Edit Product";
require_once __DIR__ . '/includes/header.php';

$productId = (int)($_GET['id'] ?? 0);
$db = get_db();

$stmt = $db->prepare("SELECT * FROM `products` WHERE `id` = ?");
$stmt->execute([$productId]);
$product = $stmt->fetch();

if (!$product) {
    set_flash('danger', 'Product not found.');
    header('Location: products.php');
    exit;
}

$categories = $db->query("SELECT * FROM `categories` ORDER BY `display_order` ASC, `name` ASC")->fetchAll();
?>

<div class="row justify-content-center">
    <div class="col-xl-10">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-black mb-1 text-dark">Edit DWC Pipe Product</h4>
                <p class="text-muted small mb-0">Updating: <strong class="text-primary"><?= htmlspecialchars($product['name']) ?></strong></p>
            </div>
            <div class="d-flex gap-2">
                <a href="<?= BASE_URL ?>product-detail.php?id=<?= $product['id'] ?>" target="_blank" class="btn btn-outline-primary btn-sm fw-bold">
                    <i class="fa-solid fa-eye me-1"></i> Preview on Store
                </a>
                <a href="products.php" class="btn btn-outline-secondary btn-sm fw-bold">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back to Catalog
                </a>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4 p-md-5">
                <form action="<?= BASE_URL ?>api/admin-products.php" method="POST" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">

                    <div class="row g-4">
                        <!-- Basic Information -->
                        <div class="col-12">
                            <h6 class="fw-bold border-bottom pb-2 mb-3 text-primary text-uppercase small tracking-wider">
                                <i class="fa-solid fa-info-circle me-1"></i> Basic Identification
                            </h6>
                        </div>

                        <div class="col-md-8">
                            <label class="form-label fw-bold small">Product Title / Name *</label>
                            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($product['name']) ?>" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Category *</label>
                            <select name="category_id" class="form-select" required>
                                <?php foreach($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>" <?= (int)$product['category_id'] === (int)$cat['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cat['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small">URL Slug / SKU *</label>
                            <input type="text" name="slug" class="form-control" value="<?= htmlspecialchars($product['slug']) ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Application Type</label>
                            <input type="text" name="application_type" class="form-control" value="<?= htmlspecialchars($product['application_type'] ?? '') ?>">
                        </div>

                        <!-- Technical Specs -->
                        <div class="col-12 mt-4">
                            <h6 class="fw-bold border-bottom pb-2 mb-3 text-primary text-uppercase small tracking-wider">
                                <i class="fa-solid fa-ruler-combined me-1"></i> Technical & Engineering Specs
                            </h6>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-bold small">Nominal Inner Dia (ID mm) *</label>
                            <input type="number" name="inner_diameter_mm" class="form-control" value="<?= $product['inner_diameter_mm'] ?>" required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-bold small">Outer Diameter (OD mm) *</label>
                            <input type="number" name="outer_diameter_mm" class="form-control" value="<?= $product['outer_diameter_mm'] ?>" required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-bold small">Standard Pipe Length (m) *</label>
                            <input type="number" step="0.5" name="standard_length_m" id="standardLength" class="form-control" value="<?= $product['standard_length_m'] ?>" required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-bold small">Stiffness Class *</label>
                            <select name="stiffness_class" class="form-select">
                                <option value="SN8" <?= $product['stiffness_class'] === 'SN8' ? 'selected' : '' ?>>SN8 (>= 8 kN/m² Heavy Traffic / Sewer)</option>
                                <option value="SN4" <?= $product['stiffness_class'] === 'SN4' ? 'selected' : '' ?>>SN4 (>= 4 kN/m² Non-Traffic / Medium)</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Raw Material Polymer</label>
                            <input type="text" name="raw_material" class="form-control" value="<?= htmlspecialchars($product['raw_material'] ?? '') ?>">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Standard Compliance</label>
                            <input type="text" name="standard_compliance" class="form-control" value="<?= htmlspecialchars($product['standard_compliance'] ?? '') ?>">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Jointing Method</label>
                            <input type="text" name="jointing_method" class="form-control" value="<?= htmlspecialchars($product['jointing_method'] ?? '') ?>">
                        </div>

                        <!-- Pricing & Stock -->
                        <div class="col-12 mt-4">
                            <h6 class="fw-bold border-bottom pb-2 mb-3 text-primary text-uppercase small tracking-wider">
                                <i class="fa-solid fa-indian-rupee-sign me-1"></i> Pricing & Inventory Control
                            </h6>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-bold small">Price Per Meter (₹) *</label>
                            <input type="number" step="0.01" name="price_per_meter" id="pricePerMeter" class="form-control" value="<?= $product['price_per_meter'] ?>" required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-bold small">Price Per Pipe (₹) *</label>
                            <input type="number" step="0.01" name="price_per_pipe" id="pricePerPipe" class="form-control" value="<?= $product['price_per_pipe'] ?>" required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-bold small">Stock Quantity (Pcs) *</label>
                            <input type="number" name="stock_quantity" class="form-control" value="<?= $product['stock_quantity'] ?>" required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-bold small">Min Order Qty (Pipes)</label>
                            <input type="number" name="min_order_qty" class="form-control" value="<?= $product['min_order_qty'] ?>" required>
                        </div>

                        <!-- Descriptions -->
                        <div class="col-12 mt-4">
                            <h6 class="fw-bold border-bottom pb-2 mb-3 text-primary text-uppercase small tracking-wider">
                                <i class="fa-solid fa-align-left me-1"></i> Product Descriptions
                            </h6>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold small">Short Description</label>
                            <textarea name="short_desc" class="form-control" rows="2"><?= htmlspecialchars($product['short_desc'] ?? '') ?></textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold small">Full Description</label>
                            <textarea name="description" class="form-control" rows="5"><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
                        </div>

                        <!-- Image Management -->
                        <div class="col-12 mt-4">
                            <h6 class="fw-bold border-bottom pb-2 mb-3 text-primary text-uppercase small tracking-wider">
                                <i class="fa-solid fa-image me-1"></i> Media & Status
                            </h6>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Current Image</label>
                            <div class="d-flex align-items-center gap-3 mb-2 p-2 border rounded bg-light">
                                <img src="<?= BASE_URL . ($product['image'] ?: 'assets/images/logo.svg') ?>" alt="Product" class="rounded border" style="width: 60px; height: 60px; object-fit: contain; background: #fff;">
                                <div class="small text-muted text-break">
                                    <strong>File:</strong> <?= htmlspecialchars($product['image'] ?? 'None') ?>
                                </div>
                            </div>
                            <label class="form-label fw-bold small">Replace Image (Optional)</label>
                            <input type="file" name="image" class="form-control" accept=".jpg,.jpeg,.png,.webp,.svg">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-bold small">Status</label>
                            <select name="status" class="form-select">
                                <option value="active" <?= $product['status'] === 'active' ? 'selected' : '' ?>>Active (Visible in Catalog)</option>
                                <option value="inactive" <?= $product['status'] === 'inactive' ? 'selected' : '' ?>>Inactive (Hidden)</option>
                            </select>
                        </div>

                        <div class="col-md-3 d-flex align-items-center pt-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="featured" id="featuredCheck" value="1" <?= $product['featured'] ? 'checked' : '' ?>>
                                <label class="form-check-label fw-bold small" for="featuredCheck">Feature on Homepage</label>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="col-12 mt-5 pt-3 border-top d-flex gap-3">
                            <button type="submit" class="btn btn-primary px-5 py-3 fw-bold rounded-3">
                                <i class="fa-solid fa-floppy-disk me-2"></i> Update Product Specifications
                            </button>
                            <a href="products.php" class="btn btn-light border px-4 py-3 fw-semibold">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('pricePerMeter').addEventListener('input', function() {
    const ppm = parseFloat(this.value) || 0;
    const len = parseFloat(document.getElementById('standardLength').value) || 6;
    document.getElementById('pricePerPipe').value = (ppm * len).toFixed(2);
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
