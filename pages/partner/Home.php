<?php
define("IN_SITE", true);
require_once("../../core/config.php");
require_once("../../core/function.php");
$tieude = 'TRANG QUẢN LÝ';
require_once("../../pages/partner/Head.php");
require_once("../../pages/partner/Header.php")  ;
CheckVeri();
//download code hôm nay 
$downhn = $DMH->num_rows(" SELECT * FROM `partner_biendongsodu` WHERE `username` = '".$getUser['username']."' AND `id_code` != 0 AND `time` >= DATE(NOW()) AND `time` < DATE(NOW()) + INTERVAL 1 DAY ") ?? 0;
$dthn   = $DMH->get_row("SELECT SUM(`tongtien`) FROM `partner_biendongsodu` WHERE `username` = '".$getUser['username']."' AND  `id_code` != 0 AND `time` >= DATE(NOW()) AND `time` < DATE(NOW()) + INTERVAL 1 DAY ")['SUM(`tongtien`)'];
$view   = $DMH->get_row("SELECT SUM(`luotxem`) FROM `danhsachmuacode` WHERE `partner` = '".$getUser['username']."' ")['SUM(`luotxem`)'];
$tongdt = $DMH->get_row("SELECT SUM(`tongtien`) FROM `partner_biendongsodu` WHERE `username` = '".$getUser['username']."' AND `id_code` != 0 ")['SUM(`tongtien`)'];
if(isset($_GET['xoa']) && $getUser['verify'] == 1) {
    $row = $DMH->get_row(" SELECT * FROM `partner_code` WHERE `id` = '".$_GET['xoa']."' AND `username` = '".$getUser['username']."' ");
    if(!$row) {
        die(msg_admin("error", "Đơn bán code này không tồn tại",BASE_URL('Partner'), 500));
    } else {
        $DMH->remove("partner_code", " `id` = '".$_GET['xoa']."' ");
        die(msg_admin("success", "Đã xóa thành công",BASE_URL('Partner'), 500));

    }
}
?>
<div class="row">
    <div class="col-sm-3 col-xs-6">

        <div class="tile-stats tile-red">
            <div class="icon"><i class="entypo-users"></i>
            </div>
            <div class="num" data-start="0" data-end="<?=$tvdkhn;?>" data-postfix="" data-duration="1500" data-delay="0"><?=number_format($downhn);?> <i class="fa fa-download"></i></div>
            <h3>Lượt mua</h3>
            <p>Trong hôm nay.</p>
        </div>

    </div>

    <div class="col-sm-3 col-xs-6">

        <div class="tile-stats tile-green">
            <div class="icon"><i class="entypo-chart-bar"></i>
            </div>
            <div class="num"><?=number_format($dthn);?> <i class="fa fa-money"></i></div>

            <h3>Doanh thu</h3>
            <p>Trong hôm nay.</p>
        </div>

    </div>

    <div class="clear visible-xs"></div>

    <div class="col-sm-3 col-xs-6">

        <div class="tile-stats tile-aqua">
            <div class="icon"><i class="entypo-mail"></i>
            </div>
            <div class="num"><?=number_format($view);?> <i class="fa fa-eye"></i></div>
            <h3>Lượt xem code</h3>
            <p>Từ trước đến nay.</p>

        </div>

    </div>

    <div class="col-sm-3 col-xs-6">

        <div class="tile-stats tile-blue">
            <div class="icon"><i class="entypo-rss"></i>
            </div>
            <div class="num"><?=number_format($tongdt);?> <i class="fa fa-money"></i></div>

            <h3>Tổng doanh thu</h3>
            <p>Từ trước đến nay.</p>
        </div>

    </div>
</div>

<br />



<br />

<div class="row">

    <div class="col-sm-12">

        <div class="panel panel-primary">
            <div class="panel-heading">
                <div class="panel-title"><b>Lịch sử tiền bán</b></div>  
            </div>

            <table class="table table-bordered responsive">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Mã code</th>
                        <th>Tiền trước</th>
                        <th>Tổng tiền</th>
                        <th>Tiền sau</th>
                        <th>Nội dung</th>
                        <th>Thời gian</th>
                        <th>Người mua</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 0; foreach($DMH->get_list(" SELECT * FROM `partner_biendongsodu` WHERE `username` = '".$getUser['username']."' AND `id_code` != 0 ORDER BY id DESC LIMIT 10") as $row){ ?>
                    <tr>
                        <td><?=++$i;?></td>
                        <td><a target="_blank" href="/mua-code/<?=$row['id_code'];?>"><span class="btn btn-info" style="padding: 4px 8px;"><?=$row['id_code'];?></span></a></td>
                        <td><b><?=number_format($row['truoc']);?>đ</b></td>
                        <td>
                            <?php if($row['sau'] > $row['truoc']) {
                                echo '<b style="color: green">+'.format_cash($row['tongtien']).'đ</b>';
                            }else {
                                echo '<b style="color: red">-'.format_cash($row['tongtien']).'đ</b>';
                            } ?>
                        </td>
                        <td><b><?=number_format($row['sau']);?>đ</b></td>
                        <td><b><?=$row['time'];?></b></td>
                        <td><b style="color: black"><?=$row['note'];?></b></td>
                        <td><b style="color: green"><?=$row['usermua'];?></b></td>
                    </tr>
                    <?php } ?>

                   

                </tbody>
            </table>
        </div>

    </div>
 
</div>

<br />


<div class="row">

    <div class="col-sm-12">

        <div class="panel panel-primary">
            <div class="panel-heading">
                <div class="panel-title"><b>Code chờ duyệt</b></div>
            </div>

            <table class="table table-bordered responsive">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>ID danh mục</th>
                        <th>Tên</th>
                        <th>Mô tả</th>
                        <th>Số tiền</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>

                <tbody>
                    <?php $i = 0; foreach($DMH->get_list(" SELECT * FROM `partner_code` WHERE `username` = '".$getUser['username']."' AND `status` = 'xuly' ORDER BY id DESC LIMIT 6") as $row){ ?>
                    <tr>
                        <td><?=++$i;?></td>
                        <td><a target="_blank" href="/danh-muc-code/<?=$row['id_danhmuc'];?>"><span class="btn btn-info" style="padding: 4px 8px;"><?=$row['id_danhmuc'];?></span></a></td>

                        <td><b style="color: black"><?=$row['name'];?></b></td>
                        <td><b style="color: black"><?=$row['mota'];?></b></td>
                        <td><b style="color: green"><?=number_format($row['sotien']);?>đ</b></td>
                        <td><?=status_partner($row['status']);?></td>
                        <td>
                            <a href="<?=BASE_URL('Partner/EditCode?id='.$row['id']);?>" class="btn btn-default btn-sm btn-icon icon-left">
                                <i class="entypo-pencil"></i>
                                Chỉnh sửa
                            </a>
                            <a href="<?=BASE_URL('Partner?xoa='.$row['id']);?>" class="btn btn-danger btn-sm btn-icon icon-left">
                                <i class="entypo-cancel"></i>
                                Delete
                            </a>
                        </td>
                    </tr>
                    <?php } ?>

                   

                </tbody>
            </table>
        </div>

    </div>
 
</div>
<?php

require_once("../../pages/partner/Footer.php");
?>