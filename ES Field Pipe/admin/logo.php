<?php
/**
 * Website Logo & Branding Management - Esfield Pipe
 */
$pageTitle = "Website Logo Management";
require_once __DIR__ . '/includes/header.php';

$siteLogo = get_setting('site_logo', 'assets/images/logo.svg');
$siteLogoMobile = get_setting('site_logo_mobile', 'assets/images/logo.svg');
$siteFavicon = get_setting('site_favicon', 'assets/images/logo.svg');
?>

<div class="row justify-content-center">
    <div class="col-xl-9">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-black mb-1 text-dark">Website Logos & Favicon</h4>
                <p class="text-muted small mb-0">Upload and configure brand assets rendered centrally across desktop, mobile app drawer, and browser tabs.</p>
            </div>
            <a href="dashboard.php" class="btn btn-outline-secondary btn-sm fw-bold">
                <i class="fa-solid fa-arrow-left me-1"></i> Dashboard
            </a>
        </div>

        <form action="<?= BASE_URL ?>api/admin-logo.php" method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="upload_logos">

            <div class="row g-4">
                <!-- Main Desktop Logo -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <h6 class="fw-bold text-dark mb-3"><i class="fa-solid fa-desktop text-primary me-2"></i> Main Desktop Header Logo</h6>
                            
                            <div class="p-4 rounded-3 border bg-light text-center mb-3 d-flex align-items-center justify-content-center" style="min-height: 120px; background: #0f172a !important;">
                                <img src="<?= BASE_URL . $siteLogo ?>?v=<?= time() ?>" alt="Main Logo Preview" style="max-height: 50px; max-width: 100%;">
                            </div>

                            <label class="form-label fw-bold small">Upload New Main Logo (SVG, PNG, WEBP)</label>
                            <input type="file" name="site_logo" class="form-control mb-3" accept=".svg,.png,.jpg,.jpeg,.webp">
                            <small class="text-muted d-block mb-3">Recommended size: 240x50px transparent SVG or PNG.</small>

                            <button type="submit" name="target" value="site_logo" class="btn btn-sm btn-outline-secondary" onclick="resetLogoField('site_logo')">
                                <i class="fa-solid fa-rotate-left me-1"></i> Reset to Default
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Mobile Header Logo -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <h6 class="fw-bold text-dark mb-3"><i class="fa-solid fa-mobile-screen text-primary me-2"></i> Mobile Navbar & Drawer Logo</h6>
                            
                            <div class="p-4 rounded-3 border bg-light text-center mb-3 d-flex align-items-center justify-content-center" style="min-height: 120px; background: #0f172a !important;">
                                <img src="<?= BASE_URL . $siteLogoMobile ?>?v=<?= time() ?>" alt="Mobile Logo Preview" style="max-height: 38px; max-width: 100%;">
                            </div>

                            <label class="form-label fw-bold small">Upload New Mobile Logo</label>
                            <input type="file" name="site_logo_mobile" class="form-control mb-3" accept=".svg,.png,.jpg,.jpeg,.webp">
                            <small class="text-muted d-block mb-3">Optimized for compact mobile header navigation.</small>

                            <button type="submit" name="target" value="site_logo_mobile" class="btn btn-sm btn-outline-secondary" onclick="resetLogoField('site_logo_mobile')">
                                <i class="fa-solid fa-rotate-left me-1"></i> Reset to Default
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Favicon -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <h6 class="fw-bold text-dark mb-3"><i class="fa-solid fa-bookmark text-primary me-2"></i> Browser Tab Favicon</h6>
                            
                            <div class="p-4 rounded-3 border bg-light text-center mb-3 d-flex align-items-center justify-content-center" style="min-height: 100px;">
                                <img src="<?= BASE_URL . $siteFavicon ?>?v=<?= time() ?>" alt="Favicon Preview" style="width: 32px; height: 32px; object-fit: contain;">
                            </div>

                            <label class="form-label fw-bold small">Upload Favicon (.ico, .png, .svg)</label>
                            <input type="file" name="site_favicon" class="form-control mb-3" accept=".ico,.png,.svg,.webp">
                            <small class="text-muted d-block mb-3">Square 32x32 or 64x64 icon.</small>

                            <button type="submit" name="target" value="site_favicon" class="btn btn-sm btn-outline-secondary" onclick="resetLogoField('site_favicon')">
                                <i class="fa-solid fa-rotate-left me-1"></i> Reset to Default
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Save All Button -->
                <div class="col-12 mt-4 text-end">
                    <button type="submit" class="btn btn-primary px-5 py-3 fw-bold rounded-3">
                        <i class="fa-solid fa-cloud-arrow-up me-2"></i> Save & Apply Logos
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<form id="resetLogoForm" action="<?= BASE_URL ?>api/admin-logo.php" method="POST" style="display:none;">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="reset_logo">
    <input type="hidden" name="target" id="resetLogoTarget">
</form>

<script>
function resetLogoField(target) {
    if (confirm('Are you sure you want to restore the default logo for this item?')) {
        document.getElementById('resetLogoTarget').value = target;
        document.getElementById('resetLogoForm').submit();
    }
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
