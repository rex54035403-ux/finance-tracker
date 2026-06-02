<?php
// 使用明確的變數名稱
$host = getenv('MYSQLHOST');
$user = getenv('MYSQLUSER');
$pass = getenv('MYSQLPASSWORD');
$db   = getenv('MYSQLDATABASE');
$port = getenv('MYSQLPORT') ?: '3306'; // 如果沒抓到變數，強制使用 3306

// 進行連線，加入 port 參數
$conn = new mysqli($host, $user, $pass, $db, $port);
$conn->set_charset("utf8");

if ($conn->connect_error) {
    // 輸出詳細錯誤，讓我們知道這一次失敗在哪
    die("資料庫連線失敗 (Host: $host, Port: $port): " . $conn->connect_error);
}
?>
