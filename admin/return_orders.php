<?php
include '../components/connect.php';
session_start();

if (!isset($_SESSION["user_id"])) {
   header("Location: admin_login.php");
   exit();
}

$message = [];

if (isset($_POST['add_phieutra'])) {
   $MaThue = $_POST['MaThue'];
   $MaKH = $_POST['MaKH'];
   $NgayTraTT = $_POST['NgayTraTT'];
   $ChatLuong = $_POST['ChatLuong'];
//kiểm trả mã thuê trước khi lập phiếu trả 
   $check = $conn->prepare("SELECT * FROM phieutra WHERE MaThue = ?");
   $check->execute([$MaThue]);

   if ($check->rowCount() > 0) {
      $message[] = "❌ Mã thuê này đã có phiếu trả!";
   } else {
   // Truy vấn ngày trả dự kiến từ phiếu thuê
   $stmt = $conn->prepare("SELECT NgayTraDK FROM phieuthue WHERE MaThue = ?");
   $stmt->execute([$MaThue]);
   $row = $stmt->fetch(PDO::FETCH_ASSOC);

   if ($row) {
      $NgayTraDK = $row['NgayTraDK'];
      $date1 = new DateTime($NgayTraDK);
      $date2 = new DateTime($NgayTraTT);

   // Tính số ngày trả muộn (>= 0)
   $interval = $date2->diff($date1);
   $TraMuon = ($date2 > $date1) ? $interval->days : 0;

   //Lấy đơn giá từ bảng liên quan (giả sử có liên kết MaBD → DonGia)
   $stmt = $conn->prepare("
      SELECT bd.DonGia 
      FROM phieuthue pt
      JOIN bangdia bd ON pt.MaBD = bd.MaBD
      WHERE pt.MaThue = ?
   ");
      $stmt->execute([$MaThue]);
      $row2 = $stmt->fetch(PDO::FETCH_ASSOC);

   if ($row2) {
      $DonGia = $row2['DonGia'];

    // Phí trễ hạn: 5% đơn giá mỗi ngày trễ
      $PhatTre = $TraMuon * (0.05 * $DonGia);

    // Phí hư hỏng
      switch ($ChatLuong) {
        case 'Tốt':
            $PhatHu = 0;
            break;
        case 'Trầy xước':
            $PhatHu = 0.3 * $DonGia;
            break;
        case 'Hỏng nặng':
            $PhatHu = 0.5 * $DonGia;
            break;
        case 'Mất':
            $PhatHu = 1.0 * $DonGia;
            break;
        default:
            $PhatHu = 0;
    }

      $TienPhat = $PhatTre + $PhatHu;

    // Thêm vào bảng phieutra
      $insert = $conn->prepare("INSERT INTO phieutra(MaThue, MaKH, NgayTraTT, ChatLuong, TraMuon, TienPhat) VALUES(?,?,?,?,?,?)");
      $insert->execute([$MaThue, $MaKH, $NgayTraTT, $ChatLuong, $TraMuon, $TienPhat]);

      $message[] = "Thêm phiếu trả thành công!";
         } else {
      $message[] = "Không tìm thấy đơn giá băng đĩa!";
         }
   }
   }
}

// xoá 
if (isset($_GET['delete'])) {
   $delete_id = $_GET['delete'];

   $delete_phieutra = $conn->prepare("DELETE FROM phieutra WHERE MaTra = ?");
   $delete_phieutra->execute([$delete_id]);

   $message[] = "Đã xóa phiếu trả thành công!";
}
?>
<!-- thông báo -->
<!-- <?php if (!empty($message) && is_array($message)): ?>
   <?php foreach ($message as $msg): ?>
      <?php
         $isSuccess = strpos($msg, 'Đã thêm') !== false || strpos($msg, 'Đã xóa') !== false;
      ?>
      <div class="message" style="background-color: <?= $isSuccess ? '#d4edda' : '#f8d7da'; ?>; color: <?= $isSuccess ? '#155724' : '#721c24'; ?>; border: 1px solid <?= $isSuccess ? '#c3e6cb' : '#f5c6cb'; ?>; padding: 10px; margin: 10px 0; border-radius: 5px;">
         <span><?= $msg; ?></span>
         <i onclick="this.parentElement.style.display='none';" style="cursor:pointer; float:right;">&times;</i>
      </div>
   <?php endforeach; ?>
<?php endif; ?> -->



<!DOCTYPE html>
<html lang="vi">
<head>
   <meta charset="UTF-8">
   <title>Quản lý trả đĩa</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>

<?php include '../components/admin_header.php' ?>
<section class="main-content placed-orders-admin">
   <section class="add-products">
        <form action="" method="POST" enctype="multipart/form-data">
   <h3>Phiếu trả đĩa</h3>
   
   <input type="text" required placeholder="Nhập mã thuê" name="MaThue" id="MaThue" maxlength="9" class="box">
   <input type="text" required placeholder="Mã khách hàng" name="MaKH" id="MaKH" maxlength="9" class="box">
   <input type="date" required name="NgayTraTT" class="box">
   
   <select name="ChatLuong" class="box" required>
      <option value="">-- Chọn chất lượng --</option>
      <option value="Tốt">Tốt</option>
      <option value="Trầy xước">Trầy xước</option>
      <option value="Hỏng nặng">Hỏng nặng</option>
      <option value="Mất">Mất</option>
   </select>
   
   <input type="submit" value="Thêm phiếu trả" name="add_phieutra" class="btn">
</form>
<!-- Thông báo -->
<?php if (!empty($message) && is_array($message)): ?>
   <?php foreach ($message as $msg): ?>
      <div class="message" style="background-color: <?= (strpos($msg, 'Đã thêm') !== false) ? '#d4edda' : '#f8d7da'; ?>; color: <?= (strpos($msg, 'Đã thêm') !== false) ? '#155724' : '#721c24'; ?>; border: 1px solid <?= (strpos($msg, 'Đã thêm') !== false) ? '#c3e6cb' : '#f5c6cb'; ?>; padding: 10px; margin: 10px 0; border-radius: 5px;">
         <span><?= $msg; ?></span>
         <i onclick="this.parentElement.style.display='none';" style="cursor:pointer; float:right;">&times;</i>
      </div>
   <?php endforeach; ?>
<?php endif; ?>
    </section>
</section>

<section class="main-content show-products">
   <h1 class="heading">Danh sách phiếu trả</h1>
   <table class="product-table">
      <thead>
         <tr>
            <th>Mã Trả</th>
            <th>Mã Thuê</th>
            <th>Khách Hàng</th>
            <th>SĐT</th>
            <th>Ngày Trả Thực Tế</th>
            <th>Chất Lượng</th>
            <th>Trả Muộn (ngày)</th>
            <th>Tiền phạt</th>
            <th>Chức năng</th>

         </tr>
      </thead>
      <tbody>
         <?php
         $stmt = $conn->prepare("
            SELECT 
               pt.MaTra, pt.MaThue, pt.MaKH, pt.NgayTraTT, pt.ChatLuong, pt.TraMuon, pt.TienPhat,
               kh.TenKH, kh.SDT
            FROM phieutra pt
            JOIN khachhang kh ON pt.MaKH = kh.MaKH
            ORDER BY pt.NgayTraTT DESC
         ");

         $stmt->execute();
         $phieutras = $stmt->fetchAll(PDO::FETCH_ASSOC);

         if (count($phieutras) > 0):
            foreach ($phieutras as $phieu):
         ?>
               <tr>
                  <td><?= $phieu['MaTra']; ?></td>
                  <td><?= $phieu['MaThue']; ?></td>
                  <td><?= $phieu['TenKH']; ?></td>
                  <td><?= $phieu['SDT']; ?></td>
                  <td><?= $phieu['NgayTraTT']; ?></td>
                  <td><?= $phieu['ChatLuong']; ?></td>
                  <td><?= $phieu['TraMuon']; ?> ngày</td>
                  <td><?= number_format($phieu['TienPhat'], 0, ',', '.'); ?> VNĐ</td>
                  <td>
                     <a href="update_return.php?update=<?= $phieu['MaTra']; ?>" class="btn btn-update">Sửa</a>
                     <a href="?delete=<?= $phieu['MaTra']; ?>" onclick="return confirm('Bạn có chắc muốn xóa phiếu trả này?');" class="btn btn-delete">Xóa</a>
                  </td>
               </tr>
         <?php
            endforeach;
         else:
            echo '<tr><td colspan="8">Không có phiếu trả nào.</td></tr>';
         endif;
         ?>
      </tbody>
   </table>
</section>

<script src="../js/admin_script.js"></script>
</body>
</html>