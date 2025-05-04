<?php

include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:admin_login.php');
};

if(isset($_POST['update_payment'])){

   $order_id = $_POST['order_id'];
   $payment_status = $_POST['payment_status'];
   $update_status = $conn->prepare("UPDATE `orders` SET payment_status = ? WHERE id = ?");
   $update_status->execute([$payment_status, $order_id]);
   $message[] = 'Trạng thái đơn hàng đã cập nhật!';

}

if(isset($_GET['delete'])){
   $delete_id = $_GET['delete'];
   $delete_order = $conn->prepare("DELETE FROM `orders` WHERE id = ?");
   $delete_order->execute([$delete_id]);
   header('location:placed_orders.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Quản lý đơn hàng</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>

<?php include '../components/admin_header.php' ?>

<!-- placed orders section starts  -->

<section class="placed-orders-admin">

   <!-- <h1 class="heading">Đơn hàng</h1> -->
   <section class="add-products">
   <form action="" method="POST" enctype="multipart/form-data">
      <h3>Thêm đơn hàng</h3>

      <input type="text" required placeholder="Nhập mã khách hàng" name="MaKH" maxlength="9" class="box">

      <input type="text" required placeholder="Nhập mã băng đĩa" name="MaBD" maxlength="9" class="box">

      <input type="date" required placeholder="Ngày thuê" name="Ngaythue" class="box">

      <input type="date" required placeholder="Hạn trả" name="Hantra" class="box">

      <input type="number" min="1" max="100" required placeholder="Số lượng thuê" name="Soluong" class="box">

      <input type="submit" value="Thêm phiếu thuê" name="add_phieuthue" class="btn">
   </form>

   <!-- Hiển thị thông báo -->
   <?php if (!empty($message) && is_array($message)): ?>
   <div class="message <?php echo (strpos($message[0], 'thành công') !== false) ? 'success' : 'error'; ?>">
      <?php foreach ($message as $msg): ?>
         <span><?php echo $msg; ?></span>
         <i onclick="this.parentElement.style.display='none';">&times;</i>
      <?php endforeach; ?>
   </div>
   <?php endif; ?>
</section>

<h1 class="heading">Danh sách đơn hàng</h1>





<!-- custom js file link  -->
<script src="../js/admin_script.js"></script>

</body>
</html>