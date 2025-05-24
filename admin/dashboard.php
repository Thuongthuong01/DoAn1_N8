<?php
include '../components/connect.php';
session_start();

// Kiểm tra đăng nhập
if (!isset($_SESSION["user_id"])) {
    header("Location:admin_login");
    exit();
}

// Kiểm tra kết nối cơ sở dữ liệu
if (!$conn) {
    die('Database connection failed');
}

// Get current month/year
$current_month = date('m'); // Chuỗi '05'
$current_year = date('Y');  // Chuỗi '2025'

// 1. Monthly Revenue
try {
    $month_revenue = $conn->prepare("SELECT SUM(TongTien) FROM phieuthue WHERE MONTH(NgayThue) = ? AND YEAR(NgayThue) = ?");
    $month_revenue->execute([$current_month, $current_year]);
    $month_revenue = $month_revenue->fetchColumn() ?? 0;
} catch (PDOException $e) {
    echo "Error fetching revenue: " . $e->getMessage();
    $month_revenue = 0;
}

try {
    $total_orders = $conn->query("SELECT COUNT(*) FROM `phieuthue`")->fetchColumn();
    $total_products = $conn->query("SELECT COUNT(*) FROM `bangdia`")->fetchColumn();
    $total_users = $conn->query("SELECT COUNT(*) FROM `khachhang`")->fetchColumn();
} catch (PDOException $e) {
    echo "Error fetching stats: " . $e->getMessage();
    $total_orders = $total_products = $total_users = 0;
}

