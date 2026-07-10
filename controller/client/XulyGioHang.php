<?php
/*MÃ NGUỒN NÀY ĐƯỢC PHÁT TRIỂN BỞI TUANORI - ZALO: 0812665001*/
define("IN_SITE", true);
require_once("../../core/config.php");
require_once("../../core/function.php");

if($TUANORI->get_row(" SELECT * FROM `blockip` WHERE `ip` = '".myip()."' ")) {
    msg_error2('Bạn đã bị chặn sử dụng tính năng của chúng tôi vĩnh viễn. Xin cảm ơn');
}
if(empty($_COOKIE['token']))
{
    msg_error2('Vui lòng đăng nhập để tiếp tục');
}

if(isset($_SESSION['muacode'])) {
    if($_SESSION['muacode'] > time())
    {
        $s = $_SESSION['muacode'] - time();
        msg_error2("Vui lòng chờ  ".$s."s để thao tác tiếp");
    }
}
if($getUser['total_money'] >= 0) {
    $mgg = check_string($_POST['mgg']);
    if($TUANORI->num_rows(" SELECT * FROM `giohang` WHERE `username` = '".$getUser['username']."' ") < 1)
    {
        msg_error2("Bạn không có sản phẩm để thanh toán");
    }
    $sotien = $TUANORI->get_row("SELECT SUM(`sotien`) FROM `giohang` WHERE `username` = '".$getUser['username']."' ")['SUM(`sotien`)'];
    $tongsp = $TUANORI->num_rows(" SELECT * FROM `giohang` WHERE `username` = '".$getUser['username']."' ");
    $tongspcophi = $TUANORI->num_rows(" SELECT * FROM `giohang` WHERE `username` = '".$getUser['username']."' AND `sotien` > 0 ");
    if($tongsp <=0) msg_error2("Sản phẩm không có!");
    if($mgg) {
        if($TUANORI->site('sukien') == 'ON' && $TUANORI->site('ptgiamgia') > 0) {
            msg_error2("Đang có sự kiện giảm giá, không thể sử dụng mã.");
        }
        if($sotien == 0)
        {
            msg_error2("Code miễn phí, không áp dụng mã giảm giá");
        }
        $check2 = $TUANORI->get_row(" SELECT * FROM `magiamgia` WHERE `magiamgia` = '$mgg'");
        if(!$check2)
        {
            msg_error2("Mã giảm giá chưa đúng");
        }
        else if($check2['theloai'] != 'muacode')
        {
            msg_error2("Mã giảm giá này không sử dụng để mua code");
        }
        else if($check2['conlai'] < $tongspcophi)
        {
            msg_error2("Lưu ý, bạn đang có $tongspcophi sản phẩm có phí, nên phải cần mã giảm giá  phải có lượt dùng còn lại từ $tongspcophi trở lên ");
        }
        else if($mgg === $check2['magiamgia'])
        {
            // $sotien = $sotien - $sotien*$check2['giambaonhieu']/100;
            /*XỬ LÝ GIẢM GIÁ TỪNG ĐƠN HÀNG*/
            foreach($TUANORI->get_list(" SELECT * FROM `giohang` WHERE `username` = '".$getUser['username']."' ORDER BY id DESC") as $ok) {
                $sotien+= $ok['sotien'] - $ok['sotien'] * $check2['giambaonhieu']/100;
            }
        }
        else
        {
            msg_error2("Mã giảm giá chưa đúng. Hãy kiểm tra lại");
        }
    }

    /*XỬ LÝ NẾU CÓ TỒN TẠI SỰ KIỆN GIẢM GIÁ*/
    if($TUANORI->site('sukien') == 'ON' && $TUANORI->site('ptgiamgia') > 0) {
        $sotien = 0;
        foreach($TUANORI->get_list(" SELECT * FROM `giohang` WHERE `username` = '".$getUser['username']."' ORDER BY id DESC") as $ok) {
            $sotien+= $ok['sotien'] - $ok['sotien'] * $TUANORI->site('ptgiamgia')/100;
        }
        $mgg = 'SỰ KIỆN';
    }


    if($my_money < $sotien)
    {
        $napthem = $sotien - $my_money;
        msg_error2("Số tiền bạn không đủ để thực hiện. Vui lòng nạp thêm ".format_cash($napthem)."đ");
    }
    else
    {
        if($sotien > 0)
        {
            $create = $TUANORI->insert("biendongsodu", [
                'username'      => $getUser['username'],
                'truoc'         => $my_money,
                'sau'           => $my_money - $sotien,
                'note'          => 'Thanh toán giỏ hàng, tổng '.$tongsp.' sản phẩm với giá '.format_cash($sotien).' đ',
                'tongtien'      => $sotien,
                'time'          => gettime()
            ]);
        }
        else
        {
            $create = true;
        }
        if($create)
        {
            $isMoney = $TUANORI->tru("users", "money", $sotien, " `tokenlog` = '".$_COOKIE['token']."'");
            if($isMoney)
            {
                if($mgg)
                {
                    $mgg2 = $TUANORI->tru("magiamgia", "conlai", $tongspcophi, " `magiamgia` = '$mgg'");
                    $TUANORI->cong("magiamgia", "dasudung", $tongspcophi, " `magiamgia` = '$mgg'");
                    if(!$mgg2)
                    {
                        msg_error2("Lỗi cấu hình CSDL rồi");
                    }
                }
                if($tongsp > 1) {
                    $magd = strtoupper(substr(randomtoken(), 0 , 7));
                    foreach($TUANORI->get_list(" SELECT * FROM `giohang` WHERE `username` = '".$getUser['username']."' ORDER BY id DESC") as $row){
                        $TUANORI->cong("danhsachmuacode", "luottai", 1, " `id` = '".$row['id_code']."'");
                        
                        $TUANORI->insert("lichsumuacode2", [
                            'username'  => $getUser['username'],
                            'magd'      => $magd,
                            'id_code'   => $row['id_code'],
                            'tongtien'  => $row['sotien'],
                            'thoigian'  => gettime(),
                            'magiamgia' => $mgg,
                            'time'      => time()
                        ]);
                    }
                    $macodene = $magd;
                }
                else
                {
                    $check = $TUANORI->get_row(" SELECT * FROM `giohang` WHERE `username` = '".$getUser['username']."' LIMIT 1");
                    $macodene = $check['id_code'];
                    
                }
                $TUANORI->remove("giohang", " `username` = '".$getUser['username']."' ");
                $_SESSION['muacode'] = time() + 15*$tongsp; // 15s mua được 1 lần code
                if($tongsp > 1) 
                {
                    $TUANORI->insert("lichsumuacode", [
                        'username'          => $getUser['username'],
                        'magd'              => $macodene,
                        'id_code'           => $macodene,
                        'magiamgia'         => $mgg,
                        'tongtien'          => $sotien,
                        'time'              => gettime(),
                        'time2'             => time(),
                    ]);
                        msg_success("Đã thanh toán giỏ hàng thành công với giá ".format_cash($sotien)."đ. Chờ chúng tôi chuyển hướng đến trang tải xuống.", BASE_URL('don-hang/'.$magd), 1000);
                }
                else
                {
                    $TUANORI->insert("lichsumuacode", [
                        'username'          => $getUser['username'],
                        'id_code'           => $macodene,
                        'magiamgia'         => $mgg,
                        'tongtien'          => $sotien,
                        'time'              => gettime(),
                        'time2'             => time()
                    ]);
                    msg_success("Đã thanh toán giỏ hàng thành công với giá ".format_cash($sotien)."đ", BASE_URL('History-mua-code'), 1000);
                }
            }
        }
        else
        {
            msg_error2("Lỗi cấu hình CSDL rồi");
        }
        
    }
    
}
else
{
    msg_error2("Số tiền thanh toán không hợp lệ!");
}