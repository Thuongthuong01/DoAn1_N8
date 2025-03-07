<?php
include '../db_connect.php';
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit();
}

$id = $_GET["id"];
$sql = "DELETE FROM customers WHERE id=$id";

if ($conn->query($sql) === TRUE) {
    header("Location: customer.php?status=deleted");
} else {
    echo "Lỗi: " . $conn->error;
}
?>