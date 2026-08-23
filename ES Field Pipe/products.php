<?php
/**
 * Product Catalog & Listing Page
 * Responsive: Desktop Grid (3-4 per row) + Mobile Vertical Card List
 * Esfield Pipe Platform
 */

$pageTitle = "DWC Corrugated HDPE Pipe Catalog";
require_once __DIR__ . '/includes/header.php';

$db = get_db();

// Filter parameters
$categorySlug = trim($_GET['category'] ?? '');
$stiffness = trim($_GET['stiffness'] ?? '');
$minDia = (int)($_GET['min_dia'] ?? 0);
$maxDia = (int)($_GET['max_dia'] ?? 0);
$search = trim($_GET['search'] ?? '');
$sort = trim($_GET['sort'] ?? 'diameter_asc');

// Base query
$sql = "
    SELECT p.*, c.name AS category_name, c.slug AS category_slug
    FROM `products` p
    JOIN `categories` c ON p.category_id = c.id
    WHERE p.status = 'active'
";
$params = [];

// Apply filters
if (!empty($categorySlug)) {
    $sql .= " AND c.slug = ?";
    $params[] = $categorySlug;
}

if (!empty($stiffness) && in_array($stiffness, ['SN4', 'SN8'])) {
    $sql .= " AND p.stiffness_class = ?";
    $params[] = $stiffness;
}

if ($minDia > 0) {
    $sql .= " AND p.inner_diameter_mm >= ?";
    $params[] = $minDia;
}

if ($maxDia > 0) {
    $sql .= " AND p.inner_diameter_mm <= ?";
    $params[] = $maxDia;
}

if (!empty($search)) {
    $sql .= " AND (p.name LIKE ? OR p.description LIKE ? OR p.inner_diameter_mm LIKE ? OR p.application_type LIKE ?)";
    $term = "%{$search}%";
    $params[] = $term;
    $params[] = $term;
    $params[] = $term;
    $params[] = $term;
}

// Sorting
switch ($sort) {
    case 'price_asc':
        $sql .= " ORDER BY p.price_per_pipe ASC";
        break;
    case 'price_desc':
        $sql .= " ORDER BY p.price_per_pipe DESC";
        break;
    case 'diameter_desc':
        $sql .= " ORDER BY p.inner_diameter_mm DESC";
        break;
    case 'popularity':
        $sql .= " ORDER BY p.views_count DESC, p.featured DESC";
        break;
    case 'diameter_asc':
    default:
        $sql .= " ORDER BY p.inner_diameter_mm ASC";
        break;
}

$stmt = $db->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

// Fetch all categories for filter options
$catList = $db->query("SELECT * FROM `categories` WHERE `status` = 'active' ORDER BY `display_order` ASC")->fetchAll();
?>

