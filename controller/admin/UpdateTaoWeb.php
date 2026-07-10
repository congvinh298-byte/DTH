<?php

    define("IN_SITE", true);
    require_once("../../core/config.php");
    require_once("../../core/function.php");
    if($_COOKIE['token']) {
        if($getUser['level'] != 'admin') {
            msg("error", "Bạn không phải là ADMIN");
        }
        $id_web     = check_string($_POST['id_web']);
        if(!$id_web) {
            msg("error", "Mã ID không hợp lệ");
        }
        $rowvn      = $DMH->get_row(" SELECT * FROM `lichsutaoweb` WHERE `id` = '$id_web' ");
        $usertv     = $DMH->getUser($rowvn['username']);
        if(!$rowvn) {
            msg("error","Bug hả bạn?");
        }

        // xử lý chỉnh sửa đơn hàng
        if($_POST['type'] == 'UpdateAll') {
            $username   = check_string($_POST['username']);
            $tenmien    = check_string($_POST['tenmien']);
            $tk         = check_string($_POST['tkadmin']);
            $mk         = check_string($_POST['mkadmin']);
            $id_code    = check_string($_POST['id_code']);
            $status     = check_string($_POST['status']);
            $thangmua   = check_string($_POST['thangmua']);
            $giahanweb  = check_string(numb($_POST['giahanweb']));
            $tongtien   = check_string(numb($_POST['tongtien']));
            $note       = check_string(numb($_POST['note']));
            $linklogin  = check_string($_POST['linklogin']);
            $tkhs       = check_string($_POST['tkhs']);
            $mkhs       = check_string($_POST['mkhs']);
            if(!$tenmien || !$tk || !$mk || !$id_code ||  !$username ) {
                msg("error","Vui lòng nhập đủ thông tin");
            }
            if(!$DMH->get_row(" SELECT * FROM `danhsachtaoweb` WHERE `id` = '$id_code'")) {
                msg("error","Mã web $id_code không tồn tại trong hệ thống");
            }
            if(!$rowvn['ngayduyet'] || !$rowvn['ngayhethan']) {
                if($status == 4) {
                    if(!$thangmua) {
                        msg("error","Vui lòng nhập tháng mua");
                    }
                    $timeduyet      = time();
                    $thanghethan    = time() + ($onethang * $thangmua);
                }
                else if($status == 3) {
                    $timeduyet      = null;
                    $thanghethan    = null;
                } else {
                   
                    $timeduyet      = $rowvn['ngayduyet'];
                    $thanghethan    = $rowvn['ngayhethan'];
                }
            } else {
                $timeduyet      = $rowvn['ngayduyet'];
                $thanghethan    = $rowvn['ngayhethan'];
            }
            // xử lý nếu giống tên miền
            if($tenmien === $rowvn['tenmien']) {
                // bước 7, hủy đơn hàng
                if($status == '7') {
                    $isMoney = $DMH->cong("users", "money", $rowvn['tongtien'], " `username` = '".$rowvn['username']."'");
                    if($isMoney) {
                        $DMH->insert("biendongsodu", [
                            'username'      => $rowvn['username'],
                            'truoc'         => $usertv['money'],
                            'sau'           => $usertv['money'] + $rowvn['tongtien'],
                            'note'          => "Hoàn tiền tạo website mã #".$rowvn['id_code'],
                            'tongtien'      => $rowvn['tongtien'],
                            'time'          => gettime()
                        ]);
                    }
                }

                $update = $DMH->update("lichsutaoweb", array(
                    'username'          => $username,
                    'taikhoan'          => $tk,
                    'matkhau'           => $mk,
                    'id_code'           => $id_code,
                    'moneygiahan'       => $giahanweb,
                    'buoc'              => $status,
                    'ngayduyet'         => $timeduyet,
                    'thangmua'          => $thangmua,
                    'ngayhethan'        => $thanghethan,
                    'tongtien'          => $tongtien,
                    'note'              => $note,
                    'linklogin'         => $linklogin,
                    'tkhs'              => $tkhs,
                    'mkhs'              => $mkhs
                ), " `id` = '$id_web' ");
                if($update) {
                    msg("success","Đã cập nhật dữ liệu thành công", "", 1000);
                } else {
                    msg("error","Lưu bị lỗi. Hãy kiểm tra lại hệ thống");
                }
            // xử lý nếu hác tên miền
            } else {
                $rowV2 = $DMH->get_row(" SELECT * FROM `domainclf` WHERE `accountid` IS NOT NULL AND `status` = 'ON' ");
                if(!$rowV2) {
                    msg("error","Chưa có tài khoản CLF nào hoạt động. Hãy thêm nào vào DATABASE");
                }
                $tuan = explode('.',$tenmien);
                if(empty($tuan[1])) {
                    msg("error","Tên miền bạn nhập không hợp lệ.");
                }
                if(preg_match('/[#@! $%^&*()+=\-\[\]\';,.\/{}|":<>?~\\\\]/', $ten)) {
                    msg("error","Tên miền của bạn không dược chứa kí tự lạ");
                }
                $domain      = $DMH->get_row(" SELECT * FROM `lichsutaoweb` WHERE `tenmien` = '$tenmien' AND `buoc` = '4'");
                if(!$domain) {
                    $post_data = [
                        'account' => ['id'=>$rowV2['accountid']],
                        'name' => $tenmien,
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
                        'X-Auth-Key: '.$rowV2['auth'].'',
                        'X-Auth-Email: '.$rowV2['email'].'',
                        'Content-Type: application/json'
                        ),
                            ));
                    $response = curl_exec($curl);
                    curl_close($curl);
                    $rowvn = json_decode($response, true);
                    if(isset($rowvn['success'])) {
                        if($rowvn['success'] == false) {
                            msg("error","Đã xảy ra lỗi trong quá trình thêm tên miền này", "", 5000);
                        } else {
                            $update = $DMH->update("lichsutaoweb", array(
                                'domainclf'         => $rowV2['id'],
                                'tenmien'           => $tenmien,
                                'id_code'           => $id_code,
                                'taikhoan'          => $tk,
                                'matkhau'           => $mk,
                                'buoc'              => $status,
                                'moneygiahan'       => $giahanweb,
                                'thangmua'          => $thangmua,
                                'domainid'          => $rowvn['result']['id'], // id có thểm xem
                                'id_addmien'        => $rowvn['result']['owner']['id'], // id bị ẩn, dùng cho api
                                'statusclf'         => 'pending',
                                'ngayduyet'         => $timeduyet,
                                'ngayhethan'        => $thanghethan,
                                'quyenedit'         => $quyenedit,
                                'sttdomain'         => 'ON',
                                'note'              => $note,
                                'linklogin'         => $linklogin,
                                'tkhs'              => $tkhs,
                                'mkhs'              => $mkhs
                            ), " `id` = '$id_web' ");
                            if($update) {
                                msg("success","Đã cập nhật dữ liệu thành công. Hãy trỏ NameServer vào hệ thống", "", 2000);
                            } else {
                                msg("error","Lưu bị lỗi. Hãy kiểm tra lại hệ thống");
                            }
                        }
                    } else {
                        msg("error","Đã xảy ra lỗi trong quá trình thêm tên miền này");
                    }
                } else {
                    msg("error","Tên miền này đã tồn tại trong hệ thống và đang hoạt động");
                }
            }
        }

        // xử lý cộng tháng
        if($_POST['type'] == 'Cong') {
            $thang  = check_string($_POST['thangcong']);
            $trutien = check_string($_POST['trutien']); // DỮ LIỆU GỬI LÊN DẠNG: YES OR NO
            if($thang <= 0 ) {
                msg("error", "Số tháng cộng không hợp lệ");
            }
            
            if($rowvn['buoc'] != 4) {
                msg("error", "Trạng thái phải hoạt động mới có thể cộng");
            }
            if($thang % 3 != 0) {
                msg("error", "Số tháng muốn cộng phải chia hết cho 3");
            }
            if($trutien == 'YES') {
                $sotien = ($thang/3)*$rowvn['moneygiahan'];
                if($usertv['money'] < $sotien) {
                    msg("error", "Số tiền của khách không đủ để gia hạn ".$thang." tháng");
                }
                $DMH->tru("users", "money", $sotien, " `username` = '".$rowvn['username']."' ");
                $create = $DMH->insert("biendongsodu", [
                    'username'      => $rowvn['username'],
                    'note'          => 'Gia hạn website '.$rowvn['tenmien'].' thêm '.$thang.' tháng',
                    'truoc'         => $usertv['money'],
                    'sau'           => $usertv['money'] - $sotien,
                    'tongtien'      => $sotien,
                    'time'          => gettime()
                ]);
                // chỉ có bước 4 và 5 mới có thể add gia hạn
                if(in_array($rowvn['buoc'], [4,5])) {
                    $DMH->insert("lichsugiahan", [
                        'username'  => $rowvn['username'],
                        'id_web'    => $id_web,
                        'tenmien'   => $rowvn['tenmien'],
                        'tongtien'  => $sotien,
                        'thoigian'  => $thang, 
                        'time'      => gettime(),
                        'status'    => 'thanhcong'
                    ]);
                }
            }
            $cong = $DMH->cong("lichsutaoweb", "ngayhethan", $thang*$onethang, " `username` = '".$rowvn['username']."' ");
            if($cong) {
                msg("success","Đã cộng thời gian thành công!", "", 2000);
            } else {
                msg("error", "Thêm dữ liệu lỗi!");
            }
        }

        // xử lý trừ tháng
        if($_POST['type'] == 'Tru') {
            $thang = check_string($_POST['thangtru']);
            if($thang <= 0 ) {
                msg("error", "Số tháng trừ không hợp lệ");
            }
            if($rowvn['buoc'] != 4) {
                msg("error", "Trạng thái phải hoạt động mới có thể cộng");
            }
            if($thang % 3 != 0) {
                msg("error", "Số tháng muốn trừ phải chia hết cho 3");
            }
            $tru = $DMH->tru("lichsutaoweb", "ngayhethan", $thang*$onethang, " `username` = '".$rowvn['username']."' ");
            if($tru) {
                msg("success","Đã trừ thời gian thành công!", "", 2000);
            } else {
                msg("error", "Thêm dữ liệu lỗi!");
            }
        }

    } else {
        msg("error", "Vui lòng đăng nhập để truy cập admin","/",1000);
    }
