<?php
// 1. 優先嘗試解析 Railway 提供的總合連線字串 (最穩定的做法)
$mysqlUrl = $_ENV['MYSQL_URL'] ?? $_SERVER['MYSQL_URL'] ?? getenv('MYSQL_URL');

if ($mysqlUrl) {
    $dbParts = parse_url($mysqlUrl);
    $host = $dbParts['host'];
    $user = $dbParts['user'];
    $pass = $dbParts['pass'] ?? '';
    $db   = ltrim($dbParts['path'], '/');
    $port = $dbParts['port'] ?? 3306;
} else {
    // 2. 備用方案：使用多重方式抓取個別變數，防止 getenv 失效
    $host = $_ENV['MYSQLHOST'] ?? $_SERVER['MYSQLHOST'] ?? getenv('MYSQLHOST');
    $user = $_ENV['MYSQLUSER'] ?? $_SERVER['MYSQLUSER'] ?? getenv('MYSQLUSER');
    $pass = $_ENV['MYSQLPASSWORD'] ?? $_SERVER['MYSQLPASSWORD'] ?? getenv('MYSQLPASSWORD');
    $db   = $_ENV['MYSQLDATABASE'] ?? $_SERVER['MYSQLDATABASE'] ?? getenv('MYSQLDATABASE') ?: 'railway';
    $port = $_ENV['MYSQLPORT'] ?? $_SERVER['MYSQLPORT'] ?? getenv('MYSQLPORT') ?: '3306';
}

// 3. 雙重檢查：如果真的抓不到，印出錯誤提示避免無意義的崩潰
if (empty($host)) {
    die("嚴重錯誤：無法獲取環境變數，請檢查 Railway 的 Variables 設定。");
}

// 建立連線
$conn = new mysqli($host, $user, $pass, $db, (int)$port);

// 檢查連線
if ($conn->connect_error) {
    die("資料庫連線失敗: " . $conn->connect_error);
}

// 設定編碼
$conn->set_charset("utf8");
?>
