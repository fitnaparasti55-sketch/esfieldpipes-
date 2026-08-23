<?php
/**
 * General Site Settings - Esfield Pipe
 */
$pageTitle = "Site Configuration";
require_once __DIR__ . '/includes/header.php';

$settings = get_settings();
?>

<div class="row justify-content-center">
    <div class="col-xl-10">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-black mb-1 text-dark">Global System & Store Configuration</h4>
                <p class="text-muted small mb-0">Configure taxation rates, default currency, payment gateways, and system parameters.</p>
            </div>
            <a href="dashboard.php" class="btn btn-outline-secondary btn-sm fw-bold">
                <i class="fa-solid fa-arrow-left me-1"></i> Dashboard
            </a>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4 p-md-5">
                <form action="<?= BASE_URL ?>api/admin-settings.php" method="POST">
                    <?= csrf_field() ?>

                    <div class="row g-4">
                        <!-- Store & Tax Configuration -->
                        <div class="col-12">
                            <h6 class="fw-bold border-bottom pb-2 mb-3 text-primary text-uppercase small tracking-wider">
                                <i class="fa-solid fa-file-invoice-dollar me-1"></i> Taxation & Currency Settings
                            </h6>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Site Currency Symbol</label>
                            <input type="text" name="site_currency" class="form-control" value="<?= htmlspecialchars($settings['site_currency'] ?? '₹') ?>" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Standard GST Rate (%)</label>
                            <input type="number" name="site_gst_rate" class="form-control" value="<?= htmlspecialchars($settings['site_gst_rate'] ?? '18') ?>" required>
                            <small class="text-muted">Standard Indian Goods & Services Tax slab.</small>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Corporate GSTIN</label>
                            <input type="text" name="gstin" class="form-control" value="<?= htmlspecialchars($settings['gstin'] ?? '07AABCE9876F1Z4') ?>">
                        </div>

                        <!-- Payment Gateways (Optional API Keys) -->
                        <div class="col-12 mt-4">
                            <h6 class="fw-bold border-bottom pb-2 mb-3 text-primary text-uppercase small tracking-wider">
                                <i class="fa-solid fa-credit-card me-1"></i> Payment Gateway API Credentials
                            </h6>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Razorpay Key ID</label>
                            <input type="text" name="razorpay_key_id" class="form-control font-monospace" value="<?= htmlspecialchars($settings['razorpay_key_id'] ?? '') ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Razorpay Key Secret</label>
                            <input type="password" name="razorpay_key_secret" class="form-control font-monospace" value="<?= htmlspecialchars($settings['razorpay_key_secret'] ?? '') ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Stripe Publishable Key</label>
                            <input type="text" name="stripe_publishable_key" class="form-control font-monospace" value="<?= htmlspecialchars($settings['stripe_publishable_key'] ?? '') ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Stripe Secret Key</label>
                            <input type="password" name="stripe_secret_key" class="form-control font-monospace" value="<?= htmlspecialchars($settings['stripe_secret_key'] ?? '') ?>">
                        </div>

                        <!-- Submit Button -->
                        <div class="col-12 mt-5 pt-3 border-top text-end">
                            <button type="submit" class="btn btn-primary px-5 py-3 fw-bold rounded-3 shadow">
                                <i class="fa-solid fa-floppy-disk me-2"></i> Save Site Configuration
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
