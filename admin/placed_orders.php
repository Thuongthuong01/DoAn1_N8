<?php
include '../components/connect.php';
session_start();

if (!isset($_SESSION["user_id"])) {
   header("Location:admin_login");
   exit();
}

$message = [];

// Thêm đơn hàng thuê mới vào bảng PhieuThue
if (isset($_POST['add_phieuthue'])) {
   $maBD = $_POST['MaBD'];
   $maKH = $_POST['MaKH'];
   $ngayThue = $_POST['Ngaythue'];
   $hanTra = $_POST['Hantra'];
   $soLuong = $_POST['Soluong'];

   try {
      // Tính số ngày thuê
      $ngayThueDate = new DateTime($ngayThue);
      $hanTraDate = new DateTime($hanTra);
      $soNgayThue = $ngayThueDate->diff($hanTraDate)->days;

      // Lấy đơn giá từ bảng bangdia
      $stmt = $conn->prepare("SELECT Dongia FROM bangdia WHERE MaBD = ?");
      $stmt->execute([$maBD]);
      $result = $stmt->fetch(PDO::FETCH_ASSOC);

      if ($result) {
         $giaThue = $result['Dongia'];

         // Tính tổng tiền = số ngày thuê * đơn giá * số lượng
         $tongTien = $soNgayThue * $giaThue * $soLuong;

         // Thêm vào bảng phieuthue
         $insert = $conn->prepare("INSERT INTO `phieuthue` (MaBD, MaKH, NgayThue, NgayTraDK, SoLuong, TongTien) VALUES (?, ?, ?, ?, ?, ?)");
         $insert->execute([$maBD, $maKH, $ngayThue, $hanTra, $soLuong, $tongTien]);
         $message[] = "Thêm phiếu thuê thành công!";
      } else {
         $message[] = "Không tìm thấy băng đĩa có mã '$maBD'.";
      }
   } catch (PDOException $e) {
      $message[] = "Lỗi khi thêm phiếu thuê: " . $e->getMessage();
   }
}
// Xoá đơn hàng thuê
   if (isset($_GET['delete'])) {
      $delete_id = $_GET['delete'];
      $delete_order = $conn->prepare("DELETE FROM `PhieuThue` WHERE MaThue = ?");
      $delete_order->execute([$delete_id]);
      header('location:placed_orders.php');
      exit();
}

?>

<!DOCTYPE html>
<html lang="vi">
<head>
   <meta charset="UTF-8">
   <title>Quản lý thuê đĩa</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>

<?php include '../components/admin_header.php' ?>

<section class="main-content placed-orders-admin">
   <section class="add-products">
      <form action="" method="POST" enctype="multipart/form-data">
         <h3>Phiếu thuê đĩa</h3>
         <input type="text" required placeholder="Nhập mã khách hàng" name="MaKH" maxlength="9" class="box">
         <input type="text" required placeholder="Nhập mã băng đĩa" name="MaBD" maxlength="9" class="box">
         <input type="date" required name="Ngaythue" class="box">
         <input type="date" required name="Hantra" class="box">
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

</section>


<section class="main-content show-products" >
      <h1 class="heading">Danh sách phiếu thuê</h1>
      <table class="product-table">
      <thead>
         <tr>
            <th>Mã Thuê</th>
            <th>Khách Hàng</th>
            <th>SĐT</th>
            <th>Băng Đĩa</th>
            <th>Đơn giá</th>
            <th>Ngày Thuê</th>
            <th>Hạn Trả</th>
            <th>Số Lượng</th>
            <th>Tổng Tiền</th>
            <th>Chức năng</th>
         </tr>
      </thead>
      <tbody>
         <?php
         $stmt = $conn->prepare("
            SELECT 
               pt.MaThue, pt.NgayThue, pt.NgayTraDK, pt.SoLuong, pt.TongTien,
               kh.TenKH, kh.SDT, kh.Email,
               bd.TenBD, bd.TheLoai, bd.Dongia
            FROM phieuthue pt
            JOIN khachhang kh ON pt.MaKH = kh.MaKH
            JOIN bangdia bd ON pt.MaBD = bd.MaBD
            ORDER BY pt.NgayThue DESC
         ");
         $stmt->execute();
         $phieuthues = $stmt->fetchAll(PDO::FETCH_ASSOC);

         if (count($phieuthues) > 0):
            foreach ($phieuthues as $phieu):
         ?>
               <tr>
                  <td><?= $phieu['MaThue']; ?></td>
                  <td><?= $phieu['TenKH']; ?></td>
                  <td><?= $phieu['SDT']; ?></td>
                  <td><?= $phieu['TenBD']; ?></td>
                  <td><?= number_format($phieu['Dongia'], 0, ',', '.') ?> VNĐ</td>
                  <td><?= $phieu['NgayThue']; ?></td>
                  <td><?= $phieu['NgayTraDK']; ?></td>
                  <td><?= $phieu['SoLuong']; ?></td>
                  <td><?= number_format($phieu['TongTien'], 0, ',', '.') ?> VNĐ</td>
                  <td>
                     <a href="update_order.php?update=<?= $phieu['MaThue']; ?>" class="btn btn-update">Sửa</a>
                     <a href="?delete=<?= $phieu['MaThue']; ?>" onclick="return confirm('Bạn có chắc muốn xóa phiếu thuê này?');" class="btn btn-delete">Xóa</a>
                  </td>
               </tr>
         <?php
            endforeach;
         else:
            echo '<tr><td colspan="12">Không có phiếu thuê nào.</td></tr>';
         endif;
         ?>
      </tbody>
   </table>
</section>
<script src="../js/admin_script.js"></script>
</body>
</html>
