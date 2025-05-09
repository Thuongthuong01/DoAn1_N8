<?php
include '../components/connect.php';
session_start();

$admin_id = $_SESSION['admin_id'];
if(!isset($admin_id)){
   header('location:admin_login.php');
}

//Lấy thông tin khách hàng từ ID
if(isset($_GET['id'])){
    $user_id = $_GET['id'];
    $select_user = $conn->prepare("SELECT * FROM `khachhang` WHERE MaKH = ?");
    $select_user->execute([$user_id]);
    $user = $select_user->fetch(PDO::FETCH_ASSOC);
}

// Xử lý cập nhật
if(isset($_POST['update'])){
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $email = $_POST['email'];
    
    $update_user = $conn->prepare("UPDATE `khachhang` SET 
        TenKH = ?, 
        SDT = ?, 
        Diachi = ?, 
        Email = ? 
        WHERE MaKH = ?");
    $update_user->execute([ $name, $phone, $address, $email ,$user_id]);
    
    $message[] = 'Cập nhật thông tin thành công!';
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
      <!-- <input type="hidden" name="id" value="<?= $user['MaKH']; ?>"> -->
      <div class="box">
         <span >Tên khách hàng:</span>
         <input type="text" name="name" value="<?= $user['TenKH']; ?>" required>
      </div>
      
      <div class="box">
         <span>Số điện thoại:</span>
         <input type="text" name="phone" value="<?= $user['SDT']; ?>" required>
      </div>
      
      <div class="box">
         <span>Địa chỉ:</span>
         <input type="text" name="address" value="<?= $user['Diachi']; ?>" required>
      </div>

      <div class="box">
         <span>Email:</span>
         <input type="email" name="email" value="<?= $user['Email']; ?>" required>
      </div>
      <input type="submit" value="Lưu thay đổi" name="update" class="btn">
      <a href="users_accounts.php" class="option-btn">Quay lại</a>
   </form>

<script src="../js/admin_script.js"></script>
</body>
</html>