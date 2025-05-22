<?php
include '../components/connect.php';
session_start();

if (!isset($_SESSION["user_id"])) {
   header("Location:admin_login");
   exit();
}

$current_month = date('m');
$current_year = date('Y');

$month_revenue_query = $conn->prepare("
    SELECT SUM(TongTien) AS total 
FROM phieuthue
WHERE MONTH(NgayThue) = ? AND YEAR(NgayThue) = ?

");
$month_revenue_query->execute([$current_month, $current_year]);
$month_revenue = $month_revenue_query->fetchColumn() ?? 0;

?> 

<!DOCTYPE html>
<html lang="vi">
<head>
   <meta charset="UTF-8" />
   <meta http-equiv="X-UA-Compatible" content="IE=edge" />
   <meta name="viewport" content="width=device-width, initial-scale=1.0" />
   <title>Trang chủ quản trị</title>
   <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css"
   />
   <link rel="stylesheet" href="../css/admin_style.css" />
   <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body style="background: #f0f5ff;">
   <?php include '../components/admin_header.php' ?>
          


   <section class="dashboard">
      <!-- <div class="dashboard-header">
         <h1 class="gradient-text">🎉 Quản Lý Hệ Thống CD House</h1>
         <p class="current-date">📅 <?= date('d/m/Y') ?></p>
      </div> -->
      <!-- Hộp giới thiệu -->
      <!-- <div class="box intro-box">
         <div class="icon-container">
            <i class="fas fa-store-alt"></i>
         </div> -->
         <!-- <h3>🏪 Nhịp Làm Việc Số</h3>
         <p>Khám phá thế giới  đỉnh cao cùng CD House!</p> -->
         
         <a href="intro.php" class="btn pink-btn">
            <i class="fas fa-chevron-circle-right"></i>  Hệ thống quản lý của CD HOUSE 
         </a>
      </div>

      <div class="box-container">
         <!-- Doanh thu -->
         <div class="box revenue-box">
            <div class="box-icon">
               <!-- <i class="fas fa-coins"></i> -->
            </div>
            <h3><?= number_format($month_revenue ?? 0, 0, ',', '.') ?> VNĐ</h3>
            <p>💰 Doanh thu tháng <?= $current_month . '/' . $current_year ?></p>
            <a href="revenue.php" class="btn pulse-effect">
               <i class="fas fa-chart-line"></i> Xem chi tiết
            </a>
         </div>
         

         <!-- Đơn hàng -->
         <div class="box orders-box">
            <div class="box-icon">
               <!-- <i class="fas fa-shopping-cart"></i> -->
            </div>
            <?php
               $select_all_orders = $conn->prepare("SELECT * FROM `phieuthue`");
               $select_all_orders->execute();
               $total_orders = $select_all_orders->rowCount();
            ?>
            <h3><?= $total_orders ?></h3>
            <p>📦 Tổng phiếu thuê</p>
            <a href="placed_orders.php" class="btn pulse-effect">
               <i class="fas fa-clipboard-list"></i> Xem đơn hàng
            </a>
         </div>

         <!-- Sản phẩm -->
         <div class="box products-box">
            <div class="box-icon">
               <!-- <i class="fas fa-compact-disc"></i> -->
            </div>
            <?php
               $select_products = $conn->prepare("SELECT * FROM `bangdia`");
               $select_products->execute();
               $numbers_of_products = $select_products->rowCount();
            ?>
            <h3><?= $numbers_of_products ?></h3>
            <p>🎵 Băng đĩa có sẵn</p>
            <a href="products.php" class="btn pulse-effect">
               <i class="fas fa-box-open"></i> Quản lý kho
            </a>
         </div>

         <!-- Người dùng -->
         <div class="box users-box">
            <div class="box-icon">
               <!-- <i class="fas fa-users"></i> -->
            </div>
            <?php
               $select_users = $conn->prepare("SELECT * FROM `khachhang`");
               $select_users->execute();
               $numbers_of_users = $select_users->rowCount();
            ?>
            <h3><?= $numbers_of_users ?></h3>
            <p>👥 Thành viên hệ thống</p>
            <a href="users_accounts.php" class="btn pulse-effect">
               <i class="fas fa-user-cog"></i> Quản lý người dùng
            </a>
         </div>
      </div>

      <!-- PHẦN BOX THỐNG KÊ -->
      <div class="box-container">
         <!-- Các box thống kê ở đây -->
      </div>

      <!-- PHẦN BIỂU ĐỒ DOANH THU -->
      <section class="chart-section">
         <div class="chart-card">
            <h3 class="chart-header"><i class="fas fa-coins"></i> Doanh Thu 6 Tháng</h3>

            <!-- Nút chọn Tháng / Tuần / Ngày -->
            <div class="chart-controls" style="margin-bottom: 20px;">
               <div class="view-mode">
                  <button class="active" data-period="month">📅 Tháng</button>
                  <button data-period="week">🗓️ Tuần</button>
                  <button data-period="day">📆 Ngày</button>
               </div>
            </div>

            <!-- Canvas biểu đồ -->
            <div class="chart-wrapper">
               <canvas id="revenueChart" style="height: 280px; width: 100%;"></canvas>
            </div>
         </div>

        
            <div class="chart-legend">
               <span class="legend-item"><span class="color-dot orders-dot"></span> Số đơn hàng</span>
            </div>
         </div>

        <?php
$order_stats = $conn->prepare("
    SELECT 
        DATE_FORMAT(NgayThue, '%Y-%m') AS thang,
        COUNT(*) AS total_orders
    FROM phieuthue
    WHERE NgayThue >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
    GROUP BY thang
    ORDER BY thang ASC
");
$order_stats->execute();
$order_data = $order_stats->fetchAll(PDO::FETCH_ASSOC);

$months = [];
$orders = [];
foreach ($order_data as $row) {
    $months[] = date('m/Y', strtotime($row['thang'])); // ví dụ: 04/2025
    $orders[] = $row['total_orders'];
}
?>

       <script>
let columnChart;

function updateColumnChart() {
    const ctx = document.getElementById('columnChart').getContext('2d');

    if (columnChart) {
        columnChart.destroy();
    }

    columnChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?= json_encode($months) ?>,
            datasets: [{
                label: 'Số đơn hàng',
                data: <?= json_encode($orders) ?>,
                backgroundColor: 'rgba(54, 162, 235, 0.6)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1,
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => `${ctx.parsed.y} đơn hàng`
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: { display: true, text: 'Số đơn hàng' },
                    ticks: {
                        stepSize: 1,
                        callback: v => Number.isInteger(v) ? v : ''
                    }
                },
                x: {
                    title: { display: true, text: 'Tháng/Năm' },
                    grid: { display: false }
                }
            }
        }
    });
}

