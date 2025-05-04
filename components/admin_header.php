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
      <div class="icons">
         <a href="search.php" class="fas fa-search" ></a>
         <div id="menu-btn" class="fas fa-bars"></div>
         <div id="user-btn" class="fas fa-user"></div>
         
      </div>

      <div class="profile">
         <?php
            $select_profile = $conn->prepare("SELECT * FROM `quantri` WHERE MaAD = ?");
            $select_profile->execute([$admin_id]);
            if($select_profile->rowCount() > 0){
            $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
         ?>
         <p><?= $fetch_profile['TenAD']; ?></p>
         <a href="update_profile.php" class="btn">Tài khoản</a>
         <a href="../components/admin_logout.php" onclick="return confirm('Đăng xuất khỏi trang web này?');" class="delete-btn">Đăng xuất</a>
         <?php
            }else{
         ?>
            
         <div class="flex-btn">
            <a href="admin_login.php" class="option-btn">Đăng nhập</a>
            <a href="register_admin.php" class="option-btn">Đăng ký</a>
         </div>
         <?php
            }
            ?>
      </div>
     
   </section>
  
      </div>
</header>
<?php
$current_page = basename($_SERVER['PHP_SELF']); // lấy tên file hiện tại, ví dụ: "products.php"
?>

<nav class="sidebar">
   <div class="logo">Danh mục</div>
   <ul class="menu">   
      <li><a href="dashboard.php" class="<?= $current_page == 'dashboard.php' ? 'active' : '' ?>"><span>Trang chủ</span></a></li>  
      <li><a href="products.php" class="<?= $current_page == 'products.php' ? 'active' : '' ?>"><span>Quản lý sản phẩm</span></a></li> 
      <li><a href="placed_orders.php" class="<?= $current_page == 'placed_orders.php' ? 'active' : '' ?>"><span>Quản lý đơn hàng</span></a></li> 
      <li><a href="users_accounts.php" class="<?= $current_page == 'users_accounts.php' ? 'active' : '' ?>"><span>Quản lý người dùng</span></a></li> 
      <li><a href="admin_accounts.php" class="<?= $current_page == 'admin_accounts.php' ? 'active' : '' ?>"><span>Quản lý quản trị viên</span></a></li> 
      <li><a href="revenue.php" class="<?= $current_page == 'revenue.php' ? 'active' : '' ?>"><span>Quản lý doanh thu</span></a></li> 
      <li><a href="warehouse.php" class="<?= $current_page == 'reviews.php' ? 'active' : '' ?>"><span>Quản lý kho</span></a></li> 
   </ul>
</nav>