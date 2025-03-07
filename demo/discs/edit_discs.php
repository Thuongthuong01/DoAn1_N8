<?php
session_start();
include '../db_connect.php';

// Lấy ID băng đĩa từ URL
$id = $_GET['id'];
$query = "SELECT * FROM discs WHERE disc_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$disc = $result->fetch_assoc();

if (!$disc) {
    die("Băng đĩa không tồn tại!");
}

// Xử lý cập nhật thông tin
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $category = $_POST['category'];
    $genre = $_POST['genre'];
    $release_year = $_POST['release_year'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];

    // Kiểm tra nếu có upload ảnh mới
    if ($_FILES['cover_image']['name']) {
        $cover_image = time() . '_' . $_FILES['cover_image']['name'];
        move_uploaded_file($_FILES['cover_image']['tmp_name'], "../uploads/$cover_image");
    } else {
        $cover_image = $disc['cover_image']; // Giữ ảnh cũ
    }

    // Cập nhật vào CSDL
    $update_query = "UPDATE discs SET title=?, category=?, genre=?, release_year=?, price=?, stock=?, cover_image=? WHERE id=?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("ssssdisi", $title, $category, $genre, $release_year, $price, $stock, $cover_image, $id);
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
    <title>Sửa băng đĩa</title>
    <link rel="stylesheet" href="../css/discs_style.css">
</head>
<body>

<div class="container">
    <h2>✏️ Sửa băng đĩa</h2>

    <form action="edit_discs.php?id=<?php echo $id; ?>" method="POST" enctype="multipart/form-data">
        <label>Tên băng đĩa:</label>
        <input type="text" name="title" value="<?php echo $disc['title']; ?>" required>

        <label>Loại:</label>
        <select name="category">
            <option value="Phim" <?php if ($disc['category'] == "Phim") echo "selected"; ?>>Phim</option>
            <option value="Nhạc" <?php if ($disc['category'] == "Nhạc") echo "selected"; ?>>Nhạc</option>
        </select>

        <label>Thể loại:</label>
        <input type="text" name="genre" value="<?php echo $disc['genre']; ?>">

        <label>Năm phát hành:</label>
        <input type="number" name="release_year" value="<?php echo $disc['release_year']; ?>" required>

        <label>Giá thuê:</label>
        <input type="number" step="0.01" name="price" value="<?php echo $disc['price']; ?>" required>

        <label>Số lượng:</label>
        <input type="number" name="stock" value="<?php echo $disc['stock']; ?>" required>

        <label>Ảnh bìa:</label>
        <input type="file" name="cover_image">
        <br>
        <img src="../uploads/<?php echo $disc['cover_image']; ?>" width="100">

        <button type="submit" class="btn">💾 Lưu</button>
        <a href="discs.php" class="btn cancel">❌ Hủy</a>
    </form>
</div>

</body>
</html>