<?php


include '../components/connect.php';

session_start();

if (!isset($_SESSION["user_id"])) {
   header("Location:admin_login");
   exit();
}
$user_id = $_SESSION['user_id'];
$tenAD = '';

$stmt = $conn->prepare("SELECT TenAD FROM quantri WHERE MaAD = ?");
$stmt->execute([$user_id]);
if ($stmt->rowCount() > 0) {
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $tenAD = $row['TenAD'];
}
if (isset($_POST['submit'])) {
   $maPhieu = $_POST['MaPhieu'];
   $maNCC = $_POST['MaNCC'];
   $ngayNhap = $_POST['NgayNhap'];
   $soLuong = $_POST['SoLuong'];
   $tongTien = $_POST['TongTien'];
   $maAD = $_SESSION['user_id'];
   $errors = [];

   // Kiểm tra MaNCC có tồn tại không
   $stmt = $conn->prepare("SELECT * FROM nhacc WHERE MaNCC = ?");
   $stmt->execute([$maNCC]);
   if ($stmt->rowCount() == 0) {
      $errors[] = "❌ Mã nhà cung cấp không tồn tại!";
   }

   // Kiểm tra MaPhieu trùng
   $check_stmt = $conn->prepare("SELECT MaPhieu FROM phieunhap WHERE MaPhieu = ?");
   $check_stmt->execute([$maPhieu]);
   if ($check_stmt->rowCount() > 0) {
      $errors[] = "❌ Mã phiếu đã tồn tại. Vui lòng nhập mã khác!";
   }

   // Kiểm tra mã băng đĩa trùng trong CSDL
   // Kiểm tra mã băng đĩa đã tồn tại trong bảng bangdia chưa
$maBDValues = [];

for ($i = 1; $i <= $soLuong; $i++) {
   $maBD = $_POST["MaBD_$i"];

   if (in_array($maBD, $maBDValues)) {
      $errors[] = "❌ Mã băng đĩa '$maBD' bị nhập trùng trong cùng một phiếu!";
   } else {
      $maBDValues[] = $maBD;
   }

   // Không cần kiểm tra trùng trong bangdia khi nhập phiếu
// Nhưng cần kiểm tra xem MaBD có tồn tại trong bảng chitietphieunhap không khi thêm băng đĩa (trong phần khác của hệ thống)

}



   // Nếu không có lỗi thì mới insert
   if (empty($errors)) {
       // Lấy mã quản trị viên đang đăng nhập

$insert = $conn->prepare("INSERT INTO phieunhap (MaPhieu, MaNCC, NgayNhap, SoLuong, TongTien, MaAD) VALUES (?, ?, ?, ?, ?, ?)");
$insert->execute([$maPhieu, $maNCC, $ngayNhap, $soLuong, $tongTien, $maAD]);


      for ($i = 1; $i <= $soLuong; $i++) {
         $maBD = $_POST["MaBD_$i"];
         $giaGoc = $_POST["GiaGoc_$i"];
         $insert_ct = $conn->prepare("INSERT INTO chitietphieunhap (MaPhieu, MaBD, GiaGoc) VALUES (?, ?, ?)");
         $insert_ct->execute([$maPhieu, $maBD, $giaGoc]);
      }

      $message[] = "✅ Đã thêm phiếu nhập thành công!";
   } else {
      $message = $errors;
   }
}
if (isset($_GET['delete_phieunhap'])) {
   $delete_id = $_GET['delete_phieunhap'];

   // Xoá chi tiết phiếu nhập trước
   $delete_ct = $conn->prepare("DELETE FROM chitietphieunhap WHERE MaPhieu = ?");
   $delete_ct->execute([$delete_id]);

   // Sau đó mới xoá phiếu nhập
   $delete_phieunhap = $conn->prepare("DELETE FROM phieunhap WHERE MaPhieu = ?");
   $delete_phieunhap->execute([$delete_id]);

   $message[] = "✅ Đã xoá phiếu nhập thành công !";
}


?> 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý kho nhập</title>
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

<?php
// Kết nối CSDL nếu chưa có
// $conn = new PDO(...);

// Truy vấn danh sách nhà cung cấp
$stmt = $conn->prepare("SELECT MaNCC, TenNCC FROM nhacc");
$stmt->execute();
$ds_ncc = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<section class="form-container" >
   <form method="post">
      <h3>Phiếu nhập băng đĩa</h3>
      <!-- phần cuối form -->
