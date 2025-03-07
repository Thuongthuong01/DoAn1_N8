<?php
session_start();
include '../db_connect.php'; // Kết nối CSDL

// Truy vấn danh sách băng đĩa
$query = "SELECT * FROM discs ";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý băng đĩa</title>
    <link rel="stylesheet" href="../css/discs_style.css">
</head>
<body>

<div class="container">
    <h2>🎬 Quản lý băng đĩa</h2>
    <div class="navbar">
    <a href="../index.php">🏠 Trang chủ</a>
    <a href="../admin/dashboard.php" class="active">📊 Dashboard</a>
    <a href="../customer/customer.php">👥 Quản lý Khách hàng</a>
    <a href="../discs/discs.php">💿 Quản lý Băng Đĩa</a>
    <a href="../rentals/rentals.php">📄 Quản lý Thuê & Trả</a>
    <a href="../reports/reports.php">📈 Báo cáo</a>
    <a href="../logout.php">🚪 Đăng xuất</a>
</div>
    <a href="add_discs.php" class="btn">➕ Thêm băng đĩa mới</a>

    <table class="table">
        <thead>
            <tr>
                <th>Ảnh</th>
                <th>Tên băng đĩa</th>
                <th>Loại</th>
                <th>Thể loại</th>
                <th>Năm phát hành</th>
                <th>Giá thuê</th>
                <th>Số lượng</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($disc = $result->fetch_assoc()) : ?>
            <tr>
                <td><?php echo $disc['disc_id']; ?></td>
                <td><img src="../uploads/<?php echo $disc['cover_image']; ?>" class="cover"></td>
                <td><?php echo htmlspecialchars($disc['title']); ?></td>
                <td><?php echo htmlspecialchars($disc['category']); ?></td>
                <td><?php echo htmlspecialchars($disc['genre']); ?></td>
                <td><?php echo $disc['release_year']; ?></td>
                <td><?php echo number_format($disc['price'], 0) . " VND"; ?></td>
                <td><?php echo $disc['stock']; ?></td>
                <td>
                    <a href="edit_discs.php?id=<?php echo $disc['disc_id']; ?>" class="btn edit"> Sửa</a>
                    <a href="delete_discs.php?id=<?php echo $disc['disc_id']; ?>" class="btn delete" onclick="return confirm('Bạn có chắc chắn muốn xóa?');"> Xóa</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>