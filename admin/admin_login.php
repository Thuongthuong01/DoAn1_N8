<?php
include '../components/connect.php';

session_start();

if(isset($_POST['submit'])){

   // Sửa tên trường từ 'name' thành 'TenAD' để khớp với form
   $TenAD = $_POST['TenAD'];
   $TenAD = filter_var($TenAD, FILTER_SANITIZE_STRING);
   
   // Sửa tên trường từ 'pass' thành 'Pass' để khớp với form
   $Pass = sha1($_POST['Pass']);
   $Pass = filter_var($Pass, FILTER_SANITIZE_STRING);

   $select_admin = $conn->prepare("SELECT MaAD, TenAD FROM quantri WHERE TenAD = ? AND Pass = ?");
   $select_admin->execute([$TenAD, $Pass]);
   
   if($select_admin->rowCount() > 0){
      $fetch_admin_id = $select_admin->fetch(PDO::FETCH_ASSOC);
      $_SESSION['MaAD'] = $fetch_admin_id['MaAD'];
      error_log("Login successful, admin_id: ".$_SESSION['admin_id']);
      header('location:dashboard.php');
      exit(); // Thêm exit() sau header để đảm bảo chuyển hướng
   }else{
      error_log("Login failed for TenAD: $TenAD");
      $message[] = 'Tài khoản hoặc mật khẩu không đúng!';
      
   }
}
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
if(isset($message)){
   foreach($message as $message){
      echo '
      <div class="message">
         <span>'.$message.'</span>
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
