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
        <a href="dashboard.php" style="margin-left: 5rem;"> <img src="..\images\logo.png" alt=""  width=" 210" ></a>
        <form action="search.php" method="post" class="search-form">
    <input type="text" name="search-box" placeholder="Tìm kiếm..." required maxlength="100">
    <button type="submit" class="fas fa-search" name="search-btn"></button>
</form>

        <h2 stype="margin:auto;">Xin chào, <?= htmlspecialchars($admin_name); ?></h2>
        <div class="icons">
            <div id="search-btn" class="fas fa-search"></div>
            <div id="user-btn"  class="fa-solid fa-user"></div>
            <!-- <div  id="menu-btn" class="fa-solid fa-bars"></div> -->
            <!-- <div id="toggle-btn" class="fa-solid fa-sun"></div> -->
        </div>
        <div class="profile">
            
        <?php if (isset($_SESSION["user_id"])) { ?>
            <img src="../images/user1.png" alt="">
            <h3>Xin chào, <?= htmlspecialchars($admin_name); ?></h3>
            <span>Quản trị viên</span>
            <a href="admin_accounts.php" class="btn">Thông tin cá nhân</a>
            <a href="../components/admin_logout.php" class="option-btn" onclick="return confirmLogout();"> Đăng xuất</a>
         <?php } ?> 
        </div>
    </section>
</header>
<?php
$current_page = basename($_SERVER['PHP_SELF']); // lấy tên file hiện tại, ví dụ: "products.php"
?>
<?php
// Các trang con của dropdown Đơn hàng
$order_pages = ['placed_orders.php', 'return_orders.php'];
$is_order_open = in_array($current_page, $order_pages);
?>

<div class="side-bar">
  
    <?php if (isset($_SESSION["user_id"])) { ?>

    <nav class="navbar">
        <div style="font-weight: bold; color: #333; padding: 20px; font-size:20px;">Danh mục</div>

        <a href="dashboard.php" class="<?= $current_page == 'dashboard.php' ? 'active' : '' ?>"><i class="fa-solid fa-house"></i><span> Trang chủ</span></a>
        <a href="products.php" class="<?= $current_page == 'products.php' ? 'active' : '' ?>"><i class="fa-solid fa-compact-disc"></i><span> Sản phẩm</span></a>
        <div class="dropdown <?= $is_order_open ? 'open' : '' ?>">
        <a href="javascript:void(0);" class="dropdown-toggle"><i class="fas fa-box"></i><span> Đơn hàng</span></a>
        <div class="dropdown-menu">
            <a href="placed_orders.php" class="<?= $current_page == 'placed_orders.php' ? 'active' : '' ?>">Đơn hàng thuê</a>
            <a href="return_orders.php" class="<?= $current_page == 'return_orders.php' ? 'active' : '' ?>">Đơn hàng trả</a>
        </div>
        <a href="users_accounts.php" class="<?= $current_page == 'users_accounts.php' ? 'active' : '' ?>"><i class="fas fa-user"></i><span> Khách hàng</span></a>
        <a href="admin_accounts.php" class="<?= $current_page == 'admin_accounts.php' ? 'active' : '' ?>"><i class="fas fa-headset"></i><span> Quản trị viên</span></a>
        <a href="revenue.php" class="<?= $current_page == 'revenue.php' ? 'active' : '' ?>"><i class="fa-solid fa-chart-line"></i><span> Doanh thu</span></a>
        <a href="supplier.php" class="<?= $current_page == 'supplier.php' ? 'active' : '' ?>"><i class="fas fa-truck-moving"></i><span> Nhà cung cấp</span></a>
        <a href="warehouse.php" class="<?= $current_page == 'warehouse.php' ? 'active' : '' ?>"><i class="fas fa-warehouse"></i><span> Kho hàng</span></a>
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
<script>
function confirmLogout() {
    return confirm("Bạn có chắc chắn muốn đăng xuất?");
}
</script>

<script src="../js/admin_script.js"></script>
<!-- <section class="quick-select">
    <h1 class="heading">Quick option</h1>
</section> -->

<!-- <footer class="footer">
    &copy; Project1 @ 2025 by <span>Group 8-DHMT16A1HN</span> | All rights reserved!
</footer> -->

