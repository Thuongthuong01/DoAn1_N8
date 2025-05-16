<?php


include '../components/connect.php';

session_start();

if (!isset($_SESSION["user_id"])) {
   header("Location:admin_login");
   exit();
}

if (isset($_GET['delete_phieunhap'])) {
   $delete_id = $_GET['delete_phieunhap'];
   $delete_phieunhap = $conn->prepare("DELETE FROM phieunhap WHERE MaPhieu = ?");
   $delete_phieunhap ->execute([$delete_id]);
   $message[] = "✅ Đã xoá phiếu nhập thành công !";
}

?> 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý kho nhập</title>
       <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

<!-- custom css file link  -->
<link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>
<?php include '../components/admin_header.php' ?>
<!-- <section class="warehouse"> -->
<section class="accounts">

   <h1 class="heading">Thêm phiếu nhập hàng</h1>
   <div class="box-container">
   <div class="box">
      <p>Thêm phiếu nhập mới</p>
      <a href="warehouse_receipt.php" class="option-btn">Thêm</a>
   </div>
</section>

<section class="main-content show-products" style="padding-top: 0;">
   <h1 class="heading">Danh sách phiếu nhập hàng</h1>
   <table class="product-table">
      <thead>
         <tr>
            <th>Mã Phiếu</th>
            <th>Mã NCC</th>
            <th>Ngày Nhập</th>
            <th>Số Lượng</th>
            <th>Chi Tiết Băng Đĩa (Mã BD - Giá Gốc)</th>
            <th>Tổng Tiền</th>
            <th>Chức năng</th>
         </tr>
      </thead>
      <tbody>
         <?php
            // Lấy danh sách phiếu nhập
            $show_phieunhap = $conn->prepare("SELECT * FROM phieunhap ORDER BY NgayNhap DESC");
            $show_phieunhap->execute();

            if ($show_phieunhap->rowCount() > 0) {
               while ($phieu = $show_phieunhap->fetch(PDO::FETCH_ASSOC)) {
                  // Lấy chi tiết băng đĩa theo mã phiếu
                  $maPhieu = $phieu['MaPhieu'];
                  $ct_stmt = $conn->prepare("SELECT MaBD, GiaGoc FROM chitietphieunhap WHERE MaPhieu = ?");
                  $ct_stmt->execute([$maPhieu]);
                  
                  $chiTietBD = [];
                  while ($ct = $ct_stmt->fetch(PDO::FETCH_ASSOC)) {
                     $chiTietBD[] = $ct['MaBD'] . " - " . number_format($ct['GiaGoc'], 0, ',', '.') . "đ";
                  }
                  
                  $chiTietStr = implode("<br>", $chiTietBD);
         ?>
         <tr>
            <td><?= htmlspecialchars($phieu['MaPhieu']); ?></td>
            <td><?= htmlspecialchars($phieu['MaNCC']); ?></td>
            <td><?= htmlspecialchars($phieu['NgayNhap']); ?></td>
            <td><?= htmlspecialchars($phieu['SoLuong']); ?></td>
            <td style="white-space: nowrap;"><?= $chiTietStr; ?></td>
            <td><?= number_format($phieu['TongTien'], 0, ',', '.') . "đ"; ?></td>
            <td>
               <!-- Ví dụ có thể thêm sửa xóa phiếu nhập -->
               <a href="update_phieunhap.php?update=<?= urlencode($phieu['MaPhieu']); ?>" class="btn btn-update">Sửa</a>
               <a href="?delete_phieunhap=<?= urlencode($phieu['MaPhieu']); ?>" class="btn btn-delete" onclick="return confirm('Bạn có chắc muốn xóa phiếu nhập này?');">Xóa</a>
            </td>
         </tr>
         <?php
               }
            } else {
               echo '<tr><td colspan="7" class="empty">Không có phiếu nhập nào!</td></tr>';
            }
         ?>
      </tbody>
   </table>
</section>


</body>
</html>