 <?php
include '../components/connect.php';

session_start();

if (!isset($_SESSION["user_id"])) {
   header("Location:admin_login");
   exit();
}
$keyword = isset($_POST['search-box']) ? trim($_POST['search-box']) : '';
$results = [];
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$price_columns = ['Dongia', 'GiaGoc', 'TongTien','TienPhat','TienTra'];


if ($keyword !== '') {
    // Tìm trong phiếu thuê
    $stmt = $conn->prepare("SELECT * FROM phieuthue WHERE MaThue LIKE ? OR MaKH LIKE ?");
    $stmt->execute(["%$keyword%", "%$keyword%"]);
    $results['Phiếu thuê'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Tìm trong phiếu trả
    $stmt = $conn->prepare("SELECT * FROM phieutra WHERE MaTra LIKE ? OR MaThue LIKE ?");
    $stmt->execute(["%$keyword%", "%$keyword%"]);
    $results['Phiếu trả'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Tìm trong phiếu nhập
    $stmt = $conn->prepare("SELECT * FROM phieunhap WHERE MaPhieu LIKE ? OR MaNCC LIKE ?");
    $stmt->execute(["%$keyword%", "%$keyword%"]);
    $results['Phiếu nhập'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Tìm trong khách hàng
    $stmt = $conn->prepare("SELECT * FROM khachhang WHERE MaKH LIKE ? OR TenKH LIKE ?");
    $stmt->execute(["%$keyword%", "%$keyword%"]);
    $results['Khách hàng'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

     // Tìm trong băng đĩa
    $stmt = $conn->prepare("SELECT * FROM bangdia WHERE MaBD LIKE ? OR TenBD LIKE ?");
    $stmt->execute(["%$keyword%", "%$keyword%"]);
    $results['Băng đĩa'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tìm kiếm</title>
       <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

<!-- custom css file link  -->
<link rel="stylesheet" href="../css/admin_style.css">

<style>
.product-table table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 30px;
    font-family: Arial, sans-serif;
}

.product-table th, .product-table td {
    border: 1px solid #ddd;
    padding: 8px 12px;
    text-align: left;
}

.product-table th {
    background-color: #f2f2f2;
    font-weight: 600;
}

.product-table tr:nth-child(even) {
    background-color: #fafafa;
}

.product-table tr:hover {
    background-color: #e1f5fe;
}

.product-table h2 {
    margin-top: 30px;
    margin-bottom: 10px;
    color: #333;
    font-weight: 700;
    font-size: 1.7rem;
}

</style>
</head>
<body>
   <?php include '../components/admin_header.php' ?>
   <section class="main-content show-products" style="padding-top: 0;">
    <h1 class="heading">Kết quả tìm kiếm cho: <?= htmlspecialchars($keyword) ?></h1>
<div class="product-table">
    <?php foreach ($results as $label => $rows): ?>
        <?php if (count($rows) > 0): // chỉ hiển thị mục có kết quả ?>
            <h2><?= htmlspecialchars($label) ?></h2>
            <table border="1" cellpadding="6" cellspacing="0">
                <tr>
                    <?php foreach (array_keys($rows[0]) as $column): ?>
                        <th><?= htmlspecialchars($column) ?></th>
                    <?php endforeach; ?>
                </tr>
                <?php foreach ($rows as $row): ?>
                    <tr>
                        <?php foreach ($row as $column => $cell): ?>
    <td>
        <?php
        // Nếu là mục "Băng đĩa" và cột là 'HinhAnh' (hoặc tên khác bạn chỉnh)
        if ($label === 'Băng đĩa' && $column === 'image' && !empty($cell)) {
            // Giả sử ảnh lưu trong folder ../images/bangdia/
            $img_path = "../uploaded_img/" . htmlspecialchars($cell);
            echo "<img src='$img_path' alt='Ảnh băng đĩa' style='max-width:100px; height:auto;display: block;margin: 0 auto;'>";
            }
        // Nếu là cột giá tiền thì thêm đơn vị VNĐ
        else if (in_array($column, $price_columns)) {
            // Format số (ví dụ dùng number_format để dễ đọc)
            $formatted_price = number_format($cell, 0, ',', '.') . ' VNĐ';
            echo $formatted_price;
        } else {
            echo htmlspecialchars($cell);
        }
        ?>
    </td>
<?php endforeach; ?>

                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
    <?php endforeach; ?>
                        </div>
</section>

</body>
</html>




