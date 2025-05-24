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
$message = [];

// Thêm đơn hàng thuê mới vào bảng PhieuThue
if (isset($_POST['add_phieuthue'])) {
   $maKH = $_POST['MaKH'];
   $ngayThue = $_POST['ngayThue'];
   $hanTra = $_POST['ngayTra'];
   $bangDiaList = json_decode($_POST['dsBangDia'], true);
   if (count($bangDiaList) > 10) {
    $message[] = "Không thể thuê quá 10 đĩa cùng lúc.";
} else {
    // Tiếp tục xử lý thêm phiếu thuê như hiện tại
    

   try {
      $conn->beginTransaction();

      // Tính số ngày thuê
      $ngayThueDate = new DateTime($ngayThue);
      $hanTraDate = new DateTime($hanTra);
      $soNgayThue = $ngayThueDate->diff($hanTraDate)->days;
      if ($soNgayThue == 0) $soNgayThue = 1;

      $tongDonGia = 0;
      $maAdmin = $_SESSION["user_id"];

      // Bước 1: Thêm phiếu thuê
      $insertPhieu = $conn->prepare("INSERT INTO phieuthue (MaKH, NgayThue, NgayTraDK, MaAD, TongTien) VALUES (?, ?, ?, ?, 0)");
      $insertPhieu->execute([$maKH, $ngayThue, $hanTra, $maAdmin]);
      $maThueMoi = $conn->lastInsertId();

      // Bước 2: Thêm chi tiết phiếu thuê
      $insertCT = $conn->prepare("INSERT INTO chitietphieuthue (MaThue, MaBD, SoLuong, DonGia) VALUES (?, ?, ?, ?)");

      foreach ($bangDiaList as $bd) {
         $maBD = $bd['maBD'];
         $soLuong = $bd['soLuong'];
         // $tenBD=$bd['tenBD'];
         $stmt = $conn->prepare("SELECT TenBD FROM bangdia WHERE MaBD = ?");
         $stmt->execute([$maBD]);
         $result = $stmt->fetch(PDO::FETCH_ASSOC);
         // Lấy đơn giá từ bảng bangdia
         $stmt = $conn->prepare("SELECT Dongia FROM bangdia WHERE MaBD = ?");
         $stmt->execute([$maBD]);
         $result = $stmt->fetch(PDO::FETCH_ASSOC);

         if (!$result) {
            throw new Exception("Không tìm thấy băng đĩa mã: $maBD.");
         }

         $donGia = $result['Dongia'];
         // Cộng dồn đơn giá cho tổng tiền
         $tongDonGia += $donGia;
         

         // Thêm vào bảng chi tiết
         $insertCT->execute([$maThueMoi, $maBD, $soLuong, $donGia]);

         // Cập nhật tình trạng băng đĩa
         $conn->prepare("UPDATE bangdia SET Tinhtrang = 'Đã thuê' WHERE MaBD = ?")->execute([$maBD]);
      }

      // Bước 3: Tính tổng thanh toán
      $tongTienThue = $tongDonGia * $soNgayThue;
      $tongThanhToan = 50000 + $tongTienThue;

      // Cập nhật tổng tiền vào bảng phieuthue
      $updateTotal = $conn->prepare("UPDATE phieuthue SET TongTien = ? WHERE MaThue = ?");
      $updateTotal->execute([$tongThanhToan, $maThueMoi]);

      $conn->commit();

      $message[] = "Đã thêm phiếu thuê thành công. Tổng thanh toán: " . number_format($tongThanhToan, 0, ',', '.') . " VNĐ.";
   } catch (Exception $e) {
      $conn->rollBack();
      $message[] = "Lỗi khi thêm phiếu thuê: " . $e->getMessage();
   }
}
}
// Xoá đơn hàng thuê
   if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];

    // Bước 1: Lấy danh sách băng đĩa của phiếu thuê cần xoá
$stmt = $conn->prepare("SELECT MaBD FROM chitietphieuthue WHERE MaThue = ?");
    $stmt->execute([$delete_id]);
    $bangDiaList = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Bước 2: Cập nhật trạng thái băng đĩa thành 'Trống'
    $updateBD = $conn->prepare("UPDATE bangdia SET Tinhtrang = 'Trống' WHERE MaBD = ?");
    foreach ($bangDiaList as $maBD) {
        $updateBD->execute([$maBD]);
    }

    // Bước 3: Xoá chi tiết phiếu thuê (do có ràng buộc khoá ngoại)
    $deleteCT = $conn->prepare("DELETE FROM chitietphieuthue WHERE MaThue = ?");
    $deleteCT->execute([$delete_id]);

    // Bước 4: Xoá phiếu thuê
    $delete_order = $conn->prepare("DELETE FROM phieuthue WHERE MaThue = ?");
    $delete_order->execute([$delete_id]);

    header('location:placed_orders.php');
    exit();
}


