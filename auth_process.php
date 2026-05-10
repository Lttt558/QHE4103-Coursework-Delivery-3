<?php
session_start();

$host = "localhost";
$dbuser = "zsm";
$dbpass = "988980";
$dbname = "seller_web_db";

$conn = new mysqli($host, $dbuser, $dbpass, $dbname);
if ($conn->connect_error) {
    die("DB_CONNECT_FAILED");
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["reg_submit"])) {
    $fullname = $_POST["fullname"];
    $username = $_POST["username"];
    $email = $_POST["useremail"];
    $phone = $_POST["userphone"];
    $password = password_hash($_POST["userpwd"], PASSWORD_DEFAULT);

    $check = $conn->prepare("SELECT uid FROM seller_account WHERE username=? OR email=?");
    $check->bind_param("ss", $username, $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        header("Location: register.html?err=exists");
        exit();
    }

    $insert = $conn->prepare("INSERT INTO seller_account (fullname, username, email, phone, password) VALUES (?,?,?,?,?)");
    $insert->bind_param("sssss", $fullname, $username, $email, $phone, $password);

    if ($insert->execute()) {
        header("Location: login.html?msg=registered");
    } else {
        header("Location: register.html?err=failed");
    }

    $insert->close();
    $check->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["log_submit"])) {
    $log_user = $_POST["log_user"];
    $log_pwd = $_POST["log_pwd"];

    $query = $conn->prepare("SELECT uid, username, password FROM seller_account WHERE username=?");
    $query->bind_param("s", $log_user);
    $query->execute();
    $query->store_result();

    if ($query->num_rows == 1) {
        $query->bind_result($sid, $suser, $shash);
        $query->fetch();

        if (password_verify($log_pwd, $shash)) {
            $_SESSION["seller_uid"] = $sid;
            $_SESSION["seller_name"] = $suser;
            header("Location: dashboard.html");
            exit();
        }
    }

    header("Location: login.html?err=invalid");
    $query->close();
}

if (isset($_GET["logout"])) {
    session_unset();
    session_destroy();
    header("Location: login.html");
    exit();
}
?>
