<?php
session_start();
include '../db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $customer_id = $_POST['customer_id'];
    $disc_id = $_POST['disc_id'];
    $rental_date = date("Y-m-d");

    // Thêm giao dịch thuê vào CSDL
    $sql = "INSERT INTO rentals (customer_id, disc_id, rental_date, status) 
            VALUES ('$customer_id', '$disc_id', '$rental_date', 0)";
    
    if ($conn->query($sql) === TRUE) {
        header("Location: rentals.php");
        exit();
    } else {
        echo "Lỗi: " . $conn->error;
    }
}

// Lấy danh sách khách hàng
$customers = $conn->query("SELECT customer_id, name  FROM customers");

// Lấy danh sách băng đĩa chưa thuê
$discs = $conn->query("SELECT disc_id, title FROM discs ");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thuê băng đĩa</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Thuê Băng Đĩa</h2>
        <form method="POST">
            <label>Khách hàng:</label>
            <select name="customer_id" required>
                <option value="">-- Chọn khách hàng --</option>
                <?php while ($row = $customers->fetch_assoc()) { ?>
                    <option value="<?= $row['customer_id'] ?>"><?= $row['name'] ?></option>
                <?php } ?>
            </select>

            <label>Băng đĩa:</label>
            <select name="disc_id" required>
                <option value="">-- Chọn băng đĩa --</option>
                <?php while ($row = $discs->fetch_assoc()) { ?>
                    <option value="<?= $row['disc_id'] ?>"><?= $row['title'] ?> </option>
                <?php } ?>
            </select>

            <button type="submit">Xác nhận thuê</button>
        </form>
    </div>
</body>
</html>