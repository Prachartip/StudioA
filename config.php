<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "shop_db";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("เชื่อมต่อฐานข้อมูลล้มเหลว: " . mysqli_connect_error());
}
?>