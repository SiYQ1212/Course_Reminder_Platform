<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>注册</title>
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
        }

        /* 飞行物样式 */
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

        /* 确保container在飞行物上层 */
        .container {
            width: 450px;
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

        .btn-register {
            background: #4CAF50;  /* 使用绿色作为注册按钮的颜色 */
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-register:hover {
            background: #45a049;
            transform: translateY(-2px);
        }

        .login-link {
            color: #4a90e2;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .login-link:hover {
            color: #357abd;
            text-decoration: underline;
        }

        /* 添加表单验证提示样式 */
        .form-group .hint {
            font-size: 12px;
            color: #888;
            margin-top: 5px;
        }

        /* 添加动画效果 */
        @keyframes slideIn {
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
            animation: slideIn 0.5s ease-out;
        }

        /* 添加输入框图标的样式 */
        .form-group {
            position: relative;
        }

        .form-group i {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #888;
        }

        /* 密码强度指示器 */
        .password-strength {
            height: 5px;
            margin-top: 8px;
            border-radius: 3px;
            background: #eee;
            overflow: hidden;
        }

        .password-strength span {
            display: block;
            height: 100%;
            width: 0;
            transition: all 0.3s ease;
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

        .error-text {
            color: #dc3545;
            font-size: 12px;
            margin-top: 5px;
            display: none;
        }

        .password-toggle {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #666;
        }
        .success-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            display: none;
        }

        .success-message {
            background: white;
            padding: 20px 40px;
            border-radius: 10px;
            font-size: 18px;
            color: #155724;
        }
    </style>
</head>
<body>
    <!-- 飞行物容器 -->
    <div class="flying-elements" id="flyingElements"></div>

    <!-- 添加成功提示遮罩层 -->
    <div class="success-overlay">
        <div class="success-message">注册成功！</div>
    </div>

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
        <h2>创建账号</h2>
        <form action="do_register.php" method="post" id="registerForm">
            <div class="form-group">
                <label>用户名</label>
                <input type="text" name="username" required placeholder="请输入用户名">
                <div class="error-text" id="username-error">该用户名已存在</div>
                <div class="hint">用户名长度3-20个字符</div>
            </div>
            <div class="form-group">
                <label>邮箱地址</label>
                <input type="email" name="email" required placeholder="请输入邮箱地址">
                <div class="hint">请输入有效的邮箱地址</div>
            </div>
            <div class="form-group">
                <label>设置密码</label>
                <input type="password" name="password" required placeholder="请输入密码">
                <span class="password-toggle" onclick="togglePassword(this)">👁️</span>
                <div class="password-strength">
                    <span></span>
                </div>
                <div class="hint">建议使用字母、数字和符号的组合</div>
            </div>
            <div class="form-group">
                <label>确认密码</label>
                <input type="password" name="confirm_password" required placeholder="请再次输入密码">
                <span class="password-toggle" onclick="togglePassword(this)">👁️</span>
                <div class="error-text" id="password-error">两次输入的密码不一致</div>
            </div>
            <div class="btn-group">
                <button type="submit" class="btn-register">注 册</button>
                <a href="login.php" class="login-link">已有账号？立即登录</a>
            </div>
        </form>
    </div>

    <script>
        // 切换密码可见性
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
        // 密码强度检测
        document.querySelector('input[name="password"]').addEventListener('input', function(e) {
            let strength = 0;
            const password = e.target.value;
            const strengthBar = document.querySelector('.password-strength span');
            
            if(password.length >= 8) strength += 25;
            if(password.match(/[a-z]/)) strength += 25;
            if(password.match(/[A-Z]/)) strength += 25;
            if(password.match(/[0-9]/)) strength += 25;
            
            strengthBar.style.width = strength + '%';
            
            if(strength <= 25) {
                strengthBar.style.backgroundColor = '#ff4444';
            } else if(strength <= 50) {
                strengthBar.style.backgroundColor = '#ffbb33';
            } else if(strength <= 75) {
                strengthBar.style.backgroundColor = '#00C851';
            } else {
                strengthBar.style.backgroundColor = '#007E33';
            }
        });

        // 表单验证和提交
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const password = document.querySelector('input[name="password"]').value;
            const confirmPassword = document.querySelector('input[name="confirm_password"]').value;
            
            // 重置错误提示
            document.querySelectorAll('.error-text').forEach(el => el.style.display = 'none');
            
            // 检查密码是否一致
            if(password !== confirmPassword) {
                document.getElementById('password-error').style.display = 'block';
                return;
            }

            // 发送表单数据
            const formData = new FormData(this);
            fetch('do_register.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if(data.status === 'success') {
                    // 显示成功提示
                    document.querySelector('.success-overlay').style.display = 'flex';
                    // 1秒后跳转
                    setTimeout(() => {
                        window.location.href = 'login.php';
                    }, 1000);
                } else if(data.message === 'username_exists') {
                    document.getElementById('username-error').style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });

        // 飞行物生成脚本
        function createFlyingElements() {
            const container = document.getElementById('flyingElements');
            const colors = ['#4a90e2', '#e74c3c', '#2ecc71', '#f1c40f', '#9b59b6'];
            
            function createElement() {
                const element = document.createElement('div');
                element.className = 'flying-element';
                
                const size = Math.random() * 30 + 10;
                const color = colors[Math.floor(Math.random() * colors.length)];
                const startX = Math.random() * window.innerWidth;
                const startY = Math.random() * window.innerHeight;
                
                element.style.width = `${size}px`;
                element.style.height = `${size}px`;
                element.style.left = `${startX}px`;
                element.style.top = `${startY}px`;
                element.style.background = color;
                element.style.boxShadow = `0 0 ${size/2}px ${color}`;
                element.style.animationDelay = `${Math.random() * -20}s`;
                
                container.appendChild(element);
            }

            // 初始创建15个元素
            for(let i = 0; i < 45; i++) {
                createElement();
            }

            // 定期检查并补充元素
            setInterval(() => {
                const currentElements = container.getElementsByClassName('flying-element');
                if (currentElements.length < 15) {
                    createElement();
                }
            }, 3000);
        }

        // 页面加载完成后启动动画
        window.addEventListener('load', createFlyingElements);
    </script>
</body>
</html>