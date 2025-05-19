<?php

include '../components/connect.php';

session_start();

if (!isset($_SESSION["user_id"])) {
   header("Location:admin_login");
   exit();
}

if (isset($_POST['add_product'])) {
   $MaBD = filter_var($_POST['MaBD'], FILTER_SANITIZE_STRING);
   $TenBD = filter_var($_POST['TenBD'], FILTER_SANITIZE_STRING);
   $Dongia = filter_var($_POST['Dongia'], FILTER_SANITIZE_STRING);
   $Theloai = filter_var($_POST['Theloai'], FILTER_SANITIZE_STRING);
   $NSX = filter_var($_POST['NSX'], FILTER_SANITIZE_STRING);
   $Tinhtrang = filter_var($_POST['Tinhtrang'], FILTER_SANITIZE_STRING);
   $ChatLuong = filter_var($_POST['ChatLuong'], FILTER_SANITIZE_STRING);
   $image = filter_var($_FILES['image']['name'], FILTER_SANITIZE_STRING);
   $image_size = $_FILES['image']['size'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_folder = '../uploaded_img/' . $image;

   $select_products = $conn->prepare("SELECT * FROM `bangdia` WHERE MaBD = ?");
   $select_products->execute([$MaBD]);
   $check_ctpn = $conn->prepare("SELECT 1 FROM chitietphieunhap WHERE MaBD = ?");
$check_ctpn->execute([$MaBD]);
   // Kiểm tra và đảm bảo message là mảng
   if (!isset($message) || !is_array($message)) {
      $message = [];
   }

   if ($check_ctpn->rowCount() == 0) {
   $message[] = '❌ Mã băng đĩa không tồn tại trong phiếu nhập. Bạn phải nhập phiếu trước khi thêm sản phẩm!';
} else if ($select_products->rowCount() > 0) {
      $message[] = '❌ Trùng mã băng đĩa ! Vui lòng nhập lại ! ';
   } else {
      // Kiểm tra kích thước ảnh
      if ($image_size > 2000000) {
         $message[] = '❌ Kích thước hình ảnh quá lớn !';
      } else {
         // Di chuyển ảnh và chèn vào cơ sở dữ liệu
         move_uploaded_file($image_tmp_name, $image_folder);
         $insert_product = $conn->prepare("INSERT INTO `bangdia` (MaBD, TenBD, Theloai, Dongia, NSX, Tinhtrang, ChatLuong, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$insert_product->execute([$MaBD, $TenBD, $Theloai, $Dongia, $NSX, $Tinhtrang, $ChatLuong, $image]);
         $message[] = '✅ Đã thêm sản phẩm mới!';
      }
   }
}


if (isset($_GET['delete'])) {
   $delete_id = $_GET['delete'];
   $delete_product_image = $conn->prepare("SELECT * FROM bangdia WHERE MaBD = ?");
   $delete_product_image->execute([$delete_id]);
   $fetch_delete_image = $delete_product_image->fetch(PDO::FETCH_ASSOC);
   unlink('../uploaded_img/' . $fetch_delete_image['image']);

   $delete_product = $conn->prepare("DELETE FROM bangdia WHERE MaBD = ?");
   $delete_product->execute([$delete_id]);
   header('location:products.php');
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Quản lý sản phẩm</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>

<?php include '../components/admin_header.php' ?>
<?php
   // Lấy các MaBD từ chitietphieunhap chưa có trong bangdia
$get_available_maBD = $conn->query("
   SELECT DISTINCT MaBD
   FROM chitietphieunhap
   WHERE MaBD NOT IN (SELECT MaBD FROM bangdia)
");
$availableMaBDs = $get_available_maBD->fetchAll(PDO::FETCH_COLUMN);

?>
<?php if (empty($availableMaBDs)): ?>
   <div class="message" style="font-size:1.5rem;background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 10px; margin-top: 10px; border-radius: 5px;margin-right:1rem">
      ⚠️ Hiện không còn mã băng đĩa nào từ phiếu nhập chưa được thêm vào!
   </div>
<?php endif; ?>

<!-- thêm sản phẩm -->
<section class="form-container" style="margin-right:2.2rem;">
   <form action="" method="POST" enctype="multipart/form-data">
      <h3>Thêm sản phẩm</h3>
      <div  style="display: flex; align-items: center; gap: 10px; margin-bottom: 1px;">
        <span style="min-width: 160px;font-size:1.8rem; text-align: left;">Mã băng đĩa:</span>
<select name="MaBD" class="box" required>
   <option value="" disabled selected>-- Chọn mã --</option>
   <?php foreach ($availableMaBDs as $maBD): ?>
      <option value="<?= htmlspecialchars($maBD) ?>"><?= htmlspecialchars($maBD) ?></option>
   <?php endforeach; ?>
</select>
   </div>
<div  style="display: flex; align-items: center; gap: 10px; margin-bottom: 1px;">
        <span style="min-width: 160px;font-size:1.8rem; text-align: left;">Tên băng đĩa:</span>
      <input type="text" required placeholder="" name="TenBD" maxlength="100" class="box">
   </div>
   <div  style="display: flex; align-items: center; gap: 10px; margin-bottom: 1px;">
        <span style="min-width: 160px;font-size:1.8rem; text-align: left;">Đơn giá thuê:</span>
      <input type="number" min="0" max="9999999999" required placeholder="" name="Dongia" onkeypress="if(this.value.length == 10) return false;" class="box">
   </div>
   <div  style="display: flex; align-items: center; gap: 10px; margin-bottom: 1px;">
        <span style="min-width: 160px;font-size:1.8rem; text-align: left;">Thể loại:</span>
      <select name="Theloai" class="box" required>
         <option value="" disabled selected>--Chọn thể loại --</option>
         <option value="Âm nhạc">Âm nhạc</option>
         <option value="Phim">Phim</option>
         <option value="Khác">Khác</option>
      </select>
   </div>
   <div  style="display: flex; align-items: center; gap: 10px; margin-bottom: 1px;">
        <span style="min-width: 160px;font-size:1.8rem; text-align: left;">Nhà sản xuất:</span>
      <input type="text" name="NSX" placeholder="" class="box" required>
   </div>
      <div  style="display: flex; align-items: center; gap: 10px; margin-bottom: 1px;">
        <span style="min-width: 160px;font-size:1.8rem; text-align: left;">Tình trạng:</span>
      <select name="Tinhtrang" class="box" required>
         <option value="" disabled selected>-- Chọn tình trạng --</option>
         <option value="Trống">Trống</option>
         <option value="Đã cho thuê">Đã cho thuê</option>
         <option value="Đang bảo trì">Đang bảo trì</option>
      </select>
   </div>
   <div  style="display: flex; align-items: center; gap: 10px; margin-bottom: 1px;">
        <span style="min-width: 160px;font-size:1.8rem; text-align: left;">Chất lượng:</span>
      <select name="ChatLuong" class="box" required>
   <option value="" disabled selected>-- Chọn chất lượng --</option>
      <option value="Tốt">Tốt</option>
      <option value="Trầy xước">Trầy xước</option>
      <option value="Hỏng nặng">Hỏng nặng</option>      
      <option value="Mất">Mất</option>
</select>
   </div>
<div  style="display: flex; align-items: center; gap: 10px; margin-bottom: 1px;">
        <span style="min-width: 160px;font-size:1.8rem; text-align: left;">Ảnh:</span>
      <input type="file" name="image" class="box" accept="image/jpg, image/jpeg, image/png, image/webp" required>
   </div>
      <input type="submit" value="Thêm sản phẩm" name="add_product" class="btn">
   </form>
   <!-- Hiển thị thông báo -->

<?php if (!empty($message) && is_array($message)): ?>
   <?php foreach ($message as $msg): ?>
      <div class="message" style="background-color: <?= (strpos($msg, 'Đã thêm') !== false) ? '#d4edda' : '#f8d7da'; ?>; color: <?= (strpos($msg, 'Đã thêm') !== false) ? '#155724' : '#721c24'; ?>; border: 1px solid <?= (strpos($msg, 'Đã thêm') !== false) ? '#c3e6cb' : '#f5c6cb'; ?>; padding: 10px; margin: 10px 0; border-radius: 5px;">
         <span><?= $msg; ?></span>
         <i onclick="this.parentElement.style.display='none';" style="cursor:pointer; float:right;">&times;</i>
      </div>
   <?php endforeach; ?>
<?php endif; ?>
</section>



<!-- HIỂN THỊ BẢN THÔNG TIN SẢN PHẨM   -->
<section class="main-content show-products" style="padding-top: 0;">
<h1 class="heading">Danh sách băng đĩa</h1>
   <table class="product-table">
      <thead>
         <tr>
            <th>Mã đĩa</th>
            <th>Ảnh</th>
            <th>Tên Băng Đĩa</th>
            <th>Thể loại</th>
            <th>Đơn giá thuê</th>
            <th>NSX</th>
            <th>Tình trạng</th>
            <th>Chất lượng</th>
            <th>Chức năng</th>

         </tr>
      </thead>
      <tbody>
         <?php
            $show_products = $conn->prepare("SELECT * FROM bangdia");
            $show_products->execute();
            if ($show_products->rowCount() > 0) {
               while ($fetch_products = $show_products->fetch(PDO::FETCH_ASSOC)) {
         ?>
         <tr>
            <td><?= $fetch_products['MaBD']; ?></td> 
            <td><img src="../uploaded_img/<?= $fetch_products['image']; ?>" alt="" width="70" height="70"></td>
            <td><?= $fetch_products['TenBD']; ?></td>
            <td><?= $fetch_products['Theloai']; ?></td>
            <td><?= number_format($fetch_products['Dongia'], 0, ",", "."); ?> VNĐ</td>
            <td><?= $fetch_products['NSX']; ?></td>
            <td style="white-space: normal; word-wrap: break-word; max-width: 200px;"><?= $fetch_products['Tinhtrang']; ?></td>
            <td><?= $fetch_products['ChatLuong']; ?></td>
            <td>
               <a href="update_product.php?update=<?= $fetch_products['MaBD']; ?>" class="btn btn-update">Sửa</a>
               <a href="products.php?delete=<?= $fetch_products['MaBD']; ?>" class="btn btn-delete" onclick="return confirm('Xoá sản phẩm?');">Xoá</a>
            </td>
         </tr>
         <?php
               }
            } else {
               echo '<tr><td colspan="8" class="empty">Chưa có sản phẩm nào được thêm vào!</td></tr>';
            }
         ?>
      </tbody>
   </table>
</section>

<!-- custom js file link  -->
<script src="../js/admin_script.js"></script>

</body>
</html>
