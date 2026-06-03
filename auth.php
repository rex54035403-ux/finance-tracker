<?php
// 1. 確保 Session 已開啟 (修正了重複警告的問題)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'db/connection.php';

echo "<div style='font-family: sans-serif; padding: 20px;'>";
echo "<h3>--- 系統除錯模式啟動 ---</h3>";

// 嘗試抓取各種常見的表單變數名稱，防止你的 HTML 寫錯
$user = $_POST['username'] ?? $_POST['user'] ?? $_POST['account'] ?? '';
$pass = $_POST['password'] ?? $_POST['pass'] ?? $_POST['pwd'] ?? '';
$action = $_GET['action'] ?? $_POST['action'] ?? 'login';

echo "當前動作: <b>" . htmlspecialchars($action) . "</b><br>";
echo "系統收到的帳號: <b>[" . htmlspecialchars($user) . "]</b><br>";

// 檢查是不是根本沒收到資料
if (empty($user) || empty($pass)) {
    die("<h2 style='color:red;'>錯誤：網頁沒有收到你輸入的帳號或密碼！</h2><p>請檢查你首頁 (index.php) 的表單中，輸入框是否有設定正確的 name 屬性（必須是 name='username' 和 name='password'）。</p></div>");
}

// ==========================================
// 動作 A：處理「註冊」邏輯
// ==========================================
if ($action === 'register') {
    echo "正在檢查帳號是否重複...<br>";
    $check_stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $check_stmt->bind_param("s", $user);
    $check_stmt->execute();
    if ($check_stmt->get_result()->num_rows > 0) {
        die("<h2 style='color:red;'>註冊失敗：這個帳號已經有人用了！</h2><a href='index.php'>點此返回重試</a></div>");
    }

    echo "帳號可用，正在建立新帳號...<br>";
    $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);
    $insert_stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    $insert_stmt->bind_param("ss", $user, $hashed_pass);
    
    if ($insert_stmt->execute()) {
        $_SESSION['user_id'] = $insert_stmt->insert_id;
        echo "<h2 style='color:green;'>🎉 註冊並登入成功！3 秒後為您跳轉到主畫面...</h2></div>";
        header("Refresh: 3; url=main.php"); // 停頓 3 秒再跳轉，讓你看清楚
        exit();
    } else {
        die("<h2 style='color:red;'>系統錯誤：資料庫寫入失敗！</h2></div>");
    }
}

// ==========================================
// 動作 B：處理「登入」邏輯
// ==========================================
else {
    echo "正在進行登入驗證...<br>";
    $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if (!$row) {
        die("<h2 style='color:red;'>登入失敗：找不到這個帳號！</h2><p>請確認你剛才真的有「註冊」成功，而不是按成了登入。</p><a href='index.php'>點此返回</a></div>");
    }

    if (password_verify($pass, $row['password'])) {
        $_SESSION['user_id'] = $row['id'];
        echo "<h2 style='color:green;'>🎉 登入成功！3 秒後為您跳轉到主畫面...</h2></div>";
        header("Refresh: 3; url=main.php");
        exit();
    } else {
        die("<h2 style='color:red;'>登入失敗：密碼錯誤！</h2><a href='index.php'>點此返回</a></div>");
    }
}
echo "</div>";
?>
