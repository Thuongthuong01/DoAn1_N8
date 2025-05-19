<?php


include '../components/connect.php';

session_start();

if (!isset($_SESSION["user_id"])) {
   header("Location:admin_login");
   exit();
}

if(isset($_GET['delete'])){
   $delete_id = $_GET['delete'];
   $delete_users = $conn->prepare("DELETE FROM `users` WHERE id = ?");
   $delete_users->execute([$delete_id]);
   $delete_order = $conn->prepare("DELETE FROM `orders` WHERE user_id = ?");
   $delete_order->execute([$delete_id]);
   $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
   $delete_cart->execute([$delete_id]);
   header('location:users_accounts.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Quản lý người dùng </title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">
<style>
   .btn-history {
    padding: 5px 10px;
    background-color: #007bff;
    color: white;
    border-radius: 3px;
    text-decoration: none;
    font-weight: 600;
}

.btn-history:hover {
    opacity: 0.85;
}

   </style>
</head>
<body>

<?php include '../components/admin_header.php' ?>

<!-- user accounts section starts  -->
<!-- 
<section class="accounts">

   <h1 class="heading">Tài khoản người dùng</h1>
 

   <div class="box-container">
   <div class="box">
      <p>Đăng ký tài khoản mới</p>
      <a href="register_user.php" class="option-btn">Đăng ký</a>
   </div>

   <?php
      $select_account = $conn->prepare("SELECT * FROM `khachhang`");
      $select_account->execute();
      if($select_account->rowCount() > 0){
         while($fetch_accounts = $select_account->fetch(PDO::FETCH_ASSOC)){  
   ?>
   <div class="box">
      <p> ID : <span><?= $fetch_accounts['MaKH']; ?></span> </p>
      <p> Tài khoản : <span><?= $fetch_accounts['TenKH']; ?></span> </p>
      <p> SĐT : <span><?= $fetch_accounts['SDT']; ?></span> </p>
      <p> Địa chỉ : <span><?= $fetch_accounts['Diachi']; ?></span> </p>
      <p> Email : <span><?= $fetch_accounts['Email']; ?></span> </p>
      <div class="flex-btn">
         <a href="update_profile_user.php?id=<?= $fetch_accounts['MaKH']; ?>" class="option-btn">Cập nhật</a>
         <a href="users_accounts.php?delete=<?= $fetch_accounts['MaKH']; ?>" class="delete-btn" onclick="return confirm('Bạn có chắc muốn xoá tài khoản này?');">Xoá</a>
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

<!-- user accounts section ends -->

<section class="accounts">

   <h1 class="heading">Tài khoản khách hàng</h1>
 

   <div class="box-container">
   <div class="box">
      <p>Đăng ký tài khoản mới</p>
      <a href="register_user.php" class="option-btn">Đăng ký</a>
   </div>
   </section>
<section class="main-content show-products" style="padding-top: 0;">
<h1 class="heading">Danh sách khách hàng</h1>
   <table class="product-table">
      <thead>
         <tr>
            <th>Mã KH</th>
            <th>Tên KH</th>
            <th>SĐT</th>
            <th>Địa Chỉ</th>
            <th>Email</th>
            <th>Lịch sử thuê</th>
            <th>Chức năng</th>
         </tr>
      </thead>
      <tbody>
      <?php
      $select_account = $conn->prepare("SELECT * FROM `khachhang`");
      $select_account->execute();
      if($select_account->rowCount() > 0){
         while($fetch_accounts = $select_account->fetch(PDO::FETCH_ASSOC)){  
   ?>
         <tr>
            <td><?= $fetch_accounts['MaKH']; ?></td>
            <td><?= $fetch_accounts['TenKH']; ?></td>
            <td><?= htmlspecialchars($fetch_accounts['SDT']); ?></td>
            <td><?= $fetch_accounts['Diachi']; ?></td>
            <td><?= $fetch_accounts['Email']; ?></td>
            <td>
               <a href="lichsu.php?MaKH=<?= $fetch_accounts['MaKH']; ?>" class="btn btn-history">Xem</a>
            </td>
            <td>
               <a href="update_profile_user.php?update=<?= $fetch_accounts['MaKH']; ?>" class="btn btn-update">Cập nhật</a>
               <a href="users_accounts.php?delete=<?= $fetch_accounts['MaKH']; ?>" class="btn btn-delete" onclick="return confirm('Xoá khách hàng?');">Xoá</a>
            </td>
         </tr>
         <?php
               }
            } else {
               echo '<tr><td colspan="8" class="empty">Chưa có khách hàng nào!</td></tr>';
            }
         ?>
      </tbody>
   </table>
</section>





<!-- custom js file link  -->
<script src="../js/admin_script.js"></script>

</body>
</html>