<div style="margin-top: 10px; font-size: 1.4rem; color:rgb(132, 130, 130);">
    <strong>Người nhập phiếu:</strong> <?= htmlspecialchars($tenAD) ?>
</div>

<div class="order_table">
           <span style="min-width: 160px;font-size:1.8rem; text-align: left;">Mã phiếu:</span>
        <input type="text"class="box" name="MaPhieu" required placeholder="" 
        value="<?= htmlspecialchars($_POST['MaPhieu'] ?? '') ?>">
        
        </div>
<div class="order_table">
           <span style="min-width: 160px;font-size:1.8rem; text-align: left;">Mã nhà cung cấp:</span>
        <select name="MaNCC" class="box" required>
    <option value="">-- Chọn nhà cung cấp --</option>
    <?php foreach ($ds_ncc as $ncc): ?>
        <option value="<?= htmlspecialchars($ncc['MaNCC']) ?>"
            <?= (isset($_POST['MaNCC']) && $_POST['MaNCC'] == $ncc['MaNCC']) ? 'selected' : '' ?>>
            <?= htmlspecialchars($ncc['MaNCC'] . ' - ' . $ncc['TenNCC']) ?>
        </option>
    <?php endforeach; ?>
</select>

        </div>
<div class="order_table">
           <span style="min-width: 160px;font-size:1.8rem; text-align: left;">Ngày nhập:</span>
         <!-- <input type="date" name="NgayNhap" required> -->
        <input type="date"class="box" name="NgayNhap" required 
       value="<?= htmlspecialchars($_POST['NgayNhap'] ?? '') ?>">
      </div>

<div class="order_table">
           <span style="min-width: 160px;font-size:1.8rem; text-align: left;">Số lượng:</span>
         <input type="number" class="box"name="SoLuong" id="SoLuong" min="1" max="30" required placeholder="" oninput="generateFields()">
        </div>

      <div class="dynamic-fields" id="dynamicFields">
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['SoLuong'])) {
    $soLuong = (int)$_POST['SoLuong'];
    for ($i = 1; $i <= $soLuong; $i++) {
        $maBD = $_POST["MaBD_$i"] ?? '';
        $giaGoc = $_POST["GiaGoc_$i"] ?? '';
        echo '<input type="text" name="MaBD_'.$i.'" placeholder="Mã băng đĩa '.$i.'" required value="'.htmlspecialchars($maBD).'">';
        echo '<input type="number" name="GiaGoc_'.$i.'" placeholder="Đơn giá '.$i.'" required value="'.htmlspecialchars($giaGoc).'" class="don-gia">';
        }
    }
    ?>
</div>


<div class="order_table">
           <span style="min-width: 160px;font-size:1.8rem; text-align: left;">Tổng tiền:</span>
         <input type="number"class="box" name="TongTien" required placeholder=" ">
        <!-- <input type="number" name="TongTien" required 
        placeholder="Tổng đơn giá " 

        value="<?= htmlspecialchars($_POST['TongTien'] ?? '') ?>"> -->
        </div>
<div class="flex-btn">
      <input type="submit" name="submit" value="Thêm" class="btn">
      <!-- <a href="warehouse.php" class="option-btn">Quay lại</a> -->
   </div>
   </form>

   <!-- hiển thị thông báo -->
<?php if (!empty($message) && is_array($message)): ?>
   <?php foreach ($message as $msg): ?>
      <div class="message" style="background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 10px; margin: 10px 0; border-radius: 5px;">
         <span><?= $msg; ?></span>
         <i onclick="this.parentElement.style.display='none';" style="cursor:pointer; float:right;">&times;</i>
      </div>
   <?php endforeach; ?>
<?php endif; ?>
</section>


<script>
function generateFields() {
   const sl = document.getElementById('SoLuong').value;
   const container = document.getElementById('dynamicFields');
   container.innerHTML = "";

   const soLuong = parseInt(sl);
   if (!isNaN(soLuong) && soLuong > 0 && soLuong <= 30) {
      for (let i = 1; i <= soLuong; i++) {
         const maBD = document.createElement("input");
         maBD.type = "text";
         maBD.name = `MaBD_${i}`;
         maBD.placeholder = `Mã băng đĩa ${i}`;
         maBD.required = true;

         const giaGoc = document.createElement("input");
         giaGoc.type = "number";
         giaGoc.name = `GiaGoc_${i}`;
         giaGoc.placeholder = `Đơn giá ${i}` ;
         giaGoc.min = 0;
         giaGoc.required = true;
         giaGoc.classList.add("don-gia");
         giaGoc.addEventListener("input", calculateTotal);

         container.appendChild(maBD);
         container.appendChild(giaGoc);
      }
   }

   calculateTotal(); // gọi ngay để reset tổng tiền
}

