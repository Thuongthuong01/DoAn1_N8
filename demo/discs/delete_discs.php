<?php
session_start();
include '../db_connect.php';

// Lấy ID băng đĩa từ URL
$id = $_GET['id'];

// Xóa ảnh bìa nếu có
$query = "SELECT cover_image FROM discs WHERE disc_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$disc = $result->fetch_assoc();
if ($disc && file_exists("../uploads/" . $disc['cover_image'])) {
    unlink("../uploads/" . $disc['cover_image']);
}

// Xóa khỏi CSDL
$delete_query = "DELETE FROM discs WHERE disc_id = ?";
$stmt = $conn->prepare($delete_query);
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location:discs.php");
exit();
?>