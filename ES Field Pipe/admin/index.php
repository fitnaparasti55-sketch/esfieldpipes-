<?php
/**
 * Admin Index Redirect
 * Esfield Pipe Platform
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';

require_editor_or_admin();

header('Location: ' . ADMIN_URL . 'dashboard.php');
exit;
