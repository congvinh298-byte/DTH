<?php
if (!defined('IN_SITE')) die('The Request Not Found');
$TUANORI = new TUANORI;
$site_gmail_momo    = $TUANORI->site('email');
$site_pass_momo     = $TUANORI->site('pass_email');
require_once(__DIR__.'/../lib/Pusher.php');
/*MÃ NGUỒN NÀY ĐƯỢC PHÁT TRIỂN BỞI TUANORI - ZALO: 0812665001*/
$base_url = 'http://'.$_SERVER['SERVER_NAME'].'/'; // Thay url web bạn
function danhmuc($data)
{
    if($data == 'SHOW')
    {
        return '<span class="badge badge-success badge-pill m-r-5 m-b-5">Hiển Thị</span>';
    }
    else if($data == 'OFF')
    {
        return '<span class="badge badge-default badge-pill m-r-5 m-b-5">Đang Ẩn</span>';
    }
}
function taoweb_on($data)
{
    if($data == 'ON')
    {
        return '<span class="badge badge-success badge-pill m-r-5 m-b-5">Đang bán</span>';
    }
    else if($data == 'OFF')
    {
        return '<span class="badge badge-danger badge-pill m-r-5 m-b-5">Ngưng bán</span>';
    }
}
function getRandomWeightedElement(array $weightedValues)
{
    $Rand = mt_Rand(1, (int) array_sum($weightedValues));
    foreach ($weightedValues as $key => $value) {
        $Rand -= $value;
        if ($Rand <= 0) {
            return $key;
        }
    }
}
function check_isMobile() {
    $is_mobile = '0';
    if(preg_match('/(android|iphone|ipad|up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone)/i', strtolower($_SERVER['HTTP_USER_AGENT'])))$is_mobile=1;
    if((strpos(strtolower($_SERVER['HTTP_ACCEPT']),'application/vnd.wap.xhtml+xml')>0) or ((isset($_SERVER['HTTP_X_WAP_PROFILE']) or isset($_SERVER['HTTP_PROFILE']))))
    $is_mobile=1;
    $mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'],0,4));
    $mobile_agents = array('w3c ','acs-','alav','alca','amoi','andr','audi','avan','benq','bird','blac','blaz','brew','cell','cldc','cmd-','dang','doco','eric','hipt','inno','ipaq','java','jigs','kddi','keji','leno','lg-c','lg-d','lg-g','lge-','maui','maxo','midp','mits','mmef','mobi','mot-','moto','mwbp','nec-','newt','noki','oper','palm','pana','pant','phil','play','port','prox','qwap','sage','sams','sany','sch-','sec-','send','seri','sgh-','shar','sie-','siem','smal','smar','sony','sph-','symb','t-mo','teli','tim-','tosh','tsm-','upg1','upsi','vk-v','voda','wap-','wapa','wapi','wapp','wapr','webc','winw','winw','xda','xda-');
    if(in_array($mobile_ua,$mobile_agents)) $is_mobile=1;
    if (isset($_SERVER['ALL_HTTP'])) 
    {
        if (strpos(strtolower($_SERVER['ALL_HTTP']),'OperaMini')>0)
        $is_mobile=1;
    }
    if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']),'windows')>0) $is_mobile=0;
    return $is_mobile;
}
function json_code($status, $mess)
{
	$row = array (
		"status" => $status,
		"message" => $mess
	);
	die(json_encode($row));
}

