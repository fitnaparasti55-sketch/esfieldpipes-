<?php
/**
 * Contact & Bulk Tender Quotation Request - Esfield Pipe
 */
$pageTitle = "Request Quote & Contact Engineering Desk";
require_once __DIR__ . '/includes/header.php';

$settings = get_settings();
$sitePhone = $settings['site_phone'] ?? '+91 98765 43210';
$sitePhoneAlt = $settings['site_phone_alt'] ?? '+91 11 2345 6789';
$siteEmail = $settings['site_email'] ?? 'sales@esfieldpipe.com';
$siteAddress = $settings['site_address'] ?? 'Plot No. 42-45, Industrial Mega Infrastructure Park, Phase-II, New Delhi - 110001, India';
$gstin = $settings['gstin'] ?? '07AABCE9876F1Z4';

$prefilledRequirement = trim($_GET['req'] ?? '');
?>

<div class="py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-11">
                <div class="text-center mb-5">
                    <span class="badge bg-primary bg-opacity-10 text-primary fw-bold px-3 py-2 rounded-pill mb-2">FACTORY DIRECT PROCUREMENT</span>
                    <h2 class="fw-black text-dark">Request Bulk Tender Quote & Technical Assistance</h2>
                    <p class="text-muted mx-auto" style="max-width: 680px;">
                        Connect with our central plant engineering desk for contractor discount slabs, factory NABL inspection certificates, and specialized pipe sizing logistics.
                    </p>
                </div>

                <div class="row g-4 mb-5">
                    <!-- Contact Channels -->
                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm p-4 h-100 bg-white rounded-4">
                            <h5 class="fw-bold text-dark mb-4 border-bottom pb-3">Corporate Works</h5>

                            <div class="d-flex align-items-start gap-3 mb-4">
                                <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-2.5 fs-5">
                                    <i class="fa-solid fa-location-dot"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1">Plant & Headquarters</h6>
                                    <p class="small text-muted mb-0"><?= htmlspecialchars($siteAddress) ?></p>
                                </div>
                            </div>

                            <div class="d-flex align-items-start gap-3 mb-4">
                                <div class="bg-warning bg-opacity-10 text-warning rounded-3 p-2.5 fs-5">
                                    <i class="fa-solid fa-phone"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1">Direct Sales Hotline</h6>
                                    <p class="small text-muted mb-0"><?= htmlspecialchars($sitePhone) ?></p>
                                    <p class="small text-muted mb-0"><?= htmlspecialchars($sitePhoneAlt) ?></p>
                                </div>
                            </div>

                            <div class="d-flex align-items-start gap-3 mb-4">
                                <div class="bg-info bg-opacity-10 text-info rounded-3 p-2.5 fs-5">
                                    <i class="fa-solid fa-envelope"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1">Commercial Inquiries</h6>
                                    <p class="small text-muted mb-0"><?= htmlspecialchars($siteEmail) ?></p>
                                </div>
                            </div>

                            <div class="d-flex align-items-start gap-3">
                                <div class="bg-success bg-opacity-10 text-success rounded-3 p-2.5 fs-5">
                                    <i class="fa-solid fa-file-invoice"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1">Tax Registration</h6>
                                    <p class="small text-muted mb-0">GSTIN: <strong><?= htmlspecialchars($gstin) ?></strong></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- RFQ Form -->
                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm p-4 p-md-5 bg-white rounded-4">
                            <h5 class="fw-bold text-dark mb-4 border-bottom pb-3">
                                <i class="fa-solid fa-pen-to-square text-primary me-2"></i> Submit Project RFQ / Technical Inquiry
                            </h5>

                            <form action="<?= BASE_URL ?>api/contact.php" method="POST">
                                <?= csrf_field() ?>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small">Your Full Name *</label>
                                        <input type="text" name="name" class="form-control" placeholder="e.g. Rajesh Verma" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small">Work Email Address *</label>
                                        <input type="email" name="email" class="form-control" placeholder="engineer@contractor.com" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small">Phone / Mobile Number</label>
                                        <input type="text" name="phone" class="form-control" placeholder="+91 98765 43210">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small">Company / Contracting Firm</label>
                                        <input type="text" name="company" class="form-control" placeholder="e.g. National Infra Ltd.">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small">Inquiry Classification</label>
                                        <select name="inquiry_type" class="form-select">
                                            <option value="quote" selected>Bulk Pricing & Tender Quotation (RFQ)</option>
                                            <option value="technical">Technical Specs & Lab BIS Test Reports</option>
                                            <option value="bulk">Mega Project Institutional Dispatch (>10,000m)</option>
                                            <option value="support">Existing Order Logistics / Dispatch Query</option>
                                            <option value="general">General Corporate Inquiry</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small">Subject *</label>
                                        <input type="text" name="subject" class="form-control" value="Quotation for DWC HDPE Corrugated Pipes" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-bold small">Specific Pipe Diameters / Quantities Required</label>
                                        <input type="text" name="pipe_requirement" class="form-control" placeholder="e.g. 300mm ID SN8 (2,500 meters) and 500mm ID SN8 (1,000 meters)" value="<?= htmlspecialchars($prefilledRequirement) ?>">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-bold small">Project Overview & Delivery Site Location *</label>
                                        <textarea name="message" class="form-control" rows="4" placeholder="Detail your project site city/state, expected delivery timeline, and technical requirements..." required></textarea>
                                    </div>
                                    <div class="col-12 mt-4">
                                        <button type="submit" class="btn btn-primary px-5 py-3 fw-bold rounded-3 shadow">
                                            <i class="fa-solid fa-paper-plane me-2"></i> Submit Quotation Request
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
