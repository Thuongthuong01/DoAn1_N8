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
   <title>placed orders</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>

<?php include '../components/admin_header.php' ?>

<!-- placed orders section starts  -->

<section class="placed-orders-admin">

   <h1 class="heading">Đơn hàng</h1>

   <!-- <div class="box-container">

   <?php
      $select_orders = $conn->prepare("SELECT * FROM `orders`");
      $select_orders->execute();
      if($select_orders->rowCount() > 0){
         while($fetch_orders = $select_orders->fetch(PDO::FETCH_ASSOC)){
   ?>
   <div class="box">
      <p> ID: <span><?= $fetch_orders['user_id']; ?></span> </p>
      <p> Được đặt ngày: <span><?= $fetch_orders['placed_on']; ?></span> </p>
      <p> Tên: <span><?= $fetch_orders['name']; ?></span> </p>
      <p> Email: <span><?= $fetch_orders['email']; ?></span> </p>
      <p> Số điện thoại: <span><?= $fetch_orders['number']; ?></span> </p>
      <p> Địa chỉ: <span><?= $fetch_orders['address']; ?></span> </p>
      <p> Tổng số sản phẩm: <span><?= $fetch_orders['total_products']; ?></span> </p>
      <p> Tổng giá: <span><?= number_format($fetch_orders['total_price'], 0, ',', '.'); ?> VNĐ</span> </p>
      <p> Phương thức thanh toán: <span><?= $fetch_orders['method']; ?></span> </p>
      <form action="" method="POST">
   <input type="hidden" name="order_id" value="<?= $fetch_orders['id']; ?>">
   <select name="payment_status" class="drop-down">
      <option value="" selected disabled><?= $fetch_orders['payment_status']; ?></option>
      <option value="Đang chờ">Đang chờ</option>
      <option value="Hoàn thành">Hoàn thành</option>
   </select>
   <div class="flex-btn">
      <input type="submit" value="Cập nhật" class="btn" name="update_payment">
      <a href="placed_orders.php?delete=<?= $fetch_orders['id']; ?>" class="delete-btn" onclick="return confirm('Xoá đơn hàng này?');">Xoá</a>
   </div>
</form>

   </div>
   <?php
      }
   }else{
      echo '<p class="empty">Chưa có sản phẩm nào!</p>';
   }
   ?>

   </div> -->
   <div class="box-container">
   <table class="order-table">
      <thead>
         <tr>
            <th>ID KH</th>
            <th>Ngày đặt</th>
            <th>Tên</th>
            <th>Email</th>
            <th>SĐT</th>
            <th>Địa chỉ</th>
            <th>Sản phẩm</th>
            <th>Tổng giá</th>
            <th>Thanh toán</th>
            <th>Trạng thái</th>
            <th>Hành động</th>
         </tr>
      </thead>
      <tbody>
         <?php
            $select_orders = $conn->prepare("SELECT * FROM `orders`");
            $select_orders->execute();
            if($select_orders->rowCount() > 0){
               while($fetch_orders = $select_orders->fetch(PDO::FETCH_ASSOC)){
         ?>
         <tr>
            <td><?= $fetch_orders['user_id']; ?></td>
            <td><?= $fetch_orders['placed_on']; ?></td>
            <td><?= $fetch_orders['name']; ?></td>
            <td><?= $fetch_orders['email']; ?></td>
            <td><?= $fetch_orders['number']; ?></td>
            <td><?= $fetch_orders['address']; ?></td>
            <td><?= $fetch_orders['total_products']; ?></td>
            <td><?= number_format($fetch_orders['total_price'], 0, ',', '.'); ?> VNĐ</td>
            <td><?= $fetch_orders['method']; ?></td>
            <td>
               <form action="" method="POST">
                  <input type="hidden" name="order_id" value="<?= $fetch_orders['id']; ?>">
                  <select name="payment_status" class="drop-down">
                     <option value="" disabled selected><?= $fetch_orders['payment_status']; ?></option>
                     <option value="Đang chờ">Đang chờ</option>
                     <option value="Hoàn thành">Hoàn thành</option>
                  </select>
            </td>
            <td>
                  <input type="submit" value="Cập nhật" class="btn" name="update_payment">
                  <a href="placed_orders.php?delete=<?= $fetch_orders['id']; ?>" class="delete-btn" onclick="return confirm('Xoá đơn hàng này?');">Xoá</a>
               </form>
            </td>
         </tr>
         <?php
            }
         } else {
            echo '<tr><td colspan="11" class="empty">Chưa có sản phẩm nào!</td></tr>';
         }
         ?>
      </tbody>
   </table>
</div>
</section>

<!-- placed orders section ends -->









<!-- custom js file link  -->
<script src="../js/admin_script.js"></script>

</body>
</html>