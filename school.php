<?php
// 引入你原本寫好的防彈版資料庫連線
require_once 'db/connection.php';

$message = "";

// 處理問卷表單送出的資料
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['name'])) {
    $stmt = $conn->prepare("INSERT INTO admissions (name, phone, high_school) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $_POST['name'], $_POST['phone'], $_POST['high_school']);
    
    if ($stmt->execute()) {
        $message = "<div style='color: green; font-weight: bold; margin-bottom: 15px;'>🎉 問卷提交成功！已存入資料庫。</div>";
    } else {
        $message = "<div style='color: red; margin-bottom: 15px;'>寫入失敗：" . $conn->error . "</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <title>龍華科技大學 - 作業二</title>
    <style>
        /* 依照作業要求設計的表格排版 */
        body { font-family: "微軟正黑體", sans-serif; padding: 20px; }
        table { width: 800px; margin: auto; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 15px; }
        
        .header-left { font-size: 24px; font-weight: bold; text-align: left; width: 70%; }
        .header-right { text-align: right; width: 30%; }
        
        .sidebar { width: 150px; vertical-align: top; line-height: 2.5; font-size: 18px; }
        .menu-item { display: block; text-decoration: none; color: black; }
        .menu-item:hover { color: blue; }
        
        .content { vertical-align: top; }
        .footer { font-size: 12px; color: #555; }
        
        /* 表單樣式 */
        input[type="text"] { width: 80%; padding: 8px; margin-bottom: 10px; }
        button { padding: 10px 20px; font-size: 16px; cursor: pointer; }
    </style>
</head>
<body>

    <table>
        <tr>
            <td class="header-left">龍華科技大學</td>
            <td class="header-right">校徽</td>
        </tr>
        
        <tr>
            <td class="sidebar">
                <a href="#" class="menu-item">簡介</a>
                <a href="#" class="menu-item">資工系</a>
                <a href="#" class="menu-item">招生</a>
                <a href="#" class="menu-item" style="color: blue; font-weight: bold;">問卷 (目前頁面)</a>
                <a href="#" class="menu-item">email</a>
            </td>
            
            <td class="content">
                <h2>招生資料問卷填寫</h2>
                
                <?php echo $message; ?>
                
                <form method="POST">
                    <p>姓名：<br><input type="text" name="name" required></p>
                    <p>聯絡電話：<br><input type="text" name="phone" required></p>
                    <p>畢業高中：<br><input type="text" name="high_school" required></p>
                    <button type="submit">送出問卷</button>
                </form>
            </td>
        </tr>
        
        <tr>
            <td colspan="2" class="footer">
                版權宣告(Copyright©)<br><br>
                例子: © 1995-2026 台灣三星電子股份有限公司 版權所有 (此為龍華科技大學資工系作業範例)
            </td>
        </tr>
    </table>

</body>
</html>
