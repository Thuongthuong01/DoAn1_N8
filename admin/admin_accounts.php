<?php


include '../components/connect.php';

session_start();

if (!isset($_SESSION["user_id"])) {
   header("Location:admin_login");
   exit();
}

if(isset($_GET['delete'])){
   $delete_id = $_GET['delete'];
   $delete_admin = $conn->prepare("DELETE FROM `admin` WHERE id = ?");
   $delete_admin->execute([$delete_id]);
   header('location:admin_accounts.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Quản lý quản trị viên</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>

<?php include '../components/admin_header.php' ?>

<!-- admins accounts section starts  -->
<!-- 
<section class="accounts">

   <h1 class="heading">Danh sách tài khoản</h1>

   <div class="box-container">

   <div class="box">
      <p>Đăng ký tài khoản mới</p>
      <a href="register_admin.php" class="option-btn">Đăng ký</a>
   </div>

   <?php
      $select_account = $conn->prepare("SELECT * FROM `quantri`");
      $select_account->execute();
      if($select_account->rowCount() > 0){
         while($fetch_accounts = $select_account->fetch(PDO::FETCH_ASSOC)){  
   ?>
   <div class="box">
      <p> ID : <span><?= $fetch_accounts['MaAD']; ?></span> </p>
      <p> Tài khoản : <span><?= $fetch_accounts['TenAD']; ?></span> </p>
      <p> SĐT : <span><?= $fetch_accounts['SDT']; ?></span> </p>
      <p> Email : <span><?= $fetch_accounts['Email']; ?></span> </p>
      <div class="flex-btn">
         <a href="admin_accounts.php?delete=<?= $fetch_accounts['MaAD']; ?>" class="delete-btn" onclick="return confirm('Xoá tài khoản?');">Xoá</a>
         <?php
            if($fetch_accounts['MaAD'] == $admin_id){
               echo '<a href="update_profile.php" class="option-btn">Chỉnh sửa</a>';
            }
         ?>
      </div>
   </div>
   <?php
      }
   }else{
      echo '<p class="empty">Không có tài khoản nào khả dụng</p>';
   }
   ?>

   </div>
</section> -->


<section class="accounts">

   <h1 class="heading">Tài khoản quản trị viên</h1>

   <div class="box-container">

   <div class="box">
      <p>Đăng ký tài khoản mới</p>
      <a href="register_admin.php" class="option-btn">Đăng ký</a>
   </div>
</section>
<section class="main-content show-products" style="padding-top: 0;">
<h1 class="heading">Danh sách quản trị</h1>
   <table class="product-table">
      <thead>
         <tr>
            <th>ID</th>
            <th>Tên tài khoản</th>
            <th>SĐT</th>
            <th>Email</th>
            <th>Chức năng</th>
         </tr>
      </thead>
      <tbody>
      <?php
            $show_admins = $conn->prepare("SELECT * FROM quantri");
            $show_admins->execute();
            if ($show_admins->rowCount() > 0) {
               while ($fetch_admin = $show_admins->fetch(PDO::FETCH_ASSOC)) {
         ?>
         <tr>
            <td><?= $fetch_admin['MaAD']; ?></td>
            <td><?= $fetch_admin['TenAD']; ?></td>
            <td><?= $fetch_admin['SDT']; ?></td>
            <td><?= $fetch_admin['Email']; ?></td>
            <td>
               <a href="update_profile_admin.php?update=<?= $fetch_admin['MaAD']; ?>" class="btn btn-update">Cập nhật</a>
               <a href="quantri.php?delete=<?= $fetch_admin['MaAD']; ?>" class="btn btn-delete" onclick="return confirm('Xoá sản phẩm?');">Xoá</a>
            </td>
         </tr>
         <?php
               }
            } else {
               echo '<tr><td colspan="8" class="empty">Chưa có sản phẩm nào được thêm vào!</td></tr>';
            }
         ?>
      </tbody>
   </table>
</section>



















<!-- custom js file link  -->
<script src="../js/admin_script.js"></script>

</body>
</html>