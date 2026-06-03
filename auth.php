<?php
// 1. 確保 Session 已開啟
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. 引入資料庫連線
require_once 'db/connection.php';

// 3. 接收表單傳來的資料與動作
$user = $_POST['username'] ?? '';
$pass = $_POST['password'] ?? '';
// 判斷表單是傳送 register (註冊) 還是 login (登入)
$action = $_GET['action'] ?? 'login'; 

// ==========================================
// 動作 A：處理「註冊」邏輯
// ==========================================
if ($action === 'register') {
    // 檢查帳號是否已經存在
    $check_stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $check_stmt->bind_param("s", $user);
    $check_stmt->execute();
    if ($check_stmt->get_result()->num_rows > 0) {
        die("這個帳號已經有人使用囉！<a href='index.php'>返回重試</a>");
    }

    // 將密碼加密 (非常重要，否則 password_verify 會驗證失敗)
    $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);

    // 新增帳號到資料庫
    $insert_stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    $insert_stmt->bind_param("ss", $user, $hashed_pass);
    
    if ($insert_stmt->execute()) {
        // 註冊成功！直接幫你登入並轉跳主畫面
        $_SESSION['user_id'] = $insert_stmt->insert_id;
        header("Location: main.php");
        exit();
    } else {
        die("註冊失敗，請稍後再試。");
    }
}

// ==========================================
// 動作 B：處理「登入」邏輯 (你原本的程式碼)
// ==========================================
else {
    $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    // 進行登入驗證
    if ($row && password_verify($pass, $row['password'])) {
        // 登入成功
        $_SESSION['user_id'] = $row['id'];
        header("Location: main.php");
        exit();
    } else {
        // 登入失敗
        echo "帳號或密碼錯誤！<a href='index.php'>返回登入頁</a>";
    }
}
?>
