<?php

define("IN_SITE", true);
require_once("../../core/config.php");
require_once("../../core/function.php");
require_once('../../class/class.smtp.php');
require_once('../../class/PHPMailerAutoload.php');
require_once('../../class/class.phpmailer.php');
if(isset($_POST['type']))
{
    if($DMH->get_row(" SELECT * FROM `blockip` WHERE `ip` = '".myip()."' "))
    {
        msg_error2('Bạn đã bị chặn sử dụng tính năng của chúng tôi vĩnh viễn. Xin cảm ơn');
    }
    /*Xử lý tạo web */
    if($_POST['type'] == 'Taoweb')
    {
        if(empty($_COOKIE['token']))
        {
            msg_error2('Vui lòng đăng nhập để tạo shop');
        }
        $id             = check_string($_POST['id']);
        $tk             = check_string($_POST['taikhoan']);
        $mk             = check_string($_POST['matkhau']);
        $mien           = check_string($_POST['tenmien']);
        // $time           = check_string($_POST['timemua']);
        $magiamgia      = check_string($_POST['magiamgia']);
        if(!$tk || !$mk || !$mien)
        {
            msg_error2("Vui lòng không để trống thông tin");
        }
        if(check_username($tk) != 'True')
        {
            msg_error2("Tên tài khoản không hợp lệ (Không chứa kí tự lạ)");
        }
        if(strlen($tk) < 7)
        {
            msg_error2("Vì an toàn cho bạn. Tài khoản nên đặt trên 7 kí tự");
        }
        if(strlen($mk) < 7)
        {
            msg_error2("Vì an toàn cho bạn. Mật khẩu nên đặt trên 7 kí tự");
        }
        if(check_username($mk) != 'True')
        {
            msg_error2("Mật khẩu không hợp lệ (Không chứa kí tự lạ)");
        }
        $tuan = explode('.',$mien);
        if(!check_domain($tuan)) {
            msg_error2("Tên miền bạn nhập không hợp lệ.");
        }
        if($DMH->get_row(" SELECT * FROM `lichsutaoweb` WHERE `tenmien` = '$mien' AND `buoc` = '4'"))
        {
            msg_error2("Tên miền bạn nhập đã tồn tại trong hệ thống");
        }
        $check = $DMH->get_row(" SELECT * FROM `danhsachtaoweb` WHERE `id` = '$id' AND `hienthi` = 'SHOW'");
        if($DMH->site('sukien') == 'ON' && $DMH->site('ptgiamgiaweb') > 0) {
            $sotien = ($check['money'] - ($check['money'] * $DMH->site('ptgiamgiaweb') / 100)) ;
        } else {
            $sotien = $check['money'] ;
        }
        $mgg22 = false;
        // thêm phần xử lý mua website
        if($magiamgia) {
            if($DMH->site('sukien') == 'ON'  && $DMH->site('ptgiamgiaweb') >= 1) {
                msg_error2("Không thể áp dụng mã giảm giá. Do đang có dự kiện giảm giá từ trước.");
            }
            $magg = $DMH->get_row(" SELECT * FROM `magiamgia` WHERE `magiamgia` = '$magiamgia'");
            if(!$magg)
            {
                if($magiamgia === strtoupper(substr(md5($getUser['timereg2']), 0 , 7)))
                {
                    if(($getUser['timereg2'] + 259200) < time())
                    {
                        msg_error2("Mã giảm giá này đã hết hạn");
                    }
                    $sotien = $sotien - ($sotien*10/100);
                }
                else
                {
                    msg_error2("Mã giảm giá không tồn tại trong hệ thống");
                }
            }

            // tồn tại mã gg 
            else
            {
                if($magg['conlai'] <= 0)
                {
                    msg_error2("Mã giảm giá để hết lượt sử dụng");
                }
                if($magg['theloai'] != 'taoweb')
                {
                    msg_error2("Mã giảm giá không sử dụng cho dịch vụ này");
                }
                if($magg['giambaonhieu'] > 100)
                {
                    msg_error2("Mã giảm giá này đã tạm bị khóa");
                }
                $mgg22 = true;
                $sotien = $sotien - ($sotien*$magg['giambaonhieu']/100);
            }
        }
        else
        {
            if($DMH->site('sukien') == 'OFF') {
                $magiamgia = NULL;
            } else if($DMH->site('ptgiamgiaweb') > 0) {
                $magiamgia = 'Giảm '.$DMH->site('ptgiamgiaweb').'% do sự kiện';
            }
        }
        if ($sotien < 1)
        {
            msg_error2("Số tiền không hợp lệ");
        }
        if($my_money < $sotien) {
            $napthem = $sotien - $my_money;
            msg_error2("Số tiền bạn không đủ để thực hiện thanh toán (Vui lòng nạp thêm ".format_cash($napthem)."đ vào tài khoản)");
        }
        if($check['status'] == 'OFF') {
            msg_error2("Phải rất tiếc thông báo. Mẫu website này chúng tôi không còn hỗ trợ tạo nữa. Bạn vui lòng chọn mẫu khác.");
        }
        if($DMH->site('sukien') == 'OFF') {
            if($magiamgia) {
                if($mgg22) {
                    $mgg2 = $DMH->tru("magiamgia", "conlai", 1, " `magiamgia` = '$magiamgia'");
                    $DMH->cong("magiamgia", "dasudung", 1, " `magiamgia` = '$magiamgia'");
                    if(!$mgg2)
                    {
                        msg_error2("Lỗi cấu hình CSDL rồi");
                    }
                }
            }
        }
        
        $hethan = time() + ($time*86400);
        $create = $DMH->insert("lichsutaoweb", [
            'username'          => $getUser['username'],
            'tenmien'           => $mien,
            'id_code'           => $id,
            'ngaytao'           => time(),
            'taikhoan'          => $tk,
            'matkhau'           => $mk,
            'moneygiahan'       => $DMH->site('tiengiahan'),
            'tongtien'          => $sotien,
            'magiamgia'         => $magiamgia,
            'buoc'              => 1,
            'type'              => 'LOGIN'
        ]);
        $add = $DMH->insert("biendongsodu", [
            'username'      => $getUser['username'],
            'truoc'         => $my_money,
            'sau'           => $my_money - $sotien,
            'note'          => 'Tạo trang web mã #'.$id.' giá '.format_cash($sotien).' đ',
            'tongtien'      => $sotien,
            'time'          => gettime()
        ]);
        $isMoney = $DMH->tru("users", "money", $sotien, " `tokenlog` = '".$_COOKIE['token']."'");
        if($create && $isMoney && $add) {
            msg_success('Bạn đã đặt tạo web thành công! Vui lòng chờ xử lý', BASE_URL('History-tao-web'), 1000);
        } else {
            msg_error2("Lỗi cấu hình CSDL rồi");
        }
    }

    /*xử lý mua code */
    if($_POST['type'] == 'Muacode')
    {
        if(empty($_COOKIE['token'])) {
            msg_error2('Vui lòng đăng nhập để tiếp tục');
        }
        if(isset($_SESSION['muacode'])) {
            if($_SESSION['muacode'] > time()) {
                $s = $_SESSION['muacode'] - time();
                msg_error2("Vui lòng chờ  ".$s."s để thao tác tiếp");
            }
        }
        $mgg    = check_string($_POST['magiamgia']);
        $id     = check_string($_POST['id']);
        $check = $DMH->get_row(" SELECT * FROM `danhsachmuacode` WHERE `id` = '$id' AND `hienthi` = 'SHOW'");
        if(!$check)
        {
            msg_error2("Code này không tồn tại hoặc đã bị ẩn");
        }
        if($check['statusmua'] != 'ON')
        {
            msg_error2("Mã nguồn hiện đang tạm ngưng");
        }
        $sotien = $check['money']; // TIỀN THANH TOÁN
        if($mgg)
        {
            if($DMH->site('sukien') == 'ON' && $DMH->site('ptgiamgia') > 0) {
                msg_error2("Đang có sự kiện giảm giá, không thể sử dụng mã.");
            }
            if($check['money'] == 0)
            {
                msg_error2("Code miễn phí, không áp dụng mã giảm giá");
            }
            $check2 = $DMH->get_row(" SELECT * FROM `magiamgia` WHERE `magiamgia` = '$mgg'");
            if(!$check2)
            {
                msg_error2("Mã giảm giá chưa đúng");
            }
            else if($check2['theloai'] != 'muacode')
            {
                msg_error2("Mã giảm giá này không sử dụng để mua code");
            }
            else if($check2['conlai'] <= 0)
            {
                msg_error2("Mã giảm giá này đã sử dụng hết lượt");
            }
            else if($mgg === $check2['magiamgia'])
            {
                $sotien = $sotien - $sotien*$check2['giambaonhieu']/100;
            }
            else
            {
                msg_error2("Mã giảm giá chưa đúng. Hãy kiểm tra lại");
            }
        }
        if($DMH->site('sukien') == 'ON' && $DMH->site('ptgiamgia') > 0) {
            $sotien -=$sotien *$DMH->site('ptgiamgia') / 100;
            $mgg = 'Giảm '.$DMH->site('ptgiamgia').'% do sự kiện';

        }
        if ($sotien < 0) {
            msg_error2("Số tiền không hợp lệ");
        }
        if($my_money < $sotien) {
            $napthem = $sotien - $my_money;
            msg_error2("Số tiền bạn không đủ để thực hiện. Vui lòng nạp thêm ".format_cash($napthem)."đ");
        }
        else
        {
            $create = $DMH->insert("lichsumuacode", [
                'username'          => $getUser['username'],
                'id_code'           => $id,
                'magiamgia'         => $mgg,
                'tongtien'          => $sotien,
                'time'              => gettime(),
                'time2'             => time()
            ]);
            if($sotien > 0)
            {
                $DMH->insert("biendongsodu", [
                    'username'      => $getUser['username'],
                    'truoc'         => $my_money,
                    'sau'           => $my_money - $sotien,
                    'note'          => 'Mua code thành công mã '.$id.' giá '.format_cash($sotien).'đ',
                    'tongtien'      => $sotien,
                    'time'          => gettime()
                ]);
                if($check['partner'] != 'adminori') {
                    $par = $DMH->getUser($check['partner']);
                    $DMH->insert("partner_biendongsodu", [
                        'username'      => $check['partner'],
                        'usermua'       => $getUser['username'],
                        'truoc'         => $par['money'],
                        'sau'           => $par['money'] + giamgia($sotien, $DMH->site('ptpartner')),
                        'note'          => $getUser['username'].' mua code thành công mã '.$id.' giá '.format_cash($sotien).'đ',
                        'tongtien'      => giamgia($sotien, $DMH->site('ptpartner')),
                        'id_code'       => $id, 
                        'time'          => gettime()
                    ]);
                    $DMH->cong("users", "money_partner", giamgia($sotien, $DMH->site('ptpartner')), " `username` = '".$check['partner']."'");
                }

            }
            
            if($create)
            {
                $isMoney = $DMH->tru("users", "money", $sotien, " `tokenlog` = '".$_COOKIE['token']."'");
                if($isMoney)
                {
                    if($mgg)
                    {
                        $mgg2 = $DMH->tru("magiamgia", "conlai", 1, " `magiamgia` = '$mgg'");
                        $DMH->cong("magiamgia", "dasudung", 1, " `magiamgia` = '$mgg'");
                        if(!$mgg2)
                        {
                            msg_error2("Lỗi cấu hình CSDL rồi");
                        }
                    }
                    $DMH->cong("danhsachmuacode", "luottai", 1, " `id` = '$id'");
                    // $_SESSION['muacode'] = time() + 15; // 15s mua được 1 lần code
                    
                    if($sotien >= 1)
                    {
                        
                        $bcc = 'dienmayhieu.com';
                        $subject = "Thông báo mua thành công mã nguồn #$id";
                        $hoten = 'Điện Máy Hiếu';
                        
                        $noi_dung = '<html xmlns="http://www.w3.org/1999/xhtml" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:v="urn:schemas-microsoft-com:vml">

                        <head>
                            <!--[if gte mso 9]><xml><o:OfficeDocumentSettings><o:AllowPNG/><o:PixelsPerInch>96</o:PixelsPerInch></o:OfficeDocumentSettings></xml><![endif]-->
                            <meta content="width=device-width" name="viewport">
                            <style>
                                /* #### Mobile Phones Portrait #### */
                                /* #### iPhone 4+ Portrait or Landscape #### */
                                
                                @media only screen and (max-width: 480px) {
                                    table[class=contentInner] {
                                        width: 100% !important;
                                        padding: 0px;
                                        margin: 0px;
                                    }
                                    img[class=zpImage] {
                                        width: 260px !important;
                                        max-width: 360px !important;
                                        text-align: center;
                                        margin: 0px;
                                        padding: 0px
                                    }
                                    body,
                                    table,
                                    td,
                                    p,
                                    li,
                                    div,
                                    span,
                                    blockquote {
                                        -webkit-text-size-adjust: none !important;
                                        margin: 0px auto;
                                        line-height: 1.7
                                    }
                                    table[class=zpImageCaption] {
                                        text-align: left;
                                    }
                                    table[class=cols] {
                                        width: 100% !important;
                                        max-width: 100% !important;
                                        text-align: left;
                                    }
                                    table[class=zpcolumns] {
                                        text-align: left;
                                        margin: 0px;
                                    }
                                    table[class=zpcolumn] {
                                        text-align: left;
                                        margin: 0px;
                                    }
                                    table[class=zpAlignPos] {
                                        width: 100%;
                                        text-align: left;
                                        margin: 0px;
                                    }
                                    td[class=txtsize] {
                                        font-size: 18px !important;
                                    }
                                    td[class=paddingcomp] {
                                        padding-left: 15px !important;
                                        padding-right: 15px !important
                                    }
                                    td[class=bannerimgpad] {
                                        padding: 0px !important;
                                    }
                                    span[class=txtsize] {
                                        font-size: 18px !important;
                                    }
                                    img[size "B"] {
                                        width: 100% !important;
                                        max-width: 100% !important;
                                        margin: 0px !important;
                                        padding: 0px !important;
                                    }
                                    img[size "F"] {
                                        width: 100% !important;
                                        max-width: 100% !important;
                                        margin: 0px !important;
                                        padding: 0px !important;
                                    }
                                    img[size "S"] {
                                        width: 105px !important;
                                        height: auto;
                                        margin: 0px auto !important;
                                        padding: 0px !important;
                                    }
                                    img[size "M"] {
                                        width: 277.869px !important;
                                        height: auto;
                                        margin: 0px auto !important;
                                        padding: 0px !important;
                                    }
                                    h1 {
                                        font-size: 28px !important;
                                        line-height: 100% !important;
                                    }
                                    h2 {
                                        font-size: 24px !important;
                                        line-height: 100% !important;
                                    }
                                    h3 {
                                        font-size: 20px !important;
                                        line-height: 100% !important;
                                    }
                                    h4 {
                                        font-size: 18px !important;
                                        line-height: 100% !important;
                                    }
                                }
                                
                                @media only screen and (max-width: 480px) {
                                    .zpImage {
                                        height: auto !important;
                                        width: 100% !important;
                                    }
                                }
                                
                                @media only screen and (max-width: 480px) {
                                    .contentInner,
                                    .cols,
                                    .zpAlignPos {
                                        width: 100% !important;
                                        max-width: 100% !important;
                                    }
                                }
                                
                                @media only screen and (max-width: 480px) {
                                    .paddingcomp {
                                        padding-left: 15px !important;
                                        padding-right: 15px !important;
                                    }
                                    .bannerimgpad {
                                        padding: 0px !important;
                                    }
                                }
                                
                                @media screen and (max-width: 480px) {
                                    .tmplheader,
                                    .tmplfooter {
                                        width: 100% !important;
                                        max-width: 400px !important;
                                        margin: 0px auto;
                                        text-align: center;
                                    }
                                }
                                
                                a[x-apple-data-detectors] {
                                    color: inherit !important;
                                    text-decoration: none !important;
                                    font-size: inherit !important;
                                    font-family: inherit !important;
                                    font-weight: inherit !important;
                                    line-height: inherit !important;
                                }
                            </style>
                            <meta content="text/html;charset=UTF-8" http-equiv="Content-Type">
                        </head>
                        
                        <body style=" font-size:12px;font-family:Arial, Helvetica, sans-serif; padding:0; color:#000000;margin:0;background-image:url(https://campaign-image.com/zohocampaigns/bg2.gif);">
                            <center>
                                <div class="zppage-container">
                                    <table background="https://campaign-image.com/zohocampaigns/bg2.gif" border="0" cellpadding="0" cellspacing="0" class="contentOuter" id="contentOuter" style="background-image:url(https://campaign-image.com/zohocampaigns/bg2.gif);border:0px; border:0px;border-collapse:collapse;font-size:12px;" width="100%">
                                        <tbody>
                                            <tr>
                                                <td style="border:0px;padding:0px;border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;">&nbsp;</td>
                                                <td align="center" style="border:0px;padding:0px;border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;">
                                                    <table bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" class="contentInner" id="contentInner" style="border-collapse:collapse; border:0px;font-size:12px;background-color:#ffffff;background-color:#ffffff;width:600px;margin:0px auto;border:0px;" width="600">
                                                        <tbody>
                                                            <tr>
                                                                <td style="border:0px;padding:0px;" valign="top">
                                                                    <a name="Top" style="text-decoration:underline;"></a>
                                                                    <div baseposition="pos_YrobDQg7SAqnBt5RUo4dhQ" class="zpcontent-wrapper" id="page-container">
                                                                        <table border="0" cellpadding="0" cellspacing="0" id="page-container" style="font-size:12px;border:0px;padding:0px;border-collapse:collapse; mso-table-lspace:0pt;mso-table-rspace:0pt;text-decoration:none !important;" width="100%">
                                                                            <tbody>
                                                                                <tr>
                                                                                    <td class="txtsize" id="elm_1604994033985" style="border:0px;padding:0px 0px;border-collapse:collapse;" valign="top">
                        
                                                                                        <div class="zpelement-wrapper spacebar" id="elm_1604994033985" style=";word-wrap:break-word;overflow:hidden;background-color:transparent;">
                        
                                                                                            <table bgcolor="transparent" border="0" cellpadding="0" cellspacing="0" class="zpAlignPos" style="padding:0px;border:0px;border-collapse:collapse; mso-table-lspace:0pt;font-size:5px; mso-table-rspace:0pt;word-break:break-word;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;height:30px;" width="100%">
                        
                                                                                                <tbody>
                                                                                                    <tr>
                                                                                                        <td style="padding:0px;border:0px;font-size:5px;height:30px;border-top:none none none;border-bottom:none none none;">
                        
                                                                                                            &nbsp;&nbsp;&nbsp;
                        
                                                                                                        </td>
                                                                                                    </tr>
                        
                                                                                                </tbody>
                                                                                            </table>
                        
                                                                                        </div>
                        
                                                                                    </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td class="txtsize" id="elm_1604993426919" style="border:0px;padding:0px 0px;border-collapse:collapse;" valign="top">
                        
                                                                                        <div class="zpelement-wrapper zpcol-layout" id="elm_1604993426919" style="word-wrap:break-word;padding-bottom:0 !important;overflow:hidden;padding:0px;margin:0px;">
                                                                                            <div class="zpcolumns" elm_wid_start="600" style="padding:0px;margin:0px;">
                                                                                                <table bgcolor="transparent" cellpadding="0" cellspacing="0" style="font-size:12px;border:0px;padding:0px;border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;width:100%;background-color:transparent;" width="100%">
                                                                                                    <tbody>
                                                                                                        <tr>
                                                                                                            <td class="txtsize" style="border:0px;padding:0px 0px;border-top:none none none;border-bottom:none none none;" valign="top">
                                                                                                                <!--[if (gte mso 9)|(IE)]>
                                            <table cellpadding="0" cellspacing="0" style=" mso-table-lspace:0pt; mso-table-rspace:0pt;font-size:12px;border:0px;padding:0px;border-collapse:collapse; mso-table-lspace:0pt;width:100%; mso-table-rspace:0pt;" width="100%">
                                            <tbody><tr>
                                        <![endif]-->
                        
                                                                                                                <!--[if (gte mso 9)|(IE)]>
                                            <td style="font-size:12px;font-family:Arial, Helvetica, sans-serif;border:0px;padding:0px 0px;" align="left" width=" 50.0%" valign="top">
                                        <![endif]-->
                                                                                                                <table align="left" cellpadding="0" cellspacing="0" class="cols" style="font-size:12px;max-width:300.0px;width:100%;border:0px;padding:0px;border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;word-break:break-word;" width="100%">
                                                                                                                    <tbody>
                                                                                                                        <tr>
                                                                                                                            <td class="txtsize" style="border:0px;padding:0px;" valign="top">
                                                                                                                                <div class="zpwrapper col-space" id="pos_1604993426915" style="padding:0px;">
                        
                                                                                                                                    <table bgcolor="transparent" border="0" cellpadding="0" cellspacing="0" style="font-size:12px;border:0px;padding:0px;width:100%;border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;word-break:break-word;background-color:transparent;">
                                                                                                                                        <tbody>
                                                                                                                                            <tr>
                                                                                                                                                <td class="txtsize" style="border:0px;padding:0px 0px;border-top:none none none ;border-bottom:none none none;">
                                                                                                                                                    <div class="zpelement-wrapper image" coupcmp id="elm_1613974950547" prodcmp style=";word-wrap:break-word;overflow:hidden;padding:0px;background-color:transparent;">
                                                                                                                                                        <div>
                                                                                                                                                            <table align="left" border="0" cellpadding="0" cellspacing="0" class="zpAlignPos" style="font-size:12px;text-align:left;padding:0px;border:0px;border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;word-break:break-word;width:100%;text-align:left;">
                                                                                                                                                                <tbody>
                                                                                                                                                                    <tr>
                                                                                                                                                                        <td class="paddingcomp" style="border:0px;padding:7px 15px;text-align:center;padding-top:7px;padding-bottom:7px;padding-right:15px;padding-left:50px;">
                                                                                                                                                                            <img align="left" alt="https://campaign-image.com/zohocampaigns/133052000002837925_zc_v14_1613975809972_logo_bs_2.png" class="zpImage" height="auto" hspace="0" size="O" src="'.$DMH->site('logo').'" style="width:150px;height:autopx;max-width:150px !important;border:0px;text-align:left;" vspace="0" width="58">
                                                                                                                                                                        </td>
                                                                                                                                                                    </tr>
                                                                                                                                                                </tbody>
                                                                                                                                                            </table>
                                                                                                                                                        </div>
                                                                                                                                                    </div>
                                                                                                                                                </td>
                                                                                                                                            </tr>
                                                                                                                                        </tbody>
                                                                                                                                    </table>
                                                                                                                                </div>
                                                                                                                            </td>
                                                                                                                        </tr>
                                                                                                                    </tbody>
                                                                                                                </table>
                                                                                                                <!--[if (gte mso 9)|(IE)]>
                                              </td>
                                    <![endif]-->
                        
                                                                                                                <!--[if (gte mso 9)|(IE)]>
                                            <td style="font-size:12px;font-family:Arial, Helvetica, sans-serif;border:0px;padding:0px 0px;" align="left" width=" 50.0%" valign="top">
                                        <![endif]-->
                                                                                                                <table align="left" cellpadding="0" cellspacing="0" class="cols" style="font-size:12px;max-width:300.0px;width:100%;border:0px;padding:0px;border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;word-break:break-word;" width="100%">
                                                                                                                    <tbody>
                                                                                                                        <tr>
                                                                                                                            <td class="txtsize" style="border:0px;padding:0px;" valign="top">
                                                                                                                                <div class="zpwrapper col-space" id="pos_1604993426917" style="padding:0px;">
                        
                                                                                                                                    <div class="zpelement-wrapper" id="elm_1604993426918" style=";word-wrap:break-word;overflow:hidden;padding-right:0px;background-color:;">
                                                                                                                                        <table border="0" cellpadding="0" cellspacing="0" class="zpAlignPos" style="font-size:12px;padding:0px;border:0px;border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;word-break:break-word;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" width="100%">
                        
                                                                                                                                            <tbody>
                                                                                                                                                <tr>
                                                                                                                                                    <td class="paddingcomp" style="border:0px;padding:7px 15px;line-height:19pt;border-top:0px none ;   border-bottom:0px none ;padding-top:25px;padding-bottom:7px;padding-right:54px;padding-left:50px;">
                                                                                                                                                        <div componentpaddingbottom="7px" componentpaddingleft="50px" componentpaddingright="54px" componentpaddingtop="25px" style>
                                                                                                                                                            <p align="right" style="font-family:Arial,verdana;font-size:12px; color:#000000;padding:0px;margin: 0;line-height: 19pt; text-align: right;"><b><u><a alt="Mua th&ecirc;m m&atilde; nguồn" href="https://dmh.com/mua-source-code" rel="noopener noreferrer" style="text-decoration:underline;" target="_blank" title="Mua th&ecirc;m m&atilde; nguồn"><font color="#0001ee" style="color:#0001ee;">Mua th&ecirc;m m&atilde; nguồn</font></a></u></b>
                                                                                                                                                            </p>
                                                                                                                                                        </div>
                                                                                                                                                    </td>
                                                                                                                                                </tr>
                                                                                                                                            </tbody>
                                                                                                                                        </table>
                                                                                                                                    </div>
                                                                                                                                </div>
                                                                                                                            </td>
                                                                                                                        </tr>
                                                                                                                    </tbody>
                                                                                                                </table>
                                                                                                                <!--[if (gte mso 9)|(IE)]>
                                              </td>
                                    <![endif]-->
                                                                                                                <!--[if (gte mso 9)|(IE)]>
                                              </tr></tbody></table>
                                    <![endif]-->
                                                                                                            </td>
                                                                                                        </tr>
                                                                                                    </tbody>
                                                                                                </table>
                                                                                            </div>
                                                                                        </div>
                                                                                    </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td class="txtsize" id="elm_1604993629796" style="border:0px;padding:0px 0px;border-collapse:collapse;" valign="top">
                        
                                                                                        <table bgcolor="transparent" border="0" cellpadding="0" cellspacing="0" style="font-size:12px;border:0px;padding:0px;width:100%;border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;word-break:break-word;background-color:transparent;">
                                                                                            <tbody>
                                                                                                <tr>
                                                                                                    <td class="txtsize" style="border:0px;padding:0px 0px;border-top:none none none ;border-bottom:none none none;">
                                                                                                        <div class="zpelement-wrapper image" coupcmp id="elm_1604993629796" prodcmp style=";word-wrap:break-word;overflow:hidden;padding:0px;background-color:transparent;">
                                                                                                            <div>
                                                                                                                <table align="center" border="0" cellpadding="0" cellspacing="0" class="zpAlignPos" style="font-size:12px;text-align:left;padding:0px;border:0px;border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;word-break:break-word;width:100%;text-align:center;">
                                                                                                                    <tbody>
                                                                                                                        <tr>
                                                                                                                            <td class="paddingcomp" style="border:0px;padding:7px 15px;text-align:center;padding-top:7px;padding-bottom:24px;padding-right:15px;padding-left:15px;">
                                                                                                                                <img align="center" alt="https://campaign-image.com/zohocampaigns/133052000002837925_1_1614082981712_banner.png" class="zpImage" height="124" hspace="0" size="C" src="https://zohopublic.com/zohocampaigns/1060061000000056006_4_1675537192766_banner.png" style="width:101px;height:124px;max-width:101px !important;border:0px;text-align:center;" vspace="0" width="101">
                                                                                                                            </td>
                                                                                                                        </tr>
                                                                                                                    </tbody>
                                                                                                                </table>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </td>
                                                                                                </tr>
                                                                                            </tbody>
                                                                                        </table>
                                                                                    </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td class="txtsize" id="elm_1604993697995" style="border:0px;padding:0px 0px;border-collapse:collapse;" valign="top">
                        
                                                                                        <div class="zpelement-wrapper" id="elm_1604993697995" style=";word-wrap:break-word;overflow:hidden;padding-right:0px;background-color:;">
                                                                                            <table border="0" cellpadding="0" cellspacing="0" class="zpAlignPos" style="font-size:12px;padding:0px;border:0px;border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;word-break:break-word;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" width="100%">
                        
                                                                                                <tbody>
                                                                                                    <tr>
                                                                                                        <td class="paddingcomp" style="border:0px;padding:7px 15px;line-height:32pt;border-top:0px none ;   border-bottom:0px none ;padding-top:17px;padding-bottom:7px;padding-right:15px;padding-left:15px;">
                                                                                                            <div componentlineheight="32pt" componentpaddingbottom="7px" componentpaddingleft="15px" componentpaddingright="15px" componentpaddingtop="17px" style>
                                                                                                                <p align="center" style="line-height:1.7;font-family:Arial,verdana;font-size:12px; color:#000000;padding:0px;margin: 0;text-align: center; line-height: 32pt;"><font color="#333333" face="Georgia, Times New Roman, Times, serif" style="color: rgb(51, 51, 51); line-height: 32pt; font-size: 38pt;"><b style>Cảm ơn!</b></font>
                                                                                                                </p>
                                                                                                            </div>
                                                                                                        </td>
                                                                                                    </tr>
                                                                                                </tbody>
                                                                                            </table>
                                                                                        </div>
                                                                                    </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td class="txtsize" id="elm_1615279664893" style="border:0px;padding:0px 0px;border-collapse:collapse;" valign="top">
                        
                        
                        
                        
                        
                                                                                        <div class="zpelement-wrapper spacebar" id="elm_1615279664893" style=";word-wrap:break-word;overflow:hidden;background-color:#ffffff;">
                        
                                                                                            <table bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" class="zpAlignPos" style="padding:0px;border:0px;border-collapse:collapse; mso-table-lspace:0pt;font-size:5px; mso-table-rspace:0pt;word-break:break-word;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;height:11px;" width="100%">
                        
                                                                                                <tbody>
                                                                                                    <tr>
                                                                                                        <td style="padding:0px;border:0px;font-size:5px;height:11px;border-top:none none none;border-bottom:none none none;">
                        
                                                                                                            &nbsp;&nbsp;&nbsp;
                        
                                                                                                        </td>
                                                                                                    </tr>
                        
                                                                                                </tbody>
                                                                                            </table>
                        
                                                                                        </div>
                        
                                                                                    </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td class="txtsize" id="elm_1604484315288" style="border:0px;padding:0px 0px;border-collapse:collapse;" valign="top">
                        
                                                                                        <div class="zpelement-wrapper" id="elm_1604484315288" style=";word-wrap:break-word;overflow:hidden;padding-right:0px;background-color:#ffffff;">
                                                                                            <table bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" class="zpAlignPos" style="font-size:12px;padding:0px;border:0px;border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;word-break:break-word;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" width="100%">
                        
                                                                                                <tbody>
                                                                                                    <tr>
                                                                                                        <td class="paddingcomp" style="border:0px;padding:7px 15px;line-height:19pt;border-top:0px none ;   border-bottom:0px none ;padding-top:18px;padding-bottom:12px;padding-right:50px;padding-left:50px;">
                                                                                                            <div componentbgcolor="#ffffff" componentpaddingbottom="12px" componentpaddingleft="50px" componentpaddingright="50px" componentpaddingtop="18px" style="background-color: rgb(255, 255, 255);">
                                                                                                                <p align="center" style="font-family:Arial,verdana;font-size:12px; color:#000000;padding:0px;margin: 0;line-height: 19pt; text-align: center;"><font color="#434343" face="Arial, Helvetica" style="font-size: 12pt;">Xin ch&agrave;o <b>'.$getUser['username'].'</b>!</font>
                                                                                                                </p>
                                                                                                            </div>
                                                                                                        </td>
                                                                                                    </tr>
                                                                                                </tbody>
                                                                                            </table>
                                                                                        </div>
                                                                                    </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td class="txtsize" id="elm_1604989945498" style="border:0px;padding:0px 0px;border-collapse:collapse;" valign="top">
                        
                                                                                        <div class="zpelement-wrapper" id="elm_1604989945498" style=";word-wrap:break-word;overflow:hidden;padding-right:0px;background-color:#ffffff;">
                                                                                            <table bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" class="zpAlignPos" style="font-size:12px;padding:0px;border:0px;border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;word-break:break-word;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" width="100%">
                        
                                                                                                <tbody>
                                                                                                    <tr>
                                                                                                        <td class="paddingcomp" style="border:0px;padding:7px 15px;line-height:23pt;border-top:0px none ;   border-bottom:0px none ;padding-top:7px;padding-bottom:10px;padding-right:80px;padding-left:80px;">
                                                                                                            <div componentbgcolor="#ffffff" componentlineheight="23pt" componentpaddingbottom="10px" componentpaddingleft="80px" componentpaddingright="80px" componentpaddingtop="7px" style="background-color: rgb(255, 255, 255);">
                                                                                                                <p align="center" style="line-height:1.7;font-family:Arial,verdana;font-size:12px; color:#000000;padding:0px;margin: 0;text-align: center; line-height: 23pt;">
                                                                                                                <span style="font-size: 12pt; line-height: 23pt;">
                                                                                                                <font face="Arial, Helvetica" style="line-height: 23pt;">
                                                                                                                <font color="#696969">Cảm ơn bạn đ&atilde; mua h&agrave;ng tại website </font>
                                                                                                                <b style><font color="#006cfb"><a alt="dienmayhieu.com" href="https://dmh.com/" rel="noopener noreferrer" style="text-decoration:underline;" target="_blank" title="dienmayhieu.com">
                                                                                                                <font color="#006cfb" style="color:#006cfb;">dienmayhieu.com</font></a></font></b>
                                                                                                               
                                                                                                            
                                                                                                               
                                                                                                            </font></span>
                                                                                                                    <br>
                                                                                                                </p>
                                                                                                            </div>
                                                                                                        </td>
                                                                                                    </tr>
                                                                                                </tbody>
                                                                                            </table>
                                                                                        </div>
                                                                                    </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td class="txtsize" id="elm_1604989966363" style="border:0px;padding:0px 0px;border-collapse:collapse;" valign="top">
                        
                                                                                        <div class="zpelement-wrapper" id="elm_1604989966363" style=";word-wrap:break-word;overflow:hidden;padding-right:0px;background-color:#ffffff;">
                                                                                            <table bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" class="zpAlignPos" style="font-size:12px;padding:0px;border:0px;border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;word-break:break-word;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" width="100%">
                        
                                                                                                <tbody>
                                                                                                    <tr>
                                                                                                        <td class="paddingcomp" style="border:0px;padding:7px 15px;line-height:21pt;border-top:0px none ;   border-bottom:0px none ;padding-top:10px;padding-bottom:16px;padding-right:50px;padding-left:50px;">
                                                                                                            <div componentbgcolor="#ffffff" componentlineheight="21pt" componentpaddingbottom="16px" componentpaddingleft="50px" componentpaddingright="50px" componentpaddingtop="10px" style="background-color: rgb(255, 255, 255);">
                                                                                                                <p align="center" style="line-height:1.7;font-family:Arial,verdana;font-size:12px; color:#000000;padding:0px;margin: 0;text-align: center; line-height: 21pt;"><span style="font-size: 12pt; line-height: 21pt;"><font color="#696969" face="Arial, Helvetica" style="line-height: 21pt;">H&atilde;y nhấn v&agrave;o n&uacute;t: &quot;<span>Download m&atilde; nguồn</span>&quot; phía dưới để tải m&atilde; nguồn vừa mua về.</font>
                                                                                                                    </span>
                                                                                                                    <br>
                                                                                                                </p>
                                                                                                            </div>
                                                                                                        </td>
                                                                                                    </tr>
                                                                                                </tbody>
                                                                                            </table>
                                                                                        </div>
                                                                                    </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td class="txtsize" id="elm_1614701074425" style="border:0px;padding:0px 0px;border-collapse:collapse;" valign="top">
                        
                        
                                                                                        <div class="zpelement-wrapper spacebar" id="elm_1614701074425" style=";word-wrap:break-word;overflow:hidden;background-color:#ffffff;">
                        
                                                                                            <table bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" class="zpAlignPos" style="padding:0px;border:0px;border-collapse:collapse; mso-table-lspace:0pt;font-size:5px; mso-table-rspace:0pt;word-break:break-word;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;height:13px;" width="100%">
                        
                                                                                                <tbody>
                                                                                                    <tr>
                                                                                                        <td style="padding:0px;border:0px;font-size:5px;height:13px;border-top:none none none;border-bottom:none none none;">
                        
                                                                                                            &nbsp;&nbsp;&nbsp;
                        
                                                                                                        </td>
                                                                                                    </tr>
                        
                                                                                                </tbody>
                                                                                            </table>
                        
                                                                                        </div>
                        
                                                                                    </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td class="txtsize" id="elm_1604993830790" style="border:0px;padding:0px 0px;border-collapse:collapse;" valign="top">
                        
                                                                                        <table bgcolor="transparent" cellpadding="0" cellspacing="0" height="48" style="font-size:12px;border:0px;border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;border:none;" width="100%">
                                                                                            <tbody>
                                                                                                <tr>
                                                                                                    <td class="paddingcomp" style="border:0px;padding:7px 15px;border-top:none none none;border-bottom:none none none;padding-top:10px;padding-bottom:10px;padding-right:15px;padding-left:15px;">
                                                                                                        <div class="zpelement-wrapper buttonElem" id="elm_1604993830790" style="overflow:hidden;word-wrap:break-word;">
                                                                                                            <div class="zpAlignPos" style="text-align:center;">
                                                                                                                <table align="center" cellpadding="0" cellspacing="0" style="font-size:12px;border:none;padding:0px;border:0px;margin:0px auto;border-collapse:separate; mso-table-lspace:0pt; mso-table-rspace:0pt;">
                                                                                                                    <tbody>
                                                                                                                        <tr>
                                                                                                                            <td align="center" class="txtsize" style="border:0px;padding:0px;color:#ffffff;font-family:Arial;text-align:center;border-radius:113px;text-align:center;cursor:pointer;">
                                                                                                                                <!--[if mso]>
                                                      <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="'.$check['download'].'" style="border-radius:113px;height:53px;v-text-anchor:middle;width:226px" arcsize="113%" strokecolor="#ffffff" strokeweight ="0px" fillcolor="#6667f7">
                                                      <v:stroke dashstyle="solid" />
                                                        <w:anchorlock/>
                                                        <center style="direction:ltr;color:#ffffff;font-family:Arial;font-size:12pt;">Download mã nguồn</center>
                                                      </v:roundrect>
                                                <![endif]-->
                                                                                                                                <a align="center" href="'.$check['download'].'" style="padding:0px 0px;background-color:#6667f7;width:226px;line-height:53px;font-size:12pt;direction:ltr;font-family:Arial;color:#ffffff;cursor:pointer;text-decoration:none;border-radius:113px;border:0px solid #ffffff;display:inline-block;mso-hide:all;text-align:center;" target="_blank">
                                                                                                                                    <font style="color:#ffffff;line-height:53px">
                                                        Download m&atilde; nguồn
                                                    </font>
                                                                                                                                </a>
                                                                                                                            </td>
                                                                                                                        </tr>
                                                                                                                    </tbody>
                                                                                                                </table>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </td>
                                                                                                </tr>
                                                                                            </tbody>
                                                                                        </table>
                                                                                    </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td class="txtsize" id="elm_1675536633635" style="border:0px;padding:0px 0px;border-collapse:collapse;" valign="top">
                        
                                                                                        <div class="zpelement-wrapper" id="elm_1675536633635" style=";word-wrap:break-word;overflow:hidden;padding-right:0px;background-color:#ffffff;">
                                                                                            <table bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" class="zpAlignPos" style="font-size:12px;padding:0px;border:0px;border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;word-break:break-word;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" width="100%">
                        
                                                                                                <tbody>
                                                                                                    <tr>
                                                                                                        <td class="paddingcomp" style="border:0px;padding:7px 15px;line-height:21pt;border-top:0px none ;   border-bottom:0px none ;padding-top:10px;padding-bottom:16px;padding-right:15px;padding-left:20px;">
                                                                                                            <div componentbgcolor="#ffffff" componentlineheight="21pt" componentpaddingbottom="16px" componentpaddingleft="20px" componentpaddingright="15px" componentpaddingtop="10px" style="background-color: rgb(255, 255, 255);">
                                                                                                                <p align="justify" style="font-family:Arial,verdana;font-size:12px; color:#000000;padding:0px;margin: 0;line-height: 21pt; text-align: justify;"><font color="#696969" face="Arial, Helvetica"><span style="font-size: 16px;"><b></b><span><b>
                        
                        <span>Thông tin về mã nguồn</span></b></span></span></font>
                                                                                                                </p>
                                                                                                            </div>
                                                                                                        </td>
                                                                                                    </tr>
                                                                                                </tbody>
                                                                                            </table>
                                                                                        </div>
                                                                                    </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td class="txtsize" id="elm_1675536572125" style="border:0px;padding:0px 0px;border-collapse:collapse;" valign="top">
                        
                                                                                        <div class="zpelement-wrapper" id="elm_1675536572125" style=";word-wrap:break-word;overflow:hidden;padding-right:0px;background-color:;">
                                                                                            <table border="0" cellpadding="0" cellspacing="0" class="zpAlignPos" style="font-size:12px;padding:0px;border:0px;border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;word-break:break-word;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" width="100%">
                        
                                                                                                <tbody>
                                                                                                    <tr>
                                                                                                        <td class="paddingcomp" style="border:0px;padding:7px 15px;line-height:19pt;border-top:0px none ;   border-bottom:0px none ;padding-top:7px;padding-bottom:7px;padding-right:15px;padding-left:15px;">
                                                                                                            <div style>
                                                                                                                <table align="center" style=" border:0px;border-collapse:collapse;font-size:12px;width: 100%; margin: 0px auto; border-collapse: collapse;">
                                                                                                                    <tbody>
                                                                                                                        <tr>
                                                                                                                            <td style="border:0px;padding:7px;width: 25%; padding: 7px; font-family: Arial, Helvetica, sans-serif; font-size: 10pt; text-align: center; border-collapse: collapse;" width="25%">M&atilde; số</td>
                                                                                                                            <td style="border:0px;padding:7px;width: 25%; padding: 7px; font-family: Arial, Helvetica, sans-serif; font-size: 10pt; text-align: center; border-collapse: collapse;" width="25%"> <span>Gi&aacute; tiền</span>
                                                                                                                            </td>
                                                                                                                            <td style="border:0px;padding:7px;width: 25%; padding: 7px; font-family: Arial, Helvetica, sans-serif; font-size: 10pt; text-align: center; border-collapse: collapse;" width="25%"> <span>Thời gian</span>
                                                                                                                            </td>
                                                                                                                        </tr>
                                                                                                                        <tr>
                                                                                                                            <td style="border:0px;padding:7px;width: 25%; padding: 7px; font-family: Arial, Helvetica, sans-serif; font-size: 10pt; text-align: center; border-collapse: collapse;" width="25%">#<b>'.$id.'</b>
                                                                                                                            </td>
                                                                                                                            <td style="border:0px;padding:7px;width: 25%; padding: 7px; font-family: Arial, Helvetica, sans-serif; font-size: 10pt; text-align: center; border-collapse: collapse;" width="25%">'.format_cash($sotien).'đ</td>
                                                                                                                            <td style="border:0px;padding:7px;width: 25%; padding: 7px; font-family: Arial, Helvetica, sans-serif; font-size: 10pt; text-align: center; border-collapse: collapse;" width="25%">'.date('d/m/Y', time()).'</td>
                                                                                                                            
                                                                                                                        </tr>
                                                                                                                    </tbody>
                                                                                                                </table>
                                                                                                            </div>
                                                                                                        </td>
                                                                                                    </tr>
                                                                                                </tbody>
                                                                                            </table>
                                                                                        </div>
                                                                                    </td>
                                                                                </tr>
                                                                               
                                                                                <tr>
                                                                                    <td class="txtsize" id="elm_1604484584978" style="border:0px;padding:0px 0px;border-collapse:collapse;" valign="top">
                        
                        
                        
                                                                                        <div class="zpelement-wrapper spacebar" id="elm_1604484584978" style=";word-wrap:break-word;overflow:hidden;background-color:#ffffff;">
                        
                                                                                            <table bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" class="zpAlignPos" style="padding:0px;border:0px;border-collapse:collapse; mso-table-lspace:0pt;font-size:5px; mso-table-rspace:0pt;word-break:break-word;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;height:37px;" width="100%">
                        
                                                                                                <tbody>
                                                                                                    <tr>
                                                                                                        <td style="padding:0px;border:0px;font-size:5px;height:37px;border-top:none none none;border-bottom:none none none;">
                        
                                                                                                            &nbsp;&nbsp;&nbsp;
                        
                                                                                                        </td>
                                                                                                    </tr>
                        
                                                                                                </tbody>
                                                                                            </table>
                        
                                                                                        </div>
                        
                                                                                    </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td class="txtsize" id="elm_1614701551260" style="border:0px;padding:0px 0px;border-collapse:collapse;" valign="top">
                                                                                        <div class="zpelement-wrapper spacebar" id="elm_1614701551260" style=";word-wrap:break-word;overflow:hidden;background-color:#333333;">
                        
                                                                                            <table bgcolor="#333333" border="0" cellpadding="0" cellspacing="0" class="zpAlignPos" style="padding:0px;border:0px;border-collapse:collapse; mso-table-lspace:0pt;font-size:5px; mso-table-rspace:0pt;word-break:break-word;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;height:22px;" width="100%">
                        
                                                                                                <tbody>
                                                                                                    <tr>
                                                                                                        <td style="padding:0px;border:0px;font-size:5px;height:22px;border-top:none none none;border-bottom:none none none;">
                        
                                                                                                            &nbsp;&nbsp;&nbsp;
                        
                                                                                                        </td>
                                                                                                    </tr>
                        
                                                                                                </tbody>
                                                                                            </table>
                        
                                                                                        </div>
                        
                                                                                    </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td class="txtsize" id="elm_1604985661086" style="border:0px;padding:0px 0px;border-collapse:collapse;" valign="top">
                        
                                                                                        <div class="zpelement-wrapper" id="elm_1604985661086" style=";word-wrap:break-word;overflow:hidden;padding-right:0px;background-color:#333333;">
                                                                                            <table bgcolor="#333333" border="0" cellpadding="0" cellspacing="0" class="zpAlignPos" style="font-size:12px;padding:0px;border:0px;border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;word-break:break-word;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" width="100%">
                        
                                                                                                <tbody>
                                                                                                    <tr>
                                                                                                        <td class="paddingcomp" style="border:0px;padding:7px 15px;line-height:19pt;border-top:0px none ;   border-bottom:0px none ;padding-top:7px;padding-bottom:7px;padding-right:50px;padding-left:50px;">
                                                                                                            <div componentbgcolor="#333333" componentpaddingbottom="7px" componentpaddingleft="50px" componentpaddingright="50px" componentpaddingtop="7px" style="background-color: rgb(51, 51, 51);">
                                                                                                                <p align="center" style="font-family:Arial,verdana;font-size:12px; color:#000000;padding:0px;margin: 0;line-height: 19pt; text-align: center;"><font color="#bababa" face="Arial, Helvetica" style="font-size: 11pt;">Kết nối với ch&uacute;ng t&ocirc;i</font>
                                                                                                                </p>
                                                                                                            </div>
                                                                                                        </td>
                                                                                                    </tr>
                                                                                                </tbody>
                                                                                            </table>
                                                                                        </div>
                                                                                    </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td class="txtsize" id="elm_1614700942884" style="border:0px;padding:0px 0px;border-collapse:collapse;" valign="top">
                        
                                                                                        <div class="zpelement-wrapper wdgts" id="elm_1614700942884" style="overflow:hidden;word-wrap:break-word;">
                                                                                            <table bgcolor="#333333" border="0" cellpadding="0" cellspacing="0" class="zpAlignPos" style="padding:0px;border:0px;border-collapse:collapse; mso-table-lspace:0pt;font-size:5px; mso-table-rspace:0pt;word-break:break-word;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;  background-color:#333333;" width="100%">
                                                                                                <tbody>
                                                                                                    <tr>
                                                                                                        <td style="padding:7px 15px;border:0px;font-size:5px;border-top:0px none ;border-bottom:0px none ;padding-top:7px;padding-bottom:7px;padding-right:15px;padding-left:15px;">
                                                                                                            <table align="center" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;font-size:12px;min-width: 100%;border: none;" width="100%">
                                                                                                                <tbody>
                                                                                                                    <tr>
                                                                                                                        <td align="center" style="border: none;padding: 0px;margin: 0px;" valign="top">
                                                                                                                            <table align="center" border="0" cellpadding="0" cellspacing="0" componentbgcolor="#333333" icontext="true" index="2" name="zcsclwdgts_alnmnt" style="font-size:12px;border-collapse: collapse;border: none;margin:auto;">
                                                                                                                                <tbody>
                                                                                                                                    <tr>
                                                                                                                                        <td align="left" style="border: none;padding: 0px;margin: 0px;" valign="top">
                                                                                                                                            <table align="center" border="0" cellpadding="0" cellspacing="0" name="zcsclwdgtscontainer" style="border-collapse:collapse;font-size:12px;border: none;">
                                                                                                                                                <tbody>
                                                                                                                                                    <tr>
                                                                                                                                                        <td align="left" style="border:none;padding:0px;margin:0px;" valign="top">
                                                                                                                                                            <table align="left" border="0" cellpadding="0" cellspacing="0" style="font-size:12px;border-collapse: collapse;border: none;">
                                                                                                                                                                <tbody>
                                                                                                                                                                    <tr>
                                                                                                                                                                        <td style="padding-right: 9px;padding-bottom: 9px;border:none;padding: 0px;margin: 0px;" valign="top">
                                                                                                                                                                            <table border="0" cellpadding="0" cellspacing="0" style="font-size:12px;border-collapse: separate;border: none;">
                                                                                                                                                                                <tbody>
                                                                                                                                                                                    <tr>
                                                                                                                                                                                        <td align="left" style="padding:7px;padding-top: 0px;padding-right: 9px;padding-bottom: 0px;padding-left: 9px;border:none;" valign="middle">
                                                                                                                                                                                            <table align="left" border="0" cellpadding="0" cellspacing="0" style="font-size:12px;border-collapse: collapse;border: none;" width>
                                                                                                                                                                                                <tbody>
                                                                                                                                                                                                    <tr>
                                                                                                                                                                                                        <td align="center" name="sclwdgtimges" style="border: none;padding: 0px;margin: 0px;padding-bottom: 6px;" valign="middle">
                                                                                                                                                                                                            <a href="https://www.facebook.com/Hotro.DMH" style="text-decoration:underline;display: block;font-size: 1px;" target="_blank"><img alt="Facebook" height="35" src="https://zohopublic.com/zohocampaigns/1060061000000056006_1_1675537192642_zcsclwgtfb2.png" style="border: 0px; margin: 0px; outline: none; text-decoration: none; width: 25px; height: 25px;" vspace="10" width="35">
                                                                                                                                                                                                            </a>
                                                                                                                                                                                                        </td>
                                                                                                                                                                                                    </tr>
                                                                                                                                                                                                    <tr>
                                                                                                                                                                                                        <td align="center" name="sclwdgtcaptns" style="border:none;padding: 0px;margin: 0px;" valign="middle">
                                                                                                                                                                                                            <a href="https://www.facebook.com/Hotro.DMH" style="display: block;font-size: 1px;font-weight: normal;line-height: normal;text-align: center;text-decoration: none;" target="_blank">
                                                                                                                                                                                                                <p fntname="Arial" fntsze="8" style="font-family:Arial,verdana;font-size:12px; color:#000000;padding:0px;margin: 0;line-height: normal; font-family: Arial, Helvetica, sans-serif; color: rgb(27, 107, 189); font-size: 8pt;">Facebook</p>
                                                                                                                                                                                                            </a>
                                                                                                                                                                                                        </td>
                                                                                                                                                                                                    </tr>
                                                                                                                                                                                                </tbody>
                                                                                                                                                                                            </table>
                                                                                                                                                                                        </td>
                                                                                                                                                                                    </tr>
                                                                                                                                                                                </tbody>
                                                                                                                                                                            </table>
                                                                                                                                                                        </td>
                                                                                                                                                                    </tr>
                                                                                                                                                                </tbody>
                                                                                                                                                            </table>
                                                                                                                                                        </td>
                                                                                                                                                        <td align="left" style="border:none;padding:0px;margin:0px;" valign="top">
                                                                                                                                                            <table align="left" border="0" cellpadding="0" cellspacing="0" style="font-size:12px;border-collapse: collapse;border: none;">
                                                                                                                                                                <tbody>
                                                                                                                                                                    <tr>
                                                                                                                                                                        <td style="padding-right: 9px;padding-bottom: 9px;border:none;padding: 0px;margin: 0px;" valign="top">
                                                                                                                                                                            <table border="0" cellpadding="0" cellspacing="0" style="font-size:12px;border-collapse: separate;border: none;">
                                                                                                                                                                                <tbody>
                                                                                                                                                                                    <tr>
                                                                                                                                                                                        <td align="left" style="padding:7px;padding-top: 0px;padding-right: 9px;padding-bottom: 0px;padding-left: 9px;border:none;" valign="middle">
                                                                                                                                                                                            <table align="left" border="0" cellpadding="0" cellspacing="0" style="font-size:12px;border-collapse: collapse;border: none;" width>
                                                                                                                                                                                                <tbody>
                                                                                                                                                                                                    <tr>
                                                                                                                                                                                                        <td align="center" name="sclwdgtimges" style="border: none;padding: 0px;margin: 0px;padding-bottom: 6px;" valign="middle">
                                                                                                                                                                                                            <a href="https://www.youtube.com/@dienmayhieu" style="text-decoration:underline;display: block;font-size: 1px;" target="_blank"><img alt="Youtube" height="35" src="https://zohopublic.com/zohocampaigns/1060061000000056006_2_1675537192680_zcsclwgtyt2.png" style="border: 0px; margin: 0px; outline: none; text-decoration: none; width: 25px; height: 25px;" vspace="10" width="35">
                                                                                                                                                                                                            </a>
                                                                                                                                                                                                        </td>
                                                                                                                                                                                                    </tr>
                                                                                                                                                                                                    <tr>
                                                                                                                                                                                                        <td align="center" name="sclwdgtcaptns" style="border:none;padding: 0px;margin: 0px;" valign="middle">
                                                                                                                                                                                                            <a href="https://www.youtube.com/@dienmayhieu" style="display: block;font-size: 1px;font-weight: normal;line-height: normal;text-align: center;text-decoration: none;" target="_blank">
                                                                                                                                                                                                                <p fntname="Arial" fntsze="8" style="font-family:Arial,verdana;font-size:12px; color:#000000;padding:0px;margin: 0;line-height: normal; font-family: Arial, Helvetica, sans-serif; color: rgb(27, 107, 189); font-size: 8pt;">Youtube</p>
                                                                                                                                                                                                            </a>
                                                                                                                                                                                                        </td>
                                                                                                                                                                                                    </tr>
                                                                                                                                                                                                </tbody>
                                                                                                                                                                                            </table>
                                                                                                                                                                                        </td>
                                                                                                                                                                                    </tr>
                                                                                                                                                                                </tbody>
                                                                                                                                                                            </table>
                                                                                                                                                                        </td>
                                                                                                                                                                    </tr>
                                                                                                                                                                </tbody>
                                                                                                                                                            </table>
                                                                                                                                                        </td>
                                                                                                                                                        <td align="left" style="border:none;padding:0px;margin:0px;" valign="top">
                                                                                                                                                            <table align="left" border="0" cellpadding="0" cellspacing="0" style="font-size:12px;border-collapse: collapse;border: none;">
                                                                                                                                                                <tbody>
                                                                                                                                                                    <tr>
                                                                                                                                                                        <td style="padding-right: 9px;padding-bottom: 9px;border:none;padding: 0px;margin: 0px;" valign="top">
                                                                                                                                                                            <table border="0" cellpadding="0" cellspacing="0" style="font-size:12px;border-collapse: separate;border: none;">
                                                                                                                                                                                <tbody>
                                                                                                                                                                                    <tr>
                                                                                                                                                                                        <td align="left" style="padding:7px;padding-top: 0px;padding-right: 9px;padding-bottom: 0px;padding-left: 9px;border:none;" valign="middle">
                                                                                                                                                                                            <table align="left" border="0" cellpadding="0" cellspacing="0" style="font-size:12px;border-collapse: collapse;border: none;" width>
                                                                                                                                                                                                <tbody>
                                                                                                                                                                                                    <tr>
                                                                                                                                                                                                        <td align="center" name="sclwdgtimges" style="border: none;padding: 0px;margin: 0px;padding-bottom: 6px;" valign="middle">
                                                                                                                                                                                                            <a href="mailto:cskh@dmh.vn" style="text-decoration:underline;display: block;font-size: 1px;" target="_blank"><img alt="Email" height="35" src="https://zohopublic.com/zohocampaigns/1060061000000056006_3_1675537192718_zcsclwgtmail2.png" style="border: 0px; margin: 0px; outline: none; text-decoration: none; width: 25px; height: 25px;" vspace="10" width="35">
                                                                                                                                                                                                            </a>
                                                                                                                                                                                                        </td>
                                                                                                                                                                                                    </tr>
                                                                                                                                                                                                    <tr>
                                                                                                                                                                                                        <td align="center" name="sclwdgtcaptns" style="border:none;padding: 0px;margin: 0px;" valign="middle">
                                                                                                                                                                                                            <a href="mailto:cskh@dmh.vn" style="display: block;font-size: 1px;font-weight: normal;line-height: normal;text-align: center;text-decoration: none;" target="_blank">
                                                                                                                                                                                                                <p fntname="Arial" fntsze="8" style="font-family:Arial,verdana;font-size:12px; color:#000000;padding:0px;margin: 0;line-height: normal; font-family: Arial, Helvetica, sans-serif; color: rgb(27, 107, 189); font-size: 8pt;">Email</p>
                                                                                                                                                                                                            </a>
                                                                                                                                                                                                        </td>
                                                                                                                                                                                                    </tr>
                                                                                                                                                                                                </tbody>
                                                                                                                                                                                            </table>
                                                                                                                                                                                        </td>
                                                                                                                                                                                    </tr>
                                                                                                                                                                                </tbody>
                                                                                                                                                                            </table>
                                                                                                                                                                        </td>
                                                                                                                                                                    </tr>
                                                                                                                                                                </tbody>
                                                                                                                                                            </table>
                                                                                                                                                        </td>
                                                                                                                                                    </tr>
                                                                                                                                                </tbody>
                                                                                                                                            </table>
                                                                                                                                        </td>
                                                                                                                                    </tr>
                                                                                                                                </tbody>
                                                                                                                            </table>
                                                                                                                        </td>
                                                                                                                    </tr>
                                                                                                                </tbody>
                                                                                                            </table>
                                                                                                        </td>
                                                                                                    </tr>
                                                                                                </tbody>
                                                                                            </table>
                                                                                        </div>
                                                                                    </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td class="txtsize" id="elm_1614701576652" style="border:0px;padding:0px 0px;border-collapse:collapse;" valign="top">
                        
                        
                        
                        
                                                                                        <div class="zpelement-wrapper spacebar" id="elm_1614701576652" style=";word-wrap:break-word;overflow:hidden;background-color:#333333;">
                        
                                                                                            <table bgcolor="#333333" border="0" cellpadding="0" cellspacing="0" class="zpAlignPos" style="padding:0px;border:0px;border-collapse:collapse; mso-table-lspace:0pt;font-size:5px; mso-table-rspace:0pt;word-break:break-word;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;height:22px;" width="100%">
                        
                                                                                                <tbody>
                                                                                                    <tr>
                                                                                                        <td style="padding:0px;border:0px;font-size:5px;height:22px;border-top:none none none;border-bottom:none none none;">
                        
                                                                                                            &nbsp;&nbsp;&nbsp;
                        
                                                                                                        </td>
                                                                                                    </tr>
                        
                                                                                                </tbody>
                                                                                            </table>
                        
                                                                                        </div>
                        
                                                                                    </td>
                                                                                </tr>
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                        
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                                <td style="border:0px;padding:0px;border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;">&nbsp;</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </center>
                        </body>
                        
                        </html>';
                        $kq = sendCSM($getUser['email'], $hoten, $subject, $noi_dung, $bcc);
                    }
                    msg_success2("Mua code thành công với giá ".format_cash($sotien)."đ. Hãy vào lịch sử mua code để tải nó về.");
                }
            }
            else
            {
                msg_error2("Lỗi cấu hình CSDL rồi");
            }
        }
    }

    // XỬ LÝ MUA MIỀN
    if($_POST['type'] == 'Muamien')
    {
        if(empty($_COOKIE['token']))
        {
            msg_error2('Vui lòng đăng nhập để tiếp tục');
        }
        $ten    = check_string(strtolower($_POST['ten'])); // chuyển kí tự về viết thường hàm strtolower
        $duoi   = check_string($_POST['duoi']);
        $nam    = check_string($_POST['nam']);
        $ns  = check_string($_POST['ns']);
        if(!$ten || !$duoi || !$nam || !$ns)
        {
            msg_error2("Vui lòng nhập đầy đủ thông tin");
        }
        if($nam < 1 || $nam >= 10)
        {
            msg_error2("Số năm không hợp lệ");
        }
        if(strlen($ten) <= 3)
        {
            msg_error2("Tên miền bạn mua phải có trên 3 kí tự");
        }
        $tuan = explode('.',$ten);
        if(isset($tuan[1]))
        {
            msg_error2("Tên miền bạn nhập không hợp lệ.");
        }
        if(preg_match('/[#@! $%^&*()+=\-\[\]\';,.\/{}|":<>?~\\\\]/', $ten))
        {
            msg_error2("Tên miền của bạn không dược chứa kí tự lạ");
        }
        $duoi1 = $DMH->get_row(" SELECT * FROM `danhsachmien` WHERE `id` = '$duoi' ");
        $duoi = $duoi1['domain'];
        $sotien = $duoi1['money'] + ($nam-1)*$duoi1['giahan'];
        if ($sotien < 1)
        {
            msg_error2("Số tiền không hợp lệ");
        }
        if($my_money < $sotien)
        {
            $napthem = $sotien - $my_money;
            msg_error2("Số tiền bạn không đủ để thực hiện thanh toán (Vui lòng nạp thêm ".format_cash($napthem)."đ vào tài khoản)");
        }
        $nss = explode("\n", $ns);
        if(count($nss) < 2) {
            msg_error2("Tối thiểu có 2 nameserver");
        }
        $tenmien = $ten.'.'.$duoi;
        if($DMH->get_row(" SELECT * FROM `lichsumuamien` WHERE `domain` =  '$tenmien' AND `status` = 'xuly'")) {
            msg_error2("Tên miền này đã được mua trên hệ thống");
        }
        $check = json_decode(curl_get('https://whois.inet.vn/api/whois/domainspecify/'.$tenmien), true);
        if($check['code'] == '1') {
            $create = $DMH->insert("lichsumuamien", [
                'username'      => $getUser['username'],
                'domain'        => $tenmien,
                'ns'            => $ns,
                'thoihan'       => $nam,
                'timemua'       => gettime(),
                'timedie'       => 0,
                'tongtien'      => $sotien,
                'status'        => 'xuly',
                'type'          => 'LOGIN'
            ]);
            if($create) {
                $DMH->insert("biendongsodu", [
                    'username'      => $getUser['username'],
                    'truoc'         => $my_money,
                    'sau'           => $my_money - $sotien,
                    'note'          => 'Mua tên miền '.$tenmien.' với giá '.format_cash($sotien).'đ',
                    'tongtien'      => $sotien,
                    'time'          => gettime()
                ]);
                $isMoney = $DMH->tru("users", "money", $sotien, " `tokenlog` = '".$_COOKIE['token']."'");
                if($isMoney){
                    $clf = $DMH->get_row(" SELECT * FROM `domainclf` WHERE `accountid` IS NOT NULL AND `status` = 'ON' LIMIT 1 ");
                    $rs = $DMH->get_row(" SELECT * FROM `lichsutaoweb` WHERE `tenmien` = '$tenmien' AND `buoc` = 2 AND `username` = '".$getUser['username']."' ORDER BY id DESC LIMIT 1 ") ?? false;
                    if($rs && count($nss) == 2 &&  ( ($nss[0] == $clf['ns1'] && $nss[1] == $clf['ns2']) || ($nss[1] == $clf['ns1'] && $nss[0] == $clf['ns2'])  )  ) {
                        $DMH->update("lichsutaoweb", array(
                            'buoc'       => 3,
                        ), " `id` = '".$rs['id']."' ");
                        msg_success('Mua miền thành công, chúng tôi đã xác nhận đơn tạo website!', BASE_URL('QuanLy/TrangWeb/'.$rs['id']), 1500);
                    }
                    // send_tele($getUser['username']." vừa mua tên miền ".$ten.'.'.$duoi.". Admin vui lòng duyệt tại website dienmayhieu.com.");
                    msg_success('Mua miền thành công! Vui lòng chờ xử lý', BASE_URL('Mua-mien'), 1000);
                }
            }
            else{
                msg_error2("Lỗi CSDL");
            }
        }
        else {
            msg_error2("Xin lỗi! $ten.".$duoi." đã có người đăng ký từ trước ");
        } 
    } 
    if($_POST['type'] == 'DownLoadCodeAdmin') {
        if($getUser['level'] == 'admin') {
            $row = $DMH->get_row(" SELECT * FROM `danhsachmuacode` WHERE `id` = '".$_POST['id']."' ");
            if(!$row) {
                msg_error2("Mã code này không tồn tại rồi!");
            }
            else {
                msg_success('Chuẩn bị chuyển hướng', $row['download'], 500);
            }
        }
        else {
            msg_error2("Bạn không phải là admin");
        }
    }
    
    if($_POST['type'] == 'DownLoadCodeAdmin2')
    {
        if($getUser['level'] == 'admin')
        {
            $row = $DMH->get_row(" SELECT * FROM `danhsachtaoweb` WHERE `id` = '".$_POST['id']."' ");
            $row2 = $DMH->get_row(" SELECT * FROM `danhsachmuacode` WHERE `img` = '".$row['img']."' ");
            if(!$row)
            {
                msg_error2("Mã code này không tồn tại rồi!");
            }
            else
            {
                msg_success('Chuẩn bị chuyển hướng', $row2['download'], 500);
            }
        }
        else
        {
            msg_error2("Bạn không phải là admin");
        }
    }

    if($_POST['type'] == 'XulyTaoWeb') {
        $id = check_string($_POST['id']);
        $row = $DMH->get_row(" SELECT * FROM `lichsutaoweb` WHERE `id` = '$id' AND `username` = '".$getUser['username']."' AND `buoc` != '6' ");
        if(!$id) {
            msg_error2("Bạn chưa truyền đủ tham số");
        }
        if(!$row) {
            msg_error2("Thông tin đơn hàng của bạn không chính xác");   
        }
        // xử lý bước 1 và gia hạn
        if(in_array($row['buoc'], [1,4,5])) { // chỉ nhận bước 1, 4,5
            $thang = check_string($_POST['thang']);
            if(!$thang) {
                msg_error2("Vui lòng chọn thời gian để gia hạn");
            }
            if(in_array($thang, [3,6,12,24,36])) {
                $sotien = 0; $timec = 0;
                if($thang > 0) {
                    $sotien = ($thang/3)*$row['moneygiahan'];
                    $timec = $thang*2592000;
                }
                if($sotien > $getUser['money']) {
                    $napthem = $sotien - $my_money;
                    msg_error2("Số tiền bạn không đủ để thực hiện thanh toán (Vui lòng nạp thêm ".format_cash($napthem)."đ vào tài khoản)");
                } else {
                    $DMH->insert("biendongsodu", [
                        'username'      => $getUser['username'],
                        'truoc'         => $my_money,
                        'sau'           => $my_money - $sotien,
                        'note'          => 'Gia hạn website '.$row['tenmien'].' thêm '.$thang.' tháng',
                        'tongtien'      => $sotien,
                        'time'          => gettime()
                    ]);
                    $isMoney = $DMH->tru("users", "money", $sotien, " `tokenlog` = '".$_COOKIE['token']."'");
                }
                if($row['buoc'] != 1) {
                    // chỉ có bước 4 và 5 mới có thể add gia hạn
                    $DMH->insert("lichsugiahan", [
                        'username'  => $getUser['username'],
                        'id_web'    => $id,
                        'tenmien'   => $row['tenmien'],
                        'tongtien'  => $sotien,
                        'thoigian'  => $thang, 
                        'time'      => gettime(),
                        'status'    => 'xuly'
                    ]);
                }
                if($isMoney)
                {
                    // send_tele($getUser['username']." vừa mua tên miền ".$ten.'.'.$duoi.". Admin vui lòng duyệt tại website dienmayhieu.com.");
                    if($row['buoc'] == 1) {
                        $DMH->cong("lichsutaoweb", "buoc", 1, " `id` = '$id'");
                        $DMH->update("lichsutaoweb", array(
                            'thangmua'       => $thang,
                        ), " `id` = '".$row['id']."' ");
                    }
                    // if($row['buoc'] != 1) $DMH->cong("lichsutaoweb", "ngayhethan", $timec, " `id` = '$id'");
                    msg_success('Đã thực hiện gia hạn thành công!', '', 1000);
                }
            } else {
                msg_error2("Thời gian gia hạn không hợp lệ");
            }
        // xử lý bước 2
        } else if($row['buoc'] == 2) {
            $clf = $DMH->get_row(" SELECT * FROM `domainclf` WHERE `accountid` IS NOT NULL AND `status` = 'ON' ");
            $domain = check_string($_POST['tenmien']);
            $tuan = explode('.',$domain);
            if(!check_domain($tuan)) {
                msg_error2("Tên miền bạn nhập không hợp lệ.");
            }
            $DMH->update("lichsutaoweb", array(
                'tenmien'       => $domain,
            ), " `id` = '".$row['id']."' ");
            $data = json_decode(curl_get("https://api.dmh.vn/domain.php?domain=$domain"), true);
            if($data['true'] == 0){ 
                msg_error2("Tên miền chưa được đăng ký, không thể xác nhận.");
            }
            if(empty($row['id_addmien'])) {
                $post_data = [
                    'account' => ['id'=>$clf['accountid']],
                    'name' => $domain,
                    'jump_start' => true,
                ];
                $curl = curl_init();
                curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.cloudflare.com/client/v4/zones',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($post_data),
                CURLOPT_HTTPHEADER => array(
                    'X-Auth-Key: '.$clf['auth'].'',
                    'X-Auth-Email: '.$clf['email'].'',
                    'Content-Type: application/json'
                    ),
                ));
                $response = curl_exec($curl);
                curl_close($curl);
                $kq = json_decode($response, true);
                if($kq['success'] == true){
                    $DMH->update("lichsutaoweb", array(
                        'tenmien'       => $domain,
                        'domainclf'     => $clf['id'],
                        'tenmien'       => $mien,
                        'sttdomain'     => 'ON',
                        'domainid'      => $kq['result']['id'],
                        'id_addmien'    => $kq['result']['owner']['id'],
                        'userthaotac'   => $getUser['username']
                    ), " `id` = '".$row['id']."' ");

                } else {
                    $res = $DMH->get_row(" SELECT * FROM `lichsutaoweb` WHERE `tenmien` = '$domain' ");
                    if($res['domainclf']) {
                        $DMH->update("lichsutaoweb", array(
                            'tenmien'       => $domain,
                            'domainclf'     => $res['domainclf'],
                            'domainid'      => $res['domainid'],
                            'id_addmien'    => $res['id_addmien']
                        ), " `id` = '".$row['id']."' ");
                    }
                }
            }
            if(isset($row['id_addmien'])) {
                $ch = curl_init("https://api.cloudflare.com/client/v4/zones/".$row['domainid']."");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");                                                                     
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'X-Auth-Key: '.$clf['auth'].'',
                    'X-Auth-Email: '.$clf['email'].'',
                    'Cache-Control: no-cache',
                    'Content-Type:application/json',
                    'purge_everything: true'
            
                ));
            
                $sonuc = curl_exec($ch);
                curl_close($ch);
                $data = json_decode($sonuc, true);
                if(isset($data['result']) && $data['result']['status'] == 'active')
                {
                    $DMH->update("lichsutaoweb", array(
                        'tenmien'   => $domain,
                        'statusclf' => 'active',
                        'buoc'      => 3
                    ), " `id` = '".$row['id']."' ");
                    msg_success("Xác nhận thành công, vui lòng chờ xử lý website!","", 1500);
                } else {
                    msg_error2("Vui lòng chờ đợi để hệ thống cập nhật");
                }
            } else {
                msg_error2("Vui lòng bấm xác nhận lại vài lần nữa!");
            }
        }
    }

    if($_POST['type'] == 'GiaHanDomain')
    {
        $id     = check_string($_POST['id']);
        $thang  = check_string($_POST['thang']);
        if(!$id || !$thang) {
            msg_error2("Vui lòng chọn thời gian gia hạn");
        }
        if(!$row = $DMH->get_row(" SELECT * FROM `lichsumuamien` WHERE `id` = '$id' AND `username` = '".$getUser['username']."' ")) {
            msg_error2("Dữ liệu tên miền không hợp lệ");
        }
        if($thang < 1 && $thang > 9) {
            msg_error2("Thời gian gia hạn không hợp lệ");
        }
        if(!in_array($row['status'], ['hoatdong', 'hethan'])) {
            msg_error2("Chưa thể gia hạn lúc này");
        } else {
            if($row['status'] == 'hethan') {
                if(strtotime($row['timedie']) + 1296000 < time()) {
                    msg_error2("Tên miền của bạn không thể gia hạn được nữa!");
                }
            }
            
        }
        $data = explode('.', $row['domain']);
        $row2 = $DMH->get_row(" SELECT * FROM `danhsachmien` WHERE `domain` = '".end($data)."' ");
        $sotien = $thang*$row2['giahan'];
        if($my_money < $sotien) {
            $napthem = $sotien - $my_money;
            msg_error2("Số tiền bạn không đủ để thực hiện thanh toán (Vui lòng nạp thêm ".format_cash($napthem)."đ vào tài khoản)");
        }
        $DMH->insert("biendongsodu", [
            'username'      => $getUser['username'],
            'truoc'         => $my_money,
            'sau'           => $my_money - $sotien,
            'note'          => 'Mua tên miền với giá '.format_cash($sotien).'đ',
            'tongtien'      => $sotien,
            'time'          => gettime()
        ]);
        $DMH->insert("giahanmien", [
            'username'      => $getUser['username'],
            'id_domain'     => $id,
            'tenmien'       => $row['domain'],
            'tongtien'      => $sotien,
            'thoigian'      => $thang,
            'status'        => 'xuly',
            'time'          => gettime()
        ]);
        $isMoney = $DMH->tru("users", "money", $sotien, " `tokenlog` = '".$_COOKIE['token']."'");
        if($isMoney){
            // send_tele($getUser['username']." vừa gia hạn tên miền ".$row['domain']." thêm $thang năm. Admin vui lòng duyệt tại website dienmayhieu.com.");
            msg_success("Bạn đã gia hạn thành công thêm $thang năm", "", 1000);
        }
    }
}
else
{
    require_once("../../pages/client/404.php");
}