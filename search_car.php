<?php
require_once 'config.php';

$model = isset($_GET['model']) ? trim($_GET['model']) : '';
$year = isset($_GET['year']) ? trim($_GET['year']) : '';

$sql = "SELECT c.*, s.fullname as seller_name, s.phone as seller_phone 
        FROM cars c 
        LEFT JOIN sellers s ON c.seller_id = s.seller_id 
        WHERE 1=1";

$params = [];
$types = "";

if (!empty($model)) {
    $sql .= " AND c.car_model LIKE ?";
    $params[] = "%$model%";
    $types .= "s";
}

if (!empty($year) && is_numeric($year)) {
    $sql .= " AND c.year = ?";
    $params[] = $year;
    $types .= "i";
}

$sql .= " ORDER BY c.car_id DESC";

$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

$cars = [];
while ($row = $result->fetch_assoc()) {
    $cars[] = $row;
}

$stmt->close();
$conn->close();

header('Content-Type: application/json');
echo json_encode($cars);
?>
