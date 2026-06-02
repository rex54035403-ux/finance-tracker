<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// 直接讀取 Railway 自動注入的變數名稱
$host = getenv('MYSQLHOST');
$user = getenv('MYSQLUSER');
$pass = getenv('MYSQLPASSWORD');
$db   = getenv('MYSQLDATABASE');

$conn = new mysqli($host, $user, $pass, $db);
$conn->set_charset("utf8");

if ($conn->connect_error) {
    die("連線失敗: " . $conn->connect_error);
}
?>
