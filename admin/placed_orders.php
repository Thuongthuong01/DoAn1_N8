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


   try {
        $conn->beginTransaction();

        // Tính số ngày thuê
        $ngayThueDate = new DateTime($ngayThue);
        $hanTraDate = new DateTime($hanTra);
        $soNgayThue = $ngayThueDate->diff($hanTraDate)->days;
        if ($soNgayThue == 0) $soNgayThue = 1;

        $tongTienTatCa = 0;
        $maAdmin = $_SESSION["user_id"];
$insertPhieu = $conn->prepare("INSERT INTO phieuthue (MaKH, NgayThue, NgayTraDK, TongTien, MaAD) VALUES (?, ?, ?, 0, ?)");
$insertPhieu->execute([$maKH, $ngayThue, $hanTra, $maAdmin]);

        $maThueMoi = $conn->lastInsertId();

        $insertCT = $conn->prepare("INSERT INTO chitietphieuthue (MaThue, MaBD, SoLuong, DonGia, ThanhTien) VALUES (?, ?, ?, ?, ?)");
      foreach ($bangDiaList as $bd) {
         $maBD = $bd['maBD'];
         $soLuong = $bd['soLuong'];

         $stmt = $conn->prepare("SELECT Dongia FROM bangdia WHERE MaBD = ?");
         $stmt->execute([$maBD]);
         $result = $stmt->fetch(PDO::FETCH_ASSOC);

         if (!$result) {
                throw new Exception("Không tìm thấy băng đĩa mã: $maBD.");
            }

            $donGia = $result['Dongia'];
            $thanhTien = $soNgayThue * $donGia * $soLuong;
            $tongTienTatCa += $thanhTien;

            $insertCT->execute([$maThueMoi, $maBD, $soLuong, $donGia, $thanhTien]);

            // Cập nhật tình trạng băng đĩa
            $conn->prepare("UPDATE bangdia SET Tinhtrang = 'Đã thuê' WHERE MaBD = ?")->execute([$maBD]);
        }

        // Cập nhật tổng tiền cho phiếu thuê
        $tongThanhToan = $tongTienTatCa + 50000;
        $updateTotal = $conn->prepare("UPDATE phieuthue SET TongTien = ? WHERE MaThue = ?");
        $updateTotal->execute([$tongThanhToan, $maThueMoi]);

        $conn->commit();

        $message[] = "Đã thêm phiếu thuê thành công. Tổng thanh toán: " . number_format($tongThanhToan, 0, ',', '.') . " VNĐ.";
    } catch (Exception $e) {
        $conn->rollBack();
        $message[] = "Lỗi khi thêm phiếu thuê: " . $e->getMessage();
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

<?php include '../components/admin_header.php' ?>

<section class="form-container" style="margin-right:2.2rem;">
   
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
<div  style="display: flex; align-items: center; gap: 10px; margin-bottom: 1px;">
        <span style="min-width: 160px;font-size:1.8rem; text-align: left;">Mã khách hàng:</span>
<input list="maKH_list" name="MaKH" class="box" placeholder="" required maxlength="9">
<datalist id="maKH_list">
   <?php foreach ($customers as $cus): ?>
      <option value="<?= htmlspecialchars($cus['MaKH']) ?>"><?= htmlspecialchars($cus['MaKH']) ?> - <?= htmlspecialchars($cus['TenKH']) ?></option>
   <?php endforeach; ?>
</datalist>
   </div>
<div style="display: flex; align-items: center; gap: 10px; margin-bottom: 1px;">
   <label style="min-width: 160px;font-size:1.8rem;text-align: left;">Mã băng đĩa:</label>
   
      <input list="maBD_list" id="inputMaBD" class="box" placeholder="Nhập hoặc chọn mã băng đĩa">
      <input type="number" id="inputSoLuong" class="box" placeholder="Số lượng" min="1" style="width: 120px;">
      
      <button type="button" onclick="addBangDia()"  style="display: block;font-size:15px;color:blue; font-weight:bold">Thêm</button>
   </div>
   <div>
   <small style=" font-size: 1.3rem; color: #666;text-align: left;">* Có thể thêm nhiều mã băng đĩa với số lượng khác nhau</small>
      </div>
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




   <div  style="display: flex; align-items: center; gap: 10px; margin-bottom: 1px;">
        <span style="min-width: 160px;font-size:1.8rem;text-align: left;">Ngày thuê:</span>
         <input type="date"class="box" required name="ngayThue" onchange="renderBangDiaList()">
   </div>
   <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 1px;">
        <span style="min-width: 160px;font-size:1.8rem;text-align: left;">Ngày trả:</span>
         <input type="date"class="box" required name="ngayTra" onchange="renderBangDiaList()">
   </div>
   <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 1px;">
   <span style="min-width: 160px; font-size:1.8rem; text-align: left;">Tiền băng đĩa:</span>
   <input type="text" readonly id="thanhTienBox" class="box"> 
</div>
<div style="display: flex; align-items: center; gap: 10px; margin-bottom: 1px;">
   <span style="min-width: 160px; font-size:1.8rem; text-align: left;">Tiền cọc:</span>
   <input type="text" readonly id="tienCocBox" class="box" value="50,000 VNĐ">
</div>

<div style="display: flex; align-items: center; gap: 10px; margin-bottom: 1px;">
   <span style="min-width: 160px; font-size:1.8rem; text-align: left;">Tổng thanh toán:</span>
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
   const datalistOptions = document.querySelectorAll("#maBD_list option");
   const listElement = document.getElementById("bangDiaList");
   const hiddenInput = document.getElementById("dsBangDia");
   let bangDiaArray = [];
document.getElementById("inputMaBD").addEventListener("input", updateThanhTien);
document.getElementById("inputSoLuong").addEventListener("input", updateThanhTien);

   function getDonGia(maBD) {
      for (let option of datalistOptions) {
         if (option.value === maBD) {
            return parseFloat(option.dataset.price) || 0;
         }
      }
      return 0;
   }

   function addBangDia() {
      const maBD = document.getElementById("inputMaBD").value.trim();
      const soLuong = parseInt(document.getElementById("inputSoLuong").value.trim());

      if (!maBD || isNaN(soLuong) || soLuong < 1) {
         alert("Vui lòng nhập mã băng đĩa và số lượng hợp lệ.");
         return;
      }

      if (bangDiaArray.find(item => item.maBD === maBD)) {
         alert("Mã băng đĩa đã được thêm. Hãy xoá nếu muốn sửa.");
         return;
      }

      const donGia = getDonGia(maBD);

      bangDiaArray.push({ maBD, soLuong, donGia });
      renderBangDiaList();

      document.getElementById("inputMaBD").value = '';
      document.getElementById("inputSoLuong").value = '';
   }

   function removeBangDia(index) {
      bangDiaArray.splice(index, 1);
      renderBangDiaList();
   }

   function renderBangDiaList() {
   listElement.innerHTML = "";
   let tongTienTatCa = 0;

   bangDiaArray.forEach((item, index) => {
      const thanhTien = item.soLuong * item.donGia;
      tongTienTatCa += thanhTien;

      const li = document.createElement("li");
      li.innerHTML = `
         ${item.maBD} - SL: ${item.soLuong} - Đơn giá: ${item.donGia.toLocaleString()} VNĐ 
         - Thành tiền: ${thanhTien.toLocaleString()} VNĐ
         <span style="color: red; cursor: pointer; margin-left: 10px;" onclick="removeBangDia(${index})">❌</span>
      `;
      listElement.appendChild(li);
   });

   hiddenInput.value = JSON.stringify(bangDiaArray);

   const thanhTienBox = document.getElementById("thanhTienBox");
const tongThanhToanBox = document.getElementById("tongThanhToanBox");
const tienCoc = 50000;

// Lấy ngày thuê và ngày trả
const ngayThue = document.querySelector('input[name="ngayThue"]').value;
const ngayTra = document.querySelector('input[name="ngayTra"]').value;

let soNgayThue = 1;
if (ngayThue && ngayTra) {
   const d1 = new Date(ngayThue);
   const d2 = new Date(ngayTra);
   const diffTime = d2.getTime() - d1.getTime();
   const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));
   soNgayThue = diffDays > 0 ? diffDays : 1;
}

