<?php
/**
 * Visual Appearance & Dynamic CSS Theme Engine - Esfield Pipe
 */
$pageTitle = "Appearance & Visual Theme";
require_once __DIR__ . '/includes/header.php';

$settings = get_settings();

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
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-black mb-1 text-dark">Visual Theme & Appearance Engine</h4>
        <p class="text-muted small mb-0">Modify the website visual styling centrally via CSS variables without editing PHP/CSS source code.</p>
    </div>
    <a href="<?= BASE_URL ?>" target="_blank" class="btn btn-outline-primary btn-sm fw-bold">
        <i class="fa-solid fa-eye me-1"></i> Preview Storefront
    </a>
</div>

<!-- Theme Presets Bar -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <span class="fw-bold small text-dark"><i class="fa-solid fa-wand-magic-sparkles text-primary me-2"></i> Quick Theme Presets:</span>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="applyPreset('#ea580c', '#c2410c', '#0284c7', '#0369a1', '#06b6d4', '#0f172a', '#f8fafc', '#ffffff')">
                    <i class="fa-solid fa-circle text-warning me-1"></i> Industrial Orange (Default)
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="applyPreset('#2563eb', '#1d4ed8', '#0d9488', '#0f766e', '#38bdf8', '#0f172a', '#f8fafc', '#ffffff')">
                    <i class="fa-solid fa-circle text-primary me-1"></i> Infrastructure Blue
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="applyPreset('#059669', '#047857', '#0284c7', '#0369a1', '#10b981', '#064e3b', '#f8fafc', '#ffffff')">
                    <i class="fa-solid fa-circle text-success me-1"></i> Eco Stormwater Green
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="applyPreset('#7c3aed', '#6d28d9', '#ea580c', '#c2410c', '#a855f7', '#1e1b4b', '#f8fafc', '#ffffff')">
                    <i class="fa-solid fa-circle text-purple me-1" style="color: #7c3aed;"></i> High-Tech Violet
                </button>
            </div>
        </div>
    </div>
</div>

