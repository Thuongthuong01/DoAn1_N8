<?php
include '../components/connect.php';
session_start();

if (!isset($_SESSION["user_id"])) {
   header("Location: admin_login");
   exit();
}

if (!isset($_GET['update']) || empty($_GET['update'])) {
   header("Location: users_accounts.php?error=missing_id");
   exit();
}

$MaKH = $_GET['update'];
$message = [];

// Lấy dữ liệu cũ của khách hàng
$stmt = $conn->prepare("SELECT * FROM khachhang WHERE MaKH = ?");
$stmt->execute([$MaKH]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
   echo "❌ Không tìm thấy khách hàng có mã $MaKH.";
   exit();
}

if (isset($_POST['update'])) {
   $TenKH = trim($_POST['TenKH']);
   $SDT   = trim($_POST['SDT']);
   $Diachi = trim($_POST['Diachi']);
   $Email = trim($_POST['Email']);

   // ======= Kiểm tra số điện thoại =======
   if (!preg_match('/^[0-9]{10}$/', $SDT)) {
      $message[] = '❌ Số điện thoại phải đúng 10 chữ số!';
   } else {
      $check_sdt = $conn->prepare("SELECT * FROM khachhang WHERE SDT = ? AND MaKH != ?");
      $check_sdt->execute([$SDT, $MaKH]);
      if ($check_sdt->rowCount() > 0) {
         $message[] = '❌ Số điện thoại đã được sử dụng!';
      }
   }

   // ======= Kiểm tra email =======
   if (empty($message)) {
      if (!filter_var($Email, FILTER_VALIDATE_EMAIL)) {
         $message[] = '❌ Email không hợp lệ!';
      } else {
         $check_email = $conn->prepare("SELECT * FROM khachhang WHERE Email = ? AND MaKH != ?");
         $check_email->execute([$Email, $MaKH]);
         if ($check_email->rowCount() > 0) {
            $message[] = '❌ Email đã được sử dụng!';
         }
      }
   }

   // ======= Kiểm tra nếu không có gì thay đổi =======
   if (empty($message)) {
      if (
         $TenKH === $user['TenKH'] &&
         $SDT === $user['SDT'] &&
         $Diachi === $user['Diachi'] &&
         $Email === $user['Email']
      ) {
         $message[] = 'ℹ️ Không có thay đổi nào để cập nhật!';
      } else {
         // ======= Cập nhật thông tin =======
         $update = $conn->prepare("UPDATE khachhang SET TenKH = ?, SDT = ?, Diachi = ?, Email = ? WHERE MaKH = ?");
         $update->execute([$TenKH, $SDT, $Diachi, $Email, $MaKH]);
         $message[] = '✅ Cập nhật thông tin thành công!';

         // Cập nhật lại dữ liệu hiển thị trong form
         $user['TenKH'] = $TenKH;
         $user['SDT'] = $SDT;
         $user['Diachi'] = $Diachi;
         $user['Email'] = $Email;
      }
   }
}
?>


<!DOCTYPE html>
<html lang="vi">
<head>
   <meta charset="UTF-8">
   <title>Cập nhật tài khoản khách hàng</title>

   
   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>
   
<?php include '../components/admin_header.php' ?>


<section class="form-container">

   <form action="" method="POST">
      <h3>Cập nhật tài khoản khách hàng</h3>
       <div class="order_table">
         <span>Mã khách hàng:</span>
         <input type="text"class="box" name="MaKH" value="<?= htmlspecialchars($user['MaKH'] ?? '') ?>" readonly>
      </div>

      <div class="order_table">
         <span>Tên khách hàng:</span>
         <input type="text"class="box" name="TenKH" value="<?= htmlspecialchars($user['TenKH'] ?? '') ?>" required>
      </div>
      
      <div class="order_table">
         <span>Số điện thoại:</span>
         <input type="text"class="box" name="SDT" value="<?= htmlspecialchars($user['SDT'] ?? '') ?>" required>
      </div>
      
      <div class="order_table">
         <span>Địa chỉ:</span>
         <input type="text"class="box" name="Diachi" value="<?= htmlspecialchars($user['Diachi'] ?? '') ?>" required>
      </div>

      <div class="order_table">
         <span>Email:</span>
         <input type="email" class="box" name="Email" value="<?= htmlspecialchars($user['Email'] ?? '') ?>" required>
      </div>
   <div class="flex-btn">
      <input type="submit" value="Lưu thay đổi" name="update" class="btn">
      <a href="users_accounts.php" class="option-btn">Quay lại</a>
</div>
   </form>

</section>

<script src="../js/admin_script.js"></script>
</body>
</html>
