<?php
// 強制顯示所有的 PHP 錯誤，不要再給 500 白畫面了
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'db/connection.php';

// 門神：沒登入就踢回首頁
if (!isset($_SESSION['user_id'])) { 
    header("Location: index.php"); 
    exit(); 
}

// 確保 user_id 是安全的數字
$user_id = intval($_SESSION['user_id']); 

// 刪除邏輯
if (isset($_GET['delete'])) {
    $stmt = $conn->prepare("DELETE FROM records WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $_GET['delete'], $user_id);
    $stmt->execute();
    header("Location: main.php");
    exit();
}

// 新增邏輯
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['title'])) {
    $record_date = $_POST['record_date'] ?? date('Y-m-d');
    $stmt = $conn->prepare("INSERT INTO records (user_id, title, amount, type, date) VALUES (?, ?, ?, ?, ?)");
    if (!$stmt) { die("<h2 style='color:red;'>新增錯誤: " . $conn->error . "</h2>"); }
    $stmt->bind_param("isdss", $user_id, $_POST['title'], $_POST['amount'], $_POST['type'], $record_date);
    $stmt->execute();
    header("Location: main.php");
    exit();
}

// 查詢資料：加上嚴格的錯誤檢查，如果出錯直接印出紅字
$q_expense = $conn->query("SELECT SUM(amount) as total FROM records WHERE user_id = $user_id AND type = 'expense'");
if (!$q_expense) die("<h2 style='color:red;'>SQL 錯誤 (計算支出): " . $conn->error . "</h2>");
$total_expense = $q_expense->fetch_assoc()['total'] ?? 0;

$q_income = $conn->query("SELECT SUM(amount) as total FROM records WHERE user_id = $user_id AND type = 'income'");
if (!$q_income) die("<h2 style='color:red;'>SQL 錯誤 (計算收入): " . $conn->error . "</h2>");
$total_income = $q_income->fetch_assoc()['total'] ?? 0;

// 將 date 加上反引號 `` 保護，避免撞到 MySQL 保留字
$result = $conn->query("SELECT * FROM records WHERE user_id = $user_id ORDER BY `date` DESC, id DESC");
if (!$result) die("<h2 style='color:red;'>SQL 錯誤 (讀取歷史紀錄): " . $conn->error . "</h2>");
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <title>理財小幫手</title>
    <style>
        body { font-family: sans-serif; background: #f9f9f9; padding: 20px; }
        .container { max-width: 600px; margin: auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .summary-box { display: flex; justify-content: space-around; background: #333; color: white; padding: 20px; border-radius: 5px; margin-bottom: 20px; }
        .summary-item { text-align: center; width: 45%; }
        .summary-item h4 { margin: 0; color: #aaa; font-weight: normal; font-size: 0.9em; }
        .summary-item h2 { margin: 5px 0 0 0; }
        .income-text { color: #4caf50; }
        .expense-text { color: #f44336; }
        .divider { border-left: 1px solid #555; }
        .record-item { display: flex; justify-content: space-between; padding: 15px 10px; border-bottom: 1px solid #eee; align-items: center; }
        .record-info { display: flex; flex-direction: column; }
        .record-date { font-size: 0.8em; color: #888; margin-top: 5px; }
        .del-btn { color: red; text-decoration: none; font-size: 0.8em; margin-left: 10px; }
    </style>
</head>
<body>
<div class="container">
    <div class="summary-box">
        <div class="summary-item">
            <h4>本月總收入</h4>
            <h2 class="income-text">$<?php echo number_format($total_income); ?></h2>
        </div>
        <div class="divider"></div>
        <div class="summary-item">
            <h4>本月總支出</h4>
            <h2 class="expense-text">$<?php echo number_format($total_expense); ?></h2>
        </div>
    </div>

    <form method="POST">
        <input type="date" name="record_date" value="<?php echo date('Y-m-d'); ?>" required style="width:100%; padding:10px; margin:5px 0; box-sizing: border-box;">
        <input type="text" name="title" placeholder="消費描述" required style="width:100%; padding:10px; margin:5px 0; box-sizing: border-box;">
        <input type="number" name="amount" placeholder="金額" required style="width:100%; padding:10px; margin:5px 0; box-sizing: border-box;">
        <select name="type" style="width:100%; padding:10px; margin:5px 0; box-sizing: border-box;">
            <option value="expense">支出</option>
            <option value="income">收入</option>
        </select>
        <button type="submit" style="width:100%; padding:10px; background:#007bff; color:white; border:none; margin-top:10px; cursor: pointer; border-radius: 5px;">確認記帳</button>
    </form>
    
    <h3 style="margin-top: 30px;">歷史紀錄</h3>
    <?php while($row = $result->fetch_assoc()): ?>
        <div class="record-item">
            <div class="record-info">
                <span style="font-size: 1.1em;"><?php echo htmlspecialchars($row['title']); ?></span>
                <span class="record-date"><?php echo htmlspecialchars($row['date']); ?></span>
            </div>
            <div>
                <span class="<?php echo $row['type'] == 'expense' ? 'expense-text' : 'income-text'; ?>" style="font-weight: bold; font-size: 1.1em;">
                    <?php echo ($row['type'] == 'expense' ? '-' : '+') . '$' . number_format($row['amount']); ?>
                </span>
                <a href="main.php?delete=<?php echo $row['id']; ?>" class="del-btn" onclick="return confirm('確定刪除這筆紀錄嗎？')">[刪除]</a>
            </div>
        </div>
    <?php endwhile; ?>
</div>
</body>
</html>
