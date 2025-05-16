<?php

include '../components/connect.php';

session_start();

if (!isset($_SESSION["user_id"])) {
   header("Location:admin_login");
   exit();
}

if(isset($_POST['submit'])){
   // 1. Lấy và validate dữ liệu
   $tenKH = htmlspecialchars(trim($_POST['tenKH']));
   $sdt = preg_replace('/[^0-9]/', '', $_POST['sdt']);
   $diachi = htmlspecialchars(trim($_POST['diachi']));
   $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);

   // 2. Kiểm tra ràng buộc
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
      // 3. Kiểm tra trùng Email/SĐT
      $check = $conn->prepare("SELECT * FROM `khachhang` WHERE Email = ? OR SDT = ?");
      $check->execute([$email, $sdt]);
      
      if($check->rowCount() > 0){
         $message[] = 'Email hoặc SĐT đã tồn tại!';
      }
      else{
         // 4. TẠO MÃ KH (tùy chọn 1 trong 3 cách trên)
         $maKH = 'KH' . str_pad($conn->query("SELECT COUNT(*) FROM `khachhang`")->fetchColumn() + 1, 3, '0', STR_PAD_LEFT);
         
         // 5. Thêm vào database
         try {
            $insert = $conn->prepare("INSERT INTO `khachhang` (MaKH, TenKH, SDT, Diachi, Email) VALUES (?, ?, ?, ?, ?)");
            $insert->execute([$maKH, $tenKH, $sdt, $diachi, $email]);
            
            $message[] = 'Đăng ký thành công! Mã KH: ' . $maKH;
            header("refresh:2;url=users_accounts.php");
         } catch(PDOException $e) {
            $message[] = 'Lỗi hệ thống: ' . $e->getMessage();
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
     <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

<!-- custom css file link  -->
<link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>
<?php include '../components/admin_header.php' ?>

<section class="form-container">
<form action="" method="post">
   <h3>Đăng ký khách hàng mới</h3>
   <div class="box">
      <span>Tên khách hàng:</span>
      <input type="text" name="tenKH" minlength="5" required placeholder="Nhập tên ">
   </div>
   
   <div class="box">
      <span>Số điện thoại:</span>
      <input type="tel" name="sdt" pattern="[0-9]{10}" required placeholder="Nhập 10 chữ số">
   </div>
   
   <div class="box">
      <span>Địa chỉ:</span>
      <input type="text" name="diachi" required placeholder="Nhập địa chỉ">
   </div>
   
   <div class="box">
      <span>Email:</span>
      <input type="email" name="email" required  placeholder="Nhập email hợp lệ">
   </div>
   
   <input type="submit" name="submit" value="Đăng ký" class="btn">
   <a href="users_accounts.php" class="option-btn">Quay lại</a>
</form>
</section>
</body>
</html>