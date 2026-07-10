<?php
/*MÃ NGUỒN NÀY ĐƯỢC PHÁT TRIỂN BỞI TUANORI - ZALO: 0812665001*/
define("IN_SITE", true);
require_once("../../core/config.php");
require_once("../../core/function.php");
require_once("../../lib/autoload.php");
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Core\ProductionEnvironment;
use PayPalCheckoutSdk\Orders\OrdersGetRequest;
use PayPalHttp\HttpException;
if (isset($_POST['type']) && $_POST['type'] == 'PayPal' && isset($_POST['order']))
{
    if(empty($_COOKIE['username']))
    {
        msg_error2('Vui lòng đăng nhập để sử dụng tính năng');
    }
    $environment = new ProductionEnvironment($TUANORI->site('clientId'), $TUANORI->site('clientSecret'));
    $client = new PayPalHttpClient($environment);
    $orderData = $_POST['order'];
    $request = new OrdersGetRequest($orderData['id']);
    try {
        $response = $client->execute($request);
        if ($response->statusCode != 200) {
            msg_error2('Đã xảy ra lỗi!');
        }
        $order = $response->result;
        if ($order->status != 'COMPLETED') {
            msg_error2('Đơn hàng không hợp lệ hoặc chưa thanh toán');
        }
        $orderDetail = $order->purchase_units[0];
        if ($TUANORI->num_rows("SELECT * FROM `nappaypal` WHERE `trans_id` = '".$order->id."' ") > 0) {
            msg_error2('Giao dịch này đã được xử lý');
        }
        if($orderDetail->amount->value < 1)
        {
            msg_error2('Nạp thất bại. Số tiền nạp phải lớn hơn 1$');
        }
        $usd = $orderDetail->amount->value;
        $price = $TUANORI->site('sotien_paypal') * $usd;
        $isInsert = $TUANORI->insert("nappaypal", [
            'username'      => $getUser['username'],
            'trans_id'      => $order->id,
            'donap'         => $usd,
            'thucnhan'      => $price,
            'create_date'   => gettime(),
            'create_time'   => time()
        ]);
        $TUANORI->insert("biendongsodu", [
            'username'      => $getUser['username'],
            'truoc'         => $my_money,
            'sau'           => $my_money + $sotien,
            'note'          => 'Nạp thành công '.format_cash($sotien).'đ bằng PayPal',
            'tongtien'      => $price,
            'time'          => gettime()
        ]);
        $TUANORI->cong("users", "money", $price, " `username` = '".$getUser['username']."' ");
        $TUANORI->cong("users", "total_money", $price, " `username` = '".$getUser['username']."' ");
        if ($isInsert) {
            // send_tele("Thành viên ".$getUser['username']." vừa nạp ".format_cash($price)." vào tài khoản lúc ".format_date(time()).". Hình thức qua PAYPAL");
            msg_success2("Bạn vừa nạp thành công ".$usd."$ tương ứng với ".format_cash($price)."đ");
        }
    } catch (HttpException $e) {
        msg_error2($e->getMessage());
    } catch (Exception $e) {
        msg_error2($e->getMessage());
    }
}