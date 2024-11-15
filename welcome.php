<?php
session_start();
// 检查用户是否已登录
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>欢迎</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/common1.css">
    <style>
        .grid-container {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            grid-template-rows: repeat(2, 1fr);
            gap: 20px;
            padding: 20px;
            max-width: 900px;
            width: 90%;
        }


        .grid-item {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            aspect-ratio: 1;
            border-radius: 10px;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            font-size: 40px;
            color: white;
            text-decoration: none;
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.37);
            border: 1px solid rgba(255, 255, 255, 0.18);
            transition: 0.3s;
        }
        
        .grid-item:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.3);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.5);
        }
    </style>
</head>
<body>
    <button class="back-button" onclick="window.location.href='../login.php'">
        <i class="icon fa-solid fa-cat"></i>
        <span class="text">退出登录</span>
    </button>
    <div class="grid-container">
        <a href="functionalPages/uploadFile.php" class="grid-item">课表上传<h5>💻</h5></a>
        <a href="functionalPages/testSend.php" class="grid-item">测试发送<h5>💌</h5></a>
        <a href="functionalPages/joinUs.php" class="grid-item">作为贡献者<h5>👨‍💻</h5></a>
        <a href="functionalPages/changeEmail.php" class="grid-item">修改邮箱<h5>✉️</h5></a>
        <div class="grid-item">功能暂未开放</div>
        <div class="grid-item">功能暂未开放</div>
    </div>
</body>
</html>