<form action="<?= BASE_URL ?>api/admin-appearance.php" method="POST">
    <?= csrf_field() ?>

    <div class="row g-4">
        <!-- Theme Controls -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3 px-4">
                    <h5 class="fw-bold mb-0 text-primary"><i class="fa-solid fa-palette me-2"></i> Brand Color Palette</h5>
                </div>
                <div class="card-body p-4 border-top">
                    <div class="row g-3">
                        <!-- Primary Color -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Primary Brand Color (Buttons & Highlights)</label>
                            <div class="input-group">
                                <input type="color" id="primaryColorPicker" class="form-control form-control-color" value="<?= htmlspecialchars($primaryColor) ?>" oninput="syncColor('primaryColor', this.value)">
                                <input type="text" name="theme_primary_color" id="primaryColorInput" class="form-control font-monospace" value="<?= htmlspecialchars($primaryColor) ?>" oninput="syncPicker('primaryColor', this.value)">
                            </div>
                        </div>

                        <!-- Primary Hover -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Primary Hover State</label>
                            <div class="input-group">
                                <input type="color" id="primaryHoverPicker" class="form-control form-control-color" value="<?= htmlspecialchars($primaryHover) ?>" oninput="syncColor('primaryHover', this.value)">
                                <input type="text" name="theme_primary_hover" id="primaryHoverInput" class="form-control font-monospace" value="<?= htmlspecialchars($primaryHover) ?>" oninput="syncPicker('primaryHover', this.value)">
                            </div>
                        </div>

                        <!-- Secondary Color -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Secondary Action Color</label>
                            <div class="input-group">
                                <input type="color" id="secondaryColorPicker" class="form-control form-control-color" value="<?= htmlspecialchars($secondaryColor) ?>" oninput="syncColor('secondaryColor', this.value)">
                                <input type="text" name="theme_secondary_color" id="secondaryColorInput" class="form-control font-monospace" value="<?= htmlspecialchars($secondaryColor) ?>" oninput="syncPicker('secondaryColor', this.value)">
                            </div>
                        </div>

                        <!-- Accent Color -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Cyan / Highlights Accent</label>
                            <div class="input-group">
                                <input type="color" id="accentColorPicker" class="form-control form-control-color" value="<?= htmlspecialchars($accentColor) ?>" oninput="syncColor('accentColor', this.value)">
                                <input type="text" name="theme_accent_color" id="accentColorInput" class="form-control font-monospace" value="<?= htmlspecialchars($accentColor) ?>" oninput="syncPicker('accentColor', this.value)">
                            </div>
                        </div>

                        <!-- Topbar & Footer Background -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Topbar & Footer Dark Background</label>
                            <div class="input-group">
                                <input type="color" id="topbarBgPicker" class="form-control form-control-color" value="<?= htmlspecialchars($topbarBg) ?>" oninput="syncColor('topbarBg', this.value)">
                                <input type="text" name="theme_topbar_bg" id="topbarBgInput" class="form-control font-monospace" value="<?= htmlspecialchars($topbarBg) ?>" oninput="syncPicker('topbarBg', this.value)">
                            </div>
                        </div>

                        <!-- Header Navbar Background -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Main Desktop Navbar Background</label>
                            <div class="input-group">
                                <input type="color" id="headerBgPicker" class="form-control form-control-color" value="<?= htmlspecialchars($headerBg) ?>" oninput="syncColor('headerBg', this.value)">
                                <input type="text" name="theme_header_bg" id="headerBgInput" class="form-control font-monospace" value="<?= htmlspecialchars($headerBg) ?>" oninput="syncPicker('headerBg', this.value)">
                            </div>
                        </div>

                        <!-- Background & Text -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Body Background (Light)</label>
                            <div class="input-group">
                                <input type="color" id="bgBodyPicker" class="form-control form-control-color" value="<?= htmlspecialchars($bgBody) ?>" oninput="syncColor('bgBody', this.value)">
                                <input type="text" name="theme_bg_body" id="bgBodyInput" class="form-control font-monospace" value="<?= htmlspecialchars($bgBody) ?>" oninput="syncPicker('bgBody', this.value)">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Main Typography Color</label>
                            <div class="input-group">
                                <input type="color" id="textMainPicker" class="form-control form-control-color" value="<?= htmlspecialchars($textMain) ?>" oninput="syncColor('textMain', this.value)">
                                <input type="text" name="theme_text_main" id="textMainInput" class="form-control font-monospace" value="<?= htmlspecialchars($textMain) ?>" oninput="syncPicker('textMain', this.value)">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Typography & Borders -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3 px-4">
                    <h5 class="fw-bold mb-0 text-primary"><i class="fa-solid fa-shapes me-2"></i> Typography & Component Radius</h5>
                </div>
                <div class="card-body p-4 border-top">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Corner Border Radius</label>
                            <select name="theme_border_radius" id="borderRadiusSelect" class="form-select" onchange="updateLivePreview()">
                                <option value="4px" <?= $borderRadius === '4px' ? 'selected' : '' ?>>4px (Sharp Engineering)</option>
                                <option value="8px" <?= $borderRadius === '8px' ? 'selected' : '' ?>>8px (Standard Industrial)</option>
                                <option value="12px" <?= $borderRadius === '12px' ? 'selected' : '' ?>>12px (Smooth Modern)</option>
                                <option value="16px" <?= $borderRadius === '16px' ? 'selected' : '' ?>>16px (Pill Soft)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Primary Font Family</label>
                            <select name="theme_font_family" id="fontFamilySelect" class="form-select" onchange="updateLivePreview()">
                                <option value="'Inter', sans-serif" <?= strpos($fontFamily, 'Inter') !== false ? 'selected' : '' ?>>Inter (Recommended High-Tech)</option>
                                <option value="'Roboto', sans-serif" <?= strpos($fontFamily, 'Roboto') !== false ? 'selected' : '' ?>>Roboto</option>
                                <option value="'Outfit', sans-serif" <?= strpos($fontFamily, 'Outfit') !== false ? 'selected' : '' ?>>Outfit Modern</option>
                                <option value="system-ui, sans-serif" <?= strpos($fontFamily, 'system-ui') !== false ? 'selected' : '' ?>>System Default</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Live Preview Pane -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm sticky-top" style="top: 90px;">
                <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-eye text-primary me-2"></i> Live Theme Preview</h6>
                    <span class="badge bg-success-subtle text-success">Real-time Reactive</span>
                </div>
                <div class="card-body p-4 border-top" id="previewContainer" style="background: <?= htmlspecialchars($bgBody) ?>; color: <?= htmlspecialchars($textMain) ?>; font-family: <?= htmlspecialchars($fontFamily) ?>;">
                    <!-- Simulated Navbar -->
                    <div class="p-3 mb-3 border rounded shadow-sm d-flex justify-content-between align-items-center" id="previewNavbar" style="background: <?= htmlspecialchars($headerBg) ?>; border-radius: <?= htmlspecialchars($borderRadius) ?>;">
                        <span class="fw-bold fs-6" id="previewBrand">ESFIELD PIPE</span>
                        <div class="d-flex gap-2">
                            <span class="badge bg-light text-dark border">Catalog</span>
                            <span class="badge bg-light text-dark border">Calculator</span>
                        </div>
                    </div>

                    <!-- Simulated Hero Card -->
                    <div class="p-4 mb-3 rounded text-white shadow" id="previewHero" style="background: <?= htmlspecialchars($topbarBg) ?>; border-radius: <?= htmlspecialchars($borderRadius) ?>;">
                        <span class="badge mb-2 px-2.5 py-1" id="previewBadge" style="background: <?= htmlspecialchars($accentColor) ?>; color: #fff;">BIS CERTIFIED</span>
                        <h5 class="fw-bold mb-2">High-Flow DWC HDPE Piping</h5>
                        <p class="small opacity-75 mb-3">Structured wall piping engineered for heavy dynamic axle load.</p>
                        <button type="button" class="btn btn-sm text-white fw-bold px-3 py-2" id="previewBtn" style="background: <?= htmlspecialchars($primaryColor) ?>; border-radius: <?= htmlspecialchars($borderRadius) ?>;">
                            Explore Products &rarr;
                        </button>
                    </div>

                    <!-- Simulated Product Card -->
                    <div class="p-3 border rounded bg-white shadow-sm" id="previewCard" style="border-radius: <?= htmlspecialchars($borderRadius) ?>;">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h6 class="fw-bold mb-0" id="previewTitle">300mm SN8 Corrugated Pipe</h6>
                                <small class="text-muted">IS 16098 Part 2</small>
                            </div>
                            <span class="badge fw-bold" id="previewTag" style="background: <?= htmlspecialchars($primaryColor) ?>; color: #fff;">₹ 1,150 / m</span>
                        </div>
                        <div class="d-flex gap-2 mt-3">
                            <button type="button" class="btn btn-sm w-100 fw-bold text-white" id="previewCartBtn" style="background: <?= htmlspecialchars($primaryColor) ?>; border-radius: <?= htmlspecialchars($borderRadius) ?>;">
                                <i class="fa-solid fa-cart-shopping me-1"></i> Add to Cart
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary px-3" style="border-radius: <?= htmlspecialchars($borderRadius) ?>;">
                                Details
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white border-0 p-3 text-end">
                    <button type="submit" class="btn btn-primary px-4 py-2.5 fw-bold w-100 rounded-3 shadow">
                        <i class="fa-solid fa-floppy-disk me-2"></i> Save Theme Variables
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
function syncColor(id, value) {
    document.getElementById(id + 'Input').value = value;
    updateLivePreview();
}

