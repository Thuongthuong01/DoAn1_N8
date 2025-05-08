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

<section class="main-content placed-orders-admin">

   <!-- <h1 class="heading">Đơn hàng</h1> -->
   <section class="add-products">
   <form action="" method="POST" enctype="multipart/form-data">
      <h3>Tạo đơn hàng</h3>

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

<section class="main-content1 show-products" style="padding-top: 0;">
<h1 class="heading">Danh sách đơn hàng</h1>
   <table class="product-table">
<?php
// Truy vấn đơn hàng (hoadonthue + chitiethoadon nếu muốn có tổng tiền)
$select_orders = $conn->prepare("
   SELECT hdt.MaHD, hdt.MaKH, hdt.Ngaythue, hdt.NgaytraDK, hdt.NgaytraTT,
          SUM(CAST(ct.tongtien AS DECIMAL(10,2))) AS tongtien
   FROM hoadonthue hdt
   LEFT JOIN chitiethoadon ct ON hdt.MaHD = ct.MaHD
   GROUP BY hdt.MaHD
   ORDER BY hdt.Ngaythue DESC
");
$select_orders->execute();
$orders = $select_orders->fetchAll(PDO::FETCH_ASSOC);
?>


 
      
         <thead>
            <tr>
               <th>Mã HĐ</th>
               <th>Mã KH</th>
               <th>Ngày thuê</th>
               <th>Hạn trả</th>
               <th>Ngày trả thực tế</th>
               <th>Tổng tiền</th>
               <th>Hành động</th>
            </tr>
         </thead>
         <tbody>
            <?php if (count($orders) > 0): ?>
               <?php foreach ($orders as $order): ?>
                  <tr>
                     <td><?= $order['MaHD']; ?></td>
                     <td><?= $order['MaKH']; ?></td>
                     <td><?= $order['Ngaythue']; ?></td>
                     <td><?= $order['NgaytraDK']; ?></td>
                     <td><?= $order['NgaytraTT']; ?></td>
                     <td><?= number_format($order['tongtien'], 0, ',', '.') ?> VNĐ</td>
                     <td>
                        <a href="?delete=<?= $order['MaHD']; ?>" onclick="return confirm('Bạn có chắc muốn xóa đơn hàng này?');" class="delete-btn">Xóa</a>
                     </td>
                  </tr>
               <?php endforeach; ?>
            <?php else: ?>
               <tr><td colspan="7">Không có đơn hàng nào.</td></tr>
            <?php endif; ?>
         </tbody>
      
   </table>
</section>





<!-- custom js file link  -->
<script src="../js/script.js"></script>

</body>
</html>