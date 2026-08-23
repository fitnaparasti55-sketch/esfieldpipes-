<?php
/**
 * Contact & RFQ Quote Inquiry Handler
 * Esfield Pipe Platform
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . 'contact.php');
    exit;
}

if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
    set_flash('danger', 'Security verification failed. Please try again.');
    header('Location: ' . BASE_URL . 'contact.php');
    exit;
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$company = trim($_POST['company'] ?? '');
$subject = trim($_POST['subject'] ?? 'Bulk Tender / RFQ Quote Request');
$inquiryType = in_array($_POST['inquiry_type'] ?? '', ['quote', 'technical', 'support', 'bulk', 'general']) ? $_POST['inquiry_type'] : 'quote';
$pipeReq = trim($_POST['pipe_requirement'] ?? '');
$message = trim($_POST['message'] ?? '');

if (empty($name) || empty($email) || empty($message)) {
    set_flash('danger', 'Please provide your Name, Email, and Project Requirements message.');
    header('Location: ' . BASE_URL . 'contact.php');
    exit;
}

try {
    $db = get_db();
    $userId = $_SESSION['user_id'] ?? null;

    $stmt = $db->prepare("
        INSERT INTO `support_inquiries` (
            `user_id`, `name`, `email`, `phone`, `company`, 
            `subject`, `inquiry_type`, `pipe_requirement`, `message`, `status`
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'new')
    ");

    $stmt->execute([
        $userId, $name, $email, $phone, $company,
        $subject, $inquiryType, $pipeReq, $message
    ]);

    set_flash('success', 'Thank you! Your quotation inquiry #RFQ-' . rand(1000, 9999) . ' has been submitted. Our engineering sales team will respond within 24 business hours.');
    header('Location: ' . BASE_URL . 'contact.php');
    exit;

} catch (Exception $e) {
    set_flash('danger', 'Error submitting inquiry: ' . $e->getMessage());
    header('Location: ' . BASE_URL . 'contact.php');
    exit;
}
