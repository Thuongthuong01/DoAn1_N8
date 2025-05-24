<?php


include '../components/connect.php';

session_start();

if (!isset($_SESSION["user_id"])) {
   header("Location:admin_login");
   exit();
}

if(isset($_GET['delete'])){
   $delete_id = $_GET['delete'];
   $delete_admin = $conn->prepare("DELETE FROM `quantri` WHERE MaAD = ?");
   $delete_admin->execute([$delete_id]);
   header('location:admin_accounts.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Quản lý quản trị viên</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>

<?php include '../components/admin_header.php' ?>


<section class="accounts">

   <h1 class="heading">Tài khoản quản trị viên</h1>

   <div class="box-container">

   <div class="box">
      <p>Đăng ký tài khoản mới</p>
      <a href="register_admin.php" class="option-btn">Đăng ký</a>
   </div>
</section>
<section class="main-content show-products" style="padding-top: 0;">
<h1 class="heading">Danh sách quản trị</h1>
   <table class="product-table">
      <thead>
         <tr>
            <th>ID</th>
            <th>Tên tài khoản</th>
            <th>SĐT</th>
            <th>Email</th>
            <th>Chức năng</th>
         </tr>
      </thead>
      <tbody>
      <?php
            $show_admins = $conn->prepare("SELECT * FROM quantri");
            $show_admins->execute();
            if ($show_admins->rowCount() > 0) {
               while ($fetch_admin = $show_admins->fetch(PDO::FETCH_ASSOC)) {
         ?>
         <tr>
            <td><?= $fetch_admin['MaAD']; ?></td>
            <td><?= $fetch_admin['TenAD']; ?></td>
            <td><?= $fetch_admin['SDT']; ?></td>
            <td><?= $fetch_admin['Email']; ?></td>
            <td>
               <a href="update_profile_admin.php?update=<?= $fetch_admin['MaAD']; ?>" class="btn btn-update">Cập nhật</a>
               <a href="admin_accounts.php?delete=<?= $fetch_admin['MaAD']; ?>" class="btn btn-delete" onclick="return confirm('Xoá sản phẩm?');">Xoá</a>
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