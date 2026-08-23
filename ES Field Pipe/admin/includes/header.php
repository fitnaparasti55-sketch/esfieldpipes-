<?php
/**
 * Master Admin Header Template
 * Esfield Pipe Platform - Industrial & Enterprise Management
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

// Access Control - Require Staff Privileges
require_editor_or_admin();

$user = current_user();
$siteName = get_setting('site_name', 'Esfield Pipe');
$siteLogo = get_setting('site_logo', 'assets/images/logo.svg');

// Active route helper
$currentPage = basename($_SERVER['PHP_SELF']);

// Count pending inquiries and orders for badges
try {
    $db = get_db();
    $pendingOrdersCount = $db->query("SELECT COUNT(*) FROM `orders` WHERE `order_status` = 'pending'")->fetchColumn() ?: 0;
    $newInquiriesCount = $db->query("SELECT COUNT(*) FROM `support_inquiries` WHERE `status` = 'new'")->fetchColumn() ?: 0;
} catch (Exception $e) {
    $pendingOrdersCount = 0;
    $newInquiriesCount = 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' | ' : '' ?>ESFIELD Control Center</title>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6.5 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

    <style>
        :root {
            --admin-sidebar-bg: #0b1120;
            --admin-sidebar-hover: #162036;
            --admin-sidebar-active: #ea580c;
            --admin-primary: #ea580c;
            --admin-primary-hover: #c2410c;
            --admin-accent: #0284c7;
            --admin-bg: #f4f6f9;
            --admin-card-bg: #ffffff;
            --admin-text-main: #0f172a;
            --admin-text-muted: #64748b;
            --admin-border: #e2e8f0;
            --sidebar-width: 270px;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background-color: var(--admin-bg);
            color: var(--admin-text-main);
            min-height: 100vh;
            display: flex;
        }

        /* Sidebar Styling */
        .admin-sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background-color: var(--admin-sidebar-bg);
            color: #cbd5e1;
            z-index: 1050;
            overflow-y: auto;
            transition: transform 0.3s ease;
            box-shadow: 4px 0 20px rgba(0,0,0,0.15);
            display: flex;
            flex-direction: column;
        }

        .admin-sidebar::-webkit-scrollbar {
            width: 5px;
        }
        .admin-sidebar::-webkit-scrollbar-thumb {
            background: #1e293b;
            border-radius: 4px;
        }

        .sidebar-brand-box {
            padding: 1.25rem 1.5rem;
            background: #070d19;
            border-bottom: 1px solid rgba(255,255,255,0.07);
        }

        .sidebar-brand-box img {
            max-height: 38px;
            max-width: 100%;
        }

        .sidebar-section-title {
            font-size: 0.68rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #475569;
            padding: 1.2rem 1.5rem 0.4rem 1.5rem;
        }

        .sidebar-nav {
            padding: 0.5rem 0.75rem;
            flex-grow: 1;
        }

        .admin-sidebar .nav-link {
            color: #94a3b8;
            padding: 0.65rem 1rem;
            border-radius: 8px;
            margin-bottom: 2px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 0.88rem;
            font-weight: 500;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .admin-sidebar .nav-link i {
            width: 20px;
            font-size: 1.05rem;
            text-align: center;
            color: #64748b;
            transition: color 0.2s ease;
        }

        .admin-sidebar .nav-link:hover {
            color: #ffffff;
            background-color: var(--admin-sidebar-hover);
        }
        .admin-sidebar .nav-link:hover i {
            color: var(--admin-primary);
        }

        .admin-sidebar .nav-link.active {
            color: #ffffff;
            background-color: var(--admin-sidebar-active);
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(234, 88, 12, 0.35);
        }
        .admin-sidebar .nav-link.active i {
            color: #ffffff;
        }

        /* Layout & Main Area */
        .admin-wrapper {
            margin-left: var(--sidebar-width);
            flex-grow: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .admin-topbar {
            background-color: #ffffff;
            border-bottom: 1px solid var(--admin-border);
            padding: 0.75rem 2rem;
            position: sticky;
            top: 0;
            z-index: 1020;
            box-shadow: 0 1px 3px rgba(0,0,0,0.03);
        }

        .admin-content {
            padding: 2rem;
            flex-grow: 1;
        }

        /* Cards & Components */
        .card {
            background: var(--admin-card-bg);
            border: 1px solid var(--admin-border);
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04), 0 1px 2px rgba(0,0,0,0.02);
            transition: box-shadow 0.2s ease;
        }

        .card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
        }

        .stat-card {
            padding: 1.5rem;
            border-radius: 12px;
        }

        .stat-icon {
            width: 52px;
            height: 52px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            flex-shrink: 0;
        }

        .btn-primary {
            background-color: var(--admin-primary);
            border-color: var(--admin-primary);
            color: #ffffff;
            font-weight: 600;
            box-shadow: 0 2px 4px rgba(234, 88, 12, 0.25);
        }
        .btn-primary:hover, .btn-primary:focus {
            background-color: var(--admin-primary-hover);
            border-color: var(--admin-primary-hover);
            box-shadow: 0 4px 8px rgba(234, 88, 12, 0.35);
        }

        .table > :not(caption) > * > * {
            padding: 0.85rem 1rem;
            border-bottom-color: var(--admin-border);
        }

        /* Responsive Mobile Layout */
        .sidebar-backdrop {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 1040;
        }

        @media (max-width: 991.98px) {
            .admin-sidebar {
                transform: translateX(-100%);
            }
            .admin-sidebar.show {
                transform: translateX(0);
            }
            .sidebar-backdrop.show {
                display: block;
            }
            .admin-wrapper {
                margin-left: 0;
            }
            .admin-topbar {
                padding: 0.75rem 1rem;
            }
            .admin-content {
                padding: 1rem;
            }
        }
    </style>
