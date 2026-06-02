<?php
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

// 查詢資料
$total = $conn->query("SELECT SUM(amount) as total FROM records WHERE user_id = $user_id AND type = 'expense'")->fetch_assoc()['total'] ?? 0;
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
        .total-box { background: #333; color: white; padding: 20px; text-align: center; border-radius: 5px; }
        .record-item { display: flex; justify-content: space-between; padding: 10px; border-bottom: 1px solid #eee; }
        .del-btn { color: red; text-decoration: none; font-size: 0.8em; }
    </style>
</head>
<body>
<div class="container">
    <div class="total-box">本月總支出 <h1>$<?php echo number_format($total); ?></h1></div>
    <form method="POST">
        <input type="text" name="title" placeholder="消費描述" required style="width:100%; padding:10px; margin:5px 0;">
        <input type="number" name="amount" placeholder="金額" required style="width:100%; padding:10px; margin:5px 0;">
        <select name="type" style="width:100%; padding:10px;"><option value="expense">支出</option><option value="income">收入</option></select>
        <button type="submit" style="width:100%; padding:10px; background:#007bff; color:white; border:none; margin-top:10px;">確認記帳</button>
    </form>
    <h3>歷史紀錄</h3>
    <?php while($row = $result->fetch_assoc()): ?>
        <div class="record-item">
            <span><?php echo htmlspecialchars($row['title']); ?></span>
            <span>
                <?php echo ($row['type'] == 'expense' ? '-' : '+') . '$' . $row['amount']; ?>
                <a href="main.php?delete=<?php echo $row['id']; ?>" class="del-btn" onclick="return confirm('確定刪除？')">[刪除]</a>
            </span>
        </div>
    <?php endwhile; ?>
</div>
</body>
</html>
