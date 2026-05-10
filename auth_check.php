<?php
session_start();

if (!isset($_SESSION["seller_uid"])) {
    header("Location: Login.html");
    exit();
}
?>
