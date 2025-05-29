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

    // Lấy thông tin admin cần xóa
    $stmt = $conn->prepare("SELECT TenAD FROM quantri WHERE MaAD = ?");
    $stmt->execute([$delete_id]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$admin) {
        $message[] = "❌ Tài khoản không tồn tại!";
    } elseif ($delete_id == $_SESSION['user_id']) {
        $message[] = "❌ Không thể xóa tài khoản đang đăng nhập!";
    } elseif (strtolower($admin['TenAD']) === 'admin') {
        $message[] = "❌ Không thể xóa tài khoản quản trị viên có tên admin!";
    } else {
        // Đếm liên kết với 3 bảng
        $tables = ['phieuthue' => 'phiếu thuê', 'phieutra' => 'phiếu trả', 'phieunhap' => 'phiếu nhập'];
        $has_data = false;
        $info = [];

        foreach ($tables as $table => $label) {
            $stmt = $conn->prepare("SELECT COUNT(*) FROM $table WHERE MaAD = ?");
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
                if (confirm('⚠️ Tài khoản này đang liên kết với: {$details}. Bạn có chắc chắn muốn xóa?')) {
                    window.location.href = '?delete={$delete_id}&confirm=yes';
                } else {
                    window.location.href = 'admin_accounts.php'; //về trang chủ
                }
            </script>";
        } else {
            // Tiến hành chuyển quyền và xoá như cũ
            try {
                $default_stmt = $conn->prepare(
                    "SELECT MaAD FROM quantri WHERE LOWER(TenAD) = 'admin' LIMIT 1"
                );
                $default_stmt->execute();
                $default = $default_stmt->fetch(PDO::FETCH_ASSOC);

                if (!$default) {
                    throw new Exception("Không tìm thấy admin mặc định để chuyển quyền!");
                }
                $default_admin_id = $default['MaAD'];

                foreach (array_keys($tables) as $table) {
                    $check = $conn->prepare("SELECT COUNT(*) FROM {$table} WHERE MaAD = ?");
                    $check->execute([$delete_id]);
                    $count = (int)$check->fetchColumn();

                    if ($count > 0) {
                        $upd = $conn->prepare("UPDATE {$table} SET MaAD = ? WHERE MaAD = ?");
                        $upd->execute([$default_admin_id, $delete_id]);
                        $message[] = "🔄 Đã chuyển quyền trong bảng {$table}.";
                    }
                }

                $del = $conn->prepare("DELETE FROM quantri WHERE MaAD = ?");
                $del->execute([$delete_id]);

                $message[] = "✅ Đã xoá tài khoản thành công!";
            } catch (PDOException $e) {
                $message[] = "❌ Lỗi hệ thống: " . $e->getMessage();
            } catch (Exception $e) {
                $message[] = "❌ " . $e->getMessage();
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
   <title>Quản lý quản trị viên</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>
<style>
   .disabled-text {
   color: #aaa;
   font-style: italic;
   cursor: not-allowed;
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
            <th class="sortable" data-index="0">ID</th>
            <th class="sortable" data-index="1">Tên tài khoản</th>
            <th class="sortable" data-index="2">SĐT</th>
            <th class="sortable" data-index="3">Email</th>
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
            <?php if ($fetch_admin['MaAD'] == $_SESSION['user_id']): ?>
               <a href="update_profile_admin.php?update=<?= $fetch_admin['MaAD']; ?>" class="btn btn-update">Cập nhật</a>
            <?php else: ?>
               <span class="disabled-text" >Không thể sửa</span>
            <?php endif; ?>
            <?php if ($fetch_admin['MaAD'] != $_SESSION['user_id']): ?>
               <a href="admin_accounts.php?delete=<?= $fetch_admin['MaAD']; ?>" class="btn btn-delete" onclick="return confirm('Xoá quản trị viên?');">Xoá</a>
            <?php else: ?>
               <span class="disabled-text" >Không thể xoá</span>
            <?php endif; ?>
         </td>

         </tr>
         <?php
               }
            } else {
               echo '<tr><td colspan="8" class="empty">Chưa có tài khoản nào được thêm vào!</td></tr>';
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