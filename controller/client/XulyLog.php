<?php

define("IN_SITE", true);
require_once("../../core/config.php");
require_once("../../core/function.php");
require_once('../../class/class.smtp.php');
require_once('../../class/PHPMailerAutoload.php');
require_once('../../class/class.phpmailer.php');
if(isset($_POST['type'])) {
    if($DMH->get_row(" SELECT * FROM `blockip` WHERE `ip` = '".myip()."' "))
    {
    }
    // ĐĂNG NHẬP
    if($_POST['type'] == 'Login')
    {
        if(isset($_COOKIE['token']))
        {
            msg_error('Bạn đã đăng nhập trước đó rồi', BASE_URL(''), 1000);
        }
        $tk = check_string($_POST['taikhoan']);
        $mk = check_string(md5($_POST['matkhau']));
        if(isset($_SESSION['lanspam']))
        {
            if($_SESSION['lanspam'] >= 5)
            {
                $giay = $_SESSION['timeblock'] - time();
                if($giay < 1)
                {
                    $_SESSION['lanspam'] = 0;
                    msg_error2("Vui lòng bấm đăng nhập lại 1 lần nữa");
                }
                else
                {
                    msg_error2("Vui lòng thử lại sau ".$giay."s");
                }
            }
            
        }
        if(!$tk || !$mk)
        {
            msg_error2("Vui lòng không để trống thông tin");
        }
        if(strlen($tk) < 4)
        {
            msg_error2("Tài khoản của bạn phải trên 4 kí tự");
        }
        if(strlen($mk) < 5)
        {
            msg_error2("Mật khẩu của bạn phải trên 4 kí tự");
        }
        $CheckUser = $DMH->get_row(" SELECT * FROM `users` WHERE `username` = '$tk' ");
        if(!$CheckUser)
        {
            msg_error2('Tên đăng nhập không tồn tại');
        }
        if($CheckUser['banned'] == 'OFF')
        {
            msg_error2('Bạn bị đình chỉ vô thời hạn. Hãy liên hệ với BQT');
        }
        if(!$DMH->get_row(" SELECT * FROM `users` WHERE `username` = '$tk' AND `password` = '$mk' "))
        {
            $_SESSION['timeblock'] = time() + 30;
            if(empty($_SESSION['lanspam'])) {
                $_SESSION['lanspam'] = 1;
            }
            else
            {
                $_SESSION['lanspam'] = $_SESSION['lanspam'] + 1;
            }
            msg_error2('Mật khẩu đăng nhập không chính xác');
        }
        $check_login = $DMH->get_row(" SELECT * FROM `users` WHERE `username` = '$tk' AND `password` = '$mk' ");
        if($check_login)
        {
            /*THÔNG BÁO CHO THÀNH VIÊN*/
            if($check_login['online'] == 'ONLINE') {
                pusher($tk, "error", "Tài khoản của bạn vừa được đăng nhập trên thiết bị có IP là: ".myip()." Vì thế bạn sẽ bị đăng xuất");
            }
            $token = randomtoken();
            $token_api = randomtoken2();
            setcookie('token', $token, time() + 2678400, '/');
            $DMH->update("users", array(
                'tokenlog'      => $token,
                'timeon'        => gettime(),
                'ip'            => myip(),
                'user_agent'    => $_SERVER['HTTP_USER_AGENT']
            ), " `username` = '$tk'");
            if(empty($check_login['token_api']))
            {
                $DMH->update("users", array(
                    'token_api'  => strtoupper($token_api),
                ), " `username` = '$tk' ");
            }
            msg_success('Đăng nhập vào tài khoản thành công', den(), 1000);
        }

    }
    
    
    if($_POST['type'] == 'Register')
    {
        if(isset($_COOKIE['token']))
        {
            msg_error('Bạn đã đăng nhập trước đó rồi', BASE_URL(''), 1000);
        }
        $email      = check_string($_POST['email']);
        $user       = check_string($_POST['taikhoan']);
        $pass       = check_string($_POST['matkhau']);
        $pass2      = check_string($_POST['matkhau2']);
        if(!$email || !$user || !$pass  )
        {
            msg_error2('Vui lòng không để trống thông tin');
        }
        if(strlen($user) < 4)
        {
            msg_error2("Tài khoản của bạn phải trên 4 kí tự");
        }
        if(strlen($pass) < 5)
        {
            msg_error2("Mật khẩu của bạn phải trên 4 kí tự");
        }
        if(check_email($email) != 'True')
        {
            msg_error2("Email của bạn không hợp lệ để đăng ký");
        }
        if(check_username($user) != 'True')
        {
            msg_error2("Tài khoản của bạn không được chứa kí tự lạ");
        }
        if($pass != $pass2)
        {
            msg_error2("Nhập lại mật khẩu không đúng");
        }
        if($DMH->get_row(" SELECT * FROM `users` WHERE `username` = '$user' "))
        {
            msg_error2('Tên đăng nhập đã tồn tại!');
        }
        if($DMH->get_row(" SELECT * FROM `users` WHERE `email` = '$email' "))
        {
            msg_error2('Email đã tồn tại!');
        }
        if($DMH->num_rows(" SELECT * FROM `users` WHERE `ip` = '".myip()."' ") >= 10)
        {
            msg_error2('Bạn đã đạt giới hạn tạo tài khoản');
        }
        $token = randomtoken();
        $token_api = randomtoken2();
        $timegio = time();
        $create = $DMH->insert("users", [
            'username'          => $user,
            'password'          => md5($pass),
            'email'             => $email,
            'money'             => 0,
            'total_money'       => 0,
            'level'             => 'member',
            'tokenlog'          => $token,
            'token_api'         => strtoupper($token_api),
            'timereg'           => gettime(),
            'timereg2'          => $timegio,
            'timeon'            => gettime(),
            'online'            => 'ONLINE',
            'ip'                => myip(),
            'user_agent'        => $_SERVER['HTTP_USER_AGENT']
        ]);
        if($create)
        {
            setcookie('token', $token, time() + 2678400, '/');
            if($DMH->num_rows(" SELECT * FROM `users` WHERE `ip` = '".myip()."' ") <= 2) {
            $magg = strtoupper(substr(md5($timegio), 0 , 7));
            $bcc = 'dienmayhieu.com';
            $subject = 'Tặng mã giảm giá 10% cho bạn tại dienmayhieu.com';
            $hoten = 'Điện Máy Hiếu';
            
            $noi_dung = '<html xmlns="http://www.w3.org/1999/xhtml" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:v="urn:schemas-microsoft-com:vml"><head><!--[if gte mso 9]><xml><o:OfficeDocumentSettings><o:AllowPNG/><o:PixelsPerInch>96</o:PixelsPerInch></o:OfficeDocumentSettings></xml><![endif]-->
	<meta content="width=device-width" name="viewport">
	<style>
	/* #### Mobile Phones Portrait #### */
	/* #### iPhone 4+ Portrait or Landscape #### */
	@media only screen and (max-width: 480px){
		table[class=contentInner] {width:100% !important;padding:0px;margin:0px;}
		img[class=zpImage]{width:260px !important;max-width: 360px !important;text-align:center;margin:0px;padding:0px} 
		body, table, td, p, li,div,span, blockquote{-webkit-text-size-adjust:none !important;margin:0px auto;line-height:1.7}
		table[class=zpImageCaption]{text-align:left;}
		table[class=cols]{width:100% !important;max-width:100% !important;text-align:left;}
		table[class=zpcolumns] {text-align:left;margin:0px;} 
		table[class=zpcolumn] {text-align:left;margin:0px;} 
		table[class=zpAlignPos]{width:100%;text-align:left;margin:0px;} 
		td[class=txtsize]{font-size:18px !important;}
		td[class=paddingcomp]{ padding-left: 15px !important; padding-right: 15px !important }
		td[class=bannerimgpad]{padding:0px !important;}
		span[class=txtsize]{font-size:18px !important;}
		img[size = "B"]{width: 100% !important;max-width:100% !important;margin: 0px !important;padding:0px !important;}
		img[size = "F"]{width: 100% !important;max-width:100% !important;margin: 0px !important;padding:0px !important;}
		img[size = "S"]{width:105px !important; height:auto; margin:0px auto !important;padding:0px !important;}
		img[size = "M"]{width:277.869px !important;height:auto;margin:0px auto !important;padding:0px !important;}
		h1{
			font-size:28px !important;
			line-height:100% !important;
		}
		h2{
			font-size:24px !important;
			line-height:100% !important;
		}
		h3{
			font-size:20px !important;
			line-height:100% !important;
		}
		h4{
			font-size:18px !important;
			line-height:100% !important;
		}
		}		
		@media only screen and (max-width: 480px){
		.zpImage{
			height:auto !important;
			width:100% !important;
		}}
		@media only screen and (max-width: 480px){
		.contentInner,.cols,.zpAlignPos{
			width:100% !important;
			max-width:100% !important;
		}}
		@media only screen and (max-width: 480px){
		.paddingcomp{
			padding-left: 15px !important;
			padding-right: 15px !important; 
		}
		.bannerimgpad{
			padding:0px !important;
		}
	}
		@media screen and (max-width: 480px)
        {
                .tmplheader,.tmplfooter{width:100% !important;max-width:400px !important;margin:0px auto;text-align:center;}
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
<meta content="text/html;charset=UTF-8" http-equiv="Content-Type"></head><body bgcolor="#f3f8fa" style="margin:0; padding:0;font-family:Arial, Helvetica, sans-serif; font-size:12px; color:#000000;"><center> 
<div class="zppage-container">                                                          
		<table bgcolor="#f3f8fa" border="0" cellpadding="0" cellspacing="0" class="contentOuter" id="contentOuter" style="background-color:#f0f0f0;background-color:#f3f8fa;font-size:12px;text-align:center;border:0px;padding:0px;border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;" width="100%"> 
			<tbody><tr> 
				<td style="border:0px;padding:0px;border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;">&nbsp;</td>
				<td align="center" style="border:0px;padding:0px;border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;">
					<table bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" class="contentInner" id="contentInner" style="border-collapse:collapse; border:0px;font-size:12px;background-color:#ffffff;background-color:#ffffff;width:600px;margin:0px auto;border:0px;" width="600"> 
					<tbody><tr> 
					<td style="border:0px;padding:0px;" valign="top">
					 	<a name="Top" style="text-decoration:underline;"></a>
    <div baseposition="pos_YrobDQg7SAqnBt5RUo4dhQ" class="zpcontent-wrapper" id="page-container">
    <table border="0" cellpadding="0" cellspacing="0" id="page-container" style="font-size:12px;border:0px;padding:0px;border-collapse:collapse; mso-table-lspace:0pt;mso-table-rspace:0pt;text-decoration:none !important;" width="100%">
<tbody><tr><td class="txtsize" id="elm_1604562245798" style="border:0px;padding:0px 0px;border-collapse:collapse;" valign="top">






























	 

	          <div class="zpelement-wrapper spacebar" id="elm_1604562245798" style=";word-wrap:break-word;overflow:hidden;background-color:#ffffff;">
					
					 <table bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" class="zpAlignPos" style="padding:0px;border:0px;border-collapse:collapse; mso-table-lspace:0pt;font-size:5px; mso-table-rspace:0pt;word-break:break-word;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;height:40px;" width="100%">

	                        <tbody><tr><td style="padding:0px;border:0px;font-size:5px;height:40px;border-top:none none none;border-bottom:none none none;">

	                        &nbsp;&nbsp;&nbsp;

	                        </td></tr>

	                </tbody></table>

	          </div>

</td></tr>
<tr><td class="txtsize" id="elm_1627304524746" style="border:0px;padding:0px 0px;border-collapse:collapse;" valign="top">

	    	<table bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" style="font-size:12px;border:0px;padding:0px;width:100%;border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;word-break:break-word;background-color:#ffffff;">
	    <tbody><tr>
		<td class="txtsize" style="border:0px;padding:0px 0px;border-top:none none none ;border-bottom:none none none;">
            	<div class="zpelement-wrapper image" coupcmp id="elm_1627304524746" prodcmp style=";word-wrap:break-word;overflow:hidden;padding:0px;background-color:#ffffff;">
			<div>
		        <table align="center" border="0" cellpadding="0" cellspacing="0" class="zpAlignPos" style="font-size:12px;text-align:left;padding:0px;border:0px;border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;word-break:break-word;width:100%;text-align:center;">
            		<tbody><tr><td class="paddingcomp" style="border:0px;padding:7px 15px;text-align:center;padding-top:7px;padding-bottom:7px;padding-right:15px;padding-left:15px;">
			<img align="center" alt="https://campaign-image.com/zohocampaigns/133052000002835550_zc_v63_1627304672589_summer_sale_01_logo.png" class="zpImage" height="auto" hspace="0" size="S" src="'.$DMH->site('logo').'" style="width:150px;height:autopx;max-width:150px !important;border:0px;text-align:center;" vspace="0" width="150">
			</td></tr>
			</tbody></table>
		</div>
            </div>
            </td></tr></tbody></table>
</td></tr>
<tr><td class="txtsize" id="elm_1627304621729" style="border:0px;padding:0px 0px;border-collapse:collapse;" valign="top">






























	 

	          <div class="zpelement-wrapper spacebar" id="elm_1627304621729" style=";word-wrap:break-word;overflow:hidden;background-color:#ffffff;">
					
					 <table bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" class="zpAlignPos" style="padding:0px;border:0px;border-collapse:collapse; mso-table-lspace:0pt;font-size:5px; mso-table-rspace:0pt;word-break:break-word;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;height:10px;" width="100%">

	                        <tbody><tr><td style="padding:0px;border:0px;font-size:5px;height:10px;border-top:none none none;border-bottom:none none none;">

	                        &nbsp;&nbsp;&nbsp;

	                        </td></tr>

	                </tbody></table>

	          </div>

</td></tr>
<tr><td class="txtsize" id="elm_1604484219453" style="border:0px;padding:0px 0px;border-collapse:collapse;" valign="top">

	    	<table bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" style="font-size:12px;border:0px;padding:0px;width:100%;border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;word-break:break-word;background-color:#ffffff;">
	    <tbody><tr>
		<td class="txtsize" style="border:0px;padding:0px 0px;border-top:none none none ;border-bottom:none none none;">
            	<div class="zpelement-wrapper image" coupcmp id="elm_1604484219453" prodcmp style=";word-wrap:break-word;overflow:hidden;padding:0px;background-color:#ffffff;">
			<div>
		        <table align="center" border="0" cellpadding="0" cellspacing="0" class="zpAlignPos" style="font-size:12px;text-align:left;padding:0px;border:0px;border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;word-break:break-word;width:100%;text-align:center;">
            		<tbody><tr><td class="paddingcomp" style="border:0px;padding:7px 15px;text-align:center;padding-top:7px;padding-bottom:7px;padding-right:15px;padding-left:15px;">
			<img align="center" alt="https://campaign-image.com/zohocampaigns/174213000013650240_zc_v30_1604568465875_welcome26_banner.png" class="zpImage" height="auto" hspace="0" size="M" src="https://lh3.googleusercontent.com/Z5Tai0vvBXR2l3REYr3xyV9y5L7gU6hVqRg34tS7-wGJ5FRXpVrkGDu94nTDqJD2BCIFbdVwB-MFxtTL6ZlUvCbH6IUz4YwUbUKRxnlHTDwBkUWleWOV-2mnlQvY7bgLsz2zRAoOIf2R844Wmgx83IOiKlzKdTT_pU-XmWl1gYt8RmrA37QmGnYhQDKHUOHN6Ir8ohJLAQdy7b8RPGYzyqcxGz-GXYOmLRj03zu_VIR7kubKxQ48HJpMLanzKbv9XXEBb5ca_ADihGzN8H30hn5s1FucRo7e7l4uCF1mJGmfXM5gst11YezkpWkQtnaNk6DynkNG9VMl5o4BQHP81HO7bVAjIanzwg26OMoLFPcg6zN_OQ9tuIYGIeMnyaVjj9nq1LIzsVBMU_NN_-VR6V-d8yruDG6_snzx4TSUgJN6j82oyXQWuk615a6OhtBIjNZEx-IJXo_mj-exruzfxQsvWHUFCg7Ogvt1cNbXS5KIa2v0hoIjk8iH3HhoCjUYGUkZU11ArLSdRoEckgUfWU5ElhgA358DAquXrhSefpGHQ50prPRjnYkAi2cpBrcTquwSz8YoolwRXccvkO7Kc_Dud9q5LjmhODMWEzVEE1T7cfvnPgaxPmqQvnhrUm6-JgiQqEz-_2MoVchAIfHwgW7ezbuCJW1UmeAA3WlrBsfgZimqW2gL7TedfXKB-R0OQcBlkzFcojRX8kJykP8nZu34fJIwV-AdybmtwYBZLuYMXkPpnhCZvMKmk4sThJxrenEPjc9KcuEZ5qgqiOd6oDp_cwHuXWcyNPBq6AtbhF-SmRKn8OxvHIzKnp6MoDI8E2rv7u6MLSHIuCJ9_OvuMFeDXk1DpdlJDvGZE21-CYlZgk0C8BJJp50WdEit3SqyI_Rhlq_LC5EOk8du_kO3YtNDRHqhJUMZehP0nXykxOJL8CJGG7UC8kefeqCmiOVJ9Cby2THWVm7tcLmNEabnlRjj5kE85tsm2qFfDh8uhGjSxs49786sG1apWvLtlJr_pD7lruW1sgLYB2bonA70uMntCgA3tYC0EDyES-rcxWoghgPSwIDh7oWDQ4m9hO5tCpqUgqEKVigQL1egXUwNS1h9Hjzra51abtWFzme5K0h0e4-iiLBS11uPuF8dck7Hri1TY6BNFXlbUhQFSO88oTShz8LTyKXrnEAilFluvUULEozJYSelM35fhQXmXaewe1oOHqrVgw1spH1AAMBKPVCu3wCiAyq1Nw9qIpaRi-dfHWYh=w362-h363-no?authuser=6" style="width:299px;height:autopx;max-width:299px !important;border:0px;text-align:center;" vspace="0" width="299">
			</td></tr>
			</tbody></table>
		</div>
            </div>
            </td></tr></tbody></table>
</td></tr>
<tr><td class="txtsize" id="elm_1604484271808" style="border:0px;padding:0px 0px;border-collapse:collapse;" valign="top">

          <div class="zpelement-wrapper" id="elm_1604484271808" style=";word-wrap:break-word;overflow:hidden;padding-right:0px;background-color:#ffffff;">
	    	<table bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" class="zpAlignPos" style="font-size:12px;padding:0px;border:0px;border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;word-break:break-word;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" width="100%">
	  	
            <tbody><tr><td class="paddingcomp" style="border:0px;padding:7px 15px;line-height:29pt;border-top:0px none ;   border-bottom:0px none ;padding-top:20px;padding-bottom:7px;padding-right:30px;padding-left:50px;">
             	    		<div componentbgcolor="#ffffff" componentlineheight="29pt" componentpaddingbottom="7px" componentpaddingleft="50px" componentpaddingright="30px" componentpaddingtop="20px" style="background-color: rgb(255, 255, 255);"><p align="center" style="font-family:Arial,verdana;font-size:12px; color:#000000;padding:0px;margin: 0;line-height: 29pt; text-align: center;"><font color="#00032b" style="line-height: 29pt;"><b style><font face="Tahoma, Geneva, sans-serif" style="font-size: 28pt;"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Cảm ơn bạn đ&atilde; đăng k&yacute;</font></font></font></b></font></p></div>
			</td></tr>
		</tbody></table>
       	  </div>
</td></tr>
<tr><td class="txtsize" id="elm_1604484315288" style="border:0px;padding:0px 0px;border-collapse:collapse;" valign="top">

          <div class="zpelement-wrapper" id="elm_1604484315288" style=";word-wrap:break-word;overflow:hidden;padding-right:0px;background-color:#ffffff;">
	    	<table bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" class="zpAlignPos" style="font-size:12px;padding:0px;border:0px;border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;word-break:break-word;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" width="100%">
	  	
            <tbody><tr><td class="paddingcomp" style="border:0px;padding:7px 15px;line-height:21pt;border-top:0px none ;   border-bottom:0px none ;padding-top:7px;padding-bottom:30px;padding-right:15px;padding-left:50px;">
             	    		<div componentbgcolor="#ffffff" componentlineheight="21pt" componentpaddingbottom="30px" componentpaddingleft="50px" componentpaddingright="15px" componentpaddingtop="7px" style="background-color: rgb(255, 255, 255);"><p align="left" style="line-height:1.7;font-family:Arial,verdana;font-size:12px; color:#000000;padding:0px;margin: 0;text-align: left; line-height: 21pt;"><font color="#252525" face="Arial, Helvetica" style="font-size: 12pt; line-height: 21pt;"><span></span></font></p><p align="center" style="font-family:Arial,verdana;font-size:12px; color:#000000;padding:0px;margin: 0;line-height: 19pt; text-align: center;"><font style="line-height: 21pt;"><font color="#222222" style><font style><font face="Tahoma, Geneva, sans-serif" style="font-size: 30pt;"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">'.$user.'!</font></font></font></font></font></font></p><p style="margin: 0;padding: 0px;font-family:Arial, Helvetica, sans-serif; color:#000000;line-height:1.7;font-family:Arial,verdana;font-size:12px;padding:0px;"></p></div>
			</td></tr>
		</tbody></table>
       	  </div>
</td></tr>
<tr><td class="txtsize" id="elm_1663129160553" style="border:0px;padding:0px 0px;border-collapse:collapse;" valign="top">






























	 

	          <div class="zpelement-wrapper spacebar" id="elm_1663129160553" style=";word-wrap:break-word;overflow:hidden;background-color:#ffffff;">
					
					 <table bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" class="zpAlignPos" style="padding:0px;border:0px;border-collapse:collapse; mso-table-lspace:0pt;font-size:5px; mso-table-rspace:0pt;word-break:break-word;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;height:20px;" width="100%">

	                        <tbody><tr><td style="padding:0px;border:0px;font-size:5px;height:20px;border-top:none none none;border-bottom:none none none;">

	                        &nbsp;&nbsp;&nbsp;

	                        </td></tr>

	                </tbody></table>

	          </div>

</td></tr>
<tr><td class="txtsize" id="elm_1604484330291" style="border:0px;padding:0px 0px;border-collapse:collapse;" valign="top">

          <div class="zpelement-wrapper" id="elm_1604484330291" style=";word-wrap:break-word;overflow:hidden;padding-right:0px;background-color:#ffffff;">
	    	<table bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" class="zpAlignPos" style="font-size:12px;padding:0px;border:0px;border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;word-break:break-word;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" width="100%">
	  	
            <tbody><tr><td class="paddingcomp" style="border:0px;padding:7px 15px;line-height:21pt;border-top:0px none ;   border-bottom:0px none ;padding-top:7px;padding-bottom:7px;padding-right:15px;padding-left:50px;">
             	    		<div componentbgcolor="#ffffff" componentlineheight="21pt" componentpaddingbottom="7px" componentpaddingleft="50px" componentpaddingright="15px" componentpaddingtop="7px" style="background-color: rgb(255, 255, 255);"><p align="left" style="line-height:1.7;font-family:Arial,verdana;font-size:12px; color:#000000;padding:0px;margin: 0;text-align: left; line-height: 21pt;"><font color="#5f5f5f" face="Arial, Helvetica" style="font-size: 12pt; line-height: 21pt;"><span></span></font></p><p style="font-family:Arial,verdana;font-size:12px; color:#000000;padding:0px;margin: 0;line-height: 19pt; text-align: left;"><font style="line-height: 21pt;"><font color="#361212" face="Tahoma, Geneva, sans-serif" style="font-size: 13pt;"><b><span style="background-color: transparent;"></span><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Xin ch&agrave;o </font><span><span><font style="vertical-align: inherit;"><span>

<span>'.$user.'!</span></span></font></span></span></font></b></font><br></font></p><p style="margin: 0;padding: 0px;font-family:Arial, Helvetica, sans-serif; color:#000000;line-height:1.7;font-family:Arial,verdana;font-size:12px;padding:0px;"></p></div>
			</td></tr>
		</tbody></table>
       	  </div>
</td></tr>
<tr><td class="txtsize" id="elm_1663067831757" style="border:0px;padding:0px 0px;border-collapse:collapse;" valign="top">

          <div class="zpelement-wrapper" id="elm_1663067831757" style=";word-wrap:break-word;overflow:hidden;padding-right:0px;background-color:#ffffff;">
	    	<table bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" class="zpAlignPos" style="font-size:12px;padding:0px;border:0px;border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;word-break:break-word;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" width="100%">
	  	
            <tbody><tr><td class="paddingcomp" style="border:0px;padding:7px 15px;line-height:27pt;border-top:0px none ;   border-bottom:0px none ;padding-top:7px;padding-bottom:7px;padding-right:70px;padding-left:50px;">
             	    		<div componentbgcolor="#ffffff" componentlineheight="27pt" componentpaddingbottom="7px" componentpaddingleft="50px" componentpaddingright="70px" componentpaddingtop="7px" style="background-color: rgb(255, 255, 255); line-height: 27pt;"><p align="left" style="line-height:1.7;font-family:Arial,verdana;font-size:12px; color:#000000;padding:0px;margin: 0;text-align: left; line-height: 27pt;"><font color="#5f5f5f" face="Arial, Helvetica" style="font-size: 12pt; line-height: 27pt;"><span style="line-height: 27pt;"></span></font></p><p style="line-height:1.7;font-family:Arial,verdana;font-size:12px; color:#000000;padding:0px;margin: 0;text-align: left; line-height: 27pt;"><font color="#5f5f5f" face="Arial, Helvetica" style="font-size: 12pt; line-height: 27pt;"><span style="font-size: 12pt; color: rgb(95, 95, 95); font-family: Arial, Helvetica; background-color: transparent; line-height: 27pt;"></span><span style="line-height: 27pt;"></span></font></p><p style="line-height:1.7;font-family:Arial,verdana;font-size:12px; color:#000000;padding:0px;margin: 0;text-align: left; line-height: 27pt;"><font style="line-height: 27pt;"><font color="#222222" face="Tahoma, Geneva, sans-serif" style="font-size: 13pt; line-height: 27pt;"></font></font></p><span style="line-height: 27pt;"><p style="line-height:1.7;font-family:Arial,verdana;font-size:12px; color:#000000;padding:0px;margin: 0;text-align: left; line-height: 27pt;"><font face="Arial, Helvetica" style="line-height: 27pt;"><span style="font-size: 18.6667px; line-height: 27pt;"><font color="#333333"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">M&igrave;nh l&agrave; </font></font><b><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Ho&agrave;ng Tuấn</font></font></b><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">. Quản trị vi&ecirc;n của hệ thống </font></font></font><a alt="dienmayhieu.com" href="https://dmh.vn/" rel="noopener noreferrer" style="text-decoration:underline;color: rgb(0, 108, 251);" target="_blank" title="dienmayhieu.com"><font color="#006cfb" style="color:#006cfb;"><b><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">dienmayhieu.com</font></font></b></font></a><font color="#333333"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">, t&ocirc;i muốn gửi lời cảm ơn bạn khi đ&atilde; đăng k&yacute; t&agrave;i khoản tại website của ch&uacute;ng t&ocirc;i.</font></font></font></span></font></p><p style="line-height:1.7;font-family:Arial,verdana;font-size:12px; color:#000000;padding:0px;margin: 0;text-align: left; line-height: 27pt;"><font color="#333333" face="Arial, Helvetica" style="color: rgb(51, 51, 51); font-size: 14pt; line-height: 27pt;">&nbsp;</font></p><p style="line-height:1.7;font-family:Arial,verdana;font-size:12px; color:#000000;padding:0px;margin: 0;text-align: left; line-height: 27pt;"><font color="#333333" face="Arial, Helvetica" style="line-height: 27pt;"><span style="font-size: 18.6667px; line-height: 27pt;">Để cảm ơn, ch&uacute;ng t&ocirc;i xin tặng bạn m&atilde; giảm gi&aacute; <b>10%</b> dịch vụ tạo website. (<b>Chương tr&igrave;nh giảm gi&aacute; chỉ &aacute;p dụng cho t&agrave;i khoản mới đăng k&yacute;</b>)</span></font></p></span><p style="line-height:1.7;font-family:Arial,verdana;font-size:12px; color:#000000;padding:0px;margin: 0;text-align: left; line-height: 27pt;"><span style="color: rgb(34, 34, 34); font-family: Tahoma, Geneva, sans-serif; font-size: 13pt; line-height: 27pt;"></span></p><p style="font-family:Arial,verdana;font-size:12px; color:#000000;padding:0px;margin: 0;line-height: 27pt;"></p><p style="font-family:Arial,verdana;font-size:12px; color:#000000;padding:0px;margin: 0;line-height: 27pt;"></p></div>
			</td></tr>
		</tbody></table>
       	  </div>
</td></tr>
<tr><td class="txtsize" id="elm_1675404938859" style="border:0px;padding:0px 0px;border-collapse:collapse;" valign="top">

			
		 	        
		            
		  



















	<table bgcolor="transparent" cellpadding="0" cellspacing="0" height="48" style="font-size:12px;border:0px;border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;border:none;" width="100%">
        <tbody><tr><td class="paddingcomp" style="border:0px;padding:7px 15px;border-top:none none none;border-bottom:none none none;padding-top:13px;padding-bottom:13px;padding-right:15px;padding-left:15px;">
        <div class="zpelement-wrapper buttonElem" id="elm_1675404938859" style="overflow:hidden;word-wrap:break-word;">
	    <div class="zpAlignPos" style="text-align:center;">
                <table align="center" cellpadding="0" cellspacing="0" style="font-size:12px;border:none;padding:0px;border:0px;margin:0px auto;border-collapse:separate; mso-table-lspace:0pt; mso-table-rspace:0pt;">
                <tbody><tr>
                   <td align="center" class="txtsize" style="border:0px;padding:0px;color:#ffffff;font-family:Arial;text-align:center;border-radius:0px;text-align:center;cursor:pointer;">
                         <!--[if mso]>
					          <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" style="border-radius:0px;height:47px;v-text-anchor:middle;width:132px" arcsize="0%" strokecolor="#ffffff" strokeweight ="0px" fillcolor="#fe0713">
					          <v:stroke dashstyle="solid" />
					            <w:anchorlock/>
					            <center style="direction:ltr;color:#ffffff;font-family:Arial;font-size:16pt;">'.$magg.'</center>
					          </v:roundrect>
        				<![endif]-->
                        <a align="center" style="padding:0px 0px;background-color:#fe0713;width:132px;line-height:47px;font-size:16pt;direction:ltr;font-family:Arial;color:#ffffff;cursor:pointer;text-decoration:none;border-radius:0px;border:0px solid #ffffff;display:inline-block;mso-hide:all;text-align:center;" target="_blank">
							<font style="color:#ffffff;line-height:47px">
                     		   '.$magg.'
							</font>
                        </a>
                    </td>
                </tr>
                </tbody></table>
            </div>
	</div>
	</td></tr></tbody></table>
</td></tr>
<tr><td class="txtsize" id="elm_1675405020137" style="border:0px;padding:0px 0px;border-collapse:collapse;" valign="top">

          <div class="zpelement-wrapper" id="elm_1675405020137" style=";word-wrap:break-word;overflow:hidden;padding-right:0px;background-color:#ffffff;">
	    	<table bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" class="zpAlignPos" style="font-size:12px;padding:0px;border:0px;border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;word-break:break-word;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" width="100%">
	  	
            <tbody><tr><td class="paddingcomp" style="border:0px;padding:7px 15px;line-height:21pt;border-top:0px none ;   border-bottom:0px none ;padding-top:7px;padding-bottom:7px;padding-right:70px;padding-left:50px;">
             	    		<div componentbgcolor="#ffffff" componentlineheight="21pt" componentpaddingbottom="7px" componentpaddingleft="50px" componentpaddingright="70px" componentpaddingtop="7px" style="background-color: rgb(255, 255, 255);"><p align="left" style="line-height:1.7;font-family:Arial,verdana;font-size:12px; color:#000000;padding:0px;margin: 0;text-align: left; line-height: 21pt;"><font color="#5f5f5f" face="Arial, Helvetica" style="font-size: 12pt; line-height: 21pt;"><span></span></font></p><p style="font-family:Arial,verdana;font-size:12px; color:#000000;padding:0px;margin: 0;line-height: 19pt; text-align: left;"><font color="#5f5f5f" face="Arial, Helvetica" style="font-size: 12pt; line-height: 21pt;"><span style="font-size: 12pt; color: rgb(95, 95, 95); font-family: Arial, Helvetica; background-color: transparent;"></span><span></span></font></p><p style="font-family:Arial,verdana;font-size:12px; color:#000000;padding:0px;margin: 0;line-height: 19pt; text-align: left;"><font style="line-height: 21pt;"><font color="#222222" face="Tahoma, Geneva, sans-serif" style="font-size: 13pt;"></font></font></p><p style="font-family:Arial,verdana;font-size:12px; color:#000000;padding:0px;margin: 0;line-height: 19pt; text-align: left;"><b style="font-size: 18.6667px; font-family: Arial, Helvetica;"><font color="#fe0713">M&atilde; giảm gi&aacute;</font></b><font style="font-size: 18.6667px; font-family: Arial, Helvetica;"><b style><font color="#fe0713">&nbsp;kh&ocirc;ng giới hạn lượt sử dụng</font></b><font color="#333333">, nhưng chỉ tồn tại trong v&ograve;ng </font></font><b style="font-size: 18.6667px; font-family: Arial, Helvetica;"><font color="#fe0713">3 ng&agrave;y</font></b><font color="#333333" style="font-size: 18.6667px; font-family: Arial, Helvetica;">, cơ hội chỉ c&oacute; 1 m&agrave; th&ocirc;i. <b>M&atilde; giảm gi&aacute;</b> n&agrave;y kh&ocirc;ng thể chia sẽ cho người kh&aacute;c.</font><br></p><p align="center" style="font-family:Arial,verdana;font-size:12px; color:#000000;padding:0px;margin: 0;line-height: 19pt; text-align: center;"><font color="#fe0713" face="Arial, Helvetica"><span style="font-size: 18.6667px;"><b style>Ng&agrave;y hết hạn: '.date('d/m/Y - H:i:s', time() + 259200).'</b></span></font></p><p style="font-family:Arial,verdana;font-size:12px; color:#000000;padding:0px;margin: 0;line-height: 19pt; text-align: left;"><span style="color: rgb(34, 34, 34); font-family: Tahoma, Geneva, sans-serif; font-size: 13pt;"></span></p><p style="margin: 0;padding: 0px;font-family:Arial, Helvetica, sans-serif; color:#000000;line-height:1.7;font-family:Arial,verdana;font-size:12px;padding:0px;"></p><p style="margin: 0;padding: 0px;font-family:Arial, Helvetica, sans-serif; color:#000000;line-height:1.7;font-family:Arial,verdana;font-size:12px;padding:0px;"></p></div>
			</td></tr>
		</tbody></table>
       	  </div>
</td></tr>
<tr><td class="txtsize" id="elm_1663130004139" style="border:0px;padding:0px 0px;border-collapse:collapse;" valign="top">






























	 

	          <div class="zpelement-wrapper spacebar" id="elm_1663130004139" style=";word-wrap:break-word;overflow:hidden;background-color:#ffffff;">
					
					 <table bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" class="zpAlignPos" style="padding:0px;border:0px;border-collapse:collapse; mso-table-lspace:0pt;font-size:5px; mso-table-rspace:0pt;word-break:break-word;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;height:20px;" width="100%">

	                        <tbody><tr><td style="padding:0px;border:0px;font-size:5px;height:20px;border-top:none none none;border-bottom:none none none;">

	                        &nbsp;&nbsp;&nbsp;

	                        </td></tr>

	                </tbody></table>

	          </div>

</td></tr>
<tr><td class="txtsize" id="elm_1663067742490" style="border:0px;padding:0px 0px;border-collapse:collapse;" valign="top">

          <div class="zpelement-wrapper" id="elm_1663067742490" style=";word-wrap:break-word;overflow:hidden;padding-right:0px;background-color:#ffffff;">
	    	<table bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" class="zpAlignPos" style="font-size:12px;padding:0px;border:0px;border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;word-break:break-word;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" width="100%">
	  	
            <tbody><tr><td class="paddingcomp" style="border:0px;padding:7px 15px;line-height:21pt;border-top:0px none ;   border-bottom:0px none ;padding-top:7px;padding-bottom:7px;padding-right:16px;padding-left:50px;">
             	    		<div componentbgcolor="#ffffff" componentlineheight="21pt" componentpaddingbottom="7px" componentpaddingleft="50px" componentpaddingright="16px" componentpaddingtop="7px" style="background-color: rgb(255, 255, 255);"><p align="left" style="line-height:1.7;font-family:Arial,verdana;font-size:12px; color:#000000;padding:0px;margin: 0;text-align: left; line-height: 21pt;"><font color="#5f5f5f" face="Arial, Helvetica" style="font-size: 12pt; line-height: 21pt;"><span></span></font></p><p style="font-family:Arial,verdana;font-size:12px; color:#000000;padding:0px;margin: 0;line-height: 19pt; text-align: left;"><font color="#5f5f5f" face="Arial, Helvetica" style="font-size: 12pt; line-height: 21pt;"><span style="font-size: 12pt; color: rgb(95, 95, 95); font-family: Arial, Helvetica; background-color: transparent;"></span><span></span></font></p><p style="font-family:Arial,verdana;font-size:12px; color:#000000;padding:0px;margin: 0;line-height: 19pt; text-align: left;"><font style="line-height: 21pt;"><font color="#222222" face="Tahoma, Geneva, sans-serif" style="font-size: 13pt;">Tr&acirc;n trọng,</font></font></p><p style="font-family:Arial,verdana;font-size:12px; color:#000000;padding:0px;margin: 0;line-height: 19pt; text-align: left;"><font style="line-height: 21pt;"><font color="#222222" face="Tahoma, Geneva, sans-serif" style="font-size: 13pt;">Ho&agrave;ng Tuấn.</font></font></p><p style="margin: 0;padding: 0px;font-family:Arial, Helvetica, sans-serif; color:#000000;line-height:1.7;font-family:Arial,verdana;font-size:12px;padding:0px;"></p><p style="margin: 0;padding: 0px;font-family:Arial, Helvetica, sans-serif; color:#000000;line-height:1.7;font-family:Arial,verdana;font-size:12px;padding:0px;"></p></div>
			</td></tr>
		</tbody></table>
       	  </div>
</td></tr>
<tr><td class="txtsize" id="elm_1675406735993" style="border:0px;padding:0px 0px;border-collapse:collapse;" valign="top">

		
			 <div class="zpelement-wrapper divider" id="elm_1675406735993" style=";word-wrap:break-word;overflow:hidden;background-color:;">
			 <table bgcolor border="0" cellpadding="0" cellspacing="0" class="zpAlignPos" style="padding:0px;border:0px;border-collapse:collapse; mso-table-lspace:0pt;font-size:5px; mso-table-rspace:0pt;word-break:break-word;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;background-color:;" width="100%">
			 <tbody><tr><td class="txtsize" style="border:0px;padding:18px 7px 18px 7px;border-collapse:collapse;">
					<div class="divider" style="margin:0px;padding:0px;text-align:center;"><table align="center" border="0" cellpadding="0" cellspacing="0" class="dvdrtbl" style="border:0px;font-size: 0px;width:100%;margin:auto;border-collapse: initial;" width="100%"> <tbody><tr><td align="center" height="0" style="border:0px;border-top:2px solid red;border-bottom:none;border-left:none;border-right:none;font-size:0px;margin:0px;padding:0px;width:100%;" width="100%"> &nbsp; &nbsp; &nbsp; </td> </tr></tbody></table></div>
			</td></tr></tbody></table>
			</div>
</td></tr>
<tr><td class="txtsize" id="elm_1675406703376" style="border:0px;padding:0px 0px;border-collapse:collapse;" valign="top">

          <div class="zpelement-wrapper" id="elm_1675406703376" style=";word-wrap:break-word;overflow:hidden;padding-right:0px;background-color:#ffffff;">
	    	<table bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" class="zpAlignPos" style="font-size:12px;padding:0px;border:0px;border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;word-break:break-word;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" width="100%">
	  	
            <tbody><tr><td class="paddingcomp" style="border:0px;padding:7px 15px;line-height:21pt;border-top:0px none ;   border-bottom:0px none ;padding-top:7px;padding-bottom:7px;padding-right:16px;padding-left:50px;">
             	    		<div componentbgcolor="#ffffff" componentlineheight="21pt" componentpaddingbottom="7px" componentpaddingleft="50px" componentpaddingright="16px" componentpaddingtop="7px" style="background-color: rgb(255, 255, 255);"><p align="left" style="font-family:Arial,verdana;font-size:12px; color:#000000;padding:0px;margin: 0;line-height: 21pt; text-align: left;"><font color="#5f5f5f" face="Arial, Helvetica" style="font-size: 12pt; line-height: 21pt;"><span></span></font></p><p align="left" style="font-family:Arial,verdana;font-size:12px; color:#000000;padding:0px;margin: 0;line-height: 19pt; text-align: left;"><font color="#5f5f5f" face="Arial, Helvetica" style="font-size: 12pt; line-height: 21pt;"><span style="font-size: 12pt; color: rgb(95, 95, 95); font-family: Arial, Helvetica; background-color: transparent;"></span><span></span></font></p><p align="left" style="font-family:Arial,verdana;font-size:12px; color:#000000;padding:0px;margin: 0;line-height: 19pt; text-align: left;"><font color="#222222"><b style><font style="vertical-align: inherit;"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;"><font face="Arial, Helvetica" style="vertical-align: inherit; font-size: 12pt;">Th&ocirc;ng tin th&ecirc;m cho bạn:</font></font></font></font></b></font></p><p align="left" style="font-family:Arial,verdana;font-size:12px; color:#000000;padding:0px;margin: 0;line-height: 19pt; text-align: left;"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;"><font face="Arial, Helvetica" style="font-size: 12pt;"><font color="#222222">Trang web:  </font><a alt="https://dmh.com/" href="https://dmh.com/" rel="noopener noreferrer" style="text-decoration:underline;color: rgb(0, 108, 251);" target="_blank" title="https://dmh.com/"><font color="#006cfb" style="color:#006cfb;">https://dmh.com/</font></a></font></font></font></font></font></p><p align="left" style="font-family:Arial,verdana;font-size:12px; color:#000000;padding:0px;margin: 0;line-height: 19pt; text-align: left;"><span style="font-size: 12pt; color: rgb(34, 34, 34); font-family: Arial, Helvetica;">Hướng dẫn mua m&atilde; nguồn:&nbsp;</span><span style="font-size: 12pt; font-family: Arial, Helvetica;"><a alt="https://bit.ly/3RyRCXv" href="https://bit.ly/3RyRCXv" rel="noopener noreferrer" style="text-decoration:underline;color: rgb(0, 108, 251);" target="_blank" title="https://bit.ly/3RyRCXv"><font color="#006cfb" style="color:#006cfb;">https://bit.ly/3RyRCXv</font></a></span></p><p align="left" style="font-family:Arial,verdana;font-size:12px; color:#000000;padding:0px;margin: 0;line-height: 19pt; text-align: left;"><font face="Arial, Helvetica" style="font-size: 12pt;"><font color="#222222">Hướng dẫn tạo website: </font><a alt="https://bit.ly/3Rqw1R2" href="https://bit.ly/3Rqw1R2" rel="noopener noreferrer" style="text-decoration:underline;color: rgb(0, 108, 251);" target="_blank" title="https://bit.ly/3Rqw1R2"><font color="#006cfb" style="color:#006cfb;">https://bit.ly/3Rqw1R2</font></a></font></p><p align="left" style="font-family:Arial,verdana;font-size:12px; color:#000000;padding:0px;margin: 0;line-height: 19pt; text-align: left;"><font face="Arial, Helvetica" style="font-size: 12pt;"><font color="#222222">Hướng dẫn nạp tiền: </font><a alt="https://bit.ly/3JBPY5Q" href="https://bit.ly/3JBPY5Q" rel="noopener noreferrer" style="text-decoration: none; color: rgb(0, 108, 251);" target="_blank" title="https://bit.ly/3JBPY5Q"><font color="#006cfb" style="color:#006cfb;">https://bit.ly/3JBPY5Q</font></a></font></p><font color="#222222" face="Tahoma, Geneva, sans-serif">

</font><p align="left" style="line-height:1.7;font-family:Arial,verdana;font-size:12px; color:#000000;padding:0px;margin: 0;text-align: left;"></p><p align="left" style="line-height:1.7;font-family:Arial,verdana;font-size:12px; color:#000000;padding:0px;margin: 0;text-align: left;"></p><p align="left" style="line-height:1.7;font-family:Arial,verdana;font-size:12px; color:#000000;padding:0px;margin: 0;text-align: left;"></p></div>
			</td></tr>
		</tbody></table>
       	  </div>
</td></tr>
<tr><td class="txtsize" id="elm_1604485385412" style="border:0px;padding:0px 0px;border-collapse:collapse;" valign="top">

          <div class="zpelement-wrapper" id="elm_1604485385412" style=";word-wrap:break-word;overflow:hidden;padding-right:0px;background-color:#593961;">
	    	<table bgcolor="#593961" border="0" cellpadding="0" cellspacing="0" class="zpAlignPos" style="font-size:12px;padding:0px;border:0px;border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;word-break:break-word;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" width="100%">
	  	
            <tbody><tr><td class="paddingcomp" style="border:0px;padding:7px 15px;line-height:19pt;border-top:0px none ;   border-bottom:0px none ;padding-top:7px;padding-bottom:7px;padding-right:15px;padding-left:25px;">
             	    		<div componentbgcolor="#593961" componentpaddingbottom="7px" componentpaddingleft="25px" componentpaddingright="15px" componentpaddingtop="7px" style="background-color: rgb(89, 57, 97);"><p align="center" style="font-family:Arial,verdana;font-size:12px; color:#000000;padding:0px;margin: 0;line-height: 19pt; text-align: center;"><font color="#ffffff" face="Arial, Helvetica" style="font-size: 11pt;"><span data-doc-id="4504799000023434366" data-doc-type="writer" style="font-style: normal;"><span>Kết nối với ch&uacute;ng t&ocirc;i</span></span></font></p></div>
			</td></tr>
		</tbody></table>
       	  </div>
</td></tr>
<tr><td class="txtsize" id="elm_1614686702323" style="border:0px;padding:0px 0px;border-collapse:collapse;" valign="top">










	                <div class="zpelement-wrapper wdgts" id="elm_1614686702323" style="overflow:hidden;word-wrap:break-word;">
						<table bgcolor="#593961" border="0" cellpadding="0" cellspacing="0" class="zpAlignPos" style="padding:0px;border:0px;border-collapse:collapse; mso-table-lspace:0pt;font-size:5px; mso-table-rspace:0pt;word-break:break-word;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;  background-color:#593961;" width="100%">
							<tbody><tr><td style="padding:7px 15px;border:0px;font-size:5px;border-top:0px none ;border-bottom:0px none ;padding-top:7px;padding-bottom:7px;padding-right:15px;padding-left:15px;">
								<table align="center" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;font-size:12px;min-width: 100%;border: none;" width="100%"><tbody><tr><td align="center" style="border: none;padding: 0px;margin: 0px;" valign="top"> <table align="center" border="0" cellpadding="0" cellspacing="0" componentbgcolor="#593961" icontext="true" index="2" name="zcsclwdgts_alnmnt" style="font-size:12px;border-collapse: collapse;border: none;margin:auto;"><tbody><tr><td align="left" style="border: none;padding: 0px;margin: 0px;" valign="top"> <table align="center" border="0" cellpadding="0" cellspacing="0" name="zcsclwdgtscontainer" style="border-collapse:collapse;font-size:12px;border: none;"><tbody><tr><td align="left" style="border:none;padding:0px;margin:0px;" valign="top"><table align="left" border="0" cellpadding="0" cellspacing="0" style="font-size:12px;border-collapse: collapse;border: none;"><tbody><tr><td style="padding-right: 9px;padding-bottom: 9px;border:none;padding: 0px;margin: 0px;" valign="top"> <table border="0" cellpadding="0" cellspacing="0" style="font-size:12px;border-collapse: separate;border: none;"><tbody><tr><td align="left" style="padding:7px;padding-top: 0px;padding-right: 9px;padding-bottom: 0px;padding-left: 9px;border:none;" valign="middle"> <table align="left" border="0" cellpadding="0" cellspacing="0" style="font-size:12px;border-collapse: collapse;border: none;" width><tbody><tr><td align="center" name="sclwdgtimges" style="border: none;padding: 0px;margin: 0px;padding-bottom: 6px;" valign="middle"> <a href="https://www.facebook.com/Hotro.DMH" style="text-decoration:underline;display: block;font-size: 1px;" target="_blank"><img alt="Facebook" height="35" src="https://zohopublic.com/zohocampaigns/1060061000000053006_3_1675405893488_zcsclwgtfb2.png" style="border: 0px; margin: 0px; outline: none; text-decoration: none; width: 25px; height: 25px;" vspace="10" width="35"></a> </td></tr><tr><td align="center" name="sclwdgtcaptns" style="border:none;padding: 0px;margin: 0px;" valign="middle"><a href="https://www.facebook.com/Hotro.DMH" style="display: block;font-size: 1px;font-weight: normal;line-height: normal;text-align: center;text-decoration: none;" target="_blank"><p fntname="Arial" fntsze="8" style="font-family:Arial,verdana;font-size:12px; color:#000000;padding:0px;margin: 0;line-height: normal; font-family: Arial, Helvetica, sans-serif; color: rgb(27, 107, 189); font-size: 8pt;"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Facebook</font></font></p></a> </td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table></td><td align="left" style="border:none;padding:0px;margin:0px;" valign="top"><table align="left" border="0" cellpadding="0" cellspacing="0" style="font-size:12px;border-collapse: collapse;border: none;"><tbody><tr><td style="padding-right: 9px;padding-bottom: 9px;border:none;padding: 0px;margin: 0px;" valign="top"> <table border="0" cellpadding="0" cellspacing="0" style="font-size:12px;border-collapse: separate;border: none;"><tbody><tr><td align="left" style="padding:7px;padding-top: 0px;padding-right: 9px;padding-bottom: 0px;padding-left: 9px;border:none;" valign="middle"> <table align="left" border="0" cellpadding="0" cellspacing="0" style="font-size:12px;border-collapse: collapse;border: none;" width><tbody><tr><td align="center" name="sclwdgtimges" style="border: none;padding: 0px;margin: 0px;padding-bottom: 6px;" valign="middle"> <a href="https://www.youtube.com/@dienmayhieu" style="text-decoration:underline;display: block;font-size: 1px;" target="_blank"><img alt="YouTube" height="35" src="https://zohopublic.com/zohocampaigns/1060061000000053006_4_1675405893534_zcsclwgtyt2.png" style="border: 0px; margin: 0px; outline: none; text-decoration: none; width: 25px; height: 25px;" vspace="10" width="35"></a> </td></tr><tr><td align="center" name="sclwdgtcaptns" style="border:none;padding: 0px;margin: 0px;" valign="middle"><a href="https://www.youtube.com/@dienmayhieu" style="display: block;font-size: 1px;font-weight: normal;line-height: normal;text-align: center;text-decoration: none;" target="_blank"><p fntname="Arial" fntsze="8" style="font-family:Arial,verdana;font-size:12px; color:#000000;padding:0px;margin: 0;line-height: normal; font-family: Arial, Helvetica, sans-serif; color: rgb(27, 107, 189); font-size: 8pt;"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">YouTube</font></font></p></a> </td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table></td><td align="left" style="border:none;padding:0px;margin:0px;" valign="top"><table align="left" border="0" cellpadding="0" cellspacing="0" style="font-size:12px;border-collapse: collapse;border: none;"><tbody><tr><td style="padding-right: 9px;padding-bottom: 9px;border:none;padding: 0px;margin: 0px;" valign="top"> <table border="0" cellpadding="0" cellspacing="0" style="font-size:12px;border-collapse: separate;border: none;"><tbody><tr><td align="left" style="padding:7px;padding-top: 0px;padding-right: 9px;padding-bottom: 0px;padding-left: 9px;border:none;" valign="middle"> <table align="left" border="0" cellpadding="0" cellspacing="0" style="font-size:12px;border-collapse: collapse;border: none;" width><tbody><tr><td align="center" name="sclwdgtimges" style="border: none;padding: 0px;margin: 0px;padding-bottom: 6px;" valign="middle"> <a href="mailto:cskh@dmh.vn" style="text-decoration:underline;display: block;font-size: 1px;" target="_blank"><img alt="E-mail" height="35" src="https://zohopublic.com/zohocampaigns/1060061000000053006_5_1675405893567_zcsclwgtmail2.png" style="border: 0px; margin: 0px; outline: none; text-decoration: none; width: 25px; height: 25px;" vspace="10" width="35"></a> </td></tr><tr><td align="center" name="sclwdgtcaptns" style="border:none;padding: 0px;margin: 0px;" valign="middle"><a href="mailto:cskh@dmh.vn" style="display: block;font-size: 1px;font-weight: normal;line-height: normal;text-align: center;text-decoration: none;" target="_blank"><p fntname="Arial" fntsze="8" style="font-family:Arial,verdana;font-size:12px; color:#000000;padding:0px;margin: 0;line-height: normal; font-family: Arial, Helvetica, sans-serif; color: rgb(27, 107, 189); font-size: 8pt;"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">E-mail</font></font></p></a> </td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table>	
							</td></tr>
			            </tbody></table>	
		            </div>
</td></tr>
    </tbody></table>
</div>

					</td> 
					</tr> 
					</tbody></table>
				</td> 
				<td style="border:0px;padding:0px;border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;">&nbsp;</td>
			</tr> 
		</tbody></table> 
</div>
</center> 
</body></html>';
            
            $kq = sendCSM($email, $hoten, $subject, $noi_dung, $bcc);
            }
            msg_success('Đăng ký thành công! Chờ chuyển hướng ..', BASE_URL(''), 1000);
        }
        else
        {
            msg_error2("Lỗi cấu hình CSDL rồi");
        }
    }


    if($_POST['type'] == 'Doimatkhau')
    {
        if(empty($_COOKIE['token'])) {
            msg_error2('Vui lòng đăng nhập để tiếp tục');
        }
        $mkcu       = check_string($_POST['mkcu']);
        $mknew      = check_string($_POST['mknew']);
        $mknew2      = check_string($_POST['mknew2']);
        if(!$mkcu || !$mknew || !$mknew2)
        {
            msg_error2("Vui lòng nhâp đầy đủ thông tin");
        }
        $mkcu = md5($mkcu);
        $row = $DMH->get_row(" SELECT * FROM `users` WHERE `tokenlog` = '".$_COOKIE['token']."' AND `password` = '$mkcu' ");
        if(!$row)
        {
            msg_error2("Mật khẩu cũ nhập không chính xác");
        }
        if(strlen($mknew) < 5)
        {
            msg_error2("Mật khẩu của bạn phải trên 4 kí tự");
        }
        if($mknew != $mknew2)
        {
            msg_error2("Nhập lại mật khẩu không khớp");
        }
        if($mkcu == md5($mknew))
        {
            msg_error2("Mật khẩu mới không được trùng với mật khẩu cũ");
        }
        else
        {
            $DMH->update("users", array(
                'password'  => md5($mknew)
            ), " `tokenlog` = '".$_COOKIE['token']."' ");
            // unset($_COOKIE['token']);
            setcookie('token', null, -1, '/');
            msg_success('Đã thay đổi mật khẩu thành công. Vui lòng đăng nhập lại', BASE_URL('dang-nhap'), 1000);
        }
        
    }
    if($_POST['type'] == 'UpdateInfo')
    {
        if(empty($_COOKIE['token'])) {
            msg_error2('Vui lòng đăng nhập để tiếp tục');
        }
        $email      = check_string($_POST['email']);
        $zalo       = check_string($_POST['zalo']);
        if(!$email || !$zalo)
        {
            msg_error2("Vui lòng nhập đầy đủ thông tin");
        }
        if(check_email($email) != 'True') {
            msg_error2("Email của bạn không hợp lệ");
        }
        if(check_phone($zalo) != 'True') {
            msg_error2("Số zalo của bạn không hợp lệ");
        }
        $row = $DMH->get_row(" SELECT * FROM `users` WHERE `email` = '$email' AND `username` != '".$getUser['username']."' ");
        if($row)
        {
            msg_error2("Email đã đã có người khác sử dụng trên hệ thống");
        }
        $DMH->update("users", array(
            'email'  => $email, 
            'zalo'  => $zalo
        ), " `tokenlog` = '".$_COOKIE['token']."' ");
        msg_success('Đã cập nhật thành công', '', 1000);
        
    }

    if($_POST['type'] == 'LoginAdmin')
    {
        if($_COOKIE['token'])
        {
            $mk = check_string($_POST['password']);
            if(!$mk)
            {
                msg_error2("Vui lòng nhập mật khẩu để tiếp tục");
            }
            if(strlen($mk) < 5)
            {
                msg_error2("Độ dài mật khẩu không chính xác");
            }
            else
            {
                $mk = md5($mk);
                $rowlog = $DMH->get_row(" SELECT * FROM `users` WHERE `tokenlog` = '".$_COOKIE['token']."' AND `passwordc2` = '$mk' AND `level` = 'admin'");
                if(!$rowlog)
                {
                    msg_error2("Thông tin đăng nhập không chính xác. Vui lòng kiểm tra lại");
                }
                else
                {
                    $_SESSION['loginadmin'] = true;
                    msg_success("Đăng nhập thành công",BASE_URL('Admin'),1000);
                }
            }
        }
        else
        {
            msg_error("Vui lòng đăng nhập để truy cập admin","",1000);
        }
        
    }

    if($_POST['type'] == 'Doiapi')
    {
        if(empty($_COOKIE['token']))
        {
            msg_error2('Vui lòng đăng nhập để gửi liên hệ');
        }
        $token_api = randomtoken2();
        $SAVE = $DMH->update("users", array(
            'token_api'  => strtoupper($token_api)
        ), " `tokenlog` = '".$_COOKIE['token']."' ");
        if($SAVE)
        {
            msg_success("Đã đổi token API thành công", "", 100);
        }
        else
        {
            msg_error2("Đổi API lỗi. Không thể thực hiện bây giờ");
        }

    }
    
    if($_POST['type'] == 'Quenpass') {
        $email      = $_POST['email'];
        if(!$email){
            msg_error2("Vui lòng không để trống thông tin");
        }
        if(check_email($email) != 'True') {
            msg_error2("Email của bạn không hợp lệ để đăng ký");
        }
        $CheckUser = $DMH->get_row(" SELECT * FROM `users` WHERE `email` = '$email' AND `banned` = 'ON' ");
        if(!$CheckUser)
        {
            msg_error2("Email không tồn tại");
        }
        $guitoi = $email;
        $token = randomtoken();
        $DMH->update("users", array(
            'token_resetpas' => $token,
        ), " `email` = '$email' ");
        $bcc = 'dienmayhieu.com';
        $subject = 'Khôi phục mật khẩu tại DMH';
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
        
        <body bgcolor="#f0f0f0" style="margin:0; padding:0;font-family:Arial, Helvetica, sans-serif; font-size:12px; color:#000000;">
            <center>
                <div class="zppage-container">
                    <table bgcolor="#f0f0f0" border="0" cellpadding="0" cellspacing="0" class="contentOuter" id="contentOuter" style="background-color:#f0f0f0;background-color:#f0f0f0;font-size:12px;text-align:center;border:0px;padding:0px;border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;" width="100%">
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
                                                                    <td class="txtsize" id="elm_1584957744815" style="border:0px;padding:0px 0px;border-collapse:collapse;" valign="top">
        
                                                                        <div class="zpelement-wrapper" id="elm_1584957744815" style=";word-wrap:break-word;overflow:hidden;padding-right:0px;background-color:#f0f0f0;">
                                                                            <table bgcolor="#f0f0f0" border="0" cellpadding="0" cellspacing="0" class="zpAlignPos" style="font-size:12px;padding:0px;border:0px;border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;word-break:break-word;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" width="100%">
        
                                                                                <tbody>
                                                                                    <tr>
                                                                                        <td class="paddingcomp" style="border:0px;padding:7px 15px;line-height:19pt;border-top:0px none ;   border-bottom:0px none ;padding-top:25px;padding-bottom:15px;padding-right:50px;padding-left:50px;">
                                                                                            <div componentbgcolor="#f0f0f0" componentpaddingbottom="15px" componentpaddingleft="50px" componentpaddingright="50px" componentpaddingtop="25px" style="background-color: rgb(240, 240, 240);">
                                                                                                <p align="center" style="line-height:1.7;font-family:Arial,verdana;font-size:12px; color:#000000;padding:0px;margin: 0;text-align: center;"><img alt="/campaigns/org54112626/sitesapi/files/images/54113621/Logo(2).png" height="48" src="'.$DMH->site('logo').'" style="font-family: Arial, Helvetica, sans-serif; width: 135px; height: 48px;" width="135"><span style="font-family: Arial, Helvetica, sans-serif;"> </span><span style="font-family: Arial, Helvetica, sans-serif;"> </span><span style="font-family: Arial, Helvetica, sans-serif;">    </span>
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
                                                                    <td class="txtsize" id="elm_1585058829283" style="border:0px;padding:0px 0px;border-collapse:collapse;" valign="top">
        
        
        
        
        
                                                                        <div class="zpelement-wrapper spacebar" id="elm_1585058829283" style=";word-wrap:break-word;overflow:hidden;background-color:#f0f0f0;">
        
                                                                            <table bgcolor="#f0f0f0" border="0" cellpadding="0" cellspacing="0" class="zpAlignPos" style="padding:0px;border:0px;border-collapse:collapse; mso-table-lspace:0pt;font-size:5px; mso-table-rspace:0pt;word-break:break-word;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;height:12px;" width="100%">
        
                                                                                <tbody>
                                                                                    <tr>
                                                                                        <td style="padding:0px;border:0px;font-size:5px;height:12px;border-top:none none none;border-bottom:none none none;">
        
                                                                                            &nbsp;&nbsp;&nbsp;
        
                                                                                        </td>
                                                                                    </tr>
        
                                                                                </tbody>
                                                                            </table>
        
                                                                        </div>
        
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="txtsize" id="elm_1584968377377" style="border:0px;padding:0px 0px;border-collapse:collapse;" valign="top">
        
                                                                        <table bgcolor="#f0f0f0" border="0" cellpadding="0" cellspacing="0" style="font-size:12px;border:0px;padding:0px;width:100%;border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;word-break:break-word;background-color:#f0f0f0;">
                                                                            <tbody>
                                                                                <tr>
                                                                                    <td class="txtsize" style="border:0px;padding:0px 0px;border-top:none none none ;border-bottom:none none none;">
                                                                                        <div class="zpelement-wrapper image" coupcmp id="elm_1584968377377" prodcmp style=";word-wrap:break-word;overflow:hidden;padding:0px;background-color:#f0f0f0;">
                                                                                            <div>
                                                                                                <table align="left" border="0" cellpadding="0" cellspacing="0" class="zpAlignPos" style="font-size:12px;text-align:left;padding:0px;border:0px;border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;word-break:break-word;width:100%;text-align:left;">
                                                                                                    <tbody>
                                                                                                        <tr>
                                                                                                            <td class="bannerimgpad" style="border:0px;padding:0px;text-align:center;padding-top:0px;padding-bottom:0px;padding-right:0px;padding-left:0px;">
                                                                                                                <img align="left" alt="https://campaign-image.com/zohocampaigns/133052000002359006_zc_v40_covid19_7_border_header.png" class="zpImage" height="auto" hspace="0" size="B" src="https://zohopublic.com/zohocampaigns/1060061000000045382_3_1675351862479_header.png" style="width:600px;height:autopx;max-width:600px !important;border:0px;text-align:left;" vspace="0" width="600">
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
                                                                    <td class="txtsize" id="elm_1584957985445" style="border:0px;padding:0px 0px;border-collapse:collapse;" valign="top">
        
                                                                        <div class="zpelement-wrapper" id="elm_1584957985445" style=";word-wrap:break-word;overflow:hidden;padding-right:0px;background-color:#ffffff;">
                                                                            <table bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" class="zpAlignPos" style="font-size:12px;padding:0px;border:0px;border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;word-break:break-word;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" width="100%">
        
                                                                                <tbody>
                                                                                    <tr>
                                                                                        <td class="paddingcomp" style="border:0px;padding:7px 15px;line-height:19pt;border-top:0px none ;   border-bottom:0px none ;padding-top:7px;padding-bottom:7px;padding-right:50px;padding-left:50px;">
                                                                                            <div componentbgcolor="#ffffff" componentpaddingbottom="7px" componentpaddingleft="50px" componentpaddingright="50px" componentpaddingtop="7px" style="background-color: rgb(255, 255, 255);">
                                                                                                <p style="margin: 0;padding: 0px;font-family:Arial, Helvetica, sans-serif; color:#000000;line-height:1.7;font-family:Arial,verdana;font-size:12px;padding:0px;"><b style><font color="#2a2a2a" style="font-size: 18pt;"><span>
        
        <span>Kh&ocirc;i phục mật khẩu tại<span>&nbsp;DMH</span></span></span></font></b>
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
                                                                    <td class="txtsize" id="elm_1584958072686" style="border:0px;padding:0px 0px;border-collapse:collapse;" valign="top">
        
                                                                        <div class="zpelement-wrapper" id="elm_1584958072686" style=";word-wrap:break-word;overflow:hidden;padding-right:0px;background-color:#ffffff;">
                                                                            <table bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" class="zpAlignPos" style="font-size:12px;padding:0px;border:0px;border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;word-break:break-word;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" width="100%">
        
                                                                                <tbody>
                                                                                    <tr>
                                                                                        <td class="paddingcomp" style="border:0px;padding:7px 15px;line-height:19pt;border-top:0px none ;   border-bottom:0px none ;padding-top:7px;padding-bottom:25px;padding-right:50px;padding-left:50px;">
                                                                                            <div componentbgcolor="#ffffff" componentlineheight="19pt" componentpaddingbottom="25px" componentpaddingleft="50px" componentpaddingright="50px" componentpaddingtop="7px" style="background-color: rgb(255, 255, 255); line-height: 19pt;">
                                                                                                <p style="font-family:Arial,verdana;font-size:12px; color:#000000;padding:0px;margin: 0;line-height: 19pt; color: rgb(51, 51, 51); font-family: Arial, Helvetica; font-size: 12pt;"><font color="#424242"><span style="font-size: 16px;"></span></font>
                                                                                                </p><span style="color: rgb(51, 51, 51); font-family: Arial, Helvetica; font-size: 12pt;">
        
        <p style="font-family:Arial,verdana;font-size:12px; color:#000000;padding:0px;margin: 0;line-height: 19pt; text-align: left;"></p></span><font color="#333333" face="Arial, Helvetica" style="color: rgb(51, 51, 51); font-family: Arial, Helvetica; font-size: 12pt;">Bạn nhận được email n&agrave;y bởi v&igrave; bạn vừa y&ecirc;u cầu thiết lập&nbsp;</font>
                                                                                                <div><font color="#333333" face="Arial, Helvetica" style="color: rgb(51, 51, 51); font-family: Arial, Helvetica; font-size: 12pt;">lại</font><span style="color: rgb(51, 51, 51); font-family: Arial, Helvetica; font-size: 12pt;">&nbsp;mật khẩu tại trang truy cập hệ thống quản l&yacute; dịch vụ của&nbsp;</span>
                                                                                                </div>
                                                                                                <div><span style="color: rgb(51, 51, 51); font-family: Arial, Helvetica; font-size: 12pt;">DMH tại&nbsp;</span><a data-saferedirecturl="'.BASE_URL('').'" href="'.BASE_URL('').'" rel="noopener noreferrer" style="text-decoration:underline;font-family: Arial, Helvetica; font-size: 12pt; color: rgb(51, 51, 51);" target="_blank">'.BASE_URL('').'</a><span style="color: rgb(51, 51, 51); font-family: Arial, Helvetica; font-size: 12pt;">.</span>
                                                                                                    <div><span style="color: rgb(51, 51, 51); font-family: Arial, Helvetica; font-size: 12pt;"></span><span style="color: rgb(51, 51, 51); font-size: 12pt; font-family: Arial, Helvetica;">Để thiết lập lại mật khẩu, bạn c&oacute; thể nhấp v&agrave;o li&ecirc;n kết Thiết lập lại</span>
                                                                                                    </div>
                                                                                                    <div><span style="color: rgb(51, 51, 51); font-size: 12pt; font-family: Arial, Helvetica;">mật khẩu b&ecirc;n dưới.</span><span style="color: rgb(51, 51, 51); font-family: Arial, Helvetica; font-size: 12pt;"><p style="margin: 0;padding: 0px;font-family:Arial, Helvetica, sans-serif; color:#000000;line-height:1.7;font-family:Arial,verdana;font-size:12px;padding:0px;"></p>
        
        </span>
                                                                                                        <p style="font-family:Arial,verdana;font-size:12px; color:#000000;padding:0px;margin: 0;line-height: 19pt; color: rgb(51, 51, 51); font-family: Arial, Helvetica; font-size: 12pt;"><font color="#424242"><span style="font-size: 16px;"></span></font>
                                                                                                        </p>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </td>
                                                                                    </tr>
                                                                                </tbody>
                                                                            </table>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="txtsize" id="elm_1675352785620" style="border:0px;padding:0px 0px;border-collapse:collapse;" valign="top">
        
        
        
        
        
                                                                        <table bgcolor="transparent" cellpadding="0" cellspacing="0" height="48" style="font-size:12px;border:0px;border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;border:none;" width="100%">
                                                                            <tbody>
                                                                                <tr>
                                                                                    <td class="paddingcomp" style="border:0px;padding:7px 15px;border-top:none none none;border-bottom:none none none;padding-top:7px;padding-bottom:7px;padding-right:15px;padding-left:15px;">
                                                                                        <div class="zpelement-wrapper buttonElem" id="elm_1675352785620" style="overflow:hidden;word-wrap:break-word;">
                                                                                            <div class="zpAlignPos" style="text-align:center;">
                                                                                                <table align="center" cellpadding="0" cellspacing="0" style="font-size:12px;border:none;padding:0px;border:0px;margin:0px auto;border-collapse:separate; mso-table-lspace:0pt; mso-table-rspace:0pt;">
                                                                                                    <tbody>
                                                                                                        <tr>
                                                                                                            <td align="center" class="txtsize" style="border:0px;padding:0px;color:#ffffff;font-family:Arial;text-align:center;border-radius:4px;text-align:center;cursor:pointer;">
                                                                                                                <!--[if mso]>
                                      <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="'.BASE_URL('Reset-Password/'.$token).'" style="border-radius:4px;height:60px;v-text-anchor:middle;width:250px" arcsize="4%" strokecolor="#ffffff" strokeweight ="0px" fillcolor="#3498db">
                                      <v:stroke dashstyle="solid" />
                                        <w:anchorlock/>
                                        <center style="direction:ltr;color:#ffffff;font-family:Arial;font-size:16pt;">Đặt lại mật khẩu</center>
                                      </v:roundrect>
                                <![endif]-->
                                                                                                                <a align="center" href="'.BASE_URL('Reset-Password/'.$token).'" style="padding:0px 0px;background-color:#3498db;width:250px;line-height:60px;font-size:16pt;direction:ltr;font-family:Arial;color:#ffffff;cursor:pointer;text-decoration:none;border-radius:4px;border:0px solid #ffffff;display:inline-block;mso-hide:all;text-align:center;" target="_blank">
                                                                                                                    <font style="color:#ffffff;line-height:60px">
                                        Đặt lại mật khẩu
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
                                                                    <td class="txtsize" id="elm_1675353517772" style="border:0px;padding:0px 0px;border-collapse:collapse;" valign="top">
        
                                                                        <div class="zpelement-wrapper" id="elm_1675353517772" style=";word-wrap:break-word;overflow:hidden;padding-right:0px;background-color:#ffffff;">
                                                                            <table bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" class="zpAlignPos" style="font-size:12px;padding:0px;border:0px;border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;word-break:break-word;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" width="100%">
        
                                                                                <tbody>
                                                                                    <tr>
                                                                                        <td class="paddingcomp" style="border:0px;padding:7px 15px;line-height:19pt;border-top:0px none ;   border-bottom:0px none ;padding-top:7px;padding-bottom:7px;padding-right:50px;padding-left:50px;">
                                                                                            <div componentbgcolor="#ffffff" componentpaddingbottom="7px" componentpaddingleft="50px" componentpaddingright="50px" componentpaddingtop="7px" style="background-color: rgb(255, 255, 255);">
                                                                                                <p align="center" style="line-height:1.7;font-family:Arial,verdana;font-size:12px; color:#000000;padding:0px;margin: 0;text-align: center;"><font color="#2a2a2a" style="font-size: 11pt;"><b style>HOẶC</b></font>
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
                                                                    <td class="txtsize" id="elm_1585058501920" style="border:0px;padding:0px 0px;border-collapse:collapse;" valign="top">
        
                                                                        <div class="zpelement-wrapper" id="elm_1585058501920" style=";word-wrap:break-word;overflow:hidden;padding-right:0px;background-color:#ffffff;">
                                                                            <table bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" class="zpAlignPos" style="font-size:12px;padding:0px;border:0px;border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;word-break:break-word;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" width="100%">
        
                                                                                <tbody>
                                                                                    <tr>
                                                                                        <td class="paddingcomp" style="border:0px;padding:7px 15px;line-height:19pt;border-top:0px none ;   border-bottom:0px none ;padding-top:7px;padding-bottom:25px;padding-right:50px;padding-left:50px;">
                                                                                            <div componentbgcolor="#ffffff" componentlineheight="19pt" componentpaddingbottom="25px" componentpaddingleft="50px" componentpaddingright="50px" componentpaddingtop="7px" style="background-color: rgb(255, 255, 255); line-height: 19pt;">
                                                                                                <p style="font-family:Arial,verdana;font-size:12px; color:#000000;padding:0px;margin: 0;line-height: 19pt;"><font size="5" style="line-height: 19pt;"><span style="line-height: 19pt;">
        
        </span></font>
                                                                                                </p>
                                                                                                <p style="line-height:1.7;font-family:Arial,verdana;font-size:12px; color:#000000;padding:0px;margin: 0px; padding: 0px; box-sizing: border-box; font-family: Arial, verdana; font-style: normal; font-variant-ligatures: normal; font-variant-caps: normal; font-weight: 400; orphans: 2; text-align: left; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px; -webkit-text-stroke-width: 0px; background-color: rgb(255, 255, 255); text-decoration-style: initial; text-decoration-color: initial; line-height: 19pt;"><span style="line-height: 19pt;"></span>
                                                                                                </p><span><p style="font-family:Arial,verdana;font-size:12px; color:#000000;padding:0px;margin: 0;line-height: 19pt; text-align: left;"><span style="color: rgb(51, 51, 51); font-size: 12pt; font-family: Arial, Helvetica;">Nếu bạn không thể nhấp v&agrave;o li&ecirc;n kết tr&ecirc;n, bạn c&oacute; thể copy đường dẫn ph&iacute;a dưới v&agrave; d&aacute;n l&ecirc;n tr&igrave;nh duyệt:</span>
                                                                                                <br>
                                                                                                </p>
                                                                                                <p style="font-family:Arial,verdana;font-size:12px; color:#000000;padding:0px;margin: 0;line-height: 19pt; text-align: left;"><span style="color: rgb(51, 51, 51); font-family: Arial, Helvetica;">
        
        <b style><a data-saferedirecturl="'.BASE_URL('Reset-Password/'.$token).'" href="'.BASE_URL('Reset-Password/'.$token).'" rel="noopener noreferrer" style="text-decoration:underline;" target="_blank"><br><font style="font-size: 10pt;">'.BASE_URL('Reset-Password/'.$token).'</font></a></b>
        
        <br></span>
                                                                                                </p>
                                                                                                <p style="margin: 0;padding: 0px;font-family:Arial, Helvetica, sans-serif; color:#000000;line-height:1.7;font-family:Arial,verdana;font-size:12px;padding:0px;"></p>
        
                                                                                                </span>
                                                                                                <div><span style="line-height: 19pt;"><b><a data-saferedirecturl="'.BASE_URL('Reset-Password/'.$token).'" href="'.BASE_URL('Reset-Password/'.$token).'" rel="noopener noreferrer" style="text-decoration:underline;" target="_blank"></a></b>
        
        </span>
                                                                                                    <p style="line-height:1.7;font-family:Arial,verdana;font-size:12px; color:#000000;padding:0px;margin: 0px; padding: 0px; box-sizing: border-box; font-family: Arial, verdana; font-style: normal; font-variant-ligatures: normal; font-variant-caps: normal; font-weight: 400; orphans: 2; text-align: left; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px; -webkit-text-stroke-width: 0px; background-color: rgb(255, 255, 255); text-decoration-style: initial; text-decoration-color: initial; line-height: 19pt;"><span style="line-height: 19pt;"></span>
                                                                                                    </p><font size="5" style="line-height: 19pt;">
        
        </font>
                                                                                                    <p style="font-family:Arial,verdana;font-size:12px; color:#000000;padding:0px;margin: 0;line-height: 19pt;"></p>
                                                                                                </div>
                                                                                            </div>
                                                                                        </td>
                                                                                    </tr>
                                                                                </tbody>
                                                                            </table>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="txtsize" id="elm_1584958535466" style="border:0px;padding:0px 0px;border-collapse:collapse;" valign="top">
        
                                                                        <div class="zpelement-wrapper" id="elm_1584958535466" style=";word-wrap:break-word;overflow:hidden;padding-right:0px;background-color:#ffffff;">
                                                                            <table bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" class="zpAlignPos" style="font-size:12px;padding:0px;border:0px;border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;word-break:break-word;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" width="100%">
        
                                                                                <tbody>
                                                                                    <tr>
                                                                                        <td class="paddingcomp" style="border:0px;padding:7px 15px;line-height:19pt;border-top:0px none ;   border-bottom:0px none ;padding-top:7px;padding-bottom:7px;padding-right:50px;padding-left:50px;">
                                                                                            <div componentbgcolor="#ffffff" componentpaddingbottom="7px" componentpaddingleft="50px" componentpaddingright="50px" componentpaddingtop="7px" style="background-color: rgb(255, 255, 255);">
                                                                                                <p style="font-family:Arial,verdana;font-size:12px; color:#000000;padding:0px;margin: 0;line-height: 19pt;"><font color="#2a2a2a"><span style="font-size: 24px;"><b>_________</b></span></font>
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
                                                                    <td class="txtsize" id="elm_1585058521367" style="border:0px;padding:0px 0px;border-collapse:collapse;" valign="top">
        
                                                                        <div class="zpelement-wrapper" id="elm_1585058521367" style=";word-wrap:break-word;overflow:hidden;padding-right:0px;background-color:#ffffff;">
                                                                            <table bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" class="zpAlignPos" style="font-size:12px;padding:0px;border:0px;border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;word-break:break-word;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" width="100%">
        
                                                                                <tbody>
                                                                                    <tr>
                                                                                        <td class="paddingcomp" style="border:0px;padding:7px 15px;line-height:19pt;border-top:0px none ;   border-bottom:0px none ;padding-top:7px;padding-bottom:25px;padding-right:50px;padding-left:50px;">
                                                                                            <div componentbgcolor="#ffffff" componentpaddingbottom="25px" componentpaddingleft="50px" componentpaddingright="50px" componentpaddingtop="7px" style="background-color: rgb(255, 255, 255);">
                                                                                                <p style="font-family:Arial,verdana;font-size:12px; color:#000000;padding:0px;margin: 0;line-height: 19pt;"><span style="font-size: 16px;"><font color="#424242">Hướng dẫn mua m&atilde; nguồn:&nbsp;</font><a alt="https://bit.ly/3YdUmvy" href="https://bit.ly/3YdUmvy" rel="noopener noreferrer" style="text-decoration:underline;color: rgb(27, 107, 189);" target="_blank" title="https://bit.ly/3YdUmvy"><font color="#1b6bbd" style="color:#1b6bbd;">https://bit.ly/3YdUmvy</font></a></span>
                                                                                                </p>
                                                                                                <p style="font-family:Arial,verdana;font-size:12px; color:#000000;padding:0px;margin: 0;line-height: 19pt;"><span style="font-size: 16px;"><font color="#424242">Hướng dẫn tạo website:&nbsp;</font><a alt="https://bit.ly/3Rqw1R2" href="https://bit.ly/3Rqw1R2" rel="noopener noreferrer" style="text-decoration:underline;color: rgb(27, 107, 189);" target="_blank" title="https://bit.ly/3Rqw1R2"><font color="#1b6bbd" style="color:#1b6bbd;">https://bit.ly/3Rqw1R2</font></a></span>
                                                                                                </p>
                                                                                                <p style="font-family:Arial,verdana;font-size:12px; color:#000000;padding:0px;margin: 0;line-height: 19pt;"><font color="#424242"><span style="font-size: 16px;"></span></font><span style="font-size: 16px;"><font color="#424242">Hướng dẫn nạp tiền:&nbsp;</font><a alt="https://bit.ly/3JBPY5Q" href="https://bit.ly/3JBPY5Q" rel="noopener noreferrer" style="text-decoration:underline;color: rgb(27, 107, 189);" target="_blank" title="https://bit.ly/3JBPY5Q"><font color="#1b6bbd" style="color:#1b6bbd;">https://bit.ly/3JBPY5Q</font></a></span>
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
                                                                    <td class="txtsize" id="elm_1584968505499" style="border:0px;padding:0px 0px;border-collapse:collapse;" valign="top">
        
                                                                        <table bgcolor="#f0f0f0" border="0" cellpadding="0" cellspacing="0" style="font-size:12px;border:0px;padding:0px;width:100%;border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;word-break:break-word;background-color:#f0f0f0;">
                                                                            <tbody>
                                                                                <tr>
                                                                                    <td class="txtsize" style="border:0px;padding:0px 0px;border-top:none none none ;border-bottom:none none none;">
                                                                                        <div class="zpelement-wrapper image" coupcmp id="elm_1584968505499" prodcmp style=";word-wrap:break-word;overflow:hidden;padding:0px;background-color:#f0f0f0;">
                                                                                            <div>
                                                                                                <table align="left" border="0" cellpadding="0" cellspacing="0" class="zpAlignPos" style="font-size:12px;text-align:left;padding:0px;border:0px;border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;word-break:break-word;width:100%;text-align:left;">
                                                                                                    <tbody>
                                                                                                        <tr>
                                                                                                            <td class="bannerimgpad" style="border:0px;padding:0px;text-align:center;padding-top:0px;padding-bottom:0px;padding-right:0px;padding-left:0px;">
                                                                                                                <img align="left" alt="https://campaign-image.com/zohocampaigns/133052000002359006_zc_v40_covid19_7_border_footer.png" class="zpImage" height="auto" hspace="0" size="B" src="https://zohopublic.com/zohocampaigns/1060061000000045382_1_1675351862330_footer.png" style="width:600px;height:autopx;max-width:600px !important;border:0px;text-align:left;" vspace="0" width="600">
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
                                                                    <td class="txtsize" id="elm_1584960527930" style="border:0px;padding:0px 0px;border-collapse:collapse;" valign="top">
        
        
        
        
        
                                                                        <div class="zpelement-wrapper spacebar" id="elm_1584960527930" style=";word-wrap:break-word;overflow:hidden;background-color:#f0f0f0;">
        
                                                                            <table bgcolor="#f0f0f0" border="0" cellpadding="0" cellspacing="0" class="zpAlignPos" style="padding:0px;border:0px;border-collapse:collapse; mso-table-lspace:0pt;font-size:5px; mso-table-rspace:0pt;word-break:break-word;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;height:26px;" width="100%">
        
                                                                                <tbody>
                                                                                    <tr>
                                                                                        <td style="padding:0px;border:0px;font-size:5px;height:26px;border-top:none none none;border-bottom:none none none;">
        
                                                                                            &nbsp;&nbsp;&nbsp;
        
                                                                                        </td>
                                                                                    </tr>
        
                                                                                </tbody>
                                                                            </table>
        
                                                                        </div>
        
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="txtsize" id="elm_1619165211505" style="border:0px;padding:0px 0px;border-collapse:collapse;" valign="top">
        
                                                                        <div class="zpelement-wrapper" id="elm_1619165211505" style=";word-wrap:break-word;overflow:hidden;padding-right:0px;background-color:#f0f0f0;">
                                                                            <table bgcolor="#f0f0f0" border="0" cellpadding="0" cellspacing="0" class="zpAlignPos" style="font-size:12px;padding:0px;border:0px;border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;word-break:break-word;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" width="100%">
        
                                                                                <tbody>
                                                                                    <tr>
                                                                                        <td class="paddingcomp" style="border:0px;padding:7px 15px;line-height:19pt;border-top:0px none ;   border-bottom:0px none ;padding-top:7px;padding-bottom:7px;padding-right:15px;padding-left:15px;">
                                                                                            <div componentbgcolor="#f0f0f0" style="background-color: rgb(240, 240, 240);">
                                                                                                <p style="line-height:1.7;font-family:Arial,verdana;font-size:12px; color:#000000;padding:0px;margin: 0;text-align: center; line-height: 19pt;"><font color="#2a2a2a" face="Arial, Helvetica, sans-serif" style="font-size: 11pt;">Kết nối với ch&uacute;ng t&ocirc;i</font>
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
                                                                    <td class="txtsize" id="elm_1619165270841" style="border:0px;padding:0px 0px;border-collapse:collapse;" valign="top">
        
        
        
        
        
                                                                        <div class="zpelement-wrapper wdgts" id="elm_1619165270841" style="overflow:hidden;word-wrap:break-word;">
                                                                            <table bgcolor="#f0f0f0" border="0" cellpadding="0" cellspacing="0" class="zpAlignPos" style="padding:0px;border:0px;border-collapse:collapse; mso-table-lspace:0pt;font-size:5px; mso-table-rspace:0pt;word-break:break-word;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;  background-color:#f0f0f0;" width="100%">
                                                                                <tbody>
                                                                                    <tr>
                                                                                        <td style="padding:7px 15px;border:0px;font-size:5px;border-top:0px none ;border-bottom:0px none ;padding-top:7px;padding-bottom:7px;padding-right:15px;padding-left:15px;">
                                                                                            <table align="center" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;font-size:12px;min-width: 100%;border: none;" width="100%">
                                                                                                <tbody>
                                                                                                    <tr>
                                                                                                        <td align="center" style="border: none;padding: 0px;margin: 0px;" valign="top">
                                                                                                            <table align="center" border="0" cellpadding="0" cellspacing="0" componentbgcolor="#f0f0f0" icontext="true" index="5" name="zcsclwdgts_alnmnt" style="font-size:12px;border-collapse: collapse;border: none;margin:auto;">
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
                                                                                                                                                                                            <a href="https://www.facebook.com/Hotro.DMH" style="text-decoration:underline;display: block;font-size: 1px;" target="_blank"><img alt="Facebook" height="35" purpose="wdgt_1675352328687" src="https://zohopublic.com/zohocampaigns/2021_facebook_icon.svg_removebg_preview_zc_v9_1060061000000045382.png" style="border: 0px; margin: 0px; outline: none; text-decoration: none; width: 25px; height: 25px;" vspace="10" width="35">
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
                                                                                                                                                                                            <a href="https://zalo.me/0812665001" style="text-decoration:underline;display: block;font-size: 1px;" target="_blank"><img alt="Zalo" height="35" purpose="wdgt_1675352238078" src="https://zohopublic.com/zohocampaigns/icon_of_zalo.svg_removebg_preview_zc_v9_1060061000000045382.png" style="border: 0px; margin: 0px; outline: none; text-decoration: none; width: 25px; height: 25px;" vspace="10" width="35">
                                                                                                                                                                                            </a>
                                                                                                                                                                                        </td>
                                                                                                                                                                                    </tr>
                                                                                                                                                                                    <tr>
                                                                                                                                                                                        <td align="center" name="sclwdgtcaptns" style="border:none;padding: 0px;margin: 0px;" valign="middle">
                                                                                                                                                                                            <a href="https://zalo.me/0812665001" style="display: block;font-size: 1px;font-weight: normal;line-height: normal;text-align: center;text-decoration: none;" target="_blank">
                                                                                                                                                                                                <p fntname="Arial" fntsze="8" style="font-family:Arial,verdana;font-size:12px; color:#000000;padding:0px;margin: 0;line-height: normal; font-family: Arial, Helvetica, sans-serif; color: rgb(27, 107, 189); font-size: 8pt;">Zalo</p>
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
                                                                                                                                                                                            <a href="https://www.youtube.com/@dienmayhieu" style="text-decoration:underline;display: block;font-size: 1px;" target="_blank"><img alt="Youtube" height="35" purpose="wdgt_1675352425412" src="https://zohopublic.com/zohocampaigns/icon_youtube_removebg_preview_zc_v9_1060061000000045382.png" style="border: 0px; margin: 0px; outline: none; text-decoration: none; width: 25px; height: 25px;" vspace="10" width="35">
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
                                                                                                                                                                                            <a href="mailto:cskh@dmh.vn" style="text-decoration:underline;display: block;font-size: 1px;" target="_blank"><img alt="Email" height="35" purpose="wdgt_1675352491056" src="https://zohopublic.com/zohocampaigns/email_shiny_icon.svg_removebg_preview_zc_v10_1060061000000045382.png" style="border: 0px; margin: 0px; outline: none; text-decoration: none; width: 25px; height: 25px;" vspace="10" width="35">
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
                                                                    <td class="txtsize" id="elm_1619165302283" style="border:0px;padding:0px 0px;border-collapse:collapse;" valign="top">
        
                                                                        <div class="zpelement-wrapper" id="elm_1619165302283" style=";word-wrap:break-word;overflow:hidden;padding-right:0px;background-color:#f0f0f0;">
                                                                            <table bgcolor="#f0f0f0" border="0" cellpadding="0" cellspacing="0" class="zpAlignPos" style="font-size:12px;padding:0px;border:0px;border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;word-break:break-word;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;" width="100%">
        
                                                                                <tbody>
                                                                                    <tr>
                                                                                        <td class="paddingcomp" style="border:0px;padding:7px 15px;line-height:19pt;border-top:0px none ;   border-bottom:0px none ;padding-top:7px;padding-bottom:7px;padding-right:100px;padding-left:100px;">
                                                                                            <div componentbgcolor="#f0f0f0" componentpaddingbottom="7px" componentpaddingleft="100px" componentpaddingright="100px" componentpaddingtop="7px" style="background-color: rgb(240, 240, 240);">
                                                                                                <p align="center" style="font-family:Arial,verdana;font-size:12px; color:#000000;padding:0px;margin: 0;line-height: 19pt; text-align: center;"><font face="Arial, Helvetica"><span style="font-size: 13.3333px;"><b style><a alt="Đăng nhập" href="https://dmh.com/dang-nhap" rel="noopener noreferrer" style="text-decoration:underline;color: rgb(0, 108, 251);" target="_blank" title="Đăng nhập"><font color="#006cfb" style="color:#006cfb;">Đăng nhập</font>
                                                                                                    </a>
                                                                                                    </b><font color="#393838">&nbsp; &nbsp;</font><b style><font color="#006cfb"><a alt="Đăng k&yacute;" href="https://dmh.com/dang-ky" rel="noopener noreferrer" style="text-decoration:underline;" target="_blank" title="Đăng k&yacute;"><font color="#006cfb" style="color:#006cfb;">Đăng k&yacute;</font></a></font></b><b style><font color="#393838">&nbsp; </font><font color="#006cfb"><a alt="Mua m&atilde; nguồn" href="https://dmh.com/mua-source-code" rel="noopener noreferrer" style="text-decoration:underline;" target="_blank" title="Mua m&atilde; nguồn"><font color="#006cfb" style="color:#006cfb;">Mua m&atilde; nguồn</font></a></font><font color="#393838">&nbsp;&nbsp;</font><font color="#006cfb"><a alt="Tạo mẫu website" href="https://dmh.com/tao-trang-web" rel="noopener noreferrer" style="text-decoration:underline;" target="_blank" title="Tạo mẫu website"><font color="#006cfb" style="color:#006cfb;">Tạo mẫu website</font></a></font></b>
                                                                                                    </span>
                                                                                                    </font>
                                                                                                </p>
                                                                                                <p align="center" style="font-family:Arial,verdana;font-size:12px; color:#000000;padding:0px;margin: 0;line-height: 19pt; text-align: center;"><font face="Arial, Helvetica" style><font color="#393838" style="font-size: 10pt;"></font>
                                                                                                    <br>
                                                                                                    </font>
                                                                                                </p>
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
        $kq = sendCSM($guitoi, $hoten, $subject, $noi_dung, $bcc);
        if($kq)
        {
            msg_success2('Chúng tôi đã gửi một địa chỉ để lấy lại PassWord. Hãy kiểm tra nó trong hòm thư hoặc thư rác, thư spam.');
        }
        else
        {
            msg_error2("Gửi email thất bại. Hãy kiểm tra lại");
        }
    }
    if($_POST['type'] == 'DatlaiPass') {
        $pass1  = check_string($_POST['Passnew1']);
        $pass2  = check_string($_POST['Passnew2']);
        if(!$pass1 || !$pass2) {
            msg_error2("Vui lòng nhập đầy đủ thông tin để tiếp tục");
        }
        if(strlen($pass1) < 5) {
            msg_error2("Mật khẩu của bạn phải trên 4 kí tự");
        }
        if($pass1 !== $pass2) {
            msg_error2("Nhập lại password không chính xác");
        }
        $token = randomtoken();
        $token_api = randomtoken2();
        $DMH->update("users", array(
            'password'  => md5($pass1),
            'tokenlog'  => $token,
            'timeon'    => gettime(),
            'ip'        => myip()
        ), " `email` = '".$_SESSION['EmailPass']."' ");
        setcookie('token', $token, time() + 2678400, '/');
        msg_success("Đã đặt lại mật khẩu thành công.", BASE_URL(''), 2000);
    }
}
else
{
    require_once("../../pages/client/404.php");
}