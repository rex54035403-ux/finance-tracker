<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// 嘗試抓取環境變數，如果沒有（在本地），就使用後面的預設值
$host = getenv('DB_HOST') ?: "localhost";
$user = getenv('DB_USER') ?: "root";
$pass = getenv('DB_PASS') ?: "";
$db   = getenv('DB_NAME') ?: "finance_db";

// 如果是使用 MySQL (XAMPP環境)
$conn = new mysqli($host, $user, $pass, $db);

// 若之後改用 PostgreSQL (Supabase)，這裡再換成 PDO 寫法即可
$conn->set_charset("utf8");
if ($conn->connect_error) { die("連線失敗: " . $conn->connect_error); }
?>