// Tính tiền thuê theo ngày
const tongTienThueTheoNgay = tongTienTatCa * soNgayThue;

thanhTienBox.value = tongTienThueTheoNgay.toLocaleString() + " VNĐ";
tongThanhToanBox.value = (tongTienThueTheoNgay + tienCoc).toLocaleString() + " VNĐ";

}


   function updateThanhTien() {
   const maBD = document.getElementById("inputMaBD").value.trim();
   const soLuong = parseInt(document.getElementById("inputSoLuong").value.trim());
   const donGia = getDonGia(maBD);

   const thanhTienBox = document.getElementById("thanhTienBox");

   if (!maBD || isNaN(soLuong) || soLuong < 1 || isNaN(donGia)) {
      thanhTienBox.value = '';
      return;
   }

   const thanhTien = donGia * soLuong;
   thanhTienBox.value = thanhTien;
}

</script>




<section class="main-content show-products" >
      <h1 class="heading">Danh sách phiếu thuê</h1>
      <table class="product-table">
      <thead>
         <tr>
            <th>Mã Thuê</th>
            <th>Khách Hàng (Mã KH)</th>
            <th>SĐT</th>
            <th>Băng Đĩa (Số Lượng)</th>
            <th>Tổng Đơn giá</th>
            <th>Ngày Thuê</th>
            <th>Hạn Trả</th>
            <th>Tổng Tiền</th>
            <th>Chức Năng</th>
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
  GROUP_CONCAT(CONCAT(bd.TenBD, ' (', ct.SoLuong, ')') SEPARATOR ',<br> ') AS DanhSachBangDia,
  SUM(ct.SoLuong * ct.DonGia) AS TongDonGia,
  pt.NgayThue,
  pt.NgayTraDK,
  pt.TongTien
