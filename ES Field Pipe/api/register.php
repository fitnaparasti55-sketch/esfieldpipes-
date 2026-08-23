<?php
/**
 * Registration Action Handler
 * Esfield Pipe Platform
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . 'register.php');
    exit;
}

// CSRF Verification
if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
    set_flash('danger', 'Security verification failed. Please try again.');
    header('Location: ' . BASE_URL . 'register.php');
    exit;
}

$name = sanitize($_POST['name'] ?? '');
$email = sanitize($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

// Basic Validation
if (empty($name) || empty($email) || empty($password)) {
    set_flash('danger', 'Please fill in all required fields.');
    header('Location: ' . BASE_URL . 'register.php');
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    set_flash('danger', 'Please provide a valid email address.');
    header('Location: ' . BASE_URL . 'register.php');
    exit;
}

if (strlen($password) < 6) {
    set_flash('danger', 'Password must be at least 6 characters long.');
    header('Location: ' . BASE_URL . 'register.php');
    exit;
}

if ($password !== $confirm_password) {
    set_flash('danger', 'Passwords do not match. Please verify.');
    header('Location: ' . BASE_URL . 'register.php');
    exit;
}

try {
    $db = get_db();

    // Check if email already exists
    $check = $db->prepare("SELECT id FROM `users` WHERE `email` = ?");
    $check->execute([$email]);
    if ($check->fetch()) {
        set_flash('danger', 'An account with this email address already exists.');
        header('Location: ' . BASE_URL . 'register.php');
        exit;
    }

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert new user
    $stmt = $db->prepare("INSERT INTO `users` (`name`, `email`, `password`, `role`, `status`) VALUES (?, ?, ?, 'customer', 'active')");
    $stmt->execute([$name, $email, $hashedPassword]);

    $newUserId = $db->lastInsertId();

    // Auto login
    $stmt = $db->prepare("SELECT * FROM `users` WHERE `id` = ?");
    $stmt->execute([$newUserId]);
    $user = $stmt->fetch();

    login_user($user);

    set_flash('success', "Account created successfully! Welcome to Esfield Pipe, " . htmlspecialchars($name) . ".");
    header('Location: ' . BASE_URL . 'index.php');
    exit;

} catch (Exception $e) {
    set_flash('danger', 'Registration failed due to a system error. Please try again.');
    header('Location: ' . BASE_URL . 'register.php');
    exit;
}