updateColumnChart();
</script>

   
         <script>
         let revenueChart;

         function updateRevenueChart(period = 'month') {
    const labels = ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5'];
    const data = [120, 150, 110, 130, 5, ]; // thay đổi số liệu ở đây

    if (revenueChart) {
        revenueChart.data.labels = labels;
        revenueChart.data.datasets[0].data = data;
        revenueChart.update();
    } else {
        const ctx = document.getElementById('revenueChart').getContext('2d');
        revenueChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Doanh thu',
                    data: data,
                    backgroundColor: 'rgba(239, 195, 244, 0.6)',
                    borderColor: 'rgb(59, 243, 246)',
                    borderWidth: 1,
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            title: ctx => ctx[0].label,
                            label: ctx => `${ctx.parsed.y} đơn hàng`
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            callback: v => Number.isInteger(v) ? v : ''
                        }
                    },
                    x: { grid: { display: false } }
                }
            }
        });
    }
}


         updateRevenueChart('month');

         document.querySelectorAll('.chart-controls .view-mode button').forEach(btn => {
             btn.addEventListener('click', function () {
                 document.querySelectorAll('.chart-controls .view-mode button').forEach(b => b.classList.remove('active'));
                 this.classList.add('active');
                 const period = this.getAttribute('data-period');
                 updateRevenueChart(period);
             });
         });
         </script>
         
<?php

// Kết nối CSDL
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "qlbd";
$conn = mysqli_connect($host, $user, $pass, $dbname);
if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

// 1. Biểu đồ tình trạng băng đĩa
$tinh_trang = ['Trống', 'Đã cho thuê']; // Trạng thái thực có trong bảng bangdia
$counts = [];

foreach ($tinh_trang as $tt) {
    $sql = "SELECT COUNT(*) as total FROM bangdia WHERE Tinhtrang = '$tt'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    $counts[] = $row['total'];
}

// 2. Biểu đồ đơn thuê theo ngày
$fromDate = $_GET['from'] ?? '';
$toDate = $_GET['to'] ?? '';

$whereClause = '';
if (!empty($fromDate) && !empty($toDate)) {
    $whereClause = "WHERE NgayThue BETWEEN '$fromDate' AND '$toDate'";
}

$sql2 = "SELECT DATE_FORMAT(NgayThue, '%Y-%m-%d') as Ngay, COUNT(*) as total
         FROM phieuthue
         $whereClause
         GROUP BY Ngay
         ORDER BY Ngay ASC";