function giaban($data)
{
    if($data > 0)
    {
        return $data;
    }
    else
    {
        return 'Miễn Phí';
    }
}
function in($str, $bo) {
    return (!$bo ? $str : $getUser['username']);

}
function sendCSM($mail_nhan,$ten_nhan,$chu_de,$noi_dung,$bcc)
{
    // return true;
    global $site_gmail_momo, $site_pass_momo;
        // PHPMailer Modify
        $mail = new PHPMailer();
        $mail->SMTPDebug = 0;
        $mail ->Debugoutput = "html";
        $mail->isSMTP();
        $mail->Host = 'mail.tuanori.vn';
        $mail->SMTPAuth = true;
        $mail->Username = $site_gmail_momo; // GMAIL STMP
        $mail->Password = $site_pass_momo; // PASS STMP
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;
        $mail->setFrom($site_gmail_momo, $bcc);
        $mail->addAddress($mail_nhan, $ten_nhan);
        $mail->addReplyTo($site_gmail_momo, $bcc);
        $mail->isHTML(true);
        $mail->Subject = $chu_de;
        $mail->Body    = $noi_dung;
        $mail->CharSet = 'UTF-8';
        $send = $mail->send();
        return $send;
}
$MEMO_PREFIX = $TUANORI->site('nd_bank');
function get_id_bank($des)
{
    global $MEMO_PREFIX;
    $re = '/'.$MEMO_PREFIX.'\d+/im';
    preg_match_all($re, $des, $matches, PREG_SET_ORDER, 0);
    if (count($matches) == 0 )
        return null;
    // Print the entire match result
    $orderCode = $matches[0][0];
    $prefixLength = strlen($MEMO_PREFIX);
    $orderId = intval(substr($orderCode, $prefixLength ));
    return $orderId ;
}
function timeran($data)
{
    if($data)
    {
        return format_date($data);
    }
    else
    {
        return 'Chưa Có Dữ Liệu';
    }
}
function napthestt($data)
{
    if($data == 'xuly')
    {
        return '<span class="badge badge-info">Chờ xử lý</span>';
    }
    else if($data == 'thanhcong')
    {
        return '<span class="badge badge-success">Thẻ Đúng</span>';
    }
    else if($data == 'thatbai')
    {
        return '<span class="badge badge-danger">Thẻ Sai</span>';
    }
    else
    {
        return '<span class="badge badge-danger">Lỗi</span>';
    }
}
function sttclf($data)
{
    if($data == 'active')
    {
        return '<span class="badge badge-success">Hoạt Động</span>';
    }
    else if($data == 'pending')
    {
        return '<span class="badge badge-warning">Chưa hoạt động</span>';
    }
    else
    {
        return '<span class="badge badge-danger">Lỗi NS</span>';
    }
}
function magiamgiav2($data)
{
    if(!$data)
    {
        return '<span style="color: red">Không Có</span>';
    }
    else
    {
        return '<span style="color: green">Có.Mã giảm giá là: '.$data.'</span>';
    }
}
function send_tele($data)
{
    $json = json_decode(file_get_contents('https://api.telegram.org/bot5065818486:AAEB6XLQmPbXUorljngJv0Yc_LQCnhqUDjM/sendMessage?chat_id=2118248410&text='.urlencode($data)), true);
    return $json;
}
function token_api($data)
{
    if($data == 'ON') {
        return '<span class="badge bg-warning rounded-lg" style="background-color: #99FF66">Đang Mở</span>';
    } else {
        return '<span class="badge bg-warning rounded-lg" style="background-color: #CCCCCC">Đang Tắt</span>';
    }
}
function token_api2($data)
{
    if($data == 'ON')
    {
        return 'checked';
    }
    else
    {
        return '';
    }
}
function statusrating($data)
{
    if($data == 'xuly')
    {
        return '<span class="badge badge-warning">Chờ kiểm tra</span>';
    }
    else if($data == 'thatbai')
    {
        return '<span class="badge badge-danger">Từ chối đánh giá</span>';
    }
    else if($data == 'hoantat')
    {
        return '<span class="badge badge-success">Đã công khai</span>';
    }
}
function statustaoweb($data)
{
    if(in_array($data, [1,2])) {
        return '<span class="badge bg-warning rounded-lg" style="background-color: #FFC436">Chưa thao tác</span>';
    }
    else if($data == 3) {
        return '<span class="badge bg-warning rounded-lg" style="background-color: #33FFFF">Chờ xử lý</span>';
    }
    else if($data == 4) {
        return '<span class="badge bg-warning rounded-lg" style="background-color: #00FF33">Đang hoạt động</span>';
    }
    else if($data == 5) {
        return '<span class="badge bg-warning rounded-lg" style="background-color: #DD0000">Sắp hết hạn</span>';
    } else if($data == 7) {
        return '<span class="badge bg-warning rounded-lg" style="background-color: #DD0000">Đã bị Hủy bởi ADMIN</span>';
    } else if($data == 8) {
        return '<span class="badge bg-warning rounded-lg" style="background-color: #    ">Vui lòng liên hệ ADMIN</span>';
    } else {    
        return '<span class="badge bg-warning rounded-lg" style="background-color: #DD0000">Chấm dứt</span>';

    }
}
function den() {
    $res = $_SESSION['url'] ?? '/';
    return $res;
}
$ck = $TUANORI->site('ckcard');
function hamlogin($url)
{
    global $tk, $mk, $login;
    $query = $login.":2083/$url";
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER,0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST,0);
    curl_setopt($curl, CURLOPT_HEADER,0);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);
    $header[0] = "Authorization: Basic " . base64_encode($tk.":".$mk) . "\n\r";
    curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
    curl_setopt($curl, CURLOPT_URL, $query);
    $result = curl_exec($curl);
    if ($result == false) {
        error_log("curl_exec threw error \"" . curl_error($curl) . "\" for $query");   
    }
    curl_close($curl);
    $dulieu = json_decode($result, true);
    return $dulieu;
}

