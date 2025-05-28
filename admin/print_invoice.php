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
        body { font-family: Arial, sans-serif; margin: auto; width:85%; }
        table { border-collapse: collapse; width: 85%; margin-top: 20px; margin:auto;}
        th, td { border: 2px solid rgb(52, 50, 50); padding: 8px; text-align: left; }
        th { background-color:rgb(179, 178, 178); }
        .total { font-weight: bold; }
        h1 {text-align: center;}
        p {text-align: left;width: 85%;margin:auto;padding:5px;}
        .btn-print { padding: 6px 12px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 4px; margin-top: 10px;margin-left:45%; cursor: pointer;font-size:1.2rem; }
        @media print {
  .btn-print {
    display: none;
  }
}

    </style>
</head>
<body>

<?php if ($loai == 'thue'): ?>
    <h1>Hóa đơn phiếu thuê #<?= htmlspecialchars($id) ?></h1>
    <p>Ngày in hóa đơn: <?= date('d/m/Y H:i:s') ?></p>
    <p><strong>Khách hàng:</strong> <?= htmlspecialchars($phieu['MaKH']) ?> - <?= htmlspecialchars($phieu['TenKH']) ?> (SĐT: <?= htmlspecialchars($phieu['SDT']) ?>)</p>
    <p><strong>Ngày thuê:</strong><?php echo date('d/m/Y', strtotime($phieu['NgayThue'])); ?> | <strong>Ngày trả dự kiến:</strong><?php echo date('d/m/Y', strtotime($phieu['NgayTraDK'])); ?></p>
    <p><strong>Người nhập phiếu thuê:</strong> <?= htmlspecialchars($phieu['TenAD']) ?></p>

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
                <td colspan="4">Tổng đơn giá băng đĩa</td>
                <td><?= number_format($tong, 0, ',', '.') ?></td>
            </tr>
            <tr class="total">
                <td colspan="4">Tiền cọc cố định</td>
                <td>50,000</td>
            </tr>
            <tr class="total">
                <td colspan="4">Tổng thanh toán</td>
                <td><?= number_format($phieu['TongTien'], 0, ',', '.') ?></td>
            </tr>
        </tbody>
    </table>

<?php elseif ($loai == 'nhap'): ?>
    <h1>Hóa đơn phiếu nhập #<?= htmlspecialchars($id) ?></h1>
    <p>Ngày in hóa đơn: <?= date('d/m/Y H:i:s') ?></p>
    <p><strong>Nhà cung cấp:</strong> <?= htmlspecialchars($phieu['MaNCC']) ?> - <?= htmlspecialchars($phieu['TenNCC']) ?></p>
    <p><strong>Ngày nhập:</strong><?php echo date('d/m/Y', strtotime($phieu['NgayNhap'])); ?></p>
    <p><strong>Người nhập:</strong> <?= htmlspecialchars($phieu['TenAD']) ?></p>

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
                <td colspan="1">Tổng thanh toán</td>
                <td><?= number_format($phieu['TongTien'], 0, ',', '.') ?></td>
            </tr>
            
        </tbody>
    </table>

<?php elseif ($loai == 'tra'): ?>
    <h1>Hóa đơn phiếu trả #<?= htmlspecialchars($id) ?></h1>
    <p>Ngày in hóa đơn: <?= date('d/m/Y H:i:s') ?></p>
    <p><strong>Khách hàng:</strong> <?= htmlspecialchars($phieu['MaKH']) ?> - <?= htmlspecialchars($phieu['TenKH']) ?> (SĐT: <?= htmlspecialchars($phieu['SDT']) ?>)</p>
    <p><strong>Ngày trả:</strong><?php echo date('d/m/Y', strtotime($phieu['NgayTraTT'])); ?></p>
    <p><strong>Người nhập:</strong> <?= htmlspecialchars($phieu['TenNguoiNhap'] ?: 'Không xác định') ?></p>

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
                <td colspan="3">Tiền phạt trả Muộn</td>
                <td colspan="3"><?= number_format($phattramuon, 0, ',', '.') ?></td>
            </tr>
            <tr class="total">
                <td colspan="3">Tổng tiền phạt</td>
                <td colspan="2"><?= number_format($tongphat, 0, ',', '.') ?></td>
        </tr>
        <tr class="total">
                <td colspan="3">Tiền trả</td>
                <td colspan="2"><?= number_format($phieu['TienTra'], 0, ',', '.') ?></td>
        </tr>
            
        </tbody>
    </table>

<?php endif; ?>

<button onclick="window.print()" class="btn-print">In hóa đơn</button>

</body>
</html>
