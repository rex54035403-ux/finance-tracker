<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// 修正：指向 db 資料夾內的檔案
require_once('db/connection.php');

$user = $_POST['username'] ?? '';
$pass = $_POST['password'] ?? '';

$stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
$stmt->bind_param("s", $user);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($row && password_verify($pass, $row['password'])) {
    $_SESSION['user_id'] = $row['id'];
    header("Location: main.php");
    exit();
} else {
    echo "帳號或密碼錯誤！ <a href='index.php'>返回登入頁</a>";
}
?>
