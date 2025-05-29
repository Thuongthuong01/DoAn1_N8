<?php


include '../components/connect.php';

session_start();

if (!isset($_SESSION["user_id"])) {
   header("Location:admin_login");
   exit();
}

if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $confirm = isset($_GET['confirm']) ? $_GET['confirm'] : 'no';
    $message = [];

    // Lấy thông tin khách hàng
    $stmt = $conn->prepare("SELECT TenKH FROM khachhang WHERE MaKH = ?");
    $stmt->execute([$delete_id]);
    $khach = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$khach) {
        $message[] = "❌ Khách hàng không tồn tại!";
    } else {
        // Kiểm tra phiếu thuê và phiếu trả
        $tables = ['phieuthue' => 'phiếu thuê', 'phieutra' => 'phiếu trả'];
        $has_data = false;
        $info = [];

        foreach ($tables as $table => $label) {
            $stmt = $conn->prepare("SELECT COUNT(*) FROM $table WHERE MaKH = ?");
            $stmt->execute([$delete_id]);
            $count = $stmt->fetchColumn();
            if ($count > 0) {
                $has_data = true;
                $info[] = "$count $label";
            }
        }

        if ($confirm !== 'yes' && $has_data) {
            // Cảnh báo xác nhận xoá
            $details = implode(', ', $info);
            echo "<script>
                if (confirm('⚠️ Khách hàng này đang có: {$details}. Nếu xoá KH sẽ xoá kèm {$details}. Bạn có chắc chắn muốn xóa?')) {
                    window.location.href = '?delete={$delete_id}&confirm=yes';
                } else {
                    window.location.href = 'users_accounts.php';
                }
            </script>";
        } else {
            try {
                // Nếu có phiếu thì nên xoá luôn phiếu trước khi xoá khách hàng (nếu muốn xoá cứng)
                foreach (array_keys($tables) as $table) {
                    $delete_stmt = $conn->prepare("DELETE FROM $table WHERE MaKH = ?");
                    $delete_stmt->execute([$delete_id]);
                }

                // Xoá khách hàng
                $del = $conn->prepare("DELETE FROM khachhang WHERE MaKH = ?");
                $del->execute([$delete_id]);

                $message[] = "✅ Đã xoá khách hàng thành công!";
            } catch (PDOException $e) {
                $message[] = "❌ Lỗi hệ thống: " . $e->getMessage();
            }
        }
    }
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