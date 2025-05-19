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
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['ajax'])) {
   if (!empty($_POST['MaThue'])) {
     $stmt = $conn->prepare("
   SELECT pt.MaKH, pt.NgayTraDK, SUM(bd.DonGia) AS TongDonGia
   FROM phieuthue pt
   JOIN chitietphieuthue ct ON pt.MaThue = ct.MaThue
   JOIN bangdia bd ON ct.MaBD = bd.MaBD
   WHERE pt.MaThue = ?
   GROUP BY pt.MaThue
");

$stmt->execute([$_POST['MaThue']]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

      header('Content-Type: application/json');
      echo json_encode($row ?: []);
      exit;
   }

   if (!empty($_POST['MaKH'])) {
      $stmt = $conn->prepare("SELECT MaThue FROM phieuthue WHERE MaKH = ? ORDER BY MaThue DESC LIMIT 1");
      $stmt->execute([$_POST['MaKH']]);
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      echo $row ? $row['MaThue'] : '';
      exit;
   }
}




$message = [];
// mới: chỉ lấy MaThue chưa xuất phiếu trả
$stmtThue = $conn->query("
   SELECT MaThue 
   FROM phieuthue 
   WHERE MaThue NOT IN (SELECT MaThue FROM phieutra)
");
$dsMaThue = $stmtThue->fetchAll(PDO::FETCH_COLUMN);


// Lấy danh sách mã khách
$stmtKH = $conn->query("SELECT MaKH FROM khachhang");
$dsMaKH = $stmtKH->fetchAll(PDO::FETCH_COLUMN);
if (isset($_POST['add_phieutra'])) {
   $MaThue = $_POST['MaThue'];
   $MaKH = $_POST['MaKH'];
   $NgayTraTT = $_POST['NgayTraTT'];
   $ChatLuong = $_POST['ChatLuong'];

   $check = $conn->prepare("SELECT * FROM phieutra WHERE MaThue = ?");
   $check->execute([$MaThue]);

   if ($check->rowCount() > 0) {
      $message[] = "❌ Mã thuê này đã có phiếu trả!";
   } else {
      // Lấy ngày trả dự kiến và đơn giá
      $stmt = $conn->prepare("
   SELECT pt.NgayTraDK, SUM(bd.DonGia) AS TongDonGia
   FROM phieuthue pt
   JOIN chitietphieuthue ct ON pt.MaThue = ct.MaThue
   JOIN bangdia bd ON ct.MaBD = bd.MaBD
   WHERE pt.MaThue = ?
   GROUP BY pt.MaThue
");
$stmt->execute([$MaThue]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);


      if ($row) {
    $NgayTraDK = $row['NgayTraDK'];
    $DonGia = $row['TongDonGia']; // dùng tổng tiền


         $date1 = new DateTime($NgayTraDK);
         $date2 = new DateTime($NgayTraTT);
         $interval = $date2->diff($date1);
         $TraMuon = ($date2 > $date1) ? $interval->days : 0;

         // Tính phí trễ
         $PhatTre = $TraMuon * (0.05 * $DonGia);

         // Phí hư hỏng
         switch ($ChatLuong) {
            case 'Tốt':
               $PhatHu = 0;
               break;
            case 'Trầy xước':
               $PhatHu = 0.3 * $DonGia;
               break;
            case 'Hỏng nặng':
               $PhatHu = 0.5 * $DonGia;
               break;
            case 'Mất':
               $PhatHu = 1.0 * $DonGia;
               break;
            default:
               $PhatHu = 0;
         }
$TienCoc = 50000;
         $TienPhat = $PhatTre + $PhatHu;
$TienTra = $TienCoc - $TienPhat;

if ($TienTra < 0) {
    $TienTra = 0; // hoặc xử lý theo quy định (không âm)
}
$update_sql = "UPDATE phieutra SET TienTra = ? WHERE MaTra = ?";
$stmt = $conn->prepare($update_sql);
$stmt->execute([$TienTra, $MaThue]); // ✅ đúng cú pháp PDO
// Lấy các mã đĩa từ phiếu thuê để cập nhật
$sql_dia = $conn->prepare("SELECT MaBD FROM chitietphieuthue WHERE MaThue = ?");
$sql_dia->execute([$MaThue]);
$ds_dia = $sql_dia->fetchAll(PDO::FETCH_ASSOC);

// Với mỗi đĩa được trả, cập nhật chất lượng và tình trạng
foreach ($ds_dia as $row) {
    $maDia = $row['MaBD'];
    
    // Nếu chất lượng là 'Tốt' thì tình trạng là rỗng, ngược lại là 'Đang bảo trì'
    $tinhTrang = ($ChatLuong == 'Tốt') ? 'Trống' : 'Đang bảo trì';

    $capnhat = $conn->prepare("UPDATE bangdia SET ChatLuong = ?, Tinhtrang = ? WHERE MaBD = ?");
    $capnhat->execute([$ChatLuong, $tinhTrang, $maDia]);
}

$maAD = $_SESSION['user_id'];
$insert = $conn->prepare("INSERT INTO phieutra(MaThue, MaKH, NgayTraTT, ChatLuong, TraMuon, TienPhat, TienTra,MaAD) VALUES (?,?, ?, ?, ?, ?, ?, ?)");
$insert->execute([$MaThue, $MaKH, $NgayTraTT, $ChatLuong, $TraMuon, $TienPhat, $TienTra, $maAD]);

         $message[] = "Đã thêm phiếu trả thành công!";
      } else {
         $message[] = "❌ Không tìm thấy thông tin thuê phù hợp để tính phí!";
      }
   }
}



// xoá 
if (isset($_GET['delete'])) {
   $delete_id = $_GET['delete'];

   $delete_phieutra = $conn->prepare("DELETE FROM phieutra WHERE MaTra = ?");
   $delete_phieutra->execute([$delete_id]);

   $message[] = "Đã xóa phiếu trả thành công!";
}
?>
<!-- thông báo -->
<!-- <?php if (!empty($message) && is_array($message)): ?>
   <?php foreach ($message as $msg): ?>
      <?php
         $isSuccess = strpos($msg, 'Đã thêm') !== false || strpos($msg, 'Đã xóa') !== false;
      ?>
      <div class="message" style="background-color: <?= $isSuccess ? '#d4edda' : '#f8d7da'; ?>; color: <?= $isSuccess ? '#155724' : '#721c24'; ?>; border: 1px solid <?= $isSuccess ? '#c3e6cb' : '#f5c6cb'; ?>; padding: 10px; margin: 10px 0; border-radius: 5px;">
         <span><?= $msg; ?></span>
         <i onclick="this.parentElement.style.display='none';" style="cursor:pointer; float:right;">&times;</i>
      </div>
   <?php endforeach; ?>
<?php endif; ?> -->



<!DOCTYPE html>
<html lang="vi">
<head>
   <meta charset="UTF-8">
   <title>Quản lý trả đĩa</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>

<?php include '../components/admin_header.php' ?>
<section class="main-content placed-orders-admin">
   <section class="form-container" style="margin-right:2.2rem;">
        <form action="" method="POST" enctype="multipart/form-data">
   <h3>Phiếu trả đĩa</h3>
   <div style="margin-top: 10px; font-size: 1.4rem; color:rgb(132, 130, 130);">
    <strong>Người nhập phiếu:</strong> <?= htmlspecialchars($tenAD) ?>
</div>
   <div  style="display: flex; align-items: center; gap: 10px; margin-bottom: 1px;">
        <span style="min-width: 160px;font-size:1.8rem; text-align: left;">Mã phiếu thuê:</span>
   <input type="text" required placeholder="" name="MaThue" id="MaThue"list="dsMaThue"maxlength="9" class="box">
   <datalist id="dsMaThue">
   <?php foreach ($dsMaThue as $maThue): ?>
       <option value="<?= htmlspecialchars($maThue) ?>"></option>
   <?php endforeach; ?>
</datalist>
   </div>
   <div  style="display: flex; align-items: center; gap: 10px; margin-bottom: 1px;">
        <span style="min-width: 160px;font-size:1.8rem; text-align: left;">Mã khách hàng:</span>
   <input type="text" required placeholder="" name="MaKH" id="MaKH"list="dsMaKH" maxlength="9" class="box">
   <datalist id="dsMaKH">
   <?php foreach ($dsMaKH as $maKH): ?>
       <option value="<?= htmlspecialchars($maKH) ?>"></option>
   <?php endforeach; ?>
</datalist>
   </div>
   <div  style="display: flex; align-items: center; gap: 10px; margin-bottom: 1px;">
        <span style="min-width: 160px;font-size:1.8rem; text-align: left;">Ngày trả:</span>
   <input type="date" required name="NgayTraTT" class="box">
   </div>
   <div  style="display: flex; align-items: center; gap: 10px; margin-bottom: 1px;">
        <span style="min-width: 160px;font-size:1.8rem; text-align: left;">Chất lượng đĩa:</span>
   <select name="ChatLuong" class="box" required>
      <option value="">-- Chọn chất lượng --</option>
      <option value="Tốt">Tốt</option>
      <option value="Trầy xước">Trầy xước</option>
      <option value="Hỏng nặng">Hỏng nặng</option>
      <option value="Mất">Mất</option>
   </select>
   </div>
   <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 1px;">
   <span style="min-width: 160px;font-size:1.8rem; text-align: left;">Phạt trả muộn:</span>
   <input type="text" id="PhatTre" readonly class="box" value="0 VNĐ">
</div>
<div style="display: flex; align-items: center; gap: 10px; margin-bottom: 1px;">
   <span style="min-width: 160px;font-size:1.8rem; text-align: left;">Phạt chất lượng:</span>
   <input type="text" id="PhatChatLuong" readonly class="box" value="0 VNĐ">
</div>
<div style="display: flex; align-items: center; gap: 10px; margin-bottom: 1px;">
   <span style="min-width: 160px;font-size:1.8rem; text-align: left; font-weight: bold;">Tổng tiền phạt:</span>
   <input type="text" id="TongTienPhat" readonly class="box" value="0 VNĐ" style="font-weight: bold; color: red;">
</div>
<input type="hidden" id="tiencoc" value="50000">

<div style="display: flex; align-items: center; gap: 10px; margin-bottom: 1px;">
   <span style="min-width: 160px;font-size:1.8rem; text-align: left; font-weight: bold;">Tiền trả đĩa:</span>
   <input type="text" id="tientra" name="TienTra" readonly class="box" value="0 VNĐ" style="font-weight: bold; color: blue;">
</div>


   <input type="submit" value="Thêm phiếu trả" name="add_phieutra" class="btn">
</form>
<!-- Thông báo -->
<?php if (!empty($message) && is_array($message)): ?>
   <?php foreach ($message as $msg): ?>
      <div class="message" style="background-color: <?= (strpos($msg, 'Đã thêm') !== false) ? '#d4edda' : '#f8d7da'; ?>; color: <?= (strpos($msg, 'Đã thêm') !== false) ? '#155724' : '#721c24'; ?>; border: 1px solid <?= (strpos($msg, 'Đã thêm') !== false) ? '#c3e6cb' : '#f5c6cb'; ?>; padding: 10px; margin: 10px 0; border-radius: 5px;">
         <span><?= $msg; ?></span>
         <i onclick="this.parentElement.style.display='none';" style="cursor:pointer; float:right;">&times;</i>
      </div>
   <?php endforeach; ?>
<?php endif; ?>
    </section>
</section>

<section class="main-content show-products">
   <h1 class="heading">Danh sách phiếu trả</h1>
   <table class="product-table">
      <thead>
         <tr>
            <th>Mã Trả</th>
            <th>Mã Thuê</th>
            <th>Khách Hàng (Mã KH)</th>
            <th>SĐT</th>
            <th>Ngày Trả Thực Tế</th>
            <th>Chất Lượng</th>
            <th>Trả Muộn (ngày)</th>
            <th>Tiền phạt</th>
            <th>Tiền trả</th>
            <th>Chức năng</th>

         </tr>
      </thead>
      <tbody>
         <?php
         $stmt = $conn->prepare("
            SELECT 
               pt.MaTra, pt.MaThue, pt.MaKH, pt.NgayTraTT, pt.ChatLuong, pt.TraMuon, pt.TienPhat,pt.TienTra,
               kh.TenKH,kh.MaKH, kh.SDT
            FROM phieutra pt
            JOIN khachhang kh ON pt.MaKH = kh.MaKH
            ORDER BY pt.NgayTraTT DESC
         ");

         $stmt->execute();
         $phieutras = $stmt->fetchAll(PDO::FETCH_ASSOC);

         if (count($phieutras) > 0):
            foreach ($phieutras as $phieu):
         ?>
               <tr>
                  <td><?= $phieu['MaTra']; ?></td>
                  <td><?= $phieu['MaThue']; ?></td>
                  <td><?= $phieu['TenKH']; ?>(<?= htmlspecialchars($phieu['MaKH']) ?>)</td>
                  <td><?= $phieu['SDT']; ?></td>
                  <td><?= $phieu['NgayTraTT']; ?></td>
                  <td><?= $phieu['ChatLuong']; ?></td>
                  <td><?= $phieu['TraMuon']; ?> ngày</td>
                  <td><?= number_format($phieu['TienPhat'], 0, ',', '.'); ?> VNĐ</td>
                  <td><?= number_format($phieu['TienTra'] ?? 0, 0, ',', '.'); ?> VNĐ</td>
                  <td>
                     <a href="update_return.php?update=<?= $phieu['MaTra']; ?>" class="btn btn-update">Sửa</a>
                     <a href="?delete=<?= $phieu['MaTra']; ?>" onclick="return confirm('Bạn có chắc muốn xóa phiếu trả này?');" class="btn btn-delete">Xóa</a>
                  </td>
               </tr>
         <?php
            endforeach;
         else:
            echo '<tr><td colspan="10">Không có phiếu trả nào.</td></tr>';
         endif;
         ?>
      </tbody>
   </table>
</section>
<script>
let donGia = 0;
let ngayTraDK = null;

document.getElementById("MaThue").addEventListener("blur", function() {
   const MaThue = this.value;
   if (!MaThue) {
      resetFines();
      return;
   }

   fetch("", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: "ajax=1&MaThue=" + encodeURIComponent(MaThue)
   })
   .then(response => response.json())
   .then(data => {
      if (data && data.MaKH) {
         document.getElementById("MaKH").value = data.MaKH;
         donGia = parseFloat(data.TongDonGia) || 0;

         ngayTraDK = data.NgayTraDK || null;

         if (!donGia || !ngayTraDK) {
            resetFines();
         } else {
            calculateFines();
         }
      } else {
         donGia = 0;
         ngayTraDK = null;
         document.getElementById("MaKH").value = "";
         resetFines();
      }
   })
   .catch(error => {
      console.error("Lỗi AJAX:", error);
      resetFines();
   });
});

document.getElementById("MaKH").addEventListener("blur", function() {
   const MaKH = this.value.trim();
   if (!MaKH) return;

   fetch("", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: "ajax=1&MaKH=" + encodeURIComponent(MaKH)
   })
   .then(response => response.text())
   .then(data => {
      if (data) {
         document.getElementById("MaThue").value = data;
         // Khi set MaThue, bạn có thể kích hoạt sự kiện blur để lấy lại donGia, ngayTraDK
         document.getElementById("MaThue").dispatchEvent(new Event('blur'));
      }
   })
   .catch(error => console.error("Lỗi khi lấy mã thuê từ mã KH:", error));
});

// Khi ngày hoặc chất lượng thay đổi => tự động tính phạt
document.querySelector('input[name="NgayTraTT"]').addEventListener("change", calculateFines);
document.querySelector('select[name="ChatLuong"]').addEventListener("change", calculateFines);

function resetFines() {
   document.getElementById("PhatTre").value = "0 VNĐ";
   document.getElementById("PhatChatLuong").value = "0 VNĐ";
   document.getElementById("TongTienPhat").value = "0 VNĐ";
   document.getElementById("tientra").value = "0 VNĐ";
}

function formatCurrency(num) {
   return num.toLocaleString('vi-VN', {style: 'currency', currency: 'VND'});
}

function calculateFines() {
   const ngayTraTT = document.querySelector('input[name="NgayTraTT"]').value;
   const chatLuong = document.querySelector('select[name="ChatLuong"]').value;
   const tienCoc = parseFloat(document.getElementById("tiencoc").value) || 0;

   if (!ngayTraTT || !ngayTraDK || !donGia) {
      resetFines();
      return;
   }

   const date1 = new Date(ngayTraDK);
   const date2 = new Date(ngayTraTT);
   const timeDiff = date2 - date1;
   const daysLate = timeDiff > 0 ? Math.floor(timeDiff / (1000 * 60 * 60 * 24)) : 0;

   const phatTre = daysLate * 0.05 * donGia;
   let phatHu = 0;

   switch (chatLuong) {
      case 'Trầy xước':
         phatHu = 0.3 * donGia;
         break;
      case 'Hỏng nặng':
         phatHu = 0.5 * donGia;
         break;
      case 'Mất':
         phatHu = 1.0 * donGia;
         break;
      default:
         phatHu = 0;
   }

   const tongPhat = phatTre + phatHu;
   let tienTra = tienCoc - tongPhat;
   if (tienTra < 0) tienTra = 0;

   document.getElementById("PhatTre").value = phatTre.toLocaleString('vi-VN') + " VNĐ";
   document.getElementById("PhatChatLuong").value = phatHu.toLocaleString('vi-VN') + " VNĐ";
   document.getElementById("TongTienPhat").value = tongPhat.toLocaleString('vi-VN') + " VNĐ";
   document.getElementById("tientra").value = tienTra.toLocaleString('vi-VN') + " VNĐ";
}


</script>

<script src="../js/admin_script.js"></script>
</body>
</html>