// Revenue Chart Data
try {
    // Tạo dữ liệu đầy đủ cho 30 ngày, 12 tháng, 5 năm
    $revenue_data = [
        'daily' => [],
        'monthly' => [],
        'yearly' => []
    ];

    // Daily: 30 ngày gần nhất
    $daily_data = $conn->query("
        SELECT 
            DATE(pt.NgayThue) AS period,
            SUM(pt.TongTien) AS tongtien,
            IFNULL(SUM(-pr.TienTra), 0) AS tientra
        FROM phieuthue pt
        LEFT JOIN phieutra pr ON DATE(pr.NgayTraTT) = DATE(pt.NgayThue)
        WHERE pt.NgayThue >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
        GROUP BY DATE(pt.NgayThue)
        ORDER BY period ASC
    ")->fetchAll(PDO::FETCH_ASSOC);

    // Tạo mảng 30 ngày gần nhất
    $daily_template = [];
    for ($i = 29; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $daily_template[$date] = ['period' => $date, 'tongtien' => 0, 'tientra' => 0, 'doanhthu' => 0];
    }
    foreach ($daily_data as $row) {
        $daily_template[$row['period']] = $row;
    }
    $revenue_data['daily'] = array_values($daily_template);

    // Monthly: 12 tháng trong năm 2025
    $monthly_data = $conn->query("
        SELECT 
            DATE_FORMAT(pt.NgayThue, '%Y-%m') AS period,
            SUM(pt.TongTien) AS tongtien,
            IFNULL(SUM(-pr.TienTra), 0) AS tientra
        FROM phieuthue pt
        LEFT JOIN phieutra pr ON DATE_FORMAT(pr.NgayTraTT, '%Y-%m') = DATE_FORMAT(pt.NgayThue, '%Y-%m')
        WHERE YEAR(pt.NgayThue) = 2025
        GROUP BY DATE_FORMAT(pt.NgayThue, '%Y-%m')
        ORDER BY period ASC
    ")->fetchAll(PDO::FETCH_ASSOC);

    // Tạo mảng 12 tháng cho năm 2025
    $monthly_template = [];
    for ($i = 1; $i <= 12; $i++) {
        $month = sprintf('2025-%02d', $i);
        $monthly_template[$month] = ['period' => $month, 'tongtien' => 0, 'tientra' => 0, 'doanhthu' => 0];
    }
    foreach ($monthly_data as $row) {
        $monthly_template[$row['period']] = $row;
    }
    $revenue_data['monthly'] = array_values($monthly_template);

    // Yearly: 5 năm gần nhất (2021-2025)
    $yearly_data = $conn->query("
        SELECT 
            YEAR(pt.NgayThue) AS period,
            SUM(pt.TongTien) AS tongtien,
            IFNULL(SUM(-pr.TienTra), 0) AS tientra
        FROM phieuthue pt
        LEFT JOIN phieutra pr ON YEAR(pr.NgayTraTT) = YEAR(pt.NgayThue)
        WHERE YEAR(pt.NgayThue) BETWEEN 2023 AND 2025
        GROUP BY YEAR(pt.NgayThue)
        ORDER BY period ASC
    ")->fetchAll(PDO::FETCH_ASSOC);

    // Tạo mảng 5 năm (2021-2025)
    $yearly_template = [];
    for ($i = 2023; $i <= 2025; $i++) {
        $yearly_template[$i] = ['period' => (string)$i, 'tongtien' => 0, 'tientra' => 0, 'doanhthu' => 0];
    }
    foreach ($yearly_data as $row) {
        $yearly_template[$row['period']] = $row;
    }
    $revenue_data['yearly'] = array_values($yearly_template);

    // Tính doanh thu
    foreach (['daily', 'monthly', 'yearly'] as $period) {
        foreach ($revenue_data[$period] as &$row) {
            $row['doanhthu'] = $row['tongtien'] + $row['tientra'];
        }
        unset($row);
    }
} catch (PDOException $e) {
    echo "Error fetching revenue data: " . $e->getMessage();
    $revenue_data = [
        'daily' => array_fill(0, 30, ['period' => date('Y-m-d'), 'tongtien' => 0, 'tientra' => 0, 'doanhthu' => 0]),
        'monthly' => array_fill(0, 12, ['period' => '2025-01', 'tongtien' => 0, 'tientra' => 0, 'doanhthu' => 0]),
        'yearly' => array_fill(0, 3, ['period' => '2023', 'tongtien' => 0, 'tientra' => 0, 'doanhthu' => 0])
    ];
}

// Product Status Data
try {
    $status_data = $conn->query("
        SELECT Tinhtrang, COUNT(*) as total 
        FROM bangdia 
        GROUP BY Tinhtrang
    ")->fetchAll(PDO::FETCH_ASSOC);
    if (empty($status_data)) {
        $status_data = [['Tinhtrang' => 'Không có dữ liệu', 'total' => 0]];
    }
} catch (PDOException $e) {
    echo "Error fetching status data: " . $e->getMessage();
    $status_data = [['Tinhtrang' => 'Không có dữ liệu', 'total' => 0]];
}

// Genre Distribution Data
try {
    $genre_data = $conn->query("
        SELECT Theloai, COUNT(*) as total 
        FROM bangdia 
        GROUP BY Theloai
    ")->fetchAll(PDO::FETCH_ASSOC);
    if (empty($genre_data)) {
        $genre_data = [['Theloai' => 'Không có dữ liệu', 'total' => 0]];
    }
} catch (PDOException $e) {
    echo "Error fetching genre data: " . $e->getMessage();
    $genre_data = [['Theloai' => 'Không có dữ liệu', 'total' => 0]];
}

// Order Stats (last 6 months)
try {
    $order_stats = $conn->query("
        SELECT 
            DATE_FORMAT(NgayThue, '%Y-%m') AS thang,
            COUNT(*) AS total_orders
        FROM phieuthue
        WHERE NgayThue >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
        GROUP BY thang
        ORDER BY thang ASC
    ")->fetchAll(PDO::FETCH_ASSOC);
    if (empty($order_stats)) {
        $order_stats = [['thang' => date('Y-m'), 'total_orders' => 0]];
    }
} catch (PDOException $e) {
    echo "Error fetching order stats: " . $e->getMessage();
    $order_stats = [['thang' => date('Y-m'), 'total_orders' => 0]];
}
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
            max-height: 50vh;
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
                        <div class="control-group time-period">
                            <span>Xem theo:</span>
                            <button class="active" data-period="daily">Ngày</button>
                            <button data-period="monthly">Tháng</button>
                            <button data-period="yearly">Năm</button>
                        </div>
                    </div>
                    <div class="chart-controls">   
                        <div class="control-group data-type">
                            <span>Hiển thị:</span>
                            <button class="active" data-type="all">Tất cả</button>
                            <button data-type="tongtien">Tổng thu
                            </button>
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
        // Revenue Chart Data
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

        // Hàm sinh màu động
        function generateColors(count) {
            const colors = [];
            const baseColors = [
                'rgba(255, 99, 132, 0.7)',
                'rgba(54, 162, 235, 0.7)',
                'rgba(255, 206, 86, 0.7)',
                'rgba(75, 192, 192, 0.7)',
                'rgba(153, 102, 255, 0.7)'
            ];
            for (let i = 0; i < count; i++) {
                colors.push(baseColors[i % baseColors.length]);
            }
            return colors;
        }

        // Tạo biểu đồ doanh thu
        const revenueCtx = document.getElementById('revenueChart');
        const revenueChart = new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: rawData.daily.labels,
                datasets: [
                    {
                        label: 'Tổng thu',
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
                            title: function(context) {
                                return `Thời gian: ${context[0].label}`;
                            },
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

        // Xử lý thay đổi biểu đồ
        document.addEventListener('DOMContentLoaded', function() {
            const updateChart = (period = 'daily', type = 'all') => {
                revenueChart.data.labels = rawData[period].labels;
                revenueChart.data.datasets.forEach((dataset, i) => {
                    dataset.data = rawData[period][['tongtien', 'tientra', 'doanhthu'][i]];
                    dataset.hidden = type !== 'all' && i !== {tongtien: 0, tientra: 1, doanhthu: 2}[type];
                });
                revenueChart.update();
            };

            document.querySelectorAll('.time-period button, .data-type button').forEach(button => {
                button.addEventListener('click', function() {
                    const group = this.parentElement.classList.contains('time-period') ? 'time-period' : 'data-type';
                    this.parentElement.querySelectorAll('button').forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');

                    const period = document.querySelector('.time-period button.active').dataset.period;
                    const type = document.querySelector('.data-type button.active').dataset.type;
                    updateChart(period, type);
                });
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
                    backgroundColor: generateColors(<?= count($status_data) ?>),
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
                    backgroundColor: generateColors(<?= count($genre_data) ?>),
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
                        ticks: { precision: 0 }
                    },
                    x: { title: { display: true, text: 'Tháng/Năm' } }
                }
            }
        });
    </script>
</body>
</html>