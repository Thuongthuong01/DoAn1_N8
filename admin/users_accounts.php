<?php


include '../components/connect.php';

session_start();

if (!isset($_SESSION["user_id"])) {
   header("Location:admin_login");
   exit();
}

if(isset($_GET['delete'])){
   $delete_id = $_GET['delete'];
   $delete_cart = $conn->prepare("DELETE FROM `phieutra` WHERE MaKH = ?");
   $delete_cart->execute([$delete_id]);
   $delete_order = $conn->prepare("DELETE FROM `phieuthue` WHERE MaKH = ?");
   $delete_order->execute([$delete_id]);
   $delete_users = $conn->prepare("DELETE FROM `khachhang` WHERE MaKH = ?");
   $delete_users->execute([$delete_id]);
   header('location:users_accounts.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Quản lý người dùng </title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">
<style>
   .btn-history {
    padding: 5px 10px;
    background-color: #007bff;
    color: white;
    border-radius: 3px;
    text-decoration: none;
    font-weight: 600;
}

.btn-history:hover {
    opacity: 0.85;
}

th.sortable {
  position: relative;
  cursor: pointer;
}

th.sortable::after {
  content: "⇅";
  position: absolute;
  right: 8px;
  opacity: 0;
  transition: opacity 0.2s;
  font-size: 1.3em;
  color: #888;
}

th.sortable:hover::after {
  opacity: 1;
}

th.sortable.sorted-asc::after {
  content: "↑";
  opacity: 1;
  font-size: 1.3em;
}

th.sortable.sorted-desc::after {
  content: "↓";
  opacity: 1;
  font-size: 1.3em;
}
.filter-row input {
  width: 95%;
  padding: 4px 6px;
  font-size: 13px;
  border: 1px solid #ccc;
  border-radius: 4px;
}

   </style>
</head>
<body>

<?php include '../components/admin_header.php' ?>



<section class="accounts">

   <h1 class="heading">Tài khoản khách hàng</h1>
 

   <div class="box-container">
   <div class="box">
      <p>Đăng ký tài khoản mới</p>
      <a href="register_user.php" class="option-btn">Đăng ký</a>
   </div>
   </section>
<section class="main-content show-products" style="padding-top: 0;">
<h1 class="heading">Danh sách khách hàng</h1>
   <table class="product-table">
      <thead>
         <tr>
            <th class="sortable" data-index="0">Mã KH</th>
            <th class="sortable" data-index="1">Tên KH</th>
            <th class="sortable" data-index="2">SĐT</th>
            <th class="sortable" data-index="3">Địa Chỉ</th>
            <th class="sortable" data-index="4">Email</th>
            <th>Lịch sử thuê</th>
            <th>Chức năng</th>
         </tr>
      </thead>
      <tbody>
      <?php
      $select_account = $conn->prepare("SELECT * FROM `khachhang`");
      $select_account->execute();
      if($select_account->rowCount() > 0){
         while($fetch_accounts = $select_account->fetch(PDO::FETCH_ASSOC)){  
   ?>
         <tr>
            <td><?= $fetch_accounts['MaKH']; ?></td>
            <td><?= $fetch_accounts['TenKH']; ?></td>
            <td><?= htmlspecialchars($fetch_accounts['SDT']); ?></td>
            <td><?= $fetch_accounts['Diachi']; ?></td>
            <td><?= $fetch_accounts['Email']; ?></td>
            <td>
               <a href="rental_history.php?MaKH=<?= $fetch_accounts['MaKH']; ?>" class="btn btn-history">Xem</a>
            </td>
            <td>
               <a href="update_profile_user.php?update=<?= $fetch_accounts['MaKH']; ?>" class="btn btn-update">Cập nhật</a>
               <a href="users_accounts.php?delete=<?= $fetch_accounts['MaKH']; ?>" class="btn btn-delete" onclick="return confirm('Xoá khách hàng?');">Xoá</a>
            </td>
         </tr>
         <?php
               }
            } else {
               echo '<tr><td colspan="8" class="empty">Chưa có khách hàng nào!</td></tr>';
            }
         ?>
      </tbody>
   </table>
</section>

<script>
let currentSortedIndex = -1;
let isAsc = true;

document.querySelectorAll("th.sortable").forEach(th => {
  th.addEventListener("click", () => {
    const table = th.closest("table");
    const tbody = table.querySelector("tbody");
    const index = parseInt(th.getAttribute("data-index"));
    const rows = Array.from(tbody.querySelectorAll("tr"));

    // Đảo chiều nếu click lại cùng cột
    if (index === currentSortedIndex) {
      isAsc = !isAsc;
    } else {
      isAsc = true;
      currentSortedIndex = index;
    }

    // Xóa class cũ
    table.querySelectorAll("th.sortable").forEach(t => {
      t.classList.remove("sorted-asc", "sorted-desc");
    });

    // Thêm class mới
    th.classList.add(isAsc ? "sorted-asc" : "sorted-desc");

    // Sắp xếp
    rows.sort((a, b) => {
      let aText = a.cells[index].textContent.trim();
      let bText = b.cells[index].textContent.trim();
      let aVal = isNaN(aText) ? aText.toLowerCase() : parseFloat(aText.replace(/[^\d.-]/g, ''));
      let bVal = isNaN(bText) ? bText.toLowerCase() : parseFloat(bText.replace(/[^\d.-]/g, ''));

      if (aVal < bVal) return isAsc ? -1 : 1;
      if (aVal > bVal) return isAsc ? 1 : -1;
      return 0;
    });

    // Gắn lại thứ tự vào bảng
    rows.forEach(row => tbody.appendChild(row));
  });
});
</script>
<script src="../js/admin_script.js"></script>

</body>
</html>