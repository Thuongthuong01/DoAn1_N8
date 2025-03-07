<?php
session_start();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý cửa hàng băng đĩa</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="header">Hệ Thống Quản Lý Cửa Hàng Cho Thuê Băng Đĩa</div>

<div class="navbar">
    <a href="index.php">🏠 Trang chủ</a>
    <?php if (isset($_SESSION["user_id"])) { ?>
        <a href="admin/dashboard.php" class="active">📊 Dashboard</a>
        <a href="customer/customer.php">👥 Quản lý Khách hàng</a>
    <a href="/discs/discs.php">💿 Quản lý Băng Đĩa</a>
    <a href="/rentals/rentals.php">📄 Quản lý Thuê & Trả</a>
    <a href="/reports/reports.php">📈 Báo cáo</a>
    <a href="logout.php">🚪 Đăng xuất</a>
    <?php } else { ?>
        <a href="login.php">Đăng nhập</a>
        <a href="register.php">Đăng ký</a>
    <?php } ?>
</div>

<div class="container">
    <div class="card">
        <h3>Giới thiệu về cửa hàng</h3>
        <p>Hệ thống quản lý cửa hàng cho thuê băng đĩa giúp bạn quản lý khách hàng, sản phẩm, đơn thuê và thanh toán một cách hiệu quả.</p>
    </div>

    <div class="card">
        <h3>Tính năng nổi bật</h3>
        <ul>
            <li>Quản lý khách hàng</li>
            <li>Quản lý danh mục băng đĩa</li>
            <li>Quản lý thuê & trả băng đĩa</li>
            <li>Hóa đơn & thanh toán</li>
            <li>Báo cáo & thống kê</li>
        </ul>
    </div>
    <!-- Hiển thị danh sách khách hàng mới nhất -->
    <!-- <div class="card">
        <h3>Khách hàng mới nhất</h3>
        <table border="1">
            <tr>
                <th>ID</th>
                <th>Họ tên</th>
                <th>Email</th>
            </tr>
            <?php
            $sql = "SELECT id, name, email FROM customer ORDER BY created_at DESC LIMIT 5";
            $result = $conn->query($sql);
            while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?= $row["id"] ?></td>
                    <td><?= $row["name"] ?></td>
                    <td><?= $row["email"] ?></td>
                </tr>
            <?php } ?>
        </table>
        <a href="customer/customer.php" class="btn">Xem tất cả</a>
    </div> -->
</div>
<div class="footer">
    &copy; 2025 - Cửa hàng cho thuê băng đĩa
</div>

</body>
</html>