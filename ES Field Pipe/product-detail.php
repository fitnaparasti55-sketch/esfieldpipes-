<?php
/**
 * Product Detail & Technical Datasheet Page
 * Esfield Pipe Platform
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

$slug = trim($_GET['slug'] ?? '');
$db = get_db();

$stmt = $db->prepare("
    SELECT p.*, c.name AS category_name, c.slug AS category_slug 
    FROM `products` p 
    JOIN `categories` c ON p.category_id = c.id 
    WHERE p.slug = ? AND p.status = 'active'
");
$stmt->execute([$slug]);
$product = $stmt->fetch();

if (!$product) {
    // If not found by slug, try by ID
    $id = (int)($_GET['id'] ?? 0);
    if ($id) {
        $stmt = $db->prepare("
            SELECT p.*, c.name AS category_name, c.slug AS category_slug 
            FROM `products` p 
            JOIN `categories` c ON p.category_id = c.id 
            WHERE p.id = ? AND p.status = 'active'
        ");
        $stmt->execute([$id]);
        $product = $stmt->fetch();
    }
}

if (!$product) {
    header('Location: ' . BASE_URL . 'products.php');
    exit;
}

// Increment view counter
$db->prepare("UPDATE `products` SET `views_count` = `views_count` + 1 WHERE `id` = ?")->execute([$product['id']]);

// Fetch Related Products in same category
$relStmt = $db->prepare("
    SELECT p.*, c.name AS category_name 
    FROM `products` p 
    JOIN `categories` c ON p.category_id = c.id 
    WHERE p.category_id = ? AND p.id != ? AND p.status = 'active' 
    LIMIT 3
");
$relStmt->execute([$product['category_id'], $product['id']]);
$related = $relStmt->fetchAll();

$pageTitle = $product['name'];
require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-4">
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>"><i class="fa-solid fa-house me-1"></i> Home</a></li>
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>products.php">DWC Pipes</a></li>
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>products.php?category=<?= urlencode($product['category_slug']) ?>"><?= htmlspecialchars($product['category_name']) ?></a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($product['name']) ?></li>
        </ol>
    </nav>

    <div class="row g-5">
        <!-- ====================================================
             LEFT COLUMN: PRODUCT IMAGE & CROSS-SECTION
             ==================================================== -->
        <div class="col-lg-6">
            <div class="card-custom p-4 text-center mb-4 position-relative" style="background-color: var(--bg-surface);">
                <span class="badge bg-primary position-absolute top-0 start-0 m-3 px-3 py-2 fs-7"><?= htmlspecialchars($product['category_name']) ?></span>
                <span class="badge bg-dark border border-secondary text-warning position-absolute top-0 end-0 m-3 px-3 py-2 fs-7"><i class="fa-solid fa-shield me-1"></i> <?= htmlspecialchars($product['stiffness_class']) ?> Rated</span>
                
                <div class="py-4">
                    <img src="<?= ASSETS_URL ?><?= htmlspecialchars($product['image'] ?: 'images/dwc-pipe-100mm.svg') ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="img-fluid rounded-3" style="max-height: 380px; width: 100%; object-fit: contain;">
                </div>

                <div class="row g-2 pt-3 border-top">
                    <div class="col-6">
                        <div class="p-2 bg-subtle rounded-3 small">
                            <span class="text-muted d-block">Nominal Bore (ID)</span>
                            <strong class="text-primary fs-6"><?= $product['inner_diameter_mm'] ?> mm</strong>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2 bg-subtle rounded-3 small">
                            <span class="text-muted d-block">Outer Diameter (OD)</span>
                            <strong class="text-info fs-6"><?= $product['outer_diameter_mm'] ?> mm</strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quality Highlights Card -->
            <div class="card-custom p-3 bg-subtle">
                <div class="d-flex align-items-center justify-content-between text-center">
                    <div>
                        <i class="fa-solid fa-award text-warning fs-4 mb-1"></i>
                        <div class="small fw-bold">IS 16098 (Part 2)</div>
                        <div class="text-muted" style="font-size: 0.72rem;">BIS Certified</div>
                    </div>
                    <div class="border-start ps-3">
                        <i class="fa-solid fa-truck-fast text-primary fs-4 mb-1"></i>
                        <div class="small fw-bold">Direct Dispatch</div>
                        <div class="text-muted" style="font-size: 0.72rem;">24-48 Hr Dispatch</div>
                    </div>
                    <div class="border-start ps-3">
                        <i class="fa-solid fa-shield-halved text-success fs-4 mb-1"></i>
                        <div class="small fw-bold">50+ Yrs Life</div>
                        <div class="text-muted" style="font-size: 0.72rem;">Virgin PE-100</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ====================================================
             RIGHT COLUMN: SPECS, PRICING & BUY ACTIONS
             ==================================================== -->
        <div class="col-lg-6">
            <h1 class="h2 fw-bold text-main mb-2"><?= htmlspecialchars($product['name']) ?></h1>
            <p class="text-muted small mb-3">
                <span>Model Code: <strong>ESP-DWC-<?= $product['inner_diameter_mm'] ?>-<?= $product['stiffness_class'] ?></strong></span> | 
                <span>Standard Length: <strong><?= number_format($product['standard_length_m'], 1) ?> Meters</strong></span>
            </p>

            <!-- Price Box -->
            <div class="p-4 rounded-4 mb-4 border" style="background-color: var(--bg-card); border-color: var(--border-color);">
                <div class="d-flex align-items-baseline gap-3 mb-1">
                    <span class="display-6 fw-bold text-primary"><?= format_price($product['price_per_pipe']) ?></span>
                    <span class="text-muted">/ 6-Meter Pipe</span>
                </div>
                <div class="small text-muted mb-3">
                    Equivalent to <strong><?= format_price($product['price_per_meter']) ?> per meter</strong> (+ 18% GST applicable with Input Tax Credit).
                </div>

                <!-- Stock & Delivery Status -->
                <div class="d-flex align-items-center gap-2 mb-4">
                    <?php if ($product['stock_quantity'] > 0): ?>
                        <span class="badge bg-success bg-opacity-15 text-success px-2.5 py-1.5"><i class="fa-solid fa-circle-check me-1"></i> Ready Stock Available (<?= $product['stock_quantity'] ?> units in factory yard)</span>
                    <?php else: ?>
                        <span class="badge bg-warning bg-opacity-15 text-warning px-2.5 py-1.5"><i class="fa-solid fa-clock me-1"></i> Made to Order (3-5 days turnaround)</span>
                    <?php endif; ?>
                </div>

                <!-- Quantity & Purchase Buttons -->
                <div class="row g-3 align-items-center">
                    <div class="col-sm-4">
                        <label class="form-label small fw-bold">Quantity (Pipes):</label>
                        <div class="qty-stepper">
                            <button type="button" onclick="let el=document.getElementById('productQty'); if(el.value>1) el.value--;" aria-label="Decrease quantity">-</button>
                            <input type="number" id="productQty" value="1" min="1" max="5000">
                            <button type="button" onclick="let el=document.getElementById('productQty'); el.value++;" aria-label="Increase quantity">+</button>
                        </div>
                    </div>
                    <div class="col-sm-8">
                        <label class="form-label small text-muted d-none d-sm-block">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-primary btn-add-to-cart flex-grow-1 py-2.5 fw-bold" data-product-id="<?= $product['id'] ?>">
                                <i class="fa-solid fa-cart-shopping me-2"></i> Add to Cart
                            </button>
                            <button class="btn btn-primary flex-grow-1 py-2.5 fw-bold" onclick="buyNow(<?= $product['id'] ?>)">
                                <i class="fa-solid fa-bolt me-1"></i> Buy Now
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Bulk RFQ Button -->
                <div class="pt-3 mt-3 border-top text-center">
                    <button class="btn btn-sm btn-link text-decoration-none text-muted" data-bs-toggle="modal" data-bs-target="#bulkQuoteModal">
                        <i class="fa-solid fa-file-contract text-warning me-1"></i> Ordering for Mega Infrastructure / Tender? <strong>Request Bulk Slab Quote</strong>
                    </button>
                </div>
            </div>

            <!-- Technical Specification Table -->
            <h5 class="fw-bold mb-3"><i class="fa-solid fa-list-check text-primary me-2"></i> Technical Specifications</h5>
            <div class="card-custom overflow-hidden mb-4">
                <table class="table table-sm table-striped mb-0 small">
                    <tbody>
                        <tr>
                            <td class="fw-semibold text-muted" style="width: 40%;">Nominal Internal Diameter (ID)</td>
                            <td class="fw-bold text-main"><?= $product['inner_diameter_mm'] ?> mm</td>
                        </tr>
                        <tr>
                            <td class="fw-semibold text-muted">Nominal Outer Diameter (OD)</td>
                            <td class="fw-bold text-main"><?= $product['outer_diameter_mm'] ?> mm</td>
                        </tr>
                        <tr>
                            <td class="fw-semibold text-muted">Ring Stiffness Class</td>
                            <td><span class="badge bg-dark border text-warning"><?= htmlspecialchars($product['stiffness_class']) ?> (&ge; 8 kN/m²)</span></td>
                        </tr>
                        <tr>
                            <td class="fw-semibold text-muted">Raw Material Resin</td>
                            <td class="text-main"><?= htmlspecialchars($product['raw_material']) ?></td>
                        </tr>
                        <tr>
                            <td class="fw-semibold text-muted">Standard Compliance</td>
                            <td class="text-main"><?= htmlspecialchars($product['standard_compliance']) ?></td>
                        </tr>
                        <tr>
                            <td class="fw-semibold text-muted">Jointing System</td>
                            <td class="text-main"><?= htmlspecialchars($product['jointing_method']) ?></td>
                        </tr>
                        <tr>
                            <td class="fw-semibold text-muted">Application Area</td>
                            <td class="text-main"><?= htmlspecialchars($product['application_type']) ?></td>
                        </tr>
                        <tr>
                            <td class="fw-semibold text-muted">Standard Length</td>
                            <td class="text-main"><?= number_format($product['standard_length_m'], 2) ?> Meters per pipe</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Description Text -->
            <h5 class="fw-bold mb-2">Description & Engineering Properties</h5>
            <div class="text-muted small leading-relaxed mb-4">
                <?= nl2br(htmlspecialchars($product['description'])) ?>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    <?php if (!empty($related)): ?>
        <div class="mt-5 pt-4 border-top">
            <h4 class="fw-bold mb-4">Related DWC Piping Options</h4>
            <div class="row g-4 product-grid-mobile-list">
                <?php foreach ($related as $rel): ?>
                    <div class="col-12 col-md-4">
                        <div class="card-custom product-card h-100">
                            <div class="product-img-wrapper">
                                <a href="<?= BASE_URL ?>product-detail.php?slug=<?= urlencode($rel['slug']) ?>" class="d-block w-100 text-center">
                                    <img src="<?= ASSETS_URL ?><?= htmlspecialchars($rel['image'] ?: 'images/dwc-pipe-100mm.svg') ?>" alt="<?= htmlspecialchars($rel['name']) ?>">
                                </a>
                            </div>
                            <div class="product-card-body">
                                <h6 class="fw-bold mb-2">
                                    <a href="<?= BASE_URL ?>product-detail.php?slug=<?= urlencode($rel['slug']) ?>" class="text-main hover-primary">
                                        <?= htmlspecialchars($rel['name']) ?>
                                    </a>
                                </h6>
                                <div class="d-flex justify-content-between align-items-center mt-auto pt-2 border-top">
                                    <span class="fw-bold text-primary"><?= format_price($rel['price_per_pipe']) ?></span>
                                    <a href="<?= BASE_URL ?>product-detail.php?slug=<?= urlencode($rel['slug']) ?>" class="btn btn-sm btn-primary">Details</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- ========================================================
     MODAL: BULK QUOTATION / TENDER INQUIRY
     ======================================================== -->
<div class="modal fade" id="bulkQuoteModal" tabindex="-1" aria-labelledby="bulkQuoteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4" style="background-color: var(--bg-card);">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold" id="bulkQuoteModalLabel"><i class="fa-solid fa-file-contract text-primary me-2"></i> Request Project Quotation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= BASE_URL ?>contact.php" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="inquiry_type" value="quote">
                <input type="hidden" name="pipe_requirement" value="<?= htmlspecialchars($product['name']) ?> (ID: <?= $product['inner_diameter_mm'] ?>mm)">

                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Selected DWC Pipe</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($product['name']) ?>" readonly>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold">Your Name *</label>
                            <input type="text" name="name" class="form-control" value="<?= $user ? htmlspecialchars($user['name']) : '' ?>" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold">Phone Number *</label>
                            <input type="tel" name="phone" class="form-control" value="<?= $user ? htmlspecialchars($user['phone'] ?? '') : '' ?>" required>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold">Email Address *</label>
                            <input type="email" name="email" class="form-control" value="<?= $user ? htmlspecialchars($user['email']) : '' ?>" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold">Company / Contractor Name</label>
                            <input type="text" name="company" class="form-control" value="<?= $user ? htmlspecialchars($user['company_name'] ?? '') : '' ?>">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Estimated Quantity (Meters / Pipes) & Project Details *</label>
                        <textarea name="message" class="form-control" rows="3" placeholder="e.g. Need 2,000 meters delivered to Metro Line 4 construction site..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="submit_inquiry" class="btn btn-primary fw-bold px-4">Submit RFQ Request</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
async function buyNow(productId) {
    const qty = document.getElementById('productQty').value || 1;
    const formData = new FormData();
    formData.append('action', 'add');
    formData.append('product_id', productId);
    formData.append('quantity', qty);

    try {
        const res = await fetch('<?= BASE_URL ?>api/cart-action.php', { method: 'POST', body: formData });
        const data = await res.json();
        if (data.success) {
            window.location.href = '<?= BASE_URL ?>checkout.php';
        }
    } catch (e) {
        window.location.href = '<?= BASE_URL ?>cart.php';
    }
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
