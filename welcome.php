<!DOCTYPE html>
<html>
<head>
    <title>欢迎</title>
    <meta charset="utf-8">
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

        @keyframes gradient {
            0% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
            100% {
                background-position: 0% 50%;
            }
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
            font-family: 'ChillReunion', sans-serif;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            aspect-ratio: 1;
            border-radius: 10px;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            font-size: 40px;
            color: white;
            text-decoration: none;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            border: 1px solid rgba(255, 255, 255, 0.18);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .grid-item:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.3);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.5);
        }
    </style>
</head>
<body>
    <div class="grid-container">
        <a href="FunctionJump/upload.php" class="grid-item">课表上传<h5>💻</h5></a>
        <div class="grid-item">测试发送<h5>💌</h5></div>
        <div class="grid-item">作为贡献者<h5>👨‍💻</h5></div>
        <div class="grid-item">功能暂未开放</div>
        <div class="grid-item">功能暂未开放</div>
        <div class="grid-item">功能暂未开放</div>
    </div>
</body>
</html>