?>

<!DOCTYPE html>
<html lang="vi">
<head>
   <meta charset="UTF-8">
   <title>Quản lý thuê đĩa</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
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

<!-- <section class="form-container" style="margin-right:2.2rem;"> -->
   <section class="form-container">
   <form action="" method="POST" enctype="multipart/form-data">
         <h3>Phiếu thuê đĩa</h3>
         <div style="margin-top: 10px; font-size: 1.4rem; color:rgb(132, 130, 130);">
         <strong>Người nhập phiếu:</strong> <?= htmlspecialchars($tenAD) ?>
         </div>
<?php
   $get_available_bd = $conn->prepare("SELECT MaBD, TenBD FROM bangdia WHERE Tinhtrang = 'Trống'");
   $get_available_bd = $conn->prepare("SELECT MaBD, TenBD, Dongia FROM bangdia WHERE Tinhtrang = 'Trống'");

   $get_available_bd->execute();
   $availableBDs = $get_available_bd->fetchAll(PDO::FETCH_ASSOC);
   // Lấy danh sách khách hàng để cho chọn
   $get_customers = $conn->prepare("SELECT MaKH, TenKH FROM khachhang");
   $get_customers->execute();
   $customers = $get_customers->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="order_table">
      <span>Mã khách hàng:</span>
      <input list="maKH_list" name="MaKH" class="box" placeholder="" required maxlength="9">
      <datalist id="maKH_list">
         <?php foreach ($customers as $cus): ?>
            <option value="<?= htmlspecialchars($cus['MaKH']) ?>"><?= htmlspecialchars($cus['MaKH']) ?> - <?= htmlspecialchars($cus['TenKH']) ?></option>
         <?php endforeach; ?>
      </datalist>
</div>
<div class="order_table">
   <span>Số lượng thuê:</span>
   <input type="number" id="inputSoLuong" class="box" placeholder="Số lượng" min="1" max="10"  oninput="taoONhapBangDia()">
</div>

<div id="InputMaBD" style="margin-top: 10px;"></div>
   <input type="hidden" name="dsBangDia" id="dsBangDia">
   <ul id="bangDiaList" style="margin-top: 10px; padding-left: 20px; font-size: 1.5rem;"></ul>
</div>

<datalist id="maBD_list">
   <?php foreach ($availableBDs as $bd): ?>
      <option 
         value="<?= htmlspecialchars($bd['MaBD']) ?>" 
         data-price="<?= htmlspecialchars($bd['Dongia']) ?>">
         <?= htmlspecialchars($bd['MaBD']) ?> - <?= htmlspecialchars($bd['TenBD']) ?>
      </option>
   <?php endforeach; ?>
</datalist>

<div class="order_table">
        <span >Ngày thuê:</span>
         <input type="date"class="box" required name="ngayThue" onchange="renderBangDiaList()">
</div>
<div class="order_table">
        <span>Ngày trả:</span>
         <input type="date"class="box" required name="ngayTra" onchange="renderBangDiaList()">
</div>
<div class="order_table">
      <span>Tiền băng đĩa:</span>
      <input type="text" readonly id="thanhTienBox" class="box"> 
</div>
<div class="order_table">
      <span>Tiền cọc:</span>
      <input type="text" readonly id="tienCocBox" class="box" value="50,000 VNĐ">
</div>
<div class="order_table">
      <span>Tổng thanh toán:</span>
      <input type="text" readonly id="tongThanhToanBox" class="box">
</div>
      <input type="submit" value="Thêm phiếu thuê" name="add_phieuthue" class="btn">
</form>

     <!-- Hiển thị thông báo -->
   <?php if (!empty($message) && is_array($message)): ?>
   <div class="message <?php echo (strpos($message[0], 'thành công') !== false) ? 'success' : 'error'; ?>">
      <?php foreach ($message as $msg): ?>
         <span><?php echo $msg; ?></span>
         <i onclick="this.parentElement.style.display='none';">&times;</i>
      <?php endforeach; ?>
   </div>
<?php endif; ?>
</section>


</section>



<script>
let bangDiaArray = [];

