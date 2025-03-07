<?php
session_start();
include '../db_connect.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit();
}

// Lấy thống kê số lượng khách hàng và băng đĩa
$sql_customers = "SELECT COUNT(*) AS total_customers FROM customers";
$sql_discs = "SELECT COUNT(*) AS total_discs FROM discs";
$sql_rentals = "SELECT COUNT(*) AS total_rentals FROM rentals";

$result_customers = $conn->query($sql_customers);
$result_discs = $conn->query($sql_discs);
$result_rentals = $conn->query($sql_rentals);

$total_customers = $result_customers->fetch_assoc()['total_customers'];
$total_discs = $result_discs->fetch_assoc()['total_discs'];
$total_rentals = $result_rentals->fetch_assoc()['total_rentals'];
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bảng Điều Khiển - Quản lý Cửa hàng Băng Đĩa</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<div class="header">Bảng Điều Khiển</div>

<!-- Thanh điều hướng -->
<div class="navbar">
    <a href="../index.php">🏠 Trang chủ</a>
    <a href="../admin/dashboard.php" class="active">📊 Dashboard</a>
    <a href="../customer/customer.php">👥 Quản lý Khách hàng</a>
    <a href="../discs/discs.php">💿 Quản lý Băng Đĩa</a>
    <a href="../rentals/rentals.php">📄 Quản lý Thuê & Trả</a>
    <a href="../reports/reports.php">📈 Báo cáo</a>
    <a href="../logout.php">🚪 Đăng xuất</a>
</div>

<!-- Nội dung chính -->
<div class="container">
    <div class="card">
        <h3>Khách hàng</h3>
        <p><strong><?php echo $total_customers; ?></strong> khách hàng đã đăng ký.</p>
        <a href="../customer/customer.php" class="btn">Quản lý</a>
    </div>

    <div class="card">
        <h3>Băng đĩa</h3>
        <p><strong><?php echo $total_discs; ?></strong> băng đĩa trong kho.</p>
        <a href="../discs/discs.php" class="btn">Quản lý</a>
    </div>

    <div class="card">
        <h3>Thuê & Trả</h3>
        <p><strong><?php echo $total_rentals; ?></strong> lượt thuê đang hoạt động.</p>
        <a href="../rentals/rentals.php" class="btn">Quản lý</a>
    </div>

    <div class="card">
        <h3>Thống kê & Báo cáo</h3>
        <p>Xem các báo cáo về doanh thu, tình trạng thuê.</p>
        <a href="../reports/reports.php" class="btn">Xem báo cáo</a>
    </div>
</div>

<div class="footer">
    &copy; 2025 - Cửa hàng cho thuê băng đĩa
</div>

</body>
</html>