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

      <a href="dashboard.php" > <img src="..\images\logo.png" alt=""  width=" 230" ></a>

      <div class="icons">
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
<nav class="sidebar">
      <div class="logo">
         Danh mục
      </div>
      <ul class="menu">   
         <li class="menu-item"><a href="dashboard.php"><span>Trang chủ</span></a></li>  
         <li class="menu-item"><a href="products.php" class="toggle-menu"><span>Quản lý sản phẩm</span></a></li> 
         <li class="menu-item"><a href="placed_orders.php" class="toggle-menu"><span>Quản lý đơn hàng</span></a></li> 
         <li class="menu-item"><a href="users_accounts.php"><span>Quản lý người dùng</span></a></li> 
         <li class="menu-item"><a href="admin_accounts.php"></i><span>Quản lý quản trị viên</span></a></li> 
         <li class="menu-item"><a href="#"></i><span>Quản lý doanh thu</span></a></li> 
         <li class="menu-item"><a href="#"></i><span>Quản lý kho</span></a></li> 
      </ul>
   </nav>