<?php
ob_start(); // Bật output buffering để tránh lỗi header
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

// Nếu đã đăng nhập, chuyển hướng tới dashboard
if (isset($_SESSION["user_id"]) && !empty($_SESSION["user_id"])) {
    header("Location: dashboard.php");
    exit();
}

// Kết nối cơ sở dữ liệu
include '../components/connect.php';

// Xử lý form đăng nhập
if (isset($_POST['submit'])) {
    // Lọc đầu vào tên người dùng
    $name = trim($_POST['TenAD'] ?? ''); // Loại bỏ khoảng trắng
    $name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); // Mã hóa ký tự đặc biệt
    $pass = $_POST['Pass'] ?? '';

    if ($name && $pass) {
        $pass_hashed = sha1($pass); // Giữ sha1 để tương thích, nhưng nên đổi sang password_hash
        try {
            $select_admin = $conn->prepare("SELECT MaAD FROM `quantri` WHERE TenAD = ? AND Pass = ?");
            $select_admin->execute([$name, $pass_hashed]);

            if ($select_admin->rowCount() > 0) {
                $admin = $select_admin->fetch(PDO::FETCH_ASSOC);
                $_SESSION['user_id'] = $admin['MaAD'];
                header("Location: dashboard.php");
                exit();
            } else {
                $message[] = 'Tài khoản hoặc mật khẩu không đúng!';
            }
        } catch (PDOException $e) {
            $message[] = 'Lỗi cơ sở dữ liệu: ' . htmlspecialchars($e->getMessage());
        }
    } else {
        $message[] = 'Vui lòng nhập đầy đủ thông tin!';
    }
}
ob_end_flush();
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Đăng nhập</title>
   
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>

<?php
if (isset($message)) {
    foreach ($message as $msg) {
        echo '
        <div class="message">
            <span>' . htmlspecialchars($msg) . '</span>
            <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
        </div>
        ';
    }
}
?>

<section class="form-container">
   <form action="" method="POST">
      <h3>Đăng nhập quản trị</h3>
      <input type="text" name="TenAD" maxlength="20" required placeholder="Nhập tài khoản của bạn" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="Pass" maxlength="20" required placeholder="Nhập mật khẩu của bạn" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="submit" value="Đăng nhập ngay" name="submit" class="btn">
   </form>
</section>

</body>
</html>