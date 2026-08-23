<?php
/**
 * Admin Logo Action Handler
 * Esfield Pipe Platform
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

require_editor_or_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . ADMIN_URL . 'logo.php');
    exit;
}

if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
    set_flash('danger', 'Security verification failed.');
    header('Location: ' . ADMIN_URL . 'logo.php');
    exit;
}

$action = $_POST['action'] ?? '';
$db = get_db();
$uploadDir = __DIR__ . '/../assets/uploads/branding/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

try {
    $stmt = $db->prepare("INSERT INTO `settings` (`key_name`, `key_value`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `key_value` = VALUES(`key_value`)");

    // 1. UPDATE LOGOS
    if ($action === 'upload_logos') {
        // Main Logo
        if (!empty($_FILES['site_logo']) && $_FILES['site_logo']['error'] === UPLOAD_ERR_OK) {
            $res = handle_file_upload($_FILES['site_logo'], $uploadDir, ['svg', 'png', 'jpg', 'jpeg', 'webp'], 5);
            if ($res['success']) {
                $path = 'assets/uploads/branding/' . $res['filename'];
                $stmt->execute(['site_logo', $path]);
            }
        }

        // Mobile Logo
        if (!empty($_FILES['site_logo_mobile']) && $_FILES['site_logo_mobile']['error'] === UPLOAD_ERR_OK) {
            $res = handle_file_upload($_FILES['site_logo_mobile'], $uploadDir, ['svg', 'png', 'jpg', 'jpeg', 'webp'], 5);
            if ($res['success']) {
                $path = 'assets/uploads/branding/' . $res['filename'];
                $stmt->execute(['site_logo_mobile', $path]);
            }
        }

        // Favicon
        if (!empty($_FILES['site_favicon']) && $_FILES['site_favicon']['error'] === UPLOAD_ERR_OK) {
            $res = handle_file_upload($_FILES['site_favicon'], $uploadDir, ['svg', 'png', 'ico', 'webp'], 2);
            if ($res['success']) {
                $path = 'assets/uploads/branding/' . $res['filename'];
                $stmt->execute(['site_favicon', $path]);
            }
        }

        set_flash('success', 'Website branding logos updated successfully.');
        header('Location: ' . ADMIN_URL . 'logo.php');
        exit;
    }

    // 2. RESET TO DEFAULT
    if ($action === 'reset_logo') {
        $target = $_POST['target'] ?? '';
        if ($target === 'site_logo') {
            $stmt->execute(['site_logo', 'assets/images/logo.svg']);
        } elseif ($target === 'site_logo_mobile') {
            $stmt->execute(['site_logo_mobile', 'assets/images/logo.svg']);
        } elseif ($target === 'site_favicon') {
            $stmt->execute(['site_favicon', 'assets/images/logo.svg']);
        }

        set_flash('success', 'Logo reset to default.');
        header('Location: ' . ADMIN_URL . 'logo.php');
        exit;
    }

} catch (Exception $e) {
    set_flash('danger', 'Error updating logos: ' . $e->getMessage());
    header('Location: ' . ADMIN_URL . 'logo.php');
    exit;
}
