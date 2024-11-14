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
    <style>
        @font-face {
            font-family: 'ChillReunion';
            src: url('fonts/ChillReunion_Sans.otf') format('opentype');
        }

        body {
            font-family: 'ChillReunion', sans-serif;
            margin: 0;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(-45deg, #ee7752, #e73c7e, #23a6d5, #23d5ab);
            background-size: 400% 400%;
            animation: gradient 15s ease infinite;
        }

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

        .back-button {
            font-family: 'ChillReunion', sans-serif;
            position: absolute;
            top: 20px;
            left: -160px;
            background-color: #ff6b6b;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 0 50px 50px 0;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            width: 240px;
            height: 100px;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            overflow: hidden;
        }

        .back-button .icon {
            font-size: 48px;
            position: absolute;
            right: 15px;
            transition: all 0.3s ease;
        }

        .back-button .text {
            font-family: 'ChillReunion', sans-serif;
            position: absolute;
            right: 65px;
            font-size: 28px;
            opacity: 0;
            transition: all 0.3s ease;
            white-space: nowrap;
        }

        .back-button:hover {
            left: 0;
            background-color: #ff4c4c;
        }

        .back-button:hover .icon {
            right: 180px;
        }

        .back-button:hover .text {
            opacity: 1;
            right: 30px;
        }

        @keyframes gradient {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }

    </style>
</head>
<body>
    <button class="back-button" onclick="window.location.href='../login.php'">
        <i class="icon fa-solid fa-cat"></i>
        <span class="text">退出登录</span>
    </button>
    <div class="grid-container">
        <a href="function_html/upload.php" class="grid-item">课表上传<h5>💻</h5></a>
        <a href="function_html/test_sending.php" class="grid-item">测试发送<h5>💌</h5></a>
        <a href="function_html/join_us.php" class="grid-item">作为贡献者<h5>👨‍💻</h5></a>
        <a href="function_html/change_email.php" class="grid-item">修改邮箱<h5>✉️</h5></a>
        <div class="grid-item">功能暂未开放</div>
        <div class="grid-item">功能暂未开放</div>
    </div>
</body>
</html>