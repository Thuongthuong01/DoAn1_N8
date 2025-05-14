<?php


include '../components/connect.php';

session_start();

if (!isset($_SESSION["user_id"])) {
   header("Location:admin_login");
   exit();
}

if (isset($_GET['delete_ncc'])) {
   $delete_id = $_GET['delete_ncc'];
   $delete_ncc = $conn->prepare("DELETE FROM nhacc WHERE MaNCC = ?");
   $delete_ncc->execute([$delete_id]);
   $message[] = "Đã xoá nhà cung cấp thành công!";
}

?> 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý kho</title>
       <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

<!-- custom css file link  -->
<link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>
<?php include '../components/admin_header.php' ?>
<!-- <section class="warehouse"> -->
<section class="main-content show-products" style="padding-top: 0;">
   <h1 class="heading">Danh sách Nhà cung cấp</h1>
      <table class="product-table">
   <thead>
      <tr>
         <th>Mã NCC</th>
         <th>Tên Nhà Cung Cấp</th>
         <th>SĐT</th>
         <th>Địa chỉ</th>
         <th>Chức năng</th>
      </tr>
   </thead>
   <tbody>
      <?php
         $show_ncc = $conn->prepare("SELECT * FROM nhacc ");
         $show_ncc->execute();
         if ($show_ncc->rowCount() > 0) {
            while ($ncc = $show_ncc->fetch(PDO::FETCH_ASSOC)) {
      ?>
      <tr>
         <td><?= $ncc['MaNCC']; ?></td>
         <td><?= $ncc['TenNCC']; ?></td>
         <td><?= $ncc['SDT']; ?></td>
         <td><?= $ncc['DiaChi']; ?></td>
         <td>
            <a href="update_supplier.php?update=<?= $ncc['MaNCC']; ?>" class="btn btn-update">Sửa</a>
            <a href="?delete_ncc=<?= $ncc['MaNCC']; ?>" class="btn btn-delete" onclick="return confirm('Bạn có chắc muốn xóa nhà cung cấp này?');">Xóa</a>
         </td>
      </tr>
      <?php
            }
         } else {
            echo '<tr><td colspan="5" class="empty">Không có nhà cung cấp nào!</td></tr>';
         }
      ?>
   </tbody>
</table>


</section>

</body>
<!-- hiển thị thông báo -->
<?php if (!empty($message) && is_array($message)): ?>
   <?php foreach ($message as $msg): ?>
      <div class="message" style="background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 10px; margin: 10px 0; border-radius: 5px;">
         <span><?= $msg; ?></span>
         <i onclick="this.parentElement.style.display='none';" style="cursor:pointer; float:right;">&times;</i>
      </div>
   <?php endforeach; ?>
<?php endif; ?>
</html>