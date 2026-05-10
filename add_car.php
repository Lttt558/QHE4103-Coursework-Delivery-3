<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION["seller_uid"])) {
    header("Location: Login.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $seller_id = $_SESSION["seller_uid"];
    
    $car_model = isset($_POST['car_model']) ? trim($_POST['car_model']) : '';
    $color = isset($_POST['color']) ? trim($_POST['color']) : '';
    $year = isset($_POST['year']) ? intval($_POST['year']) : 0;
    $location = isset($_POST['location']) ? trim($_POST['location']) : '';
    $price = isset($_POST['price']) ? str_replace(',', '', $_POST['price']) : 0;
    
    $car_image = "default.jpg";
    if (isset($_FILES['car_image']) && $_FILES['car_image']['error'] == 0) {
        $target_dir = "uploads/";
        
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $image_extension = pathinfo($_FILES["car_image"]["name"], PATHINFO_EXTENSION);
        $image_name = time() . "_" . uniqid() . "." . $image_extension;
        $target_file = $target_dir . $image_name;
        
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (in_array(strtolower($image_extension), $allowed_types)) {
            if (move_uploaded_file($_FILES["car_image"]["tmp_name"], $target_file)) {
                $car_image = $target_file;
            }
        }
    }
    
    if (empty($car_model) || empty($color) || $year <= 0 || empty($location) || empty($price)) {
        header("Location: AddCar.html?status=empty_fields");
        exit();
    }
    
    $sql = "INSERT INTO cars (seller_id, car_model, color, year, location, price, car_image)
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issssis", $seller_id, $car_model, $color, $year, $location, $price, $car_image);
    
    if ($stmt->execute()) {
        header("Location: AddCar.html?status=success");
    } else {
        header("Location: AddCar.html?status=error&msg=" . urlencode($conn->error));
    }
    
    $stmt->close();
    $conn->close();
    exit();
    
} else {
    header("Location: AddCar.html");
    exit();
}
?>
