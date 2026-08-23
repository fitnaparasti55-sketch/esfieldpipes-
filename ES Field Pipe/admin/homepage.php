<?php
/**
 * Homepage Editor & Section Manager - Esfield Pipe
 */
$pageTitle = "Homepage Content Editor";
require_once __DIR__ . '/includes/header.php';

$settings = get_settings();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-black mb-1 text-dark">Homepage Sections & Content</h4>
        <p class="text-muted small mb-0">Customize hero marketing statements, corporate descriptions, engineered features, and call-to-actions dynamically.</p>
    </div>
    <a href="<?= BASE_URL ?>" target="_blank" class="btn btn-outline-primary btn-sm fw-bold">
        <i class="fa-solid fa-eye me-1"></i> Preview Homepage
    </a>
</div>

<form action="<?= BASE_URL ?>api/admin-homepage.php" method="POST" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <div class="row g-4">
        <!-- 1. HERO SECTION -->
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3 px-4">
                    <h5 class="fw-bold mb-0 text-primary"><i class="fa-solid fa-desktop me-2"></i> 1. Hero Showcase Section</h5>
                </div>
                <div class="card-body p-4 border-top">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Hero Badge Label</label>
                            <input type="text" name="home_hero_badge" class="form-control" value="<?= htmlspecialchars($settings['home_hero_badge'] ?? 'BIS IS:16098 (Part-2) & EN 13476 Certified') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Hero Primary Headline</label>
                            <input type="text" name="home_hero_heading" class="form-control" value="<?= htmlspecialchars($settings['home_hero_heading'] ?? 'Engineered Strength. High-Flow DWC HDPE Piping Systems.') ?>" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold small">Hero Subheading & Engineering Description</label>
                            <textarea name="home_hero_subheading" class="form-control" rows="3"><?= htmlspecialchars($settings['home_hero_subheading'] ?? '') ?></textarea>
                        </div>

                        <!-- Buttons -->
                        <div class="col-md-3">
                            <label class="form-label fw-bold small">Primary Button Text</label>
                            <input type="text" name="home_hero_btn1_text" class="form-control" value="<?= htmlspecialchars($settings['home_hero_btn1_text'] ?? 'Explore Pipe Catalog') ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold small">Primary Button URL</label>
                            <input type="text" name="home_hero_btn1_url" class="form-control" value="<?= htmlspecialchars($settings['home_hero_btn1_url'] ?? 'products.php') ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold small">Secondary Button Text</label>
                            <input type="text" name="home_hero_btn2_text" class="form-control" value="<?= htmlspecialchars($settings['home_hero_btn2_text'] ?? 'Sizing Calculator') ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold small">Secondary Button URL</label>
                            <input type="text" name="home_hero_btn2_url" class="form-control" value="<?= htmlspecialchars($settings['home_hero_btn2_url'] ?? 'pipe-calculator.php') ?>">
                        </div>

                        <!-- 3 Stats Cards -->
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Stat 1 (Number & Label)</label>
                            <div class="input-group input-group-sm">
                                <input type="text" name="home_stat1_number" class="form-control" value="<?= htmlspecialchars($settings['home_stat1_number'] ?? '50-1200') ?>" placeholder="50-1200">
                                <input type="text" name="home_stat1_label" class="form-control" value="<?= htmlspecialchars($settings['home_stat1_label'] ?? 'mm Diameters') ?>" placeholder="Label">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Stat 2 (Number & Label)</label>
                            <div class="input-group input-group-sm">
                                <input type="text" name="home_stat2_number" class="form-control" value="<?= htmlspecialchars($settings['home_stat2_number'] ?? 'SN8') ?>" placeholder="SN8">
                                <input type="text" name="home_stat2_label" class="form-control" value="<?= htmlspecialchars($settings['home_stat2_label'] ?? 'Ring Stiffness') ?>" placeholder="Label">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Stat 3 (Number & Label)</label>
                            <div class="input-group input-group-sm">
                                <input type="text" name="home_stat3_number" class="form-control" value="<?= htmlspecialchars($settings['home_stat3_number'] ?? '50+ Yrs') ?>" placeholder="50+ Yrs">
                                <input type="text" name="home_stat3_label" class="form-control" value="<?= htmlspecialchars($settings['home_stat3_label'] ?? 'Service Lifespan') ?>" placeholder="Label">
                            </div>
                        </div>

                        <!-- Hero Image -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Hero Graphic / Visual Image Path</label>
                            <input type="text" name="home_hero_image" class="form-control" value="<?= htmlspecialchars($settings['home_hero_image'] ?? 'assets/images/dwc-cross-section.svg') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Or Upload New Hero Image</label>
                            <input type="file" name="hero_image_file" class="form-control" accept=".jpg,.jpeg,.png,.webp,.svg">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. COMPANY / ABOUT SECTION -->
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3 px-4">
                    <h5 class="fw-bold mb-0 text-primary"><i class="fa-solid fa-building me-2"></i> 2. Company & Manufacturing Profile Section</h5>
                </div>
                <div class="card-body p-4 border-top">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Company Section Main Heading</label>
                            <input type="text" name="home_company_heading" class="form-control" value="<?= htmlspecialchars($settings['home_company_heading'] ?? 'Pioneering Heavy Infrastructure & Drainage Technology') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Company Subheading / Tagline</label>
                            <input type="text" name="home_company_subheading" class="form-control" value="<?= htmlspecialchars($settings['home_company_subheading'] ?? 'Precision Engineered Double Wall Corrugated HDPE Pipes') ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold small">Detailed Company Narrative</label>
                            <textarea name="home_company_desc" class="form-control" rows="3"><?= htmlspecialchars($settings['home_company_desc'] ?? 'Esfield Pipe provides advanced structural wall piping designed to withstand severe dynamic axle loading, seismic movement, and chemical aggression in municipal and industrial projects.') ?></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Company Section Visual Image Path</label>
                            <input type="text" name="home_company_image" class="form-control" value="<?= htmlspecialchars($settings['home_company_image'] ?? 'assets/images/dwc-cross-section.svg') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Or Upload Image</label>
                            <input type="file" name="company_image_file" class="form-control" accept=".jpg,.jpeg,.png,.webp,.svg">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. PRODUCTS CATALOG DISPLAY ON HOMEPAGE -->
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3 px-4">
                    <h5 class="fw-bold mb-0 text-primary"><i class="fa-solid fa-cubes me-2"></i> 3. Featured Products Showcase</h5>
                </div>
                <div class="card-body p-4 border-top">
                    <div class="row g-3">
                        <div class="col-md-5">
                            <label class="form-label fw-bold small">Products Section Title</label>
                            <input type="text" name="home_products_heading" class="form-control" value="<?= htmlspecialchars($settings['home_products_heading'] ?? 'Featured Infrastructure Pipe Systems') ?>">
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-bold small">Products Subtitle / Description</label>
                            <input type="text" name="home_products_desc" class="form-control" value="<?= htmlspecialchars($settings['home_products_desc'] ?? 'Explore our BIS IS:16098 Part 2 certified structured wall DWC pipes.') ?>">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold small">Max Products Shown</label>
                            <input type="number" name="home_products_count" class="form-control" value="<?= htmlspecialchars($settings['home_products_count'] ?? '6') ?>" min="2" max="24">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. CALL TO ACTION BANNER -->
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3 px-4">
                    <h5 class="fw-bold mb-0 text-primary"><i class="fa-solid fa-bullhorn me-2"></i> 4. Call-To-Action (CTA) Banner</h5>
                </div>
                <div class="card-body p-4 border-top">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">CTA Banner Heading</label>
                            <input type="text" name="home_cta_heading" class="form-control" value="<?= htmlspecialchars($settings['home_cta_heading'] ?? 'Ready to Upgrade Your Pipeline Infrastructure?') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">CTA Banner Subtitle</label>
                            <input type="text" name="home_cta_desc" class="form-control" value="<?= htmlspecialchars($settings['home_cta_desc'] ?? 'Contact our engineering sales department for project-specific sizing, factory inspections, or bulk institutional quotation tenders.') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">CTA Button Text</label>
                            <input type="text" name="home_cta_btn_text" class="form-control" value="<?= htmlspecialchars($settings['home_cta_btn_text'] ?? 'Request Bulk RFQ Quote') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">CTA Button URL</label>
                            <input type="text" name="home_cta_btn_url" class="form-control" value="<?= htmlspecialchars($settings['home_cta_btn_url'] ?? 'contact.php') ?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Save Button -->
        <div class="col-12 mt-4 text-end">
            <button type="submit" class="btn btn-primary px-5 py-3 fw-bold rounded-3 shadow">
                <i class="fa-solid fa-floppy-disk me-2"></i> Save Homepage Settings
            </button>
        </div>
    </div>
</form>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
