<?php
include '../db_connect.php';
session_start();

// Kiểm tra quyền admin
if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $phone = $_POST["phone"];
    $address = $_POST["address"];

    $sql = "INSERT INTO customers (name, email, phone, address) VALUES ('$name', '$email', '$phone', '$address')";
    
    if ($conn->query($sql) === TRUE) {
        header("Location: customer.php?status=added");
    } else {
        echo "Lỗi: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm khách hàng</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<div class="header">Thêm khách hàng mới</div>
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
        <input type="text" name="name" required>
    </div>
    <div class="input-group">    
        <label>Email:</label>
        <input type="email" name="email" required>
    </div>
    <div class="input-group">    
        <label>Số điện thoại:</label>
        <input type="text" name="phone" required>
    </div>
    <div class="input-group">   
        <label>Địa chỉ:</label>
        <textarea name="address"></textarea>
    </div>
        <button type="submit" class="btn-back">Thêm khách hàng</button>
    </form>
    <a href="../customer/customer.php" class = "btn-back">Quay lại</a>
</div>
</div>

</body>
</html>