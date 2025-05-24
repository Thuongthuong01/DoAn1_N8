<?php
include '../components/connect.php';
session_start();

if (!isset($_SESSION["user_id"])) {
   header("Location:admin_login");
   exit();
}
$MaKH = $_GET['update'];

// Lấy dữ liệu cũ
$stmt = $conn->prepare("SELECT * FROM khachhang WHERE MaKH = ?");
$stmt->execute([$MaKH]);
$supplier = $stmt->fetch(PDO::FETCH_ASSOC);
// Lấy thông tin khách hàng từ ID
if (isset($_GET['id'])) {
    $user_id = $_GET['id'];
    $select_user = $conn->prepare("SELECT * FROM khachhang WHERE MaKH = ?");
    $select_user->execute([$user_id]);
    $user = $select_user->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo "Không tìm thấy khách hàng có mã $user_id";
        exit();
    }
}

// Xử lý cập nhật
if (isset($_POST['update'])) {
    $MaKH = $_POST['MaKH'];
    $TenKH = $_POST['TenKH'];
    $SDT = $_POST['SDT'];
    $Diachi = $_POST['Diachi'];
    $Email = $_POST['Email'];

    $update_query = $conn->prepare("UPDATE khachhang SET TenKH = ?, SDT = ?, Diachi = ?, Email = ? WHERE MaKH = ?");
    $update_result = $update_query->execute([$TenKH, $SDT, $Diachi, $Email, $MaKH]);

    if ($update_result) {
        header("Location: users_accounts.php?success=1");
        exit();
    } else {
        echo "Lỗi khi cập nhật thông tin.";
    }
}
?>


<!DOCTYPE html>
<html lang="vi">
<head>
   <meta charset="UTF-8">
   <title>Chỉnh sửa thông tin</title>

   
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
      <!-- Nếu cần giữ mã KH để xử lý cập nhật -->
      <input type="hidden" class="box"name="MaKH" value="<?= htmlspecialchars($user['MaKH'] ?? '') ?>">

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
