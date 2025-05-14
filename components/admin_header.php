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

$admin_name = '';

if (isset($_SESSION['user_id'])) {
   $stmt = $conn->prepare("SELECT TenAD FROM quantri WHERE MaAD = ?");
   $stmt->execute([$_SESSION['user_id']]);
   $admin = $stmt->fetch(PDO::FETCH_ASSOC);
   if ($admin) {
      $admin_name = $admin['TenAD'];
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
        <h3>Xin chào, <?= htmlspecialchars($admin_name); ?></h3>
        <div class="icons">
            <div id="search-btn" class="fas fa-search"></div>
            <div id="user-btn"  class="fa-solid fa-user"></div>
            <!-- <div  id="menu-btn" class="fa-solid fa-bars"></div> -->
            <!-- <div id="toggle-btn" class="fa-solid fa-sun"></div> -->
        </div>
        <div class="profile">
        <?php if (isset($_SESSION["user_id"])) { ?>
            <img src="../uploads/SHOL0253.JPG" alt="">
            <h3>Xin chào, <?= htmlspecialchars($admin_name); ?></h3>
            <span>Quản trị viên</span>
            <a href="admin_accounts.php" class="btn">Thông tin cá nhân</a>
            <a href="../components/admin_logout.php" class="option-btn"> Đăng xuất</a>
        
         <?php } ?> 
        </div>
    </section>
</header>
<?php
$current_page = basename($_SERVER['PHP_SELF']); // lấy tên file hiện tại, ví dụ: "products.php"
?>

<div class="side-bar">
  
    <?php if (isset($_SESSION["user_id"])) { ?>

    <nav class="navbar">
    <div style="font-weight: bold; color: #333; padding: 10px; font-size:20px;">Danh mục</div>

    <a href="dashboard.php" class="<?= $current_page == 'dashboard.php' ? 'active' : '' ?>"><i class="fa-solid fa-house"></i><span> Trang chủ</span></a>
    <a href="products.php" class="<?= $current_page == 'products.php' ? 'active' : '' ?>"><i class="fa-solid fa-compact-disc"></i><span> Sản phẩm</span></a>
    <div class="dropdown">
        <a href="javascript:void(0);" class="dropdown-toggle"><i class="fas fa-box"></i><span> Đơn hàng <i class="fas fa-caret-down"></i></span></a>
        <div class="dropdown-menu">
            <a href="placed_orders.php">Đơn hàng thuê</a>
            <a href="return_orders.php">Đơn hàng trả</a>
        </div>
    </div>
    <a href="users_accounts.php" class="<?= $current_page == 'users_accounts.php' ? 'active' : '' ?>"><i class="fas fa-user"></i><span> Khách hàng</span></a>
    <a href="admin_accounts.php" class="<?= $current_page == 'admin_accounts.php' ? 'active' : '' ?>"><i class="fas fa-headset"></i><span> Quản trị viên</span></a>
    <a href="revenue.php" class="<?= $current_page == 'revenue.php' ? 'active' : '' ?>"><i class="fa-solid fa-chart-line"></i><span> Doanh thu</span></a>
    <a href="warehouse.php" class="<?= $current_page == 'reviews.php' ? 'active' : '' ?>"><i class="fas fa-warehouse"></i><span> Kho</span></a>
    </nav>
   
    <?php } ?>
    
</div>
<script>
document.addEventListener("DOMContentLoaded", function () {
   const dropdownToggles = document.querySelectorAll(".dropdown-toggle");

   dropdownToggles.forEach(function (toggle) {
      toggle.addEventListener("click", function () {
         const parent = this.closest(".dropdown");
         parent.classList.toggle("open");
      });
   });
});
</script>

<!-- <section class="quick-select">
    <h1 class="heading">Quick option</h1>
</section> -->

<!-- <footer class="footer">
    &copy; Project1 @ 2025 by <span>Group 8-DHMT16A1HN</span> | All rights reserved!
</footer> -->

