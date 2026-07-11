<?php
define("IN_SITE", true);
require_once(__DIR__."/../../core/config.php");

header('Content-Type: application/json');

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['message'])) {
    $msg = mb_strtolower(trim($_POST['message']), 'UTF-8');
    
    // Giả lập độ trễ suy nghĩ của AI (0.5s - 1.5s)
    usleep(rand(500000, 1500000));
    
    $reply = "";

    // Heuristics logic (Anh thiên Openclaw persona)
    if(strpos($msg, 'chào') !== false || strpos($msg, 'hello') !== false || strpos($msg, 'hi') !== false) {
        $reply = "Chào bạn! Tui là Anh thiên Openclaw nè. Bạn cần tui hỗ trợ mua hàng Điện Máy, đặt In 3D hay Gọi Thợ sửa chữa tới nhà?";
    } 
    elseif(strpos($msg, 'giá') !== false || strpos($msg, 'bao nhiêu') !== false || strpos($msg, 'tiền') !== false) {
        $reply = "Anh thiên báo giá luôn cho nóng: Tất cả sản phẩm Điện Máy và In 3D đều có giá niêm yết trên web. Riêng khoản gọi thợ: Vệ sinh máy lạnh 150k, sửa chữa thì thợ tới kiểm tra rồi báo giá minh bạch. Bạn ưng thì thợ làm nha!";
    }
    elseif(strpos($msg, 'mua') !== false || strpos($msg, 'đặt hàng') !== false || strpos($msg, 'giỏ hàng') !== false) {
        $reply = "Quá dễ! Bạn cứ bấm 'Thêm vào giỏ' ở các sản phẩm bạn thích, sau đó bấm vào nút Giỏ Hàng góc trên bên phải màn hình để thanh toán nha. Anh thiên sẽ báo đơn cho Giám đốc Hiếu duyệt lẹ lắm!";
    }
    elseif(strpos($msg, '3d') !== false || strpos($msg, 'in') !== false || strpos($msg, 'mô hình') !== false) {
        $reply = "À há, mảng In 3D là đỉnh của chóp! Giám đốc Hiếu có dàn máy xịn, bạn chỉ việc chọn mô hình trên web, hoặc nhắn Zalo file .STL qua, Anh thiên sẽ tính giá theo gram nhựa. Rẻ bèo mà đẹp nức nách!";
    }
    elseif(strpos($msg, 'thợ') !== false || strpos($msg, 'sửa') !== false || strpos($msg, 'hư') !== false || strpos($msg, 'bảo hành') !== false) {
        $reply = "Máy hư hả? Chuyện nhỏ! Bạn lướt lên phần 'GỌI THỢ NGAY' điền thông tin vô form giùm Anh thiên. Trong vòng 15-30 phút là có thợ phóng xe máy tới nhà liền! Bán kính 15km quanh Lấp Vò bao tốc độ!";
    }
    elseif(strpos($msg, 'hỗ trợ') !== false || strpos($msg, 'tư vấn') !== false || strpos($msg, 'admin') !== false) {
        $reply = "Nếu cần tư vấn sâu hơn, bạn cứ gọi trực tiếp số Hotline 0939.354.937, Giám đốc Hiếu sẽ nhấc máy tiếp bạn luôn. Tui là AI nên tư vấn vòng ngoài thui hè hè.";
    }
    elseif(strpos($msg, 'anh thiên') !== false || strpos($msg, 'openclaw') !== false) {
        $reply = "Bạn gọi tui hả? Anh thiên Openclaw luôn sẵn sàng phục vụ! Tui được code bằng công nghệ đỉnh cao để tự động chốt sale thay Giám đốc Hiếu đó. Đẳng cấp chưa?";
    }
    else {
        // Fallback ngẫu nhiên
        $fallbacks = [
            "Anh thiên nghe nè. Cơ mà tui chưa hiểu ý bạn lắm. Bạn nói rõ hơn xíu được không?",
            "Vụ này tui hơi lú. Bạn gọi thẳng Hotline 0939.354.937 cho Giám đốc Hiếu giùm tui nha!",
            "Chà chà, câu này khó! Anh thiên chỉ chuyên tư vấn Điện máy, In 3D và Gọi thợ thôi. Bạn hỏi trúng tủ giùm tui nghen.",
            "Tui hiểu sơ sơ, nhưng để chắc ăn bạn cứ bỏ hàng vô giỏ rồi đặt đi, hệ thống sẽ có người gọi lại xác nhận liền!"
        ];
        $reply = $fallbacks[array_rand($fallbacks)];
    }

    echo json_encode(['status' => 'success', 'reply' => $reply]);
    exit;
}
?>
