<?php
/**
 * Admin Category Action Handler
 * Esfield Pipe Platform
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

require_editor_or_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . ADMIN_URL . 'categories.php');
    exit;
}

if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
    set_flash('danger', 'Security verification failed.');
    header('Location: ' . ADMIN_URL . 'categories.php');
    exit;
}

$action = $_POST['action'] ?? '';
$db = get_db();
$uploadDir = __DIR__ . '/../assets/uploads/categories/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

try {
    // 1. ADD CATEGORY
    if ($action === 'add') {
        $name = trim($_POST['name'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        if (empty($slug)) {
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-'));
        }
        $desc = trim($_POST['description'] ?? '');
        $icon = trim($_POST['icon'] ?? 'fa-solid fa-water-ladder');
        $displayOrder = (int)($_POST['display_order'] ?? 0);
        $status = ($_POST['status'] ?? 'active') === 'inactive' ? 'inactive' : 'active';
        $imagePath = null;

        if (!empty($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $upload = handle_file_upload($_FILES['image'], $uploadDir, ['jpg', 'jpeg', 'png', 'webp', 'svg'], 10);
            if ($upload['success']) {
                $imagePath = 'assets/uploads/categories/' . $upload['filename'];
            }
        }

        $stmt = $db->prepare("INSERT INTO `categories` (`name`, `slug`, `description`, `icon`, `image`, `display_order`, `status`) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $slug, $desc, $icon, $imagePath, $displayOrder, $status]);

        set_flash('success', "Category '{$name}' created successfully.");
        header('Location: ' . ADMIN_URL . 'categories.php');
        exit;
    }

    // 2. EDIT CATEGORY
    if ($action === 'edit') {
        $id = (int)($_POST['category_id'] ?? 0);
        $stmt = $db->prepare("SELECT * FROM `categories` WHERE `id` = ?");
        $stmt->execute([$id]);
        $cat = $stmt->fetch();

        if (!$cat) {
            set_flash('danger', 'Category not found.');
            header('Location: ' . ADMIN_URL . 'categories.php');
            exit;
        }

        $name = trim($_POST['name'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        if (empty($slug)) {
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-'));
        }
        $desc = trim($_POST['description'] ?? '');
        $icon = trim($_POST['icon'] ?? 'fa-solid fa-water-ladder');
        $displayOrder = (int)($_POST['display_order'] ?? 0);
        $status = ($_POST['status'] ?? 'active') === 'inactive' ? 'inactive' : 'active';
        $imagePath = $cat['image'];

        if (!empty($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $upload = handle_file_upload($_FILES['image'], $uploadDir, ['jpg', 'jpeg', 'png', 'webp', 'svg'], 10);
            if ($upload['success']) {
                $imagePath = 'assets/uploads/categories/' . $upload['filename'];
            }
        }

        $update = $db->prepare("UPDATE `categories` SET `name` = ?, `slug` = ?, `description` = ?, `icon` = ?, `image` = ?, `display_order` = ?, `status` = ? WHERE `id` = ?");
        $update->execute([$name, $slug, $desc, $icon, $imagePath, $displayOrder, $status, $id]);

        set_flash('success', "Category '{$name}' updated successfully.");
        header('Location: ' . ADMIN_URL . 'categories.php');
        exit;
    }

    // 3. DELETE CATEGORY
    if ($action === 'delete') {
        $id = (int)($_POST['category_id'] ?? 0);
        // Check if products exist in category
        $pCheck = $db->prepare("SELECT COUNT(*) FROM `products` WHERE `category_id` = ?");
        $pCheck->execute([$id]);
        $count = $pCheck->fetchColumn();

        if ($count > 0) {
            set_flash('danger', "Cannot delete category containing {$count} products. Reassign or delete products first.");
        } else {
            $del = $db->prepare("DELETE FROM `categories` WHERE `id` = ?");
            $del->execute([$id]);
            set_flash('success', 'Category deleted successfully.');
        }

        header('Location: ' . ADMIN_URL . 'categories.php');
        exit;
    }

    // 4. TOGGLE STATUS
    if ($action === 'toggle_status') {
        $id = (int)($_POST['category_id'] ?? 0);
        $stmt = $db->prepare("UPDATE `categories` SET `status` = IF(`status`='active', 'inactive', 'active') WHERE `id` = ?");
        $stmt->execute([$id]);
        set_flash('success', 'Category status updated.');
        header('Location: ' . ADMIN_URL . 'categories.php');
        exit;
    }

} catch (Exception $e) {
    set_flash('danger', 'Error processing category: ' . $e->getMessage());
    header('Location: ' . ADMIN_URL . 'categories.php');
    exit;
}
