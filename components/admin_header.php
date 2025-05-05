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

      <a href="dashboard.php" > <img src="..\images\logo.png" alt=""  width=" 210" ></a>
      <form action="" method="post" class="search-form">
            <input type="text" name="search-box" placeholder="tìm kiếm..." required maxlength="100">
            <button type="submit" class="fas fa-search" name="search-box"></button>
        </form>
        <div class="icons">
            
            <div id="search-btn" class="fas fa-search"></div>
            <div id="user-btn"  class="fa-solid fa-user"></div>
            <!-- <div  id="menu-btn" class="fa-solid fa-bars"></div> -->
            <!-- <div id="toggle-btn" class="fa-solid fa-sun"></div> -->
        </div>

        <div class="profile">
        <?php if (isset($_SESSION["user_id"])) { ?>
            <img src="../uploads/SHOL0253.JPG" alt="">
            <h3>name</h3>
            <span>Nhân viên</span>
            <a href="profile.php" class="btn">Thông tin cá nhân</a>
            <a href="logout.php" class="option-btn"> Đăng xuất</a>
        <?php } else { ?>
            <span>Please login first</span>
            <div class="flex-btn">                
                <a href="admin_login.php" class="option-btn">Đăng nhập</a>
                <a href="register.php" class="option-btn">Đăng ký</a>
            </div>
        
        <?php } ?>
            
        </div>
     
   </section>
  
      </div>
</header>
<?php
$current_page = basename($_SERVER['PHP_SELF']); // lấy tên file hiện tại, ví dụ: "products.php"
?>
<div class="side-bar">
    <!-- <div class="close-side-bar">
        <i class="fa-solid fa-xmark"></i>
    </div> -->
    <?php if (isset($_SESSION["user_id"])) { ?>
    <div class="profile">
        <!-- <img src="../uploads/SHOL0253.JPG" alt="">
        <h3>name</h3>
        <span>Nhân viên</span> -->
        <!-- <a href="profile.php" class="btn">Thông tin</a> -->
    
    <nav class="navbar">
    <div style="font-weight: bold; color: #333; padding: 10px; font-size:20px;">Danh mục</div>

    <a href="dashboard.php" class="<?= $current_page == 'dashboard.php' ? 'active' : '' ?>"><i class="fa-solid fa-house"></i><span> Trang chủ</span></a>
        <a href="products.php" class="<?= $current_page == 'products.php' ? 'active' : '' ?>"><i class="fa-solid fa-compact-disc"></i><span> Sản phẩm</span></a>
        <a href="placed_orders.php" class="<?= $current_page == 'placed_orders.php' ? 'active' : '' ?>"><i class="fa-solid fa-shop"></i><span> Đơn hàng</span></a>
        <a href="users_accounts.php" class="<?= $current_page == 'users_accounts.php' ? 'active' : '' ?>"><i class="fa-solid fa-users"></i><span> Khách hàng</span></a>
      <a href="admin_accounts.php" class="<?= $current_page == 'admin_accounts.php' ? 'active' : '' ?>"><i class="fa-solid fa-headset"></i><span> Quản trị viên</span></a>
      <a href="revenue.php" class="<?= $current_page == 'revenue.php' ? 'active' : '' ?>"><i class="fa-solid fa-chart-line"></i><span> Doanh thu</span></a>
      <a href="warehouse.php" class="<?= $current_page == 'reviews.php' ? 'active' : '' ?>"><span>Quản lý kho</span></a>

    </nav>
   
    <?php } ?>
    </div>
</div>

<!-- <section class="quick-select">
    <h1 class="heading">Quick option</h1>
</section> -->

<!-- <footer class="footer">
    &copy; Project1 @ 2025 by <span>Group 8-DHMT16A1HN</span> | All rights reserved!
</footer> -->
<script src="../js/script.js"></script>
