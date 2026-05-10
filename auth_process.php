<?php
session_start();
require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["reg_submit"])) {
    $fullname = $_POST["fullname"];
    $address = $_POST["address"];
    $phone = $_POST["phone"];
    $email = $_POST["email"];
    $username = $_POST["username"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    $check = $conn->prepare("SELECT seller_id FROM sellers WHERE username = ? OR email = ?");
    $check->bind_param("ss", $username, $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        header("Location: Register.html?err=exists");
        exit();
    }

    $insert = $conn->prepare("INSERT INTO sellers (fullname, address, phone, email, username, password) VALUES (?, ?, ?, ?, ?, ?)");
    $insert->bind_param("ssssss", $fullname, $address, $phone, $email, $username, $password);

    if ($insert->execute()) {
        header("Location: Login.html?msg=registered");
    } else {
        header("Location: Register.html?err=failed");
    }

    $insert->close();
    $check->close();
    $conn->close();
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["log_submit"])) {
    $log_user = $_POST["username"];
    $log_pwd = $_POST["password"];

    $query = $conn->prepare("SELECT seller_id, username, password FROM sellers WHERE username = ?");
    $query->bind_param("s", $log_user);
    $query->execute();
    $query->store_result();

    if ($query->num_rows == 1) {
        $query->bind_result($sid, $suser, $shash);
        $query->fetch();

        if (password_verify($log_pwd, $shash)) {
            $_SESSION["seller_uid"] = $sid;
            $_SESSION["seller_name"] = $suser;
            header("Location: AddCar.html");
            exit();
        }
    }

    header("Location: Login.html?err=invalid");
    $query->close();
    $conn->close();
    exit();
}

if (isset($_GET["logout"])) {
    session_unset();
    session_destroy();
    header("Location: Home.html");
    exit();
}
?>
