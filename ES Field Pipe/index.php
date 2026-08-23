<?php
/**
 * Landing Page - Esfield Pipe
 * High Conversion Manufacturing & eCommerce Experience - Dynamic Content
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';

$settings = get_settings();
$pageTitle = $settings['home_seo_title'] ?? "India's Premier DWC HDPE Corrugated Pipe Manufacturer";
$pageDescription = $settings['home_seo_description'] ?? null;

require_once __DIR__ . '/includes/header.php';

$db = get_db();

// Fetch Featured DWC Pipe Products (Limit from settings)
$productsCount = (int)($settings['home_products_count'] ?? 6);
$featuredStmt = $db->prepare("
    SELECT p.*, c.name AS category_name 
    FROM `products` p 
    JOIN `categories` c ON p.category_id = c.id 
    WHERE p.status = 'active' AND p.featured = 1 
    ORDER BY p.inner_diameter_mm ASC 
    LIMIT ?
");
$featuredStmt->bindValue(1, $productsCount, PDO::PARAM_INT);
$featuredStmt->execute();
$featuredProducts = $featuredStmt->fetchAll();

// If not enough featured products, fetch active products
if (count($featuredProducts) < 3) {
    $altStmt = $db->prepare("
        SELECT p.*, c.name AS category_name 
        FROM `products` p 
        JOIN `categories` c ON p.category_id = c.id 
        WHERE p.status = 'active' 
        ORDER BY p.inner_diameter_mm ASC 
        LIMIT ?
    ");
    $altStmt->bindValue(1, $productsCount, PDO::PARAM_INT);
    $altStmt->execute();
    $featuredProducts = $altStmt->fetchAll();
}

// Fetch Categories for Quick Navigation
$catStmt = $db->query("SELECT * FROM `categories` WHERE `status` = 'active' ORDER BY `display_order` ASC");
$categories = $catStmt->fetchAll();

// Fetch FAQ Items
$faqStmt = $db->query("SELECT * FROM `faqs` WHERE `status` = 'active' ORDER BY `display_order` ASC LIMIT 5");
$faqs = $faqStmt->fetchAll();

// Homepage settings
$heroBadge = $settings['home_hero_badge'] ?? 'BIS IS:16098 (Part-2) & EN 13476 Certified';
$heroHeading = $settings['home_hero_heading'] ?? 'Engineered Strength. High-Flow DWC HDPE Piping Systems.';
$heroSubheading = $settings['home_hero_subheading'] ?? 'Manufactured with 100% virgin PE-100 polymers for underground gravity drainage, highway culverts, municipal sewerage, and telecom cable ducting with 50+ year design life.';
$heroBtn1Text = $settings['home_hero_btn1_text'] ?? 'Explore Pipe Catalog';
$heroBtn1Url = $settings['home_hero_btn1_url'] ?? 'products.php';
$heroBtn2Text = $settings['home_hero_btn2_text'] ?? 'Sizing Calculator';
$heroBtn2Url = $settings['home_hero_btn2_url'] ?? 'pipe-calculator.php';
$heroImage = $settings['home_hero_image'] ?? 'assets/images/dwc-cross-section.svg';

$stat1Num = $settings['home_stat1_number'] ?? '50-1200';
$stat1Label = $settings['home_stat1_label'] ?? 'mm Diameters';
$stat2Num = $settings['home_stat2_number'] ?? 'SN8';
$stat2Label = $settings['home_stat2_label'] ?? 'Ring Stiffness';
$stat3Num = $settings['home_stat3_number'] ?? '50+ Yrs';
$stat3Label = $settings['home_stat3_label'] ?? 'Service Lifespan';

$companyHeading = $settings['home_company_heading'] ?? 'Pioneering Heavy Infrastructure & Drainage Technology';
$companySubheading = $settings['home_company_subheading'] ?? 'Precision Engineered Double Wall Corrugated HDPE Pipes';
$companyDesc = $settings['home_company_desc'] ?? 'Esfield Pipe provides advanced structural wall piping designed to withstand severe dynamic axle loading, seismic movement, and chemical aggression in municipal and industrial projects.';
$companyImage = $settings['home_company_image'] ?? 'assets/images/dwc-cross-section.svg';

$productsHeading = $settings['home_products_heading'] ?? 'Featured Infrastructure Pipe Systems';
$productsDesc = $settings['home_products_desc'] ?? 'Explore our BIS IS:16098 Part 2 certified structured wall DWC pipes available in standard 6.0m lengths with integrated socket couplers.';

$ctaHeading = $settings['home_cta_heading'] ?? 'Ready to Upgrade Your Pipeline Infrastructure?';
$ctaDesc = $settings['home_cta_desc'] ?? 'Contact our engineering sales department for project-specific sizing, factory inspections, or bulk institutional quotation tenders.';
$ctaBtnText = $settings['home_cta_btn_text'] ?? 'Request Bulk RFQ Quote';
$ctaBtnUrl = $settings['home_cta_btn_url'] ?? 'contact.php';
?>

<!-- ========================================================
     HERO SECTION
     ======================================================== -->
<section class="hero-section">
    <div class="container position-relative z-1">
        <div class="row align-items-center g-5">
            <div class="col-lg-7">
                <div class="hero-badge">
                    <i class="fa-solid fa-certificate"></i> <?= htmlspecialchars($heroBadge) ?>
                </div>
                <h1 class="display-4 fw-black text-white mb-3" style="font-weight: 800; line-height: 1.15;">
                    <?= nl2br(htmlspecialchars($heroHeading)) ?>
                </h1>
                <p class="lead text-light mb-4 opacity-90" style="max-width: 600px; font-size: 1.15rem;">
                    <?= htmlspecialchars($heroSubheading) ?>
                </p>

                <div class="d-flex flex-wrap gap-3 mb-4">
                    <a href="<?= BASE_URL . $heroBtn1Url ?>" class="btn btn-primary btn-lg px-4 py-3 shadow-lg">
                        <i class="fa-solid fa-cubes-stacked me-2"></i> <?= htmlspecialchars($heroBtn1Text) ?>
                    </a>
                    <a href="<?= BASE_URL . $heroBtn2Url ?>" class="btn btn-outline-light btn-lg px-4 py-3">
                        <i class="fa-solid fa-calculator me-2 text-warning"></i> <?= htmlspecialchars($heroBtn2Text) ?>
                    </a>
                </div>

                <!-- Quick Stats -->
                <div class="row g-3 pt-2">
                    <div class="col-4">
                        <div class="hero-stats-card text-center">
                            <div class="h3 fw-bold text-warning mb-0"><?= htmlspecialchars($stat1Num) ?></div>
                            <div class="small text-light"><?= htmlspecialchars($stat1Label) ?></div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="hero-stats-card text-center">
                            <div class="h3 fw-bold text-info mb-0"><?= htmlspecialchars($stat2Num) ?></div>
                            <div class="small text-light"><?= htmlspecialchars($stat2Label) ?></div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="hero-stats-card text-center">
                            <div class="h3 fw-bold text-success mb-0"><?= htmlspecialchars($stat3Num) ?></div>
                            <div class="small text-light"><?= htmlspecialchars($stat3Label) ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hero Visual -->
            <div class="col-lg-5 text-center">
                <div class="position-relative">
                    <img src="<?= BASE_URL . $heroImage ?>" alt="DWC Corrugated HDPE Pipe Profile" class="img-fluid rounded-4 shadow-lg border border-secondary border-opacity-25" style="background: rgba(15,23,42,0.85); max-height: 380px; object-fit: contain;">
                    <div class="position-absolute bottom-0 start-50 translate-middle-x mb-3 bg-dark bg-opacity-90 px-3 py-1.5 rounded-pill border border-secondary border-opacity-50 text-white small">
                        <i class="fa-solid fa-circle-check text-success me-1"></i> Dual Wall: Corrugated Exterior + Smooth Bore
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ========================================================
     QUICK DIAMETER SELECTOR CHIPS
     ======================================================== -->
<section class="py-3 border-bottom" style="background-color: var(--bg-surface);">
    <div class="container">
        <div class="d-flex align-items-center gap-2 overflow-auto py-1" style="scrollbar-width: thin;">
            <span class="fw-bold small text-muted text-uppercase me-2 text-nowrap"><i class="fa-solid fa-ruler-combined text-primary me-1"></i> Popular Sizes:</span>
            <a href="<?= BASE_URL ?>products.php?search=100mm" class="filter-chip">100 mm ID</a>
            <a href="<?= BASE_URL ?>products.php?search=150mm" class="filter-chip">150 mm ID</a>
            <a href="<?= BASE_URL ?>products.php?search=200mm" class="filter-chip">200 mm ID</a>
            <a href="<?= BASE_URL ?>products.php?search=250mm" class="filter-chip">250 mm ID</a>
            <a href="<?= BASE_URL ?>products.php?search=300mm" class="filter-chip">300 mm ID</a>
            <a href="<?= BASE_URL ?>products.php?search=400mm" class="filter-chip">400 mm ID</a>
            <a href="<?= BASE_URL ?>products.php?search=500mm" class="filter-chip">500 mm ID</a>
            <a href="<?= BASE_URL ?>products.php?search=600mm" class="filter-chip">600 mm ID</a>
            <a href="<?= BASE_URL ?>products.php?category=telecom-cable-ducting" class="filter-chip bg-warning text-dark border-warning">50/75mm Ducts</a>
        </div>
    </div>
</section>

<!-- ========================================================
     FEATURED PRODUCTS GRID
     ======================================================== -->
<section class="py-5" style="background-color: var(--bg-body);">
    <div class="container">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end mb-4">
            <div>
                <span class="badge bg-primary bg-opacity-10 text-primary fw-bold px-3 py-2 rounded-pill mb-2">INFRASTRUCTURE CATALOG</span>
                <h2 class="fw-black mb-1"><?= htmlspecialchars($productsHeading) ?></h2>
                <p class="text-muted mb-0"><?= htmlspecialchars($productsDesc) ?></p>
            </div>
            <a href="<?= BASE_URL ?>products.php" class="btn btn-outline-primary fw-bold mt-3 mt-md-0">
                View All Pipes <i class="fa-solid fa-arrow-right ms-1"></i>
            </a>
        </div>

        <div class="row g-4">
            <?php foreach($featuredProducts as $prod): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card card-custom product-card h-100">
                    <div class="product-img-wrapper">
                        <span class="badge bg-primary product-badge-overlay"><?= $prod['inner_diameter_mm'] ?> mm ID</span>
                        <span class="badge <?= $prod['stiffness_class'] === 'SN8' ? 'bg-dark text-warning' : 'bg-secondary' ?> product-stiffness-overlay"><?= $prod['stiffness_class'] ?></span>
                        <a href="<?= BASE_URL ?>product-detail.php?id=<?= $prod['id'] ?>">
                            <img src="<?= BASE_URL . ($prod['image'] ?: 'assets/images/logo.svg') ?>" alt="<?= htmlspecialchars($prod['name']) ?>">
                        </a>
                    </div>
                    <div class="product-card-body">
                        <div class="small text-muted mb-1"><?= htmlspecialchars($prod['category_name']) ?></div>
                        <h5 class="fw-bold mb-2">
                            <a href="<?= BASE_URL ?>product-detail.php?id=<?= $prod['id'] ?>" class="text-dark text-decoration-none hover-primary">
                                <?= htmlspecialchars($prod['name']) ?>
                            </a>
                        </h5>
                        <p class="small text-muted mb-3 flex-grow-1 text-truncate-2">
                            <?= htmlspecialchars($prod['short_desc'] ?? '') ?>
                        </p>

                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <span class="product-spec-pill"><i class="fa-solid fa-arrows-left-right"></i> OD: <?= $prod['outer_diameter_mm'] ?>mm</span>
                            <span class="product-spec-pill"><i class="fa-solid fa-ruler"></i> <?= $prod['standard_length_m'] ?>m Length</span>
                        </div>

                        <div class="d-flex justify-content-between align-items-center pt-3 border-top mt-auto">
                            <div>
                                <span class="fw-black text-primary fs-5">₹<?= number_format($prod['price_per_pipe'], 2) ?></span>
                                <span class="text-muted small d-block" style="font-size: 0.72rem;">(₹<?= number_format($prod['price_per_meter'], 2) ?>/m ex-works)</span>
                            </div>
                            <button type="button" class="btn btn-primary btn-sm btn-add-to-cart px-3" data-product-id="<?= $prod['id'] ?>">
                                <i class="fa-solid fa-cart-plus me-1"></i> Add
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ========================================================
     COMPANY / MANUFACTURING SECTION
     ======================================================== -->
<section class="py-5" style="background-color: var(--bg-surface);">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <span class="badge bg-primary bg-opacity-10 text-primary fw-bold px-3 py-2 rounded-pill mb-2">MANUFACTURING EXCELLENCE</span>
                <h2 class="fw-black mb-3"><?= htmlspecialchars($companyHeading) ?></h2>
                <h5 class="text-muted fw-semibold mb-4"><?= htmlspecialchars($companySubheading) ?></h5>
                <p class="text-muted leading-relaxed mb-4">
                    <?= nl2br(htmlspecialchars($companyDesc)) ?>
                </p>

                <div class="row g-3 mb-4">
                    <div class="col-sm-6">
                        <div class="d-flex align-items-start gap-3">
                            <div class="rounded-3 bg-primary bg-opacity-10 text-primary p-2 fs-5">
                                <i class="fa-solid fa-award"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1">BIS IS:16098-2</h6>
                                <small class="text-muted">Certified third-party lab batch inspection</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex align-items-start gap-3">
                            <div class="rounded-3 bg-success bg-opacity-10 text-success p-2 fs-5">
                                <i class="fa-solid fa-truck-ramp-box"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1">Direct Factory Dispatch</h6>
                                <small class="text-muted">Nationwide flatbed delivery logistics</small>
                            </div>
                        </div>
                    </div>
                </div>

                <a href="<?= BASE_URL ?>contact.php" class="btn btn-primary px-4 py-2.5 fw-bold">
                    <i class="fa-solid fa-file-signature me-2"></i> Request Project Tender Quotation
                </a>
            </div>

            <div class="col-lg-6 text-center">
                <img src="<?= BASE_URL . $companyImage ?>" alt="Esfield Manufacturing" class="img-fluid rounded-4 shadow-lg border">
            </div>
        </div>
    </div>
</section>

<!-- ========================================================
     CALL TO ACTION BANNER
     ======================================================== -->
<section class="py-5 text-white position-relative" style="background: linear-gradient(135deg, var(--primary) 0%, var(--primary-hover) 100%);">
    <div class="container text-center py-4 position-relative z-1">
        <h2 class="fw-black display-5 mb-3 text-white"><?= htmlspecialchars($ctaHeading) ?></h2>
        <p class="lead opacity-90 mb-4 mx-auto" style="max-width: 680px;">
            <?= htmlspecialchars($ctaDesc) ?>
        </p>
        <div class="d-flex justify-content-center gap-3">
            <a href="<?= BASE_URL . $ctaBtnUrl ?>" class="btn btn-light btn-lg px-4 py-3 fw-black text-dark shadow-lg">
                <i class="fa-solid fa-paper-plane me-2 text-primary"></i> <?= htmlspecialchars($ctaBtnText) ?>
            </a>
            <a href="<?= BASE_URL ?>pipe-calculator.php" class="btn btn-outline-light btn-lg px-4 py-3 fw-bold">
                <i class="fa-solid fa-calculator me-2"></i> Hydraulic Calculator
            </a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
