<?php
/**
 * Add New Product - Esfield Pipe
 */
$pageTitle = "Add New Product";
require_once __DIR__ . '/includes/header.php';

$db = get_db();
$categories = $db->query("SELECT * FROM `categories` ORDER BY `display_order` ASC, `name` ASC")->fetchAll();
?>

<div class="row justify-content-center">
    <div class="col-xl-10">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-black mb-1 text-dark">Add New DWC Pipe Product</h4>
                <p class="text-muted small mb-0">Specify dimensional standards, pricing slabs, stiffness ratings, and upload technical imagery.</p>
            </div>
            <a href="products.php" class="btn btn-outline-secondary btn-sm fw-bold">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Catalog
            </a>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4 p-md-5">
                <form action="<?= BASE_URL ?>api/admin-products.php" method="POST" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="add">

                    <div class="row g-4">
                        <!-- Basic Information -->
                        <div class="col-12">
                            <h6 class="fw-bold border-bottom pb-2 mb-3 text-primary text-uppercase small tracking-wider">
                                <i class="fa-solid fa-info-circle me-1"></i> Basic Identification
                            </h6>
                        </div>

                        <div class="col-md-8">
                            <label class="form-label fw-bold small">Product Title / Name *</label>
                            <input type="text" name="name" id="productName" class="form-control" placeholder="e.g. Esfield 300mm ID (350mm OD) Large Bore DWC Sewerage Pipe" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Category *</label>
                            <select name="category_id" class="form-select" required>
                                <?php foreach($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small">URL Slug / SKU</label>
                            <input type="text" name="slug" id="productSlug" class="form-control" placeholder="e.g. esfield-300mm-dwc-sewerage-pipe (auto-generated if left blank)">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Application Type</label>
                            <input type="text" name="application_type" class="form-control" value="Municipal Sewerage & Highway Culverts" placeholder="e.g. Underground Gravity Drainage, Telecom OFC Ducting">
                        </div>

                        <!-- Technical & Engineering Specifications -->
                        <div class="col-12 mt-4">
                            <h6 class="fw-bold border-bottom pb-2 mb-3 text-primary text-uppercase small tracking-wider">
                                <i class="fa-solid fa-ruler-combined me-1"></i> Technical & Engineering Specs
                            </h6>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-bold small">Nominal Inner Dia (ID mm) *</label>
                            <input type="number" name="inner_diameter_mm" class="form-control" value="300" required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-bold small">Outer Diameter (OD mm) *</label>
                            <input type="number" name="outer_diameter_mm" class="form-control" value="350" required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-bold small">Standard Pipe Length (m) *</label>
                            <input type="number" step="0.5" name="standard_length_m" id="standardLength" class="form-control" value="6.00" required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-bold small">Stiffness Class *</label>
                            <select name="stiffness_class" class="form-select">
                                <option value="SN8" selected>SN8 (>= 8 kN/m² Heavy Highway / Sewer)</option>
                                <option value="SN4">SN4 (>= 4 kN/m² Non-Traffic / Medium)</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Raw Material Polymer</label>
                            <input type="text" name="raw_material" class="form-control" value="PE-100 Virgin Grade HDPE">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Standard Compliance</label>
                            <input type="text" name="standard_compliance" class="form-control" value="IS 16098 (Part 2) / EN 13476">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Jointing Method</label>
                            <input type="text" name="jointing_method" class="form-control" value="Push-fit Socket with EPDM Rubber Ring">
                        </div>

                        <!-- Pricing & Stock Inventory -->
                        <div class="col-12 mt-4">
                            <h6 class="fw-bold border-bottom pb-2 mb-3 text-primary text-uppercase small tracking-wider">
                                <i class="fa-solid fa-indian-rupee-sign me-1"></i> Pricing & Inventory Control
                            </h6>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-bold small">Price Per Meter (₹) *</label>
                            <input type="number" step="0.01" name="price_per_meter" id="pricePerMeter" class="form-control" placeholder="e.g. 1150.00" required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-bold small">Price Per 6m Pipe (₹) *</label>
                            <input type="number" step="0.01" name="price_per_pipe" id="pricePerPipe" class="form-control" placeholder="e.g. 6900.00" required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-bold small">Stock Quantity (Pcs) *</label>
                            <input type="number" name="stock_quantity" class="form-control" value="500" required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-bold small">Min Order Qty (Pipes)</label>
                            <input type="number" name="min_order_qty" class="form-control" value="1" required>
                        </div>

                        <!-- Descriptions -->
                        <div class="col-12 mt-4">
                            <h6 class="fw-bold border-bottom pb-2 mb-3 text-primary text-uppercase small tracking-wider">
                                <i class="fa-solid fa-align-left me-1"></i> Product Descriptions
                            </h6>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold small">Short Description (Catalog Summary)</label>
                            <textarea name="short_desc" class="form-control" rows="2" placeholder="Brief 1-2 sentence engineering summary..."></textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold small">Full Detailed Description (Specifications & Advantages)</label>
                            <textarea name="description" class="form-control" rows="5" placeholder="Detailed product specifications, installation requirements, hydraulic characteristics..."></textarea>
                        </div>

                        <!-- Imagery & Visibility -->
                        <div class="col-12 mt-4">
                            <h6 class="fw-bold border-bottom pb-2 mb-3 text-primary text-uppercase small tracking-wider">
                                <i class="fa-solid fa-image me-1"></i> Media & Visibility
                            </h6>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Product Image (JPG, PNG, WEBP, SVG - Max 10MB)</label>
                            <input type="file" name="image" class="form-control" accept=".jpg,.jpeg,.png,.webp,.svg">
                            <small class="text-muted">Clean vector profile or 4:3 high-res photo recommended.</small>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-bold small">Status</label>
                            <select name="status" class="form-select">
                                <option value="active" selected>Active (Visible in Catalog)</option>
                                <option value="inactive">Inactive (Hidden)</option>
                            </select>
                        </div>

                        <div class="col-md-3 d-flex align-items-center pt-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="featured" id="featuredCheck" value="1">
                                <label class="form-check-label fw-bold small" for="featuredCheck">Feature on Homepage</label>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="col-12 mt-5 pt-3 border-top d-flex gap-3">
                            <button type="submit" class="btn btn-primary px-5 py-3 fw-bold rounded-3">
                                <i class="fa-solid fa-floppy-disk me-2"></i> Save Product to Catalog
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
// Auto-calculate price per pipe when price per meter changes
document.getElementById('pricePerMeter').addEventListener('input', function() {
    const ppm = parseFloat(this.value) || 0;
    const len = parseFloat(document.getElementById('standardLength').value) || 6;
    document.getElementById('pricePerPipe').value = (ppm * len).toFixed(2);
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