function taoONhapBangDia() {
   const soLuong = parseInt(document.getElementById("inputSoLuong").value);
   const container = document.getElementById("InputMaBD");
   container.innerHTML = "";

   if (isNaN(soLuong) || soLuong <= 0) return;
   if (soLuong > 10) {
       alert("Bạn chỉ được thuê tối đa 10 đĩa.");
       soLuong = 10;
       document.getElementById("inputSoLuong").value = soLuong;
   }
   for (let i = 0; i < soLuong; i++) {
      const wrapper = document.createElement("div");
      wrapper.style.marginBottom = "10px";

      const label = document.createElement("label");
      label.textContent = `Mã băng đĩa ${i + 1}:`;
      label.style.display = "block";
      label.style.fontSize="1.6rem";
      label.style.marginRight="10rem";

      const input = document.createElement("input");
      input.type = "text";
      input.setAttribute("list", "maBD_list");
      input.className = "box";
      input.name = "maBDs[]";
      input.placeholder = `Nhập hoặc chọn mã băng đĩa ${i + 1}`;
      input.style.display = "block";
      input.style.width="70%";
      input.style.marginLeft="auto";

      const priceSpan = document.createElement("span");
      priceSpan.className = "dongia";
      priceSpan.style.display = "block";
      priceSpan.style.marginTop = "5px";
      priceSpan.style.fontSize = "1.6rem";
      priceSpan.style.color = "#333";
      priceSpan.style.marginLeft="17rem";

      // Sự kiện nhập mã đĩa
      input.addEventListener("input", function () {
         const maBD = input.value;
         const option = document.querySelector(`#maBD_list option[value="${maBD}"]`);
         if (option) {
            const donGia = option.getAttribute("data-price");
            priceSpan.textContent = `Đơn giá: ${Number(donGia).toLocaleString()} VNĐ`;
         } else {
            priceSpan.textContent = "Không tìm thấy đơn giá.";
         }

         // Gọi cập nhật danh sách
         capNhatDanhSachBangDia();
      });

      wrapper.appendChild(label);
      wrapper.appendChild(input);
      wrapper.appendChild(priceSpan);
      container.appendChild(wrapper);
   }
}

// Lấy đơn giá từ <option>
function getDonGia(maBD) {
   const option = document.querySelector(`#maBD_list option[value="${maBD}"]`);
   return option ? parseFloat(option.dataset.price) || 0 : 0;
}

// Cập nhật danh sách đĩa và tính toán
function capNhatDanhSachBangDia() {
   bangDiaArray = [];
   const inputMaBDs = document.querySelectorAll('input[name="maBDs[]"]');

   inputMaBDs.forEach(input => {
      const maBD = input.value.trim();
      if (maBD !== "") {
         const donGia = getDonGia(maBD);
         bangDiaArray.push({
            maBD,
            // tenBD,
            soLuong: 1,
            donGia
         });
      }
   });

   renderBangDiaList();
}

// Hiển thị danh sách và tính tiền
function renderBangDiaList() {
   const listElement = document.getElementById("bangDiaList");
   const hiddenInput = document.getElementById("dsBangDia");
   const thanhTienBox = document.getElementById("thanhTienBox");
   const tongThanhToanBox = document.getElementById("tongThanhToanBox");

   listElement.innerHTML = "";
   let tongDonGia = 0;

   bangDiaArray.forEach((item, index) => {
      tongDonGia += item.donGia;
      const li = document.createElement("li");
      li.innerHTML = `
         ${item.maBD} - SL: ${item.soLuong} - Đơn giá: ${item.donGia.toLocaleString()} VNĐ
         <span style="color: red; cursor: pointer; margin-left: 10px;" onclick="removeBangDia(${index})">❌</span>
      `;
      listElement.appendChild(li);
   });

   hiddenInput.value = JSON.stringify(bangDiaArray);

   // Lấy ngày thuê và trả
   const ngayThue = document.querySelector('input[name="ngayThue"]').value;
   const ngayTra = document.querySelector('input[name="ngayTra"]').value;

   let soNgayThue = 1;
   if (ngayThue && ngayTra) {
      const d1 = new Date(ngayThue);
      const d2 = new Date(ngayTra);
      const diffDays = Math.floor((d2 - d1) / (1000 * 60 * 60 * 24));
      soNgayThue = diffDays > 0 ? diffDays : 1;
   }

   // Áp dụng công thức mới
   const thanhTien = tongDonGia;
   const tongTienThue = 50000 + (thanhTien * soNgayThue);

   // Gán kết quả
   if (thanhTienBox) thanhTienBox.value = thanhTien.toLocaleString() + " VNĐ";
   if (tongThanhToanBox) tongThanhToanBox.value = tongTienThue.toLocaleString() + " VNĐ";
}

