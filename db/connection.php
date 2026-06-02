<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// 1. 強制從 Railway 環境變數抓取資料庫設定
$host = getenv('DB_HOST');
$user = getenv('DB_USER');
$pass = getenv('DB_PASS');
$db   = getenv('DB_NAME');

// 2. 如果發現任何一個變數是空的，直接報錯，方便你檢查 Railway Variables 設定
if (!$host || !$user || !$db) {
    die("錯誤：找不到資料庫環境變數！請檢查 Railway 的 Variables 設定是否正確 (DB_HOST, DB_USER, DB_PASS, DB_NAME)。<br>
         當前抓取值: Host=$host, User=$user, DB=$db");
}

// 3. 建立連線
$conn = new mysqli($host, $user, $pass, $db);

// 4. 設定編碼
$conn->set_charset("utf8");

// 5. 若連線失敗，直接顯示錯誤訊息
if ($conn->connect_error) {
    die("資料庫連線失敗: " . $conn->connect_error);
}
?>
