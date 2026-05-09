<?php
include 'db_connect.php';

$model = isset($_GET['model']) ? $_GET['model'] : '';
$year = isset($_GET['year']) ? $_GET['year'] : '';

$sql = "SELECT * FROM cars WHERE 1=1";
$params = array();
$types = "";

if (!empty($model)) {
    $sql .= " AND car_model LIKE ?";
    $params[] = "%$model%";
    $types .= "s";
}

if (!empty($year)) {
    $sql .= " AND year = ?";
    $params[] = $year;
    $types .= "s";
}

$stmt = mysqli_prepare($conn, $sql);

if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>
