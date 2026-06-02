<?php
// 請將引號內的內容替換成你 MySQL 變數畫面的真實數值
$host = "mysql.railway.internal"; 
$user = "root";
$pass = "moAjGDttMEbsmJvBmxPDRvvwZoTbkvVs"; // 請確保這是你後台看到的完整密碼
$db   = "railway";
$port = "3306";

$conn = new mysqli($host, $user, $pass, $db, $port);
$conn->set_charset("utf8");

if ($conn->connect_error) {
    die("連線失敗: " . $conn->connect_error);
}
?>
