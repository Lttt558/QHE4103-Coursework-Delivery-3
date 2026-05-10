<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION["seller_uid"])) {
    header("Location: Login.html");
    exit();
}

$msg = "";
$msg_type = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $seller_id = $_SESSION["seller_uid"];
    $car_model = $_POST['car_model'];
    $color = $_POST['color'];
    $year = $_POST['year'];
    $location = $_POST['location'];
    $price = str_replace(',', '', $_POST['price']);
    $car_image = "default.jpg";

    if (isset($_FILES['car_image']) && $_FILES['car_image']['error'] == 0) {
        $target_dir = "uploads/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $image_name = time() . "_" . basename($_FILES["car_image"]["name"]);
        $target_file = $target_dir . $image_name;
        if (move_uploaded_file($_FILES["car_image"]["tmp_name"], $target_file)) {
            $car_image = $target_file;
        }
    }

    $sql = "INSERT INTO cars (seller_id, car_model, color, year, location, price, car_image)
            VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issssis", $seller_id, $car_model, $color, $year, $location, $price, $car_image);

    if ($stmt->execute()) {
        header("Location: AddCar.html?status=success");
    } else {
        header("Location: AddCar.html?status=error");
    }

    $stmt->close();
    $conn->close();
    exit();
}
?>
