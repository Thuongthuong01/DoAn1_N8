<?php
include '../components/connect.php';
session_start();

if (!isset($_SESSION["user_id"])) {
   header("Location: admin_login");
   exit();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Giới Thiệu Cửa Hàng CD HOUSE</title>
  
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<!-- custom css file link  -->
    <link rel="stylesheet" href="../css/admin_style.css">

  <style>
    /* body {
      font-family: Arial, sans-serif;
      font-size: 18px;
      line-height: 1.6;
      padding: 20px;
      background-color: #fdfdfd;
      color: #333;
    } */
/* 
    h2 {
      font-size: 24px;
      margin-top: 30px;
      color: #2c3e50;
    } */

    /* ul {
      padding-left: 20px;
    }
*/
    ul li {
      margin-bottom: 10px;
    }

    /* .btn {
      display: inline-block;
      margin-top: 30px;
      padding: 10px 20px;
      background-color: #3498db;
      color: white;
      text-decoration: none;
      border-radius: 5px;
      font-size: 18px;
    } */

    .btn:hover {
      background-color: #2980b9;
    }

    .store-intro, .rental-rules, .compensation-rules, .late-return-rules {
      margin-bottom: 40px;
    } 
  </style>
</head>
<body>
<?php include '../components/admin_header.php' ?>
<section class="dashboard">
  <!-- Giới thiệu cửa hàng -->
  <div class="rental-rules">
    <h2><i class="fas fa-store"></i> Giới thiệu cửa hàng CD</h2>
    <p>
      CD là một cửa hàng chuyên kinh doanh băng đĩa giải trí hoạt động theo hình thức bán hàng offline, tọa lạc tại địa chỉ:
      <strong>218 Lĩnh Nam - Q.Hoàng Mai - TP Hà Nội</strong>.
    </p>
    <p>
      Chúng tôi chuyên cung cấp đa dạng các loại băng đĩa như:
      phim điện ảnh, phim truyền hình, ca nhạc, trò chơi điện tử và phần mềm.
      Tất cả sản phẩm đều được kiểm tra chất lượng trước khi đưa ra thị trường,
      đảm bảo mang lại trải nghiệm giải trí tốt nhất cho khách hàng.
    </p>
    <p>
      Hệ thống quản lý của cửa hàng được xây dựng nhằm mục tiêu:<br>
      – Tối ưu hóa công tác quản lý kho đĩa,<br>
      – Theo dõi việc thuê và trả đĩa của khách hàng,<br>
      – Thống kê, báo cáo doanh thu vào mỗi cuối tháng để hỗ trợ việc ra quyết định kinh doanh hiệu quả.
    </p>
  </div>

  <!-- Nội quy thuê đĩa -->
  <div class="rental-rules">
    <h2><i class="fas fa-file-contract"></i> Nội Quy Thuê Băng Đĩa</h2>
    <p>
      Để đảm bảo quyền lợi và sự công bằng cho tất cả khách hàng,
      cửa hàng CD ban hành những quy định cụ thể trong quá trình thuê băng đĩa như sau:
    </p>
    <ul>
      <li>📌 Mỗi khách hàng chỉ được thuê tối đa <strong>10 đĩa</strong> trong cùng một thời điểm.<br>
          Việc giới hạn này nhằm đảm bảo nguồn băng đĩa có thể được phục vụ cho nhiều khách hàng khác.</li>
      <li>🪪 Khách hàng cần cung cấp thông tin cá nhân chính xác, bao gồm:<br>
          – Họ tên<br>– Số điện thoại<br>– Địa chỉ<br>
          để phục vụ việc quản lý thuê – trả.</li>
      <li>📅 Thời hạn thuê tiêu chuẩn là <strong>3 ngày</strong>.<br>
          Nếu có nhu cầu thuê dài hơn, vui lòng thông báo trước với nhân viên cửa hàng.</li>
      <li>🔍 Khách hàng được khuyến khích kiểm tra tình trạng đĩa trước khi nhận<br>
          để đảm bảo băng đĩa không có lỗi vật lý hoặc nội dung không phù hợp.</li>
    </ul>
  </div>

  <!-- Quy định đền bù -->
  <div class="compensation-rules">
    <h2><i class="fas fa-exclamation-triangle"></i> Quy Định Đền Bù Băng Đĩa</h2>
    <p>
      Trong trường hợp băng đĩa bị hư hỏng, mất mát hoặc không thể sử dụng lại,
      khách hàng sẽ phải bồi thường theo mức độ thiệt hại như sau:
    </p>
    <ul>
      <li>💔 <strong>Mất hoàn toàn</strong>:<br>
          Khách hàng sẽ phải đền bù <strong>100% giá thuê</strong> của đĩa đó.</li>
      <li>⚠️ <strong>Hỏng nặng</strong> (nứt vỡ, trầy sâu, cong vênh, không thể phát được):<br>
          Đền bù <strong>50% giá thuê</strong>.</li>
      <li>🔧 <strong>Trầy xước nhẹ</strong> nhưng vẫn sử dụng được:<br>
          Đền bù <strong>30% giá thuê</strong> nhằm phục vụ công tác bảo dưỡng và xử lý kỹ thuật.</li>
      <li>✅ <strong>Đĩa còn tốt</strong>:<br>
          Không yêu cầu đền bù, miễn là đĩa được trả đúng thời hạn.</li>
    </ul>
    <p>
      <strong>Lưu ý:</strong> Mức giá thuê được tính theo hệ thống quản lý và có thể thay đổi theo từng loại đĩa (phim mới, bản đặc biệt, v.v.).
    </p>
  </div>

  <!-- Quy định trả muộn -->
  <div class="late-return-rules">
    <h2><i class="fas fa-clock"></i> Quy Định Trả Muộn</h2>
    <p>
      Việc trả băng đĩa đúng hạn không chỉ giúp hệ thống vận hành trơn tru mà còn là sự tôn trọng đối với các khách hàng khác.
      Cửa hàng áp dụng chính sách tính phí trả muộn như sau:
    </p>
    <ul>
      <li>⏳ Với mỗi ngày trả trễ, khách hàng sẽ phải trả thêm <strong>5% giá thuê</strong> của mỗi đĩa.</li>
      <li>📅 Thời gian trả được tính từ ngày thuê đến thời điểm thực tế trả lại.<br>
          Cửa hàng có thể linh động trong các trường hợp khách có lý do chính đáng.</li>
      <li>🧾 Mọi khoản phí trả muộn sẽ được cộng dồn vào hóa đơn cuối cùng và cần thanh toán ngay khi trả đĩa.</li>
    </ul>
    <p>
      Để tránh phát sinh chi phí không đáng có, quý khách vui lòng theo dõi lịch trả và chủ động liên hệ nếu có thay đổi.
    </p>
  </div>

  <a href="dashboard.php" class="btn">← Quay lại trang chủ</a>
</section>
</body>
</html>

<!-- Style cho quy trình quản lý -->
<style>
/* 
.management-process {
   background: #fff;
   padding: 2rem;
   border-radius: 15px;
   box-shadow: 0 4px 20px rgba(0,0,0,0.08);
   margin-top: 2rem;
}

.process-step {
   display: flex;
   gap: 1.5rem;
   padding: 1.5rem;
   margin: 1.5rem 0;
   background: #f8f9fa;
   border-radius: 10px;
   align-items: center;
}

.step-icon {
   font-size: 2.5rem;
   color: #3498db;
   min-width: 60px;
   text-align: center;
}

.process-step h3 {
   color: #2c3e50;
   margin-bottom: 0.5rem;
}

.process-step ul {
   list-style: none;
   padding-left: 0;
}

.process-step li {
   padding: 0.3rem 0;
   display: flex;
   align-items: center;
   gap: 0.5rem;
} */
</style>

<script src="../js/admin_script.js"></script>
</body>
</html>