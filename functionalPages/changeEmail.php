<?php
session_start();
// 检查用户是否已登录
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// 获取用户邮箱
require_once '../dbConfig.php';
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
    <link rel="stylesheet" href="../css/common1.css">
    <meta charset="utf-8">
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