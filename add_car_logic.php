<?php
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $seller_id = 1;
    $car_model = $_POST['car_model'];
    $color = $_POST['color'];
    $year = $_POST['year'];
    $location = $_POST['location'];
    $price = str_replace(',', '', $_POST['price']);
    $car_image = "default.jpg";

    $query = "INSERT INTO cars (seller_id, car_model, color, year, location, price, car_image)
              VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "issssis", $seller_id, $car_model, $color, $year, $location, $price, $car_image);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: AddCar.html?status=success");
    } else {
        header("Location: AddCar.html?status=error");
    }

    mysqli_stmt_close($stmt);
}
?>
