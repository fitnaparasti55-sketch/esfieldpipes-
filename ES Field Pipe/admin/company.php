<?php
/**
 * Company Information & Business Details - Esfield Pipe
 */
$pageTitle = "Company Information & Contact";
require_once __DIR__ . '/includes/header.php';

$settings = get_settings();
?>

<div class="row justify-content-center">
    <div class="col-xl-10">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-black mb-1 text-dark">Corporate & Contact Information</h4>
                <p class="text-muted small mb-0">Changes update dynamically across the header topbar, footer, quotation invoices, and contact pages.</p>
            </div>
            <a href="dashboard.php" class="btn btn-outline-secondary btn-sm fw-bold">
                <i class="fa-solid fa-arrow-left me-1"></i> Dashboard
            </a>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4 p-md-5">
                <form action="<?= BASE_URL ?>api/admin-company.php" method="POST">
                    <?= csrf_field() ?>

                    <div class="row g-4">
                        <!-- Corporate Identification -->
                        <div class="col-12">
                            <h6 class="fw-bold border-bottom pb-2 mb-3 text-primary text-uppercase small tracking-wider">
                                <i class="fa-solid fa-building me-1"></i> Legal Entity & Brand Details
                            </h6>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Company Name *</label>
                            <input type="text" name="site_name" class="form-control" value="<?= htmlspecialchars($settings['site_name'] ?? 'Esfield Pipe Pvt. Ltd.') ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Brand Tagline</label>
                            <input type="text" name="site_tagline" class="form-control" value="<?= htmlspecialchars($settings['site_tagline'] ?? '') ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Website Base URL</label>
                            <input type="text" name="site_url" class="form-control" value="<?= htmlspecialchars($settings['site_url'] ?? BASE_URL) ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small">BIS & Standards Certification</label>
                            <input type="text" name="bis_info" class="form-control" value="<?= htmlspecialchars($settings['bis_info'] ?? 'BIS IS:16098 (Part-2) Certified Manufacturer') ?>">
                        </div>

                        <!-- Direct Contact & Channels -->
                        <div class="col-12 mt-4">
                            <h6 class="fw-bold border-bottom pb-2 mb-3 text-primary text-uppercase small tracking-wider">
                                <i class="fa-solid fa-phone me-1"></i> Contact Channels & Customer Support
                            </h6>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Primary Phone / Customer Care *</label>
                            <input type="text" name="site_phone" class="form-control" value="<?= htmlspecialchars($settings['site_phone'] ?? '+91 98765 43210') ?>" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Alternate / Landline Phone</label>
                            <input type="text" name="site_phone_alt" class="form-control" value="<?= htmlspecialchars($settings['site_phone_alt'] ?? '+91 11 2345 6789') ?>">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold small">WhatsApp Number</label>
                            <input type="text" name="site_whatsapp" class="form-control" value="<?= htmlspecialchars($settings['site_whatsapp'] ?? '+91 98765 43210') ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Sales & Inquiries Email *</label>
                            <input type="email" name="site_email" class="form-control" value="<?= htmlspecialchars($settings['site_email'] ?? 'sales@esfieldpipe.com') ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small">GSTIN Number (Tax Registration)</label>
                            <input type="text" name="gstin" class="form-control" value="<?= htmlspecialchars($settings['gstin'] ?? '07AABCE9876F1Z4') ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Corporate PAN</label>
                            <input type="text" name="pan_number" class="form-control" value="<?= htmlspecialchars($settings['pan_number'] ?? 'AABCE9876F') ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Corporate CIN Number</label>
                            <input type="text" name="cin_number" class="form-control" value="<?= htmlspecialchars($settings['cin_number'] ?? 'U25209DL2018PTC334567') ?>">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold small">Head Office & Plant Address</label>
                            <textarea name="site_address" class="form-control" rows="2"><?= htmlspecialchars($settings['site_address'] ?? 'Plot No. 42-45, Industrial Mega Infrastructure Park, Phase-II, New Delhi - 110001, India') ?></textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold small">Footer Corporate Statement</label>
                            <textarea name="footer_about" class="form-control" rows="3"><?= htmlspecialchars($settings['footer_about'] ?? '') ?></textarea>
                        </div>

                        <!-- Social Media Handles -->
                        <div class="col-12 mt-4">
                            <h6 class="fw-bold border-bottom pb-2 mb-3 text-primary text-uppercase small tracking-wider">
                                <i class="fa-solid fa-share-nodes me-1"></i> Social Media & Networks
                            </h6>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold small"><i class="fa-brands fa-linkedin text-primary me-1"></i> LinkedIn Page</label>
                            <input type="url" name="linkedin_url" class="form-control" value="<?= htmlspecialchars($settings['linkedin_url'] ?? '') ?>" placeholder="https://linkedin.com/company/esfieldpipe">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold small"><i class="fa-brands fa-facebook text-primary me-1"></i> Facebook Page</label>
                            <input type="url" name="facebook_url" class="form-control" value="<?= htmlspecialchars($settings['facebook_url'] ?? '') ?>" placeholder="https://facebook.com/esfieldpipe">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold small"><i class="fa-brands fa-x-twitter me-1"></i> Twitter / X Profile</label>
                            <input type="url" name="twitter_url" class="form-control" value="<?= htmlspecialchars($settings['twitter_url'] ?? '') ?>" placeholder="https://twitter.com/esfieldpipe">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small"><i class="fa-brands fa-instagram text-danger me-1"></i> Instagram Profile</label>
                            <input type="url" name="instagram_url" class="form-control" value="<?= htmlspecialchars($settings['instagram_url'] ?? '') ?>" placeholder="https://instagram.com/esfieldpipe">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small"><i class="fa-brands fa-youtube text-danger me-1"></i> YouTube Channel</label>
                            <input type="url" name="youtube_url" class="form-control" value="<?= htmlspecialchars($settings['youtube_url'] ?? '') ?>" placeholder="https://youtube.com/@esfieldpipe">
                        </div>

                        <!-- Submit Button -->
                        <div class="col-12 mt-5 pt-3 border-top text-end">
                            <button type="submit" class="btn btn-primary px-5 py-3 fw-bold rounded-3 shadow">
                                <i class="fa-solid fa-floppy-disk me-2"></i> Save Company Information
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
