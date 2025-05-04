<?php
include '../components/connect.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header('location:admin_login.php');
    exit;
}

// Lấy tổng doanh thu theo tháng
// $revenue_stmt = $conn->prepare("
//     SELECT 
//         DATE_FORMAT(placed_on, '%m-%Y') AS month_year,
//         SUM(total_price) AS total_revenue
//     FROM orders
//     WHERE payment_status = 'Hoàn thành'
//     GROUP BY month_year
//     ORDER BY STR_TO_DATE(month_year, '%m-%Y') DESC
// ");
// $revenue_stmt->execute();
// $revenues = $revenue_stmt->fetchAll(PDO::FETCH_ASSOC);
// ?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý doanh thu</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
s
    <link rel="stylesheet" href="../css/admin_style.css">
    <!-- <style>
        .revenue-table {
            width: 80%;
            margin: auto;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .revenue-table th, .revenue-table td {
            padding: 12px;
            text-align: center;
            border: 1px solid #ddd;
        }

        .revenue-table th {
            background: #3498db;
            color: #fff;
        }

        .revenue-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        h2.heading {
            text-align: center;
            margin-top: 30px;
            color: #2c3e50;
        }
    </style> -->
</head>
<body>
<?php include '../components/admin_header.php' ?>
<!-- 
<section class="monthly-revenue">
    <h2 class="heading">Thống kê doanh thu theo tháng</h2>
    <table class="revenue-table">
        <thead>
            <tr>
                <th>Tháng - Năm</th>
                <th>Tổng doanh thu</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($revenues): ?>
                <?php foreach ($revenues as $row): ?>
                    <tr>
                        <td><?= $row['month_year']; ?></td>
                        <td><?= number_format($row['total_revenue'], 0, ',', '.'); ?> VNĐ</td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="2">Không có dữ liệu doanh thu.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</section> -->

</body>
</html>