$result2 = mysqli_query($conn, $sql2);

$ngaythue_labels = [];
$ngaythue_counts = [];

while ($row2 = mysqli_fetch_assoc($result2)) {
    $ngaythue_labels[] = $row2['Ngay'];
    $ngaythue_counts[] = $row2['total'];
}
?>


<h2>Biểu đồ tình trạng băng đĩa</h2>
<canvas id="trangThaiChart"></canvas>

<h2>Biểu đồ số lượng đơn thuê theo ngày</h2>
<form method="GET" action="">
    Từ ngày:
    <input type="date" name="from" value="<?php echo htmlspecialchars($fromDate); ?>">
    Đến ngày:
    <input type="date" name="to" value="<?php echo htmlspecialchars($toDate); ?>">
    <button type="submit">Lọc</button>
</form>
<canvas id="ngayThueChart"></canvas>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Biểu đồ 1: Tình trạng băng đĩa
    const trangThaiCtx = document.getElementById('trangThaiChart').getContext('2d');
    const trangThaiChart = new Chart(trangThaiCtx, {
        type: 'bar',
        data: {
            labels: ['Trống', 'Đã cho thuê'],
            datasets: [{
                label: 'Số lượng',
                data: [<?php echo implode(',', $counts); ?>],
                backgroundColor: ['rgba(75, 192, 192, 0.7)', 'rgba(255, 99, 132, 0.7)'],
                borderColor: ['rgba(75, 192, 192, 1)', 'rgba(255, 99, 132, 1)'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: { display: true, text: 'Tình trạng băng đĩa' },
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true, title: { display: true, text: 'Số lượng' } },
                x: { title: { display: true, text: 'Tình trạng' } }
            }
        }
    });

    // Biểu đồ 2: Đơn thuê theo ngày
    const ngayThueCtx = document.getElementById('ngayThueChart').getContext('2d');
    const ngayThueChart = new Chart(ngayThueCtx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($ngaythue_labels); ?>,
            datasets: [{
                label: 'Số đơn thuê',
                data: <?php echo json_encode($ngaythue_counts); ?>,
                fill: false,
                borderColor: 'rgba(54, 162, 235, 1)',
                backgroundColor: 'rgba(54, 162, 235, 0.4)',
                tension: 0.2,
                pointStyle: 'circle',
                pointRadius: 5,
                pointHoverRadius: 7
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: { display: true, text: 'Số lượng đơn thuê theo ngày' }
            },
            scales: {
                y: { beginAtZero: true, title: { display: true, text: 'Số đơn thuê' } },
                x: { title: { display: true, text: 'Ngày' } }
            }
        }
    });
</script>



         <div class="chart-card">
            <h3 class="chart-header"><i class="fas fa-music"></i> Thể Loại Băng Đĩa</h3>
            <div class="chart-wrapper">
               <canvas id="musicChart" style="height: 280px; width: 100%;"></canvas>
            </div>
            <div class="chart-legend">
               <span class="legend-item"><span class="color-dot pop-dot"></span> Nhạc</span>
               <span class="legend-item"><span class="color-dot rock-dot"></span> Phim</span>
            </div>
         </div>
      </section>

      <style>
         /* CHÈN CUỐI CÙNG CỦA PHẦN <style> */
         .chart-controls {
            display: flex;
            justify-content: space-between;
            margin: 20px 0;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 8px;
         }

         .view-mode button {
            padding: 8px 15px;
            margin: 0 5px;
            border: 1px solid #e2e8f0;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s;
         }

         .view-mode button.active {
            background: #3b82f6;
            color: white;
            border-color: #2563eb;
         }

         .stat-item {
            background: #ffffff;
            padding: 10px;
            border-radius: 6px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            margin: 5px 0;
         }
         /* CHART SECTION */
         .chart-section {
            padding: 2rem;
            background: #f8fafc;
            margin-top: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
         }

         .chart-header {
            font-size: 1.1rem;
            color: #2d3748;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
         }

         .chart-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 1.5rem;
            margin-top: 1.5rem;
         }

         .chart-card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
         }

         .chart-legend {
            margin-top: 1rem;
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
         }

         .legend-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
         }

         .color-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
         }

         .revenue-dot {
            background: #3b82f6;
         }

         .pending-dot {
            background: #f59e0b;
         }

         .shipped-dot {
            background: #10b981;
         }

         .pop-dot {
            background: #8b5cf6;
         }

         .rock-dot {
            background: #ef4444;
         }
      </style>

      <script src="../js/admin_script.js"></script>
   </body>
</html>
