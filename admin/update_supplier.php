<?php
// include '../components/connect.php';
// session_start();

// if (!isset($_SESSION["user_id"])) {
//    header("Location:admin_login");
//    exit();
// }

// if (!isset($_GET['update'])) {
//     header("Location:supplier.php"); // quay về nếu không có mã
//     exit();
// }

// $MaNCC = $_GET['update'];

// // Lấy dữ liệu cũ
// $stmt = $conn->prepare("SELECT * FROM nhacc WHERE MaNCC = ?");
// $stmt->execute([$MaNCC]);
// $supplier = $stmt->fetch(PDO::FETCH_ASSOC);

// if (!$supplier) {
//     echo "❌ Không tìm thấy nhà cung cấp!";
//     exit();
// }

// // Xử lý cập nhật
// if (isset($_POST['update_supplier'])) {
//     $TenNCC = $_POST['TenNCC'];
//     $SDT = $_POST['SDT'];
//     $DiaChi = $_POST['DiaChi'];

//     $update_stmt = $conn->prepare("UPDATE nhacc SET  SDT = ?, DiaChi = ? WHERE MaNCC = ?");
//     $success = $update_stmt->execute([ $SDT, $DiaChi, $MaNCC]);

//     if ($success) {
//         $message[] = "✅ Cập nhật thành công!";
//         // Cập nhật lại dữ liệu hiển thị
//         $supplier = ['MaNCC' => $MaNCC, 'TenNCC' => $TenNCC, 'SDT' => $SDT, 'DiaChi' => $DiaChi];
//     } else {
//         $message[] = "❌ Cập nhật thất bại!";
//     }
// }

include '../components/connect.php';
session_start();

if (!isset($_SESSION["user_id"])) {
   header("Location:admin_login");
   exit();
}

if (!isset($_GET['update'])) {
    header("Location:supplier.php"); // quay về nếu không có mã
    exit();
}

$MaNCC = $_GET['update'];

// Lấy dữ liệu cũ
$stmt = $conn->prepare("SELECT * FROM nhacc WHERE MaNCC = ?");
$stmt->execute([$MaNCC]);
$supplier = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$supplier) {
    echo "❌ Không tìm thấy nhà cung cấp!";
    exit();
}

$message = []; // Khởi tạo mảng thông báo

// Xử lý cập nhật
if (isset($_POST['update_supplier'])) {
    $TenNCC = trim($_POST['TenNCC']);
    $SDT = trim($_POST['SDT']);
    $DiaChi = trim($_POST['DiaChi']);

    // Kiểm tra hợp lệ số điện thoại
    if (!preg_match('/^[0-9]{10}$/', $SDT)) {
        $message[] = "❌ Số điện thoại phải đúng 10 chữ số!";
    } else {
        $update_stmt = $conn->prepare("UPDATE nhacc SET SDT = ?, DiaChi = ?, TenNCC = ? WHERE MaNCC = ?");
        $success = $update_stmt->execute([$SDT, $DiaChi, $TenNCC, $MaNCC]);

        if ($success) {
            $message[] = "✅ Cập nhật thành công!";
            // Cập nhật lại dữ liệu hiển thị
            $supplier = ['MaNCC' => $MaNCC, 'TenNCC' => $TenNCC, 'SDT' => $SDT, 'DiaChi' => $DiaChi];
        } else {
            $message[] = "❌ Cập nhật thất bại!";
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
   <title>Sửa nhà cung cấp</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>

<?php include '../components/admin_header.php' ?>

<section class="form-container">
    <form method="POST">
        <h3>Sửa thông tin nhà cung cấp</h3>
        <div class="order_table">
            <span style="min-width: 160px;font-size:1.8rem;">Mã NCC:</span>
            <input type="text" name="MaNCC" value="<?= htmlspecialchars($supplier['MaNCC']) ?>" class="box" readonly>
        </div>
        <div class="order_table">
            <span style="min-width: 160px;font-size:1.8rem;">Tên nhà cung cấp:</span>
            <input type="text" name="TenNCC" value="<?= htmlspecialchars($supplier['TenNCC']) ?>" class="box" readonly>
        </div>
        <div class="order_table">
            <span style="min-width: 160px;font-size:1.8rem;">Số điện thoại:</span>
            <input type="text" name="SDT" value="<?= htmlspecialchars($supplier['SDT']) ?>" class="box" required>
        </div>
        <div class="order_table">
            <span style="min-width: 160px;font-size:1.8rem;">Địa chỉ:</span>
            <input type="text" name="DiaChi" value="<?= htmlspecialchars($supplier['DiaChi']) ?>" class="box" required>
        </div>
        <div class="flex-btn">
        <input type="submit" name="update_supplier" value="Cập nhật" class="btn">
        <a href="supplier.php" class="option-btn" >Quay lại</a>
        </div>
    </form>
</section>
<script src="../js/admin_script.js"></script>
</body>
</html>
