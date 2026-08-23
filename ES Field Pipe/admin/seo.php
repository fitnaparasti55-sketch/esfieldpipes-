<?php
/**
 * SEO & Meta Tags Configuration - Esfield Pipe
 */
$pageTitle = "SEO & Meta Configuration";
require_once __DIR__ . '/includes/header.php';

$settings = get_settings();
?>

<div class="row justify-content-center">
    <div class="col-xl-9">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-black mb-1 text-dark">Search Engine Optimization (SEO)</h4>
                <p class="text-muted small mb-0">Control how Google, Bing, LinkedIn, and social media scrapers index and display your website.</p>
            </div>
            <a href="dashboard.php" class="btn btn-outline-secondary btn-sm fw-bold">
                <i class="fa-solid fa-arrow-left me-1"></i> Dashboard
            </a>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4 p-md-5">
                <form action="<?= BASE_URL ?>api/admin-seo.php" method="POST">
                    <?= csrf_field() ?>

                    <div class="row g-4">
                        <!-- Global Metadata -->
                        <div class="col-12">
                            <h6 class="fw-bold border-bottom pb-2 mb-3 text-primary text-uppercase small tracking-wider">
                                <i class="fa-solid fa-globe me-1"></i> Global Meta Tags
                            </h6>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold small">Default Page Title Tag</label>
                            <input type="text" name="meta_title" class="form-control" value="<?= htmlspecialchars($settings['meta_title'] ?? 'Esfield Pipe | DWC HDPE Pipes & Infrastructure Solutions') ?>" required>
                            <small class="text-muted">Appended as suffix to catalog and product detail pages.</small>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold small">Global Meta Description</label>
                            <textarea name="meta_description" class="form-control" rows="3"><?= htmlspecialchars($settings['meta_description'] ?? 'India\'s premier manufacturer of Double Wall Corrugated (DWC) HDPE pipes conforming to IS 16098 Part-2 and EN 13476 for sewerage, drainage, and telecom ducting.') ?></textarea>
                            <small class="text-muted">Recommended length: 140-160 characters for optimal Google search snippet display.</small>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold small">Meta Keywords</label>
                            <input type="text" name="meta_keywords" class="form-control" value="<?= htmlspecialchars($settings['meta_keywords'] ?? 'DWC pipe, HDPE corrugated pipe, IS 16098 Part 2, structured wall pipe, sewerage pipe, culvert pipe, SN8 stiffness, telecom duct') ?>">
                            <small class="text-muted">Comma-separated industry search terms.</small>
                        </div>

                        <!-- Homepage Specific SEO -->
                        <div class="col-12 mt-4">
                            <h6 class="fw-bold border-bottom pb-2 mb-3 text-primary text-uppercase small tracking-wider">
                                <i class="fa-solid fa-house-chimney me-1"></i> Homepage Specific SEO
                            </h6>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold small">Homepage SEO Title</label>
                            <input type="text" name="home_seo_title" class="form-control" value="<?= htmlspecialchars($settings['home_seo_title'] ?? 'Esfield Pipe - High Flow DWC HDPE Corrugated Pipes') ?>">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold small">Homepage SEO Meta Description</label>
                            <textarea name="home_seo_description" class="form-control" rows="3"><?= htmlspecialchars($settings['home_seo_description'] ?? 'Manufacturer of DWC HDPE pipes 50mm to 1200mm for municipal drainage, smart cities, highway culverts and cable ducting.') ?></textarea>
                        </div>

                        <!-- Social Share (OpenGraph) & Indexing -->
                        <div class="col-12 mt-4">
                            <h6 class="fw-bold border-bottom pb-2 mb-3 text-primary text-uppercase small tracking-wider">
                                <i class="fa-solid fa-share-nodes me-1"></i> Social Sharing & Robots Indexing
                            </h6>
                        </div>

                        <div class="col-md-8">
                            <label class="form-label fw-bold small">OpenGraph Social Preview Image Path (OG:Image)</label>
                            <input type="text" name="og_image" class="form-control" value="<?= htmlspecialchars($settings['og_image'] ?? 'assets/images/logo.svg') ?>">
                            <small class="text-muted">Image shown when links are shared on LinkedIn, WhatsApp, or Facebook.</small>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Robots Indexing</label>
                            <select name="robots_indexing" class="form-select">
                                <option value="index, follow" <?= ($settings['robots_indexing'] ?? '') === 'index, follow' ? 'selected' : '' ?>>index, follow (Standard)</option>
                                <option value="noindex, follow" <?= ($settings['robots_indexing'] ?? '') === 'noindex, follow' ? 'selected' : '' ?>>noindex, follow</option>
                                <option value="noindex, nofollow" <?= ($settings['robots_indexing'] ?? '') === 'noindex, nofollow' ? 'selected' : '' ?>>noindex, nofollow (Development)</option>
                            </select>
                        </div>

                        <!-- Submit Button -->
                        <div class="col-12 mt-5 pt-3 border-top text-end">
                            <button type="submit" class="btn btn-primary px-5 py-3 fw-bold rounded-3 shadow">
                                <i class="fa-solid fa-floppy-disk me-2"></i> Save SEO Configurations
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
