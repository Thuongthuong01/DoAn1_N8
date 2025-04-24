<?php
if(isset($message)){
   foreach($message as $message){
      echo '
      <div class="message">
         <span>'.$message.'</span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>
      ';
   }
}
?>

<header class="header">

   <section class="flex">

      <a href="home.php" class="logo">
               <img src="images/logo.png" width="190" >
      </a>
      <nav class="navbar">
         <a href="home.php">Trang Chủ</a>
         <a href="about.php">Giới Thiệu</a>
         <a href="menu.php">Thực Đơn</a>
         <a href="orders.php">Đơn Hàng</a>
         <a href="contact.php">Liên Hệ</a>
         <a href="rated_orders.php">Đánh Giá</a>
      </nav>

      <div class="icons">
         <?php
            $count_cart_items = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
            $count_cart_items->execute([$user_id]);
            $total_cart_items = $count_cart_items->rowCount();
         ?>
         <a href="search.php"><i class="fas fa-search"></i></a>
         <a href="cart.php"><i class="fas fa-shopping-cart"></i><span>(<?= $total_cart_items; ?>)</span></a>
         <div id="user-btn" class="fas fa-user"></div>
         <div id="menu-btn" class="fas fa-bars"></div>
      </div>

      <div class="profile">
         <?php
            $select_profile = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
            $select_profile->execute([$user_id]);
            if($select_profile->rowCount() > 0){
               $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
               $profile_image = !empty($fetch_profile['image']) ? $fetch_profile['image'] : 'user-icon.png';
         ?>
         <div style="text-align:center; margin-bottom: 1rem;">
         <img src="<?= !empty($fetch_profile['image']) ? $fetch_profile['image'] : 'images/user-icon.png'; ?>" alt="Avatar" width="120" style="border-radius: 50%; border: 2px solid #333;">
      </div>
         <p class="name"><?= $fetch_profile['name']; ?></p>
         <div class="flex">
            <a href="profile.php" class="btnn">Tài khoản</a>
            <a href="components/user_logout.php" onclick="return confirm('Đăng xuất khỏi trang web này?');" class="delete-btnn">Đăng Xuất</a>
         </div>
         
         <?php
            }else{
         ?>
            <p class="name">Vui lòng đăng nhập!</p>
            <p class="account">
            <a href="login.php">Đăng Nhập</a> hoặc
            <a href="register.php">Đăng Ký</a>
         </p> 
         <?php
          }
         ?>
      </div>

   </section>

</header>


<!-- menu trái -->
<nav class="sidebar">
   <div class="logo">
      Danh mục
   </div>
   <ul class="menu">   
      <li class="menu-item">
         <a href="menu.php?category=Ưu đãi"><i class="fa-solid fa-fire-flame-curved"></i><span> Khuyến Mại</span></a>
      </li>  
      <li class="menu-item">
         <a href="menu.php?category=Đồ ăn"><i class="fa-solid fa-burger"></i><span> Đồ ăn</span></a> 
         <ul class="submenu">
            <li><a href="menu.php?category=Đồ ăn&filter=Pizza" class="btn">Pizza</a></li>
            <li><a href="menu.php?category=Đồ ăn&filter=Bánh mì" class="btn">Bánh mì</a></li>
            <li><a href="menu.php?category=Đồ ăn&filter=Burger" class="btn">Burger</a></li>
            <li><a href="menu.php?category=Đồ ăn&filter=Gà Rán" class="btn">Gà rán</a></li>
         </ul>   
      </li> 
      <li class="menu-item">
         <a href="menu.php?category=Đồ uống"><i class="fa-solid fa-martini-glass-citrus"></i><span> Đồ uống</span></a>   
         <ul class="submenu">
            <li><a href="menu.php?category=Đồ uống&filter=Nước ép" class="btn">Nước ép</a></li>
            <li><a href="menu.php?category=Đồ uống&filter=Trà" class="btn">Trà</a></li>
            <li><a href="menu.php?category=Đồ uống&filter=Khác" class="btn">Đồ uống khác</a></li>
         </ul>  
      </li> 
      <li class="menu-item">
         <a href="menu.php?category=Tráng miệng"><i class="fa-solid fa-ice-cream"></i><span> Tráng miệng</span></a>
      </li> 
      <li class="menu-item">
         <a href="menu.php?category=Combo"><i class="fa-solid fa-utensils"></i><span> COMBO</span></a>
      </li>
   </ul>
</nav>

