<?php

include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:admin_login.php');
}

?> 

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Trang chủ</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href=".../css/admin_style.css">

</head>
<body>

<?php include '../components/admin_header.php' ?>

<!-- admin dashboard section starts  -->

<section class="dashboard">

   <h1 class="heading">Trang chủ quản trị</h1>

   <div class="box-container">

   <div class="box">
      <h3>Xin chào!</h3>
      <p><?= $fetch_profile['name']; ?></p>
      <a href="update_profile.php" class="btn">Cập nhật tài khoản</a>
   </div>
<div class="box">
   <?php
      $select_all_orders = $conn->prepare("SELECT COUNT(*) FROM `orders`");
      $select_all_orders->execute();
      $total_orders = $select_all_orders->fetchColumn();
   ?>
   <h3><?= $total_orders; ?></h3>
   <p>Tổng số đơn đặt hàng</p>
   <a href="placed_orders.php" class="btn">Xem đơn hàng</a>
</div>

   <div class="box">
   <?php
      $select_pending = $conn->prepare("SELECT COUNT(*) FROM `orders` WHERE payment_status = 'Đang chờ'");
      $select_pending->execute();
      $count_pending = $select_pending->fetchColumn();
   ?>
   <h3><?= $count_pending; ?></h3>
   <p>Đơn hàng chờ xử lý</p>
   <a href="pending_orders.php" class="btn">Xem đơn hàng</a>
</div>

<div class="box">
   <?php
      $select_completed = $conn->prepare("SELECT COUNT(*) FROM `orders` WHERE payment_status = 'Hoàn thành'");
      $select_completed->execute();
      $count_completed = $select_completed->fetchColumn();
   ?>
   <h3><?= $count_completed; ?></h3>
   <p>Đơn hàng hoàn thành</p>
   <a href="completed_orders.php" class="btn">Xem đơn hàng</a>
</div>


   <div class="box">
      <?php
         $select_products = $conn->prepare("SELECT * FROM `products`");
         $select_products->execute();
         $numbers_of_products = $select_products->rowCount();
      ?>
      <h3><?= $numbers_of_products; ?></h3>
      <p>Tổng số sản phẩm</p>
      <a href="products.php" class="btn">Xem sản phẩm</a>
   </div>

   <div class="box">
      <?php
         $select_users = $conn->prepare("SELECT * FROM `users`");
         $select_users->execute();
         $numbers_of_users = $select_users->rowCount();
      ?>
      <h3><?= $numbers_of_users; ?></h3>
      <p>Tài khoản người dùng</p>
      <a href="users_accounts.php" class="btn">Xem người dùng</a>
   </div>

   <div class="box">
      <?php
         $select_admins = $conn->prepare("SELECT * FROM `admin`");
         $select_admins->execute();
         $numbers_of_admins = $select_admins->rowCount();
      ?>
      <h3><?= $numbers_of_admins; ?></h3>
      <p>Quản trị viên</p>
      <a href="admin_accounts.php" class="btn">Xem quản trị viên</a>
   </div>

   <div class="box">
      <?php
         $select_messages = $conn->prepare("SELECT * FROM `messages`");
         $select_messages->execute();
         $numbers_of_messages = $select_messages->rowCount();
      ?>
      <h3><?= $numbers_of_messages; ?></h3>
      <p>Phản hồi về cửa hàng</p>
      <a href="messages.php" class="btn">Xem tin nhắn</a>
   </div>

   </div>

</section>

<!-- admin dashboard section ends -->


<!-- custom js file link  -->
<script src="../js/admin_script.js"></script>

</body>
</html>