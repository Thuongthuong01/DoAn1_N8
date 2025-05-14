<?php
include '../components/connect.php';

session_start();

if (!isset($_SESSION["user_id"])) {
   header("Location: admin_login.php");
   exit();
}

if (isset($_POST['update'])) {
   $MaThue = $_POST['MaThue'];
   $MaKH = $_POST['MaKH'];
   $MaBD = $_POST['MaBD'];
   $NgayThue = $_POST['NgayThue'];
   $NgayTraDK = $_POST['NgayTraDK'];
   $SoLuong = $_POST['SoLuong'];

   // Filter input
   $MaKH = filter_var($MaKH, FILTER_SANITIZE_STRING);
   $MaBD = filter_var($MaBD, FILTER_SANITIZE_STRING);
   $SoLuong = filter_var($SoLuong, FILTER_VALIDATE_INT);

   $update_stmt = $conn->prepare("UPDATE phieuthue SET MaKH = ?, MaBD = ?, NgayThue = ?, NgayTraDK = ?, SoLuong = ? WHERE MaThue = ?");
   $update_stmt->execute([$MaKH, $MaBD, $NgayThue, $NgayTraDK, $SoLuong, $MaThue]);

   $message[] = 'Phiếu thuê đã được cập nhật!';
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
   <meta charset="UTF-8">
   <title>Cập nhật phiếu thuê</title>
    <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>

<?php include '../components/admin_header.php'; ?>

<section class="update-product">
   <h1 class="heading">Cập nhật phiếu thuê</h1>

   <?php
   $update_id = $_GET['update'];
   $stmt = $conn->prepare("SELECT * FROM phieuthue WHERE MaThue = ?");
   $stmt->execute([$update_id]);
   if ($stmt->rowCount() > 0) {
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
   ?>
   <form action="" method="POST">
      <input type="hidden" name="MaThue" value="<?= $row['MaThue']; ?>">

      <span>Mã khách hàng</span>
      <input type="text" name="MaKH" value="<?= $row['MaKH']; ?>" class="box" required>

      <span>Mã băng đĩa</span>
      <input type="text" name="MaBD" value="<?= $row['MaBD']; ?>" class="box" required>

      <span>Ngày thuê</span>
      <input type="date" name="NgayThue" value="<?= $row['NgayThue']; ?>" class="box" required>

      <span>Ngày trả dự kiến</span>
      <input type="date" name="NgayTraDK" value="<?= $row['NgayTraDK']; ?>" class="box" required>

      <span>Số lượng</span>
      <input type="number" name="SoLuong" value="<?= $row['SoLuong']; ?>" class="box" min="1" required>

      <div class="flex-btn">
         <input type="submit" name="update" value="Cập nhật" class="btn">
         <a href="placed_orders.php" class="option-btn">Quay lại</a>
      </div>
   </form>
   <?php
   } else {
      echo '<p class="empty">Không tìm thấy phiếu thuê!</p>';
   }
   ?>
</section>

<script src="../js/admin_script.js"></script>
</body>
</html>
