<?php
/**
 * Admin Media Action Handler
 * Esfield Pipe Platform
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

require_editor_or_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . ADMIN_URL . 'media.php');
    exit;
}

if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
    set_flash('danger', 'Security verification failed.');
    header('Location: ' . ADMIN_URL . 'media.php');
    exit;
}

$action = $_POST['action'] ?? '';
$db = get_db();
$uploadDir = __DIR__ . '/../assets/uploads/media/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

try {
    // 1. UPLOAD MEDIA
    if ($action === 'upload') {
        if (!empty($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
            $res = handle_file_upload($_FILES['file'], $uploadDir, ['jpg', 'jpeg', 'png', 'webp', 'svg', 'pdf'], 15);
            if ($res['success']) {
                $filePath = 'assets/uploads/media/' . $res['filename'];
                $fileSize = (int)$_FILES['file']['size'];
                $origName = basename($_FILES['file']['name']);
                $mimeType = mime_content_type($res['path']) ?: 'image/jpeg';
                $altText = trim($_POST['alt_text'] ?? pathinfo($origName, PATHINFO_FILENAME));

                $stmt = $db->prepare("INSERT INTO `media` (`filename`, `original_name`, `file_path`, `file_size`, `mime_type`, `alt_text`) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$res['filename'], $origName, $filePath, $fileSize, $mimeType, $altText]);

                set_flash('success', "Media file '{$origName}' uploaded successfully.");
            } else {
                set_flash('danger', 'Upload failed: ' . $res['error']);
            }
        } else {
            set_flash('danger', 'Please select a valid file to upload.');
        }

        header('Location: ' . ADMIN_URL . 'media.php');
        exit;
    }

    // 2. DELETE MEDIA
    if ($action === 'delete') {
        $id = (int)($_POST['media_id'] ?? 0);
        $stmt = $db->prepare("SELECT * FROM `media` WHERE `id` = ?");
        $stmt->execute([$id]);
        $m = $stmt->fetch();

        if ($m) {
            $absPath = __DIR__ . '/../' . $m['file_path'];
            if (file_exists($absPath)) {
                @unlink($absPath);
            }
            $del = $db->prepare("DELETE FROM `media` WHERE `id` = ?");
            $del->execute([$id]);
            set_flash('success', 'Media file removed.');
        }

        header('Location: ' . ADMIN_URL . 'media.php');
        exit;
    }

    // 3. SET AS HOMEPAGE HERO OR ABOUT IMAGE
    if ($action === 'set_as') {
        $target = $_POST['target'] ?? '';
        $filePath = trim($_POST['file_path'] ?? '');

        if ($target === 'home_hero_image') {
            $stmt = $db->prepare("INSERT INTO `settings` (`key_name`, `key_value`) VALUES ('home_hero_image', ?) ON DUPLICATE KEY UPDATE `key_value` = VALUES(`key_value`)");
            $stmt->execute([$filePath]);
            set_flash('success', 'Image successfully assigned as Homepage Hero Image.');
        } elseif ($target === 'home_company_image') {
            $stmt = $db->prepare("INSERT INTO `settings` (`key_name`, `key_value`) VALUES ('home_company_image', ?) ON DUPLICATE KEY UPDATE `key_value` = VALUES(`key_value`)");
            $stmt->execute([$filePath]);
            set_flash('success', 'Image successfully assigned as Company Section Image.');
        }

        header('Location: ' . ADMIN_URL . 'media.php');
        exit;
    }

} catch (Exception $e) {
    set_flash('danger', 'Media operation error: ' . $e->getMessage());
    header('Location: ' . ADMIN_URL . 'media.php');
    exit;
}