function syncPicker(id, value) {
    if (/^#[0-9A-F]{6}$/i.test(value)) {
        document.getElementById(id + 'Picker').value = value;
        updateLivePreview();
    }
}

function applyPreset(primary, hover, sec, secHover, accent, topbar, bg, header) {
    document.getElementById('primaryColorInput').value = primary;
    document.getElementById('primaryColorPicker').value = primary;
    document.getElementById('primaryHoverInput').value = hover;
    document.getElementById('primaryHoverPicker').value = hover;
    document.getElementById('secondaryColorInput').value = sec;
    document.getElementById('secondaryColorPicker').value = sec;
    document.getElementById('accentColorInput').value = accent;
    document.getElementById('accentColorPicker').value = accent;
    document.getElementById('topbarBgInput').value = topbar;
    document.getElementById('topbarBgPicker').value = topbar;
    document.getElementById('bgBodyInput').value = bg;
    document.getElementById('bgBodyPicker').value = bg;
    document.getElementById('headerBgInput').value = header;
    document.getElementById('headerBgPicker').value = header;
    updateLivePreview();
}

function updateLivePreview() {
    const primary = document.getElementById('primaryColorInput').value;
    const accent = document.getElementById('accentColorInput').value;
    const topbar = document.getElementById('topbarBgInput').value;
    const header = document.getElementById('headerBgInput').value;
    const bgBody = document.getElementById('bgBodyInput').value;
    const textMain = document.getElementById('textMainInput').value;
    const radius = document.getElementById('borderRadiusSelect').value;
    const font = document.getElementById('fontFamilySelect').value;

    const container = document.getElementById('previewContainer');
    container.style.backgroundColor = bgBody;
    container.style.color = textMain;
    container.style.fontFamily = font;

    document.getElementById('previewNavbar').style.backgroundColor = header;
    document.getElementById('previewNavbar').style.borderRadius = radius;

    document.getElementById('previewHero').style.backgroundColor = topbar;
    document.getElementById('previewHero').style.borderRadius = radius;

    document.getElementById('previewBadge').style.backgroundColor = accent;

    document.getElementById('previewBtn').style.backgroundColor = primary;
    document.getElementById('previewBtn').style.borderRadius = radius;

    document.getElementById('previewTag').style.backgroundColor = primary;
    document.getElementById('previewCartBtn').style.backgroundColor = primary;
    document.getElementById('previewCartBtn').style.borderRadius = radius;
    document.getElementById('previewCard').style.borderRadius = radius;
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
