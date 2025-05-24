<?php
include '../components/connect.php';
session_start();

// Kiểm tra đăng nhập admin
if (!isset($_SESSION["user_id"])) {
   header("Location:admin_login");
   exit();
}

if(isset($_POST['submit'])){

   // Lấy và xử lý dữ liệu đầu vào
   $tenKH = htmlspecialchars(trim($_POST['tenKH']));
   $sdt = preg_replace('/[^0-9]/', '', $_POST['sdt']);
   $diachi = htmlspecialchars(trim($_POST['diachi']));
   $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);

   // Kiểm tra hợp lệ
   if(empty($tenKH) || strlen($tenKH) < 5){
      $message[] = 'Tên khách hàng phải từ 5 ký tự!';
   }
   elseif(!preg_match('/^[0-9]{10}$/', $sdt)){
      $message[] = 'Số điện thoại phải có 10 chữ số!';
   }
   elseif(!$email){
      $message[] = 'Email không hợp lệ!';
   }
   else{
      // Kiểm tra trùng email hoặc số điện thoại
      $check = $conn->prepare("SELECT * FROM `khachhang` WHERE Email = ? OR SDT = ?");
      $check->execute([$email, $sdt]);

      if($check->rowCount() > 0){
         $message[] = 'Email hoặc SĐT đã tồn tại!';
      }
      else{
         // Sinh mã KH mới KH001, KH002,... bằng cách tìm mã KH lớn nhất
         $stmt = $conn->query("SELECT MaKH FROM khachhang ORDER BY MaKH DESC LIMIT 1");
         $lastMaKH = $stmt->fetchColumn();

         if ($lastMaKH) {
            $so = (int)substr($lastMaKH, 2) + 1;
            $maKH = 'KH' . str_pad($so, 3, '0', STR_PAD_LEFT);
         } else {
            $maKH = 'KH001';
         }

         // Thêm dữ liệu
         try {
            $insert = $conn->prepare("INSERT INTO `khachhang` (MaKH, TenKH, SDT, Diachi, Email) VALUES (?, ?, ?, ?, ?)");
            $insert->execute([$maKH, $tenKH, $sdt, $diachi, $email]);

            $message[] = 'Đăng ký thành công! Mã KH: ' . $maKH;
            header("refresh:2;url=users_accounts.php");
            exit();

         } catch(PDOException $e) {
            if ($e->getCode() == 23000) {
               $message[] = 'Mã khách hàng đã tồn tại! Vui lòng thử lại.';
            } else {
               $message[] = 'Lỗi hệ thống: ' . $e->getMessage();
            }
         }
      }
   }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Đăng kí khách hàng mới</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>

<?php include '../components/admin_header.php' ?>

<section class="form-container">
   <form action="" method="post">
      <h3>Đăng ký khách hàng mới</h3>

        <!-- Hiển thị thông báo -->
   <?php if (!empty($message) && is_array($message)): ?>
   <div class="message <?php echo (strpos($message[0], 'thành công') !== false) ? 'success' : 'error'; ?>">
      <?php foreach ($message as $msg): ?>
         <span><?php echo $msg; ?></span>
         <i onclick="this.parentElement.style.display='none';">&times;</i>
      <?php endforeach; ?>
   </div>
<?php endif; ?>

      <div class="order_table">
         <span style="min-width: 160px; font-size:1.8rem;">Tên khách hàng:</span>
         <input type="text" name="tenKH" minlength="5" required class="box" placeholder="Nhập tên khách hàng">
      </div>

      <div class="order_table">
         <span style="min-width: 160px; font-size:1.8rem;">Số điện thoại:</span>
         <input type="tel" name="sdt" pattern="[0-9]{10}" required class="box" placeholder="10 chữ số">
      </div>

      <div class="order_table">
         <span style="min-width: 160px; font-size:1.8rem;">Địa chỉ:</span>
         <input type="text" name="diachi" required class="box" placeholder="Nhập địa chỉ">
      </div>

      <div class="order_table">
         <span style="min-width: 160px; font-size:1.8rem;">Email:</span>
         <input type="email" name="email" required class="box" placeholder="example@gmail.com">
      </div>

      <div class="flex-btn">
         <input type="submit" name="submit" value="Đăng ký" class="btn">
         <a href="users_accounts.php" class="option-btn">Quay lại</a>
      </div>
   </form>
</section>

</body>
</html>