function curl_mb($id)
{
    if(!$id)
    {
        return 'Không tồn tại ID';
    }
    else
    {
        $data = curl_get("https://www.googleapis.com/drive/v3/files/$id?fields=size&key=AIzaSyAb8Dh9cLxrVNDVD_7KS1qlT_kwy75Xn-A");
        $data = json_decode($data, true);
        if(isset($data['size']))
        {
            return $data['size'];
        }
        else
        {
            return 'Lỗi Check';
        }
    }
}
function locDomain($data)
{
    $parsedUrl = parse_url($data);
    return $parsedUrl['host'];
}
function trampham2($data1, $data2, $data3)
{
    return ceil($data1/($data1 + $data2 + $data3)*100);
}
function statusclf2($data)
{
    if($data == 'pending')
    {
        $show = '<span class="badge badge-warning">Chờ trỏ miền</span>';
    }
    else if($data == 'active')
    {
        $show = '<span class="badge badge-success">Trỏ thành công</span>';
    }
    else
    {
        $show = '<span class="badge badge-danger">Lỗi</span>';
    }
    return $show;
}
function tenmienv22($data, $data2)
{
    if(!$data)
    {
        return '<span class="badge badge-danger">Chưa thêm tên miền vào CLF</span>';
    }
    if($data2 == 'pending')
    {
        return '<span class="badge badge-warning">Chưa trỏ tên miền</span>';  
    }
    else if($data2 == 'active')
    {
        return '<span class="badge badge-success">Đã trỏ hoàn tất</span>';
    }
    else if($data2 == 'moved')
    {
        return '<span class="badge badge-danger">Lỗi</span>';
    }
}
function stthosting($data)
{
    if($data == 'YES')
    {
        return '<span class="badge badge-success">Đã Mua</span>';
    }
    else if($data == 'NO')
    {
        return '<span class="badge badge-info">Không Mua</span>';
    }
    else 
    {
        return '<span class="badge badge-warning">Chờ Khách Hàng Chọn</span>';    
    }
}
function BASE_URL($url)
{
    global $base_url;
    return $base_url.$url;
}
function check_chars($data)
{
    $value = preg_match('/[#@! $%^&*()+=\-\[\]\';,.\/{}|":<>?~\\\\]/', $data);
    if($value)
    {
        return false;
    }
    else
    {
        return true;
    }
}
function on_off($data)
{
    if($data == 'ON')
    {
        return '<span class="badge badge-success">ON</span>';
    }
    else if($data == 'OFF')
    {
        return '<span class="badge badge-secondary">OFF</span>';
    }
}
function format_date($time){
    return date("H:i:s d/m/Y", $time);
}
function gettime()
{
    return date('Y/m/d H:i:s', time());
}
function gettime2($data)
{
    return date('Y-m-d H:i:s', $data);
}
function check_string($data)
{
    return trim(htmlspecialchars(addslashes($data)));
}
function format_cash($price)
{
    return str_replace(",", ".", number_format($price));
}
function sotienmua($data)
{
    if($data > 0)
    {
        return format_cash($data).'₫';
    }
    else
    {
        return 'Miễn phí';
    }
}
function curl_get($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $data = curl_exec($ch);
    
    curl_close($ch);
    return $data;
}
function random($string, $int)
{  
    return substr(str_shuffle($string), 0, $int);
}
function pheptru($int1, $int2)
{
    return $int1 - $int2;
}
function phepcong($int1, $int2)
{
    return $int1 + $int2;
}
function phepnhan($int1, $int2)
{
    return $int1 * $int2;
}
function phepchia($int1, $int2)
{
    return $int1 / $int2;
}
function check_img($img)
{
    $filename = $_FILES[$img]['name'];
    $ext = explode(".", $filename);
    $ext = end($ext);
    $valid_ext = array("png","jpeg","jpg","PNG","JPEG","JPG","gif","GIF");
    if(in_array($ext, $valid_ext))
    {
        return true;
    }
}
function hienthi($data)
{
    if(!$data)
    {
        return '';
    }
    else
    {
        return $data;
    }
}
// function msg($text, $stt) {
//     return '<div class="alert alert-'.$stt.'" role="alert">'.$text.'</div>';
// }
function msg($status, $msg, $url = '-1', $time = 0) {
    die(json_encode(['status' => $status, 'msg' => $msg, 'url' => $url, 'time'=> $time ]));
}
function chietkhau($data, $data1)
{
    return $data - ($data*$data1/ 100);
}
function msg_admin($status, $msg, $url = '-1', $time = 0) {
    // cần có echo để in ra màn hình
    $kq =  '<script>cuteToast({
            type: "'.$status.'",
            message: "'.$msg.'",
            timer: 5000
        });
        </script>';
    if($url != '-1') {
        $kq .= '<script>setTimeout("location.href = \'' . $url . '\';", ' . $time . ');</script>';
    }
    return $kq;
}
function pusher($username = '', $status = false, $msg = '') {
    // $username username nhận thông báo; $status // trạng thái success, error; $msg nội dung
    $options = array(
        'encrypted' => true
    );
    $pusher = new Pusher(
            '10d5ea7e7b632db09c72', 'a496a6f084ba9c65fffb', '234217', $options
    );
    $arr['type'] = $status;
    $arr['message'] = $msg;
    $pusher->trigger($username, 'realtime', $arr);
}
function msg_success2($text)
{
    return die('<script type="text/javascript">Swal.fire("Thành Công", "'.$text.'", "success");</script>');
}
function msg_error2($text)
{
    return die('<script type="text/javascript">Swal.fire("Thất Bại", "'.$text.'", "error");</script>');
}
function msg_warning2($text)
{
    return die('<script type="text/javascript">Swal.fire("Cảnh Báo", "'.$text.'", "warning");</script>');
}
function msg_success($text, $url, $time)
{
    return die('<script type="text/javascript">Swal.fire("Thành Công", "'.$text.'", "success");
    setTimeout(function(){ location.href = "'.$url.'" },'.$time.');</script>');
}
function msg_error($text, $url, $time)
{
    return die('<script type="text/javascript">Swal.fire("Thất Bại", "'.$text.'", "error");
    setTimeout(function(){ location.href = "'.$url.'" },'.$time.');</script>');
}
function msg_warning($text, $url, $time)
{
    return die('<script type="text/javascript">Swal.fire("Cảnh Báo", "'.$text.'", "warning");
    setTimeout(function(){ location.href = "'.$url.'" },'.$time.');</script>');
}

function display_banned($data)
{
    if ($data == 1)
    {
        $show = '<span class="badge badge-danger">Banned</span>';
    }
    else if ($data == 0)
    {
        $show = '<span class="badge badge-success">Hoạt động</span>';
    }
    return $show;
}
function display_loaithe($data)
{
    if ($data == 0)
    {
        $show = '<span class="badge badge-warning">Bảo trì</span>';
    }
    else 
    {
        $show = '<span class="badge badge-success">Hoạt động</span>';
    }
    return $show;
}
function datahost($data)
{
    if($data == 'Lay')
    {
        $jsc = '<span class="badge badge-success">Khách Hàng Lấy Cả Hosting</span>';
    }
    else if($data == 'Khonglay')
    {
        $jsc = '<span class="badge badge-danger">Khách Hàng Không Lấy Hosting</span>';
    }
    else
    {
        $jsc = '<span class="badge badge-danger">Không Xác Định</span>';
    }
    return $jsc;
}
function mienvn($data)
{
    if($data == 'Daco')
    {
        $jsc = '<span class="badge badge-success">Khách đã có miền này</span>';
    }
    else if($data == 'Chuaco')
    {
        $jsc = '<span class="badge badge-danger">Khách chưa có miền này</span>';
    }
    else
    {
        $jsc = '<span class="badge badge-danger">Không Xác Định</span>';
    }
    return $jsc;
}

function XoaDauCach($text)
{
    return trim(preg_replace('/\s+/',' ', $text));
}
function display($data)
{
    if ($data == 'HIDE')
    {
        $show = '<span class="badge badge-danger">ẨN</span>';
    }
    else if ($data == 'SHOW')
    {
        $show = '<span class="badge badge-success">HIỂN THỊ</span>';
    }
    return $show;
}
function nodata() {
    echo '<div class="ant-empty css-eq3tly ant-empty-normal">
        <div class="ant-empty-image">
            <svg width="64" height="41" viewBox="0 0 64 41" xmlns="http://www.w3.org/2000/svg">
                <g transform="translate(0 1)" fill="none" fill-rule="evenodd">
                    <ellipse fill="#f5f5f5" cx="32" cy="33" rx="32" ry="7"></ellipse>
                    <g fill-rule="nonzero" stroke="#d9d9d9">
                        <path d="M55 12.76L44.854 1.258C44.367.474 43.656 0 42.907 0H21.093c-.749 0-1.46.474-1.947 1.257L9 12.761V22h46v-9.24z"></path>
                        <path d="M41.613 15.931c0-1.605.994-2.93 2.227-2.931H55v18.137C55 33.26 53.68 35 52.05 35h-40.1C10.32 35 9 33.259 9 31.137V13h11.16c1.233 0 2.227 1.323 2.227 2.928v.022c0 1.605 1.005 2.901 2.237 2.901h14.752c1.232 0 2.237-1.308 2.237-2.913v-.007z" fill="#fafafa"></path>
                    </g>
                </g>
            </svg>
        </div>
        <p class="ant-empty-description">Chưa có dữ liệu</p>
    </div>';
}
function status($data) {
    if ($data == 'xuly'){
        $show = '<span style="color: #FFCC00">Đang xử lý</span>';
    }
    else if ($data == 'hoantat'){
        $show = '<span style="color: green">Hoàn tất</span>';
    }
    else if ($data == 'thanhcong'){
        $show = '<span style="color: green">Thành công</span>';
    }
    else if ($data == 'hoatdong'){
        $show = '<span style="color: green">Hoạt động</span>';
    }
    else if ($data == 'hethan'){
        $show = '<span style="color: red">Hết hạn</span>';
    }
    else if ($data == 'thatbai'){
        $show = '<span style="color: red">Thất bại</span>';
    }
    else if ($data == 'error'){
        $show = '<span class="badge badge-danger">Error</span>';
    }
    else if ($data == 'loi'){
        $show = '<span class="badge badge-danger">Lỗi</span>';
    }
    else if ($data == 'huy'){
        $show = '<span class="badge badge-danger">Hủy</span>';
    }
  	else if ($data == 'tamkhoa'){
        $show = '<span style="color: red">Tạm khóa</span>';
    }
    else if ($data == 'dangnap'){
        $show = '<span class="badge badge-warning">Đang đợi nạp</span>';
    }
    else if ($data == 2){
        $show = '<span class="badge badge-success">Hoàn tất</span>';
    }
    else if ($data == 1){
        $show = '<span class="badge badge-info">Đang xử lý</span>';
    }
    else{
        $show = '<span class="badge badge-warning">Khác</span>';
    }
    return $show;
}
function stnapthe($data) {
    if ($data == 'xuly'){
        $show = '<span class="badge bg-warning rounded-lg" style="background-color: #FFC436">Chờ xử lý</span>';
    }
    else if ($data == 'hoantat'){
        $show = '<span class="badge bg-warning rounded-lg" style="background-color: ##33FF33">Hoàn tất</span>';
    }
    else if ($data == 'thanhcong'){
        $show = '<span class="badge bg-warning rounded-lg" style="background-color: #00FF33">Thành công</span>';
    }
    else if ($data == 'hoatdong'){
        $show = '<span style="color: green">Hoạt động</span>';
    }
    else if ($data == 'thatbai'){
        $show = '<span class="badge bg-warning rounded-lg" style="background-color: #DD0000">Thất bại</span>';
    }
    return $show;
}
function numb($data) {
    return str_replace('.', '', $data);
}
function active($url) {
    return ($_SERVER['REQUEST_URI'] == $url) ? 'active': '';
}
function list_bank() {
    $data = [
        'MOMO',
        'VIETINBANK',
        'VIETCOMBANK',
        'AGRIBANK',
        'TPBANK',
        'HDB',
        'VPBANK',
        'MBBANK',
        'OCEANBANK',
        'BIDV',
        'SACOMBANK',
        'ACB',
        'ABBANK',
        'NCB',
        'IBK',
        'CIMB',
        'EXIMBANK',
        'SEABANK',
        'SCB',
        'DONGABANK',
        'SAIGONBANK',
        'PVCOMBANK',
        'PVCOMBANK',
        'OCB',
        'MSB',
        'SHB',
        'NAMABANK',
        'VIB',
        'TECHCOMBANK',
        'VIETBANK'
    ];
    return $data;
}
function status_partner($data) {
    switch($data) {
        case 'xuly':
            $show = '<span class="badge bg-warning rounded-lg" style="background-color: #FFC436">Chờ duyệt</span>';
            break;
        case 'thanhcong':
            $show = '<span class="badge bg-warning rounded-lg" style="background-color: #00FF33">Chấp thuận</span>';
            break;
        case 'thatbai':
            $show = '<span class="badge bg-warning rounded-lg" style="background-color: #DD0000">Từ chối</span>';
            break;
    }
    return $show;
}
function hoso($data) {
    switch($data) {
        case 'xuly':
            $show = '<span class="badge bg-warning rounded-lg" style="background-color: #FFC436">Chờ duyệt</span>';
            break;
        case 'thanhcong':
            $show = '<span class="badge bg-warning rounded-lg" style="background-color: #00FF33">Chấp thuận</span>';
            break;
        case 'thatbai':
            $show = '<span class="badge bg-warning rounded-lg" style="background-color: #DD0000">Từ chối</span>';
            break;
    }
    return $show;
}
function giamgia($money, $pt) {
    return $money - $money *$pt/100;
}
/*XỬ LÝ UPLOAD ẢNH*/
function upload_imgur($images) {
    $file     = file_get_contents($images);
    $dataPost = array(
        'image' => base64_encode($file)
    );
    $ch       = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.imgur.com/3/image.json');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_POST, 1);
    $header[] = 'Authorization: Client-ID d5062e24816be2a';
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $dataPost);
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}
function stnapvi($status) {
    switch($status) {
        case 'xuly':
            $show = '<span class="badge bg-warning rounded-lg" style="background-color: #FFC436">Chờ chuyển tiền</span>';
        break;
        case 'thanhcong':
            $show = '<span class="badge bg-warning rounded-lg" style="background-color: #33FF33">Đã thanh toán</span>';
        break;
        case 'huy':
            $show = '<span class="badge bg-warning rounded-lg" style="background-color: #DD0000">Đã hủy</span>';
        break;
    }
    return $show;
}
function sttgiahan($status) {
    switch($status) {
        case 'xuly':
            $show = '<span class="badge bg-warning rounded-lg" style="background-color: #FFC436">Chờ xử lý</span>';
        break;
        case 'thanhcong':
            $show = '<span class="badge bg-warning rounded-lg" style="background-color: #33FF33">Đã gia hạn</span>';
        break;
        case 'thatbai':
            $show = '<span class="badge bg-warning rounded-lg" style="background-color: #DD0000">Thất bại</span>';
        break;
    }
    return $show;
}
// if ($data == 'xuly'){
//     $show = '<span class="badge bg-warning rounded-lg" style="background-color: #FFC436">Chờ xử lý</span>';
// }
// else if ($data == 'hoantat'){
//     $show = '<span class="badge bg-warning rounded-lg" style="background-color: ##33FF33">Hoàn tất</span>';
// }
// else if ($data == 'thanhcong'){
//     $show = '<span class="badge bg-warning rounded-lg" style="background-color: #00FF33">Thành công</span>';
// }
// else if ($data == 'hoatdong'){
//     $show = '<span style="color: green">Hoạt động</span>';
// }
// else if ($data == 'thatbai'){
//     $show = '<span class="badge bg-warning rounded-lg" style="background-color: #DD0000">Thất bại</span>';
// }
function magiamgia($data)
{
    if($data == 'muacode')
    {
        return 'MUA CODE';
    }
    else if($data == 'taoweb')
    {
        return 'TẠO WEB';
    }
    else
    {
        return 'LỖI';
    }
}
function check_domain($domain) {
    return count($domain) >=2;
}
function duyetapi($data)
{
    if($data == 0)
    {
        $jsc = '<span class="badge badge-info">Chờ duyệt</span>';
    }
    else if($data == 1)
    {
        $jsc = '<span class="badge badge-success">Hoạt động</span>';
    }
    else if($data == 2)
    {
        $jsc = '<span class="badge badge-danger">Không duyệt</span>';
    }
    else if($data == 3)
    {
        $jsc = '<span class="badge badge-danger">Ngưng hoạt động</span>>';
    }
    return $jsc;
}
function muamienvn($data)
{
    if($data == 'API')
    {
        $jsc = '<span class="badge badge-success">API MUA MIỀN</span>';
    }
    else if($data = 'LOGWEB')
    {
        $jsc = '<span class="badge badge-success">MUA MIỀN TẠI WEB</span>';
    }
    return $jsc;
}
function inkq($data, $str = '') {
    return ($data) ? $data: $str;
}
function checkmienne($data)
{
    if($data == 0)
    {
        return '<span class="badge badge-warning">Chờ duyệt</span>';
    }
    else if($data == 1)
    {
        return '<span class="badge badge-success">Hoạt động</span>';
    }
    else if($data == 2)
    {
        return '<span class="badge badge-danger">Thất Bại</span>';
    }
    else if($data == 3)
    {
        return '<span class="badge badge-danger">Miền die - Hoàn tiền</span>';
    }
    else if($data == 4)
    {
        return '<span class="badge badge-danger">Hết Hạn</span>';
    }
}
function phantram($thang1, $thang2)
{
    if($thang1 && $thang2)
    {
        $kq = ($thang2 - $thang1)/$thang1 * 100; // tính phần trăm doanh thu tăng trưởng của tháng sau so với tháng trước
        $kq = ceil($kq);
        if($kq > 0)
        {
            return '<a class="success">Tăng '.$kq.'% (hơn '.format_cash($thang2 -  $thang1).'đ)</a>';
        }
        else if($kq < 0)
        {
            return '<a class="success">Giảm '.abs($kq).'% (giảm '.abs(format_cash($thang2 -  $thang1)).'đ)</a>';
        }
    }
    else
    {
        return '<a class="success">Giảm 100%</a>';;
    }
}
function check_username($data)
{
    if (preg_match('/^[a-zA-Z0-9_-]{3,16}$/', $data, $matches))
    {
        return True;
    }
    else
    {
        return False;
    }
}
function check_email($data)
{
    if (preg_match('/^.+@.+$/', $data, $matches))
    {
        return True;
    }
    else
    {
        return False;
    }
}
function check_phone($data)
{
    if(preg_match('/^[0-9]{10}+$/', $data)) {
        return True;
    } else {
        return False;
    }
}
function check_url($url)
{
    $c = curl_init();
    curl_setopt($c, CURLOPT_URL, $url);
    curl_setopt($c, CURLOPT_HEADER, 1);
    curl_setopt($c, CURLOPT_NOBODY, 1);
    curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($c, CURLOPT_FRESH_CONNECT, 1);
    if(!curl_exec($c))
    {
        return false;
    }
    else
    {
        return true;
    }
}
function check_zip($img)
{
    $filename = $_FILES[$img]['name'];
    $ext = explode(".", $filename);
    $ext = end($ext);
    $valid_ext = array("zip","ZIP");
    if(in_array($ext, $valid_ext))
    {
        return true;
    }
}
function TypePassword($string)
{
    return $string;
}
function phantrang($url, $start, $total, $kmess)
{
    $out[] = ' <nav class="relative z-0 inline-flex v-pagination mx-auto v-text-1 v-light-theme">';
    $neighbors = 2;
    if ($start >= $total) $start = max(0, $total - (($total % $kmess) == 0 ? $kmess : ($total % $kmess)));
    else $start = max(0, (int)$start - ((int)$start % (int)$kmess));
    $base_link = '<li><a class="mx-1 border border-gray-400 bg-white relative v-page-no w-8 md:w-10 h-8 md:h-10 text-md md:text-lg rounded font-bold inline-flex items-center justify-center px-2 py-2 leading-5 font-medium focus:outline-none transition ease-in-out duration-150 text-gray-800 v-pagination-text disabled" href="' . strtr($url, array('%' => '%%')) . 'page=%d' . '">%s</a></li>';
    $out[] = $start == 0 ? '' : sprintf($base_link, $start / $kmess, '<svg viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5">
    <path fill-rule="evenodd"
        d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
        clip-rule="evenodd"></path>
</svg>');
    if ($start > $kmess * $neighbors) $out[] = sprintf($base_link, 1, '1');
    if ($start > $kmess * ($neighbors + 1)) $out[] = '<li class="page-item"><a class="page-link">...</a></li>';
    for ($nCont = $neighbors;$nCont >= 1;$nCont--) if ($start >= $kmess * $nCont) {
        $tmpStart = $start - $kmess * $nCont;
        $out[] = sprintf($base_link, $tmpStart / $kmess + 1, $tmpStart / $kmess + 1);
    }
    $out[] = '<li class="border mx-1 w-8 md:w-10 h-8 md:h-10 text-md md:text-lg select-none rounded inline-flex justify-center items-center px-4 py-2 focus:outline-none text-white border-red-600 text-white bg-red-600"><a class="page-link">' . ($start / $kmess + 1) . '</a></li>';
    $tmpMaxPages = (int)(($total - 1) / $kmess) * $kmess;
    for ($nCont = 1;$nCont <= $neighbors;$nCont++) if ($start + $kmess * $nCont <= $tmpMaxPages) {
        $tmpStart = $start + $kmess * $nCont;
        $out[] = sprintf($base_link, $tmpStart / $kmess + 1, $tmpStart / $kmess + 1);
    }
    if ($start + $kmess * ($neighbors + 1) < $tmpMaxPages) $out[] = '<li class="page-item"><a class="page-link">...</a></li>';
    if ($start + $kmess * $neighbors < $tmpMaxPages) $out[] = sprintf($base_link, $tmpMaxPages / $kmess + 1, $tmpMaxPages / $kmess + 1);
    if ($start + $kmess < $total)
    {
        $display_page = ($start + $kmess) > $total ? $total : ($start / $kmess + 2);
        $out[] = sprintf($base_link, $display_page, '<svg viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5">
        <path fill-rule="evenodd"
            d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
            clip-rule="evenodd"></path>
    </svg>
        ');
    }
    $out[] = '</ul></nav>';
    return implode('', $out);
}
function myip()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP']))     
    {  
        $ip_address = $_SERVER['HTTP_CLIENT_IP'];  
    }  
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))    
    {  
        $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];  
    }  
    else  
    {  
        $ip_address = $_SERVER['REMOTE_ADDR'];  
    }
    return $ip_address;
}
function online($data)
{
    if($data == 'ONLINE')
    {
        return '🟢 ONLINE';
    }
    else
    {
        return '🔴 OFFLINE';
    }
}
function sukien($data)
{
    if($data == 'ON')
    {
        return '🟢';
    }
    else
    {
        return '🔴';
    }
}
function timeHave($time) {
    $data = intval(($time - time()) /86400 );
    if($data <= 0) $data = 0;
    return $data.' ngày nữa';
}
function timeAgo($time_ago)
{
    $time_ago   = date("Y-m-d H:i:s", $time_ago);
    $time_ago   = strtotime($time_ago);
    $cur_time   = time();
    $time_elapsed   = $cur_time - $time_ago;
    $seconds    = $time_elapsed ;
    $minutes    = round($time_elapsed / 60 );
    $hours      = round($time_elapsed / 3600);
    $days       = round($time_elapsed / 86400 );
    $weeks      = round($time_elapsed / 604800);
    $months     = round($time_elapsed / 2600640 );
    $years      = round($time_elapsed / 31207680 );
    // Seconds
    if($seconds <= 60)
    {
        return "$seconds giây trước";
    }
    //Minutes
    else if($minutes <= 60)
    {
        return "$minutes phút trước";
    }
    //Hours
    else if($hours <= 24)
    {
        return "$hours tiếng trước";
    }
    //Days
    else if($days <= 7)
    {
        if($days == 1)
        {
            return "Hôm qua";
        }
        else
        {   
            return "$days ngày trước";
        }
    }
    //Weeks
    else if($weeks <= 4.3)
    {
        return "$weeks tuần trước";
    }
    //Months
    else if($months <=12)
    {
        return "$months tháng trước";
    }
    //Years
    else
    {
        return "$years năm trước";
    }
}
function randomtoken($length = 25) {
    $bytes = random_bytes($length);
    return bin2hex($bytes);
}
function randomtoken2($length = 15) {
    $bytes = random_bytes($length);
    return bin2hex($bytes);
}
$onethang = 2592000;
if(isset($_COOKIE['token']))
{
    $TUANORI->update("users", array(
        'timeon' => gettime(),
        'online' => 'ONLINE',
        'user_agent'    => $_SERVER['HTTP_USER_AGENT']
    ), "tokenlog = '".$_COOKIE['token']."' ");
}
function logclient()
{
    global $TUANORI;
    if(!$TUANORI->get_row(" SELECT * FROM `logclient` WHERE `ip` = '".myip()."' AND `time` >= DATE(NOW()) AND `time` < DATE(NOW()) + INTERVAL 1 DAY"))
    {
            $TUANORI->insert("logclient", [
            'ip'    => myip(),
            'time'  => gettime()
        ]);
    }
}
logclient();
if(empty($_COOKIE['magd']))
{
    $magd = strtoupper(substr(md5( $_SERVER ['HTTP_USER_AGENT'].myip()), 0 , 8));
    setcookie('magd', $magd, time() + 2678400, '/');
}