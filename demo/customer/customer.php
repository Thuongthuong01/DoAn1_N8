<?php
include '../db_connect.php';
session_start();

// Kiểm tra quyền admin
if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit();
}

// Truy vấn dữ liệu khách hàng
$sql = "SELECT * FROM customers";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý khách hàng</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<div class="header">Quản lý khách hàng</div>

<div class="navbar">
    <a href="../index.php">🏠 Trang chủ</a>
    <a href="../admin/dashboard.php" class="active">📊 Dashboard</a>
    <a href="../customer/customer.php">👥 Quản lý Khách hàng</a>
    <a href="../discs/discs.php">💿 Quản lý Băng Đĩa</a>
    <a href="../rentals/rentals.php">📄 Quản lý Thuê & Trả</a>
    <a href="../reports/reports.php">📈 Báo cáo</a>
    <a href="../logout.php">🚪 Đăng xuất</a>
</div>

<div class="container">
<h2>Danh sách khách hàng</h2>

<!-- Hiển thị thông báo nếu có -->
<?php if (isset($_GET['status'])): ?>
    <p class="success">
        <?php 
            if ($_GET['status'] == 'added') echo "Khách hàng đã được thêm thành công!";
            if ($_GET['status'] == 'updated') echo "Thông tin khách hàng đã được cập nhật!";
            if ($_GET['status'] == 'deleted') echo "Khách hàng đã bị xóa!";
        ?>
    </p>
<?php endif; ?>
    <a href="add_customer.php" class="btn">Thêm khách hàng</a>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Họ và tên</th>
            <th>Email</th>
            <th>Điện thoại</th>
            <th>Địa chỉ</th>
            <th>Thao tác</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?= $row["customer_id"] ?></td>
            <td><?= $row["name"] ?></td>
            <td><?= $row["email"] ?></td>
            <td><?= $row["phone"] ?></td>
            <td><?= $row["address"] ?></td>
            <td>
                <a href="edit_customer.php?id=<?= $row['customer_id'] ?>">Sửa</a> | 
                <a href="delete_customer.php?id=<?= $row['customer_id'] ?>" onclick="return confirm('Xóa khách hàng này?')">Xóa</a>
            </td>
        </tr>
        <?php } ?>
    </table>
</div>

</body>
</html>