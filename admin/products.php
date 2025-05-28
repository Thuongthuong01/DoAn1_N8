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

    // Kiểm tra băng đĩa này có đang được thuê không
    $check = $conn->prepare("SELECT * FROM chitietphieuthue WHERE MaBD = ?");
    $check->execute([$delete_id]);

    if ($check->rowCount() > 0 && !isset($_GET['confirm'])) {
        // Nếu có phiếu thuê liên quan và chưa xác nhận xoá
        echo "<script>
            if (confirm('❗ Sản phẩm đang có trong phiếu thuê. Bạn có chắc muốn xoá không?')) {
                window.location.href = 'products.php?delete={$delete_id}&confirm=yes';
            } else {
                window.location.href = 'products.php';
            }
        </script>";
        exit(); // Dừng xử lý tiếp cho đến khi người dùng xác nhận
    }

    // Xoá chi tiết phiếu thuê trước
    $delete_ctpt = $conn->prepare("DELETE FROM chitietphieuthue WHERE MaBD = ?");
    $delete_ctpt->execute([$delete_id]);

    // Xoá băng đĩa
    $delete_product = $conn->prepare("DELETE FROM bangdia WHERE MaBD = ?");
    $delete_product->execute([$delete_id]);

    $message[] = '✅ Đã xoá băng đĩa thành công!';
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


<!-- thêm sản phẩm -->
<section class="form-container" >
   <form action="" method="POST" enctype="multipart/form-data">
      <h3>Thêm sản phẩm</h3>
<div class="order_table">
   <span style="min-width: 160px;font-size:1.8rem; text-align: left;">Mã băng đĩa:</span>
<select name="MaBD" class="box" required>
   <option value="" disabled selected>-- Chọn mã --</option>
   <?php foreach ($availableMaBDs as $maBD): ?>
      <option value="<?= htmlspecialchars($maBD) ?>"><?= htmlspecialchars($maBD) ?></option>
   <?php endforeach; ?>
</select>
   </div>
<div class="order_table">
           <span style="min-width: 160px;font-size:1.8rem; text-align: left;">Tên băng đĩa:</span>
      <input type="text" required placeholder="" name="TenBD" maxlength="100" class="box">
   </div>
<div class="order_table">
           <span style="min-width: 160px;font-size:1.8rem; text-align: left;">Đơn giá thuê:</span>
      <input type="number" min="0" max="9999999999" required placeholder="" name="Dongia" onkeypress="if(this.value.length == 10) return false;" class="box">
   </div>
<div class="order_table">
           <span style="min-width: 160px;font-size:1.8rem; text-align: left;">Thể loại:</span>
      <select name="Theloai" class="box" required>
         <option value="" disabled selected>--Chọn thể loại --</option>
         <option value="Âm nhạc">Âm nhạc</option>
         <option value="Phim">Phim</option>
         <option value="Khác">Khác</option>
      </select>
   </div>
<div class="order_table">
           <span style="min-width: 160px;font-size:1.8rem; text-align: left;">Nhà sản xuất:</span>
      <input type="text" name="NSX" placeholder="" class="box" required>
   </div>
<div class="order_table">
           <span style="min-width: 160px;font-size:1.8rem; text-align: left;">Tình trạng:</span>
      <select name="Tinhtrang" class="box" required>
         <option value="" disabled selected>-- Chọn tình trạng --</option>
         <option value="Trống">Trống</option>
         <option value="Đã cho thuê">Đã cho thuê</option>
         <option value="Đang bảo trì">Đang bảo trì</option>
      </select>
   </div>
<div class="order_table">
           <span style="min-width: 160px;font-size:1.8rem; text-align: left;">Chất lượng:</span>
      <select name="ChatLuong" class="box" required>
   <option value="" disabled selected>-- Chọn chất lượng --</option>
      <option value="Tốt">Tốt</option>
      <option value="Trầy xước">Trầy xước</option>
      <option value="Hỏng nặng">Hỏng nặng</option>      
      <option value="Mất">Mất</option>
</select>
   </div>
<div class="order_table">
   
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
            <th class="sortable" data-index="0">Mã đĩa</th>
      <th>Ảnh</th>
      <th class="sortable" data-index="2">Tên băng đĩa</th>
      <th class="sortable" data-index="3">Thể loại</th>
      <th class="sortable" data-index="4">Đơn giá thuê</th>
      <th class="sortable" data-index="5">NSX</th>
      <th class="sortable" data-index="6">Tình trạng</th>
      <th class="sortable" data-index="7">Chất lượng</th>
      <th>Chức năng</th>
         </tr>
         <tr class="filter-row">
    <th><input type="text" placeholder="Lọc mã đĩa" data-index="0"></th>
    <th></th>
    <th><input type="text" placeholder="Lọc tên" data-index="2"></th>
    <th><input type="text" placeholder="Lọc thể loại" data-index="3"></th>
    <th><input type="text" placeholder="Lọc giá" data-index="4"></th>
    <th><input type="text" placeholder="Lọc NSX" data-index="5"></th>
    <th><input type="text" placeholder="Lọc tình trạng" data-index="6"></th>
    <th><input type="text" placeholder="Lọc chất lượng" data-index="7"></th>
    <th></th>
    
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
               <a href="update_product.php?update=<?= $fetch_products['MaBD']; ?>" class="btn btn-update">Cập nhật</a>
               <a href="products.php?delete=<?= $fetch_products['MaBD']; ?>" class="btn btn-delete" onclick="return confirm('Xoá sản phẩm?');">Xoá</a>
            </td>
         </tr>
         <?php
               }
            } else {
               echo '<tr><td colspan="9" class="empty">Chưa có sản phẩm nào được thêm vào!</td></tr>';
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
<script>
document.querySelectorAll(".filter-row input").forEach((input) => {
  input.addEventListener("input", () => {
    const table = input.closest("table");
    const tbody = table.querySelector("tbody");
    const rows = tbody.querySelectorAll("tr");
    const filters = {};

    document.querySelectorAll(".filter-row input").forEach(i => {
      if (i.value.trim() !== "") {
        filters[i.dataset.index] = i.value.trim().toLowerCase();
      }
    });

    rows.forEach(row => {
      const cells = row.querySelectorAll("td");
      let visible = true;

      for (let index in filters) {
        const cellText = cells[index]?.textContent.toLowerCase() || "";
        if (!cellText.includes(filters[index])) {
          visible = false;
          break;
        }
      }

      row.style.display = visible ? "" : "none";
    });
  });
});
</script>


<!-- custom js file link  -->
<script src="../js/admin_script.js"></script>

</body>
</html>
