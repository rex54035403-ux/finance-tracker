<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

$host = getenv('DB_HOST') ?: "localhost";
$user = getenv('DB_USER') ?: "root";
$pass = getenv('DB_PASS') ?: "";
$db   = getenv('DB_NAME') ?: "finance_db";

$conn = new mysqli($host, $user, $pass, $db);
$conn->set_charset("utf8");

if ($conn->connect_error) { die("連線失敗: " . $conn->connect_error); }
?>
