<?php
/**
 * AJAX API: Search Suggestions
 * Esfield Pipe Platform
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

$q = trim($_GET['q'] ?? '');
if (strlen($q) < 2) {
    echo json_encode([]);
    exit;
}

$db = get_db();
$stmt = $db->prepare("
    SELECT id, name, slug, inner_diameter_mm, outer_diameter_mm, stiffness_class, price_per_pipe, image
    FROM `products`
    WHERE `status` = 'active' AND (
        `name` LIKE ? OR `description` LIKE ? OR `inner_diameter_mm` LIKE ? OR `application_type` LIKE ?
    )
    ORDER BY `featured` DESC, `inner_diameter_mm` ASC
    LIMIT 6
");

$term = "%{$q}%";
$stmt->execute([$term, $term, $term, $term]);
$results = $stmt->fetchAll();

$formatted = [];
foreach ($results as $item) {
    $formatted[] = [
        'id' => $item['id'],
        'name' => $item['name'],
        'url' => BASE_URL . 'product-detail.php?slug=' . urlencode($item['slug']),
        'specs' => "ID: {$item['inner_diameter_mm']}mm | OD: {$item['outer_diameter_mm']}mm | {$item['stiffness_class']}",
        'price' => format_price($item['price_per_pipe']),
        'image' => BASE_URL . ($item['image'] ?: 'assets/images/dwc-pipe-100mm.svg')
    ];
}

echo json_encode($formatted);
