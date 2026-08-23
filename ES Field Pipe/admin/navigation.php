<?php
/**
 * Navigation Menu Configuration - Esfield Pipe
 */
$pageTitle = "Navigation Menu Settings";
require_once __DIR__ . '/includes/header.php';

$settings = get_settings();

$defaultNav = [
    1 => ['label' => 'Home', 'url' => 'index.php', 'default' => 'Home'],
    2 => ['label' => 'DWC Pipes Catalog', 'url' => 'products.php', 'default' => 'DWC Pipes Catalog'],
    3 => ['label' => 'Pipe Calculator', 'url' => 'pipe-calculator.php', 'default' => 'Pipe Calculator'],
    4 => ['label' => 'FAQs', 'url' => 'faq.php', 'default' => 'FAQs'],
    5 => ['label' => 'Request Quote / Contact', 'url' => 'contact.php', 'default' => 'Request Quote / Contact']
];
?>

<div class="row justify-content-center">
    <div class="col-xl-9">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-black mb-1 text-dark">Website Navigation Menu</h4>
                <p class="text-muted small mb-0">Configure top navigation menu labels and destinations rendered across desktop and mobile screens.</p>
            </div>
            <a href="dashboard.php" class="btn btn-outline-secondary btn-sm fw-bold">
                <i class="fa-solid fa-arrow-left me-1"></i> Dashboard
            </a>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4 p-md-5">
                <form action="<?= BASE_URL ?>api/admin-navigation.php" method="POST">
                    <?= csrf_field() ?>

                    <div class="row g-4">
                        <div class="col-12">
                            <h6 class="fw-bold border-bottom pb-2 mb-3 text-primary text-uppercase small tracking-wider">
                                <i class="fa-solid fa-bars me-1"></i> Primary Navbar Links (Order 1 - 5)
                            </h6>
                        </div>

                        <?php for($i = 1; $i <= 5; $i++): 
                            $curLabel = $settings["nav_label_{$i}"] ?? $defaultNav[$i]['label'];
                            $curUrl = $settings["nav_url_{$i}"] ?? $defaultNav[$i]['url'];
                        ?>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Item <?= $i ?> Label</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light fw-bold text-muted"><?= $i ?></span>
                                    <input type="text" name="nav_label_<?= $i ?>" class="form-control" value="<?= htmlspecialchars($curLabel) ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Item <?= $i ?> Link Destination URL</label>
                                <input type="text" name="nav_url_<?= $i ?>" class="form-control font-monospace" value="<?= htmlspecialchars($curUrl) ?>" required>
                            </div>
                        <?php endfor; ?>

                        <div class="col-12 mt-5 pt-3 border-top text-end">
                            <button type="submit" class="btn btn-primary px-5 py-3 fw-bold rounded-3 shadow">
                                <i class="fa-solid fa-floppy-disk me-2"></i> Update Navigation Menu
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
