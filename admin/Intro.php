
<?php
include '../components/connect.php';
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: admin_login");
    exit();
}
date_default_timezone_set('Asia/Ho_Chi_Minh');
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Giới Thiệu CD HOUSE</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <link rel="stylesheet" href="../css/admin_style.css" />
    <style>
        .intro-section {
    background: #fff;
    padding: 30px;
    border-radius: 12px;
    margin-top: 30px;
    font-size: 18px;
    line-height: 1.9;
    color: #333;
}
        .intro-section h2 {
    font-size: 24px;
    color: #2c3e50;
    margin-top: 30px;
}
        .intro-section ul {
    margin-left: 20px;
    font-size: 20px;
}
    
.dashboard-header {
  text-align: center;
  display: flex;
  flex-direction: column;
  align-items: center;
  margin-bottom: 5px;
  font-size: 28px;
}

</style>
</head>
<body style="background: #f0f5ff;">

<?php include '../components/admin_header.php'; ?>

<section class="dashboard">
  <div class="dashboard-header">
     <h1 class="gradient-text">📀 Thông tin về CD HOUSE</h1>
     <p class="current-date">📅 <?= date('d/m/Y') ?></p>
  </div>

  <div class="box-container">
    <div class="intro-section">
      <h2>📌 Giới thiệu cửa hàng CD</h2>
      <p>
        CD là một cửa hàng chuyên kinh doanh băng đĩa giải trí hoạt động theo hình thức bán hàng offline, tọa lạc tại địa chỉ: <strong>218 Lĩnh Nam - Q.Hoàng Mai - TP Hà Nội</strong>.
      </p>
      <p>
        Chúng tôi chuyên cung cấp đa dạng các loại băng đĩa như: phim điện ảnh, phim truyền hình, ca nhạc, trò chơi điện tử và phần mềm.
        Tất cả sản phẩm đều được kiểm tra chất lượng trước khi đưa ra thị trường, đảm bảo mang lại trải nghiệm giải trí tốt nhất cho khách hàng.
      </p>
      <p>
        Hệ thống quản lý của cửa hàng được xây dựng nhằm mục tiêu:<br>
        – Tối ưu hóa công tác quản lý kho đĩa,<br>
        – Theo dõi việc thuê và trả đĩa của khách hàng,<br>
        – Thống kê, báo cáo doanh thu vào mỗi cuối tháng để hỗ trợ việc ra quyết định kinh doanh hiệu quả.
      </p>

      <h2>📜 Nội Quy Thuê Băng Đĩa</h2>
      <p>
        Để đảm bảo quyền lợi và sự công bằng cho tất cả khách hàng, cửa hàng CD ban hành những quy định cụ thể trong quá trình thuê băng đĩa như sau:
      </p>
      <ul>
        <li>Mỗi khách hàng chỉ được thuê tối đa <strong>10 đĩa</strong> trong cùng một thời điểm.</li>
        <li>Việc giới hạn này nhằm đảm bảo nguồn băng đĩa có thể được phục vụ cho nhiều khách hàng khác.</li>
        <li>Khách hàng cần cung cấp thông tin cá nhân chính xác, bao gồm: Họ tên, Số điện thoại, Địa chỉ.</li>
        <li>Thời hạn thuê tiêu chuẩn là <strong>3 ngày</strong>. Nếu có nhu cầu thuê dài hơn, vui lòng thông báo trước với nhân viên cửa hàng.</li>
        <li>Khách hàng được khuyến khích kiểm tra tình trạng đĩa trước khi nhận.</li>
      </ul>

      <h2>⚠️ Quy Định Đền Bù Băng Đĩa</h2>
      <p>Trong trường hợp băng đĩa bị hư hỏng, mất mát hoặc không thể sử dụng lại, khách hàng sẽ phải bồi thường theo mức độ thiệt hại như sau:</p>
      <ul>
        <li><strong>Mất hoàn toàn:</strong> Đền bù 100% giá thuê của đĩa đó.</li>
        <li><strong>Hỏng nặng:</strong> Đền bù 50% giá thuê.</li>
        <li><strong>Trầy xước nhẹ:</strong> Đền bù 30% giá thuê để phục vụ xử lý kỹ thuật.</li>
        <li><strong>Đĩa còn tốt:</strong> Không yêu cầu đền bù nếu trả đúng hạn.</li>
      </ul>
      <p><strong>Lưu ý:</strong> Mức giá thuê có thể thay đổi theo từng loại đĩa (phim mới, bản đặc biệt...)</p>

      <h2>⏰ Quy Định Trả Muộn</h2>
      <p>Việc trả đĩa đúng hạn giúp hệ thống vận hành trơn tru và tôn trọng khách hàng khác:</p>
      <ul>
        <li>Với mỗi ngày trả trễ, khách hàng trả thêm <strong>5% giá thuê</strong>.</li>
        <li>Thời gian trả tính từ ngày thuê đến thời điểm thực tế.</li>
        <li>Hóa đơn trả muộn cần thanh toán ngay khi hoàn trả đĩa.</li>
        <li>Hệ thống có thể linh động nếu có lý do chính đáng.</li>
      </ul>
    </div>
  </div>
</section>

<script src="../js/admin_script.js"></script>
</body>
</html>
