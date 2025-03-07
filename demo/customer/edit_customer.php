<?php
include '../db_connect.php';
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit();
}

$id = $_GET["id"];
$sql = "SELECT * FROM customers WHERE id = $id";
$result = $conn->query($sql);
$customer = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $phone = $_POST["phone"];
    $address = $_POST["address"];

    $sql = "UPDATE customers SET name='$name', email='$email', phone='$phone', address='$address' WHERE id=$id";
    
    if ($conn->query($sql) === TRUE) {
        header("Location: customer.php?status=updated");
    } else {
        echo "Lỗi: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa khách hàng</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<div class="header">Sửa thông tin khách hàng</div>
<div class="navbar">
    <a href="../index.php">🏠 Trang chủ</a>
    <a href="../admin/dashboard.php" class="active">📊 Dashboard</a>
    <a href="../customer/customer.php">👥 Quản lý Khách hàng</a>
    <a href="../discs/discs.php">💿 Quản lý Băng Đĩa</a>
    <a href="../rentals/rentals.php">📄 Quản lý Thuê & Trả</a>
    <a href="../reports/reports.php">📈 Báo cáo</a>
    <a href="../logout.php">🚪 Đăng xuất</a>
</div>

<div class="form-container">   
    <form action="" method="post">
    <div class="input-group">
        <label>Họ và tên:</label>
        <input type="text" name="name" value="<?= $customer['name'] ?>" required>
    </div>
    <div class="input-group">    
        <label>Email:</label>
        <input type="email" name="email" value="<?= $customer['email'] ?>" required>
    </div>
    <div class="input-group">    
        <label>Số điện thoại:</label>
        <input type="text" name="phone" value="<?= $customer['phone'] ?>" required>
    </div>
    <div class="input-group">    
        <label>Địa chỉ:</label>
        <textarea name="address"><?= $customer['address'] ?></textarea>
    </div>
        <button type="submit">Cập nhật</button>
    </form>
    <a href="../customer/customer.php" class = "btn-back">Quay lại</a>
</div>
</div>

</body>
</html>