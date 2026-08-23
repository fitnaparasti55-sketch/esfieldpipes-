<?php
/**
 * Master Footer Template (Desktop Corporate Footer + Mobile Bottom Navigation Bar)
 * Esfield Pipe Platform - Dynamic Settings Integration
 */

$cartCount = get_cart_count();
$user = current_user();
$settings = get_settings();

$siteName = $settings['site_name'] ?? 'Esfield Pipe Pvt. Ltd.';
$sitePhone = $settings['site_phone'] ?? '+91 98765 43210';
$sitePhoneAlt = $settings['site_phone_alt'] ?? '+91 11 2345 6789';
$siteEmail = $settings['site_email'] ?? 'sales@esfieldpipe.com';
$siteAddress = $settings['site_address'] ?? 'Plot No. 42-45, Industrial Mega Infrastructure Park, Phase-II, New Delhi - 110001, India';
$footerAbout = $settings['footer_about'] ?? 'Esfield Pipe is India\'s premier manufacturer of high-density polyethylene Double Wall Corrugated (DWC) pipes conforming to IS 16098 (Part 2) & EN 13476 standards.';
$gstin = $settings['gstin'] ?? '07AABCE9876F1Z4';
$siteLogo = $settings['site_logo'] ?? 'assets/images/logo.svg';

// Social Links
$facebookUrl = $settings['facebook_url'] ?? '';
$linkedinUrl = $settings['linkedin_url'] ?? '';
$twitterUrl = $settings['twitter_url'] ?? '';
$instagramUrl = $settings['instagram_url'] ?? '';
$youtubeUrl = $settings['youtube_url'] ?? '';

