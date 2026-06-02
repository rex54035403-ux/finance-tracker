<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// 嘗試讀取變數
$host = getenv('DB_HOST') ?: $_ENV['DB_HOST'] ?? '';
$user = getenv('DB_USER') ?: $_ENV['DB_USER'] ?? '';
$pass = getenv('DB_PASS') ?: $_ENV['DB_PASS'] ?? '';
$db   = getenv('DB_NAME') ?: $_ENV['DB_NAME'] ?? '';

// 偵錯用：如果環境變數有抓到，我們可以在這行看到它們的值
// 如果網頁顯示空值，代表 Railway Variables 沒設對！
if (empty($host)) {
    die("錯誤：讀取不到環境變數 (DB_HOST 為空)。<br>請確認 Railway > finance-tracker > Variables 設定！");
}

// 建立連線
$conn = new mysqli($host, $user, $pass, $db);
$conn->set_charset("utf8");

// 檢查連線狀態
if ($conn->connect_error) {
    // 這裡我們把主機位址印出來，幫你確認到底嘗試連去哪裡
    die("資料庫連線失敗 (Host: $host): " . $conn->connect_error);
}
?>
