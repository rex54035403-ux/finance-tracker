<?php
// 使用 ?: 確保不只是 null，連「空字串」或 false 都會自動尋找下一個備援
$mysqlUrl = ($_ENV['MYSQL_URL'] ?? null) ?: ($_SERVER['MYSQL_URL'] ?? null) ?: getenv('MYSQL_URL');

if ($mysqlUrl) {
    $dbParts = parse_url($mysqlUrl);
    $host = $dbParts['host'] ?? '';
    $user = $dbParts['user'] ?? '';
    $pass = $dbParts['pass'] ?? '';
    $db   = isset($dbParts['path']) ? ltrim($dbParts['path'], '/') : '';
    $port = $dbParts['port'] ?? 3306;
} else {
    $host = ($_ENV['MYSQLHOST'] ?? null) ?: ($_SERVER['MYSQLHOST'] ?? null) ?: getenv('MYSQLHOST');
    $user = ($_ENV['MYSQLUSER'] ?? null) ?: ($_SERVER['MYSQLUSER'] ?? null) ?: getenv('MYSQLUSER');
    $pass = ($_ENV['MYSQLPASSWORD'] ?? null) ?: ($_SERVER['MYSQLPASSWORD'] ?? null) ?: getenv('MYSQLPASSWORD');
    $db   = ($_ENV['MYSQLDATABASE'] ?? null) ?: ($_SERVER['MYSQLDATABASE'] ?? null) ?: getenv('MYSQLDATABASE') ?: 'railway';
    $port = ($_ENV['MYSQLPORT'] ?? null) ?: ($_SERVER['MYSQLPORT'] ?? null) ?: getenv('MYSQLPORT') ?: '3306';
}

// 最終雙重檢查
if (empty($host)) {
    die("嚴重錯誤：網頁伺服器完全抓不到 MYSQLHOST 或 MYSQL_URL 環境變數。請檢查 Railway 的 Variables 設定。");
}

// 建立連線
$conn = new mysqli($host, $user, $pass, $db, (int)$port);

if ($conn->connect_error) {
    die("資料庫連線失敗: " . $conn->connect_error);
}

$conn->set_charset("utf8");
?>