$currentPage = basename($_SERVER['PHP_SELF']);
?>
    </main>

    <!-- ========================================================
         DESKTOP FOOTER
         ======================================================== -->
    <footer class="mt-5 pt-5 pb-4 border-top" style="background-color: var(--topbar-bg); color: var(--topbar-text);">
        <div class="container">
            <div class="row g-4 pb-4 border-bottom border-secondary border-opacity-25">
                <!-- Brand & Certifications -->
                <div class="col-lg-4 col-md-6">
                    <div class="d-flex align-items-center mb-3">
                        <img src="<?= BASE_URL . $siteLogo ?>?v=<?= time() ?>" alt="<?= htmlspecialchars($siteName) ?>" style="height: 38px;">
                    </div>
                    <p class="small text-secondary mb-3 leading-relaxed">
                        <?= htmlspecialchars($footerAbout) ?>
                    </p>
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <span class="badge bg-dark border border-secondary text-warning px-2.5 py-1.5"><i class="fa-solid fa-award me-1"></i> BIS IS:16098-2</span>
                        <span class="badge bg-dark border border-secondary text-info px-2.5 py-1.5"><i class="fa-solid fa-shield-halved me-1"></i> ISO 9001:2015</span>
                        <span class="badge bg-dark border border-secondary text-success px-2.5 py-1.5"><i class="fa-solid fa-leaf me-1"></i> 100% Recyclable PE-100</span>
                    </div>

                    <!-- Social Icons -->
                    <div class="d-flex gap-2 mt-2">
                        <?php if (!empty($linkedinUrl)): ?>
                            <a href="<?= htmlspecialchars($linkedinUrl) ?>" target="_blank" class="btn btn-sm btn-dark border border-secondary text-light" title="LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
                        <?php endif; ?>
                        <?php if (!empty($facebookUrl)): ?>
                            <a href="<?= htmlspecialchars($facebookUrl) ?>" target="_blank" class="btn btn-sm btn-dark border border-secondary text-light" title="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                        <?php endif; ?>
                        <?php if (!empty($twitterUrl)): ?>
                            <a href="<?= htmlspecialchars($twitterUrl) ?>" target="_blank" class="btn btn-sm btn-dark border border-secondary text-light" title="Twitter / X"><i class="fa-brands fa-x-twitter"></i></a>
                        <?php endif; ?>
                        <?php if (!empty($instagramUrl)): ?>
                            <a href="<?= htmlspecialchars($instagramUrl) ?>" target="_blank" class="btn btn-sm btn-dark border border-secondary text-light" title="Instagram"><i class="fa-brands fa-instagram"></i></a>
                        <?php endif; ?>
                        <?php if (!empty($youtubeUrl)): ?>
                            <a href="<?= htmlspecialchars($youtubeUrl) ?>" target="_blank" class="btn btn-sm btn-dark border border-secondary text-light" title="YouTube"><i class="fa-brands fa-youtube"></i></a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Quick Product Categories -->
                <div class="col-lg-2 col-md-6">
                    <h6 class="text-white fw-bold mb-3">DWC Pipe Catalog</h6>
                    <ul class="list-unstyled small text-secondary d-flex flex-column gap-2 mb-0">
                        <li><a href="<?= BASE_URL ?>products.php?category=underground-drainage-sewerage" class="text-secondary text-decoration-none hover-primary">Sewerage & Drainage</a></li>
                        <li><a href="<?= BASE_URL ?>products.php?category=telecom-cable-ducting" class="text-secondary text-decoration-none hover-primary">Telecom Cable Ducts</a></li>
                        <li><a href="<?= BASE_URL ?>products.php?category=highway-railway-culverts" class="text-secondary text-decoration-none hover-primary">Highway Culverts (SN8)</a></li>
                        <li><a href="<?= BASE_URL ?>products.php?category=industrial-effluent-pipes" class="text-secondary text-decoration-none hover-primary">Industrial Chemical</a></li>
                        <li><a href="<?= BASE_URL ?>products.php?category=stormwater-rainwater-harvesting" class="text-secondary text-decoration-none hover-primary">Stormwater Drainage</a></li>
                    </ul>
                </div>

                <!-- Engineering & Quick Tools -->
                <div class="col-lg-3 col-md-6">
                    <h6 class="text-white fw-bold mb-3">Engineering Tools</h6>
                    <ul class="list-unstyled small text-secondary d-flex flex-column gap-2 mb-0">
                        <li><a href="<?= BASE_URL ?>pipe-calculator.php" class="text-secondary text-decoration-none hover-primary"><i class="fa-solid fa-calculator me-1 text-warning"></i> Hydraulic Sizing Calculator</a></li>
                        <li><a href="<?= BASE_URL ?>products.php?stiffness=SN8" class="text-secondary text-decoration-none hover-primary">SN8 Heavy Axle Stiffness Pipes</a></li>
                        <li><a href="<?= BASE_URL ?>faq.php" class="text-secondary text-decoration-none hover-primary">DWC vs RCC Comparison Guide</a></li>
                        <li><a href="<?= BASE_URL ?>contact.php" class="text-secondary text-decoration-none hover-primary">Request Bulk Tender Quote (RFQ)</a></li>
                        <li><a href="<?= BASE_URL ?>orders.php" class="text-secondary text-decoration-none hover-primary">Track Order / GST Invoices</a></li>
                    </ul>
                </div>

                <!-- Plant & Factory Contact -->
                <div class="col-lg-3 col-md-6">
                    <h6 class="text-white fw-bold mb-3">Plant & Headquarters</h6>
                    <ul class="list-unstyled small text-secondary d-flex flex-column gap-2 mb-3">
                        <li class="d-flex align-items-start gap-2">
                            <i class="fa-solid fa-location-dot text-primary mt-1"></i>
                            <span><?= htmlspecialchars($siteAddress) ?></span>
                        </li>
                        <li class="d-flex align-items-center gap-2">
                            <i class="fa-solid fa-phone text-warning"></i>
                            <span><?= htmlspecialchars($sitePhone) ?></span>
                        </li>
                        <li class="d-flex align-items-center gap-2">
                            <i class="fa-solid fa-envelope text-info"></i>
                            <span><?= htmlspecialchars($siteEmail) ?></span>
                        </li>
                        <li class="d-flex align-items-center gap-2">
                            <i class="fa-solid fa-file-invoice text-success"></i>
                            <span>GSTIN: <strong class="text-white"><?= htmlspecialchars($gstin) ?></strong></span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Bottom Copyright Bar -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center pt-3 small text-secondary">
                <div>
                    &copy; <?= date('Y') ?> <?= htmlspecialchars($siteName) ?>. All Rights Reserved. Double Wall Corrugated HDPE Manufacturing.
                </div>
                <div class="d-flex align-items-center gap-3 mt-2 mt-md-0">
                    <span>Designed for High Performance Infrastructure</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- ========================================================
         MOBILE NATIVE BOTTOM NAVIGATION BAR (4 Primary Tabs)
         ======================================================== -->
    <nav class="mobile-bottom-nav">
        <div class="container d-flex justify-content-around">
            <div class="nav-item">
                <a href="<?= BASE_URL ?>" class="nav-link <?= $currentPage === 'index.php' ? 'active' : '' ?>">
                    <i class="fa-solid fa-house"></i>
                    <span>Home</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="<?= BASE_URL ?>products.php" class="nav-link <?= in_array($currentPage, ['products.php', 'product-detail.php']) ? 'active' : '' ?>">
                    <i class="fa-solid fa-layer-group"></i>
                    <span>Products</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="<?= BASE_URL ?>cart.php" class="nav-link <?= in_array($currentPage, ['cart.php', 'checkout.php', 'order-confirmation.php']) ? 'active' : '' ?>">
                    <i class="fa-solid fa-cart-shopping"></i>
                    <span>Cart</span>
                    <span class="badge rounded-pill bg-danger badge-counter cart-badge-count" style="<?= $cartCount > 0 ? '' : 'display:none;' ?>">
                        <?= $cartCount ?>
                    </span>
                </a>
            </div>
            <div class="nav-item">
                <a href="<?= $user ? BASE_URL . 'profile.php' : BASE_URL . 'login.php' ?>" class="nav-link <?= in_array($currentPage, ['profile.php', 'login.php', 'register.php', 'orders.php']) ? 'active' : '' ?>">
                    <i class="fa-solid fa-user"></i>
                    <span><?= $user ? 'Account' : 'Sign In' ?></span>
                </a>
            </div>
        </div>
    </nav>

    <!-- Bootstrap 5.3 Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Master JavaScript -->
    <script src="<?= ASSETS_URL ?>js/main.js"></script>
</body>
</html>
