<?php

include '../components/connect.php';
session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:admin_login.php');
   exit;
}

?>

<!DOCTYPE html>
<html lang="vi">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Đánh giá của khách hàng</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="../css/admin_style.css">
   <style>
      .review-box {
         background:rgb(255, 255, 255);
         padding: 20px;
         border: 2px solid #ccc;
         border-radius: 10px;
         margin-bottom: 20px;
         font-size: 16px;
      }
      .review-box h4 {
         margin-bottom: 10px;
         color: #555;
      }
      .review-box p {
         margin-bottom: 6px;
      }
      .rating {
         color: #fbc02d;
         font-size: 18px;
      }
   </style>
</head>
<body>

<?php include '../components/admin_header.php'; ?>

<section class="placed-orders">
   <h1 class="heading">Đánh giá của khách hàng</h1>

   <div class="box-container">

   <?php
      $select_reviews = $conn->prepare("SELECT r.*, u.name AS user_name, o.placed_on 
                                        FROM reviews r 
                                        JOIN users u ON r.user_id = u.id 
                                        JOIN orders o ON r.order_id = o.id 
                                        ORDER BY r.review_date DESC");
      $select_reviews->execute();
      if($select_reviews->rowCount() > 0){
         while($row = $select_reviews->fetch(PDO::FETCH_ASSOC)){
   ?>
   <div class="review-box">
      <h4>Khách hàng: <?= $row['user_name']; ?> </h4>
      <h4> Ngày đặt: <?= $row['placed_on']; ?></h4>
      <p><strong>Đánh giá:</strong> <?= $row['review']; ?></p>
      <p><strong>Ngày gửi:</strong> <?= $row['review_date']; ?></p>
      <p class="rating"><strong>Số sao:</strong> <?= str_repeat('⭐️', $row['rating']); ?> (<?= $row['rating']; ?>/5)</p>
   </div>
   <?php
         }
      } else {
         echo '<p class="empty">Chưa có đánh giá nào!</p>';
      }
   ?>

   </div>
</section>

<script src="../js/admin_script.js"></script>
</body>
</html>
