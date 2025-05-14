<?php

include '../components/connect.php';

session_start();

if (!isset($_SESSION["user_id"])) {
   header("Location:admin_login");
   exit();
}
//Lấy thông tin khách hàng từ ID
if(isset($_GET['id'])){
    $user_id = $_GET['id'];
    $select_user = $conn->prepare("SELECT * FROM `khachhang` WHERE MaKH = ?");
    $select_user->execute([$user_id]);
    $user = $select_user->fetch(PDO::FETCH_ASSOC);
}

// Xử lý cập nhật
// if(isset($_POST['update'])){
//     $name = $_POST['name'];
//     $phone = $_POST['phone'];
//     $address = $_POST['address'];
//     $email = $_POST['email'];
    
//     $update_user = $conn->prepare("UPDATE `khachhang` SET 
//         TenKH = ?, 
//         SDT = ?, 
//         Diachi = ?, 
//         Email = ? 
//         WHERE MaKH = ?");
//     $update_user->execute([ $name, $phone, $address, $email ,$user_id]);
    
//     $message[] = 'Cập nhật thông tin thành công!';
// }
if (isset($_GET['id'])) {
    $user_id = $_GET['id'];
    $select_user = $conn->prepare("SELECT * FROM khachhang WHERE MaKH = ?");
    $select_user->execute([$user_id]);
    $user = $select_user->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $name = $user['TenKH'];
        $sdt = $user['SDT'];
        $diachi = $user['DiaChi'];
        $email = $user['Email'];
    } else {
        echo "Không tìm thấy khách hàng có mã $user_id";
        // Có thể chuyển hướng về trang danh sách
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
<!-- <section class="form-container">

   <form action="" method="POST">
      <h3>Cập nhật tài khoản khách hàng</h3>
      <input type="hidden" name="id" value="<?= $user['MaKH']; ?>"> 
      <div class="box">
         <span >Tên khách hàng:</span>
         <input type="text" name="name" value="<?= $name['TenKH']; ?>" required>
      </div>
      
      <div class="box">
         <span>Số điện thoại:</span>
         <input type="text" name="phone" value="<?= $phone['SDT']; ?>" required>
      </div>
      
      <div class="box">
         <span>Địa chỉ:</span>
         <input type="text" name="address" value="<?= $address['Diachi']; ?>" required>
      </div>

      <div class="box">
         <span>Email:</span>
         <input type="email" name="email" value="<?= $email['Email']; ?>" required>
      </div>
      <input type="submit" value="Lưu thay đổi" name="update" class="btn">
      <a href="users_accounts.php" class="option-btn">Quay lại</a>
   </form>
</section> -->

<section class="form-container">

   <form action="" method="POST">
      <h3>Cập nhật tài khoản khách hàng</h3>
      <!-- Nếu cần giữ mã KH để xử lý cập nhật -->
      <input type="hidden" name="MaKH" value="<?= htmlspecialchars($user['MaKH'] ?? '') ?>">

      <div class="box">
         <span>Tên khách hàng:</span>
         <input type="text" name="TenKH" value="<?= htmlspecialchars($user['TenKH'] ?? '') ?>" required>
      </div>
      
      <div class="box">
         <span>Số điện thoại:</span>
         <input type="text" name="SDT" value="<?= htmlspecialchars($user['SDT'] ?? '') ?>" required>
      </div>
      
      <div class="box">
         <span>Địa chỉ:</span>
         <input type="text" name="DiaChi" value="<?= htmlspecialchars($user['DiaChi'] ?? '') ?>" required>
      </div>

      <div class="box">
         <span>Email:</span>
         <input type="email" name="Email" value="<?= htmlspecialchars($user['Email'] ?? '') ?>" required>
      </div>

      <input type="submit" value="Lưu thay đổi" name="update" class="btn">
      <a href="users_accounts.php" class="option-btn">Quay lại</a>
   </form>

</section>

<script src="../js/admin_script.js"></script>
</body>
</html>
