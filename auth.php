<?php
// 1. 確保 Session 已開啟
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. 引入資料庫連線
// 將原本的 require_once('connection.php'); 改為：
require_once('db/connection.php');

// 3. 接收表單傳來的資料
$user = $_POST['username'] ?? '';
$pass = $_POST['password'] ?? '';

// 4. 準備 SQL 語句避免 SQL Injection
$stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
$stmt->bind_param("s", $user);
$stmt->execute();
$result = $stmt->get_result();

// 5. 先將結果存入 $row，再判斷是否有抓到資料
$row = $result->fetch_assoc();

// 6. 進行登入驗證
// 確保 $row 有值且密碼驗證通過
if ($row && password_verify($pass, $row['password'])) {
    // 登入成功，將 user_id 存入 Session
    $_SESSION['user_id'] = $row['id'];
    
    // 轉跳到主頁面 (請確保路徑正確)
    header("Location: main.php");
    exit();
} else {
    // 登入失敗處理
    echo "帳號或密碼錯誤！<a href='index.php'>返回登入頁</a>";
}
?>
