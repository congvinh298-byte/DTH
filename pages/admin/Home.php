<?php
define("IN_SITE", true);
require_once("../../core/config.php");
require_once("../../core/function.php");
$tieude = 'TRANG QUẢN TRỊ HỆ THỐNG';
require_once("../../pages/admin/Head.php");
require_once("../../pages/admin/Header.php");
CheckAdmin();
$tvdkhn = $DMH->num_rows(" SELECT * FROM `users` WHERE `timereg` >= DATE(NOW()) AND `timereg` < DATE(NOW()) + INTERVAL 1 DAY ") ?? 0;
$viewhn = $DMH->num_rows(" SELECT * FROM `logclient` WHERE `ip` != '' AND `time` >= DATE(NOW()) AND `time` < DATE(NOW()) + INTERVAL 1 DAY ") ?? 0;

// doanh thu hôm nay
$tiencard = $DMH->get_row("SELECT SUM(`thucnhan`) FROM `napcard` WHERE `status` = 'thanhcong' AND `thoigian` >= DATE(NOW()) AND `thoigian` < DATE(NOW()) + INTERVAL 1 DAY ")['SUM(`thucnhan`)'];
$tienatm  = $DMH->get_row("SELECT SUM(`sotien`) FROM `napatm` WHERE `thoigian` >= DATE(NOW()) AND `thoigian` < DATE(NOW()) + INTERVAL 1 DAY ")['SUM(`sotien`)'];
$doanhthuhn = $tiencard + $tienatm;

//download code hôm nay 
$downhn = 0;
foreach($DMH->get_list(" SELECT * FROM `lichsumuacode` WHERE `time` >= DATE(NOW()) AND `time` < DATE(NOW()) + INTERVAL 1 DAY") as $ok) {
    if(isset($ok['magd'])) {
        $downhn +=$DMH->num_rows(" SELECT * FROM `lichsumuacode2` WHERE `magd` = '".$ok['magd']."' ") ?? 0;
    } else {
        ++$downhn;
    }
}
?>
<div class="row">
    <div class="col-sm-3 col-xs-6">

        <div class="tile-stats tile-red">
            <div class="icon"><i class="entypo-users"></i>
            </div>
            <div class="num" ><?=number_format($tvdkhn);?></div>
            <h3>Thành viên đăng ký</h3>
            <p>Trong hôm nay.</p>
        </div>

    </div>

    <div class="col-sm-3 col-xs-6">

        <div class="tile-stats tile-green">
            <div class="icon"><i class="entypo-chart-bar"></i>
            </div>
            <div class="num"><?=number_format($viewhn);?></div>

            <h3>Lượt ghé thăm</h3>
            <p>Trong hôm nay.</p>
        </div>

    </div>

    <div class="clear visible-xs"></div>

    <div class="col-sm-3 col-xs-6">

        <div class="tile-stats tile-aqua">
            <div class="icon"><i class="entypo-mail"></i>
            </div>
            <div class="num"><?=number_format($doanhthuhn);?></div>
            <h3>Tổng doanh thu</h3>
            <p>Trong hôm nay.</p>

        </div>

    </div>

    <div class="col-sm-3 col-xs-6">

        <div class="tile-stats tile-blue">
            <div class="icon"><i class="entypo-rss"></i>
            </div>
            <div class="num"><?=number_format($downhn);?></div>

            <h3>Lượt download</h3>
            <p>Trong hôm nay.</p>
        </div>

    </div>
</div>

<br />



<br />

<div class="row">

    <div class="col-sm-12">

        <div class="panel panel-primary">
            <div class="panel-heading">
                <div class="panel-title"><b>Lịch sử dòng tiền</b></div>
            </div>

            <table class="table table-bordered responsive">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Username</th>
                        <th>Tiền trước</th>
                        <th>Tổng tiền</th>
                        <th>Tiền sau</th>
                        <th>Thời gian</th>
                        <th>Nội dung</th>

                    </tr>
                </thead>

                <tbody>
                    <?php $i = 0; foreach($DMH->get_list(" SELECT * FROM `biendongsodu` ORDER BY id DESC LIMIT 6") as $row){ ?>
                    <tr>
                        <td><?=++$i;?></td>
                        <td><a target="_blank" style="color: #0099CC; font-weight: bold" href="/pages/admin/EditQuanlythanhvien.php?id=<?=$DMH->getUser($row['username'])['id'];?>"><?=$row['username'];?></a></td>
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
                        <td ><b style="color: black"><?=inkq($row['note'], 'Không Có');?></b></td>
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
                <div class="panel-title"><b>Lịch sử tạo website</b></div>

                <!-- <div class="panel-options">
                    <a href="#sample-modal" data-toggle="modal" data-target="#sample-modal-dialog-1" class="bg"><i class="entypo-cog"></i></a>
                </div> -->
            </div>

            <table class="table table-bordered responsive">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Username</th>
                        <th>Tên miền</th>
                        <th>Tổng tiền</th>
                        <th>Thời gian</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>

                <tbody>
                    <?php $i = 0; foreach($DMH->get_list(" SELECT * FROM `lichsutaoweb` WHERE `buoc` = '3' ORDER BY id DESC LIMIT 6") as $row){ ?>
                    <tr>
                        <td><?=++$i;?></td>
                        <td><a target="_blank" style="color: #0099CC; font-weight: bold" href="/pages/admin/EditQuanlythanhvien.php?id=<?=$DMH->getUser($row['username'])['id'];?>"><?=$row['username'];?></a></td>
                        <td><a target="_blank" href ="//<?=$row['tenmien'];?>" style="color: green; font-weight: bold;"><?=$row['tenmien'];?></a></td>
                        <td><b style="color: red"><?=number_format($row['tongtien']);?>đ</b></td>
                        <td ><b><?=gettime2($row['ngaytao']);?></b></td>
                        <td ><b><?=statustaoweb($row['buoc']);?></b></td>
                        <td>
                        <a href="<?=BASE_URL('pages/admin/EditQuanlytaoweb.php?id='.$row['id']);?>" class="btn btn-default btn-sm btn-icon icon-left">
                            <i class="entypo-pencil"></i>
                            Quản lý
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

require_once("../../pages/admin/Footer.php");
?>