<?php
// 自動讀取 Railway 變數
$host = getenv('MYSQLHOST');
$user = getenv('MYSQLUSER');
$pass = getenv('MYSQLPASSWORD');
$db   = getenv('MYSQLDATABASE');
$port = getenv('MYSQLPORT') ?: '3306'; // 預設使用 3306

// 使用 $port 建立連線
$conn = new mysqli($host, $user, $pass, $db, $port);

$conn->set_charset("utf8");

if ($conn->connect_error) {
    die("資料庫連線失敗 (Host: $host, Port: $port): " . $conn->connect_error);
}
?>
