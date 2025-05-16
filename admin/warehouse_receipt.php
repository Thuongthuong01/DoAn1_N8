<?php

include '../components/connect.php';

session_start();

if (!isset($_SESSION["user_id"])) {
   header("Location:admin_login");
   exit();
}
if (isset($_POST['submit'])) {
   $maPhieu = $_POST['MaPhieu'];
   $maNCC = $_POST['MaNCC'];
   $ngayNhap = $_POST['NgayNhap'];
   $soLuong = $_POST['SoLuong'];
   $tongTien = $_POST['TongTien'];

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
   for ($i = 1; $i <= $soLuong; $i++) {
      $maBD = $_POST["MaBD_$i"];
      $stmt = $conn->prepare("SELECT * FROM chitietphieunhap WHERE MaBD = ?");
      $stmt->execute([$maBD]);
      if ($stmt->rowCount() > 0) {
         $errors[] = "❌ Mã băng đĩa '$maBD' đã tồn tại trong hệ thống!";
      }
   }

   // Nếu không có lỗi thì mới insert
   if (empty($errors)) {
      $insert = $conn->prepare("INSERT INTO phieunhap (MaPhieu, MaNCC, NgayNhap, SoLuong, TongTien) VALUES (?, ?, ?, ?, ?)");
      $insert->execute([$maPhieu, $maNCC, $ngayNhap, $soLuong, $tongTien]);

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

// if (isset($_POST['submit'])) {
//    $maPhieu = $_POST['MaPhieu'];
//    $maNCC = $_POST['MaNCC'];
//    $ngayNhap = $_POST['NgayNhap'];
//    $soLuong = $_POST['SoLuong'];
//    $tongTien = $_POST['TongTien'];

//    // Kiểm tra MaNCC có tồn tại không
//    $stmt = $conn->prepare("SELECT * FROM nhacc WHERE MaNCC = ?");
//    $stmt->execute([$maNCC]);

//    if ($stmt->rowCount() == 0) {
//       $message[] = "❌ Mã nhà cung cấp không tồn tại!";
//    } else { 
//         $check_stmt = $conn->prepare("SELECT MaPhieu FROM phieunhap WHERE MaPhieu = ?");
//         $check_stmt->execute([$maPhieu]);
//         if ($check_stmt->rowCount() > 0) {
//             $message[] = "❌ Mã phiếu đã tồn tại. Vui lòng nhập mã khác!";
//         } else {
//         // Thêm phiếu nhập
//         $insert = $conn->prepare("INSERT INTO phieunhap (MaPhieu, MaNCC, NgayNhap, SoLuong, TongTien) VALUES (?, ?, ?, ?, ?)");
//         $insert->execute([$maPhieu, $maNCC, $ngayNhap, $soLuong, $tongTien]);

//         // Lặp để thêm các chi tiết sản phẩm
//         // Kiểm tra trùng mã băng đĩa trong POST
// $maBDList = [];
// $trungMaBD = false;

// for ($i = 1; $i <= $soLuong; $i++) {
//     $maBDKey = "MaBD_$i";
//     $giaGocKey = "GiaGoc_$i";

//     if (isset($_POST[$maBDKey]) && isset($_POST[$giaGocKey])) {
//         $maBD = $_POST[$maBDKey];
//         $giaGoc = $_POST[$giaGocKey];

//         // Kiểm tra xem mã băng đĩa đã tồn tại trong chitietphieunhap chưa
//         $check = $conn->prepare("SELECT * FROM chitietphieunhap WHERE MaBD = ?");
//         $check->execute([$maBD]);

//         if ($check->rowCount() > 0) {
//             $message[] = "❌ Mã băng đĩa đã tồn tại trong phiếu nhập trước: $maBD";
//             $trungMaBD = true;
//             break;
//         }

//         // Nếu không trùng, thêm vào danh sách để xử lý sau
//         $maBDList[] = ['MaBD' => $maBD, 'GiaGoc' => $giaGoc];
//     }
// }

// if (!$trungMaBD) {
//     // Không có trùng, tiếp tục thêm chi tiết phiếu nhập
//      foreach ($maBDList as $item) {
//         $insert_ct = $conn->prepare("INSERT INTO chitietphieunhap (MaPhieu, MaBD, GiaGoc) VALUES (?, ?, ?)");
//         $insert_ct->execute([$maPhieu, $item['MaBD'], $item['GiaGoc']]);
//     }

//     $message[] = "✅ Đã thêm phiếu nhập thành công!";
// }}} }


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng kí khách hàng mới</title>
     <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

<!-- custom css file link  -->
<link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>
<?php include '../components/admin_header.php' ?>

<section class="form-container">
   <form method="post">
      <h3>Phiếu nhập băng đĩa</h3>

      <div class="box">
        <span>Mã Phiếu :</span>
         <!-- <input type="text" name="MaPhieu" required placeholder="Nhập mã phiếu"> -->
        <input type="text" name="MaPhieu" required placeholder="Nhập mã phiếu" 
        value="<?= htmlspecialchars($_POST['MaPhieu'] ?? '') ?>">

        </div>

      <div class="box">
         <span>Mã NCC :</span>
         <!-- <input type="text" name="MaNCC" required placeholder="Nhập mã nhà cung cấp"> -->
        <input type="text" name="MaNCC" required placeholder="Nhập mã nhà cung cấp" 
       value="<?= htmlspecialchars($_POST['MaNCC'] ?? '') ?>">
        </div>

      <div class="box">
         <span>Ngày nhập :</span>
         <!-- <input type="date" name="NgayNhap" required> -->
        <input type="date" name="NgayNhap" required 
       value="<?= htmlspecialchars($_POST['NgayNhap'] ?? '') ?>">
      </div>

      <div class="box">
         <span>Số lượng :</span>
         <input type="number" name="SoLuong" id="SoLuong" min="1" max="30" required placeholder="Nhập số lượng (1-30)" oninput="generateFields()">
        <!-- <input type="number" name="SoLuong" id="SoLuong" min="1" max="30" required 
       placeholder="Nhập số lượng (1-30)" 
       value="<?= htmlspecialchars($_POST['SoLuong'] ?? '') ?>" 
       oninput="generateFields()"> -->
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

      <div class="box">
         <span>Tổng tiền :</span>
         <input type="number" name="TongTien" required placeholder="Tổng đơn giá ">
        <!-- <input type="number" name="TongTien" required 
        placeholder="Tổng đơn giá " 
        value="<?= htmlspecialchars($_POST['TongTien'] ?? '') ?>"> -->
        </div>

      <input type="submit" name="submit" value="Thêm" class="btn">
      <a href="warehouse.php" class="option-btn">Quay lại</a>
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
         giaGoc.placeholder = `Đơn giá ${i}`;
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

</body>
</html>