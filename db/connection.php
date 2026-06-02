<?php
// 直接對應你在 Railway Variables 裡看到的 Key
$host = getenv('MYSQLHOST');
$user = getenv('MYSQLUSER');
$pass = getenv('MYSQLPASSWORD');
$db   = getenv('MYSQLDATABASE');
$port = getenv('MYSQLPORT') ?: '3306';

// 建立連線
$conn = new mysqli($host, $user, $pass, $db, $port);
$conn->set_charset("utf8");

// 檢查連線
if ($conn->connect_error) {
    // 這裡會顯示確切的 HOST 和 USER，方便我們除錯
    die("連線失敗! Host: $host, User: $user, Error: " . $conn->connect_error);
}
?>
