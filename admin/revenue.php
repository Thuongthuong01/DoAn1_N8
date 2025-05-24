<?php
include '../components/connect.php';
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: admin_login.php");
    exit();
}

$filter = $_GET['filter'] ?? 'ngày';

switch ($filter) {
    case 'tháng':
        $query = "
            SELECT MONTH(NgayThue) AS thang, YEAR(NgayThue) AS nam, SUM(TongTien) AS tong_thue
            FROM phieuthue
            GROUP BY YEAR(NgayThue), MONTH(NgayThue)
            ORDER BY nam DESC, thang DESC
        ";
        break;
    case 'năm':
        $query = "
            SELECT YEAR(NgayThue) AS nam, SUM(TongTien) AS tong_thue
            FROM phieuthue
            GROUP BY YEAR(NgayThue)
            ORDER BY nam DESC
        ";
        break;
    default: // 'day'
        $query = "
            SELECT DATE(NgayThue) AS ngay, SUM(TongTien) AS tong_thue
            FROM phieuthue
            GROUP BY DATE(NgayThue)
            ORDER BY ngay DESC
        ";
        break;
}

$stmt = $conn->prepare($query);
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Quản lý sản phẩm</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">


    <style>
        body { font-family: Arial, sans-serif; width: 100%;  }
        h1 { text-align: center; margin-top:2rem;}
        table { width: 70%; margin: 20px auto; border-collapse: collapse; }
        th, td { border: 1px solid #333; padding: 8px; text-align: center; font-size:1.5rem;}
        th { background-color:rgb(196, 194, 194); }
        .filter-links { text-align: center; margin-top: 20px;font-size:1.5rem; }
        .filter-links a {
            margin: 0 10px; text-decoration: none; padding: 6px 12px;
            background: #007bff; color: white; border-radius: 4px;
        }
        .filter-links a.active { background: #0056b3; }
    </style>

</head>
<body>

<?php include '../components/admin_header.php' ?>
<h1 style="font-size:3rem;">Thống kê doanh thu theo <?= htmlspecialchars($filter) ?></h1>

<div class="filter-links">
    <a href="?filter=ngày" class="<?= $filter == 'ngày' ? 'active' : '' ?>">Theo ngày</a>
    <a href="?filter=tháng" class="<?= $filter == 'tháng' ? 'active' : '' ?>">Theo tháng</a>
    <a href="?filter=năm" class="<?= $filter == 'năm' ? 'active' : '' ?>">Theo năm</a>
</div>

<table>
    <thead>
        <tr>
            <?php if ($filter == 'ngày'): ?>
                <th>Ngày</th>
            <?php elseif ($filter == 'tháng'): ?>
                <th>Tháng / Năm</th>
            <?php else: ?>
                <th>Năm</th>
            <?php endif; ?>
            <th>Tổng doanh thu (VNĐ)</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($data as $row): ?>
        <tr>
            <?php if ($filter == 'ngày'): ?>
                <td><?php echo date('d/m/Y', strtotime($row['ngay'])); ?></td>

            <?php elseif ($filter == 'tháng'): ?>
                <td><?= $row['thang'] ?>/<?= $row['nam'] ?> </td>
                
            <?php else: ?>
                <td><?= $row['nam'] ?></td>
            <?php endif; ?>
            <td><?= number_format($row['tong_thue'], 0, ',', '.') ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>
