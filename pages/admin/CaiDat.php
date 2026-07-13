<?php
define("IN_SITE", true);
require_once(__DIR__."/../../core/config.php");
require_once(__DIR__."/../../core/function.php");
CheckAdmin();
$tieude = 'Cai dat | Dien May Hieu';
require_once(__DIR__."/../../pages/admin/Head.php");
require_once(__DIR__."/../../pages/admin/Header.php");

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['options'] as $key => $value) {
        $keyEsc = mysqli_real_escape_string($DMH->ketnoi, $key);
        $valEsc = mysqli_real_escape_string($DMH->ketnoi, $value);
        $DMH->query("INSERT INTO `options` (`key`, `value`) VALUES ('$keyEsc', '$valEsc') ON DUPLICATE KEY UPDATE `value` = '$valEsc'");
    }
    $msg = 'Da luu cai dat.';
}
$options = [];
$rows = $DMH->get_list("SELECT * FROM `options`");
foreach ($rows as $r) { $options[$r['key']] = $r['value']; }
?>

<h2 style="margin:0 0 20px; font-size:18px; font-weight:800;"><i class="fa-solid fa-gear" style="color:#0ea5e9;"></i> Cai dat he thong</h2>

<?php if ($msg): ?>
    <div class="alert alert-success"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-header"><h3>Thong tin co ban</h3></div>
    <div class="card-body">
        <form method="POST" action="">
            <div class="form-group">
                <label>Ten website</label>
                <input type="text" name="options[site_name]" class="form-control" value="<?= htmlspecialchars($options['site_name'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>So dien thoai</label>
                <input type="text" name="options[site_phone]" class="form-control" value="<?= htmlspecialchars($options['site_phone'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Dia chi</label>
                <textarea name="options[site_address]" class="form-control" rows="3"><?= htmlspecialchars($options['site_address'] ?? '') ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Luu lai</button>
        </form>
    </div>
</div>

<?php require_once(__DIR__."/../../pages/admin/Footer.php"); ?>
