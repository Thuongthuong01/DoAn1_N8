<?php
include '../components/connect.php';

session_start();

if(isset($_POST['submit'])){

   $name = $_POST['TenAD'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $pass = sha1($_POST['Pass']);
   $pass = filter_var($pass, FILTER_SANITIZE_STRING);

   $select_admin = $conn->prepare("SELECT * FROM `quantri` WHERE TenAD = ? AND Pass = ?");
   $select_admin->execute([$name, $pass]);
   
   if($select_admin->rowCount() > 0){
      $fetch_admin_id = $select_admin->fetch(PDO::FETCH_ASSOC);
      $_SESSION['admin_id'] = $fetch_admin_id['MaAD'];
      header('location:dashboard.php');
   }else{
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
   foreach($message as $msg){
      echo '
      <div class="message">
         <span>'.$msg.'</span>
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
