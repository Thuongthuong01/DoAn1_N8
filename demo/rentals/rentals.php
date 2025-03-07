<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Thuê & Trả Băng Đĩa</title>
    <link rel="stylesheet" href="../css/rental_style.css">
    <link rel="stylesheet" href="../css/discs_style.css">
</head>
<body>
    <div class="container">
        <h2>Quản lý Thuê & Trả Băng Đĩa</h2>
        <div class="navbar">
    <a href="../index.php">🏠 Trang chủ</a>
    <a href="../admin/dashboard.php" class="active">📊 Dashboard</a>
    <a href="../customer/customer.php">👥 Quản lý Khách hàng</a>
    <a href="../discs/discs.php">💿 Quản lý Băng Đĩa</a>
    <a href="../rentals/rentals.php">📄 Quản lý Thuê & Trả</a>
    <a href="../reports/reports.php">📈 Báo cáo</a>
    <a href="../logout.php">🚪 Đăng xuất</a>
</div>
        <a href="add_rent.php" class="btn">Thêm băng đĩa</a>
        <button onclick="window.location.href='return_rent.php'">Trả băng đĩa</button>

        <table>
            <thead>
                <tr>
                    <th>Mã giao dịch</th>
                    <th>Khách hàng</th>
                    <th>Băng đĩa</th>
                    <th>Ngày thuê</th>
                    <th>Ngày trả</th>
                    <th>Trạng thái</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php
                include '../db_connect.php';
                session_start();
                $sql = "SELECT * FROM rentals";
                $result = $conn->query($sql);
                
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                        <td>{$row['rental_id']}</td>
                        <td>{$row['customer_id']}</td>
                        <td>{$row['disc_id']}</td>
                        <td>{$row['rental_date']}</td>
                        <td>{$row['return_date']}</td>
                        <td>".($row['status'] == 1 ? 'Đã trả' : 'Chưa trả')."</td>
                        <td>
                            <a href='return_rent.php?id={$row['rental_id']}'>Trả</a> | 
                            <a href='delete_rent.php?id={$row['rental_id']}'>Xóa</a>
                        </td>
                    </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>