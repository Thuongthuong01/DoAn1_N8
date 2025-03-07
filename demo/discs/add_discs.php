<?php
session_start();
include '../db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $category = $_POST['category'];
    $genre = $_POST['genre'];
    $release_year = $_POST['release_year'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];

    // Xử lý upload ảnh
    $cover_image = time() . '_' . $_FILES['cover_image']['name'];
    move_uploaded_file($_FILES['cover_image']['tmp_name'], "../uploads/$cover_image");

    // Thêm vào CSDL
    $query = "INSERT INTO discs (title, category, genre, release_year, price, stock, cover_image) 
              VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssdis", $title, $category, $genre, $release_year, $price, $stock, $cover_image);
    $stmt->execute();

    header("Location: discs.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm băng đĩa</title>
    <link rel="stylesheet" href="../css/discs_style.css">
</head>
<body>

<div class="container">
    <h2>➕ Thêm băng đĩa mới</h2>

    <form action="add_discs.php" method="POST" enctype="multipart/form-data">
        <label>Tên băng đĩa:</label>
        <input type="text" name="title" required>

        <label>Loại:</label>
        <select name="category">
            <option value="Phim">Phim</option>
            <option value="Nhạc">Nhạc</option>
            <option value="Game">Game</option>
        </select>

        <label>Thể loại:</label>
        <input type="text" name="genre">

        <label>Năm phát hành:</label>
        <input type="number" name="release_year" required>

        <label>Giá thuê:</label>
        <input type="number" step="0.01" name="price" required>

        <label>Số lượng:</label>
        <input type="number" name="stock" required>

        <label>Ảnh bìa:</label>
        <input type="file" name="cover_image" required>

        <button type="submit" class="btn">💾 Lưu</button>
        <a href="discs.php" class="btn cancel">❌ Hủy</a>
    </form>
</div>

</body>
</html>