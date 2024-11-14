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
    $newEmail = $_POST['new_email'];
    
    // 更新数据库中的邮箱
    $updateStmt = $conn->prepare("UPDATE info SET email = ? WHERE id = ?");
    $updateStmt->bind_param("si", $newEmail, $_SESSION['user_id']);
    
    if ($updateStmt->execute()) {
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                var modal = document.getElementById('success-modal');
                var modalContent = document.getElementById('success-modal-content').querySelector('p');
                modalContent.textContent = '邮箱更新成功！';
                modal.style.display = 'block';
                setTimeout(function() {
                    modal.style.display = 'none';
                    window.location.href = '../welcome.php';
                }, 1000);
            });
        </script>";
    } else {
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                var modal = document.getElementById('success-modal');
                var modalContent = document.getElementById('success-modal-content').querySelector('p');
                modalContent.textContent = '邮箱更新失败，请稍后重试！';
                modal.style.display = 'block';
                setTimeout(function() {
                    modal.style.display = 'none';
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
            justify-content: center;
            padding: 20px;
            width: 100%;
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
        <div class="container">
            <form action="#" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="old_email">原始邮箱：</label>
                    <input type="email" id="old_email" name="old_email" required 
                           readonly value="<?php echo htmlspecialchars($userEmail); ?>" 
                           style="opacity: 0.6;">
                </div>
                
                <div class="form-group">
                    <label for="new_email">新邮箱：</label>
                    <input type="email" id="new_email" name="new_email" required placeholder="请输入新邮箱">
                </div>
                
                <button type="submit" class="submit-btn">提交</button>
            </form>
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