<div class="container py-4">
    <!-- Breadcrumb & Header -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>"><i class="fa-solid fa-house me-1"></i> Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">DWC Pipe Catalog</li>
        </ol>
    </nav>

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 pb-3 border-bottom">
        <div>
            <h1 class="h3 fw-bold mb-1">DWC Corrugated HDPE Pipe Catalog</h1>
            <p class="text-muted small mb-0">Showing <strong><?= count($products) ?></strong> manufactured pipe models conforming to IS 16098-2</p>
        </div>

        <!-- Mobile Filter Trigger & Sort Dropdown -->
        <div class="d-flex align-items-center gap-2">
            <button class="btn btn-outline-primary d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#filterOffcanvas" aria-controls="filterOffcanvas">
                <i class="fa-solid fa-filter me-1"></i> Filters
            </button>

            <form method="GET" id="sortForm" class="d-flex align-items-center gap-2">
                <?php if ($categorySlug): ?><input type="hidden" name="category" value="<?= htmlspecialchars($categorySlug) ?>"><?php endif; ?>
                <?php if ($stiffness): ?><input type="hidden" name="stiffness" value="<?= htmlspecialchars($stiffness) ?>"><?php endif; ?>
                <?php if ($search): ?><input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>"><?php endif; ?>
                
                <label class="small text-muted text-nowrap d-none d-sm-inline">Sort by:</label>
                <select name="sort" class="form-select form-select-sm" onchange="document.getElementById('sortForm').submit()" style="min-width: 170px;">
                    <option value="diameter_asc" <?= $sort === 'diameter_asc' ? 'selected' : '' ?>>Diameter (Small to Large)</option>
                    <option value="diameter_desc" <?= $sort === 'diameter_desc' ? 'selected' : '' ?>>Diameter (Large to Small)</option>
                    <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : '' ?>>Price (Low to High)</option>
                    <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Price (High to Low)</option>
                    <option value="popularity" <?= $sort === 'popularity' ? 'selected' : '' ?>>Most Popular</option>
                </select>
            </form>
        </div>
    </div>

    <!-- Active Filter Tags -->
    <?php if ($categorySlug || $stiffness || $search || $minDia || $maxDia): ?>
        <div class="d-flex flex-wrap align-items-center gap-2 mb-4 p-2 bg-subtle rounded-3">
            <span class="small fw-semibold text-muted me-1">Active Filters:</span>
            <?php if ($categorySlug): ?>
                <span class="badge bg-primary px-2.5 py-1.5">Category: <?= htmlspecialchars($categorySlug) ?> <a href="<?= BASE_URL ?>products.php?<?= http_build_query(array_merge($_GET, ['category' => ''])) ?>" class="text-white ms-1 text-decoration-none">&times;</a></span>
            <?php endif; ?>
            <?php if ($stiffness): ?>
                <span class="badge bg-warning text-dark px-2.5 py-1.5">Stiffness: <?= htmlspecialchars($stiffness) ?> <a href="<?= BASE_URL ?>products.php?<?= http_build_query(array_merge($_GET, ['stiffness' => ''])) ?>" class="text-dark ms-1 text-decoration-none">&times;</a></span>
            <?php endif; ?>
            <?php if ($search): ?>
                <span class="badge bg-secondary px-2.5 py-1.5">Search: "<?= htmlspecialchars($search) ?>" <a href="<?= BASE_URL ?>products.php?<?= http_build_query(array_merge($_GET, ['search' => ''])) ?>" class="text-white ms-1 text-decoration-none">&times;</a></span>
            <?php endif; ?>
            <a href="<?= BASE_URL ?>products.php" class="small text-danger fw-semibold ms-auto text-decoration-none">Reset All</a>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- ====================================================
             DESKTOP FILTER SIDEBAR
             ==================================================== -->
        <div class="col-lg-3 d-none d-lg-block">
            <div class="card-custom p-4 sticky-top" style="top: 80px; z-index: 10;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0"><i class="fa-solid fa-sliders text-primary me-2"></i> Filters</h5>
                    <a href="<?= BASE_URL ?>products.php" class="small text-muted text-decoration-none">Clear</a>
                </div>

                <form method="GET" action="<?= BASE_URL ?>products.php">
                    <?php if ($sort): ?><input type="hidden" name="sort" value="<?= htmlspecialchars($sort) ?>"><?php endif; ?>
                    
                    <!-- Search Filter -->
                    <div class="mb-4">
                        <label class="form-label small fw-bold">Search Keywords</label>
                        <div class="input-group input-group-sm">
                            <input type="text" name="search" class="form-control" placeholder="e.g. 200mm, culvert..." value="<?= htmlspecialchars($search) ?>">
                            <button class="btn btn-outline-primary" type="submit"><i class="fa-solid fa-search"></i></button>
                        </div>
                    </div>

                    <!-- Category Filter -->
                    <div class="mb-4">
                        <label class="form-label small fw-bold">Application Category</label>
                        <div class="d-flex flex-column gap-1.5">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="category" id="cat_all" value="" <?= empty($categorySlug) ? 'checked' : '' ?> onchange="this.form.submit()">
                                <label class="form-check-label small" for="cat_all">All Categories</label>
                            </div>
                            <?php foreach ($catList as $cat): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="category" id="cat_<?= $cat['id'] ?>" value="<?= $cat['slug'] ?>" <?= $categorySlug === $cat['slug'] ? 'checked' : '' ?> onchange="this.form.submit()">
                                    <label class="form-check-label small text-truncate" for="cat_<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Stiffness Rating Filter -->
                    <div class="mb-4">
                        <label class="form-label small fw-bold">Ring Stiffness Rating</label>
                        <div class="d-flex gap-2">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="stiffness" id="stiff_all" value="" <?= empty($stiffness) ? 'checked' : '' ?> onchange="this.form.submit()">
                                <label class="form-check-label small" for="stiff_all">All</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="stiffness" id="stiff_sn8" value="SN8" <?= $stiffness === 'SN8' ? 'checked' : '' ?> onchange="this.form.submit()">
                                <label class="form-check-label small" for="stiff_sn8"><strong>SN8</strong> (8 kN/m²)</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="stiffness" id="stiff_sn4" value="SN4" <?= $stiffness === 'SN4' ? 'checked' : '' ?> onchange="this.form.submit()">
                                <label class="form-check-label small" for="stiff_sn4"><strong>SN4</strong> (4 kN/m²)</label>
                            </div>
                        </div>
                    </div>

                    <!-- Diameter Range -->
                    <div class="mb-4">
                        <label class="form-label small fw-bold">Nominal Diameter (mm)</label>
                        <div class="row g-2">
                            <div class="col-6">
                                <input type="number" name="min_dia" class="form-control form-control-sm" placeholder="Min mm" value="<?= $minDia ?: '' ?>">
                            </div>
                            <div class="col-6">
                                <input type="number" name="max_dia" class="form-control form-control-sm" placeholder="Max mm" value="<?= $maxDia ?: '' ?>">
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold">
                        <i class="fa-solid fa-check me-1"></i> Apply Filters
                    </button>
                </form>
            </div>
        </div>

        <!-- ====================================================
             PRODUCT LISTING GRID / MOBILE CARDS
             ==================================================== -->
        <div class="col-lg-9">
            <?php if (empty($products)): ?>
                <div class="card-custom p-5 text-center my-4">
                    <div class="rounded-circle bg-subtle d-inline-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px; font-size: 2.5rem; color: var(--text-muted);">
                        <i class="fa-solid fa-box-open"></i>
                    </div>
                    <h4 class="fw-bold mb-2">No Matching DWC Pipes Found</h4>
                    <p class="text-muted mb-4">Try adjusting your diameter filters, category selection, or search keywords.</p>
                    <a href="<?= BASE_URL ?>products.php" class="btn btn-primary px-4 py-2 fw-semibold">
                        <i class="fa-solid fa-rotate-left me-1"></i> Clear Filters
                    </a>
                </div>
            <?php else: ?>
                <div class="row g-4 product-grid-mobile-list">
                    <?php foreach ($products as $prod): ?>
                        <div class="col-12 col-md-6 col-xl-4">
                            <div class="card-custom product-card h-100">
                                <div class="product-img-wrapper position-relative">
                                    <span class="badge bg-primary product-badge-overlay"><?= htmlspecialchars($prod['category_name']) ?></span>
                                    <span class="badge bg-dark border border-secondary text-warning product-stiffness-overlay"><?= htmlspecialchars($prod['stiffness_class']) ?></span>
                                    <a href="<?= BASE_URL ?>product-detail.php?slug=<?= urlencode($prod['slug']) ?>" class="d-block w-100 text-center">
                                        <img src="<?= ASSETS_URL ?><?= htmlspecialchars($prod['image'] ?: 'images/dwc-pipe-100mm.svg') ?>" alt="<?= htmlspecialchars($prod['name']) ?>">
                                    </a>
                                </div>

                                <div class="product-card-body">
                                    <div class="d-flex flex-wrap gap-1 mb-2">
                                        <span class="product-spec-pill"><i class="fa-solid fa-arrows-left-right-to-line text-primary"></i> ID: <?= $prod['inner_diameter_mm'] ?>mm</span>
                                        <span class="product-spec-pill"><i class="fa-solid fa-arrows-left-right text-info"></i> OD: <?= $prod['outer_diameter_mm'] ?>mm</span>
                                        <span class="product-spec-pill"><i class="fa-solid fa-ruler text-secondary"></i> <?= number_format($prod['standard_length_m'], 1) ?>m Pipe</span>
                                    </div>

                                    <h5 class="fw-bold mb-2 fs-6">
                                        <a href="<?= BASE_URL ?>product-detail.php?slug=<?= urlencode($prod['slug']) ?>" class="text-main hover-primary">
                                            <?= htmlspecialchars($prod['name']) ?>
                                        </a>
                                    </h5>

                                    <p class="text-muted small mb-3 flex-grow-1" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                        <?= htmlspecialchars($prod['short_desc'] ?: $prod['application_type']) ?>
                                    </p>

                                    <div class="d-flex justify-content-between align-items-center pt-2 border-top mt-auto">
                                        <div>
                                            <div class="fs-5 fw-bold text-primary"><?= format_price($prod['price_per_pipe']) ?></div>
                                            <div class="small text-muted" style="font-size: 0.72rem;">(<?= format_price($prod['price_per_meter']) ?> / m)</div>
                                        </div>
                                        <div class="d-flex gap-1.5">
                                            <button class="btn btn-sm btn-outline-primary btn-add-to-cart px-2.5" data-product-id="<?= $prod['id'] ?>" title="Add to Cart">
                                                <i class="fa-solid fa-cart-plus me-1"></i> Add
                                            </button>
                                            <a href="<?= BASE_URL ?>product-detail.php?slug=<?= urlencode($prod['slug']) ?>" class="btn btn-sm btn-primary px-3">
                                                View
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- ========================================================
     MOBILE FILTER OFFCANVAS BOTTOM SHEET
     ======================================================== -->
