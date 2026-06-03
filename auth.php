<?php
// 1. 啟動 Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'db/connection.php';

// 2. 接收表單資料
$user = $_POST['username'] ?? '';
$pass = $_POST['password'] ?? '';
$action = $_GET['action'] ?? 'login';

// 如果完全沒抓到帳號密碼，直接停下來報錯
if (empty($user) || empty($pass)) {
    die("<h2 style='color:red;'>錯誤：請輸入帳號與密碼！</h2><p>若持續出現此錯誤，請檢查 index.php 表單的 input 名稱是否為 name='username'。</p><a href='index.php'>返回</a>");
}

// ==========================================
// 動作 A：處理「註冊」
// ==========================================
if ($action === 'register') {
    $check_stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $check_stmt->bind_param("s", $user);
    $check_stmt->execute();
    
    if ($check_stmt->get_result()->num_rows > 0) {
        die("<h2 style='color:red;'>註冊失敗：這個帳號已經有人用了！</h2><a href='index.php'>返回重試</a>");
    }

    $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);
    $insert_stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    $insert_stmt->bind_param("ss", $user, $hashed_pass);
    
    if ($insert_stmt->execute()) {
        $_SESSION['user_id'] = $insert_stmt->insert_id;
        // 註冊成功，使用 JS 強制跳轉，不再依賴 302
        echo "<h2 style='color:green;'>🎉 註冊成功！為您轉跳中...</h2>";
        echo "<script>window.location.href = 'main.php';</script>";
        exit();
    } else {
        die("<h2 style='color:red;'>系統錯誤：註冊寫入失敗！</h2>");
    }
} 
// ==========================================
// 動作 B：處理「登入」
// ==========================================
else {
    $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();

    if ($row && password_verify($pass, $row['password'])) {
        $_SESSION['user_id'] = $row['id'];
        // 登入成功，使用 JS 強制跳轉
        echo "<h2 style='color:green;'>🎉 登入成功！為您轉跳中...</h2>";
        echo "<script>window.location.href = 'main.php';</script>";
        exit();
    } else {
        die("<h2 style='color:red;'>登入失敗：帳號或密碼錯誤！</h2><a href='index.php'>返回登入頁</a>");
    }
}
?>
