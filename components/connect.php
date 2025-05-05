<?php
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'qlbd'; // Thay bằng tên database của bạn
try {
    $conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Kết nối thất bại: " . $e->getMessage());
}
?>