function calculateTotal() {
   let total = 0;
   const giaGocFields = document.querySelectorAll(".don-gia");
   giaGocFields.forEach(input => {
      const val = parseInt(input.value);
      if (!isNaN(val)) {
         total += val;
      }
   });

   const tongTienInput = document.querySelector('input[name="TongTien"]');
   tongTienInput.value = total;
}
document.querySelector("form").addEventListener("submit", function (e) {
   const maBDInputs = document.querySelectorAll('input[name^="MaBD_"]');
   const maBDValues = [];

   for (let input of maBDInputs) {
      const val = input.value.trim();
      if (maBDValues.includes(val)) {
         alert("❌ Mã băng đĩa bị trùng: " + val);
         e.preventDefault(); // chặn submit
         return;
      }
      maBDValues.push(val);
   }
});
</script>
<section class="main-content show-products" style="padding-top: 0;">
   <h1 class="heading">Danh sách phiếu nhập hàng</h1>
   <table class="product-table">
      <thead>
         <tr>
            <th class="sortable" data-index="0">Mã phiếu</th>
            <th class="sortable" data-index="1">Mã NCC</th>
            <th class="sortable" data-index="2">Ngày nhập</th>
            <th class="sortable" data-index="3">Số lượng</th>
            <th class="sortable" data-index="4">Chi tiết băng đĩa (Mã BD - Giá gốc)</th>
            <th class="sortable" data-index="5">Tổng tiền</th>
            <th>Chức năng</th>
         </tr>
      </thead>
      <tbody>
         <?php
            // Lấy danh sách phiếu nhập
            $show_phieunhap = $conn->prepare("SELECT * FROM phieunhap ORDER BY NgayNhap DESC");
            $show_phieunhap->execute();

            if ($show_phieunhap->rowCount() > 0) {
               while ($phieu = $show_phieunhap->fetch(PDO::FETCH_ASSOC)) {
                  // Lấy chi tiết băng đĩa theo mã phiếu
                  $maPhieu = $phieu['MaPhieu'];
                  $ct_stmt = $conn->prepare("SELECT MaBD, GiaGoc FROM chitietphieunhap WHERE MaPhieu = ?");
                  $ct_stmt->execute([$maPhieu]);
                  
                  $chiTietBD = [];
                  while ($ct = $ct_stmt->fetch(PDO::FETCH_ASSOC)) {
                     $chiTietBD[] = $ct['MaBD'] . " - " . number_format($ct['GiaGoc'], 0, ',', '.') . " VNĐ";
                  }
                  
                  $chiTietStr = implode("<br>", $chiTietBD);
         ?>
         <tr>
            <td><?= htmlspecialchars($phieu['MaPhieu']); ?></td>
            <td><?= htmlspecialchars($phieu['MaNCC']); ?></td>
            <td><?php echo date('d/m/Y', strtotime($phieu['NgayNhap'])); ?></td>
            <td><?= htmlspecialchars($phieu['SoLuong']); ?></td>
            <td style="white-space: nowrap;"><?= $chiTietStr; ?></td>
            <td><?= number_format($phieu['TongTien'], 0, ',', '.') . " VNĐ"; ?></td>
            <td>
               <!-- Ví dụ có thể thêm sửa xóa phiếu nhập -->
               <!-- <a href="update_phieunhap.php?update=<?= urlencode($phieu['MaPhieu']); ?>" class="btn btn-update">Sửa</a> -->
               <a href="?delete_phieunhap=<?= urlencode($phieu['MaPhieu']); ?>" class="btn delete-btn" onclick="return confirm('Bạn có chắc muốn xóa phiếu nhập này?');">Xóa</a>
               <a href="print_invoice.php?loai=nhap&id=<?= $phieu['MaPhieu']; ?>" target="_blank" class="btn btn-print">In hóa đơn</a>

            </td>
         </tr>
         <?php
               }
            } else {
               echo '<tr><td colspan="7" class="empty">Không có phiếu nhập nào!</td></tr>';
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
</html>