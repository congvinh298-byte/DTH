<?php
define("IN_SITE", true);
require_once(__DIR__."/../../core/config.php");
require_once(__DIR__."/../../core/function.php");

if (empty($getUser) || ($getUser['level'] !== 'admin' && $getUser['level'] !== 'bct')) {
    die("Access denied");
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 1;

require_once(__DIR__."/../../core/policies_data.php");

$title = $bct_policies_data[$id]['title'] ?? "Tài liệu";
$body = $bct_policies_data[$id]['content'] ?? "";

$filename = "Hoso_BCT_" . $id . ".doc";

header("Content-Type: application/vnd.ms-word");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("content-disposition: attachment;filename=" . $filename);

echo "
<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:w='urn:schemas-microsoft-com:office:word' xmlns='http://www.w3.org/TR/REC-html40'>
<head>
<meta charset='utf-8'>
<style>
    body { font-family: 'Times New Roman', serif; font-size: 14pt; line-height: 1.5; margin: 0; padding: 0; }
    .header-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
    .header-table td { vertical-align: top; text-align: center; }
    .agency-name { font-weight: bold; text-transform: uppercase; font-size: 13pt; }
    .republic { font-weight: bold; text-transform: uppercase; font-size: 13pt; }
    .independence { font-weight: bold; font-size: 14pt; }
    .date-line { font-style: italic; font-size: 14pt; font-weight: normal; margin-top: 5px; }
    h1 { font-size: 15pt; text-align: center; text-transform: uppercase; margin-top: 30px; margin-bottom: 20px; font-weight: bold; }
    p { margin: 0 0 10px 0; text-align: justify; text-indent: 1.27cm; }
    ul, ol { margin-top: 0; margin-bottom: 10px; text-align: justify; }
    li { margin-bottom: 5px; }
    .signature-table { width: 100%; border-collapse: collapse; margin-top: 30px; page-break-inside: avoid; }
    .signature-table td { vertical-align: top; text-align: center; width: 50%; }
    .sign-title { font-weight: bold; text-transform: uppercase; font-size: 14pt; }
    .sign-sub { font-style: italic; font-size: 13pt; font-weight: normal; }
</style>
</head>
<body>
    <table class='header-table'>
        <tr>
            <td style='width: 40%;'>
                <div class='agency-name'>CÔNG TY TNHH MTV<br>ĐIỆN TỬ HIẾU</div>
                <hr style='width: 40%; border: 0; border-top: 1px solid black; margin-top: 2px; margin-bottom: 0;'>
            </td>
            <td style='width: 60%;'>
                <div class='republic'>CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM</div>
                <div class='independence'>Độc lập - Tự do - Hạnh phúc</div>
                <hr style='width: 40%; border: 0; border-top: 1px solid black; margin-top: 2px; margin-bottom: 5px;'>
                <div class='date-line'>Đồng Tháp, ngày ...... tháng ...... năm 202...</div>
            </td>
        </tr>
    </table>
    
    <h1>" . $title . "</h1>
    
    <div style='text-align: justify;'>
        " . $body . "
    </div>
    
    <table class='signature-table'>
        <tr>
            <td></td>
            <td>
                <div class='sign-title'>GIÁM ĐỐC</div>
                <div class='sign-sub'>(Ký, ghi rõ họ tên và đóng dấu)</div>
                <br><br><br><br><br>
                <div style='font-weight: bold;'>.......................................</div>
            </td>
        </tr>
    </table>
</body>
</html>
";
?>
