<?php

include '../components/connect.php';

session_start();

if (!isset($_SESSION["user_id"])) {
   header("Location:admin_login");
   exit();
}

// Giả sử admin hoặc user đã login
// Nếu user muốn xem lịch sử của chính mình:
// Nếu user là khách thì lấy từ session
$is_admin = isset($_SESSION['user_id']) && !isset($_SESSION['MaKH']);

$MaKH = null;

if ($is_admin && isset($_GET['MaKH'])) {
    $MaKH = $_GET['MaKH']; // admin chọn khách hàng để xem
} elseif (isset($_SESSION['MaKH'])) {
    $MaKH = $_SESSION['MaKH']; // khách hàng tự xem
}
 // Nếu bạn muốn xem riêng từng khách

// Nếu admin muốn xem tất cả lịch sử thuê thì bỏ điều kiện WHERE pt.MaKH = ?
// Lấy danh sách khách hàng để admin chọn
$khachhangs = [];
if (!$MaKH) { // chỉ admin mới có quyền xem tất cả khách và chọn
    $stmtKH = $conn->prepare("SELECT MaKH, TenKH FROM khachhang ORDER BY TenKH ASC");
    $stmtKH->execute();
    $khachhangs = $stmtKH->fetchAll(PDO::FETCH_ASSOC);
}

// Lấy danh sách phiếu thuê (cho user hoặc admin tùy chỉnh)
if ($MaKH) {
    $stmt = $conn->prepare("
        SELECT pt.MaThue, pt.NgayThue, pt.NgayTraDK, kh.TenKH,
               CASE WHEN ptr.MaThue IS NOT NULL THEN 'Đã trả' ELSE 'Chưa trả' END AS TrangThai
        FROM phieuthue pt
        JOIN khachhang kh ON pt.MaKH = kh.MaKH
        LEFT JOIN phieutra ptr ON pt.MaThue = ptr.MaThue
        WHERE pt.MaKH = ?
        ORDER BY pt.NgayThue DESC
    ");
    $stmt->execute([$MaKH]);
} else {
    $stmt = $conn->prepare("
        SELECT pt.MaThue, pt.NgayThue, pt.NgayTraDK, kh.TenKH,
               CASE WHEN ptr.MaThue IS NOT NULL THEN 'Đã trả' ELSE 'Chưa trả' END AS TrangThai
        FROM phieuthue pt
        JOIN khachhang kh ON pt.MaKH = kh.MaKH
        LEFT JOIN phieutra ptr ON pt.MaThue = ptr.MaThue
        ORDER BY pt.NgayThue DESC
    ");
    $stmt->execute();
}


$phieu_thue = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Xử lý xem chi tiết phiếu thuê nếu có GET param
$chi_tiet = [];
if (isset($_GET['detail'])) {
    $maThueChiTiet = $_GET['detail'];
    $stmt2 = $conn->prepare("
        SELECT ct.MaCT, b.MaBD, b.TenBD, b.TinhTrang, b.ChatLuong, ct.SoLuong
        FROM chitietphieuthue ct
        JOIN bangdia b ON ct.MaBD = b.MaBD
        WHERE ct.MaThue = ?
    ");
    $stmt2->execute([$maThueChiTiet]);
    $chi_tiet = $stmt2->fetchAll(PDO::FETCH_ASSOC);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Lịch sử thuê hàng </title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>
<?php include '../components/admin_header.php' ?>
<section class="main-content show-products" style="padding-top: 0;">
<h1 class="heading">
    Lịch sử thuê băng đĩa
    <?php
        if ($is_admin && isset($_GET['MaKH'])) {
            echo ": " . htmlspecialchars($_GET['MaKH']);
        } elseif (isset($_SESSION['MaKH'])) {
            echo "của bạn";
        }
    ?>
</h1>

<table class="product-table">
    <thead>
        <tr>
            <th>Mã thuê</th>
            <th>Tên khách hàng</th>
            <th>Ngày thuê</th>
            <th>Ngày hẹn trả</th>
            <th>Trạng thái</th>
            <th>Xem chi tiết</th>
        </tr>
    </thead>
    <tbody>
        <?php if (count($phieu_thue) == 0): ?>
            <tr><td colspan="<?= $MaKH ? 6 : 6 ?>">Không có phiếu thuê nào.</td></tr>
        <?php else: ?>
            <?php foreach($phieu_thue as $pt): ?>
                <tr>
                    <td><?= htmlspecialchars($pt['MaThue']) ?></td>
                    <td><?= htmlspecialchars($pt['TenKH']) ?></td>
                    <td><?= htmlspecialchars($pt['NgayThue']) ?></td>
                    <td><?= htmlspecialchars($pt['NgayTraDK']) ?></td>
                    <td><?= htmlspecialchars($pt['TrangThai']) ?></td>
                    <td><a href="?<?= $MaKH ? 'MaKH=' . urlencode($MaKH) . '&' : '' ?>detail=<?= urlencode($pt['MaThue']) ?>" class="btn">Xem</a></td>

                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php if (count($chi_tiet) > 0): ?>
<h2 style="margin-top:10px; font-size:1.5rem;">Chi tiết phiếu thuê: <?= htmlspecialchars($maThueChiTiet) ?></h2>
<table class="product-table">
    <thead>
        <tr>
            <th>Mã băng đĩa</th>
            <th>Tên băng đĩa</th>
            <th>Tình trạng</th>
            <th>Chất lượng</th>
            <th>Số lượng</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($chi_tiet as $ct): ?>
        <tr>
            <td><?= htmlspecialchars($ct['MaBD']) ?></td>
            <td><?= htmlspecialchars($ct['TenBD']) ?></td>
            <td><?= htmlspecialchars($ct['TinhTrang']) ?></td>
            <td><?= htmlspecialchars($ct['ChatLuong']) ?></td>
            <td><?= htmlspecialchars($ct['SoLuong']) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>
</section>
</body>
</html>