</head>
<body>

<!-- Mobile Sidebar Backdrop -->
<div class="sidebar-backdrop" id="sidebarBackdrop" onclick="toggleSidebar()"></div>

<!-- ========================================================
     ADMIN SIDEBAR NAVIGATION
     ======================================================== -->
<aside class="admin-sidebar" id="adminSidebar">
    <div class="sidebar-brand-box d-flex align-items-center justify-content-between">
        <a href="dashboard.php" class="d-flex align-items-center gap-2 text-decoration-none">
            <img src="<?= BASE_URL . $siteLogo ?>" alt="ESFIELD">
        </a>
        <button class="btn btn-sm btn-link text-white d-lg-none p-0" onclick="toggleSidebar()">
            <i class="fa-solid fa-xmark fs-5"></i>
        </button>
    </div>

    <div class="px-3 py-2 text-warning small fw-bold border-bottom border-secondary border-opacity-25 d-flex align-items-center gap-2">
        <i class="fa-solid fa-shield-halved"></i>
        <span>ADMIN CONTROL PANEL</span>
    </div>

    <nav class="sidebar-nav">
        <!-- MAIN -->
        <div class="sidebar-section-title">Overview</div>
        <a href="dashboard.php" class="nav-link <?= $currentPage === 'dashboard.php' ? 'active' : '' ?>">
            <i class="fa-solid fa-gauge-high"></i>
            <span>Dashboard</span>
        </a>

        <!-- CATALOG MANAGEMENT -->
        <div class="sidebar-section-title">Catalog Management</div>
        <a href="products.php" class="nav-link <?= in_array($currentPage, ['products.php', 'product-add.php', 'product-edit.php']) ? 'active' : '' ?>">
            <i class="fa-solid fa-boxes-stacked"></i>
            <span>Products Catalog</span>
        </a>
        <a href="categories.php" class="nav-link <?= $currentPage === 'categories.php' ? 'active' : '' ?>">
            <i class="fa-solid fa-folder-tree"></i>
            <span>Categories</span>
        </a>
        <a href="media.php" class="nav-link <?= $currentPage === 'media.php' ? 'active' : '' ?>">
            <i class="fa-solid fa-images"></i>
            <span>Media / Images</span>
        </a>

        <!-- SALES & CUSTOMER INQUIRIES -->
        <div class="sidebar-section-title">Sales & CRM</div>
        <a href="orders.php" class="nav-link <?= in_array($currentPage, ['orders.php', 'order-detail.php']) ? 'active' : '' ?>">
            <i class="fa-solid fa-cart-shopping"></i>
            <span>Orders & Invoices</span>
            <?php if ($pendingOrdersCount > 0): ?>
                <span class="badge bg-danger rounded-pill ms-auto"><?= $pendingOrdersCount ?></span>
            <?php endif; ?>
        </a>
        <a href="inquiries.php" class="nav-link <?= in_array($currentPage, ['inquiries.php', 'inquiry-detail.php']) ? 'active' : '' ?>">
            <i class="fa-solid fa-envelope-open-text"></i>
            <span>Inquiries / Quotes</span>
            <?php if ($newInquiriesCount > 0): ?>
                <span class="badge bg-warning text-dark rounded-pill ms-auto"><?= $newInquiriesCount ?></span>
            <?php endif; ?>
        </a>
        <a href="users.php" class="nav-link <?= in_array($currentPage, ['users.php', 'user-edit.php']) ? 'active' : '' ?>">
            <i class="fa-solid fa-users"></i>
            <span>Users & Accounts</span>
        </a>

        <!-- SITE BUILDER & CUSTOMIZATION -->
        <div class="sidebar-section-title">Customization</div>
        <a href="homepage.php" class="nav-link <?= $currentPage === 'homepage.php' ? 'active' : '' ?>">
            <i class="fa-solid fa-house-laptop"></i>
            <span>Homepage Editor</span>
        </a>
        <a href="appearance.php" class="nav-link <?= $currentPage === 'appearance.php' ? 'active' : '' ?>">
            <i class="fa-solid fa-palette"></i>
            <span>Appearance & Theme</span>
        </a>
        <a href="logo.php" class="nav-link <?= $currentPage === 'logo.php' ? 'active' : '' ?>">
            <i class="fa-solid fa-icons"></i>
            <span>Website Logo & Favicon</span>
        </a>
        <a href="company.php" class="nav-link <?= $currentPage === 'company.php' ? 'active' : '' ?>">
            <i class="fa-solid fa-building"></i>
            <span>Company Information</span>
        </a>
        <a href="navigation.php" class="nav-link <?= $currentPage === 'navigation.php' ? 'active' : '' ?>">
            <i class="fa-solid fa-bars"></i>
            <span>Navigation Menu</span>
        </a>
        <a href="seo.php" class="nav-link <?= $currentPage === 'seo.php' ? 'active' : '' ?>">
            <i class="fa-solid fa-magnifying-glass-chart"></i>
            <span>SEO Configuration</span>
        </a>

        <!-- SYSTEM & SETTINGS -->
        <div class="sidebar-section-title">Configuration</div>
        <a href="settings.php" class="nav-link <?= $currentPage === 'settings.php' ? 'active' : '' ?>">
            <i class="fa-solid fa-sliders"></i>
            <span>Site Settings</span>
        </a>
        <a href="profile.php" class="nav-link <?= $currentPage === 'profile.php' ? 'active' : '' ?>">
            <i class="fa-solid fa-user-shield"></i>
            <span>Admin Profile</span>
        </a>
        <a href="<?= BASE_URL ?>logout.php" class="nav-link text-danger mt-2">
            <i class="fa-solid fa-arrow-right-from-bracket text-danger"></i>
            <span>Sign Out</span>
        </a>
    </nav>
