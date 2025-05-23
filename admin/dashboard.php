<?php
include '../components/connect.php';
session_start();

if (!isset($_SESSION["user_id"])) {
   header("Location:admin_login");
   exit();
}

// Get current month/year
$current_month = date('m');
$current_year = date('Y');

// 1. Monthly Revenue
$month_revenue = $conn->prepare("SELECT SUM(TongTien) FROM phieuthue WHERE MONTH(NgayThue) = ? AND YEAR(NgayThue) = ?");
$month_revenue->execute([$current_month, $current_year]);
$month_revenue = $month_revenue->fetchColumn() ?? 0;

$total_orders = $conn->query("SELECT COUNT(*) FROM `phieuthue`")->fetchColumn();
$total_products = $conn->query("SELECT COUNT(*) FROM `bangdia`")->fetchColumn();
$total_users = $conn->query("SELECT COUNT(*) FROM `khachhang`")->fetchColumn();

// Revenue Chart Data
$revenue_data = [
    'daily' => $conn->query("
        SELECT 
            DATE(ngaythue) AS period,
            SUM(TongTien) AS tongtien,
            (SELECT IFNULL(SUM(TienTra), 0) FROM phieutra WHERE DATE(NgayTraTT) = DATE(phieuthue.ngaythue)) AS tientra
        FROM phieuthue
        GROUP BY DATE(ngaythue)
        ORDER BY period ASC  -- Thay DESC bằng ASC
        LIMIT 30
    ")->fetchAll(PDO::FETCH_ASSOC),
    
    'monthly' => $conn->query("
        SELECT 
            DATE_FORMAT(ngaythue, '%Y-%m') AS period,
            SUM(TongTien) AS tongtien,
            (SELECT IFNULL(SUM(TienTra), 0) FROM phieutra WHERE DATE_FORMAT(NgayTraTT, '%Y-%m') = DATE_FORMAT(phieuthue.ngaythue, '%Y-%m')) AS tientra
        FROM phieuthue
        GROUP BY DATE_FORMAT(ngaythue, '%Y-%m')
        ORDER BY period ASC  -- Thay DESC bằng ASC
        LIMIT 12
    ")->fetchAll(PDO::FETCH_ASSOC),
    
    'yearly' => $conn->query("
        SELECT 
            YEAR(ngaythue) AS period,
            SUM(TongTien) AS tongtien,
            (SELECT IFNULL(SUM(TienTra), 0) FROM phieutra WHERE YEAR(NgayTraTT) = YEAR(phieuthue.ngaythue)) AS tientra
        FROM phieuthue
        GROUP BY YEAR(ngaythue)
        ORDER BY period ASC  -- Thay DESC bằng ASC
        LIMIT 5
    ")->fetchAll(PDO::FETCH_ASSOC)
];

foreach ($revenue_data as &$time_period) {
    foreach ($time_period as &$row) {
        $row['doanhthu'] = $row['tongtien'] - $row['tientra'];
    }
    unset($row);
}
unset($time_period);

// Product Status Data
$status_data = $conn->query("
    SELECT Tinhtrang, COUNT(*) as total 
    FROM bangdia 
    GROUP BY Tinhtrang
")->fetchAll(PDO::FETCH_ASSOC);

// Genre Distribution Data
$genre_data = $conn->query("
    SELECT Theloai, COUNT(*) as total 
    FROM bangdia 
    GROUP BY Theloai
")->fetchAll(PDO::FETCH_ASSOC);

// Order Stats (last 6 months)
$order_stats = $conn->query("
    SELECT 
        DATE_FORMAT(NgayThue, '%Y-%m') AS thang,
        COUNT(*) AS total_orders
    FROM phieuthue
    WHERE NgayThue >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
    GROUP BY thang
    ORDER BY thang ASC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Trang chủ quản trị - CD House</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="../css/admin_style.css">
   <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
   <style>
    /* === CHUNG CHO TẤT CẢ BIỂU ĐỒ === */
    .chart-section {
        padding: 1.5rem;
        background: white;
        margin: 1.5rem 0;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
    }

    .chart-container {
        width: 100%;
        height: 300px;
        margin: 1rem 0;
        position: relative;
    }

    .chart-wrapper {
        margin-bottom: 2rem;
    }

    .chart-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.2rem;
    }

    .chart-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #2d3748;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .chart-title i {
        color: #4f46e5;
    }

    .chart-controls {
        display: flex;
        justify-content: space-between;
        margin: 1rem 0;
        padding: 0.75rem;
        background: #f8fafc;
        border-radius: 8px;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    .control-group {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .control-group span {
        font-size: 0.9rem;
        color: #4b5563;
    }

    .chart-controls button {
        padding: 0.5rem 1rem;
        border: 1px solid #e2e8f0;
        background: white;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s;
        font-size: 0.9rem;
        color: #4b5563;
    }

    .chart-controls button:hover {
        background: #f1f5f9;
    }

    .chart-controls button.active {
        background: #4f46e5;
        color: white;
        border-color: #4f46e5;
    }

    /* === LAYOUT BIỂU ĐỒ === */
    .chart-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 1.5rem;
        margin-top: 1.5rem;
    }

    .chart-card {
        background: white;
        padding: 1.25rem;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
    }

    .chart-card h3 {
        font-size: 1.1rem;
        margin-bottom: 1rem;
        color: #2d3748;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .chart-card h3 i {
        color: #4f46e5;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .chart-controls {
            flex-direction: column;
        }
        
        .control-group {
            flex-wrap: wrap;
        }
        
        .chart-row {
            grid-template-columns: 1fr;
        }
    }
   </style>
</head>
<body class="dashboard-page">
   <?php include '../components/admin_header.php' ?>

   <section class="dashboard">
      <!-- Các phần box giữ nguyên -->
      <div class="intro-box">
         <a href="intro.php" class="btn pink-btn">
            <i class="fas fa-chevron-circle-right"></i> Hệ thống quản lý của CD HOUSE
         </a>
      </div>

      <div class="box-container">
         <!-- Revenue Box -->
         <div class="box revenue-box">
            <h3><?= number_format($month_revenue, 0, ',', '.') ?> VNĐ</h3>
            <p>💰 Doanh thu tháng <?= $current_month . '/' . $current_year ?></p>
            <a href="revenue.php" class="btn pulse-effect">
               <i class="fas fa-chart-line"></i> Xem chi tiết
            </a>
         </div>
         
         <!-- Orders Box -->
         <div class="box orders-box">
            <h3><?= $total_orders ?></h3>
            <p>📦 Tổng phiếu thuê</p>
            <a href="placed_orders.php" class="btn pulse-effect">
               <i class="fas fa-clipboard-list"></i> Xem đơn hàng
            </a>
         </div>

         <!-- Products Box -->
         <div class="box products-box">
            <h3><?= $total_products ?></h3>
            <p>🎵 Băng đĩa có sẵn</p>
            <a href="products.php" class="btn pulse-effect">
               <i class="fas fa-box-open"></i> Quản lý kho
            </a>
         </div>

         <!-- Users Box -->
         <div class="box users-box">
            <h3><?= $total_users ?></h3>
            <p>👥 Thành viên hệ thống</p>
            <a href="users_accounts.php" class="btn pulse-effect">
               <i class="fas fa-user-cog"></i> Quản lý người dùng
            </a>
         </div>
      </div>
      <!-- Charts Section -->
      <section class="chart-section">
         <!-- Revenue Chart -->
         <div class="chart-wrapper">
            <div class="chart-header">
               <h3 class="chart-title"><i class="fas fa-chart-line"></i> Doanh thu</h3>
               <div class="chart-controls">
                  <div class="time-period">
                     <span>Xem theo:</span>
                     <button class="active" data-period="daily">Ngày</button>
                     <button data-period="monthly">Tháng</button>
                     <button data-period="yearly">Năm</button>
                  </div>
                  
                  <div class="data-type">
                     <span>Hiển thị:</span>
                     <button class="active" data-type="all">Tất cả</button>
                     <button data-type="tongtien">Tổng tiền</button>
                     <button data-type="tientra">Tiền trả</button>
                     <button data-type="doanhthu">Doanh thu</button>
                  </div>
               </div>
            </div>
            <div class="chart-container">
               <canvas id="revenueChart"></canvas>
            </div>
         </div>

         <!-- Các biểu đồ phụ -->
         <div class="chart-row">
            <div class="chart-card">
               <h3><i class="fas fa-info-circle"></i> Tình Trạng Băng Đĩa</h3>
               <div class="chart-container">
                  <canvas id="statusChart"></canvas>
               </div>
            </div>
            
            <div class="chart-card">
               <h3><i class="fas fa-music"></i> Thể Loại Băng Đĩa</h3>
               <div class="chart-container">
                  <canvas id="genreChart"></canvas>
               </div>
            </div>
            
            <div class="chart-card">
               <h3><i class="fas fa-chart-bar"></i> Đơn Hàng</h3>
               <div class="chart-container">
                  <canvas id="ordersChart"></canvas>
               </div>
            </div>
         </div>
      </section>
   </section>

   <script>
   // Revenue Chart
   const rawData = {
    daily: {
        labels: <?= json_encode(array_map(function($d) { 
            return date('d/m', strtotime($d['period'])); 
        }, $revenue_data['daily'])) ?>,
        tongtien: <?= json_encode(array_column($revenue_data['daily'], 'tongtien')) ?>,
        tientra: <?= json_encode(array_column($revenue_data['daily'], 'tientra')) ?>,
        doanhthu: <?= json_encode(array_column($revenue_data['daily'], 'doanhthu')) ?>
    },
    monthly: {
        labels: <?= json_encode(array_map(function($m) { 
            return date('m/Y', strtotime($m['period'].'-01')); 
        }, $revenue_data['monthly'])) ?>,
        tongtien: <?= json_encode(array_column($revenue_data['monthly'], 'tongtien')) ?>,
        tientra: <?= json_encode(array_column($revenue_data['monthly'], 'tientra')) ?>,
        doanhthu: <?= json_encode(array_column($revenue_data['monthly'], 'doanhthu')) ?>
    },
    yearly: {
        labels: <?= json_encode(array_column($revenue_data['yearly'], 'period')) ?>,
        tongtien: <?= json_encode(array_column($revenue_data['yearly'], 'tongtien')) ?>,
        tientra: <?= json_encode(array_column($revenue_data['yearly'], 'tientra')) ?>,
        doanhthu: <?= json_encode(array_column($revenue_data['yearly'], 'doanhthu')) ?>
    }
};

    // Định dạng tiền tệ
    const vndFormatter = new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND',
        maximumFractionDigits: 0
    });

    // Tạo biểu đồ doanh thu
    const revenueCtx = document.getElementById('revenueChart');
    const revenueChart = new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: rawData.daily.labels,
            datasets: [
                {
                    label: 'Tổng tiền',
                    data: rawData.daily.tongtien,
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 3,
                    tension: 0.3,
                    fill: true
                },
                {
                    label: 'Tiền trả',
                    data: rawData.daily.tientra,
                    borderColor: '#ef4444',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    borderWidth: 3,
                    tension: 0.3,
                    fill: true
                },
                {
                    label: 'Doanh thu',
                    data: rawData.daily.doanhthu,
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 3,
                    tension: 0.3,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return `${context.dataset.label}: ${vndFormatter.format(context.raw)}`;
                        }
                    }
                },
                legend: {
                    position: 'top',
                    labels: {
                        boxWidth: 10,
                        padding: 15,
                        usePointStyle: true,
                        pointStyle: 'circle'
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return vndFormatter.format(value);
                        }
                    },
                    grid: {
                        drawBorder: false
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            },
            elements: {
                point: {
                    radius: 3,
                    hoverRadius: 6
                }
            }
        }
    });

    // Xử lý thay đổi khoảng thời gian
    document.addEventListener('DOMContentLoaded', function() {
    // Xử lý thay đổi khoảng thời gian
    const timePeriodButtons = document.querySelectorAll('.time-period button');
    
    timePeriodButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Xóa active class từ tất cả các nút
            timePeriodButtons.forEach(btn => btn.classList.remove('active'));
            
            // Thêm active class cho nút được click
            this.classList.add('active');
            
            const period = this.dataset.period;
            
            // Cập nhật tiêu đề biểu đồ
            document.getElementById('chartTitle').textContent = 
                period === 'daily' ? 'Biểu đồ Doanh thu 30 ngày gần nhất' :
                period === 'monthly' ? 'Biểu đồ Doanh thu 12 tháng gần nhất' :
                'Biểu đồ Doanh thu 5 năm gần nhất';
            
            // Cập nhật dữ liệu biểu đồ
            revenueChart.data.labels = rawData[period].labels;
            revenueChart.data.datasets[0].data = rawData[period].tongtien;
            revenueChart.data.datasets[1].data = rawData[period].tientra;
            revenueChart.data.datasets[2].data = rawData[period].doanhthu;
            
            // Cập nhật biểu đồ
            revenueChart.update();
        });
    });
    const dataTypeButtons = document.querySelectorAll('.data-type button');
    
    dataTypeButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Xóa active class từ tất cả các nút
            dataTypeButtons.forEach(btn => btn.classList.remove('active'));
            
            // Thêm active class cho nút được click
            this.classList.add('active');
            
            const type = this.dataset.type;
            
            // Ẩn/hiện dataset tương ứng
            revenueChart.data.datasets.forEach((dataset, i) => {
                dataset.hidden = !(
                    type === 'all' ||
                    (type === 'tongtien' && i === 0) ||
                    (type === 'tientra' && i === 1) ||
                    (type === 'doanhthu' && i === 2)
                );
            });
            
            // Cập nhật biểu đồ
            revenueChart.update();
        });
    });
});

    // Xử lý thay đổi loại dữ liệu hiển thị
    document.querySelectorAll('.data-type button').forEach(button => {
        button.addEventListener('click', function() {
            document.querySelectorAll('.data-type button').forEach(btn => {
                btn.classList.remove('active');
            });
            this.classList.add('active');
            
            const type = this.dataset.type;
            
            revenueChart.data.datasets.forEach((dataset, i) => {
                if (type === 'all') {
                    dataset.hidden = false;
                } else {
                    dataset.hidden = !(
                        (type === 'tongtien' && i === 0) ||
                        (type === 'tientra' && i === 1) ||
                        (type === 'doanhthu' && i === 2)
                    );
                }
            });
            
            revenueChart.update();
        });
    });

    // Tạo biểu đồ trạng thái
    const statusCtx = document.getElementById('statusChart');
    new Chart(statusCtx, {
        type: 'bar',
        data: {
            labels: <?= json_encode(array_column($status_data, 'Tinhtrang')) ?>,
            datasets: [{
                label: 'Số lượng',
                data: <?= json_encode(array_column($status_data, 'total')) ?>,
                backgroundColor: [
                    'rgba(75, 192, 192, 0.7)', 
                    'rgba(255, 99, 132, 0.7)', 
                    'rgba(255, 206, 86, 0.7)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            }
        }
    });

    // Tạo biểu đồ thể loại
    const genreCtx = document.getElementById('genreChart');
    new Chart(genreCtx, {
        type: 'doughnut',
        data: {
            labels: <?= json_encode(array_column($genre_data, 'Theloai')) ?>,
            datasets: [{
                data: <?= json_encode(array_column($genre_data, 'total')) ?>,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.7)',
                    'rgba(54, 162, 235, 0.7)',
                    'rgba(255, 206, 86, 0.7)',
                    'rgba(75, 192, 192, 0.7)'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });

    // Tạo biểu đồ đơn hàng
    const ordersCtx = document.getElementById('ordersChart');
    new Chart(ordersCtx, {
        type: 'bar',
        data: {
            labels: <?= json_encode(array_map(function($m) { 
                return date('m/Y', strtotime($m['thang'].'-01')); 
            }, $order_stats)) ?>,
            datasets: [{
                label: 'Số đơn hàng',
                data: <?= json_encode(array_column($order_stats, 'total_orders')) ?>,
                backgroundColor: 'rgba(54, 162, 235, 0.6)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1,
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: { 
                    beginAtZero: true, 
                    title: { display: true, text: 'Số đơn hàng' },
                    ticks: {
                        precision: 0
                    }
                },
                x: { title: { display: true, text: 'Tháng/Năm' } }
            }
        }
    });
   </script>
</body>
</html>