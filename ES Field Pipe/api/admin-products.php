<?php
/**
 * Admin Product Action Handler
 * Esfield Pipe Platform - Product CRUD & Media Handling
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

// Access Control
require_editor_or_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . ADMIN_URL . 'products.php');
    exit;
}

// CSRF Verification
if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
    set_flash('danger', 'Security verification failed.');
    header('Location: ' . ADMIN_URL . 'products.php');
    exit;
}

$action = $_POST['action'] ?? '';
$db = get_db();

// Directory for product image uploads
$uploadDir = __DIR__ . '/../assets/uploads/products/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

try {
    // ==========================================
    // 1. ADD NEW PRODUCT
    // ==========================================
    if ($action === 'add') {
        $name = trim($_POST['name'] ?? '');
        $categoryId = (int)($_POST['category_id'] ?? 1);
        $slug = trim($_POST['slug'] ?? '');
        if (empty($slug)) {
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-'));
        }
        
        // Ensure slug is unique
        $slugCheck = $db->prepare("SELECT id FROM `products` WHERE `slug` = ?");
        $slugCheck->execute([$slug]);
        if ($slugCheck->fetch()) {
            $slug .= '-' . rand(100, 999);
        }

        $shortDesc = trim($_POST['short_desc'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $innerDia = (int)($_POST['inner_diameter_mm'] ?? 100);
        $outerDia = (int)($_POST['outer_diameter_mm'] ?? 120);
        $standardLength = (float)($_POST['standard_length_m'] ?? 6.00);
        $stiffness = in_array($_POST['stiffness_class'] ?? '', ['SN4', 'SN8']) ? $_POST['stiffness_class'] : 'SN8';
        $rawMaterial = trim($_POST['raw_material'] ?? 'PE-100 Virgin Grade HDPE');
        $standardCompliance = trim($_POST['standard_compliance'] ?? 'IS 16098 (Part 2) / EN 13476');
        $jointingMethod = trim($_POST['jointing_method'] ?? 'Coupler with EPDM Rubber Seal');
        $applicationType = trim($_POST['application_type'] ?? 'Underground Gravity Drainage');
        $pricePerMeter = (float)($_POST['price_per_meter'] ?? 0);
        $pricePerPipe = (float)($_POST['price_per_pipe'] ?? ($pricePerMeter * $standardLength));
        $stockQuantity = (int)($_POST['stock_quantity'] ?? 100);
        $minOrderQty = (int)($_POST['min_order_qty'] ?? 1);
        $featured = !empty($_POST['featured']) ? 1 : 0;
        $status = ($_POST['status'] ?? 'active') === 'inactive' ? 'inactive' : 'active';

        // Handle Image Upload
        $imagePath = 'assets/images/dwc-pipe-100mm.svg'; // Fallback
        if (!empty($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadRes = handle_file_upload($_FILES['image'], $uploadDir, ['jpg', 'jpeg', 'png', 'webp', 'svg'], 10);
            if ($uploadRes['success']) {
                $imagePath = 'assets/uploads/products/' . $uploadRes['filename'];
            } else {
                set_flash('danger', 'Image upload failed: ' . $uploadRes['error']);
                header('Location: ' . ADMIN_URL . 'product-add.php');
                exit;
            }
        }

        $stmt = $db->prepare("
            INSERT INTO `products` (
                `category_id`, `name`, `slug`, `short_desc`, `description`,
                `inner_diameter_mm`, `outer_diameter_mm`, `standard_length_m`, `stiffness_class`,
                `raw_material`, `standard_compliance`, `jointing_method`, `application_type`,
                `price_per_meter`, `price_per_pipe`, `stock_quantity`, `min_order_qty`,
                `featured`, `image`, `status`
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $categoryId, $name, $slug, $shortDesc, $description,
            $innerDia, $outerDia, $standardLength, $stiffness,
            $rawMaterial, $standardCompliance, $jointingMethod, $applicationType,
            $pricePerMeter, $pricePerPipe, $stockQuantity, $minOrderQty,
            $featured, $imagePath, $status
        ]);

        set_flash('success', "Product '{$name}' created successfully in catalog.");
        header('Location: ' . ADMIN_URL . 'products.php');
        exit;
    }

    // ==========================================
    // 2. EDIT PRODUCT
    // ==========================================
    if ($action === 'edit') {
        $productId = (int)($_POST['product_id'] ?? 0);
        $stmt = $db->prepare("SELECT * FROM `products` WHERE `id` = ?");
        $stmt->execute([$productId]);
        $product = $stmt->fetch();

        if (!$product) {
            set_flash('danger', 'Product not found.');
            header('Location: ' . ADMIN_URL . 'products.php');
            exit;
        }

        $name = trim($_POST['name'] ?? '');
        $categoryId = (int)($_POST['category_id'] ?? 1);
        $slug = trim($_POST['slug'] ?? '');
        if (empty($slug)) {
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-'));
        }

        $shortDesc = trim($_POST['short_desc'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $innerDia = (int)($_POST['inner_diameter_mm'] ?? 100);
        $outerDia = (int)($_POST['outer_diameter_mm'] ?? 120);
        $standardLength = (float)($_POST['standard_length_m'] ?? 6.00);
        $stiffness = in_array($_POST['stiffness_class'] ?? '', ['SN4', 'SN8']) ? $_POST['stiffness_class'] : 'SN8';
        $rawMaterial = trim($_POST['raw_material'] ?? 'PE-100 Virgin Grade HDPE');
        $standardCompliance = trim($_POST['standard_compliance'] ?? 'IS 16098 (Part 2) / EN 13476');
        $jointingMethod = trim($_POST['jointing_method'] ?? 'Coupler with EPDM Rubber Seal');
        $applicationType = trim($_POST['application_type'] ?? 'Underground Gravity Drainage');
        $pricePerMeter = (float)($_POST['price_per_meter'] ?? 0);
        $pricePerPipe = (float)($_POST['price_per_pipe'] ?? ($pricePerMeter * $standardLength));
        $stockQuantity = (int)($_POST['stock_quantity'] ?? 100);
        $minOrderQty = (int)($_POST['min_order_qty'] ?? 1);
        $featured = !empty($_POST['featured']) ? 1 : 0;
        $status = ($_POST['status'] ?? 'active') === 'inactive' ? 'inactive' : 'active';

        $imagePath = $product['image'];

        // Handle Image Replacement if new file provided
        if (!empty($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadRes = handle_file_upload($_FILES['image'], $uploadDir, ['jpg', 'jpeg', 'png', 'webp', 'svg'], 10);
            if ($uploadRes['success']) {
                // Remove old custom upload image safely if stored in assets/uploads/
                if (!empty($product['image']) && strpos($product['image'], 'assets/uploads/') === 0) {
                    $oldFilePath = __DIR__ . '/../' . $product['image'];
                    if (file_exists($oldFilePath)) {
                        @unlink($oldFilePath);
                    }
                }
                $imagePath = 'assets/uploads/products/' . $uploadRes['filename'];
            }
        }

        $update = $db->prepare("
            UPDATE `products` SET
                `category_id` = ?, `name` = ?, `slug` = ?, `short_desc` = ?, `description` = ?,
                `inner_diameter_mm` = ?, `outer_diameter_mm` = ?, `standard_length_m` = ?, `stiffness_class` = ?,
                `raw_material` = ?, `standard_compliance` = ?, `jointing_method` = ?, `application_type` = ?,
                `price_per_meter` = ?, `price_per_pipe` = ?, `stock_quantity` = ?, `min_order_qty` = ?,
                `featured` = ?, `image` = ?, `status` = ?
            WHERE `id` = ?
        ");

        $update->execute([
            $categoryId, $name, $slug, $shortDesc, $description,
            $innerDia, $outerDia, $standardLength, $stiffness,
            $rawMaterial, $standardCompliance, $jointingMethod, $applicationType,
            $pricePerMeter, $pricePerPipe, $stockQuantity, $minOrderQty,
            $featured, $imagePath, $status, $productId
        ]);

        set_flash('success', "Product '{$name}' updated successfully.");
        header('Location: ' . ADMIN_URL . 'products.php');
        exit;
    }

    // ==========================================
    // 3. DELETE PRODUCT
    // ==========================================
    if ($action === 'delete') {
        $productId = (int)($_POST['product_id'] ?? 0);
        $stmt = $db->prepare("SELECT * FROM `products` WHERE `id` = ?");
        $stmt->execute([$productId]);
        $product = $stmt->fetch();

        if ($product) {
            // Remove uploaded image safely
            if (!empty($product['image']) && strpos($product['image'], 'assets/uploads/') === 0) {
                $oldFilePath = __DIR__ . '/../' . $product['image'];
                if (file_exists($oldFilePath)) {
                    @unlink($oldFilePath);
                }
            }

            $delStmt = $db->prepare("DELETE FROM `products` WHERE `id` = ?");
            $delStmt->execute([$productId]);
            set_flash('success', "Product '{$product['name']}' deleted from catalog.");
        } else {
            set_flash('danger', 'Product not found.');
        }

        header('Location: ' . ADMIN_URL . 'products.php');
        exit;
    }

    // ==========================================
    // 4. TOGGLE STATUS / FEATURED
    // ==========================================
    if ($action === 'toggle_status') {
        $productId = (int)($_POST['product_id'] ?? 0);
        $stmt = $db->prepare("UPDATE `products` SET `status` = IF(`status`='active', 'inactive', 'active') WHERE `id` = ?");
        $stmt->execute([$productId]);
        set_flash('success', 'Product visibility updated.');
        header('Location: ' . ADMIN_URL . 'products.php');
        exit;
    }

    if ($action === 'toggle_featured') {
        $productId = (int)($_POST['product_id'] ?? 0);
        $stmt = $db->prepare("UPDATE `products` SET `featured` = IF(`featured`=1, 0, 1) WHERE `id` = ?");
        $stmt->execute([$productId]);
        set_flash('success', 'Product featured status updated.');
        header('Location: ' . ADMIN_URL . 'products.php');
        exit;
    }

} catch (Exception $e) {
    set_flash('danger', 'Database error: ' . $e->getMessage());
    header('Location: ' . ADMIN_URL . 'products.php');
    exit;
}