</aside>

<!-- ========================================================
     MAIN ADMIN WRAPPER
     ======================================================== -->
<div class="admin-wrapper">
    <!-- Top Navbar -->
    <header class="admin-topbar d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-outline-secondary btn-sm d-lg-none" type="button" onclick="toggleSidebar()">
                <i class="fa-solid fa-bars"></i>
            </button>
            <div class="d-flex align-items-center gap-2">
                <span class="text-muted small d-none d-md-inline">ESFIELD /</span>
                <h5 class="mb-0 fw-bold text-dark"><?= isset($pageTitle) ? htmlspecialchars($pageTitle) : ucfirst(str_replace(['.php', '-'], ['', ' '], $currentPage)) ?></h5>
            </div>
        </div>

        <div class="d-flex align-items-center gap-3">
            <a href="<?= BASE_URL ?>" target="_blank" class="btn btn-sm btn-outline-primary fw-semibold">
                <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> <span class="d-none d-sm-inline">Live Website</span>
            </a>

            <!-- User Menu -->
            <div class="dropdown">
                <button class="btn btn-light border d-flex align-items-center gap-2 py-1.5 px-3 rounded-pill" type="button" data-bs-toggle="dropdown">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 28px; height: 28px; font-size: 0.75rem;">
                        <?= strtoupper(substr($user['name'] ?? 'A', 0, 1)) ?>
                    </div>
                    <div class="text-start d-none d-md-block" style="line-height: 1.1;">
                        <span class="fw-bold d-block small"><?= htmlspecialchars(explode(' ', $user['name'] ?? 'Admin')[0]) ?></span>
                        <span class="text-muted" style="font-size: 0.65rem;"><?= strtoupper($user['role'] ?? 'ADMIN') ?></span>
                    </div>
                    <i class="fa-solid fa-chevron-down text-muted small ms-1"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3 mt-2">
                    <li class="px-3 py-2 border-bottom">
                        <div class="fw-bold small"><?= htmlspecialchars($user['name'] ?? 'Administrator') ?></div>
                        <div class="text-muted" style="font-size: 0.75rem;"><?= htmlspecialchars($user['email'] ?? '') ?></div>
                    </li>
                    <li><a class="dropdown-item py-2" href="profile.php"><i class="fa-solid fa-user-gear me-2 text-primary"></i> Edit Profile</a></li>
                    <li><a class="dropdown-item py-2" href="settings.php"><i class="fa-solid fa-sliders me-2 text-secondary"></i> Site Settings</a></li>
                    <li><a class="dropdown-item py-2" href="<?= BASE_URL ?>" target="_blank"><i class="fa-solid fa-globe me-2 text-success"></i> Visit Storefront</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item py-2 text-danger fw-semibold" href="<?= BASE_URL ?>logout.php"><i class="fa-solid fa-arrow-right-from-bracket me-2"></i> Log Out</a></li>
                </ul>
            </div>
        </div>
    </header>

    <!-- Main Workspace Container -->
    <main class="admin-content">
        <?= render_flash() ?>
