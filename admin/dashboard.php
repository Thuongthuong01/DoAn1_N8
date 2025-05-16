<?php

include '../components/connect.php';

session_start();

if (!isset($_SESSION["user_id"])) {
   header("Location:admin_login");
   exit();
}

$current_month = date('m');
$current_year = date('Y');

// Lấy tên tháng (hiển thị)
$current_month_name = $current_month;

// Truy vấn doanh thu từ chitiethoadon JOIN phieuthue
// $month_revenue_query = $conn->prepare("
//     SELECT SUM( CAST(cthd.tongtien AS DECIMAL(10,2)) ) AS total 
//     FROM chitiethoadon cthd
//     JOIN phieuthue hdt ON cthd.MaHD = hdt.MaHD
//     WHERE MONTH(hdt.Ngaythue) = ? AND YEAR(hdt.Ngaythue) = ?
// ");
// $month_revenue_query->execute([$current_month, $current_year]);
// $month_revenue = $month_revenue_query->fetchColumn() ?? 0;

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
   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>

<?php include '../components/admin_header.php' ?>

<!-- phần nội dung chính -->
<section class="dashboard">
   <h1 class="heading">Trang chủ quản trị</h1>
<div class="box-container">
<!-- hộp 1 : doanh thu -->   
<div class="box">
<h3><?= number_format($month_revenue ?? 0, 0, ',', '.'); ?> VNĐ</h3>
<p>Doanh thu tháng <?= $current_month_name . '/' . $current_year; ?></p>   <a href="../admin/revenue.php" class="btn">Xem chi tiết</a>
</div>

<!-- hộp 2 : tổng đơn hàng -->
<div class="box">
   <?php
      $select_all_orders = $conn->prepare("SELECT COUNT(*) FROM `phieuthue`");
      $select_all_orders->execute();
      $total_orders = $select_all_orders->fetchColumn();
   ?>
   <h3><?= $total_orders; ?></h3>
   <p>Tổng số đơn đặt hàng</p>
   <a href="placed_orders.php" class="btn">Xem đơn hàng</a>
</div>

<!-- hộp 3 : tổng sản phẩm -->
   <div class="box">
      <?php
         $select_products = $conn->prepare("SELECT * FROM `bangdia`");
         $select_products->execute();
         $numbers_of_products = $select_products->rowCount();
      ?>
      <h3><?= $numbers_of_products; ?></h3>
      <p>Tổng số sản phẩm</p>
      <a href="products.php" class="btn">Xem sản phẩm</a>
   </div>
<!-- hộp 4 : tổng thành viên  -->
   <div class="box">
      <?php
         $select_users = $conn->prepare("SELECT * FROM `khachhang`");
         $select_users->execute();
         $numbers_of_users = $select_users->rowCount();
      ?>
      <h3><?= $numbers_of_users; ?></h3>
      <p>Tài khoản người dùng</p>
      <a href="users_accounts.php" class="btn">Xem người dùng</a>
   </div>

<!-- <div class="box">
   <?php
      $select_pending = $conn->prepare("SELECT COUNT(*) FROM `phieuthue` WHERE payment_status = 'Đang chờ'");
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
         $select_admins = $conn->prepare("SELECT * FROM `admin`");
         $select_admins->execute();
         $numbers_of_admins = $select_admins->rowCount();
      ?>
      <h3><?= $numbers_of_admins; ?></h3>
      <p>Quản trị viên</p>
      <a href="admin_accounts.php" class="btn">Xem quản trị viên</a>
   </div> 
-->
</div>  

<!-- <h1 class="heading">Trang chủ quản trị</h1> -->   
</section>

<!-- custom js file link  -->
<script src="../js/script.js"></script>

</body>
</html>