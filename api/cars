<?php
require_once '../db.php';
require_once '../auth.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $filters = [];
    $sql = "SELECT c.*, (SELECT image_path FROM car_images WHERE car_id = c.id AND is_primary = 1 LIMIT 1) as primary_image FROM cars c WHERE status != 'disabled'";

    if (!empty($_GET['brand'])) {
        $sql .= " AND brand = :brand";
        $filters[':brand'] = sanitize($_GET['brand']);
    }
    if (!empty($_GET['fuel'])) {
        $sql .= " AND fuel_type = :fuel";
        $filters[':fuel'] = sanitize($_GET['fuel']);
    }
    if (!empty($_GET['transmission'])) {
        $sql .= " AND transmission = :transmission";
        $filters[':transmission'] = sanitize($_GET['transmission']);
    }
    if (!empty($_GET['seating'])) {
        $sql .= " AND seating_capacity = :seating";
        $filters[':seating'] = (int)$_GET['seating'];
    }
    if (!empty($_GET['search'])) {
        $sql .= " AND (name LIKE :search OR brand LIKE :search OR model LIKE :search)";
        $filters[':search'] = '%' . sanitize($_GET['search']) . '%';
    }

    $db->query($sql);
    foreach ($filters as $key => $value) {
        $db->bind($key, $value);
    }
    $cars = $db->fetchAll();
    echo json_encode(['status' => 'success', 'data' => $cars]);
    exit;
}

if ($method === 'POST') {
    $auth->requireAuth('admin');
    $data = $_POST;
    // Validation and insert logic here
    jsonResponse('success', 'Car added');
}
