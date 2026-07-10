
<?php

define("IN_SITE", true);
require_once("../../core/config.php");
require_once("../../core/function.php");
$title = "Whois tên miền";
require_once("../../pages/client/Head.php");
require_once("../../pages/client/Header.php");
if(isset($_POST['search'])) { 
    $domain = check_string($_POST['domain']);
    $data   = json_decode(curl_get("https://whois.inet.vn/api/whois/domainspecify/$domain"), true);
}
?>

<div class="content-wrapper transition-all duration-150 ltr:ml-0 rtl:mr-0 xl:ltr:ml-[248px] xl:rtl:mr-[248px]" id="content_wrapper">
    <div class="page-content">
        <div class="container-fluid transition-all duration-150" id="page_layout">
            <main id="content_layout">
                <!-- Page Content -->
                <div class="mb-3"></div>
                <section class="space-y-6">
                    <div class="grid grid-cols-1 gap-3 md:grid-cols-6">
                     
                        <div class="card">
                            <header class="card-header noborder">
                                <h4 class="card-title">Nhập tên miền của bạn</h4>
                            </header>
                            <div class="card-body px-6 pb-6">
                                <form method="POST" class="space-y-3">
                                    <div class="input-area">
                                        <label for="domain" class="form-label">Tên miền:</label>
                                        <input type="text" class="form-control py-2" name="domain" placeholder="Tên miền của bạn" value="<?= $domain ?? '';?>" required="">
                                    </div>
                                    <div class="input-area">
                                        <button type="submit" name="search" class="btn btn-primary w-full"><i class="fa-solid fa-magnifying-glass"></i> Kiểm tra</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php if(isset($_POST['search'])) { 
                        $domain = check_string($_POST['domain']);
                    ?>
                    <div class="grid grid-cols-1 gap-3 md:grid-cols-6">
                    
                        <div class="card">
                            <header class="card-header noborder">
                                <h4 class="card-title">Thông tin của bạn</h4>
                            </header>
                            <div class="card-body px-6 pb-6">
                                <?php if(empty($data['domainName'])) { ?>
                                    <div class="alert alert-danger" role="alert" style="font-size: 18px">Tên miền không hợp lệ.</div>
                                
                                <?php } else if($data['code'] == 0) { ?>
                                <form method="POST" class="space-y-3">
                                    <div class="input-area">
                                        <label class="form-label">Tên miền:</label>
                                        <input type="text" class="form-control py-2" value="<?=$data['domainName'] ?? '';?>" required="" disabled>
                                    </div>
                                    <div class="input-area">
                                        <label class="form-label">Ngày đăng ký:</label>
                                        <input type="text" class="form-control py-2" value="<?=$data['creationDate'] ?? '';?>" disabled>
                                    </div>
                                    <div class="input-area">
                                        <label class="form-label">Ngày hết hạn:</label>
                                        <input type="text" class="form-control py-2" value="<?=$data['expirationDate'] ?? '';?>" disabled>
                                    </div>
                                    <div class="input-area">
                                        <label class="form-label">Chủ sở hữu tên miền:</label>
                                        <input type="text" class="form-control py-2" value="<?=$data['registrantName'] ?? '';?>" disabled>
                                    </div>
                                    <div class="input-area">
                                        <label class="form-label">Cờ trạng thái:</label>
                                        <textarea type="text" class="form-control py-2" rows="5" disabled><?php foreach($data['status'] as $b) { echo $b."\n"; } ?></textarea>

                                    </div>
                                    <div class="input-area">
                                        <label class="form-label">Quản lý tại Nhà đăng ký:</label>
                                        <input type="text" class="form-control py-2" value="<?=$data['registrar'] ?? '';?>" disabled>
                                    </div>
                                    <div class="input-area">
                                        <label class="form-label">Nameserver:</label>
                                        <textarea type="text" class="form-control py-2" rows="5" disabled><?php foreach($data['nameServer'] as $b) { echo $b."\n"; } ?></textarea>
                                    </div>
                                    <div class="input-area">
                                        <label class="form-label">DNSSEC:</label>
                                        <input type="text" class="form-control py-2" value="<?=$data['DNSSEC'] ?? '';?>" disabled>
                                    </div>
                                </form>
                                <?php } else  { $ok = explode('.', $domain);
                                    $duoi = '';
                                    for($i = 1; $i< count($ok); ++$i) {
                                        $duoi.=$ok[$i];
                                        if(strlen($duoi) > 0 && $i < count($ok)-1) {
                                            $duoi.='.';
                                        }
                                    }
                                ?>
                                    <div class="alert alert-danger" role="alert" style="font-size: 18px">Tên miền này chưa được đăng ký.
                                    <?php if($DMH->get_row(" SELECT * FROM `danhsachmien` WHERE `domain` = '".$duoi."'")) { ?>
                                        Đăng ký <a style="color: green" href="/Mua-mien?name=<?=$ok[0].'&duoi='.$duoi;?>">tại đây</a> 
                                    <?php } ?>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                </section>
            </main>
        </div>
    </div>
</div>
</div>

<?php

require_once("../../pages/client/Footer.php");
?>