<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>登录</title>
    <meta charset="utf-8">
    <style>
        @font-face {
            font-family: 'ChillReunion';
            src: url('fonts/ChillReunion_Sans.otf') format('opentype');
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'ChillReunion', sans-serif;
        }

        body {
            background-image: url('images/background.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f0f2f5;
        }

        .container {
            width: 400px;
            padding: 40px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            position: relative;
            z-index: 1;
        }

        .container h2 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
            font-size: 2em;
            font-weight: 600;
        }

        .form-group {
            margin-bottom: 25px;
            position: relative;
        }

        .form-group label {
            display: block;
            color: #666;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.3s ease;
            outline: none;
        }

        .form-group input:focus {
            border-color: #4a90e2;
            box-shadow: 0 0 10px rgba(74, 144, 226, 0.1);
        }

        .btn-group {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 30px;
        }

        .btn-login {
            background: #4a90e2;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            background: #357abd;
            transform: translateY(-2px);
        }

        .register-link {
            color: #4a90e2;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .register-link:hover {
            color: #357abd;
            text-decoration: underline;
        }

        /* 添加动画效果 */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .container {
            animation: fadeIn 0.5s ease-out;
        }

        .message {
        padding: 10px 15px;
        border-radius: 5px;
        margin-bottom: 20px;
        animation: fadeIn 0.5s ease-out;
        text-align: center;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        /* 添加飞行物样式 */
        .flying-elements {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 0;
            overflow: hidden;
        }

        .flying-element {
            position: absolute;
            width: 20px;
            height: 20px;
            background: rgba(255, 255, 255, 0.5);
            border-radius: 50%;
            pointer-events: none;
            animation: moveAround 20s linear infinite;
        }

        .flying-element::before {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            background: inherit;
            border-radius: inherit;
            animation: pulse 2s ease-out infinite;
        }

        @keyframes moveAround {
            0% {
                transform: translate(0, 0) rotate(0deg);
            }
            25% {
                transform: translate(calc(100vw - 100%), 0) rotate(180deg);
            }
            50% {
                transform: translate(calc(100vw - 100%), calc(100vh - 100%)) rotate(360deg);
            }
            75% {
                transform: translate(0, calc(100vh - 100%)) rotate(540deg);
            }
            100% {
                transform: translate(0, 0) rotate(720deg);
            }
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
                opacity: 1;
            }
            50% {
                transform: scale(1.5);
                opacity: 0.7;
            }
            100% {
                transform: scale(1);
                opacity: 1;
            }
        }
    </style>
</head>
<body>
    <!-- 添加飞行物容器 -->
    <div class="flying-elements" id="flyingElements"></div>

    <div class="container">
        <?php
        if (isset($_SESSION['error'])) {
            echo '<div class="message error">' . $_SESSION['error'] . '</div>';
            unset($_SESSION['error']);
        }
        if (isset($_SESSION['success'])) {
            echo '<div class="message success">' . $_SESSION['success'] . '</div>';
            unset($_SESSION['success']);
        }
        ?>
        <h2>欢迎登录</h2>
        <form action="check_login.php" method="post">
            <div class="form-group">
                <label>学号：</label>
                <input type="text" name="username" required 
                    value="<?php echo isset($_SESSION['login_username']) ? htmlspecialchars($_SESSION['login_username']) : ''; ?>">
            </div>
            <div class="form-group">
                <label>密码：</label>
                <input type="password" name="password" required
                    value="<?php echo isset($_SESSION['login_password']) ? htmlspecialchars($_SESSION['login_password']) : ''; ?>">
                <span class="password-toggle" onclick="togglePassword(this)">👁️</span>
            </div>
            <div class="btn-group">
                <button type="submit" class="btn-login">登 录</button>
                <a href="register.php" class="register-link">还没有账号？立即注册</a>
            </div>
        </form>
    </div>

    <script>
        function togglePassword(element) {
            const input = element.previousElementSibling;
            if (input.type === 'password') {
                input.type = 'text';
                element.textContent = '🔒';
            } else {
                input.type = 'password';
                element.textContent = '👁️';
            }
        }

        /* 添加飞行物生成脚本 */
        function createFlyingElements() {
            const container = document.getElementById('flyingElements');
            const colors = ['#4a90e2', '#e74c3c', '#2ecc71', '#f1c40f', '#9b59b6'];
            
            function createElement() {
                const element = document.createElement('div');
                element.className = 'flying-element';
                
                // 随机大小和颜色
                const size = Math.random() * 30 + 10;
                const color = colors[Math.floor(Math.random() * colors.length)];
                
                // 随机起始位置
                const startX = Math.random() * window.innerWidth;
                const startY = Math.random() * window.innerHeight;
                
                element.style.width = `${size}px`;
                element.style.height = `${size}px`;
                element.style.left = `${startX}px`;
                element.style.top = `${startY}px`;
                element.style.background = color;
                element.style.boxShadow = `0 0 ${size/2}px ${color}`;
                
                // 机动画延迟，使元素不同步移动
                element.style.animationDelay = `${Math.random() * -20}s`;
                
                container.appendChild(element);
            }

            // 初始创建多个元素
            for(let i = 0; i < 45; i++) {
                createElement();
            }

            // 每隔一段时间检查并补充元素
            setInterval(() => {
                const currentElements = container.getElementsByClassName('flying-element');
                if (currentElements.length < 15) {
                    createElement();
                }
            }, 3000);
        }

        /* 页面加载完成后启动动画 */
        window.addEventListener('load', createFlyingElements);
    </script>
</body>
</html> 