<?php


include '../components/connect.php';
session_start();
if (!isset($_SESSION["user_id"])) {
   header("Location:admin_login");
   exit();
}

if (isset($_POST['add_supplier'])) {
    // Lấy dữ liệu và lọc bớt khoảng trắng
    $MaNCC = trim($_POST['MaNCC']);
    $TenNCC = trim($_POST['TenNCC']);
    $SDT = trim($_POST['SDT']);
    $DiaChi = trim($_POST['DiaChi']);
    $message = [];

    // Kiểm tra dữ liệu đầu vào
    if (empty($MaNCC) || empty($TenNCC) || empty($SDT) || empty($DiaChi)) {
        $message[] = "❌ Vui lòng điền đầy đủ thông tin!";
    } elseif (!preg_match('/^[0-9]{10}$/', $SDT)) {
        $message[] = "❌ Số điện thoại phải đúng 10 chữ số!";
    } else {
        try {
            // Kiểm tra mã NCC đã tồn tại chưa
            $check_stmt = $conn->prepare("SELECT MaNCC FROM nhacc WHERE MaNCC = ?");
            $check_stmt->execute([$MaNCC]);

            if ($check_stmt->rowCount() > 0) {
                $message[] = "❌ Mã nhà cung cấp đã tồn tại, vui lòng nhập mã khác!";
            } else {
                // Thêm mới nhà cung cấp
                $insert_stmt = $conn->prepare("INSERT INTO nhacc (MaNCC, TenNCC, SDT, DiaChi) VALUES (?, ?, ?, ?)");
                if ($insert_stmt->execute([$MaNCC, $TenNCC, $SDT, $DiaChi])) {
                    $message[] = "✅ Đã thêm nhà cung cấp thành công!";
                } else {
                    $message[] = "❌ Thêm thất bại, hãy kiểm tra lại dữ liệu!";
                }
            }
        } catch (PDOException $e) {
            $message[] = "❌ Lỗi hệ thống: " . $e->getMessage();
        }
    }
}
// Xử lý xoá nhà cung cấp (nếu có)
if (isset($_GET['delete_ncc'])) {
    $delete_id = $_GET['delete_ncc'];

    try {
        $delete_ncc = $conn->prepare("DELETE FROM nhacc WHERE MaNCC = ?");
        if ($delete_ncc->execute([$delete_id])) {
            $message[] = "✅ Đã xoá nhà cung cấp thành công!";
        } else {
            $message[] = "❌ Xoá thất bại!";
        }
    } catch (PDOException $e) {
        $message[] = "❌ Lỗi khi xoá: " . $e->getMessage();
    }
}

?> 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Nhà cung cấp</title>
       <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="../css/admin_style.css">
<style>
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

<section class="form-container" >
   <form action="" method="POST">
      <h3>Thêm nhà cung cấp</h3>
<div class="order_table">
        <span style="min-width: 160px;font-size:1.8rem; text-align: left;">Mã nhà cung cấp:</span>
      <input type="text" required placeholder="" name="MaNCC" maxlength="10" class="box">
</div>
<div class="order_table">
           <span style="min-width: 160px;font-size:1.8rem; text-align: left;">Tên nhà cung cấp:</span>
      <input type="text" required placeholder="" name="TenNCC" maxlength="100" class="box">
</div>
<div class="order_table">
           <span style="min-width: 160px;font-size:1.8rem; text-align: left;">Số điện thoại:</span>
      <input type="number" required placeholder="" name="SDT" maxlength="15" class="box">
</div>
<div class="order_table">
           <span style="min-width: 160px;font-size:1.8rem; text-align: left;">Địa chỉ:</span>
      <input type="text" required placeholder="" name="DiaChi" maxlength="255" class="box">
</div>
      <input type="submit" value="Thêm nhà cung cấp" name="add_supplier" class="btn">
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

<section class="main-content show-products" style="padding-top: 0;">
   <h1 class="heading">Danh sách Nhà cung cấp</h1>
      <table class="product-table">
   <thead>
      <tr>
         <th class="sortable" data-index="0">Mã NCC</th>
         <th class="sortable" data-index="1">Tên nhà cung cấp</th>
         <th class="sortable" data-index="2">SĐT</th>
         <th class="sortable" data-index="3">Địa chỉ</th>
         <th >Chức năng</th>
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
         <td><?= htmlspecialchars($ncc['SDT']); ?></td>
         <td><?= $ncc['DiaChi']; ?></td>
         <td>
            <a href="update_supplier.php?update=<?= $ncc['MaNCC']; ?>" class="btn btn-update">Sửa</a>
            <a href="?delete_ncc=<?= $ncc['MaNCC']; ?>" class="btn delete-btn" onclick="return confirm('Bạn có chắc muốn xóa nhà cung cấp này?');">Xóa</a>
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
</body>
<!-- hiển thị thông báo -->
</html>