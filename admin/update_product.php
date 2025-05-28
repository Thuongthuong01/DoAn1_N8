<?php

include '../components/connect.php';

session_start();

if (!isset($_SESSION["user_id"])) {
   header("Location:admin_login");
   exit();
}
if(isset($_POST['update'])){

   $MaBD = $_POST['MaBD'];
   $MaBD = filter_var($MaBD, FILTER_SANITIZE_STRING);
   $TenBD = $_POST['TenBD'];
   $TenBD = filter_var($TenBD, FILTER_SANITIZE_STRING);
   $Dongia = $_POST['Dongia'];
   $Dongia = filter_var($Dongia, FILTER_SANITIZE_STRING);
   $Theloai = $_POST['Theloai'];
   $Theloai = filter_var($Theloai, FILTER_SANITIZE_STRING);
   $NSX = $_POST['NSX'];
   $NSX = filter_var($NSX, FILTER_SANITIZE_STRING);
   $Tinhtrang = $_POST['Tinhtrang'];
   $Tinhtrang = filter_var($Tinhtrang, FILTER_SANITIZE_STRING);
if ($Tinhtrang == 'Trống') {
   $update_chatluong = $conn->prepare("UPDATE `bangdia` SET ChatLuong = 'Tốt' WHERE MaBD = ?");
   $update_chatluong->execute([$MaBD]);
}
   // Kiểm tra MaBD có trong chitietphieunhap không
$stmt_check = $conn->prepare("SELECT 1 FROM chitietphieunhap WHERE MaBD = ?");
$stmt_check->execute([$MaBD]);

if ($stmt_check->rowCount() == 0) {
   $message[] = "❌ Mã băng đĩa '$MaBD' chưa được nhập kho! Không thể cập nhật.";
} else {
   // Cho phép cập nhật nếu mã tồn tại
   $update_product = $conn->prepare("UPDATE `bangdia` SET TenBD = ?, Dongia = ?, Theloai = ?, NSX = ?, Tinhtrang = ? WHERE MaBD = ?");
   $update_product->execute([$TenBD, $Dongia, $Theloai, $NSX, $Tinhtrang, $MaBD]);

   $old_image = $_POST['old_image'];
   $image = $_FILES['img']['name'];
   $image = filter_var($image, FILTER_SANITIZE_STRING);
   $image_size = $_FILES['img']['size'];
   $image_tmp_name = $_FILES['img']['tmp_name'];
   $image_folder = '../uploaded_img/' . $image;

   if (!empty($image)) {
      if ($image_size > 2000000) {
         $message[] = 'Kích thước ảnh quá lớn!';
      } else {
         $update_image = $conn->prepare("UPDATE `bangdia` SET image = ? WHERE MaBD = ?");
         $update_image->execute([$image, $MaBD]);
         move_uploaded_file($image_tmp_name, $image_folder);
         if (file_exists('../uploaded_img/' . $old_image)) {
            unlink('../uploaded_img/' . $old_image);
         }
         $message[] = '✅ Hình ảnh đã được cập nhật!';
      }
   }

   $message[] = '✅ Cập nhật thông tin băng đĩa thành công!';
   header("Location: products.php");
   exit();
}


}


?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Cập nhật sản phẩm</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>

<?php include '../components/admin_header.php' ?>

<!-- update product section starts  -->

<section class="update-product">
   <form action="" method="POST" enctype="multipart/form-data">

   <h3 class="heading">Cập nhật sản phẩm</h3>

   <?php
      $update_id = $_GET['update'];
      $show_products = $conn->prepare("SELECT * FROM `bangdia` WHERE MaBD = ?");
      $show_products->execute([$update_id]);
      if($show_products->rowCount() > 0){
         while($fetch_products = $show_products->fetch(PDO::FETCH_ASSOC)){  
   ?>

   <input type="hidden" name="MaBD" value="<?= $fetch_products['MaBD']; ?>">
   
   <input type="hidden" name="old_image" value="<?= $fetch_products['image']; ?>">
   
    <img src="../uploaded_img/<?= $fetch_products['image']; ?>" alt="Ảnh băng đĩa"   >  
   <br>
   <div class="order_table">
   <span>Mã băng đĩa</span>
   <input type="text" name="MaBD" value="<?= $fetch_products['MaBD']; ?>" class="box" readonly>
         </div>
         <div class="order_table">
   <span>Tên băng đĩa</span>
   <input type="text" name="TenBD" value="<?= $fetch_products['TenBD']; ?>" class="box" required>
         </div>
         <div class="order_table">
   <span>Đơn giá</span>
   <input type="number" name="Dongia" value="<?= $fetch_products['Dongia']; ?>" placeholder="<?= $fetch_products['Dongia']; ?>" class="box" required>
         </div>
         <div class="order_table">
   <span>Thể loại</span>
   <select name="Theloai" class="box" required>
      <option value="Âm nhạc" <?= ($fetch_products['Theloai'] == 'Âm nhạc') ? 'selected' : ''; ?>>Âm nhạc</option>
      <option value="Phim" <?= ($fetch_products['Theloai'] == 'Phim') ? 'selected' : ''; ?>>Phim</option>
      <option value="Khác" <?= ($fetch_products['Theloai'] == 'Khác') ? 'selected' : ''; ?>>Khác</option>
   </select>
         </div>
         <div class="order_table">
   <span>Nhà sản xuất</span>
   <input type="text" name="NSX" value="<?= $fetch_products['NSX']; ?>" class="box" required>
         </div>
         <div class="order_table">
   <span>Tình trạng</span>
   <select name="Tinhtrang" class="box" required>
      <option value="Trống" <?= ($fetch_products['Tinhtrang'] == 'Trống') ? 'selected' : ''; ?>>Trống</option>
      <option value="Đã cho thuê" <?= ($fetch_products['Tinhtrang'] == 'Đã cho thuê') ? 'selected' : ''; ?>>Đã cho thuê</option>
      <option value="Đang bảo trì" <?= ($fetch_products['Tinhtrang'] == 'Đang bảo trì') ? 'selected' : ''; ?>>Đang bảo trì</option>
   </select>
         </div>
         <div class="order_table">
   <span>Hình ảnh</span>
   <input type="file" name="img" class="box" accept="image/*">
         </div>
   <div class="flex-btn">
      <input type="submit" name="update" value="Cập nhật" class="btn">
      <a href="products.php" class="option-btn">Quay lại</a>
   </div>
</form>

   <?php
         }
      }else{
         echo '<p class="empty">Chưa có sản phẩm nào được thêm vào!</p>';
      }
   ?>

</section>

<!-- update product section ends -->










<!-- custom js file link  -->
<script src="../js/admin_script.js"></script>

</body>
</html>