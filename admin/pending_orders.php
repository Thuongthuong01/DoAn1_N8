<?php
include '../components/connect.php';
session_start();

if(!isset($_SESSION['admin_id'])){
   header('location:admin_login.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <title>Đơn hàng đang chờ</title>
   <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>

<?php include '../components/admin_header.php'; ?>
<?php
   $count_pending = $conn->prepare("SELECT COUNT(*) FROM `orders` WHERE payment_status = 'Đang chờ'");
   $count_pending->execute();
   $total_pending = $count_pending->fetchColumn();
?>

<section class="placed-orders">
   <h1 class="heading">Đơn hàng đang chờ</h1>
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
            </tr>
         </thead>
         <tbody>
         <?php
            $select_orders = $conn->prepare("SELECT * FROM `orders` WHERE payment_status = 'Đang chờ'");
            $select_orders->execute();
            if($select_orders->rowCount() > 0){
               while($order = $select_orders->fetch(PDO::FETCH_ASSOC)){
         ?>
         <tr>
            <td><?= $order['user_id']; ?></td>
            <td><?= $order['placed_on']; ?></td>
            <td><?= $order['name']; ?></td>
            <td><?= $order['email']; ?></td>
            <td><?= $order['number']; ?></td>
            <td><?= $order['address']; ?></td>
            <td><?= $order['total_products']; ?></td>
            <td><?= number_format($order['total_price'], 0, ',', '.'); ?> VNĐ</td>
            <td><?= $order['method']; ?></td>
            <td><span style="color:red; font-weight:bold;"><?= $order['payment_status']; ?></span></td>
         </tr>
         <?php }} else {
            echo '<tr><td colspan="10">Không có đơn hàng nào đang chờ.</td></tr>';
         } ?>
         </tbody>
      </table>
   </div>
</section>

</body>
</html>
