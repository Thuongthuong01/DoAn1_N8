<?php
include '../components/connect.php';

session_start();

if (!isset($_SESSION["user_id"])) {
   header("Location:admin_login");
   exit();
}

if (isset($_POST['submit'])) {
   $maad = filter_var($_POST['maad'], FILTER_SANITIZE_NUMBER_INT);
   $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
   $pass = filter_var(sha1($_POST['pass']), FILTER_SANITIZE_STRING);
   $cpass = filter_var(sha1($_POST['cpass']), FILTER_SANITIZE_STRING);
   $sdt = filter_var($_POST['sdt'], FILTER_SANITIZE_STRING);
   $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);

   $check_admin = $conn->prepare("SELECT * FROM `quantri` WHERE TenAD = ? OR MaAD = ?");
   $check_admin->execute([$name, $maad]);

   if ($check_admin->rowCount() > 0) {
      $message[] = 'Tên tài khoản hoặc Mã Admin đã tồn tại!';
   } else {
      if ($pass != $cpass) {
         $message[] = 'Xác nhận mật khẩu không khớp!';
      } else {
         $insert_admin = $conn->prepare("INSERT INTO `quantri`(MaAD, TenAD, Pass, SDT, Email) VALUES(?,?,?,?,?)");
         $insert_admin->execute([$maad, $name, $cpass, $sdt, $email]);
         $message[] = 'Quản trị viên mới đã được đăng ký!';
         header("Location: admin_accounts.php");
         exit();
      }
   }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Đăng kí admin</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>

<?php include '../components/admin_header.php' ?>

<!-- register admin section starts  -->

<section class="form-container" >
<form action="" method="POST">
   <h3>Đăng ký quản trị viên</h3>
   <div class="order_table">
      <span style="min-width: 160px;font-size:1.8rem;">Mã Admin:</span>
      <input type="number" name="maad" required class="box">
   </div>

   <div class="order_table">
      <span style="min-width: 160px;font-size:1.8rem;">Tên tài khoản:</span>
      <input type="text" name="name" maxlength="50" required class="box" oninput="this.value = this.value.replace(/\s/g, '')">
   </div>

   <div class="order_table">
      <span style="min-width: 160px;font-size:1.8rem;">Mật khẩu:</span>
      <input type="password" name="pass" maxlength="50" required class="box" oninput="this.value = this.value.replace(/\s/g, '')">
   </div>

   <div class="order_table">
      <span style="min-width: 160px;font-size:1.8rem;">Nhập lại mật khẩu:</span>
      <input type="password" name="cpass" maxlength="50" required class="box" oninput="this.value = this.value.replace(/\s/g, '')">
   </div>

   <div class="order_table">
      <span style="min-width: 160px;font-size:1.8rem;">Số điện thoại:</span>
      <input type="text" name="sdt" maxlength="20" required class="box" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
   </div>

   <div class="order_table">
      <span style="min-width: 160px;font-size:1.8rem;">Email:</span>
      <input type="email" name="email" maxlength="100" required class="box">
   </div>

   <div class="flex-btn">
      <input type="submit" value="Đăng ký ngay" name="submit" class="btn">
      <a href="admin_accounts.php" class="option-btn">Quay lại</a>
   </div>
</form>
</section>

<!-- register admin section ends -->


<!-- custom js file link  -->
<script src="../js/admin_script.js"></script>

</body>
</html>