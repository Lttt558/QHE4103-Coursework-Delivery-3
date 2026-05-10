<?php
$host = "localhost";
$dbuser = "zsm";
$dbpass = "988980";
$dbname = "seller_web_db";
session_start();
$conn = new mysqli($host,$dbuser,$dbpass,$dbname);
if($conn->connect_error){die("DB_CONNECT_FAILED");}

if($_SERVER["REQUEST_METHOD"]=="POST" && isset($_POST["reg_submit"])){
    $fname = $_POST["fullname"];
    $uname = $_POST["username"];
    $mail = $_POST["useremail"];
    $phone = $_POST["userphone"];
    $pwd = password_hash($_POST["userpwd"],PASSWORD_DEFAULT);
    $check = $conn->prepare("SELECT uid FROM seller_account WHERE username=? OR email=?");
    $check->bind_param("ss",$uname,$mail);
    $check->execute();
    $check->store_result();
    if($check->num_rows>0){header("Location:?page=reg&err=exists");exit();}
    $insert = $conn->prepare("INSERT INTO seller_account(fullname,username,email,phone,password) VALUES(?,?,?,?,?)");
    $insert->bind_param("sssss",$fname,$uname,$mail,$phone,$pwd);
    if($insert->execute()){header("Location:?page=login&msg=ok");}
    else{header("Location:?page=reg&err=fail");}
    $insert->close();$check->close();
}

if($_SERVER["REQUEST_METHOD"]=="POST" && isset($_POST["log_submit"])){
    $log_un = $_POST["log_user"];
    $log_pw = $_POST["log_pwd"];
    $query = $conn->prepare("SELECT uid,username,password FROM seller_account WHERE username=?");
    $query->bind_param("s",$log_un);
    $query->execute();
    $query->store_result();
    if($query->num_rows==1){
        $query->bind_result($sid,$suser,$shash);
        $query->fetch();
        if(password_verify($log_pw,$shash)){
            $_SESSION["seller_uid"]=$sid;
            $_SESSION["seller_name"]=$suser;
            header("Location:?page=dashboard");exit();
        }
    }
    header("Location:?page=login&err=wrong");
    $query->close();
}

if(isset($_GET["logout"])){
    session_unset();
    session_destroy();
    header("Location:?page=login");exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Seller Web System</title>
<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:Arial}
.container{max-width:400px;margin:50px auto;padding:25px;border:1px solid #eee;border-radius:8px}
form{display:flex;flex-direction:column;gap:12px;margin-top:20px}
input{padding:10px;border:1px solid #ccc;border-radius:4px}
button{padding:10px;background:#222;color:white;border:none;border-radius:4px;cursor:pointer}
.link{margin-top:15px;text-align:center}
.dash{margin-top:30px;line-height:2}
</style>
</head>
<body>
<div class="container">
<?php
$page = $_GET["page"]??"login";
if($page=="reg"){
echo "<h2>Seller Account Registration</h2>";
if(isset($_GET["err"]) && $_GET["err"]=="exists")echo "<p style='color:red'>User already exists</p >";
?>
<form method="post">
<input type="hidden" name="reg_submit" value="1">
<input type="text" name="fullname" placeholder="Full Name" required>
<input type="text" name="username" placeholder="Account Username" required>
<input type="email" name="useremail" placeholder="User Email" required>
<input type="text" name="userphone" placeholder="Contact Phone">
<input type="password" name="userpwd" placeholder="Login Password" required>
<button type="submit">Complete Registration</button>
</form>
<div class="link"><a href=" ">Back to Login</a ></div>
<?php }else if($page=="login"){
echo "<h2>Seller Login Portal</h2>";
if(isset($_GET["err"]) && $_GET["err"]=="wrong")echo "<p style='color:red'>Invalid username or password</p >";
if(isset($_GET["msg"]) && $_GET["msg"]=="ok")echo "<p style='color:green'>Register Success, Please Login</p >";
?>
<form method="post">
<input type="hidden" name="log_submit" value="1">
<input type="text" name="log_user" placeholder="Username" required>
<input type="password" name="log_pwd" placeholder="Password" required>
<button type="submit">Account Login</button>
</form>
<div class="link"><a href="?page=reg">Register New Seller Account</a ></div>
<?php }else if($page=="dashboard"){
if(!isset($_SESSION["seller_uid"])){header("Location:?page=login");exit();}
echo "<h2>Seller Dashboard</h2>";
echo "<div class='dash'>Welcome, ".$_SESSION["seller_name"]."<br>";
echo "<a href='?logout=1'>Account Logout</a ></div>";
}
?>
</div>
</body>
</html>