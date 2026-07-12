<?php
define("IN_SITE", true);
require_once(__DIR__."/../../core/config.php");
require_once(__DIR__."/../../core/function.php");

if (empty($getUser) || ($getUser['level'] !== 'admin' && $getUser['level'] !== 'bct')) {
    die("Access denied");
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 1;

$titles = [
    1 => "Chính sách bảo mật (*)",
    2 => "Phương thức tiếp nhận và giải quyết phản ánh, yêu cầu, khiếu nại (*)",
    3 => "Chính sách giá (*)",
    4 => "Chính sách về thanh toán (*)",
    5 => "Các điều kiện hoặc hạn chế trong việc cung cấp hàng hóa hoặc dịch vụ trên nền tảng (*)",
    6 => "Chính sách giao hàng, đổi trả và hoàn tiền (áp dụng cho hàng hóa) hoặc phương thức cung cấp dịch vụ, chính sách chấm dứt dịch vụ và hoàn tiền (áp dụng cho dịch vụ) (*)",
    7 => "Hình thức hỗ trợ trực tuyến"
];

$title = $titles[$id] ?? "Tài liệu";

$body = "";
switch ($id) {
    case 1:
        $body = "
        <p><strong>a. Mục đích thu thập & Định danh:</strong> Việc thu thập dữ liệu trên website Điện Máy Hiếu bao gồm: Tên, email, số điện thoại, địa chỉ khách hàng. Theo quy định mới, các tài khoản Đối tác (Thợ kỹ thuật) bắt buộc phải thực hiện định danh qua hệ thống VNeID hoặc đối chiếu CCCD nhằm đảm bảo an toàn tuyệt đối cho khách hàng khi giao dịch tại nhà.</p>
        <p><strong>b. Phạm vi sử dụng:</strong> Hệ thống sử dụng thông tin khách hàng cung cấp để liên hệ xác nhận đơn hàng, điều phối thợ kỹ thuật, gửi thông báo và giải quyết khiếu nại.</p>
        <p><strong>c. Quyền của Chủ thể dữ liệu:</strong> Khách hàng có toàn quyền yêu cầu Điện Máy Hiếu cung cấp bản sao dữ liệu cá nhân, yêu cầu chỉnh sửa, hoặc rút lại sự đồng ý thu thập dữ liệu và yêu cầu xóa bỏ hoàn toàn tài khoản bằng cách liên hệ CSKH.</p>
        <p><strong>d. Cam kết bảo mật:</strong> Không sử dụng, không chuyển giao hay tiết lộ cho bên thứ 3 khi không có sự cho phép, trừ trường hợp cơ quan pháp luật yêu cầu.</p>";
        break;
    case 2:
        $body = "
        <p>Điện Máy Hiếu hoạt động dưới mô hình \"Nền tảng TMĐT tích hợp\" và chịu trách nhiệm liên đới trong việc bảo vệ quyền lợi người tiêu dùng theo Luật Bảo vệ quyền lợi người tiêu dùng 2023 và NĐ 248/2026/NĐ-CP.</p>
        <p><strong>Quy trình 3 bước giải quyết khiếu nại:</strong></p>
        <ul>
            <li><strong>Bước 1 (Tiếp nhận):</strong> Khách hàng phản ánh qua Hotline: 0979.553.289 hoặc Chatbot Anh thiên Openclaw trên website.</li>
            <li><strong>Bước 2 (Xử lý):</strong> Bộ phận CSKH xác minh trong vòng 24 giờ. Nếu thiệt hại phát sinh do lỗi hàng hóa hoặc hành vi của thợ thuộc hệ thống quản lý, Điện Máy Hiếu cam kết đứng ra bồi thường và xử lý triệt để.</li>
            <li><strong>Bước 3 (Khắc phục):</strong> Liên hệ lại khách hàng để đưa ra phương án đền bù, đổi trả hoặc cử thợ khác đến khắc phục hoàn toàn miễn phí (áp dụng trong 48 giờ).</li>
        </ul>";
        break;
    case 3:
        $body = "
        <p>Chúng tôi cam kết tính minh bạch tuyệt đối về giá cả đối với toàn bộ hàng hóa và dịch vụ trên nền tảng.</p>
        <ul>
            <li><strong>Giá Sản Phẩm (Hàng hóa & In 3D):</strong> Giá niêm yết trên website là giá cuối cùng đã bao gồm Thuế Giá trị gia tăng (VAT). Giá này chưa bao gồm phí vận chuyển (nếu có).</li>
            <li><strong>Giá Dịch Vụ Gọi Thợ:</strong> Bảng giá dịch vụ gọi thợ là giá tiền công trọn gói cho một hạng mục cơ bản.</li>
            <li><strong>Phát sinh linh kiện:</strong> Trong trường hợp sửa chữa cần thay thế linh kiện, vật tư, Thợ kỹ thuật bắt buộc phải báo giá cụ thể cho khách hàng và chỉ được tiến hành sửa chữa khi khách hàng đồng ý. Khách hàng có quyền từ chối nếu thấy giá vật tư không hợp lý mà không phải trả bất kỳ khoản phí khảo sát nào.</li>
        </ul>";
        break;
    case 4:
        $body = "
        <p>Nhằm mang đến sự tiện lợi tối đa cho quý khách, Điện Máy Hiếu áp dụng 2 hình thức thanh toán linh hoạt, an toàn và có đầy đủ chứng từ:</p>
        <ol>
            <li><strong>Thanh toán Tiền Mặt (COD):</strong> Khách hàng thanh toán trực tiếp cho Thợ Kỹ Thuật sau khi đã nghiệm thu công việc sửa chữa hoàn tất, hoặc thanh toán cho Shipper khi nhận hàng.</li>
            <li><strong>Chuyển khoản Ngân Hàng (Mã QR):</strong> Chuyển khoản qua mã VietQR hoặc Internet Banking vào tài khoản công ty. Áp dụng cho các đơn hàng lớn hoặc khách hàng thanh toán từ xa.</li>
        </ol>";
        break;
    case 5:
        $body = "
        <p>Để đảm bảo tuân thủ pháp luật và chất lượng dịch vụ, chúng tôi áp dụng các quy định sau:</p>
        <ul>
            <li><strong>Giới hạn địa lý (Vùng phục vụ):</strong> Dịch vụ Gọi Thợ tận nơi hiện tại chỉ áp dụng trong phạm vi Bán kính 15km tính từ Chợ Lấp Vò, Tỉnh Đồng Tháp. Các đơn hàng nằm ngoài khu vực này, hệ thống sẽ tự động từ chối hoặc thỏa thuận phụ thu phí di chuyển.</li>
            <li><strong>Tư vấn Tự động (AI Chatbot):</strong> Chatbot \"Anh thiên Openclaw\" sử dụng trí tuệ nhân tạo để hỗ trợ nhanh 24/7. Các thông tin tư vấn kỹ thuật từ Chatbot mang tính chất tham khảo. Trong trường hợp phức tạp, quyết định cuối cùng phải dựa trên khảo sát thực tế của Thợ có chuyên môn.</li>
            <li><strong>Hạn chế độ tuổi:</strong> Người mua hàng và đặt lịch yêu cầu dịch vụ phải từ đủ 15 tuổi trở lên. Trẻ em dưới 15 tuổi cần có sự giám sát của người lớn khi thợ đến làm việc tại nhà.</li>
        </ul>";
        break;
    case 6:
        $body = "
        <p>Chúng tôi luôn nỗ lực mang đến sự an tâm tuyệt đối khi khách hàng sử dụng dịch vụ và mua sắm.</p>
        <p><strong>Giao hàng (Hàng hóa):</strong> Thời gian giao hàng chuẩn trong khu vực là 2-4 giờ kể từ lúc chốt đơn. Các sản phẩm In 3D cần thời gian chế tác sẽ được hẹn cụ thể (từ 1-3 ngày). Phí vận chuyển áp dụng biểu phí tiêu chuẩn của các đơn vị GHTK, Viettel Post.</p>
        <p><strong>Đổi Trả & Hoàn Tiền:</strong><br>
        - Sản phẩm vật lý: 1 đổi 1 trong vòng 7 ngày nếu có lỗi do nhà sản xuất. Sản phẩm đổi trả phải còn nguyên tem, mác, không bị rơi vỡ hay vào nước.<br>
        - Dịch vụ sửa chữa: Bảo hành linh kiện thay thế theo tiêu chuẩn của hãng (thường từ 1-6 tháng tùy linh kiện). Hoàn tiền 100% nếu thợ sửa không khắc phục được lỗi như đã cam kết ban đầu.</p>";
        break;
    case 7:
        $body = "
        <p>Điện Máy Hiếu ứng dụng công nghệ đa kênh để hỗ trợ khách hàng nhanh nhất có thể:</p>
        <ul>
            <li><strong>Chatbot Trí Tuệ Nhân Tạo (Anh thiên Openclaw):</strong> Hoạt động 24/7 ngay trên góc phải màn hình website, giải đáp các thắc mắc cơ bản về dịch vụ và hướng dẫn sử dụng.</li>
            <li><strong>Hotline hỗ trợ khẩn cấp:</strong> 0979.553.289 (Hoạt động từ 07:30 đến 18:00). Dành cho các khiếu nại, phản ánh chất lượng hoặc cần điều thợ gấp.</li>
            <li><strong>Zalo OA / Facebook Messenger:</strong> Tích hợp liên kết tại chân trang, giúp khách hàng gửi hình ảnh, video tình trạng hỏng hóc của thiết bị để thợ chẩn đoán từ xa trước khi đến.</li>
        </ul>";
        break;
}

$filename = "Hoso_BCT_" . $id . ".doc";

header("Content-Type: application/vnd.ms-word");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("content-disposition: attachment;filename=" . $filename);

echo "
<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:w='urn:schemas-microsoft-com:office:word' xmlns='http://www.w3.org/TR/REC-html40'>
<head>
<meta charset='utf-8'>
<style>
    body { font-family: 'Times New Roman', serif; font-size: 14pt; line-height: 1.5; margin: 0; padding: 0; }
    .header-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
    .header-table td { vertical-align: top; text-align: center; }
    .agency-name { font-weight: bold; text-transform: uppercase; font-size: 13pt; }
    .republic { font-weight: bold; text-transform: uppercase; font-size: 13pt; }
    .independence { font-weight: bold; font-size: 14pt; }
    .date-line { font-style: italic; font-size: 14pt; font-weight: normal; margin-top: 5px; }
    h1 { font-size: 15pt; text-align: center; text-transform: uppercase; margin-top: 30px; margin-bottom: 20px; font-weight: bold; }
    p { margin: 0 0 10px 0; text-align: justify; text-indent: 1.27cm; }
    ul, ol { margin-top: 0; margin-bottom: 10px; text-align: justify; }
    li { margin-bottom: 5px; }
    .signature-table { width: 100%; border-collapse: collapse; margin-top: 30px; page-break-inside: avoid; }
    .signature-table td { vertical-align: top; text-align: center; width: 50%; }
    .sign-title { font-weight: bold; text-transform: uppercase; font-size: 14pt; }
    .sign-sub { font-style: italic; font-size: 13pt; font-weight: normal; }
</style>
</head>
<body>
    <table class='header-table'>
        <tr>
            <td style='width: 40%;'>
                <div class='agency-name'>CÔNG TY TNHH MTV<br>ĐIỆN TỬ HIẾU</div>
                <hr style='width: 40%; border: 0; border-top: 1px solid black; margin-top: 2px; margin-bottom: 0;'>
            </td>
            <td style='width: 60%;'>
                <div class='republic'>CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM</div>
                <div class='independence'>Độc lập - Tự do - Hạnh phúc</div>
                <hr style='width: 40%; border: 0; border-top: 1px solid black; margin-top: 2px; margin-bottom: 5px;'>
                <div class='date-line'>Đồng Tháp, ngày ...... tháng ...... năm 202...</div>
            </td>
        </tr>
    </table>
    
    <h1>" . $title . "</h1>
    
    <div style='text-align: justify;'>
        " . $body . "
    </div>
    
    <table class='signature-table'>
        <tr>
            <td></td>
            <td>
                <div class='sign-title'>GIÁM ĐỐC</div>
                <div class='sign-sub'>(Ký, ghi rõ họ tên và đóng dấu)</div>
                <br><br><br><br><br>
                <div style='font-weight: bold;'>.......................................</div>
            </td>
        </tr>
    </table>
</body>
</html>
";
?>
