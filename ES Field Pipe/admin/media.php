<?php
/**
 * Media & Images Gallery - Esfield Pipe
 */
$pageTitle = "Media & Assets Library";
require_once __DIR__ . '/includes/header.php';

$db = get_db();

// Fetch all media items
$mediaItems = $db->query("SELECT * FROM `media` ORDER BY `created_at` DESC")->fetchAll();

// Also scan standard asset images for quick access
$assetImages = [
    'assets/images/dwc-cross-section.svg',
    'assets/images/dwc-pipe-100mm.svg',
    'assets/images/dwc-pipe-150mm.svg',
    'assets/images/dwc-pipe-200mm.svg',
    'assets/images/dwc-pipe-300mm.svg',
    'assets/images/dwc-pipe-400mm.svg',
    'assets/images/dwc-pipe-500mm.svg',
    'assets/images/dwc-pipe-600mm.svg',
    'assets/images/dwc-pipe-50mm.svg',
    'assets/images/dwc-pipe-75mm.svg',
    'assets/images/logo.svg'
];

$currentHeroImage = get_setting('home_hero_image', 'assets/images/dwc-cross-section.svg');
$currentCompanyImage = get_setting('home_company_image', 'assets/images/dwc-cross-section.svg');
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h4 class="fw-black mb-1 text-dark">Website Media & Image Library</h4>
        <p class="text-muted small mb-0">Upload and manage promotional banners, hero graphics, pipe cross-sections, and technical media.</p>
    </div>
    <button type="button" class="btn btn-primary px-4 py-2 fw-bold" data-bs-toggle="modal" data-bs-target="#uploadMediaModal">
        <i class="fa-solid fa-cloud-arrow-up me-1"></i> Upload Image / Media
    </button>
</div>

<!-- Uploaded Media Grid -->
<div class="row g-4 mb-5">
    <div class="col-12">
        <h6 class="fw-bold text-dark mb-3"><i class="fa-solid fa-folder-open text-primary me-2"></i> Uploaded Assets Library (<?= count($mediaItems) ?> files)</h6>
    </div>

    <?php if (empty($mediaItems)): ?>
        <div class="col-12">
            <div class="card border-0 shadow-sm text-center py-5">
                <div class="card-body">
                    <i class="fa-solid fa-images fs-1 text-muted opacity-50 mb-3"></i>
                    <h6 class="text-dark fw-bold">No custom media files uploaded yet.</h6>
                    <p class="text-muted small mb-3">Upload your high-res plant photos, project site banners, and technical drawings.</p>
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#uploadMediaModal">
                        <i class="fa-solid fa-upload me-1"></i> Upload First Image
                    </button>
                </div>
            </div>
        </div>
    <?php else: ?>
        <?php foreach($mediaItems as $item): ?>
            <div class="col-sm-6 col-md-4 col-xl-3">
                <div class="card border-0 shadow-sm h-100 overflow-hidden">
                    <div class="bg-light p-3 text-center position-relative d-flex align-items-center justify-content-center" style="height: 180px;">
                        <img src="<?= BASE_URL . $item['file_path'] ?>" alt="<?= htmlspecialchars($item['alt_text']) ?>" style="max-height: 100%; max-width: 100%; object-fit: contain;">
                        <?php if ($item['file_path'] === $currentHeroImage): ?>
                            <span class="position-absolute top-0 start-0 m-2 badge bg-primary">Hero Image</span>
                        <?php endif; ?>
                    </div>
                    <div class="card-body p-3">
                        <div class="fw-bold text-dark text-truncate small mb-1" title="<?= htmlspecialchars($item['original_name']) ?>">
                            <?= htmlspecialchars($item['original_name']) ?>
                        </div>
                        <div class="text-muted small mb-3">
                            <?= round($item['file_size'] / 1024, 1) ?> KB &bull; <?= date('d M Y', strtotime($item['created_at'])) ?>
                        </div>

                        <div class="d-flex gap-1">
                            <button type="button" class="btn btn-sm btn-light border flex-grow-1" onclick="copyUrl('<?= BASE_URL . $item['file_path'] ?>')" title="Copy URL">
                                <i class="fa-solid fa-copy me-1"></i> Copy Link
                            </button>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light border" type="button" data-bs-toggle="dropdown">
                                    <i class="fa-solid fa-ellipsis-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                    <li>
                                        <form action="<?= BASE_URL ?>api/admin-media.php" method="POST">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="action" value="set_as">
                                            <input type="hidden" name="target" value="home_hero_image">
                                            <input type="hidden" name="file_path" value="<?= htmlspecialchars($item['file_path']) ?>">
                                            <button type="submit" class="dropdown-item small"><i class="fa-solid fa-desktop me-2 text-primary"></i> Set as Hero Image</button>
                                        </form>
                                    </li>
                                    <li>
                                        <form action="<?= BASE_URL ?>api/admin-media.php" method="POST">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="action" value="set_as">
                                            <input type="hidden" name="target" value="home_company_image">
                                            <input type="hidden" name="file_path" value="<?= htmlspecialchars($item['file_path']) ?>">
                                            <button type="submit" class="dropdown-item small"><i class="fa-solid fa-building me-2 text-info"></i> Set as Company Image</button>
                                        </form>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="<?= BASE_URL ?>api/admin-media.php" method="POST" onsubmit="return confirm('Delete this media file?');">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="media_id" value="<?= $item['id'] ?>">
                                            <button type="submit" class="dropdown-item text-danger small"><i class="fa-solid fa-trash-can me-2"></i> Delete File</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Built-in System Vector Assets -->
<div class="row g-4">
    <div class="col-12">
        <h6 class="fw-bold text-dark mb-3"><i class="fa-solid fa-cubes text-secondary me-2"></i> Core System Pipe Profiles & Vectors</h6>
    </div>

    <?php foreach($assetImages as $ai): ?>
        <div class="col-6 col-sm-4 col-md-3 col-xl-2">
            <div class="card border-0 shadow-sm h-100 p-2 text-center">
                <div class="p-2 rounded bg-light mb-2 d-flex align-items-center justify-content-center" style="height: 90px; background: #0f172a !important;">
                    <img src="<?= BASE_URL . $ai ?>" alt="Vector" style="max-height: 100%; max-width: 100%;">
                </div>
                <div class="text-truncate small fw-bold mb-2 text-muted" style="font-size: 0.72rem;">
                    <?= basename($ai) ?>
                </div>
                <div class="d-flex gap-1">
                    <button type="button" class="btn btn-xs btn-outline-secondary w-100" style="font-size: 0.7rem;" onclick="copyUrl('<?= $ai ?>')">
                        <i class="fa-solid fa-link"></i> Copy Path
                    </button>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Upload Media Modal -->
<div class="modal fade" id="uploadMediaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form action="<?= BASE_URL ?>api/admin-media.php" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="upload">

                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-dark"><i class="fa-solid fa-cloud-arrow-up text-primary me-2"></i> Upload New Media</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body py-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Choose File (JPG, PNG, WEBP, SVG, PDF - Max 15MB) *</label>
                        <input type="file" name="file" class="form-control" required accept=".jpg,.jpeg,.png,.webp,.svg,.pdf">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Alt Text / Label</label>
                        <input type="text" name="alt_text" class="form-control" placeholder="e.g. 500mm DWC Pipe Site Installation">
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold px-4">Upload File</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function copyUrl(url) {
    navigator.clipboard.writeText(url).then(() => {
        alert('Copied to clipboard: ' + url);
    });
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