// Xoá đĩa khỏi danh sách
function removeBangDia(index) {
   bangDiaArray.splice(index, 1);
   renderBangDiaList();
}
</script>




<!-- BẢNG HIỂN THỊ -->
<section class="main-content show-products" >
      <h1 class="heading">Danh sách phiếu thuê</h1>
      <table class="product-table">
      <thead>
         <tr>
            <th class="sortable" data-index="0">Quản trị</th> <!-- thêm dòng này -->
            <th class="sortable" data-index="1">Mã thuê</th>
            <th class="sortable" data-index="2">Khách hàng (Mã KH)</th>
            <th class="sortable" data-index="3">SĐT</th>
            <th class="sortable" data-index="4">Băng đĩa (Mã BD)</th>
            <th class="sortable" data-index="5">Tổng đơn giá</th>
            <th class="sortable" data-index="6">Ngày thuê</th>
            <th class="sortable" data-index="7">Ngày trả</th>
            <th class="sortable" data-index="8">Tổng tiền</th>
            <th>Chức năng</th>
         </tr>
         <tr class="filter-row">
    <th><input type="text" placeholder="Lọc " data-index="0"></th>
    <th><input type="text" placeholder="Lọc mã thuê" data-index="1"></th>
    <th><input type="text" placeholder="Lọc tên" data-index="2"></th>
    <th></th>
    <th><input type="text" placeholder="Lọc băng đĩa" data-index="4"></th>
    <th></th>
    <th></th>
    <th></th>
    <th></th>
    <th></th>
  </tr>
      </thead>
      <tbody>
         <?php
         $stmt = $conn->prepare("
SELECT
  pt.MaThue,
  kh.TenKH,
  kh.MaKH,
  kh.SDT,
  qt.TenAD,
  GROUP_CONCAT(
    CONCAT(bd.TenBD, ' (', ct.MaBD, ')')
    SEPARATOR ',<br> '
  ) AS DanhSachBangDia,
  SUM(ct.SoLuong * ct.DonGia) AS TongDonGia,
  pt.NgayThue,
  pt.NgayTraDK,
  pt.TongTien
FROM phieuthue pt
JOIN khachhang kh ON pt.MaKH = kh.MaKH
JOIN quantri qt ON pt.MaAD = qt.MaAD
JOIN chitietphieuthue ct ON pt.MaThue = ct.MaThue
JOIN bangdia bd ON ct.MaBD = bd.MaBD
GROUP BY
  pt.MaThue,
  kh.TenKH,
  kh.MaKH,
  kh.SDT,
  qt.TenAD,
  pt.NgayThue,
  pt.NgayTraDK,
  pt.TongTien
ORDER BY pt.NgayThue DESC;
");

         $stmt->execute();
         $phieuthues = $stmt->fetchAll(PDO::FETCH_ASSOC);

         if (count($phieuthues) > 0):
            foreach ($phieuthues as $phieu):
         ?>
               <tr>
                     <td><?= htmlspecialchars($phieu['TenAD']) ?></td> 
                     <td><?= $phieu['MaThue']; ?></td>
                     <td><?= $phieu['TenKH']; ?> (<?= htmlspecialchars($phieu['MaKH']) ?>)</td>
                     <td><?= $phieu['SDT']; ?></td>
                     <td><?= $phieu['DanhSachBangDia']; ?></td>
                     <td><?= number_format($phieu['TongDonGia'], 0, ',', '.') ?> VNĐ</td>
                     <td><?php echo date('d/m/Y', strtotime($phieu['NgayThue'])); ?></td>
                     <td><?php echo date('d/m/Y', strtotime($phieu['NgayTraDK'])); ?></td>
                     <td><?= number_format($phieu['TongTien'], 0, ',', '.'). " VNĐ" ?> 
                  </td>
                     <td>
                        <!-- <a href="update_order.php?update=<?= $phieu['MaThue']; ?>" class="btn btn-update">Sửa</a> -->
                        <a href="?delete=<?= $phieu['MaThue']; ?>" onclick="return confirm('Bạn có chắc muốn xóa phiếu thuê này?');" class="btn delete-btn">Xóa</a>
                        <a href="print_invoice.php?loai=thue&id=<?= $phieu['MaThue']; ?>" target="_blank" class="btn btn-print">In hóa đơn</a>
                     </td>
               </tr>
         <?php
            endforeach;
         else:
            echo '<tr><td colspan="12">Không có phiếu thuê nào.</td></tr>';
         endif;
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
<script src="../js/admin_script.js"></script>
</body>
</html>


