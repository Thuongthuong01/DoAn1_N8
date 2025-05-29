<?php
include '../components/connect.php';
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: admin_login");
    exit();
}
date_default_timezone_set('Asia/Ho_Chi_Minh');
if (!isset($_GET['loai']) || !isset($_GET['id'])) {
    die("Thiếu tham số.");
}

$loai = $_GET['loai'];
$id = $_GET['id'];

switch ($loai) {
    case 'thue':
        // Lấy phiếu thuê và chi tiết
        $stmt = $conn->prepare("
            SELECT pt.MaThue, pt.NgayThue, pt.NgayTraDK, pt.TongTien,
                   kh.TenKH, kh.SDT,kh.MaKH,
                   qt.TenAD
            FROM phieuthue pt
            JOIN khachhang kh ON pt.MaKH = kh.MaKH
            LEFT JOIN quantri qt ON pt.MaAD = qt.MaAD
            WHERE pt.MaThue = ?
        ");
        $stmt->execute([$id]);
        $phieu = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$phieu) die("Không tìm thấy phiếu thuê.");
        
        $stmt_ct = $conn->prepare("
            SELECT bd.TenBD, ct.MaBD, ct.SoLuong, ct.DonGia 
            FROM chitietphieuthue ct
            JOIN bangdia bd ON ct.MaBD = bd.MaBD
            WHERE ct.MaThue = ?
        ");
        $stmt_ct->execute([$id]);
        $chiTiet = $stmt_ct->fetchAll(PDO::FETCH_ASSOC);
        break;
    
    case 'nhap':
        // Lấy phiếu nhập và chi tiết
        $stmt = $conn->prepare("
            SELECT pn.MaPhieu, pn.MaNCC,cc.TenNCC, pn.NgayNhap, pn.SoLuong, pn.TongTien, qt.TenAD
            FROM phieunhap pn
            LEFT JOIN quantri qt ON pn.MaAD = qt.MaAD
            LEFT JOIN nhacc cc ON pn.MaNCC= cc.MaNCC
            WHERE pn.MaPhieu = ?
        ");
        $stmt->execute([$id]);
        $phieu = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$phieu) die("Không tìm thấy phiếu nhập.");
        
        $stmt_ct = $conn->prepare("SELECT MaBD, GiaGoc FROM chitietphieunhap WHERE MaPhieu = ?");
        $stmt_ct->execute([$id]);
        $chiTiet = $stmt_ct->fetchAll(PDO::FETCH_ASSOC);
        break;
    
    case 'tra':
        // Lấy phiếu trả
        $stmt = $conn->prepare("
            SELECT pt.MaTra, pt.MaKH,pt.MaThue, pt.NgayTraTT, pt.ChatLuong, pt.TraMuon, pt.TienPhat, pt.TienTra, pt.MaAD, kh.TenKH,kh.SDT ,qt.TenAD AS TenNguoiNhap
            FROM phieutra pt
            LEFT JOIN khachhang kh ON pt.MaKH = kh.MaKH
            LEFT JOIN quantri qt ON pt.MaAD = qt.MaAD

            WHERE pt.MaTra = ?
        ");
        $stmt->execute([$id]);

        $phieu = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$phieu) die("Không tìm thấy phiếu trả.");
        $maThue = $phieu['MaThue'];
        $stmt_ct = $conn->prepare("
            SELECT bd.TenBD, ct.MaBD, ct.SoLuong, ct.DonGia 
            FROM chitietphieuthue ct
            JOIN bangdia bd ON ct.MaBD = bd.MaBD
            WHERE ct.MaThue = ?
        ");
        $stmt_ct->execute([$maThue]);
        $chiTiet = $stmt_ct->fetchAll(PDO::FETCH_ASSOC);
        break;
        break;
    
    default:
        die("Loại phiếu không hợp lệ.");
}

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Hóa đơn chi tiết</title>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 20px;
        }
        .receipt-container {
            max-width: 800px;
            margin: 0 auto;
            border: 1px solid #ccc;
            padding: 20px;
            border-radius: 5px;
        }
        .receipt-header {
            text-align: left;
            margin: 30px 0;
        }
        .store-name {
            font-weight: bold;
            color: #000000;
            margin-bottom: 5px;
            font-size: 28px;
        }
        .store-address {
            font-size: 16px;
            color: #555;
            margin-bottom: 10px;
        }
        .receipt-info {
            margin-bottom: 20px;
        }
        .receipt-info p {
            margin: 5px 0;
        }
        .receipt-title {
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        
        .note {
            text-align: center;
            font-style: italic;
            margin-top: 20px;
            color: #444;
        }
        .print-button {
            text-align: center;
        }
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin: 15px 10px;
            
        }
        .btn-print {
            background-color:rgb(51, 88, 209);
            color: white;
            
        }
        .btn-print:hover {
            background-color:rgb(42, 46, 157);
        }
        .btn-secondary {
            background-color: #6c757d;
            color: white;
            text-decoration: none;
        }
        .btn-secondary:hover {
            background-color: #5c636a;
        }
        @media print {
            .print-button {
                display: none;
            }
            .qr-code {
                display: block !important;
            }
            canvas, img {
                display: block !important;
                max-width: 100%;
                margin: 0 auto;
            }
            .btn-print {
    display: none;
  }
        }
        

    </style>
</head>
<body>

<div class="receipt-container">
        <div class="receipt-header">
            <div class="store-name">CD House</div>
            <div class="store-address">Lĩnh Nam - Hoàng Mai - Hà Nội</div>
        </div>
<?php if ($loai == 'thue'): ?>
    <div class="receipt-title">HOÁ ĐƠN THUÊ #<?= htmlspecialchars($id) ?></div>
    <div class="receipt-info">
    <p><strong>Ngày in hóa đơn: </strong>  <?= date('d/m/Y H:i:s') ?></p>
    <p><strong>Khách hàng: </strong> <?= htmlspecialchars($phieu['MaKH']) ?> - <?= htmlspecialchars($phieu['TenKH']) ?> (SĐT: <?= htmlspecialchars($phieu['SDT']) ?>)</p>
    <p><strong>Ngày thuê: </strong><?php echo date('d/m/Y', strtotime($phieu['NgayThue'])); ?> | <strong>Ngày trả dự kiến: </strong><?php echo date('d/m/Y', strtotime($phieu['NgayTraDK'])); ?></p>
    <p><strong>Người nhập phiếu thuê: </strong> <?= htmlspecialchars($phieu['TenAD']) ?></p>
    </div>
    <table>
        <thead>
            <tr>
                <th>Mã băng đĩa</th>
                <th>Tên băng đĩa</th>
                <th>Số lượng</th>
                <th>Đơn giá (VNĐ)</th>
                <th>Thành tiền (VNĐ)</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $tong = 0;
            foreach ($chiTiet as $item):
                $thanhTien = $item['SoLuong'] * $item['DonGia'];
                $tong += $thanhTien;
            ?>
            <tr>
                <td><?= htmlspecialchars($item['MaBD']) ?></td>
                <td><?= htmlspecialchars($item['TenBD']) ?></td>
                <td><?= $item['SoLuong'] ?></td>
                <td><?= number_format($item['DonGia'], 0, ',', '.') ?></td>
                <td><?= number_format($thanhTien, 0, ',', '.') ?></td>
            </tr>
            <?php endforeach; ?>
            <tr class="total">
                <th colspan="5"></th>
            </tr>
            <tr class="total">
                <td colspan="4">Tổng giá băng đĩa</td>
                <td><?= number_format($tong, 0, ',', '.') ?></td>
            </tr>
            <tr class="total">
                <td colspan="4">Tiền cọc cố định</td>
                <td>50,000</td>
            </tr>
            <tr class="total">
                <td colspan="4" style="font-weight: bold;">Tổng thanh toán</td>
                <td><?= number_format($phieu['TongTien'], 0, ',', '.') ?></td>
            </tr>
        </tbody>
    </table>

<?php elseif ($loai == 'nhap'): ?>
    <div class="receipt-title">HOÁ ĐƠN NHẬP #<?= htmlspecialchars($id) ?></div>
    <div class="receipt-info">
    <p><strong>Ngày in hóa đơn: </strong><?= date('d/m/Y H:i:s') ?></p>
    <p><strong>Nhà cung cấp: </strong> <?= htmlspecialchars($phieu['MaNCC']) ?> - <?= htmlspecialchars($phieu['TenNCC']) ?></p>
    <p><strong>Ngày nhập: </strong><?php echo date('d/m/Y', strtotime($phieu['NgayNhap'])); ?></p>
    <p><strong>Người nhập: </strong> <?= htmlspecialchars($phieu['TenAD']) ?></p>
</div>
    <table>
        <thead>
            <tr>
                <th>Mã băng đĩa</th>
                <th>Giá gốc (VNĐ)</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($chiTiet as $ct): ?>
            <tr>
                <td><?= htmlspecialchars($ct['MaBD']) ?></td>
                <td><?= number_format($ct['GiaGoc'], 0, ',', '.') ?></td>
            </tr><?php endforeach; ?>
            <tr class="total">
                <th colspan="2"></th>
            </tr>
            <tr class="total">
                <td colspan="1" style="font-weight: bold;">Tổng thanh toán</td>
                <td><?= number_format($phieu['TongTien'], 0, ',', '.') ?></td>
            </tr>
            
        </tbody>
    </table>

<?php elseif ($loai == 'tra'): ?>
    <div class="receipt-title">HOÁ ĐƠN TRẢ #<?= htmlspecialchars($id) ?></div>
<div class="receipt-info">
    <p><strong>Ngày in hóa đơn: </strong><?= date('d/m/Y H:i:s') ?></p>
    <p><strong>Khách hàng: </strong> <?= htmlspecialchars($phieu['MaKH']) ?> - <?= htmlspecialchars($phieu['TenKH']) ?> (SĐT: <?= htmlspecialchars($phieu['SDT']) ?>)</p>
    <p><strong>Ngày trả: </strong><?php echo date('d/m/Y', strtotime($phieu['NgayTraTT'])); ?></p>
    <p><strong>Người nhập: </strong> <?= htmlspecialchars($phieu['TenNguoiNhap'] ?: 'Không xác định') ?></p>
</div>
    <table>
        <thead>
            <tr>
                <th>Mã băng đĩa</th>
                <th>Tên băng đĩa</th>
                <th>Đơn giá (VNĐ)</th>
                <th>Chất lượng</th>
                <th>Trả muộn (Ngày)</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $phatchatluong = 0;
            $phattramuon=0;
            
            foreach ($chiTiet as $tra): 
            $thanhTien = $phieu['TraMuon'] *0.05* $tra['DonGia'];
            $phattramuon += $thanhTien;
            $donGia = $tra['DonGia'];
            $chatLuong = strtolower(trim($phieu['ChatLuong']));
            $tienPhatCL = 0;

            switch ($chatLuong) {
                case 'trầy xước':
                    $tienPhatCL = 0.3 * $donGia;
                    break;
                case 'hỏng nặng':
                    $tienPhatCL = 0.5 * $donGia;
                    break;
                case 'mất':
                    $tienPhatCL = 1.0 * $donGia;
                    break;
                default: // "Tốt" hoặc không xác định thì không phạt
                    $tienPhatCL = 0;
            }

            $phatchatluong += $tienPhatCL;
            $tongphat=$phatchatluong+$phattramuon;

            ?>

            <tr>
                <td><?= htmlspecialchars($tra['MaBD']) ?></td>
                <td><?= htmlspecialchars($tra['TenBD']) ?></td>
                <td><?= number_format($tra['DonGia'], 0, ',', '.') ?></td>
                <td><?= htmlspecialchars($phieu['ChatLuong']) ?></td>
                <td><?= htmlspecialchars($phieu['TraMuon']) ?></td>
            </tr><?php endforeach; ?> 
            <tr class="total">
                <th colspan="5"></th>
            </tr>
        <tr class="total">
            <th colspan="3"></th>
            <th colspan="2">Thành tiền (VNĐ)</th>
        </tr>      
            <tr class="total">
            <td colspan="3">Tổng tiền phạt chất lượng</td>
            <td colspan="2"><?= number_format($phatchatluong, 0, ',', '.') ?></td>
        </tr>
        
            <tr class="total">
                <td colspan="3">Tiền phạt trả muộn</td>
                <td colspan="3"><?= number_format($phattramuon, 0, ',', '.') ?></td>
            </tr>
            <tr class="total">
                <td colspan="3">Tổng tiền phạt</td>
                <td colspan="2"><?= number_format($tongphat, 0, ',', '.') ?></td>
        </tr>
        <tr class="total">
                <td colspan="3" style="font-weight: bold;">Tổng tiền trả</td>
                <td colspan="2"><?= number_format($phieu['TienTra'], 0, ',', '.') ?></td>
        </tr>
            
        </tbody>
    </table>

<?php endif; ?>
        <div class="note">
            Cảm ơn quý khách đã tin tưởng và sử dụng dịch vụ. Hẹn gặp lại!
        </div>
        <div class="print-button">
        <button onclick="window.print()" class="btn btn-print" >In hóa đơn</button>
        </div>
    
        </div>

</body>
</html>
