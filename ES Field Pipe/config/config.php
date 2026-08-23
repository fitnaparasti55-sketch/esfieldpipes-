<?php
/**
 * Global Configuration & Initialization
 * Esfield Pipe Platform
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    session_start();
}

// Ensure session ID exists for guest cart tracking
if (empty($_SESSION['guest_session_id'])) {
    $_SESSION['guest_session_id'] = bin2hex(random_bytes(16));
}

// Require database
require_once __DIR__ . '/database.php';

// Base URL calculation (works dynamically with localhost, virtual hosts, and subfolders)
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
// Extract root project folder: /ES Field Pipe
$projectPath = preg_replace('#/(admin|api|includes|config).*$#i', '', $scriptDir);
$projectPath = rtrim($projectPath, '/');

$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
define('BASE_URL', $protocol . $host . $projectPath . '/');
define('ADMIN_URL', BASE_URL . 'admin/');
define('ASSETS_URL', BASE_URL . 'assets/');

// CSRF Token Helpers
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string {
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token()) . '">';
}

function verify_csrf_token(?string $token): bool {
    if (!$token || empty($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

// Global Site Settings Cache
function get_settings(): array {
    static $settings = null;
    if ($settings === null) {
        try {
            $db = get_db();
            $stmt = $db->query("SELECT `key_name`, `key_value` FROM `settings`");
            $settings = [];
            while ($row = $stmt->fetch()) {
                $settings[$row['key_name']] = $row['key_value'];
            }
        } catch (Exception $e) {
            $settings = [];
        }
    }
    return $settings;
}

function get_setting(string $key, string $default = ''): string {
    $settings = get_settings();
    return $settings[$key] ?? $default;
}
