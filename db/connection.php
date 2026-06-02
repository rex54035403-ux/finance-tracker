<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// 這是 Railway 服務連接後會自動注入的標準名稱
$host = getenv('MYSQLHOST');
$user = getenv('MYSQLUSER');
$pass = getenv('MYSQLPASSWORD');
$db   = getenv('MYSQLDATABASE');
$port = getenv('MYSQLPORT') ?: '3306';

// 檢查是否成功讀取到變數 (方便除錯)
if (!$host || !$user || !$db) {
    die("錯誤：無法讀取資料庫設定。請確認 Railway 服務連接已建立。");
}

// 建立連線
$conn = new mysqli($host, $user, $pass, $db, $port);
$conn->set_charset("utf8");

// 檢查連線
if ($conn->connect_error) {
    die("連線失敗 (Host: $host): " . $conn->connect_error);
}
?>
