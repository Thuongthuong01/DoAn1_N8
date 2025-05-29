<?php
include '../components/connect.php';
session_start();

if (!isset($_SESSION["user_id"])) {
   header("Location:admin_login");
   exit();
}

$admin_id = $_SESSION["user_id"];
// $message = [];

// Lấy thông tin hiện tại
$fetch_profile_stmt = $conn->prepare("SELECT TenAD, Email, SDT FROM quantri WHERE MaAD = ?");
$fetch_profile_stmt->execute([$admin_id]);
$fetch_profile = $fetch_profile_stmt->fetch(PDO::FETCH_ASSOC);

if (isset($_POST['submit'])) {

   $email = trim($_POST['email']);
   $sdt = trim($_POST['sdt']);
   $old_pass = sha1($_POST['old_pass']);
   $new_pass = sha1($_POST['new_pass']);
   $confirm_pass = sha1($_POST['confirm_pass']);
   $empty_pass = sha1('');

   $message = [];

  // ======= Kiểm tra số điện thoại =======
   if (!preg_match('/^[0-9]{10}$/', $sdt)) {
      $message[]= '❌ Số điện thoại phải đúng 10 chữ số!';
   } else {
      $check_sdt = $conn->prepare("SELECT * FROM quantri WHERE SDT = ? AND MaAD != ?");
      $check_sdt->execute([$sdt, $admin_id]);
      if ($check_sdt->rowCount() > 0) {
         $message[] = '❌ Số điện thoại đã được sử dụng!';
      }
   }

  // ======= Kiểm tra email =======
  if (empty($message)) { 
      if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
         $message[] = '❌ Email không hợp lệ!';
      } else {
         $check_email = $conn->prepare("SELECT * FROM quantri WHERE Email = ? AND MaAD != ?");
         $check_email->execute([$email, $admin_id]);
         if ($check_email->rowCount() > 0) {
            $message[]= '❌ Email đã được sử dụng!';
         }
      }
   }
   // ======= Kiểm tra mật khẩu nếu có nhập =======
   if (empty($message) && $old_pass != $empty_pass) {
      $get_pass_stmt = $conn->prepare("SELECT Pass FROM quantri WHERE MaAD = ?");
      $get_pass_stmt->execute([$admin_id]);
      $current_pass = $get_pass_stmt->fetch(PDO::FETCH_ASSOC)['Pass'];

      if ($old_pass != $current_pass) {
         $message[] = '❌ Mật khẩu cũ không đúng!';
      } elseif ($new_pass != $confirm_pass) {
         $message[] = '❌ Mật khẩu mới không khớp!';
      } elseif ($new_pass == $empty_pass) {
         $message[] = '❌ Mật khẩu mới không được để trống!';
      } else {
         $update_pass = $conn->prepare("UPDATE quantri SET Pass = ? WHERE MaAD = ?");
         $update_pass->execute([$new_pass, $admin_id]);
      }
   }

   // ======= Nếu không có lỗi, cập nhật thông tin =======
   if (empty($message)) {
      $update_sdt = $conn->prepare("UPDATE quantri SET SDT = ? WHERE MaAD = ?");
      $update_sdt->execute([$sdt, $admin_id]);

      $update_email = $conn->prepare("UPDATE quantri SET Email = ? WHERE MaAD = ?");
      $update_email->execute([$email, $admin_id]);

      $message[] = '✅ Cập nhật thông tin thành công!';
   }

   // ======= Hiển thị thông báo =======
   // if (!empty($message)) {
   //    echo "<p>$message</p>";
   // }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Cập nhật tài khoản admin </title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>

<?php include '../components/admin_header.php' ?>

<!-- admin profile update section starts  -->

<section class="form-container">
   <form action="" method="POST">
      <h3>Cập nhật tài khoản quản trị viên</h3>
      
      <input type="text" name="name" maxlength="20" class="box" readonly
         value="<?= isset($fetch_profile['TenAD']) ? htmlspecialchars($fetch_profile['TenAD']) : '' ?>">

      <input type="number" name="sdt" maxlength="10" placeholder="Nhập SĐT"
         class="box" oninput="this.value = this.value.replace(/\D/g, '')"
         value="<?= isset($fetch_profile['SDT']) ? htmlspecialchars($fetch_profile['SDT']) : '' ?>">

      <input type="email" name="email" maxlength="50" placeholder="Nhập email"
         class="box"
         value="<?= isset($fetch_profile['Email']) ? htmlspecialchars($fetch_profile['Email']) : '' ?>">

      <input type="password" name="old_pass" maxlength="20" placeholder="Nhập mật khẩu cũ" class="box"
         oninput="this.value = this.value.replace(/\s/g, '')">

      <input type="password" name="new_pass" maxlength="20" placeholder="Nhập mật khẩu mới" class="box"
         oninput="this.value = this.value.replace(/\s/g, '')">

      <input type="password" name="confirm_pass" maxlength="20" placeholder="Nhập lại mật khẩu mới" class="box"
         oninput="this.value = this.value.replace(/\s/g, '')">

      <div class="flex-btn">
         <input type="submit" value="Cập nhật" name="submit" class="btn">
         <a href="admin_accounts.php" class="option-btn">Quay lại</a>
      </div>

      <?php if (!empty($message) && is_array($message)): ?>
         <?php foreach ($message as $msg): ?>
            <div class="message" style="background-color: <?= (strpos($msg, '✅') !== false) ? '#d4edda' : '#f8d7da'; ?>; color: <?= (strpos($msg, '✅') !== false) ? '#155724' : '#721c24'; ?>; border: 1px solid <?= (strpos($msg, '✅') !== false) ? '#c3e6cb' : '#f5c6cb'; ?>; padding: 10px; margin: 10px 0; border-radius: 5px;">
               <span><?= $msg; ?></span>
               <i onclick="this.parentElement.style.display='none';" style="cursor:pointer; float:right;">&times;</i>
            </div>
         <?php endforeach; ?>
      <?php endif; ?>
   </form>
</section>

<!-- custom js file link  -->
<script src="../js/admin_script.js"></script>

</body>
</html>