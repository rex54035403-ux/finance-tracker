
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <title>理財小幫手 - 登入</title>
    <style>
        body { font-family: sans-serif; display: flex; justify-content: center; padding-top: 100px; background-color: #f4f4f9; }
        .login-card { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); width: 300px; }
        input { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; }
    </style>
</head>
<body>
    <div class="login-card">
        <h2>💰 理財小幫手</h2>
        <form action="auth.php" method="POST">
            <input type="text" name="username" required placeholder="帳號">
            <input type="password" name="password" required placeholder="密碼">
            <button type="submit">登入系統</button>
        </form>
        <p style="text-align:center; margin-top:15px;">
            <a href="register.php">沒有帳號？一鍵測試註冊</a>
        </p>
    </div>
</body>
</html>
