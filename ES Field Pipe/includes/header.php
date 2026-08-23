<?php
/**
 * Master Header Template (Desktop + Mobile Native App Experience)
 * Esfield Pipe Platform - Dynamic Theme & Centralized Configuration
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

$cartCount = get_cart_count();
$user = current_user();
$settings = get_settings();

// Dynamic Company & Branding
$siteName = $settings['site_name'] ?? 'Esfield Pipe Pvt. Ltd.';
$sitePhone = $settings['site_phone'] ?? '+91 98765 43210';
$siteEmail = $settings['site_email'] ?? 'sales@esfieldpipe.com';
$bisInfo = $settings['bis_info'] ?? 'BIS IS:16098 (Part-2) Certified Manufacturer';
$siteLogo = $settings['site_logo'] ?? 'assets/images/logo.svg';
$siteLogoMobile = $settings['site_logo_mobile'] ?? 'assets/images/logo.svg';
$siteFavicon = $settings['site_favicon'] ?? 'assets/images/logo.svg';

// Dynamic SEO
$metaTitle = isset($pageTitle) ? $pageTitle . ' | ' . ($settings['meta_title'] ?? $siteName) : ($settings['meta_title'] ?? $siteName);
$metaDesc = $pageDescription ?? ($settings['meta_description'] ?? 'Premier manufacturer & supplier of Double Wall Corrugated (DWC) HDPE pipes conforming to IS 16098 Part-2.');
$metaKeywords = $settings['meta_keywords'] ?? 'DWC pipe, HDPE corrugated pipe, IS 16098 Part 2, sewerage pipe';
$ogImage = BASE_URL . ($settings['og_image'] ?? $siteLogo);
$robotsIndexing = $settings['robots_indexing'] ?? 'index, follow';

// Dynamic Appearance & Colors
$primaryColor = $settings['theme_primary_color'] ?? '#ea580c';
$primaryHover = $settings['theme_primary_hover'] ?? '#c2410c';
$secondaryColor = $settings['theme_secondary_color'] ?? '#0284c7';
$secondaryHover = $settings['theme_secondary_hover'] ?? '#0369a1';
$accentColor = $settings['theme_accent_color'] ?? '#06b6d4';
$bgBody = $settings['theme_bg_body'] ?? '#f8fafc';
$textMain = $settings['theme_text_main'] ?? '#0f172a';
$headerBg = $settings['theme_header_bg'] ?? '#ffffff';
$topbarBg = $settings['theme_topbar_bg'] ?? '#0f172a';
$footerBg = $settings['theme_footer_bg'] ?? '#0f172a';
$btnColor = $settings['theme_btn_color'] ?? '#ea580c';
$btnHover = $settings['theme_btn_hover_color'] ?? '#c2410c';
$borderRadius = $settings['theme_border_radius'] ?? '8px';
$fontFamily = $settings['theme_font_family'] ?? 'Inter, sans-serif';

// Dynamic Navigation (1-5)
$navItems = [
    1 => ['label' => $settings['nav_label_1'] ?? 'Home', 'url' => $settings['nav_url_1'] ?? 'index.php'],
    2 => ['label' => $settings['nav_label_2'] ?? 'DWC Pipes Catalog', 'url' => $settings['nav_url_2'] ?? 'products.php'],
    3 => ['label' => $settings['nav_label_3'] ?? 'Pipe Calculator', 'url' => $settings['nav_url_3'] ?? 'pipe-calculator.php'],
    4 => ['label' => $settings['nav_label_4'] ?? 'FAQs', 'url' => $settings['nav_url_4'] ?? 'faq.php'],
    5 => ['label' => $settings['nav_label_5'] ?? 'Request Quote / Contact', 'url' => $settings['nav_url_5'] ?? 'contact.php'],
];

// Fetch categories for navbar dropdown
try {
    $db = get_db();
    $navCategories = $db->query("SELECT * FROM `categories` WHERE `status` = 'active' ORDER BY `display_order` ASC")->fetchAll();
} catch (Exception $e) {
    $navCategories = [];
}

// Active route helper
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <title><?= htmlspecialchars($metaTitle) ?></title>
    <meta name="description" content="<?= htmlspecialchars($metaDesc) ?>">
    <meta name="keywords" content="<?= htmlspecialchars($metaKeywords) ?>">
    <meta name="robots" content="<?= htmlspecialchars($robotsIndexing) ?>">
    <meta name="theme-color" content="<?= htmlspecialchars($topbarBg) ?>">

    <!-- Open Graph Social Tags -->
    <meta property="og:title" content="<?= htmlspecialchars($metaTitle) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($metaDesc) ?>">
    <meta property="og:image" content="<?= htmlspecialchars($ogImage) ?>">
    <meta property="og:type" content="website">

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="<?= BASE_URL . $siteFavicon ?>?v=<?= time() ?>">
    
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Outfit:wght@400;600;700&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6.5 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    
    <!-- Base Stylesheet -->
    <link rel="stylesheet" href="<?= ASSETS_URL ?>css/style.css">

    <!-- Dynamic Theme Customizations (Admin Managed CSS Variables) -->
    <style id="dynamic-theme-vars">
        :root {
            --primary: <?= htmlspecialchars($primaryColor) ?> !important;
            --primary-hover: <?= htmlspecialchars($primaryHover) ?> !important;
            --primary-subtle: rgba(<?= hexdec(substr($primaryColor,1,2)) ?>, <?= hexdec(substr($primaryColor,3,2)) ?>, <?= hexdec(substr($primaryColor,5,2)) ?>, 0.12) !important;
            --secondary: <?= htmlspecialchars($secondaryColor) ?> !important;
            --secondary-hover: <?= htmlspecialchars($secondaryHover) ?> !important;
            --accent-cyan: <?= htmlspecialchars($accentColor) ?> !important;
            --bg-body: <?= htmlspecialchars($bgBody) ?> !important;
            --text-main: <?= htmlspecialchars($textMain) ?> !important;
            --bg-header: <?= htmlspecialchars($headerBg) ?> !important;
            --topbar-bg: <?= htmlspecialchars($topbarBg) ?> !important;
            --border-focus: <?= htmlspecialchars($primaryColor) ?> !important;
        }
        body {
            font-family: <?= $fontFamily ?>, system-ui, sans-serif !important;
        }
        .btn-primary {
            background-color: <?= htmlspecialchars($btnColor) ?> !important;
            border-color: <?= htmlspecialchars($btnColor) ?> !important;
            border-radius: <?= htmlspecialchars($borderRadius) ?> !important;
        }
        .btn-primary:hover, .btn-primary:focus {
            background-color: <?= htmlspecialchars($btnHover) ?> !important;
            border-color: <?= htmlspecialchars($btnHover) ?> !important;
        }
        .card-custom, .card, .form-control, .form-select {
            border-radius: <?= htmlspecialchars($borderRadius) ?> !important;
        }
    </style>
    
    <script>
        window.ESFIELD_BASE_URL = '<?= BASE_URL ?>';
    </script>
</head>
<body>

    <!-- ========================================================
         DESKTOP TOPBAR (Utility Information & Standards)
         ======================================================== -->
    <div class="site-topbar">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-4">
                <span><i class="fa-solid fa-phone me-1 text-warning"></i> <?= htmlspecialchars($sitePhone) ?></span>
                <span><i class="fa-solid fa-envelope me-1 text-warning"></i> <?= htmlspecialchars($siteEmail) ?></span>
                <span class="d-none d-xl-inline"><i class="fa-solid fa-certificate me-1 text-info"></i> <?= htmlspecialchars($bisInfo) ?></span>
            </div>
            <div class="d-flex align-items-center gap-3">
                <a href="<?= BASE_URL ?>pipe-calculator.php" class="text-decoration-none fw-semibold">
                    <i class="fa-solid fa-calculator me-1 text-warning"></i> Sizing Calculator
                </a>
                <span class="text-secondary opacity-50">|</span>
                <button class="btn btn-sm btn-link p-0 text-decoration-none theme-toggle-btn" title="Toggle Dark/Light Mode" aria-label="Toggle Theme">
                    <i class="fa-solid fa-moon text-secondary"></i>
                </button>
                <span class="text-secondary opacity-50">|</span>
                <?php if ($user): ?>
                    <span class="fw-semibold text-light">Hi, <?= htmlspecialchars(explode(' ', $user['name'])[0]) ?></span>
                    <?php if (is_editor_or_admin()): ?>
                        <a href="<?= ADMIN_URL ?>dashboard.php" class="badge bg-warning text-dark text-decoration-none fw-bold">Admin Panel</a>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="<?= BASE_URL ?>login.php" class="fw-semibold"><i class="fa-solid fa-arrow-right-to-bracket me-1"></i> Sign In</a>
                    <span class="text-secondary opacity-50">/</span>
                    <a href="<?= BASE_URL ?>register.php" class="fw-semibold">Sign Up</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- ========================================================
         DESKTOP MAIN NAVBAR
         ======================================================== -->
    <nav class="navbar navbar-expand-lg site-navbar desktop-navbar">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="<?= BASE_URL ?>">
                <img src="<?= BASE_URL . $siteLogo ?>?v=<?= time() ?>" alt="<?= htmlspecialchars($siteName) ?>" class="brand-logo-img">
            </a>

            <div class="collapse navbar-collapse" id="desktopNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
                    <!-- Nav Item 1 -->
                    <li class="nav-item">
                        <a class="nav-link <?= $currentPage === 'index.php' ? 'active' : '' ?>" href="<?= BASE_URL . $navItems[1]['url'] ?>">
                            <?= htmlspecialchars($navItems[1]['label']) ?>
                        </a>
                    </li>

                    <!-- Nav Item 2 (Catalog Dropdown) -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?= in_array($currentPage, ['products.php', 'product-detail.php']) ? 'active' : '' ?>" href="<?= BASE_URL . $navItems[2]['url'] ?>" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <?= htmlspecialchars($navItems[2]['label']) ?>
                        </a>
                        <ul class="dropdown-menu shadow-lg border-0 rounded-3">
                            <li><a class="dropdown-item fw-semibold" href="<?= BASE_URL ?>products.php">All DWC Pipes Catalog</a></li>
                            <?php if (!empty($navCategories)): ?>
                                <li><hr class="dropdown-divider"></li>
                                <?php foreach($navCategories as $nc): ?>
                                    <li>
                                        <a class="dropdown-item" href="<?= BASE_URL ?>products.php?category=<?= urlencode($nc['slug']) ?>">
                                            <i class="<?= htmlspecialchars($nc['icon'] ?: 'fa-solid fa-water-ladder') ?> me-2 text-primary"></i>
                                            <?= htmlspecialchars($nc['name']) ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                    </li>

                    <!-- Nav Item 3 -->
                    <li class="nav-item">
                        <a class="nav-link <?= $currentPage === 'pipe-calculator.php' ? 'active' : '' ?>" href="<?= BASE_URL . $navItems[3]['url'] ?>">
                            <i class="fa-solid fa-calculator text-primary me-1"></i> <?= htmlspecialchars($navItems[3]['label']) ?>
                        </a>
                    </li>

                    <!-- Nav Item 4 -->
                    <li class="nav-item">
                        <a class="nav-link <?= $currentPage === 'faq.php' ? 'active' : '' ?>" href="<?= BASE_URL . $navItems[4]['url'] ?>">
                            <?= htmlspecialchars($navItems[4]['label']) ?>
                        </a>
                    </li>

                    <!-- Nav Item 5 -->
                    <li class="nav-item">
                        <a class="nav-link <?= $currentPage === 'contact.php' ? 'active' : '' ?>" href="<?= BASE_URL . $navItems[5]['url'] ?>">
                            <?= htmlspecialchars($navItems[5]['label']) ?>
                        </a>
                    </li>
                </ul>

                <!-- Live Search Form -->
                <form class="d-flex me-3 position-relative" action="<?= BASE_URL ?>products.php" method="GET" style="width: 240px;">
                    <div class="input-group input-group-sm">
                        <input class="form-control" type="search" name="search" id="desktopSearchInput" placeholder="Search size, e.g. 300mm..." aria-label="Search">
                        <button class="btn btn-outline-primary" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                    </div>
                </form>

                <!-- User & Cart Actions -->
                <div class="d-flex align-items-center gap-3">
                    <a href="<?= BASE_URL ?>cart.php" class="btn btn-outline-primary position-relative px-3 py-2">
                        <i class="fa-solid fa-cart-shopping me-1"></i> Cart
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger cart-badge-count" style="<?= $cartCount > 0 ? '' : 'display:none;' ?>">
                            <?= $cartCount ?>
                        </span>
                    </a>

                    <?php if ($user): ?>
                        <div class="dropdown">
                            <button class="btn btn-primary dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown">
                                <i class="fa-solid fa-user-circle fs-5"></i>
                                <span><?= htmlspecialchars(explode(' ', $user['name'])[0]) ?></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3">
                                <li><a class="dropdown-item fw-semibold" href="<?= BASE_URL ?>profile.php"><i class="fa-solid fa-id-card me-2 text-primary"></i> My Profile</a></li>
                                <li><a class="dropdown-item fw-semibold" href="<?= BASE_URL ?>orders.php"><i class="fa-solid fa-box-open me-2 text-success"></i> My Orders & Invoices</a></li>
                                <?php if (is_editor_or_admin()): ?>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item fw-semibold text-warning" href="<?= ADMIN_URL ?>dashboard.php"><i class="fa-solid fa-gauge-high me-2"></i> Admin Dashboard</a></li>
                                <?php endif; ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger fw-semibold" href="<?= BASE_URL ?>logout.php"><i class="fa-solid fa-arrow-right-from-bracket me-2"></i> Sign Out</a></li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a href="<?= BASE_URL ?>login.php" class="btn btn-primary">
                            <i class="fa-solid fa-user me-1"></i> Sign In
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- ========================================================
         MOBILE NATIVE APP BAR (Top Navigation for Handhelds)
         ======================================================== -->
    <div class="mobile-app-bar align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
            <button class="btn btn-sm btn-outline-secondary border-0 p-1 text-main" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileDrawer" aria-controls="mobileDrawer" aria-label="Open Navigation">
                <i class="fa-solid fa-bars-staggered fs-4"></i>
            </button>
            <a href="<?= BASE_URL ?>" class="d-flex align-items-center">
                <img src="<?= BASE_URL . $siteLogoMobile ?>?v=<?= time() ?>" alt="Esfield" style="height: 30px;">
            </a>
        </div>
        
        <div class="d-flex align-items-center gap-2">
            <button class="btn btn-sm btn-outline-secondary border-0 theme-toggle-btn" aria-label="Toggle Theme">
                <i class="fa-solid fa-moon"></i>
            </button>
            <a href="<?= BASE_URL ?>products.php" class="btn btn-sm btn-outline-secondary border-0 text-main" aria-label="Search Catalog">
                <i class="fa-solid fa-magnifying-glass fs-5"></i>
            </a>
            <a href="<?= BASE_URL ?>cart.php" class="btn btn-sm btn-primary position-relative px-2.5 py-1" aria-label="View Cart">
                <i class="fa-solid fa-cart-shopping"></i>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger cart-badge-count" style="<?= $cartCount > 0 ? '' : 'display:none;' ?>">
                    <?= $cartCount ?>
                </span>
            </a>
        </div>
    </div>

    <!-- ========================================================
         MOBILE OFFCANVAS DRAWER MENU
         ======================================================== -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="mobileDrawer" aria-labelledby="mobileDrawerLabel">
        <div class="offcanvas-header border-bottom">
            <div class="d-flex align-items-center gap-2">
                <img src="<?= BASE_URL . $siteLogoMobile ?>?v=<?= time() ?>" alt="Esfield" style="height: 32px;">
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body p-0">
            <?php if ($user): ?>
                <div class="p-3 bg-subtle border-bottom d-flex align-items-center gap-3">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 44px; height: 44px; font-size: 1.2rem;">
                        <?= strtoupper(substr($user['name'], 0, 1)) ?>
                    </div>
                    <div>
                        <div class="fw-bold"><?= htmlspecialchars($user['name']) ?></div>
                        <div class="small text-muted"><?= htmlspecialchars($user['email']) ?></div>
                    </div>
                </div>
            <?php else: ?>
                <div class="p-3 bg-subtle border-bottom d-flex gap-2">
                    <a href="<?= BASE_URL ?>login.php" class="btn btn-primary btn-sm flex-grow-1">Sign In</a>
                    <a href="<?= BASE_URL ?>register.php" class="btn btn-outline-primary btn-sm flex-grow-1">Sign Up</a>
                </div>
            <?php endif; ?>

            <div class="list-group list-group-flush py-2">
                <a href="<?= BASE_URL ?>" class="list-group-item list-group-item-action d-flex align-items-center gap-3 py-3 border-0">
                    <i class="fa-solid fa-house text-primary fs-5" style="width: 24px;"></i>
                    <span class="fw-semibold"><?= htmlspecialchars($navItems[1]['label']) ?></span>
                </a>
                <a href="<?= BASE_URL ?>products.php" class="list-group-item list-group-item-action d-flex align-items-center gap-3 py-3 border-0">
                    <i class="fa-solid fa-cubes-stacked text-primary fs-5" style="width: 24px;"></i>
                    <span class="fw-semibold"><?= htmlspecialchars($navItems[2]['label']) ?></span>
                </a>
                <a href="<?= BASE_URL ?>pipe-calculator.php" class="list-group-item list-group-item-action d-flex align-items-center gap-3 py-3 border-0">
                    <i class="fa-solid fa-calculator text-warning fs-5" style="width: 24px;"></i>
                    <span class="fw-semibold"><?= htmlspecialchars($navItems[3]['label']) ?></span>
                </a>
                <a href="<?= BASE_URL ?>faq.php" class="list-group-item list-group-item-action d-flex align-items-center gap-3 py-3 border-0">
                    <i class="fa-solid fa-circle-question text-info fs-5" style="width: 24px;"></i>
                    <span class="fw-semibold"><?= htmlspecialchars($navItems[4]['label']) ?></span>
                </a>
                <a href="<?= BASE_URL ?>contact.php" class="list-group-item list-group-item-action d-flex align-items-center gap-3 py-3 border-0">
                    <i class="fa-solid fa-headset text-success fs-5" style="width: 24px;"></i>
                    <span class="fw-semibold"><?= htmlspecialchars($navItems[5]['label']) ?></span>
                </a>

                <?php if ($user): ?>
                    <div class="dropdown-divider my-2"></div>
                    <a href="<?= BASE_URL ?>orders.php" class="list-group-item list-group-item-action d-flex align-items-center gap-3 py-3 border-0">
                        <i class="fa-solid fa-box-open text-success fs-5" style="width: 24px;"></i>
                        <span class="fw-semibold">My Orders & Invoices</span>
                    </a>
                    <a href="<?= BASE_URL ?>profile.php" class="list-group-item list-group-item-action d-flex align-items-center gap-3 py-3 border-0">
                        <i class="fa-solid fa-user-gear text-secondary fs-5" style="width: 24px;"></i>
                        <span class="fw-semibold">Profile Settings</span>
                    </a>
                    <?php if (is_editor_or_admin()): ?>
                        <a href="<?= ADMIN_URL ?>dashboard.php" class="list-group-item list-group-item-action d-flex align-items-center gap-3 py-3 border-0 text-warning">
                            <i class="fa-solid fa-gauge-high fs-5" style="width: 24px;"></i>
                            <span class="fw-bold">Admin Portal</span>
                        </a>
                    <?php endif; ?>
                    <a href="<?= BASE_URL ?>logout.php" class="list-group-item list-group-item-action d-flex align-items-center gap-3 py-3 border-0 text-danger">
                        <i class="fa-solid fa-arrow-right-from-bracket fs-5" style="width: 24px;"></i>
                        <span class="fw-semibold">Sign Out</span>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Main Content Container Wrapper -->
    <main>
        <div class="container mt-3">
            <?= render_flash() ?>
        </div>