<div class="offcanvas offcanvas-bottom rounded-top-4" tabindex="-1" id="filterOffcanvas" style="height: 80vh;" aria-labelledby="filterOffcanvasLabel">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title fw-bold" id="filterOffcanvasLabel"><i class="fa-solid fa-filter text-primary me-2"></i> Filter Products</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-4">
        <form method="GET" action="<?= BASE_URL ?>products.php">
            <div class="mb-3">
                <label class="form-label small fw-bold">Keywords</label>
                <input type="text" name="search" class="form-control" placeholder="Search size, name..." value="<?= htmlspecialchars($search) ?>">
            </div>

            <div class="mb-3">
                <label class="form-label small fw-bold">Application Category</label>
                <select name="category" class="form-select">
                    <option value="">All Categories</option>
                    <?php foreach ($catList as $cat): ?>
                        <option value="<?= $cat['slug'] ?>" <?= $categorySlug === $cat['slug'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label small fw-bold">Ring Stiffness Rating</label>
                <select name="stiffness" class="form-select">
                    <option value="">All Stiffness Ratings</option>
                    <option value="SN8" <?= $stiffness === 'SN8' ? 'selected' : '' ?>>SN8 (Heavy Axle 8 kN/m²)</option>
                    <option value="SN4" <?= $stiffness === 'SN4' ? 'selected' : '' ?>>SN4 (Standard 4 kN/m²)</option>
                </select>
            </div>

            <div class="d-flex gap-2 pt-3">
                <a href="<?= BASE_URL ?>products.php" class="btn btn-outline-secondary w-50">Reset</a>
                <button type="submit" class="btn btn-primary w-50 fw-bold">Apply Filters</button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