FROM phieuthue pt
JOIN khachhang kh ON pt.MaKH = kh.MaKH
JOIN chitietphieuthue ct ON pt.MaThue = ct.MaThue
JOIN bangdia bd ON ct.MaBD = bd.MaBD
GROUP BY pt.MaThue, kh.TenKH, kh.MaKH, kh.SDT, pt.NgayThue, pt.NgayTraDK, pt.TongTien
ORDER BY pt.NgayThue DESC
");

         $stmt->execute();
         $phieuthues = $stmt->fetchAll(PDO::FETCH_ASSOC);

         if (count($phieuthues) > 0):
            foreach ($phieuthues as $phieu):
         ?>
               <tr>
                  <td><?= $phieu['MaThue']; ?></td>
                  <td><?= $phieu['TenKH']; ?> (<?= htmlspecialchars($phieu['MaKH']) ?>)</td>
                  <td><?= $phieu['SDT']; ?></td>
                  <td><?= $phieu['DanhSachBangDia']; ?></td>
                  <td><?= number_format($phieu['TongDonGia'], 0, ',', '.') ?> VNĐ</td>
                  <td><?= $phieu['NgayThue']; ?></td>
                  <td><?= $phieu['NgayTraDK']; ?></td>
                  <td><?= number_format($phieu['TongTien'], 0, ',', '.') ?> VNĐ</td>
                  <td>
                     <a href="update_order.php?update=<?= $phieu['MaThue']; ?>" class="btn btn-update">Sửa</a>
                     <a href="?delete=<?= $phieu['MaThue']; ?>" onclick="return confirm('Bạn có chắc muốn xóa phiếu thuê này?');" class="btn btn-delete">Xóa</a>
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
<script src="../js/admin_script.js"></script>
</body>
</html>
