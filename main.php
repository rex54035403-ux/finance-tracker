<?php
// 1. 安全啟動 Session (已移除重複的語法)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'db/connection.php';

// 2. 門神：沒登入就踢回首頁
if (!isset($_SESSION['user_id'])) { 
    header("Location: index.php"); 
    exit(); 
}
$user_id = $_SESSION['user_id'];

// 3. 刪除邏輯
if (isset($_GET['delete'])) {
    $stmt = $conn->prepare("DELETE FROM records WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $_GET['delete'], $user_id);
    $stmt->execute();
    header("Location: main.php");
    exit();
}

// 4. 新增邏輯 (已加入自訂日期功能)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['title'])) {
    // 抓取表單傳來的日期，如果沒填就預設為今天
    $record_date = $_POST['record_date'] ?? date('Y-m-d');
    
    // 將原本寫死的 CURDATE() 改為變數綁定
    $stmt = $conn->prepare("INSERT INTO records (user_id, title, amount, type, date) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("isdss", $user_id, $_POST['title'], $_POST['amount'], $_POST['type'], $record_date);
    $stmt->execute();
    
    // 新增完畢後重新導向，防止使用者重整網頁時重複送出表單
    header("Location: main.php");
    exit();
}

// 5. 查詢資料：分別計算「支出」與「收入」的總和
$total_expense = $conn->query("SELECT SUM(amount) as total FROM records WHERE user_id = $user_id AND type = 'expense'")->fetch_assoc()['total'] ?? 0;
$total_income = $conn->query("SELECT SUM(amount) as total FROM records WHERE user_id = $user_id AND type = 'income'")->fetch_assoc()['total'] ?? 0;

// 依照日期與 ID 排序，最新的紀錄排在最上面
$result = $conn->query("SELECT * FROM records WHERE user_id = $user_id ORDER BY date DESC, id DESC");
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <title>理財小幫手</title>
    <style>
        body { font-family: sans-serif; background: #f9f9f9; padding: 20px; }
        .container { max-width: 600px; margin: auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        
        /* 左右兩格統計區塊樣式 */
        .summary-box { display: flex; justify-content: space-around; background: #333; color: white; padding: 20px; border-radius: 5px; margin-bottom: 20px; }
        .summary-item { text-align: center; width: 45%; }
        .summary-item h4 { margin: 0; color: #aaa; font-weight: normal; font-size: 0.9em; }
        .summary-item h2 { margin: 5px 0 0 0; }
        .income-text { color: #4caf50; } /* 收入用綠色 */
        .expense-text { color: #f44336; } /* 支出用紅色 */
        .divider { border-left: 1px solid #555; }
        
        /* 歷史紀錄列表樣式優化，容納日期顯示 */
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
                    <?php echo ($row['type'] == 'expense' ? '-' : '+') . '$' . number_
