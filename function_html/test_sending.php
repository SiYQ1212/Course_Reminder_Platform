<?php
session_start();
// 检查用户是否已登录
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// 获取用户邮箱
require_once '../db_config.php';
$db = Database::getInstance();
$conn = $db->getConnection();

$stmt = $conn->prepare("SELECT email FROM info WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$userEmail = '';
if ($row = $result->fetch_assoc()) {
    $userEmail = $row['email'];
}

// 添加处理表单提交的代码
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    
    // 调用Python脚本
    $command = "python3 ../auxiliary_program/send_test_email.py " . escapeshellarg($email);
    $output = shell_exec($command);
    
    // 可以添加处理返回结果的代码
    if ($output !== null) {
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    // 创建遮罩层
                    var overlay = document.createElement('div');
                    overlay.style.position = 'fixed';
                    overlay.style.top = '0';
                    overlay.style.left = '0';
                    overlay.style.width = '100%';
                    overlay.style.height = '100%';
                    overlay.style.background = 'rgba(0, 0, 0, 0.5)';
                    overlay.style.zIndex = '999';
                    document.body.appendChild(overlay);
                    
                    // 创建提示消息
                    var msg = document.createElement('div');
                    msg.style.position = 'fixed';
                    msg.style.top = '50%';
                    msg.style.left = '50%';
                    msg.style.transform = 'translate(-50%, -50%)';
                    msg.style.padding = '20px 40px';
                    msg.style.background = 'rgba(0, 0, 0, 0.7)';
                    msg.style.color = 'white';
                    msg.style.borderRadius = '5px';
                    msg.style.zIndex = '1000';
                    msg.textContent = '已发送，请稍等！';
                    document.body.appendChild(msg);
                    
                    setTimeout(function() {
                        msg.style.transition = 'opacity 0.5s';
                        overlay.style.transition = 'opacity 0.5s';
                        msg.style.opacity = '0';
                        overlay.style.opacity = '0';
                        window.location.href = '../welcome.php';
                    }, 1000);
                });
            </script>";
    } else {
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    // 创建遮罩层
                    var overlay = document.createElement('div');
                    overlay.style.position = 'fixed';
                    overlay.style.top = '0';
                    overlay.style.left = '0';
                    overlay.style.width = '100%';
                    overlay.style.height = '100%';
                    overlay.style.background = 'rgba(0, 0, 0, 0.5)';
                    overlay.style.zIndex = '999';
                    document.body.appendChild(overlay);
                    
                    // 创建提示消息
                    var msg = document.createElement('div');
                    msg.style.position = 'fixed';
                    msg.style.top = '50%';
                    msg.style.left = '50%';
                    msg.style.transform = 'translate(-50%, -50%)';
                    msg.style.padding = '20px 40px';
                    msg.style.background = 'rgba(0, 0, 0, 0.7)';
                    msg.style.color = 'white';
                    msg.style.borderRadius = '5px';
                    msg.style.zIndex = '1000';
                    msg.textContent = '发送出错，等待维护';
                    document.body.appendChild(msg);
                    
                    setTimeout(function() {
                        msg.style.transition = 'opacity 0.5s';
                        overlay.style.transition = 'opacity 0.5s';
                        msg.style.opacity = '0';
                        overlay.style.opacity = '0';
                        window.location.href = '../welcome.php';
                    }, 1000);
                });
            </script>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>欢迎</title>
    <meta charset="utf-8">
    <style>
        @font-face {
            font-family: 'ChillReunion';
            src: url('../fonts/ChillReunion_Sans.otf') format('opentype');
        }

        * {
            font-family: 'ChillReunion', sans-serif;
        }

        body {
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
        
        .container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            border: 1px solid rgba(255, 255, 255, 0.18);
            width: 400px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-size: 16px;
            color: #333;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            background: white;
            color: #333;
            transition: all 0.3s ease;
        }

        .form-group input:focus {
            outline: none;
            border-color: #4a90e2;
            box-shadow: 0 0 5px rgba(74, 144, 226, 0.3);
        }

        .form-group input::placeholder {
            color: #999;
        }

        .form-group input[type="file"] {
            padding: 8px;
            background: white;
            color: #333;
        }

        .submit-btn {
            background-color: #4a90e2;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            transition: all 0.3s ease;
            padding: 20px 20px;
            text-align: center;
        }

        .submit-btn:hover {
            background-color: #357abd;
            transform: translateY(-2px);
        }

        .page-container {
            display: flex;
            align-items: stretch;
            gap: 40px;
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }

        .demo-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            border: 1px solid rgba(255, 255, 255, 0.18);
            display: flex;
            flex-direction: column;
            justify-content: center;
            width: 400px;
        }

        .demo-container img {
            max-width: 100%;
            height: auto;
            border-radius: 5px;
            object-fit: contain;
        }

        .demo-title {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
            font-size: 16px;
        }

        @media (max-width: 900px) {
            .page-container {
                flex-direction: column;
                align-items: center;
            }

            .demo-container {
                width: 100%;
                max-width: 400px;
            }
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
    </style>
</head>
<body>
<button class="back-button" onclick="window.history.back();"">
        <i class="icon fa-solid fa-cat"></i>
        <span class="text">返回上页</span>
    </button>
    <div class="page-container">
    <div class="disclaimer-container">
            <center>    
                <h1>测试说明</h1>
            </center>
            <p style="padding-top: 6px;">
                1. 用户没有上传课表，邮件默认认为您没有接收权限，会发送一个无权限提示邮件。
                只能证明您的邮箱可以使用。<br>
            </p>
            <p style="padding-top: 6px;">
                2. 用户没有上传课表，同时邮箱不正确，则不会收到邮箱。<br>
            </p>
            <p style="padding-top: 6px;">
                3. 用户上传了课表，但是课表格式不正确导致PDF解析失败，则会发送无权限提示邮件，
                并且该邮箱会被删除，之后无法接收邮件。解决办法就是重新上传正确的课表。<br>
            </p>
            <p style="padding-top: 6px;">
                4. 用户上传了课表，且课表格式正确，但是明日无课，会发送一个无课测试邮件。<br>
            </p>
            <p style="padding-top: 6px;">
                5. 用户上传了课表，且课表格式正确，且明日有课，会发送一个明日课表测试邮件。<br>
            </p>
        </div>
        <div class="container">
            <form action="#" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="email">接收邮箱：</label>
                    <input type="email" id="email" name="email" required 
                           readonly value="<?php echo htmlspecialchars($userEmail); ?>" 
                           style="opacity: 0.6;">
                </div>
                    <button type="submit" class="submit-btn">提交</button>
            </form>
        </div>

    </div>
</body>
</html>