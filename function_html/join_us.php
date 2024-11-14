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

// 处理表单提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $auth_code = $_POST['auth_code'];

    // 读取现有的 JSON 数据
    $filePath = '../json_data/proxy_pool.json';
    $data = file_exists($filePath) ? json_decode(file_get_contents($filePath), true) : [];

    // 追加新数据
    $data[$email] = $auth_code;

    // 将数据写回 JSON 文件
    file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT));

    // 使用模态框提示和重定向
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            var modal = document.getElementById('success-modal');
            modal.style.display = 'block';
            setTimeout(function() {
                modal.style.display = 'none';
                window.location.href = '../welcome.php';
            }, 1000);
        });
    </script>";
}

?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
            justify-content: flex-start;
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
        }

        .submit-btn:hover {
            background-color: #357abd;
            transform: translateY(-2px);
        }

        .page-container {
            display: flex;
            align-items: center;
            gap: 40px;
            margin-left: 15%;
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

        @media (max-width: 1200px) {
            .page-container {
                margin-left: 5%;
            }
        }

        @media (max-width: 1000px) {
            .page-container {
                flex-direction: column;
                align-items: center;
                margin-left: 0;
            }
        }

        /* 模态框样式 */
        #success-modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
        }

        #success-modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 300px;
            text-align: center;
            border-radius: 10px;
        }

        .disclaimer-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            border: 1px solid rgba(255, 255, 255, 0.18);
            width: 500px;
            flex-shrink: 0;
            font-size: 18px;
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
                <h1>免责声明</h1>
                <h3>本项目仅供学习交流使用，不承担任何法律责任。</h3>
            </center>
            <p style="padding-top: 6px;">
                1. 用户上传的邮箱以及对应的授权码，会作为邮件的发送方出现，
                仅会出现邮箱号，不会出现邮箱其他隐私信息。<br>
            </p>
            <p style="padding-top: 6px;">
                2. 用户上传的授权码如有错误，会在某一次尝试发送邮件的时候，
                发现错误并会从邮箱代理池中移除，用户可以重新上传正确的授权码。<br>
            </p>
            <p style="padding-top: 6px;">
                3. 用户上传的邮箱以及授权码，仅用于本项目，不会用于其他任何用途。<br>
            </p>
            <p style="padding-top: 6px;">
                4. 本平台暂时的客服支持为QQ<span style="color: #8a20e2; font-size: 22px;">2668733873</span>,请通过QQ联系。<br>
            </p>
            <p style="padding-top: 6px;">
                5. 我们会采取合理措施保护用户的个人信息，但不对因黑客攻击、通讯线路中断等不可抗力因素导致的信息泄露承担责任。<br>
            </p>    
            <p style="padding-top: 6px;">
                6. 用户应遵守中华人民共和国相关法律法规，不得利用本平台从事违法违规活动。<br>
            </p>
        </div>
        <div class="container">
            <form action="#" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="email">贡献邮箱：</label>
                    <input type="email" id="email" name="email" required 
                           readonly value="<?php echo htmlspecialchars($userEmail); ?>" 
                           style="opacity: 0.6;">
                </div>
                
                <div class="form-group">
                    <label for="auth_code">授权码：</label>
                    <input type="text" id="auth_code" name="auth_code" required placeholder="请输入授权码">
                </div>
                
                <button type="submit" class="submit-btn">提交</button>
            </form>
            <div style="text-align: center; margin-top: 20px;">
                <a href="get_auth_code.php">如何获取授权码</a>
            </div>
        </div>
    </div>

    <!-- 模态框 -->
    <div id="success-modal">
        <div id="success-modal-content">
            <p>提交成功！</p>
        </div>
    </div>
</body>
</html>