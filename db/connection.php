<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

session_start();
require_once 'db/connection.php';
if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit(); }
$user_id = $_SESSION['user_id'];

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
    $stmt = $conn->prepare("INSERT INTO records (user_id, title, amount, type, date) VALUES (?, ?, ?, ?, CURDATE())");
    $stmt->bind_param("isds", $user_id, $_POST['title'], $_POST['amount'], $_POST['type']);
    $stmt->execute();
}

// 查詢資料：分別計算「支出」與「收入」的總和
$total_expense = $conn->query("SELECT SUM(amount) as total FROM records WHERE user_id = $user_id AND type = 'expense'")->fetch_assoc()['total'] ?? 0;
$total_income = $conn->query("SELECT SUM(amount) as total FROM records WHERE user_id = $user_id AND type = 'income'")->fetch_assoc()['total'] ?? 0;

$result = $conn->query("SELECT * FROM records WHERE user_id = $user_id ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <title>理財小幫手</title>
    <style>
        body { font-family: sans-serif; background: #f9f9f9; padding: 20px; }
        .container { max-width: 600px; margin: auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        
        /* 新增的左右兩格統計區塊樣式 */
        .summary-box { display: flex; justify-content: space-around; background: #333; color: white; padding: 20px; border-radius: 5px; margin-bottom: 20px; }
        .summary-item { text-align: center; width: 45%; }
        .summary-item h4 { margin: 0; color: #aaa; font-weight: normal; font-size: 0.9em; }
        .summary-item h2 { margin: 5px 0 0 0; }
        .income-text { color: #4caf50; } /* 收入用綠色 */
        .expense-text { color: #f44336; } /* 支出用紅色 */
        .divider { border-left: 1px solid #555; }
        
        .record-item { display: flex; justify-content: space-between; padding: 10px; border-bottom: 1px solid #eee; }
        .del-btn { color: red; text-decoration: none; font-size: 0.8em; }
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
        <input type="text" name="title" placeholder="消費描述" required style="width:100%; padding:10px; margin:5px 0; box-sizing: border-box;">
        <input type="number" name="amount" placeholder="金額" required style="width:100%; padding:10px; margin:5px 0; box-sizing: border-box;">
        <select name="type" style="width:100%; padding:10px; margin:5px 0; box-sizing: border-box;">
            <option value="expense">支出</option>
            <option value="income">收入</option>
        </select>
        <button type="submit" style="width:100%; padding:10px; background:#007bff; color:white; border:none; margin-top:10px; cursor: pointer;">確認記帳</button>
    </form>
    
    <h3>歷史紀錄</h3>
    <?php while($row = $result->fetch_assoc()): ?>
        <div class="record-item">
            <span><?php echo htmlspecialchars($row['title']); ?></span>
            <span>
                <?php /* 幫歷史紀錄的金額也加上千分位逗號 */ ?>
                <?php echo ($row['type'] == 'expense' ? '-' : '+') . '$' . number_format($row['amount']); ?>
                <a href="main.php?delete=<?php echo $row['id']; ?>" class="del-btn" onclick="return confirm('確定刪除？')">[刪除]</a>
            </span>
        </div>
    <?php endwhile; ?>
</div>
</body>
</html>
